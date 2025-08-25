<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- LANGKAH 1: KUMPULKAN SEMUA DATA ---

        // Data untuk Card di Atas
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = SuratKeluar::count();
        $penggunaCount = User::count();

        // Data untuk Bar Chart (6 Bulan Terakhir)
        $barChartLabels = [];
        $suratMasukData = [];
        $suratKeluarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulan = $date->format('F');
            $tahun = $date->format('Y');

            array_push($barChartLabels, $bulan);

            $masuk = SuratMasuk::whereYear('tanggal_terima', $tahun)
                               ->whereMonth('tanggal_terima', $date->month)
                               ->count();
            array_push($suratMasukData, $masuk);

            $keluar = SuratKeluar::whereYear('tanggal', $tahun)
                                 ->whereMonth('tanggal', $date->month)
                                 ->count();
            array_push($suratKeluarData, $keluar);
        }

        // Data untuk Pie Chart (Bulan Ini)
        $currentMonth = Carbon::now();
        $pieChartSuratMasuk = SuratMasuk::whereYear('tanggal_terima', $currentMonth->year)
                                        ->whereMonth('tanggal_terima', $currentMonth->month)
                                        ->count();
        $pieChartSuratKeluar = SuratKeluar::whereYear('tanggal', $currentMonth->year)
                                          ->whereMonth('tanggal', $currentMonth->month)
                                          ->count();
        $pieChartData = [$pieChartSuratMasuk, $pieChartSuratKeluar];


        // --- LANGKAH 2: KIRIM SEMUA DATA KE VIEW (HANYA SEKALI DI AKHIR) ---
        return view('admin.dashboard', [
            // Data untuk card
            'suratMasuk' => $suratMasukCount,
            'suratKeluar' => $suratKeluarCount,
            'pengguna' => $penggunaCount,

            // Data untuk chart
            'barChartLabels' => $barChartLabels,
            'suratMasukData' => $suratMasukData,
            'suratKeluarData' => $suratKeluarData,
            'pieChartData' => $pieChartData,

            // Data tambahan jika ada
            'pageTitle' => 'Dashboard'
        ]);
    }
}
