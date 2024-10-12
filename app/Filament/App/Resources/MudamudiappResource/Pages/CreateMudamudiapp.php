<?php

namespace App\Filament\App\Resources\MudamudiappResource\Pages;

use App\Filament\App\Resources\MudamudiappResource;
use App\Models\Riwayat;
use Filament\Actions;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateMudamudiapp extends CreateRecord
{
    protected static string $resource = MudamudiappResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Data Muda-Mudi';
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Hitung Usia
        $data['usia'] = Carbon::parse($data['tgl_lahir'])->age;
        
        // Masukkan data ke Muda Mudi
        $record = static::getModel()::create($data);

        // Membuat Instansiasi Riwayat
        $riwayat = new Riwayat();
        $riwayat->daerah_id = $data['daerah_id'];
        $riwayat->desa_id = $data['desa_id'];
        $riwayat->kelompok_id = $data['kelompok_id'];
        $riwayat->nama = $data['nama'];
        $riwayat->nm_user = auth()->user()->name;
        $riwayat->action = 'Tambah';

        // Menyimpan data riwayat
        $riwayat->save();

        return $record;
    }
}
