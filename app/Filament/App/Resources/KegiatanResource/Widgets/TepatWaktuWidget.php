<?php

namespace App\Filament\App\Resources\KegiatanResource\Widgets;

use App\Models\Kegiatan;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TepatWaktuWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Ketepatan Waktu';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $id = $this->filters['kegiatan'];
        $sesi = intval($this->filters['sesi']);

        $data = [];
        if ($id != null && $sesi == 0) {
            $data[0] = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Hadir')->where('kedatangan', 'In Time')->count();
            $data[1] = DB::table('presensis')->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Hadir')->where('kedatangan', 'On Time')->count();
            $data[2] = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Hadir')->where('kedatangan', 'Overtime')->count();
        } elseif ($id != null && $sesi != 0) {
            $data[0] = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Hadir')->where('kedatangan', 'In Time')->count();
            $data[1] = DB::table('presensis')->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Hadir')->where('kedatangan', 'On Time')->count();
            $data[2] = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Hadir')->where('kedatangan', 'Overtime')->count();
        } else {
            $data[0] = 0;
            $data[1] = 0;
            $data[2] = 0;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Jenis Kelamin',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(9, 227, 27)',
                        'rgb(227, 9, 9)',
                    ],
                ],
            ],
            'labels' => ['In-Time', 'On-Time', 'Over-Time'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
