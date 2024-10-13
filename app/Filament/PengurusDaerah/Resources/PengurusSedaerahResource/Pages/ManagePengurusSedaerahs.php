<?php

namespace App\Filament\PengurusDaerah\Resources\PengurusSedaerahResource\Pages;

use App\Filament\Exports\PengurusSedaerahExporter;
use App\Filament\PengurusDaerah\Resources\PengurusSedaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManagePengurusSedaerahs extends ManageRecords
{
    protected static string $resource = PengurusSedaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data'),
            Actions\ExportAction::make()
                ->label('Export')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->exporter(PengurusSedaerahExporter::class)
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Pengurus Se-Daerah';
    }
}
