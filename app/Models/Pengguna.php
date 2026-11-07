<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';

    /**
     * @var list<string>
     */
    protected $fillable = ['sso_id', 'nama', 'email', 'peran', 'id_prodi'];

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

    public function profil(): HasOne
    {
        return $this->hasOne(ProfilPengguna::class);
    }
}
