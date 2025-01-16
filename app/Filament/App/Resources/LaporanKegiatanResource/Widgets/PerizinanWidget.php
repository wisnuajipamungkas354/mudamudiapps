<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use Filament\Widgets\ChartWidget;

class PerizinanWidget extends ChartWidget
{
    protected static ?string $heading = 'Perizinan';

    public $sakit;
    public $kerja;
    public $kuliah;
    public $sekolah;
    public $acaraKeluarga;
    public $acaraMendesak;

    protected function getData(): array
    {
        $data[0] = $this->sakit;
        $data[1] = $this->kerja;
        $data[2] = $this->kuliah;
        $data[3] = $this->sekolah;
        $data[4] = $this->acaraKeluarga;
        $data[5] = $this->acaraMendesak;

        return [
            'datasets' => [
                [
                    'label' => 'Total Izin',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(239, 167, 58, 0.8)',
                        'rgba(0, 239, 72, 0.94)',
                        'rgba(51, 146, 247, 0.8)',
                        'rgba(169, 57, 255, 0.8)',
                        'rgba(254, 50, 173, 0.8)',
                        'rgba(255, 40, 40, 0.8)',
                    ],
                ],
            ],
            'labels' => ['Sakit', 'Kerja', 'Kuliah', 'Acara Sekolah', 'Acara Keluarga', 'Acara Mendesak'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
