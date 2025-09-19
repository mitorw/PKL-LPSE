@extends('layouts.admin')

@section('content')
    {{-- Form Tambah Surat Keluar --}}
    <div class="card">
        <div class="card-header">
            <h4>Tambah Surat Keluar</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('surat_keluar.store') }}" method="POST" enctype="multipart/form-data"
                id="form-tambah-surat">
                @csrf
                <div class="mb-3">
                    <label>Nomor Surat</label>
                    <input type="text" name="nomor_surat" class="form-control" value="{{ old('nomor_surat') }}" required>

                    @error('no_surat')
                        <div class="mt-1 text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Perihal</label>
                    <input type="text" name="perihal" class="form-control" value="{{ old('perihal') }}" required>
                </div>

                <div class="mb-3">
                    <label>Tujuan</label>
                    <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan') }}" required>
                </div>

                <div class="mb-3" style="width: 200px">
                    <label>Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal') }}"
                        placeholder="Pilih Tanggal.." required>
                </div>

                <div class="mb-3">
                    <label>Dibuat Oleh</label>
                    <input type="text" name="dibuat_oleh" class="form-control" value="{{ old('dibuat_oleh') }}" required>
                </div>

                <div class="mb-3">
                    <label>Lokasi Penyimpanan</label>
                    <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Klasifikasi</label>
                    <select name="klasifikasi" class="form-control" required>
                        <option value="biasa" {{ old('klasifikasi') == 'biasa' ? ' selected' : '' }}> Biasa</option>
                        <option value="penting" {{ old('klasifikasi') == 'penting' ? ' selected' : '' }}>Penting</option>
                        <option value="rahasia" {{ old('klasifikasi') == 'rahasia' ? ' selected' : '' }}>Rahasia</option>
                        <option value="segera" {{ old('klasifikasi') == 'segera' ? ' selected' : '' }}>Segera</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>File Surat (PDF/PNG/JPG) [Max 5 MB]</label>
                    <input type="file" name="isi_surat" class="form-control" accept=".pdf,.png,.jpeg,.jpg" required>
                </div>

                <button type="submit" class="btn btn-primary" id="tombol-simpan">Simpan</button>
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>

        {{-- Script Menyimpan --}}
        <script>
            document.getElementById('form-tambah-surat').addEventListener('submit', function() {
                const tombolSimpan = document.getElementById('tombol-simpan');
                tombolSimpan.disabled = true;
                tombolSimpan.innerText = 'Menyimpan...';
            });
        </script>
    @endsection
