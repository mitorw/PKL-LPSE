{{-- resources/views/admin/Surat/laporan-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventarisasi Surat</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin: 3cm 2.5cm; }
        .header-section { text-align: center; margin-bottom: 30px; }
        .header-section h3, .header-section h4 { margin: 0; line-height: 1.2; }
        .line { border-bottom: 2px solid black; margin: 15px 0; }
        .report-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; }
        
        /* Gaya untuk tabel laporan */
        .report-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .report-table th, .report-table td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 10pt; }
        .report-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    {{-- BAGIAN HEADER SURAT DARI FOTO --}}
    <div class="header-section">
        <h3>YAYASAN PERGURUAN 17 AGUSTUS 1945 SURABAYA</h3>
        <h4>UNIVERSITAS 17 AGUSTUS 1945 (UNTAG) SURABAYA</h4>
        <p style="font-size: 10pt;">Jl. Semolowaru No. 45 Surabaya 60118 Telp. +62 31 5931800 (hunting) Fax. +62 31 5927817</p>
    </div>
    <div class="line"></div>
    
    {{-- JUDUL LAPORAN --}}
    <h1 class="report-title">Laporan Inventarisasi Surat</h1>
    
    {{-- TABEL LAPORAN --}}
    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Surat</th>
                <th>Tanggal</th>
                <th>Jenis Surat</th>
                <th>Perihal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataSurat as $surat)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d-m-Y') }}</td>
                <td>{{ $surat->jenis_surat }}</td>
                <td>{{ $surat->perihal }}</td>
                <td>{{ $surat->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data surat yang ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>