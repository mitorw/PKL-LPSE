<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMasuk;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = SuratMasuk::count();
        $suratKeluar = 3;
        $pengguna = 2;
        $disposisi = 5;
        return view('admin.dashboard',[
                    'suratMasuk' => $suratMasuk,
                    'suratKeluar' => $suratKeluar,
                    'pengguna' => $pengguna,
                    'disposisi' => $disposisi,
                    'pageTitle' => 'Dashboard'
                    ]);
    }
}
