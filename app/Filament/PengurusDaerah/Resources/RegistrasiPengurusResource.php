<?php

namespace App\Filament\PengurusDaerah\Resources;

use App\Filament\PengurusDaerah\Resources\RegistrasiPengurusResource\Pages;
use App\Filament\PengurusDaerah\Resources\RegistrasiPengurusResource\RelationManagers;
use App\Models\PengurusSedaerah;
use App\Models\RegistrasiPengurus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class RegistrasiPengurusResource extends Resource
{
    protected static ?string $model = RegistrasiPengurus::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Database';

    protected static ?string $navigationLabel = 'Registrasi Pengurus';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tingkatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('dapukan')
                    ->required()
                    ->maxLength(255),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('dapukan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pengurus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
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
                Tables\Actions\Action::make('Apply')
                    ->icon('heroicon-s-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Data')
                    ->modalDescription('Apakah kamu yakin data yang diregistrasi adalah pengurus ?')
                    ->modalSubmitActionLabel('Ya Benar')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (RegistrasiPengurus $record) {
                       if (DB::table('pengurus_sedaerahs')
                        ->where('nama_pengurus', $record->nama_pengurus)
                        ->where('tingkatan', $record->tingkatan)
                        ->where('dapukan', $record->dapukan)
                        ->exists()) {
                            return Notification::make()
                                ->danger()
                                ->title('Data Sudah Ada Didatabase, Silahkan Cek Kembali!')
                                ->send();
                        } else {
                            $data = $record->toArray();
                            PengurusSedaerah::create($data);
                            $record->delete();
                            return Notification::make()
                                ->success()
                                ->title('Data Berhasil Ditambahkan')
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Reject')
                    ->modalHeading('Hapus Data Registrasi')
                    ->modalDescription('Apakah kamu yakin ingin menghapus data registrasi ini ?')
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada yang registrasi');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRegistrasiPenguruses::route('/'),
        ];
    }

    public static function getNavigationBadge() : ?string
    {
        return static::getModel()::count();
    }
}
