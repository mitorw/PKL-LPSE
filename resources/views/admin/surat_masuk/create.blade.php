@extends('layouts.admin')

@section('content')
    {{-- Form Tambah Surat Masuk --}}
    <div class="card">
        <div class="card-header">
            <h4>Tambah Surat Masuk</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('surat_masuk.store') }}" method="POST" enctype="multipart/form-data" id="form-tambah-surat">
                @csrf

                <div class="mb-3">
                    <label>No Surat</label>
                    <input type="text" name="no_surat" class="form-control" value="{{ old('no_surat') }}" required>

                    @error('no_surat')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Asal Surat</label>
                    <input type="text" name="asal_surat" class="form-control" value="{{ old('asal_surat') }}" required>
                </div>

                <div class="mb-3" style="width: 200px">
                    <label>Tanggal Terima</label>
                    <input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima') }}"
                        required>
                </div>

                <div class="mb-3">
                    <label>Perihal</label>
                    <input type="text" name="perihal" class="form-control" value="{{ old('perihal') }}" required>
                </div>

                <div class="mb-3">
                    <label>Lokasi Penyimpanan</label>
                    <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Klasifikasi</label>
                    <select name="klasifikasi" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Biasa" {{ old('klasifikasi') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Penting" {{ old('klasifikasi') == 'Penting' ? 'selected' : '' }}>Penting</option>
                        <option value="Rahasia" {{ old('klasifikasi') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                </div>

                {{-- Pilihan Ada / Tidak Disposisi --}}
                <div class="mb-3">
                    <label>Disposisi</label>
                    {{-- Tambahkan atribut 'name' agar nilainya bisa dikirim dan diingat --}}
                    <select id="disposisi_status" name="disposisi_status" class="form-control">
                        <option value="tidak" {{ old('disposisi_status', 'tidak') == 'tidak' ? 'selected' : '' }}>Tidak
                            Ada</option>
                        <option value="ada" {{ old('disposisi_status') == 'ada' ? 'selected' : '' }}>Ada</option>
                    </select>
                </div>

                {{-- Form Disposisi Tambahan --}}
                {{-- Logika untuk menampilkan form jika 'old' adalah 'ada' --}}
                <div id="disposisi_fields" style="{{ old('disposisi_status') == 'ada' ? '' : 'display: none;' }}">
                    <div class="mb-3">
                        <label>Bagian Tujuan</label>
                        <select name="dis_bagian" class="form-control">
                            <option value="">-- Pilih Bagian --</option>
                            <option value="Bagian Layanan Pengadaan Secara Elektronik"
                                {{ old('dis_bagian') == 'Bagian Layanan Pengadaan Secara Elektronik' ? 'selected' : '' }}>
                                Bagian Layanan Pengadaan Secara Elektronik
                            </option>
                            <option value="Bagian Advokasi dan Pembinaan"
                                {{ old('dis_bagian') == 'Bagian Advokasi dan Pembinaan' ? 'selected' : '' }}>
                                Bagian Advokasi dan Pembinaan
                            </option>
                            <option value="Bagian Pengelolaan Pengadaan Barang dan Jasa"
                                {{ old('dis_bagian') == 'Bagian Pengelolaan Pengadaan Barang dan Jasa' ? 'selected' : '' }}>
                                Bagian Pengelolaan Pengadaan Barang dan Jasa
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Catatan</label>
                        {{-- Untuk textarea, old() diletakkan di antara tag --}}
                        <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>Instruksi</label>
                        {{-- Untuk input, old() diletakkan di dalam atribut value --}}
                        <input type="text" name="instruksi" class="form-control" value="{{ old('instruksi') }}">
                    </div>
                </div>

                {{-- Upload PDF --}}
                <div class="mb-3">
                    <label>Upload Scan Surat (PDF/PNG/JPG)</label>
                    <input type="file" name="file_surat" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                </div>

                <button class="btn btn-success" type="submit" id="tombol-simpan">Simpan</button>
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>

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

        {{-- Script Menyimpan --}}
        <script>
            document.getElementById('form-tambah-surat').addEventListener('submit', function() {
                const tombolSimpan = document.getElementById('tombol-simpan');
                tombolSimpan.disabled = true;
                tombolSimpan.innerText = 'Menyimpan...';
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const disposisiStatus = document.getElementById('disposisi_status');
                const disposisiFields = document.getElementById('disposisi_fields');

                disposisiStatus.addEventListener('change', function() {
                    if (this.value === 'ada') {
                        disposisiFields.style.display = 'block';
                    } else {
                        disposisiFields.style.display = 'none';
                    }
                });
            });
        </script>
    @endsection
