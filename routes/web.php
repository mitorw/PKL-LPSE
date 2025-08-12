<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard',
        [DashboardController::class, 'index'])->
name('dashboard');


use App\Http\Controllers\SuratKeluarController;

Route::resource('surat_keluar', SuratKeluarController::class);

Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan.surat');
Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');
=======

