<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Surat Masuk
    Route::get('/surat_masuk', [SuratMasukController::class, 'index'])->name('surat_masuk.index');
    Route::get('/surat_masuk/search', [SuratMasukController::class, 'search'])->name('surat_masuk.search');
    Route::get('/surat_masuk/{surat_masuk}', [SuratMasukController::class, 'show'])->name('surat_masuk.show');

    // Create/Store/Edit/Update/Delete hanya admin
    Route::middleware(['checkrole:admin'])->group(function () {
        Route::get('/surat_masuk/create', [SuratMasukController::class, 'create'])->name('surat_masuk.create');
        Route::post('/surat_masuk', [SuratMasukController::class, 'store'])->name('surat_masuk.store');
        Route::get('/surat_masuk/{surat_masuk}/edit', [SuratMasukController::class, 'edit'])->name('surat_masuk.edit');
        Route::put('/surat_masuk/{surat_masuk}', [SuratMasukController::class, 'update'])->name('surat_masuk.update');
        Route::delete('/surat_masuk/{surat_masuk}', [SuratMasukController::class, 'destroy'])->name('surat_masuk.destroy');
    });

    // Surat Keluar
    Route::resource('surat_keluar', SuratKeluarController::class);
    Route::get('/surat_keluar/{surat_keluar}', [SuratKeluarController::class, 'show'])->name('surat_keluar.show');

    // Create/Store/Edit/Update/Delete hanya admin
    Route::middleware(['checkrole:admin'])->group(function () {
        Route::get('/surat_keluar/create', [SuratKeluarController::class, 'create'])->name('surat_keluar.create');
        Route::post('/surat_keluar', [SuratKeluarController::class, 'store'])->name('surat_keluar.store');
        Route::get('/surat_keluar/{surat_keluar}/edit', [SuratKeluarController::class, 'edit'])->name('surat_keluar.edit');
        Route::put('/surat_keluar/{surat_keluar}', [SuratKeluarController::class, 'update'])->name('surat_keluar.update');
        Route::delete('/surat_keluar/{surat_keluar}', [SuratKeluarController::class, 'destroy'])->name('surat_keluar.destroy');
    });

    Route::get('/manajemen-akun/create', [UserController::class, 'create'])->name('manajemen_akun.create');

    Route::post('/manajemen-akun', [UserController::class, 'store'])->name('manajemen_akun.store');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan.surat');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');

    // Manajemen Akun
    Route::middleware(['checkrole:admin'])->group(function () {
        Route::get('/manajemen-akun', [UserController::class, 'index'])->name('manajemen_akun.index');
        Route::post('/manajemen-akun/{user}', [UserController::class, 'updateRole'])->name('manajemen_akun.updateRole');
        Route::post('/manajemen_akun/{user}/reset-password', [UserController::class, 'resetPassword'])->name('manajemen_akun.resetPassword');
        Route::delete('/manajemen_akun/{user}', [UserController::class, 'destroy'])->name('manajemen_akun.destroy');

    });
});
require __DIR__ . '/auth.php';
