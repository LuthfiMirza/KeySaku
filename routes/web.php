<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/welcome', '/login')->name('welcome');

Route::middleware(['auth', \App\Http\Middleware\EnsureHouseholdExists::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/anggaran', [PageController::class, 'budgets'])->name('budgets.index');
    Route::get('/tabungan', [PageController::class, 'goals'])->name('savings.index');
    Route::get('/pengaturan', [PageController::class, 'settings'])->name('settings');
    Route::get('/pengaturan/notifikasi', [PageController::class, 'notificationSettings'])->name('settings.notifications');
    Route::get('/pengaturan/mata-uang', [PageController::class, 'currencySettings'])->name('settings.currency');
    Route::patch('/pengaturan/mata-uang', [PageController::class, 'updateCurrency'])->name('settings.currency.update');
    Route::get('/pengaturan/saldo-awal', [PageController::class, 'initialBalanceSettings'])->name('settings.initial-balance');
    Route::patch('/pengaturan/saldo-awal', [PageController::class, 'updateInitialBalance'])->name('settings.initial-balance.update');
    Route::get('/pengaturan/anggota-keluarga', [PageController::class, 'householdSettings'])->name('settings.household');
    Route::get('/pengaturan/bahasa', [PageController::class, 'languageSettings'])->name('settings.language');
    Route::patch('/pengaturan/bahasa', [PageController::class, 'updateLanguage'])->name('settings.language.update');
    Route::get('/pengaturan/keamanan', [PageController::class, 'securitySettings'])->name('settings.security');
    Route::get('/pengaturan/pasang', [PageController::class, 'installSettings'])->name('settings.install');
    Route::get('/kategori', [PageController::class, 'categories'])->name('categories');
    Route::get('/akun', [PageController::class, 'accounts'])->name('accounts');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export/csv', [ExportController::class, 'monthlyReportCsv'])->name('reports.export.csv');
    Route::get('/laporan/export/xlsx', [ExportController::class, 'monthlyReportXlsx'])->name('reports.export.xlsx');
    Route::get('/laporan/export/pdf', [ExportController::class, 'monthlyReportPdf'])->name('reports.export.pdf');
    Route::get('/transaksi', [PageController::class, 'transactions'])->name('transactions.index');
    Route::get('/transaksi/export/csv', [ExportController::class, 'transactionsCsv'])->name('transactions.export.csv');
    Route::get('/transaksi/export/xlsx', [ExportController::class, 'transactionsXlsx'])->name('transactions.export.xlsx');
    Route::get('/transaksi/tambah', [PageController::class, 'createTransaction'])->name('transactions.create');
    Route::get('/transaksi/{transaction}/edit', [PageController::class, 'editTransaction'])->name('transactions.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
