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

    {{-- FORM FILTER & TABEL HASIL FILTER --}}

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

    @if ($isFiltering)
    {{-- Tampilan Hasil Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="mb-3 h5 fw-bold">Hasil Filter Laporan Surat</h2>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>
                                {{-- Kode Anda, disesuaikan untuk kolom Nomor Surat --}}
                                <a class="text-decoration-none text-dark" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Nomor Surat
                                    @if (request('sort') == 'nomor_surat')
                                        <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                    @else
                                        <i class="ms-1 fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                {{-- Kode Anda, disesuaikan untuk kolom Tanggal --}}
                                <a class="text-decoration-none text-dark" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Tanggal Terima
                                    {{-- Pengecekan 'sort' menggunakan 'tanggal' sesuai controller dashboard kita --}}
                                    @if (request('sort') == 'tanggal')
                                        <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                    @else
                                        <i class="ms-1 fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Perihal</th>
                            <th>Status</th>
                            <th>Asal/Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanSurat as $index => $surat)
                            <tr>
                                <td>{{ $laporanSurat->firstItem() + $index }}</td>
                                <td>
                                    @if ($surat->jenis_surat == 'masuk')
                                        <span class="badge bg-primary">Masuk</span>
                                    @else
                                        <span class="badge bg-success">Keluar</span>
                                    @endif
                                </td>
                                <td>{{ $surat->nomor_surat }}</td>
                                <td>{{ \Carbon\Carbon::parse($surat->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $surat->perihal }}</td>
                                <td>
                                    @php
                                        $badgeClass = match (strtolower(trim($surat->status))) {
                                            'penting' => 'bg-warning text-dark',
                                            'rahasia' => 'bg-danger',
                                            default => 'bg-info',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($surat->status) }}</span>
                                </td>
                                <td>{{ $surat->asal ?? $surat->tujuan }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Tidak ada data yang cocok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $laporanSurat->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@else
    {{-- Tampilan Default Dashboard --}}
    <div class="card mb-4">
    <div class="card-body">
        <h2 class="mb-3 h5 fw-bold">Daftar Surat Masuk</h2>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>
                            <a class="text-decoration-none text-dark"
                                href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                Nomor Surat
                                @if (request('sort') == 'nomor_surat')
                                    <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                @else
                                    <i class="ms-1 fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a class="text-decoration-none text-dark"
                                href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                Tanggal
                                @if (request('sort') == 'tanggal')
                                    <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                @else
                                    <i class="ms-1 fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>Perihal</th>
                        <th>Status</th>
                        <th>Asal</th>
                        <th>Lokasi Penyimpanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratMasukTerakhir as $index => $surat)
                        <tr>
                            <td>{{ $suratMasukTerakhir->firstItem() + $index }}</td>
                            <td>{{ $surat->no_surat }}</td>
                            <td>{{ \Carbon\Carbon::parse($surat->tanggal_terima)->translatedFormat('d F Y') }}</td>
                            <td>{{ $surat->perihal }}</td>
                            <td>
                                @php
                                    $badgeClass = match (strtolower(trim($surat->klasifikasi))) {
                                        'penting' => 'bg-warning text-dark',
                                        'rahasia' => 'bg-danger',
                                        default => 'bg-success',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($surat->klasifikasi) }}</span>
                            </td>
                            <td>{{ $surat->asal_surat ?? '-' }}</td>
                            <td>{{ $surat->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data surat masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            {{ $suratMasukTerakhir->links() }}
        </div>
    </div>
</div>

---

<div class="card">
    <div class="card-body">
        <h2 class="mb-3 h5 fw-bold">Daftar Surat Keluar</h2>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>
                            <a class="text-decoration-none text-dark"
                                href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                Nomor Surat
                                @if (request('sort') == 'nomor_surat')
                                    <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                @else
                                    <i class="ms-1 fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a class="text-decoration-none text-dark"
                                href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                Tanggal
                                @if (request('sort') == 'tanggal')
                                    <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'sort-up' : 'sort-down' }}"></i>
                                @else
                                    <i class="ms-1 fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>Perihal</th>
                        <th>Status</th>
                        <th>Tujuan</th>
                        <th>Lokasi Penyimpanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratKeluarTerakhir as $index => $surat)
                        <tr>
                            <td>{{ $suratKeluarTerakhir->firstItem() + $index }}</td>
                            <td>{{ $surat->nomor_surat }}</td>
                            <td>{{ \Carbon\Carbon::parse($surat->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>{{ $surat->perihal }}</td>
                            <td>
                                @php
                                    $badgeClass = match (strtolower(trim($surat->klasifikasi))) {
                                        'penting' => 'bg-warning text-dark',
                                        'rahasia' => 'bg-danger',
                                        default => 'bg-success',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($surat->klasifikasi) }}</span>
                            </td>
                            <td>{{ $surat->tujuan ?? '-' }}</td>
                            <td>{{ $surat->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data surat keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            {{ $suratKeluarTerakhir->links() }}
        </div>
    </div>
</div>
@endif

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
