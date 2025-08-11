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

    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'id_disposisi', 'id_disposisi');
    }
}
