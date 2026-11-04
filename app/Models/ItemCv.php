<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_portofolio_cv',
    'tipe',
    'judul',
    'institusi',
    'tanggal_mulai',
    'tanggal_selesai',
    'deskripsi',
    'sumber_api',
    'disembunyikan',
])]
class ItemCv extends Model
{
    protected $table = 'item_cv';

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
            'disembunyikan' => 'boolean',
        ];
    }

    public function portofolioCv(): BelongsTo
    {
        return $this->belongsTo(PortofolioCv::class, 'id_portofolio_cv');
    }
}
