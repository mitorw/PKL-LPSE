@extends('layouts.admin')

@section('content')

{{-- Tampilkan Error Validasi --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('surat_masuk.update', $surat->id_surat_masuk) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ... (semua input dari No Surat sampai Klasifikasi tetap sama) ... --}}
    <div class="mb-3">
        <label>No Surat</label>
        <input type="text" name="no_surat" class="form-control" value="{{ old('no_surat', $surat->no_surat) }}">
    </div>

    <div class="mb-3">
        <label>Asal Surat</label>
        <input type="text" name="asal_surat" class="form-control" value="{{ old('asal_surat', $surat->asal_surat) }}">
    </div>

    <div class="mb-3" style="width: 200px">
        <label>Tanggal Terima</label>
        <input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima', $surat->tanggal_terima) }}">
    </div>

    <div class="mb-3">
        <label>Perihal</label>
        <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}">
    </div>

    <div class="mb-3">
        <label>Keterangan (Lokasi Penyimpanan)</label>
        <textarea name="keterangan" class="form-control">{{ old('keterangan', $surat->keterangan) }}</textarea>
    </div>

    <div class="mb-3">
        <label>Klasifikasi</label>
        <select name="klasifikasi" class="form-control">
            <option value="Rahasia" {{ old('klasifikasi', $surat->klasifikasi) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
            <option value="Penting" {{ old('klasifikasi', $surat->klasifikasi) == 'Penting' ? 'selected' : '' }}>Penting</option>
            <option value="Biasa" {{ old('klasifikasi', $surat->klasifikasi) == 'Biasa' ? 'selected' : '' }}>Biasa</option>
        </select>
    </div>

    <hr>
    <h5>Disposisi</h5>

    {{-- Pilihan Ada / Tidak Disposisi --}}
    <div class="mb-3">
        <label>Status Disposisi</label>
        {{-- TAMBAHKAN 'name' DAN LOGIKA UNTUK MENENTUKAN PILIHAN AWAL --}}
        <select id="disposisi_status" name="disposisi_status" class="form-control">
            <option value="tidak" {{ old('dis_bagian', $surat->id_disposisi) ? '' : 'selected' }}>Tidak Ada</option>
            <option value="ada" {{ old('dis_bagian', $surat->id_disposisi) ? 'selected' : '' }}>Ada</option>
        </select>
    </div>

    {{-- Form Disposisi Tambahan (Dibungkus dalam satu div) --}}
    <div id="disposisi_fields">
        <div class="mb-3">
            <label>Bagian Tujuan</label>
            <select name="dis_bagian" class="form-control">
                <option value="">-- Pilih Bagian --</option>
                <option value="Bagian Layanan Pengadaan Secara Elektronik" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Layanan Pengadaan Secara Elektronik' ? 'selected' : '' }}>Bagian Layanan Pengadaan Secara Elektronik</option>
                <option value="Bagian Advokasi dan Pembinaan" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Advokasi dan Pembinaan' ? 'selected' : '' }}>Bagian Advokasi dan Pembinaan</option>
                <option value="Bagian Pengelolaan Pengadaan Barang dan Jasa" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Pengelolaan Pengadaan Barang dan Jasa' ? 'selected' : '' }}>Bagian Pengelolaan Pengadaan Barang dan Jasa</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control">{{ old('catatan', $surat->disposisi->catatan ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Instruksi</label>
            <input type="text" name="instruksi" class="form-control" value="{{ old('instruksi', $surat->disposisi->instruksi ?? '') }}">
        </div>
    </div>


    {{-- ... Input File Surat dan Tombol Simpan ... --}}
    <hr>
    <div class="mb-3">
        <label>Upload File Surat Baru (Opsional)</label>
        @if($surat->file_surat)
            <p>File sekarang: <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank" class="text-primary">Lihat File</a></p>
        @endif
        <input type="file" name="file_surat" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file.</small>
    </div>

    <button class="btn btn-success" type="submit">Simpan Perubahan</button>
    <a href="{{ route('surat_masuk.index') }}" class="btn btn-secondary">Batal</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('disposisi_status');
    const fieldsDiv = document.getElementById('disposisi_fields');

    function toggleDisposisiFields() {
        if (statusSelect.value === 'ada') {
            fieldsDiv.style.display = 'block';
        } else {
            fieldsDiv.style.display = 'none';
        }
    }

    // Panggil fungsi saat halaman pertama kali dimuat
    toggleDisposisiFields();

    // Tambahkan event listener untuk perubahan
    statusSelect.addEventListener('change', toggleDisposisiFields);
});
</script>

@endsection
