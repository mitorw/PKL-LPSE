<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom untuk surat_keluar
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->string('isi_surat_original')->nullable()->after('isi_surat')->comment('Path file asli yang di-upload');
        });

        // Tambahkan kolom untuk surat_masuk
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->string('file_surat_original')->nullable()->after('file_surat')->comment('Path file asli yang di-upload');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn('isi_surat_original');
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('file_surat_original');
        });
    }
};
