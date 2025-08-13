@extends('layouts.admin')

@section('content')
<style>
    .profile-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #6c757d;
    }

    .card-custom {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .card-custom .card-header {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 1.1rem;
        border-bottom: 1px solid #e9ecef;
    }
</style>

<div class="container mt-4">
    <!-- Header Profil -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fa fa-user"></i>
        </div>
        <div>
            <h4 class="mb-1">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h4>
            <p class="text-muted mb-0">{{ Auth::user()->email ?? 'email@example.com' }}</p>
        </div>
    </div>

    <!-- Update Profile Information -->
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="fa fa-id-card me-2"></i> Update Profil
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3 row">
                    <label for="name" class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input id="name" name="name" type="text" class="form-control"
                            value="{{ old('name', Auth::user()->name) }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="email" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input id="email" name="email" type="email" class="form-control"
                            value="{{ old('email', Auth::user()->email) }}" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="fa fa-lock me-2"></i> Ubah Password
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3 row">
                    <label for="current_password" class="col-sm-3 col-form-label">Current Password</label>
                    <div class="col-sm-9">
                        <input id="current_password" name="current_password" type="password" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="password" class="col-sm-3 col-form-label">New Password</label>
                    <div class="col-sm-9">
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="password_confirmation" class="col-sm-3 col-form-label">Confirm Password</label>
                    <div class="col-sm-9">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Account -->
    <div class="card card-custom">
        <div class="card-header text-danger">
            <i class="fa fa-trash me-2"></i> Hapus Akun
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <p class="mb-3">Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.</p>

                <div class="text-end">
                    <button type="submit" class="btn btn-danger">Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
