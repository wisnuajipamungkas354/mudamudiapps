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

    protected function getStats(): array
    {

        $role = $this->getUserRole();
        if ($role[0] == 'MM Daerah') {
            return [
                Stat::make('Total Muda-Mudi', Mudamudi::query()->where('daerah_id', $role[1]->id)->count())
                    ->chart([4, 2, 8])
                    ->chartColor('success'),
                Stat::make('SMP', Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->where('status', '=', 'Pelajar SMP')->count())
                    ->chart([8, 5, 10])->chartColor('danger'),
                Stat::make('SMA/K', Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->where('status', '=', 'Pelajar SMA')->orWhere('daerah_id', '=', $role[1]->id)->where('status', '=', 'Pelajar SMK')->count())
                    ->chart([10, 8, 7])->chartColor('info'),
                Stat::make('Mahasiswa', Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->where('status', 'LIKE', 'Mahasiswa %')->orWhere('daerah_id', '=', $role[1]->id)->where('status', 'LIKE', 'Kuliah %')->count())
                    ->chart([8, 3, 4, 6])->chartColor('primary'),
                Stat::make('Lepas Pelajar', Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->whereNot('status', 'LIKE', 'Pelajar %')->count())
                    ->chart([8, 5, 4, 6])->chartColor('gray'),
                Stat::make('Siap Nikah', Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->where('siap_nikah', '=', 'Siap')->count())
                    ->chart([2, 5, 4, 6])->chartColor('warning'),
            ];
        } elseif ($role[0] == 'MM Desa') {
            return [
                Stat::make('Total Muda-Mudi', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->count())
                    ->chart([4, 2, 8])->chartColor('success'),
                Stat::make('SMP', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', '=', 'Pelajar SMP')->count())
                    ->chart([8, 5, 10])->chartColor('danger'),
                Stat::make('SMA/K', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', '=', 'Pelajar SMA')->orWhere('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', '=', 'Pelajar SMK')->count())
                    ->chart([10, 8, 7])->chartColor('info'),
                Stat::make('Mahasiswa', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', 'LIKE', 'Mahasiswa %')->orWhere('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', 'LIKE', 'Kuliah %')->count())
                    ->chart([8, 3, 4, 6])->chartColor('primary'),
                Stat::make('Lepas Pelajar', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'Pelajar %')->count())
                    ->chart([8, 5, 4, 6])->chartColor('gray'),
                Stat::make('Siap Nikah', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('siap_nikah', '=', 'Siap')->count())
                    ->chart([2, 5, 4, 6])->chartColor('warning'),
            ];
        } elseif ($role[0] == 'MM Kelompok') {
            return [
                Stat::make('Total Muda-Mudi', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->count())
                    ->chart([4, 2, 8])->chartColor('success'),
                Stat::make('SMP', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', '=', 'Pelajar SMP')->count())
                    ->chart([8, 5, 10])->chartColor('danger'),
                Stat::make('SMA/K', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'LIKE', 'Pelajar%')->whereNot('status', '=', 'Pelajar SMP')->count())
                    ->chart([10, 8, 7])->chartColor('info'),
                Stat::make('Mahasiswa', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'LIKE', 'Mahasiswa%')->orWhere('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'LIKE', 'Kuliah %')->count())
                    ->chart([8, 3, 4, 6])->chartColor('primary'),
                Stat::make('Lepas Pelajar', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->whereNot('status', 'LIKE', 'Pelajar%')->count())
                    ->chart([8, 5, 4, 6])->chartColor('gray'),
                Stat::make('Siap Nikah', Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('siap_nikah', '=', 'Siap')->count())
                    ->chart([2, 5, 4, 6])->chartColor('warning'),
            ];
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
