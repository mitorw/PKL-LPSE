@extends('layouts.admin')

@section('content')
    <h2>Profil Pengguna</h2>

   {{-- ALERT BERHASIL --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ALERT GAGAL (custom dari controller) --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ALERT VALIDASI LARAVEL --}}
@if($errors->updatePassword->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->updatePassword->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ALERT VALIDASI PROFIL --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .card-custom .card-header {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid #e9ecef;
        }

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

    <div class="container mt-4">
        <div class="profile-header">
            <div class="profile-avatar">
                @if (Auth::user()->profile_photo)
                    <img src="{{ Auth::user()->profile_photo
                        ? asset('storage/' . Auth::user()->profile_photo)
                        : 'https://via.placeholder.com/150' }}"
                        alt="Foto Profil" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                @else
                    <i class="fa fa-user"></i>
                @endif
                </a>

            </div>
            <div>
                <h4 class="mb-1">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h4>
                <p class="mb-0 text-muted">{{ Auth::user()->email ?? 'email@example.com' }}</p>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-7 col-md-7">

                {{-- Edit Profile --}}
                <div class="mb-4 card card-custom">
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
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-end">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Ubah Password --}}
                <div class="mb-4 card card-custom">
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
                                    <div class="input-group">
                                        <input id="current_password" name="current_password" type="password"
                                            class="form-control" required>
                                        <button class="btn toggle-password" type="button" data-target="#current_password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="password" class="col-sm-3 col-form-label">New Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input id="password" name="password" type="password" class="form-control" required>
                                        <button class="btn toggle-password" type="button" data-target="#password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="password_confirmation" class="col-sm-3 col-form-label">Confirm Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                            class="form-control" required>
                                        <button class="btn toggle-password" type="button"
                                            data-target="#password_confirmation">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-end">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Ubah Foto Profil --}}
            <div class="col-lg-5 col-md-5 ">
                <div class="mb-4 card card-custom h-100">
                    <div class="card-header">
                        <i class="fa fa-image me-2"></i> Ubah Foto Profil
                    </div>
                    <div class="text-center card-body d-flex flex-column justify-content-center">
                        <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <img src="{{ Auth::user()->profile_photo
                                    ? asset('storage/' . Auth::user()->profile_photo)
                                    : 'https://via.placeholder.com/150' }}"
                                    alt="Foto Profil" class="rounded-circle" width="150" height="150"
                                    style="object-fit: cover;">
                            </div>
                            <div class="mb-3">
                                <input class="form-control" type="file" name="profile_photo" id="formFile"
                                    accept="image/*">

                                <!-- Area Cropping -->
                                <div id="croppie-container"
                                    style="width: 100%; height: 300px; margin-top: 15px; display: none;"></div>



                            </div>
                            <div class="mt-5">
                                <button type="button" id="crop-result" class="btn btn-primary">Simpan Foto</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
document.addEventListener('DOMContentLoaded', function() {
    let croppieInstance;
    const fileInput = document.getElementById('formFile');
    const croppieContainer = document.getElementById('croppie-container');
    const cropBtn = document.getElementById('crop-result');

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (!file) return;

        // ✅ Validasi ekstensi / MIME type
        const validTypes = ["image/jpeg", "image/jpg", "image/png"];
        if (!validTypes.includes(file.type)) {
            alert("❌ Hanya file JPG, JPEG, atau PNG yang diperbolehkan!");
            fileInput.value = ""; // reset input
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            croppieContainer.style.display = 'block';

            // Hapus instance lama
            if (croppieInstance) {
                croppieInstance.destroy();
            }

            // Buat croppie baru
            croppieInstance = new Croppie(croppieContainer, {
                viewport: {
                    width: 200,
                    height: 200,
                    type: 'circle'
                },
                boundary: {
                    width: 300,
                    height: 300
                },
                enableOrientation: true
            });

            croppieInstance.bind({
                url: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });

    cropBtn.addEventListener('click', function() {
        if (!croppieInstance) {
            alert("⚠️ Silakan pilih foto dulu!");
            return;
        }

        croppieInstance.result({
            type: 'base64',
            size: 'viewport'
        }).then(function(base64) {
            fetch("{{ route('profile.photo.update') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    image: base64
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("❌ Gagal menyimpan foto profil!");
                }
            })
            .catch(() => {
                alert("❌ Terjadi error saat mengirim data!");
            });
        });
    });
});

    </script>
@endpush
