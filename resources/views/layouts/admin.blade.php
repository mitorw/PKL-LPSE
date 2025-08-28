<!DOCTYPE html>
<html>

<head>
    <title>Sistem Manajemen Surat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Croppie CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" />

    <style>
        body {
            background-color: #f5f5f5;
            /* Mencegah scroll horizontal saat sidebar transisi */
            overflow-x: hidden;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #3f51b5;
            color: white;
            position: fixed;
            padding-top: 5px;
            /* Menambahkan transisi untuk efek animasi yang mulus */
            transition: margin-left 0.3s ease-in-out;
            z-index: 1030;
            /* Pastikan sidebar di atas konten lain */
        }

        .sidebar a {
            color: white;
            display: flex;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            align-items: center; /* Menyejajarkan ikon dan teks secara vertikal di tengah */
            transition: background-color 0.2s transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        /* Memberi gaya pada ikon di dalam link */
        .sidebar a i {
            width: 40px;         /* Memberi lebar tetap agar semua teks lurus sejajar */
            margin-right: 8px;   /* Memberi sedikit jarak antara ikon dan teks */
            text-align: center;  /* Memastikan ikon berada di tengah area lebarnya */
            font-size: 1.1em;    /* Sedikit menyesuaikan ukuran ikon */
        }

        .sidebar a:hover {
            background-color: #ffffff33;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        /* Wrapper untuk konten utama (header + content) */
        #content-wrapper {
            margin-left: 240px;
            padding-top: 0;
            /* Header akan menangani padding atas */
            transition: margin-left 0.3s ease-in-out;
        }

        .content {
            padding: 20px;
        }

        .card {
            border-radius: 10px;
        }

        /* Header bar yang menempel */
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

        /* Tombol untuk toggle sidebar */
        #sidebarToggle {
            background: transparent;
            /* Latar belakang transparan */
            border: 1px solid rgba(255, 255, 255, 0.3);
            /* Border tipis semi-transparan */
            color: white;
            font-size: 20px;
            /* Sedikit diperkecil agar pas dengan padding */
            margin-right: 15px;
            padding: 6px 12px;
            /* Memberi ruang di dalam tombol */
            border-radius: 8px;
            /* Sudut yang sedikit melengkung */
            cursor: pointer;
            /* Mengubah kursor menjadi tangan saat di-hover */

            /* Menambahkan transisi untuk efek hover yang mulus */
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        /* Efek saat kursor mouse berada di atas tombol */
        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            /* Latar belakang sedikit menyala */
            border-color: rgba(255, 255, 255, 0.7);
            /* Border menjadi lebih jelas */
        }

        /* === KONDISI KETIKA SIDEBAR DISEMBUNYIKAN === */
        body.sidebar-toggled .sidebar {
            margin-left: -240px;
            /* Sembunyikan sidebar ke kiri */
        }

        body.sidebar-toggled #content-wrapper {
            margin-left: 0;
            /* Konten utama memakai lebar penuh */
        }


        /* === ATURAN RESPONSIVE UNTUK MOBILE === */
        @media (max-width: 768px) {

            /* Secara default, sembunyikan sidebar di mobile */
            .sidebar {
                margin-left: -240px;
            }

            #content-wrapper {
                margin-left: 0;
            }

            /* Saat di-toggle, tampilkan sidebar */
            body.sidebar-toggled .sidebar {
                margin-left: 0;
            }

            /* Di mobile, saat sidebar muncul, kita tidak ingin kontennya terdorong */
            /* Ini akan membuat sidebar muncul di atas konten (overlay) */
            body.sidebar-toggled #content-wrapper {
                margin-left: 0;
            }
        }


        .profile-link {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .profile-link:hover {
            color: #ffffff33;
        }
    </style>
</head>

<body>
    {{-- Side Bar --}}
    <div class="sidebar">
        <div style="text-align: center; padding: 10px 0; border-bottom: 4px solid #3f51b5;">
            <h4>Sistem Inventaris Surat</h4>
        </div>
        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="{{ route('surat_masuk.index') }}"><i class="fa fa-inbox"></i> Surat Masuk</a>
        <a href="{{ route('surat_keluar.index') }}"><i class="fa fa-paper-plane"></i> Surat Keluar</a>
        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('manajemen_akun.index') }}"><i class="fa fa-users-cog"
                        ></i> Manajemen
                    Akun</a>
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
            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                 alt="Foto Profil" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
        @else
            <i class="fa fa-user-circle fa-2x text-secondary"></i>
        @endif
        <span class="ms-2" style="color: #f5f5f5">Hallo, {{ Auth::user()->name }}</span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
        <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="fa fa-user me-2"></i> Profile
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
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

    <!-- Croppie JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>


    {{-- SCRIPT BARU UNTUK FUNGSI TOGGLE SIDEBAR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebarToggle = document.getElementById('sidebarToggle');
            const contentWrapper = document.getElementById('content-wrapper');

            // Event listener untuk Tombol Hamburger
            sidebarToggle.addEventListener('click', function(event) {
                // HENTIKAN event agar tidak "menggelembung" ke content-wrapper
                event.stopPropagation();

                // Lakukan aksi seperti biasa
                document.body.classList.toggle('sidebar-toggled');
            });

            // Event listener untuk menutup sidebar saat klik di konten
            contentWrapper.addEventListener('click', function() {
                const isSidebarToggled = document.body.classList.contains('sidebar-toggled');
                const isMobile = window.innerWidth <= 768;

                if (isSidebarToggled && isMobile) {
                    document.body.classList.remove('sidebar-toggled');
                }
            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
