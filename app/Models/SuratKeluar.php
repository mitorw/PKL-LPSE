<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $table = 'surat_keluar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_surat',
        'perihal',
        'tujuan',
        'tanggal',
        'dibuat_oleh',
        'keterangan',
        'klasifikasi',
        'isi_surat',
        'isi_surat_original',
    ];
}
