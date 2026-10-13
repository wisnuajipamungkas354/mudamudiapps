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
use Illuminate\Database\Eloquent\Builder;
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

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $role = $user->getAppRoleName();

        $daerahId = null;
        if ($role === 'MM Daerah') {
            $daerahId = \App\Models\Daerah::where('nm_daerah', $user->detail)->value('id');
        } elseif ($role === 'MM Desa') {
            $daerahId = \App\Models\Desa::where('nm_desa', $user->detail)->value('daerah_id');
        } elseif ($role === 'MM Kelompok') {
            $desaId = \App\Models\Kelompok::where('nm_kelompok', $user->detail)->value('desa_id');
            $daerahId = \App\Models\Desa::where('id', $desaId)->value('daerah_id');
        }

        if (! $daerahId) {
            return parent::getEloquentQuery();
        }

        $namaDaerah = \App\Models\Daerah::where('id', $daerahId)->value('nm_daerah');
        $namaDesas = \App\Models\Desa::where('daerah_id', $daerahId)->pluck('nm_desa')->toArray();
        $namaKelompoks = \App\Models\Kelompok::whereHas('desa', fn ($q) => $q->where('daerah_id', $daerahId))->pluck('nm_kelompok')->toArray();

        return parent::getEloquentQuery()->where(function (Builder $query) use ($namaDaerah, $namaDesas, $namaKelompoks) {
            $query->where(function (Builder $q) use ($namaDaerah) {
                $q->where('role', 'MM Daerah')->where('nm_tingkatan', $namaDaerah);
            })->orWhere(function (Builder $q) use ($namaDesas) {
                $q->where('role', 'MM Desa')->whereIn('nm_tingkatan', $namaDesas);
            })->orWhere(function (Builder $q) use ($namaKelompoks) {
                $q->where('role', 'MM Kelompok')->whereIn('nm_tingkatan', $namaKelompoks);
            });
        });
    }
}
