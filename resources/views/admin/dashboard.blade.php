@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>SURAT MASUK</h5>
                <h2 class="text-primary">{{ $suratMasuk }}</h2>
                <i class="fa fa-envelope fa-2x text-primary"></i>
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-success btn-sm mt-3">
                    Lihat </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>SURAT KELUAR</h5>
                <h2 class="text-success">{{ $suratKeluar }}</h2>
                <i class="fa fa-briefcase fa-2x text-success"></i>
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-success btn-sm mt-3">
                    Lihat </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>PENGGUNA</h5>
                <h2 class="text-secondary">{{ $pengguna }}</h2>
                <i class="fa fa-user fa-2x text-secondary"></i>
                <a href="{{ route('manajemen_akun.index') }}" class="btn btn-success btn-sm mt-3">
                    Lihat </a>
            </div>
        </div>
    </div>

    <div class="card shadow text-center p-3 mt-4">
        <div class="row mt-2">
            <div>
                <h4 class="mb-2">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted">Berikut adalah ringkasan data inventaris surat.</p>
            </div>
            {{-- Kolom untuk Bar Chart --}}
            <div class="col-md-8">
                <div class="card shadow p-3">
                    <h5>Statistik Surat 6 Bulan Terakhir</h5>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            {{-- Kolom untuk Pie Chart --}}
            <div class="col-md-4">
                <div class="card shadow p-3">
                    <h5>Proporsi Surat Bulan Ini</h5>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Ambil data dari controller yang sudah di-passing ke view
    const barChartLabels = @json($barChartLabels);
    const suratMasukData = @json($suratMasukData);
    const suratKeluarData = @json($suratKeluarData);
    const pieChartData = @json($pieChartData);

    // BAR CHART
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barChartLabels,
            datasets: [{
                label: 'Surat Masuk',
                data: suratMasukData,
                backgroundColor: 'rgb(25, 135, 84)',
                borderColor: 'rgb(25, 135, 84, 1)',
                borderWidth: 1
            }, {
                label: 'Surat Keluar',
                data: suratKeluarData,
                backgroundColor: 'rgb(220, 53, 69)',
                borderColor: 'rgb(92, 107, 192, 0.5)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // PIE CHART
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: [
                'Surat Masuk',
                'Surat Keluar'
            ],
            datasets: [{
                label: 'Jumlah Surat',
                data: pieChartData,
                backgroundColor: [
                    'rgb(25, 135, 84)', // Surat Masuk
                    'rgb(220, 53, 69)'  // Surat Keluar
                ],
                hoverOffset: 4
            }]
        }
    });
</script>
@endpush
