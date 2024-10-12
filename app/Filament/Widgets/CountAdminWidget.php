<?php

namespace App\Filament\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Pengurus;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CountAdminWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Muda-Mudi', Mudamudi::count())
                ->chart([4, 2, 8])
                ->chartColor('success'),
            Stat::make('Total Pengurus Muda-Mudi', Pengurus::count())
                ->chart([8, 5, 4, 6])->chartColor('primary'),
            Stat::make('Total User Sistem', User::count())
                ->chart([8, 5, 4, 6])->chartColor('gray'),
            Stat::make('Total Daerah', Daerah::count())
                ->chart([8, 5, 10])->chartColor('danger'),
            Stat::make('Total Desa', Desa::count())
                ->chart([10, 8, 7])->chartColor('info'),
            Stat::make('Total Kelompok', Kelompok::count())
                ->chart([8, 3, 4, 6])->chartColor('warning'),
        ];
    }
}
