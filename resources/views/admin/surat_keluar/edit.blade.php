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

    <form action="{{ route('surat_keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nomor Surat</label>
            <input type="text" name="nomor_surat" class="form-control" value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                required>
        </div>

        <div class="mb-3">
            <label>Perihal</label>
            <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}"
                required>
        </div>

        <div class="mb-3">
            <label>Tujuan</label>
            <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan', $surat->tujuan) }}" required>
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
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" value="{{ old('keterangan', $surat->keterangan) }}"></textarea>
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
            <label>Isi Surat (PDF/PNG/JPG)</label>
            @if ($surat->isi_surat)
                <p>File sekarang: <a href="{{ asset('storage/' . $surat->isi_surat) }}" target="_blank">Lihat PDF</a></p>
            @endif
            <input type="file" name="isi_surat" class="form-control">
        </div>

        <button class="btn btn-success" type="submit">Simpan</button>
    </form>

    <div class="mt-3">
        <a href="{{ route('surat_keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    </div>
@endsection
