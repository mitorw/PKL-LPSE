@extends('layouts.admin')

@section('content')
    <style>
        /* Menambahkan efek hover pada link sorting di header tabel */
        .sortable-link:hover {
            color: #0d6efd !important;
            /* Ganti warna teks menjadi biru saat di-hover */
            text-decoration: underline !important;
            /* Tambahkan garis bawah saat di-hover */
        }

        .clickable-row {
            cursor: pointer;
        }
    </style>

    <h2 class="mb-4">Daftar Surat Keluar</h2>

    <form action="{{ route('surat_keluar.index') }}" method="GET" class="mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    {{-- Kolom Pencarian --}}
                    <div class="col-md-12">
                        <label for="search" class="form-label">Cari Surat</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" class="form-control"
                                placeholder="No surat, tujuan, perihal, Lokasi Penyimpanan..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </div>

                    {{-- Kolom Filter Lanjutan --}}
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Terima</label>
                        <div class="input-group">
                            <input type="date" name="tanggal_awal" class="form-control"
                                value="{{ request('tanggal_awal') }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="klasifikasi" class="form-label">Klasifikasi</label>
                        <select id="klasifikasi" class="form-select" name="klasifikasi">
                            <option value="">Semua</option>
                            <option value="Rahasia" {{ request('klasifikasi') == 'Rahasia' ? 'selected' : '' }}>Rahasia
                            </option>
                            <option value="Penting" {{ request('klasifikasi') == 'Penting' ? 'selected' : '' }}>Penting
                            </option>
                            <option value="Biasa" {{ request('klasifikasi') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        </select>
                    </div>


                    <div class="col-md-2  d-grid">
                        <div class="btn-group">
                            <button class="btn btn-primary" type="submit">Filter</button>
                            <a href="{{ route('surat_keluar.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @auth
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('surat_keluar.create') }}" class="mb-3 btn btn-primary">+ Tambah Surat Keluar</a>
        @endif
    @endauth

    <div class="table-responsive card">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>
                        <a class="text-decoration-none text-dark sortable-link"
                            href="{{ route('surat_keluar.index', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Nomor Surat
                            @if (request('sort') == 'nomor_surat')
                                <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @else
                                <i class="ms-1 fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>Perihal</th>
                    <th>Tujuan</th>
                    <th>
                        <a class="text-decoration-none text-dark sortable-link"
                            href="{{ route('surat_keluar.index', array_merge(request()->query(), ['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Tanggal
                            @if (request('sort') == 'tanggal')
                                <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @else
                                <i class="ms-1 fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>Dibuat Oleh</th>
                    <th>Klasifikasi</th>
                    <th>File</th>
                    @auth
                        @if (Auth::user()->role === 'admin')
                            <th>Action</th>
                        @endif
                    @endauth

                </tr>
            </thead>
            <tbody>
                @foreach ($suratKeluar as $surat)
                    <tr class="clickable-row">
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal{{ $surat->id }}">
                            {{ $surat->nomor_surat }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal{{ $surat->id }}">
                            {{ $surat->perihal }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal{{ $surat->id }}">
                            {{ $surat->tujuan }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal{{ $surat->id }}">
                            {{ date('d/m/Y', strtotime($surat->tanggal)) }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal{{ $surat->id }}">
                            {{ $surat->dibuat_oleh }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            @php
                                $badgeClass = match (strtolower(trim($surat->klasifikasi))) {
                                    'penting' => 'bg-warning text-dark',
                                    'rahasia' => 'bg-danger',
                                    default => 'bg-success',
                                };
                            @endphp

                            {{-- Kode untuk menampilkan badge-nya, misalnya: --}}
                            <span class="badge {{ $badgeClass }}">{{ $surat->klasifikasi }}</span>
                        </td>
                        <td>
                            @if ($surat->isi_surat)
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#pdfModal"
                                    data-file="{{ asset('storage/' . $surat->isi_surat) }}"
                                    onclick="event.stopPropagation()">
                                    Preview
                                </button>
                                <a href="{{ asset('storage/' . $surat->isi_surat) }}" class="btn btn-sm btn-success"
                                    download onclick="event.stopPropagation()">
                                    Download
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        @auth
                            @if (Auth::user()->role === 'admin')
                                <td>
                                    <a href="{{ route('surat_keluar.edit', $surat->id) }}" class="btn btn-sm btn-warning"
                                        onclick="event.stopPropagation()">Edit</a>
                                    <form class="delete-form" action="{{ route('surat_keluar.destroy', $surat->id) }}"
                                        method="POST" style="display:inline;" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            @endif
                        @endauth
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($suratKeluar->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $suratKeluar->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @foreach ($suratKeluar as $surat)
        <div class="modal fade" id="detailSuratKeluarModal{{ $surat->id }}" tabindex="-1" aria-hidden="true">
            <style>
                .surat-detail-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .surat-detail-table th,
                .surat-detail-table td {
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }

                .surat-detail-table th {
                    background-color: #f2f2f2;
                    width: 30%;
                }
            </style>
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Surat Keluar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="surat-detail-table">
                            <tbody>
                                <tr>
                                    <th>Nomor Surat</th>
                                    <td>{{ $surat->nomor_surat }}</td>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    <td>{{ $surat->tujuan }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ \Carbon\Carbon::parse($surat->tanggal)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Perihal</th>
                                    <td>{{ $surat->perihal }}</td>
                                </tr>
                                <tr>
                                    <th>Klasifikasi</th>
                                    <td>{{ ucfirst($surat->klasifikasi) }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $surat->dibuat_oleh }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi Penyimpanan</th>
                                    <td>{{ $surat->keterangan }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width:90vw;">
            <div class="modal-content" style="height: 90vh;">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Preview Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-0 modal-body" style="height: 80vh;">
                    <iframe id="pdfFrame" src="" frameborder="0" style="width:100%; height:100%;"></iframe>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pdfModal = document.getElementById('pdfModal');

            pdfModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var fileUrl = button.getAttribute('data-file');

                var iframe = pdfModal.querySelector('#pdfFrame');
                iframe.src = fileUrl;
            });

            pdfModal.addEventListener('hidden.bs.modal', function() {
                var iframe = pdfModal.querySelector('#pdfFrame');
                iframe.src = '';
            });
        });
    </script>
    @push('scripts')
        <script>
            // Menjalankan semua script setelah halaman dimuat sepenuhnya
            document.addEventListener('DOMContentLoaded', function() {

                // --- Script untuk PDF Modal ---
                const pdfModal = document.getElementById('pdfModal');
                if (pdfModal) {
                    pdfModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const fileUrl = button.getAttribute('data-file');
                        const iframe = pdfModal.querySelector('#pdfFrame');
                        iframe.src = fileUrl;
                    });

                    pdfModal.addEventListener('hidden.bs.modal', function() {
                        const iframe = pdfModal.querySelector('#pdfFrame');
                        iframe.src = '';
                    });
                }

                // --- Script untuk SweetAlert Delete Confirmation ---
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745', // Contoh warna hijau
                            cancelButtonColor: '#d33', // Contoh warna abu-abu
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });

            });
        </script>
    @endpush
@endsection
