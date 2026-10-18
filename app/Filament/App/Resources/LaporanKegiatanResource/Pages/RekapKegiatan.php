<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Pages;

use App\Filament\App\Resources\LaporanKegiatanResource;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\HadirAlfaTableWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\IzinTableWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\JkHadirWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\KehadiranStat;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\KetepatanWaktuWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\PerizinanWidget;
use App\Services\RekapKegiatanExportService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class RekapKegiatan extends Page
{
    use InteractsWithRecord;

    protected static string $resource = LaporanKegiatanResource::class;

    protected static string $view = 'filament.app.resources.laporan-kegiatan-resource.pages.rekap-kegiatan';

    protected ?string $heading = 'Rekap Kegiatan';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('export_hadir')
                    ->label('Export Hadir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading('Export Data Peserta Hadir')
                    ->modalDescription(fn() => "Download data muda-mudi yang Hadir pada kegiatan " . ($this->record->kegiatan?->nm_kegiatan ?? '') . " ({$this->record->detail_tingkatan})")
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->record, 'Hadir', $data['format'])),

                Action::make('export_alfa')
                    ->label('Export Alfa')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Export Data Peserta Alfa')
                    ->modalDescription(fn() => "Download data muda-mudi yang Alfa pada kegiatan " . ($this->record->kegiatan?->nm_kegiatan ?? '') . " ({$this->record->detail_tingkatan})")
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->record, 'Alfa', $data['format'])),

                Action::make('export_semua')
                    ->label('Export Hadir & Alfa')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->modalHeading('Export Semua (Hadir & Alfa)')
                    ->modalDescription(fn() => "Download seluruh data peserta Hadir dan Alfa pada kegiatan " . ($this->record->kegiatan?->nm_kegiatan ?? '') . " ({$this->record->detail_tingkatan})")
                    ->modalSubmitActionLabel('Download')
                    ->form([
                        Radio::make('format')
                            ->label('Pilih Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->inline()
                            ->required(),
                    ])
                    ->action(fn(array $data) => RekapKegiatanExportService::export($this->record, null, $data['format'])),
            ])
            ->label('Export Rekap')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KehadiranStat::make([
                'data' => $this->record,
            ]),
            JkHadirWidget::make([
                'lakiLaki' => $this->record->hadir_l,
                'perempuan' => $this->record->hadir_p,
            ]),
            KetepatanWaktuWidget::make([
                'inTime' => $this->record->in_time,
                'onTime' => $this->record->on_time,
                'overTime' => $this->record->over_time,
            ]),
            PerizinanWidget::make([
                'sakit' => $this->record->sakit,
                'kerja' => $this->record->kerja,
                'kuliah' => $this->record->kuliah,
                'sekolah' => $this->record->sekolah,
                'acaraKeluarga' => $this->record->acara_keluarga,
                'acaraMendesak' => $this->record->acara_mendesak,
            ]),
            IzinTableWidget::make([
                'laporan' => $this->record,
            ]),
            HadirAlfaTableWidget::make([
                'laporan' => $this->record,
            ])
        ];
    }
}
