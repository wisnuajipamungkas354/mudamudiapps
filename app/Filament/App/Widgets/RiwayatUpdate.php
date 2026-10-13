<?php

namespace App\Filament\App\Widgets;

use App\Models\Riwayat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RiwayatUpdate extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected function getDaerahId(): ?int
    {
        $user = auth()->user();
        $role = $user->getAppRoleName();

        if ($role === 'MM Daerah') {
            return \App\Models\Daerah::where('nm_daerah', $user->detail)->value('id');
        } elseif ($role === 'MM Desa') {
            return \App\Models\Desa::where('nm_desa', $user->detail)->value('daerah_id');
        } elseif ($role === 'MM Kelompok') {
            $desaId = \App\Models\Kelompok::where('nm_kelompok', $user->detail)->value('desa_id');
            return \App\Models\Desa::where('id', $desaId)->value('daerah_id');
        }

        return null;
    }

    public function table(Table $table): Table
    {
        $daerahId = $this->getDaerahId();

        return $table
            ->query(
                Riwayat::query()
                    ->when($daerahId, fn ($q) => $q->where('daerah_id', $daerahId))
                    ->latest()
            )
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
