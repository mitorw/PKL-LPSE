<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = 8;
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
