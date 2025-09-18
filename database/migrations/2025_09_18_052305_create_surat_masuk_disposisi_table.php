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
        Schema::create('surat_masuk_disposisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_surat_masuk');
            $table->unsignedBigInteger('id_disposisi');
            $table->text('catatan')->nullable();
            $table->string('instruksi', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('id_surat_masuk')->references('id_surat_masuk')->on('surat_masuk')->onDelete('cascade');
            $table->foreign('id_disposisi')->references('id_disposisi')->on('disposisi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk_disposisi');
    }
};
