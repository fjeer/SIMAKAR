<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidikan extends Model
{
    protected $table = 'cv_pendidikan';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'jenjang',
        'nama_institusi',
        'jurusan',
        'tahun_mulai',
        'tahun_selesai',
        'nilai_akhir',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
