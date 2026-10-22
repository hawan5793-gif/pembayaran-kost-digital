<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/penghuni', [DashboardController::class, 'tenants'])->name('tenants.index');
    Route::post('/penghuni', [DashboardController::class, 'storeTenant'])->name('tenants.store');

    Route::get('/riwayat', [DashboardController::class, 'history'])->name('payments.index');
    Route::get('/riwayat/catat', [DashboardController::class, 'payment'])->name('payments.create');
    Route::post('/riwayat', [DashboardController::class, 'storePayment'])->name('payments.store');

    Route::get('/laporan', [DashboardController::class, 'report'])->name('reports.index');
    Route::get('/tunggakan', [DashboardController::class, 'arrears'])->name('arrears.index');
    Route::get('/notifikasi', [DashboardController::class, 'notifications'])->name('notifications.index');

    Route::post('/api/pembayaran', [DashboardController::class, 'storePayment'])->name('api.payments.store');
    Route::post('/api/penghuni', [DashboardController::class, 'storeTenant'])->name('api.tenants.store');
});

