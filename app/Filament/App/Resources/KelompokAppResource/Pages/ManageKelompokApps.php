<?php

namespace App\Filament\App\Resources\KelompokAppResource\Pages;

use App\Filament\App\Resources\KelompokAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageKelompokApps extends ManageRecords
{
    protected static string $resource = KelompokAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Kelompok';
    }
}
