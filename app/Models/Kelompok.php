<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function mudamudi()
    {
        return $this->hasMany(Mudamudi::class);
    }

    public function registrasi()
    {
        return $this->hasMany(Registrasi::class);
    }

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class);
    }
}
