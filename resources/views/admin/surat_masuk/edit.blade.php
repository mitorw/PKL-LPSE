@extends('layouts.admin')

@section('content')


<div class="card">
    <div class="card-header">
        <h4>Edit Surat Masuk</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('surat_masuk.update', $surat->id_surat_masuk) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Data Surat Utama --}}
            <div class="mb-3"><label class="form-label">No Surat</label><input type="text" name="no_surat" class="form-control" value="{{ old('no_surat', $surat->no_surat) }}"></div>
            <div class="mb-3"><label class="form-label">Asal Surat</label><input type="text" name="asal_surat" class="form-control" value="{{ old('asal_surat', $surat->asal_surat) }}"></div>
            <div class="mb-3" style="width: 250px;"><label class="form-label">Tanggal Terima</label><input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima', $surat->tanggal_terima) }}"></div>
            <div class="mb-3"><label class="form-label">Perihal</label><input type="text" name="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}"></div>
            <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control">{{ old('keterangan', $surat->keterangan) }}</textarea></div>
            <div class="mb-3"><label class="form-label">Klasifikasi</label><select name="klasifikasi" class="form-select"><option value="Rahasia" {{ old('klasifikasi', $surat->klasifikasi) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option><option value="Penting" {{ old('klasifikasi', $surat->klasifikasi) == 'Penting' ? 'selected' : '' }}>Penting</option><option value="Biasa" {{ old('klasifikasi', $surat->klasifikasi) == 'Biasa' ? 'selected' : '' }}>Biasa</option></select></div>

            <hr>
            <h5>Disposisi</h5>

            {{-- KODE DENGAN LOGIKA YANG SUDAH DIPERBAIKI --}}
            <div class="mb-3">
                <label class="form-label">Status Disposisi</label>
                <select id="disposisi_status" name="disposisi_status" class="form-select">
                    <option value="tidak" {{ old('disposisi_status', $surat->id_disposisi ? 'ada' : 'tidak') == 'tidak' ? 'selected' : '' }}>Tidak Ada</option>
                    <option value="ada" {{ old('disposisi_status', $surat->id_disposisi ? 'ada' : 'tidak') == 'ada' ? 'selected' : '' }}>Ada</option>
                </select>
            </div>

            <div id="disposisi_fields">
                <div class="mb-3">
                    <label class="form-label">Bagian Tujuan</label>
                    <select name="dis_bagian" class="form-select">
                        <option value="">-- Pilih Bagian --</option>
                        <option value="Bagian Layanan Pengadaan Secara Elektronik" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Layanan Pengadaan Secara Elektronik' ? 'selected' : '' }}>Bagian Layanan Pengadaan Secara Elektronik</option>
                        <option value="Bagian Advokasi dan Pembinaan" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Advokasi dan Pembinaan' ? 'selected' : '' }}>Bagian Advokasi dan Pembinaan</option>
                        <option value="Bagian Pengelolaan Pengadaan Barang dan Jasa" {{ old('dis_bagian', $surat->disposisi->dis_bagian ?? '') == 'Bagian Pengelolaan Pengadaan Barang dan Jasa' ? 'selected' : '' }}>Bagian Pengelolaan Pengadaan Barang dan Jasa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control">{{ old('catatan', $surat->disposisi->catatan ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Instruksi</label>
                    <input type="text" name="instruksi" class="form-control" value="{{ old('instruksi', $surat->disposisi->instruksi ?? '') }}">
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">File Surat (PDF/PNG/JPG/DOC) <span style="color: crimson">*Kosongkan jika tidak ingin diubah</span></label>
                @if($surat->file_surat)
                    <p class="mt-2">File sekarang: <a href="{{ asset('storage/'. $surat->file_surat) }}" target="_blank">Lihat File</a></p>
                @endif
                <input type="file" name="file_surat" class="form-control">
            </div>

            <button class="btn btn-success" type="submit">Simpan Perubahan</button>
            <a href="{{ route('surat_masuk.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('disposisi_status');
    const fieldsDiv = document.getElementById('disposisi_fields');

    function toggleDisposisiFields() {
        fieldsDiv.style.display = (statusSelect.value === 'ada') ? 'block' : 'none';
    }

    toggleDisposisiFields();
    statusSelect.addEventListener('change', toggleDisposisiFields);
});
</script>
@endpush
