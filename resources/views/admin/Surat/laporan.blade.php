@extends('layouts.admin')

@section('content')
    <h1 class="mb-4 h3 fw-semibold">Laporan Inventarisasi Surat</h1>

    {{-- FILTERS --}}
    <div class="mb-4 card">
        <div class="card-body">
            <h2 class="mb-3 h5 fw-bold">Filters</h2>

            <form id="filterForm" action="{{ route('laporan.surat') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" value="{{ request('nomor_surat') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" id="dari_tanggal" value="{{ request('dari_tanggal') }}"
                            class="form-control">
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
                            <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk
                            </option>
                            <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar
                            </option>
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
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('laporan.surat') }}" class="btn btn-secondary">
                        <i class="fa fa-redo me-1"></i> Reset
                    </a>
                    <button type="button" onclick="cetakLaporan()" class="btn btn-danger">
                        <i class="fa fa-file-pdf me-1"></i> Cetak PDF
                    </button>
                </div>
            </form>

            <script>
    function cetakLaporan() {
        const form = document.getElementById('filterForm');
        const url = '{{ route('laporan.cetak') }}';
        const queryString = new URLSearchParams(new FormData(form)).toString();

        // tetap pakai query string, tapi dibuka di tab baru
        window.open(`${url}?${queryString}`, '_blank', 'noopener,noreferrer');
    }
</script>

        </div>
    </div>

    {{-- TABEL --}}
    <div class="card">
        <div class="card-body">
            <h2 class="mb-3 h5 fw-bold">Daftar Surat</h2>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a class="text-decoration-none"
                                    href="{{ route('laporan.surat', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Nomor Surat
                                    @if (request('sort') == 'nomor_surat')
                                        <i
                                            class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a class="text-decoration-none"
                                    href="{{ route('laporan.surat', array_merge(request()->query(), ['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Tanggal
                                    @if (request('sort') == 'tanggal')
                                        <i
                                            class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    @endif
                                </a>
                            </th>
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
                                    <span
                                        class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($surat->status) }}</span>

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
        @endsection
