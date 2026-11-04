<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_pengguna', 'judul', 'ringkasan_profesional', 'jenis_templat', 'file_terakhir_dibuat'])]
class PortofolioCv extends Model
{
    protected $table = 'portofolio_cv';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function itemCv(): HasMany
    {
        return $this->hasMany(ItemCv::class, 'id_portofolio_cv');
    }
}
