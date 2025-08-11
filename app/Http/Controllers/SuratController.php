<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    // app/Http/Controllers/SuratController.php

public function dashboard()
{
    // Hitung total surat masuk, surat keluar, pengguna, dan disposisi
    $suratMasuk = Surat::where('jenis_surat', 'masuk')->count();
    $suratKeluar = Surat::where('jenis_surat', 'keluar')->count();
    
    // Ini contoh statis, sesuaikan dengan logika aplikasi Anda
    $pengguna = 2; 
    $disposisi = 5;

    // Kirim variabel dengan nama yang sesuai dengan view
   return view('admin.Surat.dashboard', compact('suratMasuk', 'suratKeluar', 'pengguna', 'disposisi'));
}

    public function laporan(Request $request)
    {
        $query = Surat::query();

        // Implementasi filter berdasarkan gambar di kiri
        if ($request->filled('nomor_surat')) {
            $query->where('nomor_surat', 'like', '%' . $request->nomor_surat . '%');
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('jenis_surat') && $request->jenis_surat != 'all') {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        
        // Sorting
        $sortColumn = $request->get('sort', 'tanggal_mulai');
        $sortDirection = $request->get('direction', 'asc');
        
        $query->orderBy($sortColumn, $sortDirection);

        $laporanSurat = $query->paginate(10); // Paginate 10 data per halaman

        return view('admin.Surat.laporan', compact('laporanSurat'));
    }
}