<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikasi extends Model
{
    protected $table = 'cv_sertifikasi';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'nama_sertifikasi',
        'penerbit',
        'nomor_sertifikat',
        'tanggal_terbit',
        'tanggal_kedaluwarsa',
        'sumber_data',
        'is_verified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_kedaluwarsa' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
