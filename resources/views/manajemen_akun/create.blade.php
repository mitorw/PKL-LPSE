@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tambah Akun</h2>

    <form action="{{ route('manajemen_akun.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('manajemen_akun.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
