<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;

class DashboardController extends Controller
{
    public function index()
    {
        $suratMasuk = SuratMasuk::count();
        $suratKeluar = SuratKeluar::Count();
        $pengguna = Laporan::count();
        return view('admin.dashboard', [
            'suratMasuk' => $suratMasuk,
            'suratKeluar' => $suratKeluar,
            'pengguna' => $pengguna,
            'pageTitle' => 'Dashboard'
        ]);
    }
}
