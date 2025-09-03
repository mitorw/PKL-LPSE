@extends('layouts.admin')

@section('content')
    <div class="container mt-4">

        @if (Auth::user()->role === 'admin')
            <div class="d-flex align-items-center gap-3 mb-3">
                <a href="{{ route('manajemen_akun.create') }}" class="btn btn-primary">
                    + Tambah Akun
                </a>
                <small class="text-muted">
                    <em>Password default = <strong style="color: red">password@12345</strong></em>
                </small>
            </div>
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
                                            <button type="submit" class="btn btn-success btn-ubah-role">Ubah</button>
                                        </div>
                                    </form>

                                    <form class="reset-password-form"
                                        action="{{ route('manajemen_akun.resetPassword', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Reset Pass</button>
                                    </form>

                                    @if (Auth::id() !== $user->id)
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
                // --- Script Reset Password ---
                document.querySelectorAll('.reset-password-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Reset Password?',
                            text: 'Anda yakin ingin mereset password akun ini?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ffc107',
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

                // --- Script Hapus Akun ---
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'PERINGATAN!',
                            text: 'Aksi ini akan menghapus akun secara permanen. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
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

                // --- Mencegah Spam Klik Ubah Role ---
                document.querySelectorAll('.form-ubah-role').forEach(form => {
                    form.addEventListener('submit', function(event) {
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
