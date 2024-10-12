<?php

namespace App\Filament\Widgets;

use App\Models\Riwayat;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RiwayatUpdateAdmin extends BaseWidget
{

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Riwayat Update';

    public function table(Table $table): Table
    {
        return $table
            ->query(Riwayat::query()->latest())
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Tanggal')
                    ->since(),
                TextColumn::make('nama')
                    ->label('Nama'),
                TextColumn::make('nm_user')
                    ->label('User Sistem'),
                BadgeColumn::make('action')
                    ->label('Action')
                    ->color(fn (string $state): string => match ($state) {
                        'Apply' => 'success',
                        'Tambah' => 'primary',
                        'Edit' => 'warning'
                    })
            ])->poll(null);
    }
}
