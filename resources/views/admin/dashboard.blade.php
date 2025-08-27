@extends('layouts.admin')

@section('content')
    {{-- JUDUL --}}
    <h1 class="mb-4 h3 fw-semibold">Dashboard</h1>

    {{-- CARD RINGKASAN --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>SURAT MASUK</h5>
                <h2 class="text-primary">{{ $suratMasuk }}</h2>
                <i class="fa fa-envelope fa-2x text-primary"></i>
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-success btn-sm mt-3">Lihat</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>SURAT KELUAR</h5>
                <h2 class="text-success">{{ $suratKeluar }}</h2>
                <i class="fa fa-briefcase fa-2x text-success"></i>
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-success btn-sm mt-3">Lihat</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>PENGGUNA</h5>
                <h2 class="text-secondary">{{ $pengguna }}</h2>
                <i class="fa fa-user fa-2x text-secondary"></i>
                <a href="{{ route('manajemen_akun.index') }}" class="btn btn-success btn-sm mt-3">Lihat</a>
            </div>
        </div>
    </div>

    {{-- SELAMAT DATANG + CHART --}}
    <div class="card shadow text-center p-3 mb-4">
        <div class="row mt-2">
            <div>
                <h4 class="mb-2">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted">Berikut adalah ringkasan data inventaris surat.</p>
            </div>
            {{-- Bar Chart --}}
            <div class="col-md-8">
                <div class="card shadow p-3">
                    <h5>Statistik Surat 6 Bulan Terakhir</h5>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            {{-- Pie Chart --}}
            <div class="col-md-4">
                <div class="card shadow p-3">
                    <h5>Proporsi Surat Bulan {{ $namaBulanIni }}</h5>
                    <canvas id="pieChart"></canvas>
                    <h6 class="mt-1">Total Surat Bulan {{ $namaBulanIni }} : {{ $totalSuratBulanIni }} Surat</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER LAPORAN --}}
    <div class="mb-4 card">
        <div class="card-body">
            <h2 class="mb-3 h5 fw-bold">Filters</h2>
            <form id="filterForm" action="{{ route('dashboard') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat"
                               value="{{ request('nomor_surat') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" id="dari_tanggal"
                               value="{{ request('dari_tanggal') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" id="sampai_tanggal"
                               value="{{ request('sampai_tanggal') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="jenis_surat" class="form-label">Jenis Surat</label>
                        <select name="jenis_surat" id="jenis_surat" class="form-select">
                            <option value="all" {{ request('jenis_surat') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                            <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status Surat</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="Biasa" {{ request('status') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Penting" {{ request('status') == 'Penting' ? 'selected' : '' }}>Penting</option>
                            <option value="Rahasia" {{ request('status') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="gap-2 mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success"><i class="fa fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fa fa-redo me-1"></i> Reset</a>
                    <button type="button" onclick="cetakLaporan()" class="btn btn-danger"><i class="fa fa-file-pdf me-1"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL LAPORAN --}}
    <div class="card">
        <div class="card-body">
            <h2 class="mb-3 h5 fw-bold">Daftar Surat</h2>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor Surat</th>
                            <th>Tanggal</th>
                            <th>Jenis Surat</th>
                            <th>Perihal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanSurat as $surat)
                            <tr>
                                <td>{{ $surat->nomor_surat }}</td>
                                <td>{{ $surat->tanggal }}</td>
                                <td>{{ ucfirst($surat->jenis_surat) }}</td>
                                <td>{{ $surat->perihal }}</td>
                                <td>
                                    @php
                                        $status = trim(strtolower($surat->status));
                                        $badgeClass = match ($status) {
                                            'penting' => 'bg-warning text-dark',
                                            'rahasia' => 'bg-danger',
                                            default => 'bg-success',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($surat->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data surat yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $laporanSurat->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function cetakLaporan() {
        const form = document.getElementById('filterForm');
        const url = '{{ route('laporan.cetak') }}';
        const queryString = new URLSearchParams(new FormData(form)).toString();
        window.open(`${url}?${queryString}`, '_blank', 'noopener,noreferrer');
    }

    // ====== Chart.js ======
    const barChartLabels = @json($barChartLabels);
    const suratMasukData = @json($suratMasukData);
    const suratKeluarData = @json($suratKeluarData);
    const pieChartData = @json($pieChartData);
    Chart.register(ChartDataLabels);

    // BAR CHART
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barChartLabels,
            datasets: [
                { label: 'Surat Masuk', data: suratMasukData, backgroundColor: 'rgb(25, 135, 84)' },
                { label: 'Surat Keluar', data: suratKeluarData, backgroundColor: 'rgb(220, 53, 69)' }
            ]
        },
        options: {
            plugins: {
                datalabels: { color: '#fff', font: { weight: 'bold' } }
            },
            scales: { y: { beginAtZero: true } }
        }
    });

    // PIE CHART
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Surat Masuk', 'Surat Keluar'],
            datasets: [{
                data: pieChartData,
                backgroundColor: ['rgb(25, 135, 84)', 'rgb(220, 53, 69)']
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    formatter: (value, context) => {
                        const datapoints = context.chart.data.datasets[0].data;
                        const total = datapoints.reduce((total, datapoint) => total + datapoint, 0);
                        const percentage = (value / total) * 100;
                        return total > 0 ? percentage.toFixed(1) + "%" : "0%";
                    },
                    color: '#fff',
                    font: { weight: 'bold', size: 14 }
                }
            }
        }
    });
</script>
@endpush
