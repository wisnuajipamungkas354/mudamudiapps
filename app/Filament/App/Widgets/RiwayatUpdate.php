<?php

namespace App\Filament\App\Widgets;

use App\Models\Riwayat;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RiwayatUpdate extends BaseWidget
{

    protected static ?int $sort = 6;

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
            ->query(Riwayat::query()->latest())
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Tanggal')
                    ->since(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('nm_user')
                    ->label('User Sistem'),
                BadgeColumn::make('action')
                    ->label('Action')
                    ->color(fn (string $state): string => match ($state) {
                        'Apply' => 'success',
                        'Tambah' => 'primary',
                        'Edit' => 'warning'
                    })
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum Ada Riwayat Update')
            ->poll('300s');
    }
}
