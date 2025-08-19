<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHKAN BARIS INI



class SuratMasukController extends Controller
{
    public function search(Request $request)
    {
        // Mulai query Eloquent untuk model SuratMasuk
        $query = SuratMasuk::with('disposisi');

        // Cek apakah ada input pencarian
        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            // Tambahkan kondisi where untuk pencarian
            $query->where('no_surat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('asal_surat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('perihal', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('tanggal_terima', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('keterangan', 'LIKE', "%{$searchTerm}%");
        }

        if ($request->has('start_date') && !empty($request->start_date)) {
        $query->whereDate('tanggal_terima', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('tanggal_terima', '<=', $request->end_date);
        }

        // Tambahkan logika untuk filter klasifikasi
        if ($request->has('klasifikasi') && !empty($request->klasifikasi)) {
            $query->where('klasifikasi', $request->klasifikasi);
        }

        if ($request->has('disposisi_status') && !empty($request->disposisi_status)) {
        if ($request->disposisi_status === 'ada') {
            $query->whereNotNull('id_disposisi'); // Hanya tampilkan surat yang id_disposisi-nya tidak kosong
        } elseif ($request->disposisi_status === 'tidak_ada') {
            $query->whereNull('id_disposisi'); // Hanya tampilkan surat yang id_disposisi-nya kosong
        }
    }

        // Dapatkan data surat masuk yang sudah difilter dan/atau dicari
        $data = $query->get();

        return view('admin.surat_masuk.index', [
            'pageTitle' => 'Surat Masuk',
            'suratMasuk' => $data
        ]);
    }

    public function index()
    {
        $data = SuratMasuk::with('disposisi')->get();
        return view('admin.surat_masuk.index', [
            'pageTitle' => 'Surat Masuk',
            'suratMasuk' => $data
        ]);
    }

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
            'no_surat' => 'required',
            'asal_surat' => 'required',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa',
            'file_surat' => 'nullable|mimes:pdf|max:2048',
            'dis_bagian' => 'nullable|in:Bagian 1,Bagian 2,Bagian 3',
        ]);

        $idDisposisi = null;

        if ($request->dis_bagian) {
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
            'user_id' => Auth::id(), // <-- INI YANG DIUBAH
            'file_surat' => $fileSuratPath
        ]);

        return redirect()->route('surat_masuk.index')->with('success', 'Surat masuk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $disposisi = Disposisi::all();

        return view('admin.surat_masuk.edit', [
            'pageTitle' => 'Edit Surat Masuk',
            'surat' => $surat,
            'disposisi' => $disposisi
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_surat' => 'required',
            'asal_surat' => 'required',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa',
            'file_surat' => 'nullable|mimes:pdf|max:2048',
            'dis_bagian' => 'nullable|in:Bagian 1,Bagian 2,Bagian 3',
        ]);

        $surat = SuratMasuk::findOrFail($id);

        // Update disposisi
        $idDisposisi = $surat->id_disposisi;
        if ($request->dis_bagian) {
            if ($idDisposisi) {
                $disposisi = Disposisi::find($idDisposisi);
                $disposisi->update([
                    'dis_bagian' => $request->dis_bagian,
                    'catatan' => $request->catatan,
                    'instruksi' => $request->instruksi
                ]);
            } else {
                $disposisi = Disposisi::create([
                    'dis_bagian' => $request->dis_bagian,
                    'catatan' => $request->catatan,
                    'instruksi' => $request->instruksi
                ]);
                $idDisposisi = $disposisi->id_disposisi;
            }
        }

        // Update file kalau ada upload baru
        $fileSuratPath = $surat->file_surat;
        if ($request->hasFile('file_surat')) {
            $fileSuratPath = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        // Update surat masuk
        $surat->update([
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'id_disposisi' => $idDisposisi,
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

        // Hapus file surat di storage jika ada
        if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
            Storage::disk('public')->delete($surat->file_surat);
        }

        // Hapus data surat
        $surat->delete();

        return redirect()->route('surat_masuk.index')->with('success', 'Surat berhasil dihapus');
    }



}
