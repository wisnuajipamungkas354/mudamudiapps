<?php

namespace App\Filament\App\Resources\PengurusAppResource\Pages;

use App\Filament\App\Resources\PengurusAppResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreatePengurusApp extends CreateRecord
{
    protected static string $resource = PengurusAppResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Data Pengurus';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
