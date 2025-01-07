<?php

namespace App\Filament\App\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Pengurus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CountWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '10s';

    public function categoriesCounter($data) {
        $all = $data->count();

        $smp = $data->where('status', '=', 'Pelajar SMP')->count();

        $smak = $data->filter(function($value, $key) {
            return $value['status'] == 'Pelajar SMA' || $value['status'] == 'Pelajar SMK';
        })->count();

        $mahasiswa = $data->filter(function($value, $key) {
            return str_contains($value['status'], 'Mahasiswa') || str_contains($value['status'], 'Kuliah');
        })->count();

        $lepasPelajar = $data->where('status', '!=', 'Pelajar SMA')->where('status', '!=', 'Pelajar SMK')->where('status', '!=', 'Pelajar SMP')->count();

        $siapNikah = $data->where('siap_nikah', '=', 'Siap')->count();

        return [
            'All' => $all,
            'SMP' => $smp,
            'SMA/K' => $smak,
            'Mahasiswa' => $mahasiswa,
            'Lepas Pelajar' => $lepasPelajar,
            'Siap Nikah' => $siapNikah,
        ];
    }

    protected function getStats(): array
    {
        $role = $this->getUserRole();
        $dataMumi = '';

        if ($role[0] == 'MM Daerah') {
            $dataMumi = Mudamudi::query()->where('daerah_id', $role[1]->id)->get();
        } elseif ($role[0] == 'MM Desa') {
            $dataMumi = Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->get();
        } elseif ($role[0] == 'MM Kelompok') {
            $dataMumi = Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->get();
        }
        
        $countedData = $this->categoriesCounter($dataMumi);

        return [
            Stat::make('Total Muda-Mudi', $countedData['All'])
                ->chart([4, 2, 8])
                ->chartColor('success'),
            Stat::make('SMP', $countedData['SMP'])
                ->chart([8, 5, 10])->chartColor('danger'),
            Stat::make('SMA/K', $countedData['SMA/K'])
                ->chart([10, 8, 7])->chartColor('info'),
            Stat::make('Mahasiswa', $countedData['Mahasiswa'])
                ->chart([8, 3, 4, 6])->chartColor('primary'),
            Stat::make('Lepas Pelajar', $countedData['Lepas Pelajar'])
                ->chart([8, 5, 4, 6])->chartColor('gray'),
            Stat::make('Siap Nikah', $countedData['Siap Nikah'])
                ->chart([2, 5, 4, 6])->chartColor('warning'),
        ];
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
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
        }

        // nama role, id daerah, desa (id daerah, id desa), kelompok (id daerah, id desa, id kelompok) 
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }
}
