<?php

namespace App\Filament\PengurusDaerah\Resources;

use App\Filament\PengurusDaerah\Resources\DapukanResource\Pages;
use App\Filament\PengurusDaerah\Resources\DapukanResource\RelationManagers;
use App\Models\Dapukan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DapukanResource extends Resource
{
    protected static ?string $model = Dapukan::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Dapukan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tingkatan')
                    ->label('Tingkatan')
                    ->options([
                        'Daerah' => 'Daerah',
                        'Desa' => 'Desa',
                        'Kelompok' => 'Kelompok',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nama_dapukan')
                    ->label('Nama Dapukan')
                    ->required()
                    ->placeholder('Nama Dapukan')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tingkatan'),
                Tables\Columns\TextColumn::make('nama_dapukan')
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
            ->emptyStateHeading('Tidak ada Dapukan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDapukans::route('/'),
        ];
    }
}
