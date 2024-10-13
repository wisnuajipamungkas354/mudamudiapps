<?php

namespace App\Filament\PengurusDaerah\Resources\DapukanResource\Pages;

use App\Filament\PengurusDaerah\Resources\DapukanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageDapukans extends ManageRecords
{
    protected static string $resource = DapukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Dapukan'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Dapukan';
    }
}
