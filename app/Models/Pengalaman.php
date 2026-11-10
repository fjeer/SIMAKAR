<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Pengalaman extends Model
{
    use LogsActivity;

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
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_verified' => 'boolean',
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
