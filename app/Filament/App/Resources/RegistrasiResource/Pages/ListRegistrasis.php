<?php

namespace App\Filament\App\Resources\RegistrasiResource\Pages;

use App\Filament\App\Resources\RegistrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListRegistrasis extends ListRecords
{
    protected static string $resource = RegistrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }


    public function getTitle(): string|Htmlable
    {
        return 'Data Registrasi';
    }
}
