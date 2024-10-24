<?php

namespace App\Filament\App\Widgets;

use App\Models\ArusKeluar as ModelArusKeluar;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ArusKeluar extends BaseWidget
{
    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        $role = Auth::user()->roles;
        if ($role[0]->name == 'MM Kelompok') {
            return false;
        } else {
            return true;
        }
    }

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
