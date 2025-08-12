<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Surat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini untuk PDF

class LaporanController extends Controller
{
    // ... (Method dashboard) ...

    public function laporan(Request $request)
    {
        $pageTitle = 'Laporan Inventarisasi Surat';

        // Mulai query ke tabel surat
        $query = Laporan::query();

        // Logika Filter
        if ($request->filled('nomor_surat')) {
            $query->where('nomor_surat', 'like', '%' . $request->nomor_surat . '%');
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_mulai', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'tanggal_mulai');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);

        // Ambil data dengan pagination
        $laporanSurat = $query->paginate(10);

        return view('admin.Surat.laporan', compact('laporanSurat', 'pageTitle'));
    }

    public function cetakLaporan(Request $request)
    {
        // Logika filter yang sama seperti di atas
        $query = Laporan::query();
        if ($request->filled('nomor_surat')) {
            $query->where('nomor_surat', 'like', '%' . $request->nomor_surat . '%');
        }
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_mulai', '<=', $request->sampai_tanggal);
        }
        if ($request->filled('jenis_surat') && $request->jenis_surat !== 'all') {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Ambil semua data tanpa pagination
        $dataSurat = $query->get();

        // Render data ke dalam view PDF
        $pdf = Pdf::loadView('admin.Surat.laporan-pdf', compact('dataSurat'));

        // Unduh file PDF
        return $pdf->download('Laporan-Surat-' . now()->format('Y-m-d') . '.pdf');
    }
}