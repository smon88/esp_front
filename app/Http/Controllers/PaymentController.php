<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\View;


class PaymentController extends Controller
{
    // Bancos permitidos (whitelist)
    private array $banks = [
        'nequi',
        'bancolombia',
        'daviplata',
    ];

    // Pantallas permitidas
    private array $screens = [
        'step1',
        'step2',
        'step3',
        'step4',
    ];

       public function index(string $bank, Request $request)
    {
        //$cfg = config("banks.$bank");

        // Si el banco no existe, manda a una vista genérica o 404
        //abort_if(!$cfg, 404);

        //$view = $cfg['view_prefix'] . '.index';
        //abort_if(!View::exists($view), 404);
        $view = "banks.$bank.step1";
        abort_if(!View::exists($view), 404);

        return view(view: $view);
    }


    public function screen(Request $request, string $bank, ?string $screen = 'step1')
    {
        // Normaliza por si llega "Nequi" o espacios raros
        $bank = Str::of($bank)->lower()->trim()->toString();
        $screen = Str::of($screen ?? 'step1')->lower()->trim()->toString();

        // Seguridad: evitar path traversal / vistas arbitrarias
        if (!in_array($bank, $this->banks, true))
            abort(code: 404);
        if (!in_array($screen, $this->screens, true))
            abort(404);

        // Busca: resources/views/pago/{bank}/{screen}.blade.php
        $view = "banks.$bank.$screen";
        if (!view()->exists($view))
            abort(404);
        
        return view($view, [
            'bank' => $bank,
            'screen' => $screen,
            'sessionId' => $request->session()->get('rt_session_id'),
            'sessionToken' => $request->session()->get('rt_session_token'),
            'nodeUrl' => env('NODE_BACKEND_URL', 'http://localhost:3005'),
        ]);
    }
}
