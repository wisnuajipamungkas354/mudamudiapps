<?php

namespace App\Filament\App\Resources\UangKasResource\Pages;

use App\Filament\App\Resources\UangKasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUangKas extends EditRecord
{
    protected static string $resource = UangKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
