<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KehadiranStat extends BaseWidget
{
    public $data;

    protected function getStats(): array
    {
        $data = $this->data;
        $hadirPercent = round((($data->hadir_l + $data->hadir_p) / $data->total_peserta) * 100);
        $izinPercent = round((($data->izin_l + $data->izin_p) / $data->total_peserta) * 100);
        $alfaPercent = round((($data->alfa_l + $data->alfa_p) / $data->total_peserta) * 100);
        // dd($hadirPercent);

        return [
            Stat::make('Hadir', $hadirPercent . '%')
                ->chart([1,1])
                ->chartColor('success')
                ->description(($data->hadir_l + $data->hadir_p) . ' dari ' . $data->total_peserta . ' peserta')
                ->color('success'),
            Stat::make('Izin', $izinPercent . '%')
                ->chart([1,1])
                ->chartColor('warning')
                ->description(($data->izin_l + $data->izin_p) . ' dari ' . $data->total_peserta . ' peserta')
                ->color('warning'),
            Stat::make('Alfa', $alfaPercent . '%')
                ->chart([1,1])
                ->chartColor('danger')
                ->description(($data->alfa_l + $data->alfa_l) . ' dari ' . $data->total_peserta . ' peserta')
                ->color('danger'),
        ];
    }
}
