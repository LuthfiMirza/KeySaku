<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/welcome', '/login')->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/anggaran', [PageController::class, 'budgets'])->name('budgets.index');
    Route::get('/tabungan', [PageController::class, 'goals'])->name('savings.index');
    Route::get('/pengaturan', [PageController::class, 'settings'])->name('settings');
    Route::get('/pengaturan/notifikasi', [PageController::class, 'notificationSettings'])->name('settings.notifications');
    Route::get('/pengaturan/keamanan', [PageController::class, 'securitySettings'])->name('settings.security');
    Route::get('/pengaturan/pasang', [PageController::class, 'installSettings'])->name('settings.install');
    Route::get('/kategori', [PageController::class, 'categories'])->name('categories');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/transaksi', [PageController::class, 'transactions'])->name('transactions.index');
    Route::get('/transaksi/tambah', [PageController::class, 'createTransaction'])->name('transactions.create');
    Route::get('/transaksi/suara', [PageController::class, 'voiceTransaction'])->name('transactions.voice');
    Route::get('/transaksi/{transaction}/edit', [PageController::class, 'editTransaction'])->name('transactions.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
