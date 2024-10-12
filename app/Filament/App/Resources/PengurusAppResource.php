<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PengurusAppResource\Pages;
use App\Filament\App\Resources\PengurusAppResource\RelationManagers;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Pengurus;
use App\Models\PengurusApp;
use App\Policies\PengurusPolicy;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class PengurusAppResource extends Resource
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
                            ->options(function () {
                                $role = auth()->user()->roles;
                                if ($role[0]->name == 'MM Daerah') {
                                    return Role::query()->where('name', '=', 'MM Daerah')->pluck('name', 'name');
                                } elseif ($role[0]->name == 'MM Desa') {
                                    return Role::query()->where('name', '=', 'MM Desa')->pluck('name', 'name');
                                } elseif ($role[0]->name == 'MM Kelompok') {
                                    return Role::query()->where('name', '=', 'MM Kelompok')->pluck('name', 'name');
                                }
                            })
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
                                    $data = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->pluck('nm_daerah', 'nm_daerah');
                                } elseif ($get('role') == 'MM Desa') {
                                    $data = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->pluck('nm_desa', 'nm_desa');
                                } elseif ($get('role') == 'MM Kelompok') {
                                    $data = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->pluck('nm_kelompok', 'nm_kelompok');
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
                    ->label('L/P')
                    ->sortable(),
                TextColumn::make('dapukan')
                    ->label('Dapukan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nm_tingkatan')
                    ->label('Tingkatan')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable()
            ])
            ->filters([
                SelectFilter::make('dapukan')
                    ->label('Dapukan')
                    ->options([
                        'Ketua' => 'Ketua',
                        'Wakil Ketua' => 'Wakil Ketua',
                        'Penerobos' => 'Penerobos',
                        'Sekretaris' => 'Sekretaris',
                        'Bendahara' => 'Bendahara',
                        'Keputrian' => 'Keputrian',
                        'Seksi-Seksi' => 'Seksi-seksi'
                    ]),
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'MM Daerah' => 'MM Daerah',
                        'MM Desa' => 'MM Desa',
                        'MM Kelompok' => 'MM Kelompok',
                    ]),
                SelectFilter::make('jk')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ])
            ])
            ->emptyStateHeading('Tidak Ada Data Pengurus');
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
            'index' => Pages\ListPengurusApps::route('/'),
            'create' => Pages\CreatePengurusApp::route('/create'),
            'edit' => Pages\EditPengurusApp::route('/{record}/edit'),
        ];
    }
}
