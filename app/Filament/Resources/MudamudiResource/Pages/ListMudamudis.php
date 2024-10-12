<?php

namespace App\Filament\Resources\MudamudiResource\Pages;

use App\Filament\Exports\MudamudiExporter;
use App\Filament\Resources\MudamudiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListMudamudis extends ListRecords
{
    protected static string $resource = MudamudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah')
                ->icon('heroicon-o-plus-circle'),
            Actions\ExportAction::make()->label('Excel')
                ->exporter(MudamudiExporter::class)
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Muda-Mudi';
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'SMP' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', 'Pelajar SMP')),
            'SMA' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', '=', 'Pelajar SMA')->orWhere('status', '=', 'Pelajar SMK')),
            'Mahasiswa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'LIKE', 'Mahasiswa %')->orWhere('status', 'LIKE', 'Kuliah %')),
            'Lepas Pelajar' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereNot('status', 'LIKE', 'Pelajar %')),
        ];
    }
}
