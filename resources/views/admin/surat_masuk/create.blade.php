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

<form action="{{ route('surat_masuk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>No Surat</label>
        <input type="text" name="no_surat" class="form-control" value="{{ old('no_surat') }}" required>
    </div>

    <div class="mb-3">
        <label>Asal Surat</label>
        <input type="text" name="asal_surat" class="form-control" value="{{ old('asal_surat') }}" required>
    </div>

    <div class="mb-3">
        <label>Tanggal Terima</label>
        <input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima') }}" required>
    </div>

    <div class="mb-3">
        <label>Perihal</label>
        <input type="text" name="perihal" class="form-control" value="{{ old('perihal') }}" required>
    </div>

    <div class="mb-3">
        <label>Keterangan (Lokasi Penyimpanan)</label>
        <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
    </div>

    <div class="mb-3">
        <label>Klasifikasi</label>
        <select name="klasifikasi" class="form-control" required>
            <option value="">-- Pilih --</option>
            <option value="Rahasia" {{ old('klasifikasi')=='Rahasia'?'selected':'' }}>Rahasia</option>
            <option value="Penting" {{ old('klasifikasi')=='Penting'?'selected':'' }}>Penting</option>
            <option value="Biasa" {{ old('klasifikasi')=='Biasa'?'selected':'' }}>Biasa</option>
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
    <div id="disposisi_fields" style="display: none;">
        <div class="mb-3">
            <label>Bagian Tujuan</label>
            <select name="dis_bagian" class="form-control">
                <option value="">-- Pilih Bagian --</option>
                <option value="Bagian 1">Bagian 1</option>
                <option value="Bagian 2">Bagian 2</option>
                <option value="Bagian 3">Bagian 3</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Instruksi</label>
            <input type="text" name="instruksi" class="form-control">
        </div>
    </div>

    {{-- Upload PDF --}}
    <div class="mb-3">
        <label>Upload Scan Surat (PDF)</label>
        <input type="file" name="file_surat" class="form-control" accept="application/pdf">
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
