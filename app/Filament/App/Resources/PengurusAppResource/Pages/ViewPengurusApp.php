<?php

namespace App\Filament\App\Resources\PengurusAppResource\Pages;

use App\Filament\App\Resources\PengurusAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengurusApp extends ViewRecord
{
    protected static string $resource = PengurusAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
