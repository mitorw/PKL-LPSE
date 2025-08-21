<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporan(Request $request)
    {
        $pageTitle = 'Laporan';

        // Query dasar untuk Surat Masuk dan Surat Keluar
        $suratMasuk = SuratMasuk::query();
        $suratKeluar = SuratKeluar::query();

        // --- Logika Filter ---

        // Filter Berdasarkan 'nomor_surat'
        if ($request->filled('nomor_surat')) {
            $nomorSurat = $request->nomor_surat;
            $suratMasuk->where('no_surat', 'like', '%' . $nomorSurat . '%');
            $suratKeluar->where('nomor_surat', 'like', '%' . $nomorSurat . '%');
        }

        // Filter Berdasarkan 'tanggal'
        if ($request->filled('dari_tanggal')) {
            $suratMasuk->whereDate('tanggal_terima', '>=', $request->dari_tanggal);
            $suratKeluar->whereDate('tanggal', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $suratMasuk->whereDate('tanggal_terima', '<=', $request->sampai_tanggal);
            $suratKeluar->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        // Filter Berdasarkan 'status' (klasifikasi)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            $suratMasuk->where('klasifikasi', $status);
            $suratKeluar->where('klasifikasi', $status);
        }

        // --- Perbaikan filter jenis_surat di sini ---
        if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
            $jenisSurat = $request->jenis_surat;
            // Jika filter adalah 'masuk', hanya ambil dari tabel surat_masuk
            if ($jenisSurat == 'masuk') {
                $suratKeluar->whereRaw('1=0'); // Query akan selalu false, jadi hasilnya kosong
            }
            // Jika filter adalah 'keluar', hanya ambil dari tabel surat_keluar
            else if ($jenisSurat == 'keluar') {
                $suratMasuk->whereRaw('1=0'); // Query akan selalu false, jadi hasilnya kosong
            }
        }

        // --- Proses SELECT dan UNION ---
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
    DB::raw('NULL as keterangan'),
    'tujuan',
    'dibuat_oleh'
)->selectRaw("'keluar' as jenis_surat");
        // Menggabungkan kedua query
        $query = $suratMasukQuery->unionAll($suratKeluarQuery);

        // ... (Logika Sorting dan Paginasi) ...
        $sortColumn = $request->get('sort', 'tanggal');
        $sortDirection = $request->get('direction', 'desc');

        $data = $query->orderBy($sortColumn, $sortDirection)->get();
        $laporanSurat = $this->paginateCollection($data, 10, $request->page);

        return view('admin.Surat.laporan', compact('laporanSurat', 'pageTitle'));
    }

public function cetakLaporan(Request $request)
{
    // --- Query dasar ---
    $suratMasuk = SuratMasuk::query();
    $suratKeluar = SuratKeluar::query();

    // --- Filter ---
    if ($request->filled('nomor_surat')) {
        $nomorSurat = $request->nomor_surat;
        $suratMasuk->where('no_surat', 'like', '%' . $nomorSurat . '%');
        $suratKeluar->where('nomor_surat', 'like', '%' . $nomorSurat . '%');
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
        $status = $request->status;
        $suratMasuk->where('klasifikasi', $status);
        $suratKeluar->where('klasifikasi', $status);
    }

    if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
        $jenisSurat = $request->jenis_surat;
        if ($jenisSurat == 'masuk') {
            $suratKeluar->whereRaw('1=0'); // kosongkan keluar
        } else if ($jenisSurat == 'keluar') {
            $suratMasuk->whereRaw('1=0'); // kosongkan masuk
        }
    }

    // --- UNION dengan tambahan kolom ---
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
    DB::raw('NULL as keterangan'),
    'tujuan',
    'dibuat_oleh'
)->selectRaw("'keluar' as jenis_surat");

    $query = $suratMasukQuery->unionAll($suratKeluarQuery);

    $sortColumn = $request->get('sort', 'tanggal');
    $sortDirection = $request->get('direction', 'desc');
    $dataSurat = $query->orderBy($sortColumn, $sortDirection)->get();

    // --- DATA DISPOSISI (hanya untuk surat masuk) ---
    if ($request->filled('jenis_surat') && $request->jenis_surat === 'keluar') {
        $disposisiSurat = collect(); // kosong
    } else {
        $disposisiSurat = SuratMasuk::with('disposisi')
            ->when($request->filled('nomor_surat'), function($q) use ($request) {
                $q->where('no_surat', 'like', '%' . $request->nomor_surat . '%');
            })
            ->when($request->filled('dari_tanggal'), function($q) use ($request) {
                $q->whereDate('tanggal_terima', '>=', $request->dari_tanggal);
            })
            ->when($request->filled('sampai_tanggal'), function($q) use ($request) {
                $q->whereDate('tanggal_terima', '<=', $request->sampai_tanggal);
            })
            ->when($request->filled('status') && $request->status !== 'all', function($q) use ($request) {
                $q->where('klasifikasi', $request->status);
            })
            ->orderBy('tanggal_terima', $sortDirection)
            ->get();
    }

    // --- Generate PDF ---
    $pdf = Pdf::loadView('admin.Surat.laporan-pdf', compact('dataSurat', 'disposisiSurat'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream('Laporan-Surat-' . now()->format('Y-m-d') . '.pdf');
}



    protected function paginateCollection($items, $perPage, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }
}
