<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk';
    protected $primaryKey = 'id_surat_masuk';

    protected $fillable = [
        'no_surat',
        'asal_surat',
        'tanggal_terima',
        'perihal',
        'keterangan',
        'klasifikasi',
        'id_disposisi',
        'user_id',
        'file_surat',
        'file_surat_original',
    ];

    public function disposisi()
    {
        return $this->belongsTo(Disposisi::class, 'id_disposisi', 'id_disposisi');
    }
}
