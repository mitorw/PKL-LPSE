@extends('layouts.admin')

@section('content')
    <div class="container mt-4">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error')) {{-- Tambahkan ini untuk menampilkan pesan error --}}
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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
                                    <form action="{{ route('manajemen_akun.updateRole', $user) }}" method="POST">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <select name="role" class="form-select">
                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                            <button type="submit" class="btn btn-success">Ubah</button>
                                        </div>
                                    </form>

                                    <form action="{{ route('manajemen_akun.resetPassword', $user) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mereset password akun ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Reset Pass</button>
                                    </form>

                                    @if (Auth::id() !== $user->id) {{-- Sembunyikan tombol hapus untuk diri sendiri --}}
                                        <form action="{{ route('manajemen_akun.destroy', $user) }}" method="POST" onsubmit="return confirm('PERINGATAN: Aksi ini akan menghapus akun secara permanen. Lanjutkan?');">
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
@endsection
