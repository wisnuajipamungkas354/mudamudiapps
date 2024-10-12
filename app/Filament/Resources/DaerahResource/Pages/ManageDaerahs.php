<?php

namespace App\Filament\Resources\DaerahResource\Pages;

use App\Filament\Resources\DaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageDaerahs extends ManageRecords
{
    protected static string $resource = DaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Daerah';
    }
}
