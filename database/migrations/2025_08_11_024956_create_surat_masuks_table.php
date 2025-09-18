<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id('id_surat_masuk');
            $table->string('no_surat', 50);
            $table->string('asal_surat', 100);
            $table->date('tanggal_terima');
            $table->string('perihal', 255);
            $table->text('keterangan')->nullable();
            $table->enum('klasifikasi', ['Rahasia', 'Penting', 'Biasa', 'Segera']);
            $table->unsignedBigInteger('id_disposisi')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('id_disposisi')->references('id_disposisi')->on('disposisi')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
