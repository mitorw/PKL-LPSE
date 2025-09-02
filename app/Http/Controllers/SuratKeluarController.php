<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;

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
            'nomor_surat' => 'required|max:50|unique:surat_keluar,nomor_surat',
            'perihal' => 'required|max:255',
            'tujuan' => 'required|max:255',
            'tanggal' => 'required|date',
            'dibuat_oleh' => 'required|max:50',
            'klasifikasi' => 'required|in:biasa,penting,rahasia',
            'isi_surat' => 'required|mimes:pdf,png,jpg,jpeg|max:5120',
            'isi_surat_original' => 'nullable|string|max:255',
        ]);

        $filePath = null;
        if ($request->hasFile('isi_surat')) {
            $file = $request->file('isi_surat');

            $ext = strtolower($file->getClientOriginalExtension());

            $noSurat = safeFileName($request->nomor_surat);
            $tanggalSurat = \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');

            // bikin nama dasar file
            $baseFileName = $noSurat . '_' . $tanggalSurat . '.pdf';
            $baseOriginal  = $noSurat . '_' . $tanggalSurat . '.' . $ext;

            // Case 1: langsung PDF
            if ($ext === 'pdf') {
                // Simpan file original PDF
                $originalPath = $file->storeAs('surat_keluar/original', $baseOriginal, 'public');
                $filePath = $originalPath; // karena sudah PDF, hasil konversi = original
            }

            // Case 2: banyak gambar -> PDF multi halaman
            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $manager = new ImageManager(new Driver());

                    // Simpan file original
                    $originalPath = $file->storeAs('surat_keluar/original', $baseOriginal, 'public');

                    // Convert ke base64 untuk dimasukkan ke PDF
                    $image = $manager->read($file->getPathname())->toJpeg();
                    $html = '
                        <html>
                        <head>
                            <style>
                                body {
                                    margin: 0;
                                    padding: 0;
                                    text-align: center;
                                }
                                img {
                                    max-width: 100%;
                                    max-height: 100%;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="data:image/jpeg;base64,' . base64_encode($image) . '">
                        </body>
                        </html>';


                // Buat PDF hasil konversi
                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                $filename = 'surat_keluar/converted/' . $baseFileName;
                Storage::disk('public')->put($filename, $pdf->output());
                $filePath = $filename;
            }
        }

        SuratKeluar::create([
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => $request->dibuat_oleh,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'isi_surat' => $filePath,
            'isi_surat_original' => $originalPath ?? null,
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
        $surat = SuratKeluar::findOrFail($id);

        $request->validate([
            'nomor_surat' => [
                'required',
                'max:50',
                // Aturan ini memastikan nomor_surat unik di tabel surat_keluar,
                // dengan mengabaikan data surat yang ID-nya sedang diedit.
                Rule::unique('surat_keluar')->ignore($surat->id)
            ],
            'perihal' => 'required|max:255',
            'tujuan' => 'required|max:255',
            'tanggal' => 'required|date',
            'dibuat_oleh' => 'required|max:50',
            'klasifikasi' => 'required|in:biasa,penting,rahasia',
            'isi_surat' => 'nullable|mimes:pdf,png,jpg,jpeg|max:5120',
            'isi_surat_original' => 'nullable|string|max:255',
        ]);


        $filePath = $surat->isi_surat;             // converted (PDF)
        $originalPath = $surat->isi_surat_original; // original

        if ($request->hasFile('isi_surat')) {
            // Hapus file lama (converted + original)
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($originalPath && Storage::disk('public')->exists($originalPath)) {
                Storage::disk('public')->delete($originalPath);
            }

            $file = $request->file('isi_surat');
            $ext  = strtolower($file->getClientOriginalExtension());
            $noSurat = safeFileName($request->nomor_surat);
            $tanggalSurat = \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');

            // bikin nama dasar file
            $baseFileName = $noSurat . '_' . $tanggalSurat . '.pdf';
            $baseOriginal  = $noSurat . '_' . $tanggalSurat . '.' . $ext;

            // Simpan original dulu
            $originalPath = $file->storeAs('surat_keluar/original', $baseOriginal, 'public');

            // Case 1: kalau sudah PDF → tidak perlu convert
            if ($ext === 'pdf') {
                $filePath = $originalPath;
            }
            // Case 2: gambar → PDF
            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getPathname())->toJpeg();

                $html = '
                        <html>
                        <head>
                            <style>
                                body {
                                    margin: 0;
                                    padding: 0;
                                    text-align: center;
                                }
                                img {
                                    max-width: 100%;
                                    max-height: 100%;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="data:image/jpeg;base64,' . base64_encode($image) . '">
                        </body>
                        </html>';

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                $filePath = 'surat_keluar/converted/' . $baseFileName;
                Storage::disk('public')->put($filePath, $pdf->output());
            }
        }

        $surat->update([
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => $request->dibuat_oleh,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'isi_surat' => $filePath,              // converted
            'isi_surat_original' => $originalPath, // original
        ]);

        return redirect()->route('surat_keluar.index')
            ->with('success', 'Surat keluar berhasil diperbarui.');
    }



    public function destroy($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        // Hapus file converted
        if ($surat->isi_surat && Storage::disk('public')->exists($surat->isi_surat)) {
            Storage::disk('public')->delete($surat->isi_surat);
        }

        // Hapus file original
        if ($surat->isi_surat_original && Storage::disk('public')->exists($surat->isi_surat_original)) {
            Storage::disk('public')->delete($surat->isi_surat_original);
        }

        // Hapus data dari database
        $surat->delete();

        return redirect()->route('surat_keluar.index')
            ->with('success', 'Surat keluar beserta file terkait berhasil dihapus.');
    }



    public function show($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        return view('admin.surat_keluar.show', compact('surat'));
    }
}
