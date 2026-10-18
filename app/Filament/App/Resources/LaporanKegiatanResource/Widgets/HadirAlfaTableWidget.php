<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Presensi;
use App\Services\RekapKegiatanExportService;
use Filament\Forms\Components\Radio;
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
            ->headerActions([
                Tables\Actions\Action::make('export_hadir')
                    ->label('Export Hadir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Export Data Peserta Hadir')
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->laporan, 'Hadir', $data['format'])),

                Tables\Actions\Action::make('export_alfa')
                    ->label('Export Alfa')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('danger')
                    ->modalHeading('Export Data Peserta Alfa')
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->laporan, 'Alfa', $data['format'])),

                Tables\Actions\Action::make('export_semua')
                    ->label('Export Hadir & Alfa')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->modalHeading('Export Semua (Hadir & Alfa)')
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->laporan, null, $data['format'])),
            ])
            ->emptyStateHeading('Tidak ada data presensi')
            ->defaultPaginationPageOption(5);
    }
}
