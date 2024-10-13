<?php

namespace App\Filament\PengurusDaerah\Resources;

use App\Filament\PengurusDaerah\Resources\PengurusSedaerahResource\Pages;
use App\Filament\PengurusDaerah\Resources\PengurusSedaerahResource\RelationManagers;
use App\Models\Dapukan;
use App\Models\PengurusSedaerah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengurusSedaerahResource extends Resource
{
    protected static ?string $model = PengurusSedaerah::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Pengurus Sedaerah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tingkatan')
                    ->options([
                        'Daerah' => 'Daerah',
                        'Desa' => 'Desa',
                        'Kelompok' => 'Kelompok'
                    ])
                    ->afterStateUpdated(function (Set $set) {
                        $set('dapukan', null);
                    })
                    ->searchable()
                    ->live()
                    ->required(),
                Forms\Components\Select::make('dapukan')
                    ->searchable()
                    ->preload()
                    ->options(function (Get $get) {
                        $data = Dapukan::query()->where('tingkatan', $get('tingkatan'))->pluck('nama_dapukan', 'nama_dapukan');
                        return $data;
                    })
                    ->required(),
                Forms\Components\TextInput::make('nama_pengurus')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_hp')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tingkatan')
                    ->label('Tingkatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dapukan')
                    ->label('Dapukan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pengurus')
                    ->label('Nama Pengurus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak Ada Data Pengurus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePengurusSedaerahs::route('/'),
        ];
    }
}
