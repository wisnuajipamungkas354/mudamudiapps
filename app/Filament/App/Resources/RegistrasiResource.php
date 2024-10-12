<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RegistrasiResource\Pages;
use App\Filament\App\Resources\RegistrasiResource\RelationManagers;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Registrasi;
use App\Models\Riwayat;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrasiResource extends Resource
{
    protected static ?string $model = Registrasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Database';
    protected static ?string $navigationLabel = 'Registrasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                Tables\Columns\TextColumn::make('mubaligh')
                    ->icon(fn (string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Bukan' => 'heroicon-o-x-circle',
                    })
                    ->iconColor(fn (string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Bukan' => 'danger',
                    }),
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
                    }),
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
                    ->modalDescription('Apakah kamu yakin data yang diregistrasi adalah benar Muda-mudimu ?')
                    ->modalSubmitActionLabel('Ya Benar')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (Registrasi $record) {
                       if (DB::table('mudamudis')->where('nama', $record->nama)->where('tgl_lahir', $record->tgl_lahir)->exists()) {
                            return Notification::make()
                                ->danger()
                                ->title('Data Sudah Ada Didatabase, Silahkan Cek Kembali!')
                                ->send();
                        } else {
                            $data = $record->toArray();
                            Mudamudi::create($data);
                            Riwayat::create([
                                'daerah_id' => $data['daerah_id'],
                                'desa_id' => $data['desa_id'],
                                'kelompok_id' => $data['kelompok_id'],
                                'nama' => $data['nama'],
                                'nm_user' => auth()->user()->name,
                                'action' => 'Apply',
                            ]);
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Reject All')
                        ->modalHeading('Hapus Data Registrasi')
                        ->modalDescription('Apakah kamu yakin ingin menghapus data registrasi ini ?')
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal'),
                    Tables\Actions\BulkAction::make('Apply All')
                        ->icon('heroicon-s-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Data')
                        ->modalDescription('Apakah kamu yakin data yang diregistrasi adalah benar Muda-mudimu ?')
                        ->modalSubmitActionLabel('Ya Benar')
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if (DB::table('mudamudis')->where('nama', $record->nama)->where('tgl_lahir', $record->tgl_lahir)->exists()) {
                                    return Notification::make()
                                        ->danger()
                                        ->title('Data Sudah Ada Didatabase, Silahkan Cek Kembali!')
                                        ->send();
                                } else {
                                    $data = $record->toArray();
                                    Mudamudi::create($record->toArray());
                                    Riwayat::create([
                                        'daerah_id' => $data['daerah_id'],
                                        'desa_id' => $data['desa_id'],
                                        'kelompok_id' => $data['kelompok_id'],
                                        'nama' => $data['nama'],
                                        'nm_user' => auth()->user()->name,
                                        'action' => 'Apply',
                                    ]);
                                    DB::table('registrasis')->where('id', $record->id)->delete();
                                    return Notification::make()
                                        ->success()
                                        ->title('Data Muda-Mudi Berhasil Ditambahkan')
                                        ->send();
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Belum ada yang Registrasi');
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
            'index' => Pages\ListRegistrasis::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first(['id']);
            return static::getModel()::where('daerah_id', $daerah->id)->count();
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
            return static::getModel()::where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id)->count();
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
            return static::getModel()::where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id)->where('kelompok_id', $kelompok->id)->count();
        }
    }
    
    public static function getEloquentQuery(): Builder
    {
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first(['id']);
            return parent::getEloquentQuery()->where('daerah_id', $daerah->id);
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
            return parent::getEloquentQuery()->where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
            return parent::getEloquentQuery()->where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id)->where('kelompok_id', $kelompok->id);
        }
    }
}
