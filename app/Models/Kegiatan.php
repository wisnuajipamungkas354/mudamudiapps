<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $incrementing = false;

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
        $dateYearMonth = date('dym'); // Dua digit tanggal tahun & bulan (contoh: 312411)
        $uniqueStr = self::generateRandomString(5);

        // Ambil ID unik yang belum dipakai untuk bulan dan tahun yang sama
        $getLastId = self::latest('id')->value('id');
        $prefixId = (int)substr($getLastId, 0, 6);
        $uniqueId = 1;

        // Jika id terakhir sama dengan bulan dan tahun hari ini maka id terakhir + 1
        if($prefixId == $dateYearMonth) { 
            $lastIdNumber = (int)substr($getLastId, 6, 3);
            $uniqueId= $lastIdNumber + 1;
        }

        // Format ID unik menjadi tiga digit
        $uniqueIdFormatted = str_pad($uniqueId, 3, '0', STR_PAD_LEFT);

        // Gabungkan tahun, bulan, dan ID unik untuk membuat ID lengkap
        return strval($dateYearMonth) . strval($uniqueIdFormatted) . $uniqueStr;
    }

    public static function generateRandomString($length) {
        return substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }
}
