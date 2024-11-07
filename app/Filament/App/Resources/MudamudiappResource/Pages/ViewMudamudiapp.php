<?php

namespace App\Filament\App\Resources\MudamudiappResource\Pages;

use App\Filament\App\Resources\MudamudiappResource;
use App\Models\ArusKeluar;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Registrasi;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ViewMudamudiapp extends ViewRecord
{
    protected static string $resource = MudamudiappResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Detail Data Muda-Mudi';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            ViewEntry::make('')
            ->view('infolists.components.qr-code-generator')
            ->columnSpan(1)
            ,
            Section::make('Sambung')
            ->schema([
                Infolists\Components\TextEntry::make('daerah.nm_daerah'),
                Infolists\Components\TextEntry::make('desa.nm_desa'),
                Infolists\Components\TextEntry::make('kelompok.nm_kelompok'),
            ])
            ->columns(3)
            ->columnSpan(3),
            Section::make('Data Diri')
            ->schema([
                Infolists\Components\TextEntry::make('nama'),
                Infolists\Components\TextEntry::make('jk')
                ->label('Jenis Kelamin'),
                Infolists\Components\TextEntry::make('kota_lahir'),
                Infolists\Components\TextEntry::make('tgl_lahir'),
                Infolists\Components\TextEntry::make('mubaligh')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Ya' => 'success',
                    'Bukan' => 'danger',
                }),
                Infolists\Components\TextEntry::make('status'),
                Infolists\Components\TextEntry::make('detail_status'),
                Infolists\Components\TextEntry::make('siap_nikah')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Siap' => 'success',
                    'Belum' => 'danger',
                }),
            ])
            ->columns(4)
        ])
        ->columns(4);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-s-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Data')
                ->modalDescription('Sebelum dihapus, Mohon Amal Sholih mengisi keterangan dihapus dibawah ini')
                ->form([
                    Select::make('keterangan')
                        ->label('Keterangan Dihapus')
                        ->options([
                            'Menikah' => 'Menikah',
                            'Meninggal' => 'Meninggal',
                            'Pindah Sambung Dalam Daerah' => 'Pindah Sambung Dalam Daerah',
                            'Pindah Sambung Keluar Daerah' => 'Pindah Sambung Keluar Daerah'
                        ])
                        ->live()
                        ->required(),
                    Select::make('desa_id')
                        ->label('Nama Desa')
                        ->options(function (Mudamudi $record) {
                            return Desa::query()->where('daerah_id', $record->daerah_id)->pluck('nm_desa', 'id');
                        })
                        ->preload()
                        ->live()
                        ->required()
                        ->visible(fn (Get $get): bool => $get('keterangan') == 'Pindah Sambung Dalam Daerah' ? true : false),
                    Select::make('kelompok_id')
                        ->label('Nama Kelompok')
                        ->options(function (Get $get) {
                            return Kelompok::query()->where('desa_id', $get('desa_id'))->pluck('nm_kelompok', 'id');
                        })
                        ->preload()
                        ->live()
                        ->required()
                        ->visible(fn (Get $get): bool => $get('keterangan') == 'Pindah Sambung Dalam Daerah' ? true : false)
                ])
                ->modalSubmitActionLabel('Hapus Data')
                ->modalCancelActionLabel('Batal')
                ->action(function (array $data, Mudamudi $record) {
                    $dataRecord = $record->toArray();
                    ArusKeluar::create([
                        'daerah_id' => $dataRecord['daerah_id'],
                        'desa_id' => $dataRecord['desa_id'],
                        'kelompok_id' => $dataRecord['kelompok_id'],
                        'nama' => $dataRecord['nama'],
                        'jk' => $dataRecord['jk'],
                        'usia' => $dataRecord['usia'],
                        'keterangan' => $data['keterangan'],
                    ]);
                    if ($data['keterangan'] == 'Pindah Sambung Dalam Daerah') {
                        Registrasi::create([
                            'daerah_id' => $dataRecord['daerah_id'],
                            'desa_id' => $data['desa_id'],
                            'kelompok_id' => $data['kelompok_id'],
                            'nama' => $dataRecord['nama'],
                            'jk' => $dataRecord['jk'],
                            'kota_lahir' => $dataRecord['kota_lahir'],
                            'tgl_lahir' => $dataRecord['tgl_lahir'],
                            'mubaligh' => $dataRecord['mubaligh'],
                            'status' => $dataRecord['status'],
                            'detail_status' => $dataRecord['detail_status'],
                            'usia' => $dataRecord['usia'],
                            'siap_nikah' => $dataRecord['siap_nikah'],
                        ]);
                    }
                    $record->delete();
                    Notification::make()
                        ->success()
                        ->title('Berhasil Dihapus')
                        ->send();
                }),
        ];
    }
}
