<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * ================= DASHBOARD =================
     */
    // ReportDashboardController.php

public function index(Request $request)
{
    // ====== DATA KARTU & CHART ======
    $suratMasukCount  = SuratMasuk::count();
    $suratKeluarCount = SuratKeluar::count();
    $penggunaCount    = User::count();

    Carbon::setLocale('id');

    $barChartLabels = [];
    $suratMasukData = [];
    $suratKeluarData = [];
    for ($i = 5; $i >= 0; $i--) {
        $date  = Carbon::now()->subMonths($i);
        $bulan = $date->translatedFormat('F');
        $tahun = $date->format('Y');

        $barChartLabels[] = $bulan;

        $suratMasukData[] = SuratMasuk::whereYear('tanggal_terima', $tahun)
            ->whereMonth('tanggal_terima', $date->month)->count();
        $suratKeluarData[] = SuratKeluar::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $date->month)->count();
    }

    $currentMonth = Carbon::now();
    $pieChartData = [
        SuratMasuk::whereYear('tanggal_terima', $currentMonth->year)
            ->whereMonth('tanggal_terima', $currentMonth->month)->count(),
        SuratKeluar::whereYear('tanggal', $currentMonth->year)
            ->whereMonth('tanggal', $currentMonth->month)->count(),
    ];
    $totalSuratBulanIni = array_sum($pieChartData);
    $namaBulanIni = $currentMonth->translatedFormat('F');

    // ====== DATA TABEL LAPORAN (FILTER + UNION + PAGINATION) ======
    $suratMasuk  = SuratMasuk::query();
    $suratKeluar = SuratKeluar::query();

    if ($request->filled('nomor_surat')) {
        $nomor = $request->nomor_surat;
        $suratMasuk->where('no_surat', 'like', "%{$nomor}%");
        $suratKeluar->where('nomor_surat', 'like', "%{$nomor}%");
    }
    if ($request->filled('dari_tanggal')) {
        $suratMasuk->whereDate('tanggal_terima', '>=', $request->dari_tanggal);
        $suratKeluar->whereDate('tanggal', '>=', $request->dari_tanggal);
    }
    if ($request->filled('sampai_tanggal')) {
        $suratMasuk->whereDate('tanggal_terima', '<=', $request->sampai_tanggal);
        $suratKeluar->whereDate('tanggal', '<=', $request->sampai_tanggal);
    }
    if ($request->filled('status') && $request->status !== 'all') {
        $suratMasuk->where('klasifikasi', $request->status);
        $suratKeluar->where('klasifikasi', $request->status);
    }
    if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
        if ($request->jenis_surat === 'masuk') {
            $suratKeluar->whereRaw('1=0');
        } elseif ($request->jenis_surat === 'keluar') {
            $suratMasuk->whereRaw('1=0');
        }
    }

    $suratMasukQuery = $suratMasuk->select(
        'no_surat as nomor_surat',
        'tanggal_terima as tanggal',
        'perihal',
        'klasifikasi as status',
        'id_surat_masuk as id',
        'asal_surat as asal',
        'keterangan',
        DB::raw('NULL as tujuan'),
        DB::raw('NULL as dibuat_oleh')
    )->selectRaw("'masuk' as jenis_surat");

    $suratKeluarQuery = $suratKeluar->select(
        'nomor_surat',
        'tanggal',
        'perihal',
        'klasifikasi as status',
        'id',
        DB::raw('NULL as asal'),
        'keterangan',
        'tujuan',
        'dibuat_oleh'
    )->selectRaw("'keluar' as jenis_surat");

    $query = $suratMasukQuery->unionAll($suratKeluarQuery);

    $sortColumn = $request->get('sort', 'tanggal');
    $sortDirection = $request->get('direction', 'desc');
    $data = $query->orderBy($sortColumn, $sortDirection)->get();

    $laporanSurat = $this->paginateCollection($data, 10, $request->page);

    // ====== KIRIM KE VIEW ======
    return view('admin.dashboard', [
        'suratMasuk' => $suratMasukCount,
        'suratKeluar' => $suratKeluarCount,
        'pengguna' => $penggunaCount,
        'barChartLabels' => $barChartLabels,
        'suratMasukData' => $suratMasukData,
        'suratKeluarData' => $suratKeluarData,
        'pieChartData' => $pieChartData,
        'totalSuratBulanIni' => $totalSuratBulanIni,
        'namaBulanIni' => $namaBulanIni,
        'laporanSurat' => $laporanSurat,   // <-- ini kuncinya
        'pageTitle' => 'Dashboard',
    ]);
}


    /**
     * ================= LAPORAN =================
     */
    public function laporan(Request $request)
    {
        $pageTitle = 'Laporan';

        $suratMasuk = SuratMasuk::query();
        $suratKeluar = SuratKeluar::query();

        // Filter Nomor Surat
        if ($request->filled('nomor_surat')) {
            $nomorSurat = $request->nomor_surat;
            $suratMasuk->where('no_surat', 'like', '%' . $nomorSurat . '%');
            $suratKeluar->where('nomor_surat', 'like', '%' . $nomorSurat . '%');
        }

        // Filter Tanggal
        if ($request->filled('dari_tanggal')) {
            $suratMasuk->whereDate('tanggal_terima', '>=', $request->dari_tanggal);
            $suratKeluar->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $suratMasuk->whereDate('tanggal_terima', '<=', $request->sampai_tanggal);
            $suratKeluar->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        // Filter Status (klasifikasi)
        if ($request->filled('status') && $request->status !== 'all') {
            $suratMasuk->where('klasifikasi', $request->status);
            $suratKeluar->where('klasifikasi', $request->status);
        }

        // Filter Jenis Surat
        if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
            if ($request->jenis_surat == 'masuk') {
                $suratKeluar->whereRaw('1=0');
            } elseif ($request->jenis_surat == 'keluar') {
                $suratMasuk->whereRaw('1=0');
            }
        }

        // UNION Query
        $suratMasukQuery = $suratMasuk->select(
            'no_surat as nomor_surat',
            'tanggal_terima as tanggal',
            'perihal',
            'klasifikasi as status',
            'id_surat_masuk as id',
            'asal_surat as asal',
            'keterangan',
            DB::raw('NULL as tujuan'),
            DB::raw('NULL as dibuat_oleh')
        )->selectRaw("'masuk' as jenis_surat");

        $suratKeluarQuery = $suratKeluar->select(
            'nomor_surat',
            'tanggal',
            'perihal',
            'klasifikasi as status',
            'id',
            DB::raw('NULL as asal'),
            'keterangan',
            'tujuan',
            'dibuat_oleh'
        )->selectRaw("'keluar' as jenis_surat");

        $query = $suratMasukQuery->unionAll($suratKeluarQuery);

        $sortColumn = $request->get('sort', 'tanggal');
        $sortDirection = $request->get('direction', 'desc');
        $data = $query->orderBy($sortColumn, $sortDirection)->get();

        $laporanSurat = $this->paginateCollection($data, 10, $request->page);

        return view('admin.Surat.laporan', compact('laporanSurat', 'pageTitle'));
    }

    public function cetakLaporan(Request $request)
    {
        // Tentukan periode
        $startDate = $request->filled('dari_tanggal') ? $request->dari_tanggal : null;
        $endDate   = $request->filled('sampai_tanggal') ? $request->sampai_tanggal : null;

        if (!$startDate && !$endDate && $request->filled('periode')) {
            $today = Carbon::today();
            switch ($request->periode) {
                case '1hari': $startDate = $today->toDateString(); $endDate = $startDate; break;
                case '7hari': $startDate = $today->copy()->subDays(6)->toDateString(); $endDate = $today->toDateString(); break;
                case '14hari': $startDate = $today->copy()->subDays(13)->toDateString(); $endDate = $today->toDateString(); break;
                case '1bulan': $startDate = $today->copy()->startOfMonth()->toDateString(); $endDate = $today->copy()->endOfMonth()->toDateString(); break;
            }
        }

        $suratMasuk  = SuratMasuk::query();
        $suratKeluar = SuratKeluar::query();

        // Filter lain
        if ($request->filled('nomor_surat')) {
            $nomorSurat = $request->nomor_surat;
            $suratMasuk->where('no_surat', 'like', '%' . $nomorSurat . '%');
            $suratKeluar->where('nomor_surat', 'like', '%' . $nomorSurat . '%');
        }
        if ($startDate) {
            $suratMasuk->whereDate('tanggal_terima', '>=', $startDate);
            $suratKeluar->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $suratMasuk->whereDate('tanggal_terima', '<=', $endDate);
            $suratKeluar->whereDate('tanggal', '<=', $endDate);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $suratMasuk->where('klasifikasi', $request->status);
            $suratKeluar->where('klasifikasi', $request->status);
        }
        if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
            if ($request->jenis_surat == 'masuk') {
                $suratKeluar->whereRaw('1=0');
            } elseif ($request->jenis_surat == 'keluar') {
                $suratMasuk->whereRaw('1=0');
            }
        }

        // UNION
        $suratMasukQuery = $suratMasuk->select(
            'no_surat as nomor_surat',
            'tanggal_terima as tanggal',
            'perihal',
            'klasifikasi as status',
            'id_surat_masuk as id',
            'asal_surat as asal',
            'keterangan',
            DB::raw('NULL as tujuan'),
            DB::raw('NULL as dibuat_oleh'),
            DB::raw("'masuk' as jenis_surat")
        );
        $suratKeluarQuery = $suratKeluar->select(
            'nomor_surat',
            'tanggal',
            'perihal',
            'klasifikasi as status',
            'id',
            DB::raw('NULL as asal'),
            'keterangan',
            'tujuan',
            'dibuat_oleh',
            DB::raw("'keluar' as jenis_surat")
        );

        $query = $suratMasukQuery->unionAll($suratKeluarQuery);
        $sortColumn = $request->get('sort', 'tanggal');
        $sortDirection = $request->get('direction', 'desc');
        $dataSurat = $query->orderBy($sortColumn, $sortDirection)->get();

        // Hitung Pie Chart
        $totalMasuk  = $dataSurat->where('jenis_surat', 'masuk')->count();
        $totalKeluar = $dataSurat->where('jenis_surat', 'keluar')->count();

        $url = "https://quickchart.io/chart";
        $chartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => ['Surat Masuk', 'Surat Keluar'],
                'datasets' => [[
                    'data' => [$totalMasuk, $totalKeluar],
                    'backgroundColor' => ['#198754', '#dc3545']
                ]]
            ],
            'options' => ['plugins' => ['legend' => ['position' => 'bottom']]]
        ];
        $response = file_get_contents($url . '?c=' . urlencode(json_encode($chartConfig)) . '&format=png&width=350&height=350');
        $chartBase64 = base64_encode($response);

        // Export PDF
        $pdf = Pdf::loadView('admin.Surat.laporan-pdf', [
            'dataSurat'      => $dataSurat,
            'tglAwal'        => $startDate,
            'tglAkhir'       => $endDate,
            'chartBase64'    => $chartBase64,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('Laporan-Surat-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Pagination Custom
     */
    protected function paginateCollection($items, $perPage, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }
}
