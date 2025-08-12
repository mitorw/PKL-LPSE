<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/dashboard',
        [DashboardController::class, 'index'])->
name('dashboard');


use App\Http\Controllers\SuratKeluarController;

Route::resource('surat_keluar', SuratKeluarController::class);
