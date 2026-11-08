<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengalaman extends Model
{
    protected $table = 'cv_pengalaman';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'jenis',
        'posisi',
        'nama_institusi',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
