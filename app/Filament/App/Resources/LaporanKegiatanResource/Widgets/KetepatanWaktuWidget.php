<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use Filament\Widgets\ChartWidget;

class KetepatanWaktuWidget extends ChartWidget
{
    protected static ?string $heading = 'Ketepatan Waktu';

    public $inTime;
    public $onTime;
    public $overTime;

    protected function getData(): array
    {
        $data[0] = $this->inTime;
        $data[1] = $this->onTime;
        $data[2] = $this->overTime;

        return [
            'datasets' => [
                [
                    'label' => 'Jenis Kelamin',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(20, 110, 255)',
                        'rgb(26, 237, 85)',
                        'rgb(254, 196, 37)',
                    ]
                ],
            ],
            'labels' => ['In Time', 'On Time', 'Over Time'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
