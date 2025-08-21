<!DOCTYPE html>
<html>

<head>
    <title>Laporan Inventaris Surat</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 2cm 2.5cm;
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

    {{-- SURAT MASUK --}}
{{-- SURAT MASUK --}}
<div class="subjudul">A. Surat Masuk</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Asal</th>
            <th>Tanggal</th>
            <th>Perihal</th>
            <th>Klasifikasi</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($dataSurat->where('jenis_surat', 'masuk') as $surat)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ $surat->asal ?? '-' }}</td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') }}
                </td>
                <td>{{ $surat->perihal }}</td>
                <td style="text-align:center;">{{ ucfirst($surat->status) }}</td>
                <td>{{ $surat->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data surat masuk.</td>
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
            <th>Dibuat Oleh</th>
            <th>Klasifikasi</th>
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
                <td>{{ $surat->dibuat_oleh ?? '-' }}</td>
                <td style="text-align:center;">{{ ucfirst($surat->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data surat keluar.</td>
            </tr>
        @endforelse
    </tbody>
</table>


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
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($disposisiSurat as $sm)
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
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- TANDA TANGAN --}}
    <div class="ttd" style="text-align: right; margin-top: 50px;">
    <div class="jabatan">
        Bandar Lampung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }} <br>
        Kepala Biro Pengadaan Barang dan Jasa
    </div>

    <div style="margin-top: 80px;"></div>

    <div class="nama">
        <u>PUADI JAILANI, SH, MH.</u><br>
        NIP. 19650905 199103 1 004
    </div>
</div>

</body>

</html>