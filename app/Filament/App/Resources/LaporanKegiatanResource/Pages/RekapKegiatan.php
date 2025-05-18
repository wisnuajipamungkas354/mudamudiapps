<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Pages;

use App\Filament\App\Resources\LaporanKegiatanResource;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\HadirAlfaTableWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\IzinTableWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\JkHadirWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\KehadiranStat;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\KetepatanWaktuWidget;
use App\Filament\App\Resources\LaporanKegiatanResource\Widgets\PerizinanWidget;
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
