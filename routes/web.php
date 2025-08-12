<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;


Route::resource('surat_masuk', SuratMasukController::class);
Route::get('/surat-masuk/search', [SuratMasukController::class, 'search'])->name('surat_masuk.search');

Route::get('/dashboard',
        [DashboardController::class, 'index'])->
        name('dashboard');
