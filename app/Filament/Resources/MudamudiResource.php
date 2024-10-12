<?php

namespace App\Filament\Resources;

use App\Filament\Exports\MudamudiExporter;
use Closure;
use App\Filament\Resources\MudamudiResource\Pages;
use App\Filament\Resources\MudamudiResource\RelationManagers;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MudamudiResource extends Resource
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
                            ->relationship(name: 'daerah', titleAttribute: 'nm_daerah')
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
                            ->options(fn (Get $get): Collection => Desa::query()
                                ->where('daerah_id', $get('daerah_id'))
                                ->pluck('nm_desa', 'id'))
                            ->required()
                            ->searchable()
                            ->afterStateUpdated(fn (Set $set) => $set('kelompok_id', null))
                            ->live()
                            ->preload(),
                        Forms\Components\Select::make('kelompok_id')
                            ->label('Kelompok')
                            ->options(fn (Get $get): Collection => Kelompok::query()
                                ->where('desa_id', $get('desa_id'))
                                ->pluck('nm_kelompok', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->preload(),
                    ])->columns(3),
                Forms\Components\Section::make('Data Diri')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            // Mengubah Text Menjadi Camel Casing
                            ->dehydrateStateUsing(fn ($state) => ucwords($state))
                            ->maxLength(255)
                            ->required()
                            ->rule(static function (Forms\Get $get): Closure {
                                return static function (string $attribute, $value, Closure $fail) use ($get) {
                                    if (DB::table('mudamudis')->where('nama', $value)->where('kota_lahir', $get('kota_lahir'))->where('tgl_lahir', $get('tgl_lahir'))->exists()) {
                                        $fail('Data Muda Mudi sudah pernah ditambahkan, Silahkan Cek Kembali!');
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
                            // Mengubah Text Menjadi Camel Casing
                            ->dehydrateStateUsing(fn ($state) => ucwords($state))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->label('Tanggal Lahir')
                            ->afterStateUpdated(
                                function (Set $set, $state) {
                                    $set('usia', Carbon::parse($state)->age);
                                }
                            )
                            ->live()
                            ->required(),
                        Forms\Components\Radio::make('mubaligh')
                            ->label('Mubaligh')
                            ->options(['Ya' => 'Ya', 'Bukan' => 'Bukan'])
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('detail_status')
                            ->label('Detail Status')
                            ->required(),
                        Forms\Components\Radio::make('siap_nikah')
                            ->label('Siap Nikah')
                            ->options(['Siap' => 'Siap', 'Belum' => 'Belum'])
                            ->required(),
                        Forms\Components\TextInput::make('usia')
                            ->label('Usia')
                            ->live()
                            ->required()
                            ->numeric(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('daerah.nm_daerah')
                    ->label('Daerah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('desa.nm_desa')
                    ->label('Desa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelompok.nm_kelompok')
                    ->label('Kelompok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
                SelectFilter::make('Desa')
                    ->relationship('desa', 'nm_desa'),
                SelectFilter::make('Kelompok')
                    ->relationship('kelompok', 'nm_kelompok'),
                SelectFilter::make('mubaligh')
                    ->label('Mubaligh')
                    ->options(['Ya' => 'Ya', 'Bukan' => 'Bukan']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMudamudis::route('/'),
            'create' => Pages\CreateMudamudi::route('/create'),
            'view' => Pages\ViewMudamudi::route('/{record}'),
            'edit' => Pages\EditMudamudi::route('/{record}/edit'),
        ];
    }

}
