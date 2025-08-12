<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::resource('surat_masuk', SuratMasukController::class);
Route::get('/surat-masuk/search', [SuratMasukController::class, 'search'])->name('surat_masuk.search');
Route::resource('surat_keluar', SuratKeluarController::class);
Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan.surat');
Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');

Route::get('/dashboard',
        [DashboardController::class, 'index'])->
        name('dashboard');
