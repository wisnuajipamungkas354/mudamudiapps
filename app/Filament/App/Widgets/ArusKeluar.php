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
                ModelArusKeluar::query()
                    ->when($daerahId, fn ($q) => $q->where('daerah_id', $daerahId))
                    ->latest()
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
