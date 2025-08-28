<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\IOFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;   // kalau pakai GD
use Dompdf\Dompdf;


class SuratMasukController extends Controller
{
    /**
     * Menampilkan daftar surat masuk dengan filter, pencarian, sorting, dan pagination.
     * Method ini menangani semuanya.
     */
    public function index(Request $request)
    {
        // BARU: Mengambil semua nama bagian yang unik dari tabel disposisi untuk dikirim ke view
        $daftarBagian = Disposisi::select('dis_bagian')->distinct()->orderBy('dis_bagian')->get();
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

        // BARU: Logika filter disposisi yang lebih canggih
        $query->when($request->filled('disposisi_status'), function ($q) use ($request) {
            if ($request->disposisi_status === 'ada') {
                $q->whereHas('disposisi'); // Hanya tampilkan surat yang punya relasi disposisi
            } elseif ($request->disposisi_status === 'tidak_ada') {
                $q->whereDoesntHave('disposisi'); // Hanya tampilkan surat yang TIDAK punya relasi disposisi
            }
        });

        // BARU: Terapkan filter berdasarkan BAGIAN disposisi jika ada
        $query->when($request->filled('dis_bagian'), function ($q) use ($request) {
            $q->whereHas('disposisi', function ($disposisiQuery) use ($request) {
                $disposisiQuery->where('dis_bagian', $request->dis_bagian);
            });
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
            'data' => $data, // Mengirim dengan nama 'data' agar konsisten
            'daftarBagian' => $daftarBagian // Kirim daftar bagian ke view
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
            'file_surat' => 'required|mimes:pdf,png,jpg,jpeg,doc,docx|max:5120',
            'dis_bagian' => 'nullable|in:Bagian Layanan Pengadaan Secara Elektronik,Bagian Advokasi dan Pembinaan,Bagian Pengelolaan Pengadaan Barang dan Jasa',
            'file_surat_original' => 'nullable|string|max:255',
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
            $files = $request->file('file_surat');
            if (!is_array($files)) {
                $files = [$files];
            }

            $ext = strtolower($files[0]->getClientOriginalExtension());

            // Case 1: PDF langsung
            if ($ext === 'pdf' && count($files) === 1) {
                $originalPath = $files[0]->store('surat_masuk/original', 'public');
                $fileSuratPath = $originalPath;
            }
            // Case 2: banyak gambar -> PDF multi halaman
            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $html = '';
                $manager = new ImageManager(new Driver());

                foreach ($files as $file) {
                    $originalPath = $files[0]->store('surat_masuk/original', 'public');
                    $image = $manager->read($file->getPathname())->toJpeg();
                    $html .= '<div style="page-break-after: always; text-align:center;">
                            <img src="data:image/jpeg;base64,' . base64_encode($image) . '" style="max-width:100%;height:auto;">
                          </div>';
                }

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                $filename = 'surat_masuk/converted/' . uniqid() . '.pdf';
                Storage::disk('public')->put($filename, $pdf->output());
                $fileSuratPath = $filename;
            }
            // Case 3: Word -> PDF
            elseif (in_array($ext, ['doc', 'docx']) && count($files) === 1) {
                $originalPath = $files[0]->store('surat_masuk/original', 'public');

                \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
                \PhpOffice\PhpWord\Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));


                $phpWord = IOFactory::load($files[0]->getPathName());
                $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');

                $filename = 'surat_masuk/converted/' . uniqid() . '.pdf';
                $tempPath = storage_path('app/public/' . $filename);
                $pdfWriter->save($tempPath);

                $fileSuratPath = $filename;
            }
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
            'file_surat' => $fileSuratPath,
            'file_surat_original' => $originalPath ?? null,
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
            'file_surat' => 'nullable|mimes:pdf,png,jpg,jpeg,doc,docx|max:5120',
            'file_surat_original' => 'nullable|string|max:255',
            // Tambahkan validasi untuk status disposisi
            'disposisi_status' => 'required|in:ada,tidak',
            'dis_bagian' => 'nullable|required_if:disposisi_status,ada|in:Bagian Layanan Pengadaan Secara Elektronik,Bagian Advokasi dan Pembinaan,Bagian Pengelolaan Pengadaan Barang dan Jasa',
        ]);

        $surat = SuratMasuk::findOrFail($id);   // converted (PDF)

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

        $fileSuratPath = $surat->file_surat;
        $originalPath = $surat->file_surat_original; // original

        if ($request->hasFile('file_surat')) {
            if ($fileSuratPath && Storage::disk('public')->exists($fileSuratPath)) {
                Storage::disk('public')->delete($fileSuratPath);
            }
            if ($originalPath && Storage::disk('public')->exists($originalPath)) {
                Storage::disk('public')->delete($originalPath);
            }

            $file = $request->file('file_surat');
            $ext = strtolower($file->getClientOriginalExtension());

            $originalPath = $file->store('surat_masuk/original', 'public');
            // Case 1: kalau sudah PDF → tidak perlu convert
            if ($ext === 'pdf') {
                $fileSuratPath = $originalPath;
            }
            // Case 2: gambar → PDF
            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getPathname())->toJpeg();

                $html = '<div style="page-break-after: always; text-align:center;">
                <img src="data:image/jpeg;base64,' . base64_encode($image) . '" style="max-width:100%;height:auto;">
            </div>';

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                $fileSuratPath = 'surat_masuk/converted/' . uniqid() . '.pdf';
                Storage::disk('public')->put($fileSuratPath, $pdf->output());
            }
            // Case 3: Word → PDF
            elseif (in_array($ext, ['doc', 'docx'])) {
                \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
                \PhpOffice\PhpWord\Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));

                $phpWord = IOFactory::load($file->getPathname());
                $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');

                $fileSuratPath = 'surat_masuk/converted/' . uniqid() . '.pdf';
                $tempPdf = storage_path('app/public/' . $fileSuratPath);
                $pdfWriter->save($tempPdf);
            }
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
            'file_surat' => $fileSuratPath,
            'file_surat_original' => $originalPath,
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
        if ($surat->file_surat_original && Storage::disk('public')->exists($surat->file_surat_original)) {
            Storage::disk('public')->delete($surat->file_surat_original);
        }
        $surat->delete();
        return redirect()->route('surat_masuk.index')->with('success', 'Surat berhasil dihapus');
    }
}
