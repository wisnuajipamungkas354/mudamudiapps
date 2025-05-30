<?php

namespace App\Filament\App\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SensusChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Sensus Muda-Mudi';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '400px';

    public static function canView(): bool
    {
        $role = Auth::user()->roles;
        if ($role[0]->name == 'MM Kelompok') {
            return false;
        } else {
            return true;
        }
    }

    public function getUserRole()
    {
        // Mengambil Nama Role User
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first('id');
            $desa = Desa::query()->where('daerah_id', '=', $daerah->id)->get(['id', 'nm_desa']);
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
            $kelompok = Kelompok::query()->where('desa_id', $desa->id)->get(['id', 'nm_kelompok']);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
        }

        // nama role, id daerah, desa (id daerah, id desa), kelompok (id daerah, id desa, id kelompok) 
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }

    protected function getData(): array
    {
        $role = $this->getUserRole();
        $laki = [];
        $perempuan = [];
        $total = [];
        $labels = [];

        if ($role[0] == 'MM Daerah') {
            foreach ($role[2] as $id) {
                $laki[] = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('desa_id', $id->id)->where('jk', 'L')->count();
                $perempuan[] = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('desa_id', $id->id)->where('jk', 'P')->count();
                $total[] = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('desa_id', $id->id)->count();
                $labels[] = $id->nm_desa;
            }
        } elseif ($role[0] == 'MM Desa') {
            foreach ($role[3] as $id) {
                $laki[] = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', $id->id)->where('jk', 'L')->count();
                $perempuan[] = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', $id->id)->where('jk', 'P')->count();
                $total[] = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', $id->id)->count();
                $labels[] = $id->nm_kelompok;
            }
        }
        return [
            'datasets' => [
                [
                    'label' => 'Laki-laki',
                    'data' => $laki,
                    'backgroundColor' => [
                        'rgba(0, 132, 255, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(89, 175, 255, 1)',
                    ],
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $perempuan,
                    'backgroundColor' => [
                        'rgba(255, 17, 0, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(255, 79, 79, 1)',
                    ],
                ],
                [
                    'label' => 'Total Muda-Mudi',
                    'data' => $total,
                    'backgroundColor' => [
                        'rgba(255, 166, 0, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(255, 205, 79, 1)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
