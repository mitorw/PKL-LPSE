@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Manajemen Akun</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('manajemen_akun.create') }}" class="mb-3 btn btn-primary">
                + Tambah Akun
            </a>
        @endif

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
                            <form action="{{ route('manajemen_akun.updateRole', $user) }}" method="POST"
                                class="gap-2 d-flex align-items-center">
                                @csrf
                                <select name="role" class="w-auto form-select form-select-sm">
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Ubah Role</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
