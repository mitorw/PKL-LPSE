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
        'user_id',
        'file_surat',
        'file_surat_original',
    ];

    public function disposisis()
    {
        return $this->belongsToMany(Disposisi::class, 'surat_masuk_disposisi', 'id_surat_masuk', 'id_disposisi')
                    ->withPivot('catatan', 'instruksi')
                    ->withTimestamps();
    }
}
