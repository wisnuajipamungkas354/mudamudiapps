<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use Filament\Widgets\ChartWidget;

class JkHadirWidget extends ChartWidget
{
    protected static ?string $heading = 'Kehadiran';

    public $lakiLaki;
    public $perempuan;

    protected function getData(): array
    {
        $data[0] = $this->perempuan;
        $data[1] = $this->lakiLaki;

        return [
            'datasets' => [
                [
                    'label' => 'Jenis Kelamin',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                    ]
                ],
            ],
            'labels' => ['Laki-laki', 'Perempuan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
