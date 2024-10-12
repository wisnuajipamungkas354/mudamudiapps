<?php

namespace App\Filament\App\Resources\PengurusAppResource\Pages;

use App\Filament\App\Resources\PengurusAppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengurusApp extends EditRecord
{
    protected static string $resource = PengurusAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
