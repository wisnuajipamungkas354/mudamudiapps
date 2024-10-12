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

        $record = static::getModel()::create($data);

        return $record;
    }
}
