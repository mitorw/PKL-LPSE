@extends('layouts.admin')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<a href="{{ route('surat_masuk.create') }}" class="btn btn-primary mb-3">+ Tambah Surat Masuk</a>

<table class="table table-bordered table-striped">
    <thead class="table-blue">
        <tr>
            <th>No Surat</th>
            <th>Asal Surat</th>
            <th>Tanggal Terima</th>
            <th>Perihal</th>
            <th>Klasifikasi</th>
            <th>Disposisi</th>
            <th>Keterangan</th>
            <th>File Surat</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suratMasuk as $surat)
        <tr>
            <td>{{ $surat->no_surat }}</td>
            <td>{{ $surat->asal_surat }}</td>
            <td>{{ $surat->tanggal_terima }}</td>
            <td>{{ $surat->perihal }}</td>
            <td>{{ $surat->klasifikasi }}</td>
            <td>{{ $surat->disposisi->dis_bagian ?? '-' }}</td>
            <td>{{ $surat->keterangan }}</td>
            <td>
                @if($surat->file_surat)
                    <!-- Preview -->
                    <button class="btn btn-sm btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#pdfModal"
                        data-file="{{ asset('storage/' . $surat->file_surat) }}">
                        Preview
                    </button>
                    <!-- Download -->
                    <a href="{{ asset('storage/' . $surat->file_surat) }}" class="btn btn-sm btn-success" download>
                        Download
                    </a>
                @else
                    -
                @endif
            </td>
            <td>
                <a href="{{ route('surat_masuk.edit', $surat->id_surat_masuk) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('surat_masuk.destroy', $surat->id_surat_masuk) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfModal = document.getElementById('pdfModal');
    const pdfViewer = document.getElementById('pdfViewer');

    pdfModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const fileUrl = button.getAttribute('data-file');
        pdfViewer.src = fileUrl;
    });

    pdfModal.addEventListener('hidden.bs.modal', function () {
        pdfViewer.src = "";
    });
});
</script>
@endsection
