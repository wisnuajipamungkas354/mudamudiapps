<?php

namespace App\Filament\App\Resources\KegiatanResource\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class PresensiTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Data Presensi';

    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
    
    public function table(Table $table): Table
    {
        $id = $this->filters['kegiatan'];
        $sesi = intval($this->filters['sesi']);
        $data = null;
        if ($id != null && $sesi == 0) {
            $data = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1);
        } elseif ($id != null && $sesi != 0) {
            $data = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi);
        } else {
            $data = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1);
        }

        return $table
            ->query($data)
            ->columns([
                TextColumn::make('mudamudi.kelompok.nm_kelompok')
                    ->searchable(),
                TextColumn::make('mudamudi.nama')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('mudamudi.jk')
                    ->label('L/P'),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Alfa' => 'danger',
                    }),
                TextColumn::make('updated_at')
                    ->label('Jam')
                    ->formatStateUsing(fn(Presensi $record, string $state) => $record->keterangan == 'Hadir' ? Carbon::parse($state)->format('H:i') : '-')
                    ->sortable(),
                TextColumn::make('kedatangan')
                    ->label('Kedatangan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'In Time' => 'primary',
                        'On Time' => 'success',
                        'Overtime' => 'warning',
                        'Tidak Datang' => 'danger',
                    })
            ])
            ->filters([
                SelectFilter::make('keterangan')
                    ->label('Keterangan')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa'
                    ]),
                SelectFilter::make('kedatangan')
                    ->label('Kedatangan')
                    ->options([
                        'In Time' => 'In Time',
                        'On Time' => 'On Time',
                        'Overtime' => 'Overtime',
                        'Tidak Datang' => 'Tidak Datang',
                    ]),
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak Ada Data');
    }
}
