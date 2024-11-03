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

    protected function getData(): array
    {
        $role = $this->getUserRole();
        $data = [];
        if ($role[0] == 'MM Daerah') {
            $data[0] = Mudamudi::query()->where('daerah_id', $role[1])->where('status', 'Pelajar SMP')->count();
            $data[1] = Mudamudi::query()->where('daerah_id', $role[1])->where('status', 'Pelajar SMA')->orWhere('daerah_id', $role[1])->where('status', 'Pelajar SMK')->count();
            $data[2] = Mudamudi::query()->where('daerah_id', $role[1])->whereNot('status', 'LIKE', 'Pelajar %')->count();
        } elseif ($role[0] == 'MM Desa') {
            $data[0] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('status', 'Pelajar SMP')->count();
            $data[1] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('status', 'Pelajar SMA')->orWhere('daerah_id', $role[2])->where('desa_id', $role[2])->where('status', 'Pelajar SMK')->count();
            $data[2] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->whereNot('status', 'LIKE', 'Pelajar %')->count();
        } elseif ($role[0] == 'MM Kelompok') {
            $data[0] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('kelompok_id', $role[3])->where('status', 'Pelajar SMP')->count();
            $data[1] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('kelompok_id', $role[3])->where('status', 'Pelajar SMA')->orWhere('daerah_id', $role[1])->where('desa_id', $role[2])->where('kelompok_id', $role[3])->where('status', 'Pelajar SMK')->count();
            $data[2] = Mudamudi::query()->where('daerah_id', $role[1])->where('desa_id', $role[2])->where('kelompok_id', $role[3])->whereNot('status', 'LIKE', 'Pelajar %')->count();
        }
        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
                    ]
                ],
            ],
            'labels' => ['SMP', 'SMA/K', 'Lepas Pelajar'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
