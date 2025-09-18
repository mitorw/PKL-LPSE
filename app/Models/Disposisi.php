<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposisi';
    protected $primaryKey = 'id_disposisi';

    protected $fillable = [
        'dis_bagian',
        'catatan',
        'instruksi'
    ];

    public function suratMasuks()
    {
        return $this->belongsToMany(SuratMasuk::class, 'surat_masuk_disposisi', 'id_disposisi', 'id_surat_masuk')
                    ->withPivot('catatan', 'instruksi')
                    ->withTimestamps();
    }
}
