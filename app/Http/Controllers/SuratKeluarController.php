<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $data = SuratKeluar::latest()->get();
        return view('admin.surat_keluar.index',[
            'pageTitle' => 'Surat Keluar',
            'suratKeluar' => $data
        ]);
    }

    public function create()
    {
        return view('admin.surat_keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|max:50',
            'perihal' => 'required|max:255',
            'tujuan' => 'required|max:255',
            'tanggal' => 'required|date',
            'dibuat_oleh' => 'required|max:50',
            'klasifikasi' => 'required|in:biasa,penting,rahasia',
            'isi_surat' => 'nullable|mimes:pdf,png,jpg,jpeg|max:2048'
        ]);

        $filePath = null;
        if ($request->hasFile('isi_surat')) {
            $filePath = $request->file('isi_surat')->store('surat_keluar', 'public');
        }

        SuratKeluar::create([
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => $request->dibuat_oleh,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'isi_surat' => $filePath
        ]);

        return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil disimpan.');
    }

        public function edit($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        return view('admin.surat_keluar.edit', [
            'pageTitle' => 'Edit Surat Keluar',
            'surat' => $surat,
        ]);
    }

    public function destroy($id)
{
    $surat = SuratKeluar::findOrFail($id);
    // Jika ada file isi_surat, hapus file-nya dari storage
    if ($surat->isi_surat && Storage::disk('public')->exists($surat->isi_surat)) {
        Storage::disk('public')->delete($surat->isi_surat);
    }
    $surat->delete();

    return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil dihapus.');
}


    public function show($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        return view('admin.surat_keluar.show', compact('surat'));
    }
}
