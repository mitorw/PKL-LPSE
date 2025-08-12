@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-semibold mb-6">Laporan Inventarisasi Surat</h1>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-xl font-bold mb-4">Filters</h2>
    
    {{-- FORM YANG SUDAH DIPERBAIKI --}}
    <form id="filterForm" action="{{ route('laporan.surat') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="nomor_surat" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                <input type="text" name="nomor_surat" id="nomor_surat" value="{{ request('nomor_surat') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="dari_tanggal" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" id="dari_tanggal" value="{{ request('dari_tanggal') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="sampai_tanggal" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" id="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="jenis_surat" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
                <select name="jenis_surat" id="jenis_surat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all" {{ request('jenis_surat') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status Surat</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="Biasa" {{ request('status') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="Penting" {{ request('status') == 'Penting' ? 'selected' : '' }}>Penting</option>
                    <option value="Rahasia" {{ request('status') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                </select>
            </div>
        </div>
    
        <div class="mt-4 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-md shadow-md hover:bg-green-600">
                Filter
            </button>
            <a href="{{ route('laporan.surat') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white font-semibold rounded-md shadow-md hover:bg-gray-600">
                Reset
            </a>
            <button type="button" onclick="cetakLaporan()" class="ml-2 px-4 py-2 bg-red-500 text-white font-semibold rounded-md shadow-md hover:bg-red-600">
                Cetak PDF
            </button>
        </div>
    </form>
    {{-- AKHIR FORM YANG DIPERBAIKI --}}

    <script>
        function cetakLaporan() {
            const form = document.getElementById('filterForm');
            const url = '{{ route('laporan.cetak') }}';
            const queryString = new URLSearchParams(new FormData(form)).toString();
            window.location.href = `${url}?${queryString}`;
        }
    </script>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Daftar Surat</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('laporan.surat', array_merge(request()->query(), ['sort' => 'nomor_surat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Nomor Surat
                            @if(request('sort') == 'nomor_surat')
                                <i class="ml-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('laporan.surat', array_merge(request()->query(), ['sort' => 'tanggal_mulai', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Tanggal
                            @if(request('sort') == 'tanggal_mulai')
                                <i class="ml-1 fas fa-{{ request('direction') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase traAcking-wider">Jenis Surat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perihal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($laporanSurat as $Laporan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $Laporan->nomor_surat }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $Laporan->tanggal_mulai }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($Laporan->jenis_surat) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $Laporan->perihal }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($Laporan->status == 'Penting') bg-yellow-100 text-yellow-800
                            @elseif($Laporan->status == 'Rahasia') bg-red-100 text-red-800
                            @elseif($Laporan->status == ' Biasa') bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($Laporan->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data surat yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $laporanSurat->appends(request()->query())->links() }}
    </div>
</div>
@endsection