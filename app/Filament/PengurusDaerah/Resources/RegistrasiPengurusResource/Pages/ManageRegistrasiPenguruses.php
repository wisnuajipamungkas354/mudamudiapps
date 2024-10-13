<?php

namespace App\Filament\PengurusDaerah\Resources\RegistrasiPengurusResource\Pages;

use App\Filament\PengurusDaerah\Resources\RegistrasiPengurusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageRegistrasiPenguruses extends ManageRecords
{
    protected static string $resource = RegistrasiPengurusResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Data Registrasi Pengurus';
    }

}
