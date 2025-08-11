<?php

namespace App\Http\Controllers;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = 8;
        $suratKeluar = SuratKeluar::Count();
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
