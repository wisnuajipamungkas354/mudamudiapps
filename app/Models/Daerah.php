<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Daerah extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function desa(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    public function mudamudi(): HasMany
    {
        return $this->hasMany(Mudamudi::class);
    }
    public function registrasi(): HasMany
    {
        return $this->hasMany(Registrasi::class);
    }
    public function riwayat(): HasMany
    {
        return $this->hasMany(Riwayat::class);
    }
    public function aruskeluar(): HasMany
    {
        return $this->hasMany(ArusKeluar::class);
    }
}
