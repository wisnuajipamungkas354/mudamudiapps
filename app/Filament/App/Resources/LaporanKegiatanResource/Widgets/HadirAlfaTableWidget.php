<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Presensi;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class HadirAlfaTableWidget extends BaseWidget
{
    protected static ?string $heading = 'List Hadir & Alfa';
    protected int | string | array $columnSpan = 'full';

    public $laporan;

    public function table(Table $table): Table
    {
        $getQuery = Presensi::query();

        if($this->laporan->tingkatan_laporan == 'MM Daerah') {
            $id = Daerah::query()->where('nm_daerah', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '!=', 'Izin')->where('mudamudis.daerah_id', $id);
        } elseif($this->laporan->tingkatan_laporan == 'MM Desa') {
            $id = Desa::query()->where('nm_desa', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '!=', 'Izin')->where('mudamudis.desa_id', $id);
        } elseif($this->laporan->tingkatan_laporan == 'MM Kelompok') {
            $id = Kelompok::query()->where('nm_kelompok', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '!=', 'Izin')->where('mudamudis.kelompok_id', $id);
        }

        return $table
            ->query($getQuery)
            ->columns([
                TextColumn::make('mudamudi.kelompok.nm_kelompok')
                    ->label('Kelompok')
                    ->searchable(),
                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('jk') 
                    ->label('L/P'),
                BadgeColumn::make('keterangan')
                ->color(fn (string $state): string => match ($state) {
                    'Hadir' => 'success',
                    'Alfa' => 'danger',
                }),
                BadgeColumn::make('kedatangan')
                ->color(fn (string $state): string => match ($state) {
                    'In Time' => 'info',
                    'On Time' => 'success',
                    'Overtime' => 'warning',
                    'Tidak Datang' => 'danger',
                }),
            ])
            ->filters([
                SelectFilter::make('keterangan')
                    ->label('Keterangan')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Alfa' => 'Alfa',
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
            ->emptyStateHeading('Tidak ada data presensi')
            ->defaultPaginationPageOption(5);
    }
}
