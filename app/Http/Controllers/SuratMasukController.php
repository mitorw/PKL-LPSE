<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    /**
     * Menampilkan daftar surat masuk dengan filter, pencarian, sorting, dan pagination.
     * Method ini menangani semuanya.
     */
    public function index(Request $request)
    {
        // Mulai query builder, jangan langsung eksekusi
        $query = SuratMasuk::with('disposisi');

        // Terapkan filter PENCARIAN jika ada input 'search'
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = $request->input('search');
            // Kelompokkan kondisi pencarian agar tidak bentrok dengan filter lain
            $q->where(function ($subq) use ($searchTerm) {
                $subq->where('no_surat', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('asal_surat', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('perihal', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('keterangan', 'LIKE', "%{$searchTerm}%");
            });
        });

        // Terapkan filter TANGGAL AWAL jika ada
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('tanggal_terima', '>=', $request->start_date);
        });

        // Terapkan filter TANGGAL AKHIR jika ada
        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('tanggal_terima', '<=', $request->end_date);
        });

        // Terapkan filter KLASIFIKASI jika ada
        $query->when($request->filled('klasifikasi'), function ($q) use ($request) {
            $q->where('klasifikasi', $request->klasifikasi);
        });

        // Terapkan filter STATUS DISPOSISI jika ada
        $query->when($request->filled('disposisi_status'), function ($q) use ($request) {
            if ($request->disposisi_status === 'ada') {
                $q->whereNotNull('id_disposisi');
            } elseif ($request->disposisi_status === 'tidak_ada') {
                $q->whereNull('id_disposisi');
            }
        });

        // Tentukan kolom dan arah SORTING (urutan)
        $sortColumn = $request->input('sort', 'created_at'); // Default sorting
        $sortDirection = $request->input('direction', 'desc');   // Default direction

        // Terapkan sorting ke query
        $query->orderBy($sortColumn, $sortDirection);

        // Eksekusi query dengan PAGINATION di akhir
        // appends($request->query()) penting agar filter tetap aktif saat pindah halaman
        $data = $query->latest()->paginate(10)->appends($request->query());

        // Kembalikan view dengan data yang sudah benar
        return view('admin.surat_masuk.index', [
            'pageTitle' => 'Surat Masuk',
            'data' => $data // Mengirim dengan nama 'data' agar konsisten
        ]);
    }

    // Method 'search' sudah tidak diperlukan lagi.

    public function create()
    {
        $disposisi = Disposisi::all();
        return view('admin.surat_masuk.create', [
            'pageTitle' => 'Tambah Surat Masuk',
            'disposisi' => $disposisi
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa',
            'file_surat' => 'required|mimes:pdf,png,jpg,jpeg|max:2048',
            'dis_bagian' => 'nullable|in:Bagian Layanan Pengadaan Secara Elektronik,Bagian Advokasi dan Pembinaan,Bagian Pengelolaan Pengadaan Barang dan Jasa',
        ]);

        $idDisposisi = null;
        if ($request->filled('dis_bagian')) {
            $disposisi = Disposisi::create([
                'dis_bagian' => $request->dis_bagian,
                'catatan' => $request->catatan,
                'instruksi' => $request->instruksi
            ]);
            $idDisposisi = $disposisi->id_disposisi;
        }

        $fileSuratPath = null;
        if ($request->hasFile('file_surat')) {
            $fileSuratPath = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        SuratMasuk::create([
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'id_disposisi' => $idDisposisi,
            'user_id' => Auth::id(),
            'file_surat' => $fileSuratPath
        ]);

        return redirect()->route('surat_masuk.index')->with('success', 'Surat masuk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $surat = SuratMasuk::with('disposisi')->findOrFail($id);
        return view('admin.surat_masuk.edit', [
            'pageTitle' => 'Edit Surat Masuk',
            'surat' => $surat
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_surat' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa',
            'file_surat' => 'nullable|mimes:pdf,png,jpg,jpeg|max:2048',
            // Tambahkan validasi untuk status disposisi
            'disposisi_status' => 'required|in:ada,tidak',
            'dis_bagian' => 'required_if:disposisi_status,ada|in:Bagian Layanan Pengadaan Secara Elektronik,Bagian Advokasi dan Pembinaan,Bagian Pengelolaan Pengadaan Barang dan Jasa',
        ]);

        $surat = SuratMasuk::findOrFail($id);
        $idDisposisi = $surat->id_disposisi;

        // LOGIKA BARU UNTUK MENGELOLA DISPOSISI
        if ($request->disposisi_status === 'ada') {
            // JIKA USER INGIN ADA DISPOSISI (CREATE ATAU UPDATE)
            $disposisiData = [
                'dis_bagian' => $request->dis_bagian,
                'catatan' => $request->catatan,
                'instruksi' => $request->instruksi
            ];

            if ($idDisposisi) {
                // Jika sudah ada, update disposisi yang lama
                Disposisi::find($idDisposisi)->update($disposisiData);
            } else {
                // Jika belum ada, buat disposisi baru
                $disposisi = Disposisi::create($disposisiData);
                $idDisposisi = $disposisi->id_disposisi; // Dapatkan ID baru
            }
        } else {
            // JIKA USER MEMILIH "TIDAK ADA" DISPOSISI (DELETE)
            if ($idDisposisi) {
                // Jika surat ini punya disposisi, hapus disposisinya
                Disposisi::find($idDisposisi)->delete();
                $idDisposisi = null; // Set ID menjadi null untuk diupdate di tabel surat
            }
        }

        // ... (Logika update file Anda tetap sama) ...
        $fileSuratPath = $surat->file_surat;
        if ($request->hasFile('file_surat')) {
            if ($fileSuratPath && Storage::disk('public')->exists($fileSuratPath)) {
                Storage::disk('public')->delete($fileSuratPath);
            }
            $fileSuratPath = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        // Update data surat masuk
        $surat->update([
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'id_disposisi' => $idDisposisi, // Simpan ID disposisi yang baru (atau null)
            'file_surat' => $fileSuratPath
        ]);

        return redirect()->route('surat_masuk.index')->with('success', 'Surat masuk berhasil diperbarui');
    }

    public function show($id)
    {
        $surat = SuratMasuk::with('disposisi')->findOrFail($id);
        return view('admin.surat_masuk.show', [
            'pageTitle' => 'Detail Surat Masuk',
            'surat' => $surat
        ]);
    }

    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
            Storage::disk('public')->delete($surat->file_surat);
        }
        $surat->delete();
        return redirect()->route('surat_masuk.index')->with('success', 'Surat berhasil dihapus');
    }
}
