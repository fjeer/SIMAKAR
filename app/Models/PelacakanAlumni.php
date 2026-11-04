<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pengguna',
    'tahun_lulus',
    'status_saat_ini',
    'sektor_industri',
    'nama_perusahaan',
    'jabatan',
    'publik_di_direktori',
])]
class PelacakanAlumni extends Model
{
    protected $table = 'pelacakan_alumni';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tahun_lulus' => 'integer',
            'publik_di_direktori' => 'boolean',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
