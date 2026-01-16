<?php

use App\Http\Controllers\BankFlowController;
use Illuminate\Support\Facades\Route;


Route::prefix('pago')->group(function ()  {
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
