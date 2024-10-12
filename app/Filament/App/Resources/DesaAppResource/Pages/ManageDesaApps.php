<?php

namespace App\Filament\App\Resources\DesaAppResource\Pages;

use App\Filament\App\Resources\DesaAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageDesaApps extends ManageRecords
{
    protected static string $resource = DesaAppResource::class;

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
        return 'Data Desa';
    }
}
