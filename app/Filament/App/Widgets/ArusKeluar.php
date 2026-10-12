<?php

namespace App\Filament\App\Widgets;

use App\Models\ArusKeluar as ModelArusKeluar;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ArusKeluar extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ModelArusKeluar::query()->latest()
            )
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('jk')
                    ->label('Jk'),
                TextColumn::make('usia')
                    ->label('Usia'),
                BadgeColumn::make('keterangan')
                    ->label('Keterangan')
                    ->color(fn (string $state): string => match ($state) {
                        'Menikah' => 'success',
                        'Meninggal' => 'danger',
                        'Pindah Sambung Dalam Daerah' => 'warning',
                        'Pindah Sambung Keluar Daerah' => 'primary',
                    })
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum Ada Riwayat Arus Keluar')
            ->poll('300s');
    }
}
