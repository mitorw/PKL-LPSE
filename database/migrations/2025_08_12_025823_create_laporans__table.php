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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->string('jenis_surat');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('perihal');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('open');
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans_');
    }
};
