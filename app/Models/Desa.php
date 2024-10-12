<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function daerah()
    {
        return $this->belongsTo(Daerah::class);
    }

    public function kelompok()
    {
        return $this->hasMany(Kelompok::class);
    }

    public function mudamudi()
    {
        return $this->hasMany(Mudamudi::class);
    }

    public function registrasi()
    {
        return $this->hasMany(Registrasi::class);
    }
}
