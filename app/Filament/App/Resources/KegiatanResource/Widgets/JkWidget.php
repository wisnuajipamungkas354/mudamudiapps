<?php

namespace App\Filament\App\Resources\KegiatanResource\Widgets;

use App\Models\Presensi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class JkWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Jenis Kelamin';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {

        $id = $this->filters['kegiatan'];
        $sesi = intval($this->filters['sesi']);
        $data = [];
        if ($id != null && $sesi == 0) {
            $data[0] = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $id)->where('presensis.sesi', 1)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'L')->count();
            $data[1] = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $id)->where('presensis.sesi', 1)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'P')->count();
        } elseif ($id != null && $sesi != 0) {
            $data[0] = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $id)->where('presensis.sesi', $sesi)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'L')->count();
            $data[1] = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $id)->where('presensis.sesi', $sesi)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'P')->count();
        } else {
            $data[0] = 0;
            $data[1] = 0;
        }
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
        return 'pie';
    }
}
