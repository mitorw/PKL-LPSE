@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Edit Surat Keluar</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('surat_keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data"
                id="form-edit-surat">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Nomor Surat</label>
                    <input type="text" name="nomor_surat" class="form-control"
                        value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                    @error('nomor_surat')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Perihal</label>
                    <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label>Tujuan</label>
                    <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan', $surat->tujuan) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $surat->tanggal) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label>Dibuat Oleh</label>
                    <input type="text" name="dibuat_oleh" class="form-control"
                        value="{{ old('dibuat_oleh', $surat->dibuat_oleh) }}" required>
                </div>

                <div class="mb-3">
                    <label>Lokasi Penyimpanan</label>
                    <textarea name="keterangan" class="form-control">{{ old('keterangan', $surat->keterangan) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Klasifikasi</label>
                    <select name="klasifikasi" class="form-control" required>
                        <option value="biasa">Biasa</option>
                        <option value="penting">Penting</option>
                        <option value="rahasia">Rahasia</option>
                    </select>
                </div>
                <!-- File Surat -->
                <div class="mb-3">
                    <label class="form-label">File Surat (PDF/PNG/JPG) <span style="color: crimson">*Kosongkan jika tidak
                            ingin diubah</span></label>

                    @if ($surat->isi_surat)
                        <p>File sekarang: <a href="{{ asset('storage/' . $surat->isi_surat) }}" target="_blank">Lihat
                                File</a></p>
                    @endif
                    <input type="file" name="isi_surat" class="form-control">
                </div>

                <button class="btn btn-success" type="submit" id="tombol-simpan">Simpan Perubahan</button>
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-secondary">Batal</a>

            </form>
        </div>

        {{-- Script Menyimpan --}}
        <script>
            document.getElementById('form-edit-surat').addEventListener('submit', function() {
                const tombolSimpan = document.getElementById('tombol-simpan');
                tombolSimpan.disabled = true;
                tombolSimpan.innerText = 'Menyimpan...';
            });
        </script>
    @endsection
