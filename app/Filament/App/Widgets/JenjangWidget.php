<?php

namespace App\Filament\App\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class JenjangWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Jenjang';
    protected static ?string $maxHeight = '400px';

    public function getUserRole()
    {
        // Mengambil Nama Role User
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->value('id');
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->value('id');
            $daerah = Desa::query()->where('id', '=', $desa)->value('daerah_id');
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->value('id');
            $desa = Kelompok::query()->where('id', '=', $kelompok)->value('desa_id');
            $daerah = Desa::query()->where('id', '=', $desa)->value('daerah_id');
        }

        // nama role, id daerah, id desa, id kelompok
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }

    public function categoriesCounter($data) {
        $all = $data->count();

        $smp = $data->where('status', '=', 'Pelajar SMP')->count();

        $smak = $data->filter(function($value, $key) {
            return $value['status'] == 'Pelajar SMA' || $value['status'] == 'Pelajar SMK';
        })->count();

        $lepasPelajar = $data->where('status', '!=', 'Pelajar SMA')->where('status', '!=', 'Pelajar SMK')->where('status', '!=', 'Pelajar SMP')->count();

        return [
            'All' => $all,
            'SMP' => $smp,
            'SMA/K' => $smak,
            'Lepas Pelajar' => $lepasPelajar,
        ];
    }

    protected function getData(): array
    {
        $role = $this->getUserRole();
        $dataMumi = '';
        $dataChart = [];

        if ($role[0] == 'MM Daerah') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[1])->get();
        } elseif ($role[0] == 'MM Desa') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->get();
        } elseif ($role[0] == 'MM Kelompok') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('kelompok_id', $role[3])->get();
        }
        
        $countedData = $this->categoriesCounter($dataMumi);

        $dataChart[0] = round(($countedData['SMP'] / $countedData['All']) * 100);
        $dataChart[1] = round(($countedData['SMA/K'] / $countedData['All']) * 100);
        $dataChart[2] = round(($countedData['Lepas Pelajar'] / $countedData['All']) * 100);

        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => $dataChart,
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
                    ]
                ],
            ],
            'labels' => ['SMP (%)', 'SMA/K (%)', 'Lepas Pelajar (%)'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
