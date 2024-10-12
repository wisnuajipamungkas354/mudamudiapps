<?php

namespace App\Filament\Resources\MudamudiResource\Pages;

use App\Filament\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditMudamudi extends EditRecord
{
    protected static string $resource = MudamudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Edit Data Muda-Mudi';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Muda Mudi Berhasil Diedit!';
    }
}
