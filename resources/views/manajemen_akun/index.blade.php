@extends('layouts.admin')

@section('content')
    <div class="container mt-4">

        @if (Auth::user()->role === 'admin')
            <a href="{{ route('manajemen_akun.create') }}" class="mb-3 btn btn-primary">
                + Tambah Akun
            </a>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    {{-- MODIFIKASI 1: Menambahkan class pada form --}}
                                    <form action="{{ route('manajemen_akun.updateRole', $user) }}" method="POST"
                                        class="form-ubah-role">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <select name="role" class="form-select">
                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User
                                                </option>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin
                                                </option>
                                            </select>
                                            {{-- MODIFIKASI 2: Menambahkan class pada tombol --}}
                                            <button type="submit" class="btn btn-success btn-ubah-role">Ubah</button>
                                        </div>
                                    </form>

                                    <form class="reset-password-form"
                                        action="{{ route('manajemen_akun.resetPassword', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Reset Pass</button>
                                    </form>

                                    @if (Auth::id() !== $user->id)
                                        {{-- Sembunyikan tombol hapus untuk diri sendiri --}}
                                        <form class="delete-form" action="{{ route('manajemen_akun.destroy', $user) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // --- Script untuk Konfirmasi Reset Password ---
                document.querySelectorAll('.reset-password-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Reset Password?',
                            text: 'Anda yakin ingin mereset password akun ini?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ffc107', // Warna kuning warning
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, reset!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });

                // --- Script untuk Konfirmasi Hapus Akun ---
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'PERINGATAN!',
                            text: 'Aksi ini akan menghapus akun secara permanen. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33', // Warna merah danger
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus permanen!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });

                // --- MODIFIKASI 3: Script untuk Mencegah Spam Klik Ubah Role ---
                document.querySelectorAll('.form-ubah-role').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        // Temukan tombol di dalam form yang sedang di-submit
                        const tombolUbah = form.querySelector('.btn-ubah-role');
                        if (tombolUbah) {
                            tombolUbah.disabled = true;
                            tombolUbah.innerText = '...';
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
