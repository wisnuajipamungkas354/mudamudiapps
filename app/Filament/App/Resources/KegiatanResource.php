<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\KegiatanResource\Pages;
use App\Filament\App\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use App\Models\SesiKegiatan;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Kegiatan';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
       return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nm_kegiatan')
                            ->label('Judul Kegiatan')
                            ->placeholder('Masukkan Judul Kegiatan')
                            ->required(),
                        Forms\Components\TextInput::make('tempat_kegiatan')
                            ->label('Tempat Kegiatan')
                            ->placeholder('Masukkan Tempat Kegiatan')
                            ->required(),
                        Forms\Components\DateTimePicker::make('waktu_pelaksanaan')
                            ->label('Waktu Pelaksanaan')
                            ->placeholder('Masukkan Waktu Pelaksanaan')
                            ->displayFormat('d/m/Y')
                            ->seconds(false)
                            ->required(),
                        Forms\Components\Select::make('asal_data_peserta')
                            ->label('Asal Data Peserta')
                            ->options(['Database Muda-Mudi' => 'Database Muda-Mudi'])
                            ->required(),
                        Forms\Components\Select::make('kategori_peserta')
                            ->label('Kategori Peserta')
                            ->options([
                                'Semua Muda-Mudi' => 'Semua Muda-mudi',
                                'Pelajar SMP' => 'Pelajar SMP',
                                'Pelajar SMA/K' => 'Pelajar SMA/K',
                                'Pelajar SMP & SMA/K' => 'Pelajar SMP & SMA/K',
                                'Mahasiswa' => 'Mahasiswa',
                                'Lepas Pelajar' => 'Lepas Pelajar',
                                'Keputrian' => 'Keputrian',
                            ])
                            ->live()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Checkbox::make('is_sesi')
                            ->live()
                            ->label('Apakah kegiatan ini memiliki lebih dari satu kali sesi presensi ?'),
                        Forms\Components\TextInput::make('jml_sesi')
                            ->label('Jumlah Sesi')
                            ->placeholder('Masukkan Jumlah Sesi (Angka Saja)')
                            ->numeric()
                            ->maxValue(24)
                            ->minValue(2)
                            ->visible(fn(Get $get) => $get('is_sesi') == true ? true : false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('waktu_pelaksanaan')
                    ->label('Waktu')
                    ->dateTime('H:i d/m/Y'),
                Tables\Columns\TextColumn::make('nm_kegiatan')
                    ->label('Judul Kegiatan'),
                Tables\Columns\TextColumn::make('tempat_kegiatan')
                    ->label('Tempat'),
                Tables\Columns\TextColumn::make('asal_data_peserta')
                    ->label('Asal Data')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tingkatan_kegiatan')
                    ->label('Tingkatan Kegiatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('detail_tingkatan')
                    ->label('Detail Tingkatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kategori_peserta')
                    ->label('Kategori Peserta'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('buka_presensi')
                    ->label('Buka Presensi')
                    ->url(fn(Kegiatan $record) =>  'kegiatans/' . $record->id . "/presensi")
                    ->icon('heroicon-o-clipboard-document-check')
                    ->visible(fn(Kegiatan $record) => $record->is_sesi || $record->is_finish ? false : true),
                Tables\Actions\Action::make('buka_sesi')
                    ->label(fn(Kegiatan $record) => 'Buka Sesi ' . $record->sesi_aktif)
                    ->requiresConfirmation()
                    ->modalHeading('Waktu Pelaksanaan Sesi')
                    ->modalDescription('Mohon Amal Sholih mengisi waktu sesi yang akan dilaksanakan! (Untuk Nama Sesi Opsional)')
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->modalSubmitActionLabel('Simpan & Mulai Sesi')
                    ->form([
                        Fieldset::make((fn(Kegiatan $record) => 'Sesi ' . $record->sesi_aktif))
                            ->schema([
                                TextInput::make('nm_sesi')
                                    ->label('Nama Sesi (Opsional)')
                                    ->placeholder('Contoh: Registrasi/Sesi 1')
                                    ->formatStateUsing(function (Get $get, Kegiatan $record) {
                                        if (DB::table('sesi_kegiatans')->where('kegiatan_id', $record->id)->where('sesi', $get('sesi'))->exists()) {
                                            return SesiKegiatan::query()->where('kegiatan_id', $record->id)->where('sesi', $get('sesi'))->value('nm_sesi');
                                        } else {
                                            return '';
                                        }
                                    }),
                                DateTimePicker::make('waktu_pelaksanaan')
                                    ->label('Waktu Mulai Sesi')
                                    ->placeholder('Masukkan Waktu Mulai Sesi')
                                    ->seconds(false)
                                    ->required()
                            ])->columns(2)
                    ])
                    ->icon('heroicon-o-clipboard-document-check')
                    ->visible(fn(Kegiatan $record) => $record->is_sesi && !$record->is_finish ? true : false)
                    ->action(function (array $data, Kegiatan $record) {
                        $data['nm_sesi'] == null ? $data['nm_sesi'] = "Sesi " . $record->sesi_aktif : $data['nm_sesi'];
                        if (!DB::table('sesi_kegiatans')->where('kegiatan_id', $record->id)->where('sesi', $record->sesi_aktif)->exists()) {
                            SesiKegiatan::create([
                                'kegiatan_id' => $record->id,
                                'sesi' => $record->sesi_aktif,
                                'nm_sesi' => $data['nm_sesi'],
                                'waktu_pelaksanaan' => $data['waktu_pelaksanaan']
                            ]);
                        }
                        return redirect('kegiatans/' . $record->id . "/presensi");
                    })
                    ->url(function (Kegiatan $record) {
                        if (DB::table('sesi_kegiatans')->where('kegiatan_id', $record->id)->where('sesi', $record->sesi_aktif)->exists()) {
                            return 'kegiatans/' . $record->id . "/presensi";
                        } else {
                            return null;
                        }
                    }),
                Tables\Actions\Action::make('lihat_rekap')
                    ->label('Lihat Rekapitulasi')
                    ->color('success')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->url('/rekapitulasi-kegiatan')
                    ->visible(fn(Kegiatan $record) => $record->is_finish ? true : false),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Kegiatan')
                    ->modalDescription('Apakah kamu yakin data kegiatan ini akan dihapus?')
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Item Terpilih')
                        ->modalHeading('Hapus Kegiatan')
                        ->modalDescription('Apakah kamu yakin data kegiatan ini akan dihapus?')
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kegiatan');
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
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
            'presensi' => Pages\PresensiKegiatan::route('/{record}/presensi')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tingkatan_kegiatan', auth()->user()->roles[0]->name)->where('detail_tingkatan', auth()->user()->detail);
    }
}
