<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = SuratMasuk::count();
        $suratKeluar = SuratKeluar::Count();
        $pengguna = 2;
        $disposisi = 5;
        return view('admin.dashboard', [
            'suratMasuk' => $suratMasuk,
            'suratKeluar' => $suratKeluar,
            'pengguna' => $pengguna,
            'disposisi' => $disposisi,
            'pageTitle' => 'Dashboard'
        ]);
    }
}
