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
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
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

class RegistrasiResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Registrasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Database';
    protected static ?string $navigationLabel = 'Registrasi';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'share',
        ];
    }

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
                    ->visible(fn() => Auth::user()->getAppRoleName() == 'MM Daerah' ? false : true)
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
                            unset($data['kegiatan_id']);
                            $mudamudi = Mudamudi::create($data);
                            Riwayat::create([
                                'daerah_id' => $data['daerah_id'],
                                'desa_id' => $data['desa_id'],
                                'kelompok_id' => $data['kelompok_id'],
                                'nama' => $data['nama'],
                                'nm_user' => auth()->user()->name,
                                'action' => 'Apply',
                            ]);
                            if ($record->kegiatan_id) {
                                $kegiatan = \App\Models\Kegiatan::find($record->kegiatan_id);
                                if ($kegiatan) {
                                    $now = \Carbon\Carbon::now();
                                    $waktuKegiatan = \Carbon\Carbon::parse($kegiatan->waktu_mulai);
                                    $onTime = \Carbon\Carbon::parse($kegiatan->waktu_mulai)->addMinutes(15);
                                    $kedatangan = '';
                                    
                                    if ($now < $waktuKegiatan) {
                                        $kedatangan = 'In Time';
                                    } elseif ($waktuKegiatan <= $now && $now <= $onTime) {
                                        $kedatangan = 'On Time';
                                    } elseif ($onTime < $now) {
                                        $kedatangan = 'Overtime';
                                    }
                                    
                                    \App\Models\Presensi::create([
                                        'kegiatan_id' => $kegiatan->id,
                                        'mudamudi_id' => $mudamudi->id,
                                        'keterangan' => 'Hadir',
                                        'kedatangan' => $kedatangan,
                                    ]);
                                }
                            }
                            $record->delete();
                            return Notification::make()
                                ->success()
                                ->title('Data Berhasil Ditambahkan')
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Reject')
                    ->visible(fn() => Auth::user()->getAppRoleName() == 'MM Daerah' ? false : true)
                    ->modalHeading('Hapus Data Registrasi')
                    ->modalDescription('Apakah kamu yakin ingin menghapus data registrasi ini ?')
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Reject All')
                        ->visible(fn() => Auth::user()->getAppRoleName() == 'MM Daerah' ? false : true)
                        ->modalHeading('Hapus Data Registrasi')
                        ->modalDescription('Apakah kamu yakin ingin menghapus data registrasi ini ?')
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal'),
                    Tables\Actions\BulkAction::make('Apply All')
                        ->icon('heroicon-s-check')
                        ->color('success')
                        ->visible(fn() => Auth::user()->getAppRoleName() == 'MM Daerah' ? false : true)
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
                                    unset($data['kegiatan_id']);
                                    $mudamudi = Mudamudi::create($data);
                                    Riwayat::create([
                                        'daerah_id' => $data['daerah_id'],
                                        'desa_id' => $data['desa_id'],
                                        'kelompok_id' => $data['kelompok_id'],
                                        'nama' => $data['nama'],
                                        'nm_user' => auth()->user()->name,
                                        'action' => 'Apply',
                                    ]);
                                    if ($record->kegiatan_id) {
                                        $kegiatan = \App\Models\Kegiatan::find($record->kegiatan_id);
                                        if ($kegiatan) {
                                            $now = \Carbon\Carbon::now();
                                            $waktuKegiatan = \Carbon\Carbon::parse($kegiatan->waktu_mulai);
                                            $onTime = \Carbon\Carbon::parse($kegiatan->waktu_mulai)->addMinutes(15);
                                            $kedatangan = '';
                                            
                                            if ($now < $waktuKegiatan) {
                                                $kedatangan = 'In Time';
                                            } elseif ($waktuKegiatan <= $now && $now <= $onTime) {
                                                $kedatangan = 'On Time';
                                            } elseif ($onTime < $now) {
                                                $kedatangan = 'Overtime';
                                            }
                                            
                                            \App\Models\Presensi::create([
                                                'kegiatan_id' => $kegiatan->id,
                                                'mudamudi_id' => $mudamudi->id,
                                                'keterangan' => 'Hadir',
                                                'kedatangan' => $kedatangan,
                                            ]);
                                        }
                                    }
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
        $count = static::getEloquentQuery()->count();

        return $count > 0 ? (string) $count : null;
    }
    
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if (! $user) {
            return parent::getEloquentQuery();
        }

        $roleName = method_exists($user, 'getAppRoleName') ? $user->getAppRoleName() : null;
        if (! $roleName && $user->roles->isNotEmpty()) {
            $roleName = $user->roles[0]->name;
        }

        if ($roleName === 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', $user->detail)->first(['id']);
            return parent::getEloquentQuery()->where('daerah_id', $daerah?->id);
        } elseif ($roleName === 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', $user->detail)->first(['id', 'daerah_id']);
            return parent::getEloquentQuery()->where('daerah_id', $desa?->daerah_id)->where('desa_id', $desa?->id);
        } elseif ($roleName === 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', $user->detail)->first(['id', 'desa_id']);
            $desa = $kelompok ? Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']) : null;
            return parent::getEloquentQuery()->where('daerah_id', $desa?->daerah_id)->where('desa_id', $desa?->id)->where('kelompok_id', $kelompok?->id);
        }

        return parent::getEloquentQuery();
    }

    public static function getShareWhatsappUrl(): string
    {
        $registrasis = static::getEloquentQuery()
            ->with(['desa', 'kelompok'])
            ->get();

        $groupedByDesa = $registrasis->groupBy(function ($item) {
            return $item->desa?->nm_desa ?? 'Lainnya';
        });

        $listText = [];

        foreach ($groupedByDesa as $nmDesa => $itemsInDesa) {
            $desaSection = "*{$nmDesa}*";

            $groupedByKelompok = $itemsInDesa->groupBy(function ($item) {
                return $item->kelompok?->nm_kelompok ?? 'Lainnya';
            });

            foreach ($groupedByKelompok as $nmKelompok => $itemsInKelompok) {
                $count = $itemsInKelompok->count();
                $desaSection .= "\n- {$nmKelompok} : {$count}";
            }

            $listText[] = $desaSection;
        }

        $bodyList = ! empty($listText)
            ? implode("\n\n", $listText)
            : "_(Tidak ada data registrasi baru)_";

        $pesan = "🙏🏻 السلام عليكم ورحمة الله وبركاته 🙏🏻\n\n"
            . "📢 Mengingatkan\n"
            . "Kepada seluruh Pengurus Muda/i, Mohon Amal sholih untuk mengecek Data Registrasi Muda/i Baru di laman https://ppgkartim.com/registrasis.\n\n"
            . "Adapun List Data Registrasi Muda/i Baru sebagai berikut :\n\n"
            . "{$bodyList}\n\n"
            . "Aksi :\n"
            . "✅ Apply : Data registrasi benar & masuk database\n"
            . "❌ Hapus : Jika data registrasi tidak benar, tidak dikenali atau data sebelumnya sudah ada (duplikat)\n\n"
            . "Semoga Allah paring kesemangatan, aman, selamat, lancar, sukses dan barokah\n\n"
            . "الحمدلله جزاكم الله خيرا";

        return 'https://api.whatsapp.com/send?text=' . rawurlencode($pesan);
    }
}
