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

    <!-- No Surat -->
    <div class="mb-3">
        <label>No Surat</label>
        <input type="text" name="no_surat" class="form-control" value="{{ old('no_surat', $surat->no_surat) }}">
    </div>

    <!-- Asal Surat -->
    <div class="mb-3">
        <label>Asal Surat</label>
        <input type="text" name="asal_surat" class="form-control" value="{{ old('asal_surat', $surat->asal_surat) }}">
    </div>

    <!-- Tanggal Terima -->
    <div class="mb-3">
        <label>Tanggal Terima</label>
        <input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima', $surat->tanggal_terima) }}">
    </div>

    <!-- Perihal -->
    <div class="mb-3">
        <label>Perihal</label>
        <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}">
    </div>

    <!-- Keterangan -->
    <div class="mb-3">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $surat->keterangan) }}">
    </div>



    <!-- Klasifikasi -->
    <div class="mb-3">
        <label>Klasifikasi</label>
        <select name="klasifikasi" class="form-control">
            <option value="Rahasia" {{ $surat->klasifikasi == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
            <option value="Penting" {{ $surat->klasifikasi == 'Penting' ? 'selected' : '' }}>Penting</option>
            <option value="Biasa" {{ $surat->klasifikasi == 'Biasa' ? 'selected' : '' }}>Biasa</option>
        </select>
    </div>

    {{-- Pilihan Ada / Tidak Disposisi --}}
    <div class="mb-3">
        <label>Disposisi</label>
        <select id="disposisi_status" class="form-control">
            <option value="tidak">Tidak Ada</option>
            <option value="ada">Ada</option>
        </select>
    </div>

    {{-- Form Disposisi Tambahan --}}
    <!-- Disposisi - Bagian Tujuan -->
    <div class="mb-3">
        <label>Bagian Tujuan</label>
        <select name="dis_bagian" class="form-control">
            <option value="">-- Tidak Ada Disposisi --</option>
            <option value="Bagian 1" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian 1' ? 'selected' : '' }}>Bagian 1</option>
            <option value="Bagian 2" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian 2' ? 'selected' : '' }}>Bagian 2</option>
            <option value="Bagian 3" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian 3' ? 'selected' : '' }}>Bagian 3</option>
        </select>
    </div>

    <!-- Disposisi - Catatan -->
    <div class="mb-3">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control">{{ old('catatan', $surat->disposisi->catatan ?? '') }}</textarea>
    </div>

    <!-- Disposisi - Instruksi -->
    <div class="mb-3">
        <label>Instruksi</label>
        <textarea name="instruksi" class="form-control">{{ old('instruksi', $surat->disposisi->instruksi ?? '') }}</textarea>
    </div>


    <!-- File Surat -->
    <div class="mb-3">
        <label>File Surat (PDF)</label>
        @if($surat->file_surat)
            <p>File sekarang: <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank">Lihat PDF</a></p>
        @endif
        <input type="file" name="file_surat" class="form-control">
    </div>

    <button class="btn btn-success" type="submit">Simpan</button>
</form>

<script>
document.getElementById('disposisi_status').addEventListener('change', function() {
    let fields = document.getElementById('disposisi_fields');
    if (this.value === 'ada') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        // Kosongkan input jika tidak ada disposisi
        fields.querySelectorAll('input, textarea, select').forEach(el => el.value = '');
    }
});
</script>

@endsection
