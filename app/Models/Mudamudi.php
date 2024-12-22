<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mudamudi extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Event "creating" untuk otomatis mengisi pengkodean id
    protected static function booted()
    {
        static::creating(function ($model) {
            // Panggil fungsi untuk generate id
            $model->id = self::generateUniqueId();
        });
    }

    // Fungsi untuk generate ID unik
    public static function generateUniqueId()
    {
        // Ambil tahun dan bulan saat ini
        $yearMonth = date('ym', mktime(24,0,0,date('m'),0,date('y'))); // Dua digit tahun & bulan (contoh: 2411)

        // Ambil ID unik yang belum dipakai untuk bulan dan tahun yang sama
        $getLastId = self::latest('id')->value('id');
        $prefixId = substr($getLastId, 0, 4);
        $uniqueId = 1;

        // Jika id terakhir sama dengan bulan dan tahun hari ini maka id terakhir + 1
        if($prefixId == $yearMonth) { 
            $lastIdNumber = (int)substr($getLastId, 4);
            $uniqueId= $lastIdNumber + 1;
        }

        // Format ID unik menjadi tiga digit
        $uniqueIdFormatted = str_pad($uniqueId, 3, '0', STR_PAD_LEFT);

        // Gabungkan tahun, bulan, dan ID unik untuk membuat ID lengkap
        return $yearMonth . $uniqueIdFormatted;
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
