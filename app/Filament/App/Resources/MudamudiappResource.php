<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MudamudiappResource\Pages;
use App\Filament\App\Resources\MudamudiappResource\RelationManagers;
use App\Filament\Exports\MudaMudiExporter;
use App\Models\ArusKeluar;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Mudamudiapp;
use App\Models\Registrasi;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Closure;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MudamudiappResource extends Resource
{
    protected static ?string $model = Mudamudi::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Muda-Mudi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sambung')
                    ->schema([
                        Forms\Components\Select::make('daerah_id')
                            ->label('Daerah')
                            ->options(
                                function () {
                                    $role = auth()->user()->roles;
                                    $daerah_id = '';
                                    $data = null;
                                    if ($role[0]->name == 'MM Daerah') {
                                        $data = Daerah::query()->where('nm_daerah', auth()->user()->detail)->pluck('nm_daerah', 'id');
                                    } elseif ($role[0]->name == 'MM Desa') {
                                        $daerah_id = Desa::query()->where('nm_desa', auth()->user()->detail)->first('daerah_id');
                                        $data = Daerah::query()->where('id', $daerah_id->daerah_id)->pluck('nm_daerah', 'id');
                                    } elseif ($role[0]->name == 'MM Kelompok') {
                                        $desa_id = Kelompok::query()->where('nm_kelompok', auth()->user()->detail)->first('desa_id');
                                        $daerah_id = Desa::query()->where('id', $desa_id->desa_id)->first('daerah_id');
                                        $data = Daerah::query()->where('id', $daerah_id->daerah_id)->pluck('nm_daerah', 'id');
                                    }
                                    return $data;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (Set $set) {
                                $set('desa_id', null);
                                $set('kelompok_id', null);
                            })
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('desa_id')
                            ->label('Desa')
                            ->options(function (Get $get) {
                                $role = auth()->user()->roles;
                                $desa_id = '';
                                $data = '';
                                if ($role[0]->name == 'MM Daerah') {
                                    $data = Desa::query()
                                        ->where('daerah_id', $get('daerah_id'))
                                        ->pluck('nm_desa', 'id');
                                } elseif ($role[0]->name == 'MM Desa') {
                                    $data = Desa::query()->where('nm_desa', auth()->user()->detail)->pluck('nm_desa', 'id');
                                } elseif ($role[0]->name == 'MM Kelompok') {
                                    $desa_id = Kelompok::query()->where('nm_kelompok', auth()->user()->detail)->first('desa_id');
                                    $data = Desa::query()->where('id', $desa_id->desa_id)->pluck('nm_desa', 'id');
                                }
                                return $data;
                            })
                            ->required()
                            ->searchable()
                            ->afterStateUpdated(fn (Set $set) => $set('kelompok_id', null))
                            ->live()
                            ->preload(),
                        Forms\Components\Select::make('kelompok_id')
                            ->label('Kelompok')
                            ->options(function (Get $get) {
                                $role = auth()->user()->roles;
                                $data = '';
                                if ($role[0]->name == 'MM Daerah') {
                                    $data = Kelompok::query()
                                        ->where('desa_id', $get('desa_id'))
                                        ->pluck('nm_kelompok', 'id');
                                } elseif ($role[0]->name == 'MM Desa') {
                                    $data = Kelompok::query()
                                        ->where('desa_id', $get('desa_id'))
                                        ->pluck('nm_kelompok', 'id');
                                } elseif ($role[0]->name == 'MM Kelompok') {
                                    $data = Kelompok::query()->where('nm_kelompok', auth()->user()->detail)->pluck('nm_kelompok', 'id');
                                }
                                return $data;
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->preload(),
                    ])->columns(3),
                Forms\Components\Section::make('Data Diri')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->placeholder('Nama Lengkap')
                            // Mengubah Text Menjadi Camel Casing
                            ->dehydrateStateUsing(fn ($state) => Str::title($state))
                            ->maxLength(255)
                            ->required()
                            ->rule(static function (Forms\Get $get): Closure {
                                return static function (string $attribute, $value, Closure $fail) use ($get) {
                                    if (DB::table('mudamudis')->where('nama', $value)->where('kota_lahir', $get('kota_lahir'))->where('tgl_lahir', $get('tgl_lahir'))->exists()) {
                                        $fail('Data Muda Mudi sudah pernah ditambahkan, Silahkan Cek Kembali!');
                                    } elseif (DB::table('registrasis')->where('nama', $value)->where('tgl_lahir', $get('tgl_lahir'))->exists()) {
                                        $fail('Data Muda-mudi yang kamu input sudah ada di Menu Registrasi, Silahkan cek & Apply datanya!');
                                    }
                                };
                            })
                            ->columnSpanFull(),
                        Forms\Components\Radio::make('jk')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required(),
                        Forms\Components\TextInput::make('kota_lahir')
                            ->label('Kota Lahir')
                            ->placeholder('Kota Lahir')
                            // Mengubah Text Menjadi Camel Casing
                            ->dehydrateStateUsing(fn ($state) => Str::title($state))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->label('Tanggal Lahir')
                            ->maxDate(Carbon::now()->format('Y-m-d'))
                            ->required(),
                        Forms\Components\Radio::make('mubaligh')
                            ->label('Mubaligh')
                            ->options(['Ya' => 'Ya', 'Bukan' => 'Bukan'])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(fn () => Status::query()->pluck('nm_status', 'nm_status'))
                            ->live()
                            ->required(),
                        Forms\Components\Textarea::make('detail_status')
                            ->label('Detail Status')
                            ->placeholder(function (Get $get) {
                                if ($get('status') == 'Pelajar SMP') {
                                    return 'Contoh : Kelas 7';
                                } elseif ($get('status') == 'Pelajar SMA') {
                                    return 'Contoh : Jurusan IPA';
                                } elseif ($get('status') == 'Pelajar SMK') {
                                    return 'Contoh : Jurusan Rekayasa Perangkat Lunak (RPL)';
                                } elseif ($get('status') == 'Mahasiswa D3') {
                                    return 'Contoh : Jurusan Sistem Informasi';
                                } elseif ($get('status') == 'Mahasiswa S1/D4') {
                                    return 'Contoh : Jurusan Teknik Sipil';
                                } elseif ($get('status') == 'Mahasiswa S2') {
                                    return 'Contoh : Jurusan Manajemen Sistem Informasi';
                                } elseif ($get('status') == 'Mahasiswa S3') {
                                    return 'Contoh : Jurusan Filsafat';
                                } elseif ($get('status') == 'Pencari Kerja SMP') {
                                    return 'Diisi Keahlian yang dimiliki';
                                } elseif ($get('status') == 'Pencari Kerja SMA/K') {
                                    return 'Contoh : Jurusan IPA/Teknik Mesin';
                                } elseif ($get('status') == 'Pencari Kerja D3') {
                                    return 'Contoh : Jurusan Sistem Informasi';
                                } elseif ($get('status') == 'Pencari Kerja S1/D4') {
                                    return 'Contoh : Teknik Informatika (S.Kom)';
                                } elseif ($get('status') == 'Pencari Kerja S2') {
                                    return 'Contoh : Teknik Informatika (M.Kom)';
                                } elseif ($get('status') == 'Karyawan/Pegawai') {
                                    return 'Contoh : Operator Produksi/Pegawai Toko';
                                } elseif ($get('status') == 'Tenaga Sabilillah (SB)') {
                                    return 'Contoh : Tugas MT/SB Rawagabus';
                                } elseif ($get('status') == 'Kuliah & Kerja') {
                                    return 'Contoh : Jurusan Manajemen/Operator Produksi';
                                } elseif ($get('status') == 'Wirausaha/Freelance') {
                                    return 'Contoh : Bakpao Barokah 354/Designer';
                                }
                            })
                            ->required(),
                        Forms\Components\Radio::make('siap_nikah')
                            ->label('Siap Nikah')
                            ->options(['Siap' => 'Siap', 'Belum' => 'Belum'])
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('daerah.nm_daerah')
                    ->label('Daerah')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('desa.nm_desa')
                    ->label('Desa')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('kelompok.nm_kelompok')
                    ->label('Kelompok')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jk')
                    ->label('L/P')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kota_lahir')
                    ->label('Kota Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('mubaligh')
                    ->icon(fn (string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Bukan' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Bukan' => 'danger',
                    })
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('detail_status')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('usia')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siap_nikah')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon(fn (string $state): string => match ($state) {
                        'Siap' => 'heroicon-o-check-circle',
                        'Belum' => 'heroicon-o-x-circle',
                    })
                    ->iconColor(fn (string $state): string => match ($state) {
                        'Siap' => 'success',
                        'Belum' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('desa')
                    ->label('Desa')
                    ->relationship('desa', 'nm_desa')
                    ->multiple(),
                SelectFilter::make('kelompok')
                    ->label('Kelompok')
                    ->relationship('kelompok', 'nm_kelompok')
                    ->multiple(),
                SelectFilter::make('mubaligh')
                    ->label('Mubaligh')
                    ->options(['Ya' => 'Ya', 'Bukan' => 'Bukan']),
                SelectFilter::make('Status')
                    ->options(fn () => Status::query()->pluck('nm_status', 'nm_status'))
                    ->multiple(),
                SelectFilter::make('siap_nikah')
                    ->label('Siap Nikah')
                    ->options(['Siap' => 'Siap', 'Belum' => 'Belum']),
                SelectFilter::make('jk')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('Detail'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Hapus Data')
                    ->modalDescription('Sebelum dihapus, Mohon Amal Sholih mengisi keterangan dihapus dibawah ini')
                    ->form([
                        Select::make('keterangan')
                            ->label('Keterangan Dihapus')
                            ->options([
                                'Menikah' => 'Menikah',
                                'Meninggal' => 'Meninggal',
                                'Pindah Sambung Dalam Daerah' => 'Pindah Sambung Dalam Daerah',
                                'Pindah Sambung Keluar Daerah' => 'Pindah Sambung Keluar Daerah'
                            ])
                            ->live()
                            ->required(),
                        Select::make('desa_id')
                            ->label('Nama Desa')
                            ->options(function (Mudamudi $record) {
                                return Desa::query()->where('daerah_id', $record->daerah_id)->pluck('nm_desa', 'id');
                            })
                            ->preload()
                            ->live()
                            ->required()
                            ->visible(fn (Get $get): bool => $get('keterangan') == 'Pindah Sambung Dalam Daerah' ? true : false),
                        Select::make('kelompok_id')
                            ->label('Nama Kelompok')
                            ->options(function (Get $get) {
                                return Kelompok::query()->where('desa_id', $get('desa_id'))->pluck('nm_kelompok', 'id');
                            })
                            ->preload()
                            ->live()
                            ->required()
                            ->visible(fn (Get $get): bool => $get('keterangan') == 'Pindah Sambung Dalam Daerah' ? true : false)
                    ])
                    ->modalSubmitActionLabel('Hapus Data')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (array $data, Mudamudi $record) {
                        $dataRecord = $record->toArray();
                        ArusKeluar::create([
                            'daerah_id' => $dataRecord['daerah_id'],
                            'desa_id' => $dataRecord['desa_id'],
                            'kelompok_id' => $dataRecord['kelompok_id'],
                            'nama' => $dataRecord['nama'],
                            'jk' => $dataRecord['jk'],
                            'usia' => $dataRecord['usia'],
                            'keterangan' => $data['keterangan'],
                        ]);
                        if ($data['keterangan'] == 'Pindah Sambung Dalam Daerah') {
                            Registrasi::create([
                                'daerah_id' => $dataRecord['daerah_id'],
                                'desa_id' => $data['desa_id'],
                                'kelompok_id' => $data['kelompok_id'],
                                'nama' => $dataRecord['nama'],
                                'jk' => $dataRecord['jk'],
                                'kota_lahir' => $dataRecord['kota_lahir'],
                                'tgl_lahir' => $dataRecord['tgl_lahir'],
                                'mubaligh' => $dataRecord['mubaligh'],
                                'status' => $dataRecord['status'],
                                'detail_status' => $dataRecord['detail_status'],
                                'usia' => $dataRecord['usia'],
                                'siap_nikah' => $dataRecord['siap_nikah'],
                            ]);
                        }
                        $record->delete();
                        Notification::make()
                            ->success()
                            ->title('Berhasil Dihapus')
                            ->send();
                    })
            ])
            
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('Tidak Ada Data Muda-Mudi');
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
            'index' => Pages\ListMudamudiapps::route('/'),
            // 'view' => Pages\ViewMudamudiapp::route('/{record}'),
            'create' => Pages\CreateMudamudiapp::route('/create'),
            'edit' => Pages\EditMudamudiapp::route('/{record}/edit'),
        ];
    }
}
