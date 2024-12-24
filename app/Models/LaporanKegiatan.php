<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKegiatan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
    
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }
}
