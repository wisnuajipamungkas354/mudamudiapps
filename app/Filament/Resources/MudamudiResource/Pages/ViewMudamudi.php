<?php

namespace App\Filament\Resources\MudamudiResource\Pages;

use App\Filament\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMudamudi extends ViewRecord
{
    protected static string $resource = MudamudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function getTitle(): string|Htmlable
    {
        return 'Detail Data Muda-Mudi';
    }
}
