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
                        <div class="mt-1 text-danger">{{ $message }}</div>
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
                        <option value="Segera" {{ old('klasifikasi') == 'Segera' ? 'selected' : '' }}>Segera</option>
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
                        <div class="form-check-list" style="margin-top: 10px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                            @foreach($disposisis as $disposisi)
                                <div class="form-check" style="margin-bottom: 10px; padding-left: 25px;">
                                    <input class="form-check-input" type="checkbox" name="disposisi_ids[]" value="{{ $disposisi->id_disposisi }}" id="disposisi_{{ $disposisi->id_disposisi }}" 
                                        {{ in_array($disposisi->id_disposisi, old('disposisi_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disposisi_{{ $disposisi->id_disposisi }}" style="font-weight: 500;">
                                        {{ $disposisi->dis_bagian }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>Instruksi</label>
                        <input type="text" name="instruksi" class="form-control" value="{{ old('instruksi') }}">
                    </div>
                </div>

                {{-- Upload PDF --}}
                <div class="mb-3">
                    <label>Upload Scan Surat (PDF/PNG/JPG) [Max 5 MB]</label>
                    <input type="file" name="file_surat" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                </div>

                <button class="btn btn-success" type="submit" id="tombol-simpan">Simpan</button>
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-secondary">Batal</a>
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const disposisiStatus = document.getElementById('disposisi_status');
                const disposisiFields = document.getElementById('disposisi_fields');

                // Fungsi untuk menampilkan/menyembunyikan form disposisi
                function toggleDisposisiFields() {
                    if (disposisiStatus.value === 'ada') {
                        disposisiFields.style.display = 'block';
                    } else {
                        disposisiFields.style.display = 'none';
                        // Kosongkan input jika tidak ada disposisi
                        disposisiFields.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
                        // Hapus centang pada checkbox
                        disposisiFields.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
                    }
                }

                // Tambahkan event listener
                disposisiStatus.addEventListener('change', toggleDisposisiFields);
                
                // Jalankan fungsi saat halaman dimuat untuk mengatur tampilan awal
                toggleDisposisiFields();
            });
        </script>
    @endsection
