<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keahlian extends Model
{
    protected $table = 'cv_keahlian';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'nama_keahlian',
        'tingkat',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
