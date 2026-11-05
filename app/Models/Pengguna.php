<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['sso_id', 'nama', 'email', 'peran', 'id_prodi'])]
class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';

    public function logAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class, 'id_pengguna');
    }

    public function portofolioCv(): HasMany
    {
        return $this->hasMany(PortofolioCv::class, 'id_pengguna');
    }

    public function pelacakanAlumni(): HasOne
    {
        return $this->hasOne(PelacakanAlumni::class, 'id_pengguna');
    }
}
