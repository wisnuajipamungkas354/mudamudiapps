<?php

namespace App\Filament\Resources\MudamudiResource\Pages;

use App\Filament\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateMudamudi extends CreateRecord
{
    protected static string $resource = MudamudiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Data Muda-Mudi';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Muda Mudi Berhasil Ditambahkan!';
    }
}
