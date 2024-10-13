<?php

namespace App\Filament\PengurusDaerah\Resources\MudamudiResource\Pages;

use App\Filament\Exports\MudamudiExporter;
use App\Filament\PengurusDaerah\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListMudamudis extends ListRecords
{
    protected static string $resource = MudamudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->label('Export')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->exporter(MudamudiExporter::class)
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Muda-Mudi';
    }
}
