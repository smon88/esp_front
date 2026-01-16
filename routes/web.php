<?php

use App\Http\Controllers\BankFlowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminSocketTokenController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAuthController;


Route::get('/', function () {
    return view('welcome');
});

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


// LOGIN ADMIN (sin users)
Route::get('/admin/login', [AdminAuthController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:10,1') // rate limit contra fuerza bruta
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ADMIN dashboard protegido por sesión
Route::middleware(['admin.session'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // token para socket admin (lo usa el dashboard)
    Route::get('/admin/socket-token', [AdminSocketTokenController::class, 'issue'])->name('admin.socket.token');
});