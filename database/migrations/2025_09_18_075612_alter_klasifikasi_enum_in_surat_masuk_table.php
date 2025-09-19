<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE surat_masuk MODIFY klasifikasi ENUM('Rahasia','Penting','Biasa','Segera') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE surat_masuk MODIFY klasifikasi ENUM('Rahasia','Penting','Biasa') NOT NULL");
    }
};

