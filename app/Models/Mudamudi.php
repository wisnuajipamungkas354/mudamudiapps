<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Mudamudi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $incrementing = false;

    // Event "creating" untuk otomatis mengisi pengkodean id
    protected static function booted()
    {
        static::creating(function ($model) {
            // Panggil fungsi untuk generate id
            $model->id = self::generateUniqueId($model);
        });
    }

    // Fungsi untuk generate ID unik
    public static function generateUniqueId($model)
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

        // Format ID unik menjadi empat digit
        $uniqueIdFormatted = str_pad($uniqueId, 4, '0', STR_PAD_LEFT);

        // Gabungkan tahun, bulan, dan ID unik untuk membuat ID lengkap
        $finalId = $yearMonth . $uniqueIdFormatted;

        // Create QR-Code Images
        $generateQr = QrCode::format('png')->style('round')->merge('/public/img/logo.png', .25)->size(300)->margin(1)->errorCorrection('H')->generate($finalId . ' | ' . $model->nama);
        Storage::disk('public')->put('qr-images/mudamudi/' . $finalId . '.png', $generateQr);

        return $finalId;
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
