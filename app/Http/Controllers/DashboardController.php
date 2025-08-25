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
        // Data untuk Card di Atas
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = SuratKeluar::count();
        $penggunaCount = User::count();

        // Atur lokalisasi ke Bahasa Indonesia
        \Carbon\Carbon::setLocale('id');

        // Data untuk Bar Chart (6 Bulan Terakhir)
        $barChartLabels = [];
        $suratMasukData = [];
        $suratKeluarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $bulan = $date->translatedFormat('F');
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
        $totalSuratBulanIni = $pieChartSuratMasuk + $pieChartSuratKeluar;
        $namaBulanIni = $currentMonth->translatedFormat('F');

        // Kirim semua data ke view HANYA SEKALI di akhir
        return view('admin.dashboard', [
            // Variabel untuk kartu info
            'suratMasuk' => $suratMasukCount,
            'suratKeluar' => $suratKeluarCount,
            'pengguna' => $penggunaCount,

            // Variabel untuk chart
            'barChartLabels' => $barChartLabels,
            'suratMasukData' => $suratMasukData,
            'suratKeluarData' => $suratKeluarData,
            'pieChartData' => $pieChartData,

            // Variabel baru untuk total surat
            'totalSuratBulanIni' => $totalSuratBulanIni,
            'namaBulanIni' => $namaBulanIni,


            // Variabel tambahan
            'pageTitle' => 'Dashboard'
        ]);
    }
}
