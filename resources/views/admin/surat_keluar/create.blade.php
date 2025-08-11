@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tambah Surat Keluar</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat_keluar.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Nomor Surat</label>
            <input type="text" name="nomor_surat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Perihal</label>
            <input type="text" name="perihal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tujuan</label>
            <input type="text" name="tujuan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Dibuat Oleh</label>
            <input type="text" name="dibuat_oleh" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Klasifikasi</label>
            <select name="klasifikasi" class="form-control" required>
                <option value="biasa">Biasa</option>
                <option value="penting">Penting</option>
                <option value="rahasia">Rahasia</option>
            </select>
        </div>

        <div class="mb-3">
            <label>File Surat (PDF/PNG/JPG)</label>
            <input type="file" name="isi_surat" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
