<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use Illuminate\Database\Seeder;

class DisposisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambahkan data bagian disposisi jika belum ada
        $bagian = [
            'Bagian Advokasi dan Pembinaan',
            'Bagian Pengelolaan Pengadaan Barang dan Jasa',
            'Bagian Layanan Pengadaan Secara Elektronik',
        ];

        foreach ($bagian as $nama) {
            // Cek apakah bagian sudah ada
            $exists = Disposisi::where('dis_bagian', $nama)->exists();

            // Jika belum ada, tambahkan
            if (!$exists) {
                Disposisi::create(['dis_bagian' => $nama]);
            }
        }
    }
}
