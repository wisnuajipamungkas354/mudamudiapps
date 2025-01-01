<?php

namespace Database\Seeders;

use App\Models\Mudamudi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreateQrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $getDataMM = Mudamudi::all();
        foreach($getDataMM as $data) {
            $generateQr = QrCode::format('png')->merge('/public/img/logo.png', .25)->size(300)->margin(1)->errorCorrection('H')->generate($data->id . ' | ' . $data->nama);
            Storage::disk('public')->put('qr-images/mudamudi/' . $data->id . '.png', $generateQr);
        }
    }
}
