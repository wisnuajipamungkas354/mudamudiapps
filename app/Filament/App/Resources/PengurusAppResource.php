<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PengurusAppResource\Pages;
use App\Models\Pengurus;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                            ->placeholder('Isi Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => Str::title($state)),
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
                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->placeholder('Isi Nomor HP')
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
                    ->url(function(Pengurus $record) {
                        $url = 'https://wa.me/+62';
                        $phoneNumber = $record->no_hp;
                        $formattedPhoneNumber = substr($phoneNumber, 1); // 0
                        return $url . $formattedPhoneNumber;
                    })
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
