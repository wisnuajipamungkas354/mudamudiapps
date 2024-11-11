<?php

namespace App\Filament\App\Resources\UangKasResource\Pages;

use App\Filament\App\Resources\UangKasResource;
use App\Filament\App\Resources\UangKasResource\Widgets\PemasukanKas;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListUangKas extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = UangKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('Tambah Data')
            ->label('Tambah Data'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PemasukanKas::class,
        ];
    }
}
