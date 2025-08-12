@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow text-center p-3">
                <h5>SURAT MASUK</h5>
                <h2 class="text-primary">{{ $suratMasuk }}</h2>
                <i class="fa fa-envelope fa-2x text-primary"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow text-center p-3">
                <h5>SURAT KELUAR</h5>
                <h2 class="text-success">{{ $suratKeluar }}</h2>
                <i class="fa fa-briefcase fa-2x text-success"></i>
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-success btn-sm mt-3">
                    Lihat </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow text-center p-3">
                <h5>PENGGUNA</h5>
                <h2 class="text-secondary">{{ $pengguna }}</h2>
                <i class="fa fa-user fa-2x text-secondary"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow text-center p-3">
                <h5>DISPOSISI</h5>
                <h2 class="text-warning">{{ $disposisi }}</h2>
                <i class="fa fa-check-circle fa-2x text-warning"></i>
            </div>
        </div>
    </div>
@endsection
