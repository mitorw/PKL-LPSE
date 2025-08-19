<!DOCTYPE html>
<html>

<head>
    <title>Sistem Manajemen Surat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f5f5f5;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #3f51b5;
            color: white;
            position: fixed;
            padding-top:5px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .sidebar h3 {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }

        .sidebar h3:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .content {
            margin-left: 240px;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
        }

        /* CSS untuk header bar yang baru */
        .header-bar {
            background-color: #5c6bc0;
            color: white;
            height: 95px;
            margin-left: 240px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Menjaga agar konten terbagi rata */
            padding: 0 20px;
        }

        /* Gaya untuk tautan profil */
        .profile-link {
            color: white;
            /* Mengatur warna ikon */
            text-decoration: none;
            /* Menghapus garis bawah */
            transition: color 0.3s;
            /* Efek transisi saat hover */
        }

        .profile-link:hover {
            color: #f1f1f1;
            /* Mengubah warna saat di-hover */
        }
    </style>
</head>

<body>
    {{-- Side Bar --}}
    <div class="sidebar">
        <a href="{{ route('dashboard') }}" class="mb-4 text-center" style="font-size: 25px; font-weight: bold">Sistem Inventaris Surat</a>
        <a href="{{ route('surat_masuk.index') }}"><i class="fa fa-inbox"
                style="padding-top: 10px; padding-block: 10px"></i> Surat Masuk</a>
        <a href="{{ route('surat_keluar.index') }}"><i class="fa fa-paper-plane"
                style="padding-top: 10px; padding-block: 10px"></i> Surat Keluar</a>
        <a href="{{ route('laporan.surat') }}"><i class="fa fa-file-alt"
                style="padding-top: 10px; padding-block: 10px"></i> Laporan</a>
        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('manajemen_akun.index') }}"><i class="fa fa-users-cog"
                        style="padding-top: 10px; padding-block: 10px"></i> Manajemen
                    Akun</a>
            @endif
        @endauth

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="fa fa-sign-out-alt" style="padding-top: 10px; padding-block: 10px"></i> Log Out
            </a>
        </form>
    </div>

    {{-- Header bar --}}
    <div class="header-bar">
        <h2 class="mb-0">{{ $pageTitle ?? 'Halaman' }}</h2>

        {{-- Profile --}}
        <a href="{{ route('profile.edit') }}" class="profile-link">
            <i class="fa fa-user-circle fa-2x"></i>
        </a>
    </div>

    <div class="content" style="padding-top: 20px;">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
