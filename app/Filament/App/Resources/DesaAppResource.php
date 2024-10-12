<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DesaAppResource\Pages;
use App\Filament\App\Resources\DesaAppResource\RelationManagers;
use App\Models\Desa;
use App\Models\DesaApp;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesaAppResource extends Resource
{
    protected static ?string $model = Desa::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Manajemen Wilayah';

    protected static ?string $navigationLabel = 'Data Desa';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('daerah_id')
                            ->label('Daerah')
                            ->native(false)
                            ->relationship(name: 'daerah', titleAttribute: 'nm_daerah')
                            ->required(),
                        TextInput::make('nm_desa')
                            ->label('Nama Desa')
                            ->unique()
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('daerah.nm_daerah')
                    ->label('Daerah')
                    ->sortable(),
                TextColumn::make('nm_desa')
                    ->label('Nama Desa')
                    ->searchable()
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDesaApps::route('/'),
        ];
    }
}
