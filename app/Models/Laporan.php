<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'jenis_surat',
        'tanggal_mulai',
        'tanggal_selesai',
        'perihal',
        'deskripsi',
        'status',
        'pengirim',
        'penerima',
    ];
}