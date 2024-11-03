<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UangKasResource\Pages;
use App\Filament\App\Resources\UangKasResource\RelationManagers;
use App\Models\Pengurus;
use App\Models\UangKas;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UangKasResource extends Resource
{
    protected static ?string $model = UangKas::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Kas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('nm_penginput')
                ->label('Nama Penginput')
                ->options(function () {
                    $bendahara = Pengurus::query()->where('role', auth()->user()->roles[0]->name)->where('nm_tingkatan', auth()->user()->detail)->where('dapukan', 'Bendahara')->pluck('nm_pengurus', 'nm_pengurus');
                    return $bendahara;
                })
                ->required(),
                Select::make('jenis_kas')
                ->label('Jenis Kas')
                ->options(['Pemasukan' => 'Pemasukan','Pengeluaran' => 'Pengeluaran', 'Saldo Awal' => 'Saldo Awal'])
                ->required(),
                TextInput::make('nominal')
                ->label('Nominal')
                ->placeholder('Masukkan nominal')
                ->numeric()
                ->required(),
                Textarea::make('keterangan')
                ->label('Keterangan')
                ->placeholder('Masukkan Keterangan')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $tahun = Carbon::now()->format('Y');

        $listTahun = [
            $tahun => $tahun,
            ($tahun-1) => ($tahun-1),
            ($tahun-2) => ($tahun-2),
        ];
        $listBulan = [
            1 => 'Januari', 
            2 => 'Februari', 
            3 => 'Maret', 
            4 => 'April', 
            5 => 'Mei', 
            6 => 'Juni', 
            7 => 'Juli', 
            8 => 'Agustus', 
            9 => 'September', 
            10 => 'Oktober', 
            11 => 'November', 
            12 => 'Desember'
        ];

        return $table
            ->columns([
                TextColumn::make('created_at')
                ->label('Tanggal')
                ->date('d-m-Y'),
                TextColumn::make('nm_penginput')
                ->label('Nama Penginput'),
                TextColumn::make('keterangan')
                ->label('Keterangan')
                ->searchable()
                ->wrap(),
                BadgeColumn::make('jenis_kas')
                ->color(fn (string $state): string => match ($state) {
                    'Saldo Awal' => 'warning',
                    'Pemasukan' => 'success',
                    'Pengeluaran' => 'danger',
                }),
                TextColumn::make('nominal')
                ->label('Nominal (Rp)')
                ->icon(function(UangKas $record): string {
                    $value = '';
                    switch ($record->jenis_kas) {
                        case 'Pemasukan' :
                            $value = 'heroicon-m-arrow-trending-up';
                            break;
                        case 'Pengeluaran':
                            $value = 'heroicon-m-arrow-trending-down';
                            break;
                        case 'Saldo Awal':
                            $value = 'heroicon-m-arrow-path';
                            break;
                    }
                    return $value;
                })
                ->iconColor(function (UangKas $record): string {
                    $value = '';
                    switch ($record->jenis_kas) {
                        case 'Pemasukan' :
                            $value = 'success';
                            break;
                        case 'Pengeluaran':
                            $value = 'danger';
                            break;
                        case 'Saldo Awal':
                            $value = 'warning';
                            break;
                    }
                    return $value;
                })
                ->formatStateUsing(fn (string $state): string => 'Rp. ' . number_format($state, 0))
                ->alignEnd()
                ->summarize([
                    Sum::make()
                    ->label('Total Pemasukan')
                    ->query(fn (Builder $query) => $query->where('jenis_kas', 'Pemasukan')),
                    Sum::make()
                    ->label('Total Pengeluaran')
                    ->query(fn (Builder $query) => $query->where('jenis_kas', 'Pengeluaran')),
                    Summarizer::make()
                    ->label('Total Akhir Kas')
                    ->using(function() {
                        $pemasukan = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('jenis_kas', 'Pemasukan')->sum('nominal');
                        $pengeluaran = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('jenis_kas', 'Pengeluaran')->sum('nominal');
                        $total = $pemasukan - $pengeluaran;
                        return 'Rp. ' . number_format($total);
                    }),
                ]
                ),
            ])
            ->filters([
                SelectFilter::make('tahun')
                ->options($listTahun),
                SelectFilter::make('bulan')
                ->options($listBulan),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada catatan kas')
            ->emptyStateDescription('Klik Tambah Data untuk menambah catatan keuangan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUangKas::route('/'),
        ];
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail);
    }
}
