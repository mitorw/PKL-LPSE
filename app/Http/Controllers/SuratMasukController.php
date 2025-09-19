<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;   // kalau pakai GD
use Illuminate\Validation\Rule;


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
        $query = SuratMasuk::with('disposisis');

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
                $q->whereHas('disposisis'); // Hanya tampilkan surat yang punya relasi disposisi
            } elseif ($request->disposisi_status === 'tidak_ada') {
                $q->whereDoesntHave('disposisis'); // Hanya tampilkan surat yang TIDAK punya relasi disposisi
            }
        });

        // BARU: Terapkan filter berdasarkan BAGIAN disposisi jika ada
        $query->when($request->filled('dis_bagian'), function ($q) use ($request) {
            $q->whereHas('disposisis', function ($disposisiQuery) use ($request) {
                $disposisiQuery->where('dis_bagian', $request->dis_bagian);
            });
        });

        // Filter status disposisi sudah ditangani di atas dengan relasi many-to-many

        // Tentukan kolom dan arah SORTING (urutan)
        $sortColumn = $request->input('sort', 'created_at'); // Default sorting
        $sortDirection = $request->input('direction', 'desc');   // Default direction

        // Terapkan sorting ke query
        $query->orderBy($sortColumn, $sortDirection);

        // Eksekusi query dengan PAGINATION di akhir
        // appends($request->query()) penting agar filter tetap aktif saat pindah halaman
        $perPage = $request->get('per_page', 10); // Ambil nilai per_page dari request, default 10
        $data = $query->paginate($perPage)->appends($request->query());


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
        $disposisis = Disposisi::all();
        return view('admin.surat_masuk.create', [
            'pageTitle' => 'Tambah Surat Masuk',
            'disposisis' => $disposisis
        ]);
    }

    public function store(Request $request)
    {
        // Validasi semua field kecuali no_surat terlebih dahulu
        $request->validate([
            'asal_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa,Segera',
            'file_surat' => 'required|mimes:pdf,png,jpg,jpeg|max:5120',
            'file_surat_original' => 'nullable|string|max:255',
            // ... validasi lain tanpa no_surat
        ]);

        // Cek no_surat secara manual
        $noSuratInput = $request->input('no_surat');
        $existingSurat = SuratMasuk::where('no_surat', $noSuratInput)->first();

        if ($existingSurat) {
            // JIKA DITEMUKAN DUPLIKAT

            // MODIFIKASI UTAMA: Buat URL yang mengisi pencarian DAN memberi penanda highlight
            $redirectUrl = route('surat_masuk.index', [
                'search' => $existingSurat->no_surat,       // Parameter untuk mengisi kolom search
                'highlight' => $existingSurat->id_surat_masuk // Parameter untuk highlight & scroll
            ]);

            return redirect()->back()
                ->withInput()
                ->with('duplicate_found', [
                    'no_surat' => $existingSurat->no_surat,
                    'redirect_url' => $redirectUrl // Kirim URL yang sudah cerdas ini
                ]);
        }

        // Tidak perlu membuat disposisi baru, karena kita menggunakan disposisi yang sudah ada

        $fileSuratPath = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');

            $ext = strtolower($file->getClientOriginalExtension());

            // ambil data dari request
            $noSurat = safeFileName($request->no_surat);
            $tanggalSurat = \Carbon\Carbon::parse($request->tanggal_terima)->format('d-m-Y');

            // bikin nama dasar file
            $baseFileName = $noSurat . '_' . $tanggalSurat . '.pdf';
            $baseOriginal  = $noSurat . '_' . $tanggalSurat . '.' . $ext;

            if ($ext === 'pdf') {
                // simpan original pakai nama khusus
                $originalPath = $file->storeAs('surat_masuk/original', $baseOriginal, 'public');
                $fileSuratPath = $originalPath;
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $html = '';
                $manager = new ImageManager(new Driver());

                $originalPath = $file->storeAs('surat_masuk/original', $baseOriginal, 'public');
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
                $filename = 'surat_masuk/converted/' . $baseFileName;
                Storage::disk('public')->put($filename, $pdf->output());
                $fileSuratPath = $filename;
            }
        }

        $suratMasuk = SuratMasuk::create([
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'user_id' => Auth::id(),
            'file_surat' => $fileSuratPath,
            'file_surat_original' => $originalPath ?? null,
        ]);

        // Simpan multiple disposisi jika ada
        if ($request->disposisi_status === 'ada' && $request->has('disposisi_ids') && !empty($request->disposisi_ids)) {
            $disposisiData = [];
            foreach ($request->disposisi_ids as $disposisiId) {
                $disposisiData[$disposisiId] = [
                    'catatan' => $request->catatan,
                    'instruksi' => $request->instruksi
                ];
            }
            $suratMasuk->disposisis()->attach($disposisiData);
        }

        return redirect()->route('surat_masuk.index')->with('success', 'Surat masuk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $surat = SuratMasuk::with('disposisis')->findOrFail($id);
        $disposisis = Disposisi::all();
        return view('admin.surat_masuk.edit', [
            'pageTitle' => 'Edit Surat Masuk',
            'surat' => $surat,
            'disposisis' => $disposisis
        ]);
    }

    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);   // converted (PDF)

        $request->validate([
            'asal_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string',
            'klasifikasi' => 'required|in:Rahasia,Penting,Biasa,Segera',
            'file_surat' => 'nullable|mimes:pdf,png,jpg,jpeg|max:5120',
            'file_surat_original' => 'nullable|string|max:255',
        ]);

        $noSuratInput = $request->input('no_surat');
        // Cari surat lain yang memiliki no_surat yang sama, TAPI bukan surat yang sedang kita edit
        $existingSurat = SuratMasuk::where('no_surat', $noSuratInput)
            ->where('id_surat_masuk', '!=', $id)
            ->first();

        if ($existingSurat) {
            // JIKA DITEMUKAN DUPLIKAT
            $redirectUrl = route('surat_masuk.index', [
                'search' => $existingSurat->no_surat,
                'highlight' => $existingSurat->id_surat_masuk
            ]);

            return redirect()->back()
                ->withInput()
                ->with('duplicate_found', [
                    'no_surat' => $existingSurat->no_surat,
                    'redirect_url' => $redirectUrl
                ]);
        }
        // --- AWAL BLOK PENGGANTI ---

        // Cek nilai dari dropdown 'Status Disposisi'
        if ($request->input('disposisi_status') === 'tidak') {

            // JIKA status adalah "Tidak", hapus semua relasi. Logika selesai di sini.
            $surat->disposisis()->sync([]);
        } else {

            // JIKA status adalah "Ada", siapkan data untuk sinkronisasi.
            $disposisiData = []; // Inisialisasi variabel di dalam blok ini

            if ($request->has('disposisi_ids')) {
                foreach ($request->disposisi_ids as $disposisiId) {
                    $disposisiData[$disposisiId] = [
                        'catatan' => $request->catatan,
                        'instruksi' => $request->instruksi
                    ];
                }
            }

            // Panggil sync di dalam blok 'else' ini juga.
            $surat->disposisis()->sync($disposisiData);
        }

        // --- AKHIR BLOK PENGGANTI ---

        $fileSuratPath = $surat->file_surat;
        $originalPath = $surat->file_surat_original; // original



        if ($request->hasFile('file_surat')) {
            // Hapus file lama jika ada
            if ($fileSuratPath && Storage::disk('public')->exists($fileSuratPath)) {
                Storage::disk('public')->delete($fileSuratPath);
            }
            if ($originalPath && Storage::disk('public')->exists($originalPath)) {
                Storage::disk('public')->delete($originalPath);
            }

            $file = $request->file('file_surat');
            $ext = strtolower($file->getClientOriginalExtension());

            // Ambil data dari request
            $noSurat       = safeFileName($request->no_surat);
            $tanggalSurat  = \Carbon\Carbon::parse($request->tanggal_terima)->format('d-m-Y');

            // Nama file dasar
            $baseFileName  = $noSurat . '_' . $tanggalSurat . '.pdf';
            $baseOriginal  = $noSurat . '_' . $tanggalSurat . '.' . $ext;

            // Simpan file original
            $originalPath = $file->storeAs('surat_masuk/original', $baseOriginal, 'public');

            // Case 1: PDF asli
            if ($ext === 'pdf') {
                $fileSuratPath = $originalPath;
            }

            // Case 2: Gambar → PDF
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
                $fileSuratPath = 'surat_masuk/converted/' . $baseFileName;
                Storage::disk('public')->put($fileSuratPath, $pdf->output());
            }

            // Simpan ke database
            $surat->file_surat = $fileSuratPath;
            $surat->file_surat_original = $originalPath;
        }


        // Update data surat masuk
        $surat->update([
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'klasifikasi' => $request->klasifikasi,
            'file_surat' => $fileSuratPath,
            'file_surat_original' => $originalPath,
        ]);

        return redirect()->route('surat_masuk.index')->with('success', 'Surat masuk berhasil diperbarui');
    }

    public function show($id)
    {
        $surat = SuratMasuk::with('disposisis')->findOrFail($id);
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
