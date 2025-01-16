<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LaporanKegiatanResource\Pages;
use App\Filament\App\Resources\LaporanKegiatanResource\RelationManagers;
use App\Models\LaporanKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanKegiatanResource extends Resource
{
    protected static ?string $model = LaporanKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Laporan Kegiatan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kegiatan.waktu_mulai')
                    ->label('Waktu Pelaksanaan')
                    ->dateTime('H:i d/m/Y'),
                TextColumn::make('kegiatan.nm_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable(),
                TextColumn::make('hadir')
                    ->label('Hadir')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function(LaporanKegiatan $record) {
                        $totalHadir = $record->hadir_l + $record->hadir_p;
                        $hadirPercent = round(($totalHadir / $record->total_peserta) * 100);
                        return $totalHadir . ' peserta / ' . $hadirPercent . '%';
                    }),
                TextColumn::make('izin')
                    ->label('Izin')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(function(LaporanKegiatan $record) {
                        $totalIzin = $record->izin_l + $record->izin_p;
                        $hadirPercent = round(($totalIzin / $record->total_peserta) * 100);
                        return $totalIzin . ' peserta / ' . $hadirPercent . '%';
                    }),
                TextColumn::make('alfa')
                    ->label('Alfa')
                    ->badge()
                    ->color('danger')
                    ->getStateUsing(function(LaporanKegiatan $record) {
                        $totalAlfa = $record->alfa_l + $record->alfa_p;
                        $hadirPercent = round(($totalAlfa / $record->total_peserta) * 100);
                        return $totalAlfa . ' peserta / ' . $hadirPercent . '%';
                    }),
                TextColumn::make('total_peserta')
                    ->label('Total Keseluruhan')
                    ->badge()
                    ->color('info')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('rekap')
                    ->label('Lihat Rekap')
                    ->color('warning')
                    ->icon('heroicon-o-rocket-launch')
                    ->url(fn(LaporanKegiatan $record) => '/laporan-kegiatans/' . $record->id . '/rekap-kegiatan'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Laporan Kegiatan');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKegiatans::route('/'),
            'create' => Pages\CreateLaporanKegiatan::route('/create'),
            'edit' => Pages\EditLaporanKegiatan::route('/{record}/edit'),
            'rekap-kegiatan' => Pages\RekapKegiatan::route('/{record}/rekap-kegiatan')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tingkatan_laporan', auth()->user()->roles[0]->name)->where('detail_tingkatan', auth()->user()->detail);
    }
}
