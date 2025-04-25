<?php

namespace App\Filament\App\Resources\KegiatanResource\Pages;

use App\Filament\App\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateKegiatan extends CreateRecord
{
    protected static string $resource = KegiatanResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Buat Kegiatan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['tingkatan_kegiatan'] = auth()->user()->roles[0]->name;
        $data['detail_tingkatan'] = auth()->user()->detail;
        
        if($data['filter_peserta'] === 'all') {
            $data['kategori_peserta'] = ['all'];
        } elseif($data['filter_peserta'] === 'age') {
            $data['kategori_peserta'] =  ['age', $data['start'], $data['until']];
            unset($data['start']);
            unset($data['until']);
        } else {
            $data['kategori_peserta'] = ['category', ...$data['kategori_peserta']];
        }

        $data['kode_kegiatan'] = rand(1000, 9999);
        unset($data['filter_peserta']);
        $record = static::getModel()::create($data);

        return $record;
    }
}
