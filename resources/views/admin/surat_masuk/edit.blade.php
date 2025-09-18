@extends('layouts.admin')

@section('content')


<div class="card">
    <div class="card-header">
        <h4>Edit Surat Masuk</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('surat_masuk.update', $surat->id_surat_masuk) }}" method="POST" enctype="multipart/form-data" id="form-edit-surat">
            @csrf
            @method('PUT')

            {{-- Data Surat Utama --}}
            <div class="mb-3"><label class="form-label">No Surat</label><input type="text" name="no_surat" class="form-control" value="{{ old('no_surat', $surat->no_surat) }}">
                @error('no_surat')
                    <div class="mt-1 text-danger">{{ $message }}</div>
                @enderror
            </div>
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
                    <option value="tidak" {{ old('disposisi_status', $surat->disposisis->isEmpty() ? 'tidak' : 'ada') == 'tidak' ? 'selected' : '' }}>Tidak Ada</option>
                    <option value="ada" {{ old('disposisi_status', $surat->disposisis->isEmpty() ? 'tidak' : 'ada') == 'ada' ? 'selected' : '' }}>Ada</option>
                </select>
            </div>

            <div id="disposisi_fields">
                <div class="mb-3">
                    <label class="form-label">Bagian Tujuan</label>
                    <div class="form-check-list">
                        @foreach($disposisis as $disposisi)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disposisi_ids[]" value="{{ $disposisi->id_disposisi }}" id="disposisi_{{ $disposisi->id_disposisi }}" 
                                    {{ in_array($disposisi->id_disposisi, old('disposisi_ids', $surat->disposisis->pluck('id_disposisi')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="disposisi_{{ $disposisi->id_disposisi }}">
                                    {{ $disposisi->dis_bagian }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control">{{ old('catatan', $surat->disposisis->first() ? $surat->disposisis->first()->pivot->catatan : '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Instruksi</label>
                    <input type="text" name="instruksi" class="form-control" value="{{ old('instruksi', $surat->disposisis->first() ? $surat->disposisis->first()->pivot->instruksi : '') }}">
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">File Surat (PDF/PNG/JPG) [Max 5 MB]<span style="color: crimson">*Kosongkan jika tidak ingin diubah</span></label>
                @if($surat->file_surat)
                    <p class="mt-2">File sekarang: <a href="{{ asset('storage/'. $surat->file_surat) }}" target="_blank">Lihat File</a></p>
                @endif
                <input type="file" name="file_surat" class="form-control">
            </div>

            <button class="btn btn-success" type="submit" id="tombol-simpan">Simpan Perubahan</button>
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

<script>
    document.getElementById('form-edit-surat').addEventListener('submit', function() {
        const tombolSimpan = document.getElementById('tombol-simpan');
        tombolSimpan.disabled = true;
        tombolSimpan.innerText = 'Menyimpan...';
    });
</script>
@endpush
