<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = SuratMasuk::count();
        $suratKeluar = SuratKeluar::Count();
        $pengguna = User::count();
        return view('admin.dashboard', [
            'suratMasuk' => $suratMasuk,
            'suratKeluar' => $suratKeluar,
            'pengguna' => $pengguna,
            'pageTitle' => 'Dashboard'
        ]);
    }
}
