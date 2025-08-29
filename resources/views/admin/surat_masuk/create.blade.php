@extends('layouts.admin')

@section('content')


    {{-- Form Tambah Surat Masuk --}}
    <div class="card">
        <div class="card-header">
            <h4>Tambah Surat Masuk</h4>
        </div>
        <div class="card-body">
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

                <div class="mb-3" style="width: 200px">
                    <label>Tanggal Terima</label>
                    <input type="date" name="tanggal_terima" class="form-control" value="{{ old('tanggal_terima') }}" required>
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
                            <option value="Bagian Layanan Pengadaan Secara Elektronik">Bagian Layanan Pengadaan Secara Elektronik
                            </option>
                            <option value="Bagian Advokasi dan Pembinaan">Bagian Advokasi dan Pembinaan</option>
                            <option value="Bagian Pengelolaan Pengadaan Barang dan Jasa">Bagian Pengelolaan Pengadaan Barang dan
                                Jasa</option>
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
                    <label>Upload Scan Surat (PDF/PNG/JPG/DOC)</label>
                    <input type="file" name="file_surat" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                </div>

                <button class="btn btn-success" type="submit">Simpan</button>
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
@endsection
