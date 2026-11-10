<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ProfilPengguna extends Model
{
    use LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName): string => 'Data '.class_basename($this).' telah di-'.$eventName);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
