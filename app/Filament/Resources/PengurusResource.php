<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengurusResource\Pages;
use App\Filament\Resources\PengurusResource\RelationManagers;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Pengurus;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PengurusResource extends Resource
{
    protected static ?string $model = Pengurus::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Pengurus Muda-Mudi';

    protected static ?string $navigationGroup = 'Database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('nm_pengurus')
                            ->label('Nama Pengurus')
                            ->required()
                            ->maxLength(255),
                        Radio::make('jk')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required(),
                        Select::make('dapukan')
                            ->label('Dapukan')
                            ->options([
                                'Ketua' => 'Ketua',
                                'Wakil Ketua' => 'Wakil Ketua',
                                'Penerobos' => 'Penerobos',
                                'Sekretaris' => 'Sekretaris',
                                'Bendahara' => 'Bendahara',
                                'Keputrian' => 'Keputrian',
                                'Seksi-Seksi' => 'Seksi-seksi'
                            ])
                            ->required(),
                        Select::make('role')
                            ->label('Role')
                            ->options(fn () => Role::query()->where('name', '!=', 'Admin')->pluck('name', 'name'))
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('nm_tingkatan', null))
                            ->searchable()
                            ->required(),
                        Select::make('nm_tingkatan')
                            ->label('Tingkatan')
                            ->options(function (Get $get) {
                                $data = null;
                                if ($get('role') == 'MM Daerah') {
                                    $data = Daerah::query()->pluck('nm_daerah', 'nm_daerah');
                                } elseif ($get('role') == 'MM Desa') {
                                    $data = Desa::query()->pluck('nm_desa', 'nm_desa');
                                } elseif ($get('role') == 'MM Kelompok') {
                                    $data = Kelompok::query()->pluck('nm_kelompok', 'nm_kelompok');
                                }
                                return $data;
                            })
                            ->required()
                            ->optionsLimit(5)
                            ->preload()
                            ->live()
                            ->searchable(),
                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->numeric()
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nm_pengurus')
                    ->label('Nama Pengurus')
                    ->searchable(),
                TextColumn::make('jk')
                    ->label('Jk')
                    ->sortable(),
                TextColumn::make('dapukan')
                    ->label('Dapukan')
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->sortable(),
                TextColumn::make('nm_tingkatan')
                    ->label('Tingkatan'),
                TextColumn::make('no_hp')
                    ->label('Nomor HP')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenguruses::route('/'),
            'create' => Pages\CreatePengurus::route('/create'),
            'edit' => Pages\EditPengurus::route('/{record}/edit'),
        ];
    }

}
