<?php

namespace App\Filament\PengurusDaerah\Resources\MudamudiResource\Pages;

use App\Filament\PengurusDaerah\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMudamudi extends EditRecord
{
    protected static string $resource = MudamudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
