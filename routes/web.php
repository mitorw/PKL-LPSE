<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::resource('surat_masuk', SuratMasukController::class);
Route::get('/surat-masuk/search', [SuratMasukController::class, 'search'])->name('surat_masuk.search');
Route::resource('surat_keluar', SuratKeluarController::class);
Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan.surat');
Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)->name('dashboard');


Route::get('/manajemen-akun', [UserController::class, 'index'])->name('manajemen_akun.index');
Route::post('/manajemen-akun/{user}', [UserController::class, 'updateRole'])->name('manajemen_akun.updateRole');

