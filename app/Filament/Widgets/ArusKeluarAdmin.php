<?php

namespace App\Filament\Widgets;

use App\Models\ArusKeluar;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ArusKeluarAdmin extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Arus Keluar';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ArusKeluar::query()->latest()
            )
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('nama')
                    ->label('Nama'),
                TextColumn::make('jk')
                    ->label('L/P'),
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
            ])->poll(null);
    }
}
