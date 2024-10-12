<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Riwayat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function daerah(): BelongsTo
    {
        return $this->belongsTo(Daerah::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}
