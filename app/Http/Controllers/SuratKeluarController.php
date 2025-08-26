<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratKeluar::query();

        // Search global (nomor surat, perihal, tujuan, dll)
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_surat', 'like', "%{$keyword}%")
                    ->orWhere('perihal', 'like', "%{$keyword}%")
                    ->orWhere('tujuan', 'like', "%{$keyword}%")
                    ->orWhere('dibuat_oleh', 'like', "%{$keyword}%")
                    ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        // Filter klasifikasi
        if ($request->filled('klasifikasi')) {
            $query->where('klasifikasi', $request->klasifikasi);
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->input('sort'), $request->input('direction'));
        } else {
        }

        $suratKeluar = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.surat_keluar.index', [
            'pageTitle' => 'Surat Keluar',
            'suratKeluar' => $suratKeluar
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
            'isi_surat' => 'required|mimes:pdf,png,jpg,jpeg|max:2048'
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

    public function update(Request $request, $id)
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

        $surat = SuratKeluar::findOrFail($id);

        // Update file kalau ada upload baru
        $filePath = $surat->isi_surat;
        if ($request->hasFile('isi_surat')) {
            $filePath = $request->file('isi_surat')->store('surat_keluar', 'public');
        }

        // Update surat keluar
        $surat->update([
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => $request->dibuat_oleh,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'isi_surat' => $filePath
        ]);

        return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil diperbarui');
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
