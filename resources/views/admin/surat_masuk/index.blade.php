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
    <h2 class="mb-4">Daftar Surat Masuk</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('surat_masuk.index') }}" method="GET" class="mb-3">

        <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari Surat" name="search" value="{{ request('index') }}">
            <div class="input-group-append">
                <button class="mx-2 btn btn-primary" type="submit">Cari</button>
            </div>
        </div>

    </form>



    <form action="{{ route('surat_masuk.index') }}" method="GET" class="mb-3">

        <div class="input-group">

            <input type="date" class="ml-2 form-control" name="start_date" value="{{ request('start_date') }}">
            <input type="date" class="ml-2 form-control" name="end_date" value="{{ request('end_date') }}">

            <select class="ml-2 form-control" name="klasifikasi">

                <option value="">Semua Klasifikasi</option>
                <option value="Rahasia" {{ request('klasifikasi') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                <option value="Penting" {{ request('klasifikasi') == 'Penting' ? 'selected' : '' }}>Penting</option>
                <option value="Biasa" {{ request('klasifikasi') == 'Biasa' ? 'selected' : '' }}>Biasa</option>

            </select>
            <select class="ml-2 form-control" name="disposisi_status">
                <option value="">Semua Disposisi</option>
                <option value="ada" {{ request('disposisi_status') == 'ada' ? 'selected' : '' }}>Ada Disposisi</option>
                <option value="tidak_ada" {{ request('disposisi_status') == 'tidak_ada' ? 'selected' : '' }}>Belum
                    Disposisi</option>
            </select>

            <div class="input-group-append">

                <button class="mx-2 btn btn-primary" type="submit">Filter</button>
                <a href="{{ route('surat_masuk.index') }}" class="mr-2 btn btn-secondary">Reset</a>

            </div>

        </div>

    </form>

    @auth
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('surat_masuk.create') }}" class="mb-3 btn btn-primary">+ Tambah Surat Masuk</a>
        @endif
    @endauth

    <div class="table-responsive card">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>
                        <a class="text-decoration-none text-dark sortable-link"
                            href="{{ route('surat_masuk.index', array_merge(request()->query(), ['sort' => 'no_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            No Surat
                            @if (request('sort') == 'no_surat')
                                <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @else
                                <i class="ms-1 fas fa-sort text-muted"></i>
                            @endif

                        </a>
                    </th>
                    <th>Asal Surat</th>
                    <th>
                        <a class="text-decoration-none text-dark sortable-link"
                            href="{{ route('surat_masuk.index', array_merge(request()->query(), ['sort' => 'tanggal_terima', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Tanggal
                            @if (request('sort') == 'tanggal_terima')
                                <i class="ms-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @else
                                <i class="ms-1 fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>Perihal</th>
                    <th>Klasifikasi</th>
                    <th>Disposisi</th>
                    <th>Keterangan</th>
                    <th>File Surat</th>
                    <th>Action</th>

                </tr>
            </thead>

            {{-- Table hover bisa menjadi view --}}
            <tbody>
                @foreach ($data as $surat)
                    <tr class="clickable-row">

                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->no_surat }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->asal_surat }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->tanggal_terima }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->perihal }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->klasifikasi }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->disposisi->dis_bagian ?? '-' }}</td>
                        <td data-bs-toggle="modal" data-bs-target="#detailSuratModal{{ $surat->id_surat_masuk }}">
                            {{ $surat->keterangan }}</td>
                        <td>
                            @if ($surat->file_surat)
                                <button class="my-2 btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#pdfModal"
                                    data-file="{{ asset('storage/' . $surat->file_surat) }}"
                                    onclick="event.stopPropagation()">
                                    Preview
                                </button>
                                <a href="{{ asset('storage/' . $surat->file_surat) }}" class="btn btn-sm btn-success"
                                    download onclick="event.stopPropagation()">
                                    Download
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @auth
                                @if (Auth::user()->role === 'admin')
                                    <a href="{{ route('surat_masuk.edit', $surat->id_surat_masuk) }}"
                                        class="my-2 btn btn-sm btn-warning" onclick="event.stopPropagation()">Edit</a>

                                    <form action="{{ route('surat_masuk.destroy', $surat->id_surat_masuk) }}" method="POST"
                                        style="display:inline;" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus data ini?')"
                                            class="btn btn-sm btn-danger ">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-center">
            {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>



    <!-- Modal Preview PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfViewer" src="" width="100%" height="600px" style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    @foreach ($data as $surat)
        <div class="modal fade" id="detailSuratModal{{ $surat->id_surat_masuk }}" tabindex="-1"
            aria-labelledby="detailSuratLabel{{ $surat->id_surat_masuk }}" aria-hidden="true">
            <style>
                .surat-detail-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-family: Arial, sans-serif;
                }

                .surat-detail-table th,
                .surat-detail-table td {
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }

                .surat-detail-table th {
                    background-color: #f2f2f2;
                    width: 20%;
                    /* Adjust as needed */
                }
            </style>

            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailSuratLabel{{ $surat->id_surat_masuk }}">Detail Surat Masuk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="surat-container">
                            <table class="surat-detail-table">
                                <tbody>
                                    <tr>
                                        <th>No Surat</th>
                                        <td>{{ $surat->no_surat }}</td>
                                    </tr>
                                    <tr>
                                        <th>Asal Surat</th>
                                        <td>{{ $surat->asal_surat }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Terima</th>
                                        <td>{{ $surat->tanggal_terima }}</td>
                                    </tr>
                                    <tr>
                                        <th>Perihal</th>
                                        <td>{{ $surat->perihal }}</td>
                                    </tr>
                                    <tr>
                                        <th>Klasifikasi</th>
                                        <td>{{ $surat->klasifikasi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>{{ $surat->keterangan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Disposisi</th>
                                        <td>
                                            @if ($surat->disposisi)
                                                <table class="surat-detail-table">
                                                    <tr>
                                                        <th>Bagian</th>
                                                        <td>
                                                            {{ $surat->disposisi->dis_bagian }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Catatan</th>
                                                        <td>
                                                            {{ $surat->disposisi->catatan }} <br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Instruksi</th>
                                                        <td>
                                                            {{ $surat->disposisi->instruksi }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            @else
                                                Tidak ada disposisi
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pdfModal = document.getElementById('pdfModal');
            const pdfViewer = document.getElementById('pdfViewer');

            pdfModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const fileUrl = button.getAttribute('data-file');
                pdfViewer.src = fileUrl;
            });

            pdfModal.addEventListener('hidden.bs.modal', function() {
                pdfViewer.src = "";
            });
        });
    </script>
@endsection
