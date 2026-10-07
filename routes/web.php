<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\LaporanController;

// Test route
Route::get('/test', function () {
    return 'Application is working!';
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Anggota (Resource)
    Route::resource('anggota', AnggotaController::class);
    
    // Simpanan
    Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan.index');
    Route::post('/simpanan', [SimpananController::class, 'store'])->name('simpanan.store');
    
    // Pinjaman
    Route::get('/pinjaman', [PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::post('/pinjaman', [PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::get('/pinjaman/{id}', [PinjamanController::class, 'show'])->name('pinjaman.show');
    
    // Angsuran
    Route::get('/angsuran', [AngsuranController::class, 'index'])->name('angsuran.index');
    Route::post('/angsuran', [AngsuranController::class, 'store'])->name('angsuran.store');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');
});
