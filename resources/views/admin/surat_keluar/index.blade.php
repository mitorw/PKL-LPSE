@extends('layouts.admin')

@section('content')
        <h2 class="mb-4">Daftar Surat Keluar</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <form method="GET" action="{{ route('surat_keluar.index') }}" class="mb-3 row g-2">
            {{-- Search --}}
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                placeholder="Cari...">
            </div>

            {{-- Filter Klasifikasi --}}
            <div class="col-md-2">
                <select name="klasifikasi" class="form-control">
                    <option value="">-- Semua Klasifikasi --</option>
                    <option value="biasa" {{ request('klasifikasi') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="penting" {{ request('klasifikasi') == 'penting' ? 'selected' : '' }}>Penting</option>
                    <option value="rahasia" {{ request('klasifikasi') == 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                </select>
            </div>

            {{-- Filter Tanggal --}}
            <div class="col-md-2">
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-control">
            </div>

            {{-- Tombol Submit --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            {{-- Tombol Reset --}}
            <div class="col-md-1">
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>

        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('surat_keluar.create') }}" class="mb-3 btn btn-primary">Tambah Surat Keluar</a>
            @endif
        @endauth

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Surat</th>
                    <th>Perihal</th>
                    <th>Tujuan</th>
                    <th>Tanggal</th>
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
                    <tr>
                        <td>{{ $surat->nomor_surat }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td>{{ $surat->tujuan }}</td>
                        <td>{{ $surat->tanggal }}</td>
                        <td>{{ $surat->dibuat_oleh }}</td>
                        <td>{{ ucfirst($surat->klasifikasi) }}</td>
                        <td>
                            @if ($surat->isi_surat)
                                <!-- Preview -->
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#pdfModal"
                                    data-file="{{ asset('storage/' . $surat->isi_surat) }}">
                                    Preview
                                </button>
                                <!-- Download -->
                                <a href="{{ asset('storage/' . $surat->isi_surat) }}" class="btn btn-sm btn-success"
                                    download>
                                    Download
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        @auth
                            @if (Auth::user()->role === 'admin')
                                <td>

                                    <a href="{{ route('surat_keluar.edit', $surat->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('surat_keluar.destroy', $surat->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus data ini?')"
                                            class="btn btn-sm btn-danger">Delete</button>
                                    </form>

                                </td>
                            @endif
                        @endauth

                    </tr>
                @endforeach
            </tbody>
        </table>

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


        {{-- Pagination --}}
        {{ $suratKeluar->appends(request()->all())->links() }}

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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
