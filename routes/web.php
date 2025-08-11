<?php

use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;

// ... (rute lainnya)

Route::get('/dashboard', [SuratController::class, 'dashboard'])->name('dashboard');
Route::get('/laporan', [SuratController::class, 'laporan'])->name('laporan.surat');