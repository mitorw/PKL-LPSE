@extends('layouts.admin')

@section('content')
    <div class="card">
    <div class="card-header">
        <h4>Tambah Akun</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('manajemen_akun.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Nama</label>
                    {{-- Tambahkan value old() untuk field nama --}}
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    {{-- Tambahkan value old() untuk field email --}}
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    {{-- JANGAN tambahkan value old() untuk password demi keamanan --}}
                    <input type="password" name="password" class="form-control" required minlength="8" maxlength="16">
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        {{-- Tambahkan logika 'selected' untuk field role --}}
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('manajemen_akun.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
