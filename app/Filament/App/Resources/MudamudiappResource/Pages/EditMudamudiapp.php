<?php

namespace App\Filament\App\Resources\MudamudiappResource\Pages;

use App\Filament\App\Resources\MudamudiappResource;
use App\Models\Riwayat;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EditMudamudiapp extends EditRecord
{
    protected static string $resource = MudamudiappResource::class;
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['usia'] = Carbon::parse($data['tgl_lahir'])->age;
        $record->update($data);

        return $record;
    }

    protected function afterSave()
    {
        $record = $this->record;
        $riwayat = new Riwayat();
        $riwayat->daerah_id = $record['daerah_id'];
        $riwayat->desa_id = $record['desa_id'];
        $riwayat->kelompok_id = $record['kelompok_id'];
        $riwayat->nama = $record['nama'];
        $riwayat->nm_user = auth()->user()->name;
        $riwayat->action = 'Edit';

        // Menyimpan data riwayat
        $riwayat->save();

        // Generate Ulang QR-Code
        $generateQr = QrCode::format('png')->style('round')->merge('/public/img/logo.png', .25)->size(300)->margin(1)->errorCorrection('H')->generate($record->id . ' | ' . $record->nama);
        Storage::disk('public')->put('qr-images/mudamudi/' . $record->id . '.png', $generateQr);
    }
}
