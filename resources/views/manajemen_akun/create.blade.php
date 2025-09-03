@extends('layouts.admin')

@section('content')
    <style>
        .toggle-password {
            border: 1px solid #ced4da;

            background-color: transparent;

            transition: background-color 0.2s ease;
        }

        .toggle-password:hover {
            background-color: #0d6efd;
            border-radius: 5px;
            color: white
        }

        .toggle-password:focus,
        .toggle-password:active {
            box-shadow: none !important;
            outline: none !important;
        }
    </style>


    <div class="card">
        <div class="card-header">
            <h4>Tambah Akun</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('manajemen_akun.store') }}" method="POST" id="form-tambah-akun">
                @csrf
                <div class="mb-3">
                    <label>Nama</label>
                    {{-- Tambahkan value old() untuk field nama --}}
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    {{-- Tambahkan value old() untuk field email --}}
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3 row">
                    <label for="password">New Password</label>
                    <div>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required minlength="8" maxlength="16" required>
                            <button class="btn toggle-password" type="button" data-target="#password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        {{-- Tambahkan logika 'selected' untuk field role --}}
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" id="tombol-simpan">Simpan</button>
                <a href="{{ route('manajemen_akun.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Menunggu hingga seluruh dokumen HTML selesai dimuat
            document.addEventListener('DOMContentLoaded', function() {

                // Pilih semua tombol yang memiliki kelas .toggle-password
                const togglePasswordButtons = document.querySelectorAll('.toggle-password');

                // Lakukan perulangan untuk setiap tombol
                togglePasswordButtons.forEach(button => {
                    // Tambahkan event listener untuk 'click'
                    button.addEventListener('click', function() {
                        // Ambil target input dari atribut data-target
                        const targetInputSelector = this.getAttribute('data-target');
                        const targetInput = document.querySelector(targetInputSelector);

                        // Ambil ikon di dalam tombol
                        const icon = this.querySelector('i');

                        // Periksa tipe input saat ini
                        if (targetInput.type === 'password') {
                            // Jika tipenya 'password', ubah ke 'text'
                            targetInput.type = 'text';
                            // Ubah ikonnya menjadi 'mata-coret'
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            // Jika tipenya 'text', ubah kembali ke 'password'
                            targetInput.type = 'password';
                            // Ubah ikonnya menjadi 'mata'
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                });

            });
        </script>

        <script>
            document.getElementById('form-tambah-akun').addEventListener('submit', function() {
                const tombolSimpan = document.getElementById('tombol-simpan');
                tombolSimpan.disabled = true;
                tombolSimpan.innerText = 'Menyimpan...';
            });
        </script>
    @endpush
@endsection
