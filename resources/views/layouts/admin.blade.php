<!DOCTYPE html>
<html>
<head>
    <title>Sistem Manajemen Surat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    {{-- Side Bar --}}
    <div class="sidebar">
        <a href="{{ route('dashboard') }}" class="text-center mb-4">Sistem Manajemen Surat</a>
        <a href="{{ route('surat_masuk.index') }}"><i class="fa fa-inbox" style="padding-top: 10px; padding-block: 10px"></i> Surat Masuk</a>
        <a href="{{ route('surat_keluar.index') }}"><i class="fa fa-paper-plane" style="padding-top: 10px; padding-block: 10px"></i> Surat Keluar</a>
        <a href="{{ route('laporan.surat') }}"><i class="fa fa-file-alt" style="padding-top: 10px; padding-block: 10px"></i> Laporan</a>
        <a href="#"><i class="fa fa-users-cog" style="padding-top: 10px; padding-block: 10px"></i> Manajemen Akun</a>
        <a href="#"><i class="fa fa-sign-out-alt" style="padding-top: 10px; padding-block: 10px"></i> Keluar</a>
    </div>

    {{-- Header bar --}}
    <div class="header-bar"
        style="background-color: #5c6bc0; color: white; height: 95px; margin-left: 240px; display: flex; align-items: center; padding: 0 20px;">
        <h2 class="mb-0">{{ $pageTitle ?? 'Halaman' }}</h2>
    </div>

    <div class="content" style="padding-top: 20px;">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
