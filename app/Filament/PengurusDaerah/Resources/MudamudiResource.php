<?php

namespace App\Filament\PengurusDaerah\Resources;

use App\Filament\PengurusDaerah\Resources\MudamudiResource\Pages;
use App\Filament\PengurusDaerah\Resources\MudamudiResource\RelationManagers;
use App\Models\Mudamudi;
use App\Models\Status;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MudamudiResource extends Resource
{
    protected static ?string $model = Mudamudi::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Muda-Mudi';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('daerah_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('desa_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kelompok_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jk')
                    ->required(),
                Forms\Components\TextInput::make('kota_lahir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->required(),
                Forms\Components\TextInput::make('mubaligh')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('detail_status')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('usia')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('siap_nikah')
                    ->required(),
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
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            Tables\Columns\TextColumn::make('kelompok.nm_kelompok')
                ->label('Kelompok')
                ->numeric()
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
            SelectFilter::make('Desa')
                ->relationship('desa', 'nm_desa'),
            SelectFilter::make('Kelompok')
                ->relationship('kelompok', 'nm_kelompok'),
            SelectFilter::make('mubaligh')
                ->label('Mubaligh')
                ->options(['Ya' => 'Ya', 'Bukan' => 'Bukan']),
            SelectFilter::make('Status')
                ->options(fn () => Status::query()->pluck('nm_status', 'nm_status')),
            SelectFilter::make('siap_nikah')
                ->label('Siap Nikah')
                ->options(['Siap' => 'Siap', 'Belum' => 'Belum']),
            SelectFilter::make('jk')
                ->label('Jenis Kelamin')
                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
        ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMudamudis::route('/'),
            'create' => Pages\CreateMudamudi::route('/create'),
            'edit' => Pages\EditMudamudi::route('/{record}/edit'),
        ];
    }
}
