<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Pages;

use App\Filament\App\Resources\LaporanKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanKegiatan extends EditRecord
{
    protected static string $resource = LaporanKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
