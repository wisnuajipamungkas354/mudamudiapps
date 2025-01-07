<?php

namespace App\Filament\App\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class StatusWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Status Lepas Pelajar';
    protected int | string | array $columnSpan = 'full';
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
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first('id');
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
        }

        // nama role, id daerah, desa (id daerah, id desa), kelompok (id daerah, id desa, id kelompok) 
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }

    public function categoriesCounter($data) {
        $tenagaSB = $data->filter(fn($val, $key) => str_contains($val, 'Tenaga'))->count();

        $mahasiswa = $data->filter(fn($val, $key) => str_contains($val, 'Mahasiswa'))->count();
        
        $kuliahKerja = $data->filter(fn($val, $key) => str_contains($val, 'Kuliah'))->count();

        $wirausaha = $data->filter(fn($val, $key) => str_contains($val, 'Wirausaha') )->count();

        $karyawan = $data->filter(fn($val, $key) => str_contains($val, 'Karyawan'))->count();

        $pencaker = $data->filter(fn($val, $key) => str_contains($val, 'Pencari'))->count();

        return [
            'Tenaga SB' => $tenagaSB,
            'Mahasiswa' => $mahasiswa,
            'Kuliah Kerja' => $kuliahKerja,
            'Wirausaha' => $wirausaha,
            'Karyawan' => $karyawan,
            'Pencaker' => $pencaker,
        ];
    }

    protected function getData(): array
    {
        $role = $this->getUserRole();
        $dataMumi = '';
        $dataChart = [];

        if ($role[0] == 'MM Daerah') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[1]->id)->get();
        } elseif ($role[0] == 'MM Desa') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->get();
        } elseif ($role[0] == 'MM Kelompok') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', $role[3]->id)->get();
        }

        $countedData = $this->categoriesCounter($dataMumi);

        $dataChart[0] = $countedData['Pencaker'];
        $dataChart[1] = $countedData['Karyawan'];
        $dataChart[2] = $countedData['Wirausaha'];
        $dataChart[3] = $countedData['Kuliah Kerja'];
        $dataChart[4] = $countedData['Mahasiswa'];
        $dataChart[5] = $countedData['Tenaga SB'];

        return [
            'datasets' => [
                [
                    'label' => 'Total Muda-Mudi',
                    'axis' => 'y',
                    'data' => $dataChart,
                    'backgroundColor' => [
                        'rgb(82, 157, 255)',
                        'rgb(0, 209, 66)',
                        'rgb(255, 139, 15)',
                        'rgb(255, 53, 53)',
                        'rgb(255, 33, 137)',
                        'rgb(123, 0, 205)',
                        
                    ],
                    'borderWidth' => '0',
                ],
            ],
            'labels' => ['Pencari Kerja', 'Karyawan', 'Wirausaha', 'Kuliah Kerja', 'Mahasiswa', 'Tenaga SB'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return ['indexAxis' => 'y'];
    }
}
