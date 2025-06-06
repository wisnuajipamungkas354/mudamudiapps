<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Presensi;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IzinTableWidget extends BaseWidget
{
    protected static ?string $heading = 'List Izin';

    public $laporan;

    public function table(Table $table): Table
    {

        $getQuery = Presensi::query();

        if($this->laporan->tingkatan_laporan == 'MM Daerah') {
            $id = Daerah::query()->where('nm_daerah', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '=', 'Izin')->where('mudamudis.daerah_id', $id);
        } elseif($this->laporan->tingkatan_laporan == 'MM Desa') {
            $id = Desa::query()->where('nm_desa', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '=', 'Izin')->where('mudamudis.desa_id', $id);
        } elseif($this->laporan->tingkatan_laporan == 'MM Kelompok') {
            $id = Kelompok::query()->where('nm_kelompok', $this->laporan->detail_tingkatan)->value('id');
            $getQuery->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', '=', $this->laporan->kegiatan_id)->where('presensis.keterangan', '=', 'Izin')->where('mudamudis.kelompok_id', $id);
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
                BadgeColumn::make('kategori_izin')
                ->color(fn (string $state): string => match ($state) {
                    'Sakit' => 'warning',
                    'Kerja' => 'success',
                    'Kuliah' => 'info',
                    'Acara Sekolah' => 'gray',
                    'Acara Keluarga' => 'bg-slate-300',
                    'Acara Mendesak' => 'bg-emerald-500'
                }),
                TextColumn::make('ket_izin')
                    ->label('Keterangan Izin')
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada yang izin');
    }
}
