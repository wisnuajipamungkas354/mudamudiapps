<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mudamudi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Event "creating" untuk otomatis mengisi unique_id
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         // Panggil fungsi untuk generate unique_id
    //         $model->nig = self::generateUniqueId();
    //     });
    // }

    // Fungsi untuk generate ID unik
    // public static function generateUniqueId()
    // {
    //     // Ambil tahun dan bulan saat ini
    //     $year = date('y'); // Dua digit tahun (contoh: 24 untuk 2024)
    //     $month = date('m'); // Dua digit bulan (contoh: 09 untuk September)

    //     // Ambil ID unik yang belum dipakai untuk bulan dan tahun yang sama
    //     $lastId = self::whereYear('created_at', date('Y'))
    //                   ->whereMonth('created_at', date('m'))
    //                   ->max('unique_id');

    //     // Jika tidak ada ID yang ditemukan, maka set ID awal menjadi 1
    //     if (!$lastId) {
    //         $uniqueId = 1;
    //     } else {
    //         // Ambil tiga digit terakhir dari unique_id yang ada dan tambahkan 1
    //         $lastIdNumber = (int)substr($lastId, 4); // Ambil tiga digit ID unik
    //         $uniqueId = $lastIdNumber + 1;
    //     }

    //     // Format ID unik menjadi tiga digit
    //     $uniqueIdFormatted = str_pad($uniqueId, 3, '0', STR_PAD_LEFT);

    //     // Gabungkan tahun, bulan, dan ID unik untuk membuat ID lengkap
    //     return $year . $month . $uniqueIdFormatted;
    // }

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
