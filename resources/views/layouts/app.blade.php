<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Surat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar {
            background-color: #2c3e50; /* Warna biru gelap dari gambar */
        }
        .sidebar-item a {
            color: #ecf0f1;
        }
        .sidebar-item a:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <div class="sidebar w-64 h-screen fixed">
            <div class="p-6 text-white text-2xl font-bold">Sistem Manajemen Surat</div>
            <ul class="mt-8">
                <li class="sidebar-item p-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item p-4">
                    <a href="{{ route('laporan.surat') }}" class="flex items-center space-x-2">
                        <span>Laporan</span>
                    </a>
                </li>
                </ul>
        </div>

        <div class="ml-64 flex-1 p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>