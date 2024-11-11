<?php

namespace App\Filament\App\Resources\UangKasResource\Pages;

use App\Filament\App\Resources\UangKasResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateUangKas extends CreateRecord
{
    protected static string $resource = UangKasResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Data Kas';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['role'] = auth()->user()->roles[0]->name;
        $data['tingkatan'] = auth()->user()->detail;
        $data['tahun'] = Carbon::parse($data['tgl'])->format('Y');
        $data['bulan'] = Carbon::parse($data['tgl'])->format('m');

        $record = static::getModel()::create($data);
        return $record;
    }
}
