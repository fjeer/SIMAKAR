<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilPengguna extends Model
{
    protected $table = 'profil_pengguna';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'no_hp',
        'id_fakultas',
        'nama_fakultas',
        'nama_prodi',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'bio',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
