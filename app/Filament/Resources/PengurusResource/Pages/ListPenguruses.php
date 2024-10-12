<?php

namespace App\Filament\Resources\PengurusResource\Pages;

use App\Filament\Resources\PengurusResource;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPenguruses extends ListRecords
{
    protected static string $resource = PengurusResource::class;

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
        return 'Pengurus Muda-Mudi';
    }

    public function getTabs(): array
    {
        return [
            'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', 'MM Daerah')->where('nm_tingkatan', '=', 'Karawang Timur')),
            'Semua' => Tab::make(),
        ];
    }
}
