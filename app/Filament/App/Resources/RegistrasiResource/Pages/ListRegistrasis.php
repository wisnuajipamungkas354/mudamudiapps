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
            Actions\Action::make('share')
                ->label('Share')
                ->icon('heroicon-o-share')
                ->color('success')
                ->openUrlInNewTab()
                ->url(fn (): string => RegistrasiResource::getShareWhatsappUrl())
                ->visible(fn (): bool => auth()->user()?->can('share_registrasi') ?? false),
        ];
    }


    public function getTitle(): string|Htmlable
    {
        return 'Data Registrasi';
    }
}
