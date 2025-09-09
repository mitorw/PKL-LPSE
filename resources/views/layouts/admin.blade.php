<!DOCTYPE html>
<html>

<head>
    <title>Sistem Manajemen Surat</title>
    <link rel="icon" type="image/png" href="{{ asset('../storage/assets/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" />

    <style>
        body {
            background-color: #f5f5f5;
            overflow-x: hidden;
        }

        .dropdown-menu {
            border-radius: 0.75rem !important;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
            border: none !important;
        }

        .dropdown-header-custom {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .dropdown-header-custom h6 {
            font-weight: 600;
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
        }

        .dropdown-item i {
            width: 20px;
            color: #6c757d;
        }

        .dropdown-item:hover {
            background-color: #5c6bc0;
            color: #f5f5f5;
        }

        .dropdown-item:hover i {
            color: #f5f5f5;
        }

        .dropdown-item.text-danger:hover,
        .dropdown-item.text-danger:hover i {
            color: white !important;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #3f51b5;
            color: white;
            position: fixed;
            padding-top: 5px;
            transition: margin-left 0.3s ease-in-out;
            z-index: 1030;
        }
        .sidebar img {
            max-width: 200px;
            margin-top: 9px;
            margin-bottom: 24px;
        }

        .sidebar a {
            color: white;
            display: flex;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            align-items: center;
        }

        .sidebar a i {
            width: 40px;
            margin-right: 8px;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: #ffffff33;
        }

        #content-wrapper {
            margin-left: 240px;
            padding-top: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        .content {
            padding: 20px;
        }

        .card {
            border-radius: 10px;
        }

        .header-bar {
            color: white;
            height: 95px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 1020;
            background-color: #5c6bc0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #sidebarToggle {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 20px;
            margin-right: 15px;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        body.sidebar-toggled .sidebar {
            margin-left: -240px;
        }

        body.sidebar-toggled #content-wrapper {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -240px;
            }

            #content-wrapper {
                margin-left: 0;
            }

            body.sidebar-toggled .sidebar {
                margin-left: 0;
            }

            body.sidebar-toggled #content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    {{-- Side Bar --}}
    <div class="sidebar">
        <div style="text-align: center; padding: 10px 0; border-bottom: 4px solid #3f51b5;">
            <img src="../storage/assets/simantap.png" alt="Logo">
        </div>
        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="{{ route('surat_masuk.index') }}"><i class="fa fa-inbox"></i> Surat Masuk</a>
        <a href="{{ route('surat_keluar.index') }}"><i class="fa fa-paper-plane"></i> Surat Keluar</a>
        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('manajemen_akun.index') }}"><i class="fa fa-users-cog"></i> Manajemen Akun</a>
            @endif
        @endauth
    </div>

    {{-- Wrapper untuk Konten Utama --}}
    <div id="content-wrapper">
        {{-- Header bar --}}
        <div class="header-bar">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h2 class="mb-0">{{ $pageTitle ?? 'Halaman' }}</h2>
            </div>

            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: #f5f5f5">
                    @if (Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Foto Profil"
                            class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle d-flex justify-content-center align-items-center"
                            style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.2);">
                            <i class="fa fa-user fa-lg"></i>
                        </div>
                    @endif
                    <span class="ms-2 d-none d-md-inline" style="color: #f5f5f5">Hallo,
                        {{ Str::limit(Auth::user()->name, 10) }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="profileDropdown">
                    <li class="dropdown-header-custom">
                        <div class="d-flex align-items-center">
                            @if (Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Foto Profil"
                                    class="rounded-circle me-2" width="50" height="50"
                                    style="object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex justify-content-center align-items-center me-2"
                                    style="width: 50px; height: 50px; background-color: #e9ecef;">
                                    <i class="fa fa-user fa-2x text-secondary"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                <small class="text-muted">{{ Auth::user()->email }}</small><br>
                                <small class="text-muted">{{ Auth::user()->role }}</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fa fa-user-edit me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Konten Utama dari setiap halaman --}}
        <div class="content">
            @yield('content')
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    document.body.classList.toggle('sidebar-toggled');
                });
            }

            const contentWrapper = document.getElementById('content-wrapper');
            if (contentWrapper) {
                contentWrapper.addEventListener('click', function() {
                    if (window.innerWidth <= 768 && document.body.classList.contains('sidebar-toggled')) {
                        document.body.classList.remove('sidebar-toggled');
                    }
                });
            }

            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Informasi',
                    text: '{{ session('info') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('duplicate_found'))
                @php
                    $duplicateData = session('duplicate_found');
                @endphp

                Swal.fire({
                    icon: 'error',
                    title: 'Dokumen Sudah Ada!',
                    // Pesan HTML dengan link yang mengarah ke halaman index
                    html: `Dokumen dengan nomor <strong>{{ $duplicateData['no_surat'] }}</strong> sudah ada di sistem.` +
                        `<br><br><a href="{{ $duplicateData['redirect_url'] }}" class="text-primary">Klik di sini untuk melihat dokumen</a>`,
                    confirmButtonText: 'OK'
                });
            @endif

            @php
                $allErrors = [];
                foreach ($errors->getBags() as $bag) {
                    $allErrors = array_merge($allErrors, $bag->all());
                }
                $errorList = implode('<br>', $allErrors);
            @endphp

            @if (!empty($allErrors))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Ada yang salah!',
                    html: '{!! $errorList !!}',
                });
            @endif
        });
    </script>


    @stack('scripts')

</body>

</html>
