<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SimpananController;
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function () {
    return 'Application is working!';
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
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
    Route::post('/simpanan/penarikan', [SimpananController::class, 'withdraw'])->name('simpanan.withdraw');

    // Pinjaman
    Route::get('/pinjaman', [PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::post('/pinjaman', [PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::get('/pinjaman/create', [PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::get('/pinjaman/{id}', [PinjamanController::class, 'show'])->name('pinjaman.show');

    // Angsuran
    Route::get('/angsuran', [AngsuranController::class, 'index'])->name('angsuran.index');
    Route::post('/angsuran', [AngsuranController::class, 'store'])->name('angsuran.store');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/print', [ReportController::class, 'print'])->name('laporan.print');
});

Route::middleware('auth')->controller(FinanceController::class)->group(function () {
    Route::get('/keuangan', 'index')->name('finance.index');
    Route::post('/keuangan/pembukaan', 'opening')->name('finance.opening');
    Route::get('/simulasi', 'simulator')->name('finance.simulator');
    Route::post('/simulasi', 'calculate')->name('finance.calculate');
    Route::get('/pengajuan', 'loans')->name('finance.loans');
    Route::post('/pengajuan/{loan}/keputusan', 'decision')->whereNumber('loan')->name('finance.decision');
    Route::post('/pengajuan/{loan}/pencairan', 'disburse')->whereNumber('loan')->name('finance.disburse');
    Route::get('/jatuh-tempo', 'monitor')->name('finance.monitor');
    Route::get('/pelunasan/{loan}', 'quote')->whereNumber('loan')->name('finance.quote');
    Route::post('/pelunasan', 'settle')->name('finance.settle');
    Route::post('/keuangan/{transaction}/pembatalan', 'reverse')->name('finance.reverse');
});
Route::middleware('auth')->group(function () {
    Route::get('/laporan/pdf', [ReportController::class, 'pdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [ReportController::class, 'excel'])->name('laporan.excel');
    Route::get('/kuitansi/{transaction}', [OperationsController::class, 'receipt'])->name('operations.receipt');
    Route::get('/aktivitas', [OperationsController::class, 'activity'])->name('operations.activity');
    Route::get('/pengaturan', [OperationsController::class, 'settings'])->name('operations.settings');
    Route::post('/pengaturan', [OperationsController::class, 'saveSettings'])->name('operations.settings.save');
});
Route::get('/verifikasi/{token}', [OperationsController::class, 'verify'])->where('token', '[a-f0-9]{64}')->middleware('throttle:30,1')->name('receipt.verify');
Route::middleware('auth')->group(function () {
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup', [BackupController::class, 'download'])->middleware('throttle:2,60')->name('backup.download');
    Route::post('/pembayaran-sandbox/simulasi', [PaymentController::class, 'simulate'])->name('payment.simulate');
});
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
