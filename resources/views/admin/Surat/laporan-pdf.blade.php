<!DOCTYPE html>
<html>

<head>
    <title>
        Laporan Inventaris
            @if ($tglAwal == $tglAkhir)
                - {{ \Carbon\Carbon::parse($tglAwal)->locale('id')->translatedFormat('d F Y') }}
            @else
                - {{ \Carbon\Carbon::parse($tglAwal)->locale('id')->translatedFormat('d F Y') }}
                  s/d {{ \Carbon\Carbon::parse($tglAkhir)->locale('id')->translatedFormat('d F Y') }}
            @endif
    </title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 0.5cm 1cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11pt;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .kop-surat {
            margin-bottom: 20px;
        }

        .line {
            border-bottom: 3px solid #000;
            margin-top: 5px;
        }

        .judul {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            font-size: 14pt;
        }

        .subjudul {
            margin-top: 25px;
            font-weight: bold;
            font-size: 12pt;
        }

        .ttd {
            margin-top: 60px;
            width: 100%;
            text-align: right;
        }
    </style>
</head>
<body>

<body>

    {{-- HEADER INSTANSI --}}
    <div class="kop-surat">
    <table width="100%" style="border: none; border-collapse: collapse;">
        <tr>
            <td width="15%" style="text-align:center; border:none;">
                <img src="{{ public_path('storage/assets/logo-lampung.png') }}" alt="Logo"
                    style="width:90px; height:auto;">
            </td>
            <td width="85%" style="text-align:center; border:none;">
                <h2 style="margin:0; font-size:16pt;">PEMERINTAH PROVINSI LAMPUNG</h2>
                <h3 style="margin:0; font-size:14pt;">SEKRETARIAT DAERAH</h3>
                <div style="font-size:11pt; margin-top:2px;">
                    Jalan R.W. Monginsidi No. 69 Teluk Betung Bandar Lampung, Kode Pos 52211 <br>
                    Telp. (0721) 483465, Fax (0721) 481166, Email: info@lampungprov.go.id <br>
                    Website: http://lampungprov.go.id
                </div>
            </td>
        </tr>
    </table>

    {{-- Garis pemisah bawah --}}
    <div class="line" style="border-top: 2px solid #000; margin-top:5px; margin-bottom:15px;"></div>
</div>


    {{-- JUDUL LAPORAN --}}
    <div class="judul">
        LAPORAN INVENTARISASI SURAT <br>
        BIRO PENGADAAN BARANG DAN JASA
    </div>
    {{-- PERIODE CETAK --}}
<div style="text-align: center; margin-top: 10px; font-weight: bold; font-size: 12pt;">
    @if ($tglAwal == $tglAkhir)
        PERIODE: {{ \Carbon\Carbon::parse($tglAwal)->locale('id')->translatedFormat('d F Y') }}
    @else
        PERIODE: {{ \Carbon\Carbon::parse($tglAwal)->locale('id')->translatedFormat('d F Y') }}
        &ndash; {{ \Carbon\Carbon::parse($tglAkhir)->locale('id')->translatedFormat('d F Y') }}
    @endif
</div>



{{-- SURAT MASUK --}}
<div class="subjudul">A. Surat Masuk</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Perihal</th>
            <th>Tanggal</th>
            <th>Asal</th>
            <th>Klasifikasi</th>
            <th>Lokasi Penyimpanan</th>
            <th>Status Disposisi</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($dataSurat->where('jenis_surat', 'masuk') as $surat)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ $surat->perihal }}</td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') }}
                </td>
                <td>{{ $surat->asal ?? '-' }}</td>
                <td style="text-align:center;">{{ ucfirst($surat->status) }}</td>
                <td>{{ $surat->keterangan ?? '-' }}</td>
                @php
                    $sm = $disposisiSurat->firstWhere('no_surat', $surat->nomor_surat);
                @endphp
                @if($sm && $sm->disposisi)
                    <td style="text-align:center;">Ada</td>
                @else
                    <td style="text-align:center;">Tidak Ada</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada data surat masuk.</td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- SURAT KELUAR --}}
<div class="subjudul">B. Surat Keluar</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Perihal</th>
            <th>Tanggal</th>
            <th>Tujuan</th>
            <th>Klasifikasi</th>
            <th>Lokasi Penyimpanan</th>
            <th>Dibuat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($dataSurat->where('jenis_surat', 'keluar') as $surat)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ $surat->perihal }}</td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') }}
                </td>
                <td>{{ $surat->tujuan ?? '-' }}</td>
                <td style="text-align:center;">{{ ucfirst($surat->status) }}</td>
                <td>{{ $surat->keterangan ?? '-' }}</td>
                <td>{{ $surat->dibuat_oleh ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada data surat keluar.</td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- LAPORAN DISPOSISI --}}
<h3>C. Laporan Disposisi</h3>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Tanggal</th>
            <th>Status Disposisi</th>
            <th>Tujuan/Bagian</th>
            <th>Catatan</th>
            <th>Instruksi</th>
            <th>Lokasi Penyimpanan</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @forelse($disposisiSurat as $sm)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ $sm->no_surat }}</td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($sm->tanggal_terima)->format('d-m-Y') }}
                </td>

                @if($sm->disposisi)
                    <td style="text-align:center;">Ada</td>
                    <td>{{ $sm->disposisi->dis_bagian }}</td>
                    <td>{{ $sm->disposisi->catatan }}</td>
                    <td>{{ $sm->disposisi->instruksi }}</td>
                @else
                    <td style="text-align:center;">Tidak Ada</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                @endif
                <td>{{ $sm->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada data disposisi.</td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- PIE CHART SURAT --}}
<div class="subjudul">D.Grafik Inventaris Surat</div>
<div style="text-align:center; margin-top:20px;">
    <img src="data:image/png;base64,{{ $chartBase64 }}" alt="Pie Chart"
         style="width:300px; height:auto;">
    <p style="font-size:11pt; margin-top:5px;">
        Total Surat Masuk: {{ $dataSurat->where('jenis_surat','masuk')->count() }} |
        Total Surat Keluar: {{ $dataSurat->where('jenis_surat','keluar')->count() }}
    </p>
</div>




    {{-- TANDA TANGAN --}}
    <div class="ttd" style="text-align: right; margin-top: 50px;">
    <div class="jabatan">
        Bandar Lampung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }} <br>
        Kepala Biro Pengadaan Barang dan Jasa
 <div class="nama">
    <br>
    <br>
    <br>
    <br>
    <u style="font-weight: bold;">PUADI JAILANI, SH, MH.</u><br>
        NIP. 19650905 199103 1 004
    </div>
</div>

</body>

</html>
