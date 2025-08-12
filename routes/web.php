<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// ... (rute lainnya)

Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan.surat');
Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');