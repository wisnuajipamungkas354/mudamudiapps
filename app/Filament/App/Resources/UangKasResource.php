<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UangKasResource\Pages;
use App\Filament\App\Resources\UangKasResource\RelationManagers;
use App\Models\Pengurus;
use App\Models\UangKas;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

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
                    ->placeholder('Pilih nama bendahara')
                    ->options(function () {
                        $bendahara = Pengurus::query()->where('role', auth()->user()->roles[0]->name)->where('nm_tingkatan', auth()->user()->detail)->where('dapukan', 'Bendahara')->pluck('nm_pengurus', 'nm_pengurus');
                        return $bendahara;
                    })
                    ->required(),
                    DatePicker::make('tgl')
                    ->label('Tanggal')
                    ->displayFormat('d-m-Y')
                    ->native(false)
                    ->maxDate(Carbon::now())
                    ->default(Carbon::now())
                    ->suffixIcon('heroicon-m-calendar'),
                    Select::make('jenis_kas')
                    ->label('Jenis Kas')
                    ->options(['Pemasukan' => 'Pemasukan','Pengeluaran' => 'Pengeluaran'])
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
                TextColumn::make('tgl')
                ->label('Tanggal')
                ->date('d M Y'),
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
            ])
            ->filters([
                Filter::make('filter')
                ->form([
                    Select::make('tahun')
                    ->placeholder('Pilih tahun')
                    ->options($listTahun)
                    ->default(Carbon::now()->format('Y')),
                    Select::make('bulan')
                    ->placeholder('Pilih bulan')
                    ->options($listBulan)
                    ->default(Carbon::now()->format('m')),
                    Select::make('jenis_kas')
                    ->placeholder('Pilih Jenis Kas')
                    ->options(['Pemasukan' => 'Pemasukan', 'Pengeluaran' => 'Pengeluaran'])
                ])
                ->query(function (EloquentBuilder $query, array $data): EloquentBuilder {
                    return $query
                        ->when(
                            $data['tahun'] ?? null,
                            fn (EloquentBuilder $query, $tahun): EloquentBuilder => $query->where('tahun', '=', $tahun),
                        )
                        ->when(
                            $data['bulan'] ?? null,
                            fn (EloquentBuilder $query, $bulan): EloquentBuilder => $query->where('bulan', '=', $bulan),
                        )
                        ->when(
                            $data['jenis_kas'] ?? null,
                            fn (EloquentBuilder $query, $jenisKas): EloquentBuilder => $query->where('jenis_kas', '=', $jenisKas),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['tahun'] ?? null) {
                        $indicators['tahun'] = 'Tahun ' . $data['tahun'];
                    }
                    if ($data['bulan'] ?? null) {
                        $indicators['bulan'] = 'Bulan ' .$data['bulan'];
                    }

                    return $indicators;
                }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Data')
                    ->modalDescription('Apakah kamu yakin data kas ini akan dihapus ?')
                    ->modalSubmitActionLabel('Ya Hapus')
                    ->modalCancelActionLabel('Batal'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada catatan kas')
            ->emptyStateDescription('Klik Tambah Data untuk menambah catatan keuangan');
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
            'index' => Pages\ListUangKas::route('/'),
            'create' => Pages\CreateUangKas::route('/create'),
            'edit' => Pages\EditUangKas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->orderBy('tgl', 'desc');
    }
}
