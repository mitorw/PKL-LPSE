<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id(); // id primary auto increment
            $table->string('nomor_surat', 50);
            $table->string('perihal', 255); // perihal surat
            $table->string('tujuan', 255);
            $table->date('tanggal');
            $table->string('dibuat_oleh', 50);
            $table->text('keterangan')->nullable();
            $table->enum('klasifikasi', ['biasa', 'penting', 'rahasia', 'segera'])->default('biasa');
            $table->string('isi_surat')->nullable(); // nama file lampiran (pdf, png, jpg)
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};

