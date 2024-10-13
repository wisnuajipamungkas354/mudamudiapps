<?php

namespace App\Filament\PengurusDaerah\Widgets;

use App\Models\Mudamudi;
use App\Models\PengurusSedaerah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengurus Se-Daerah', PengurusSedaerah::query()->count())
                    ->chart([1, 1, 1])
                    ->chartColor('info'),
            Stat::make('Total Muda-Mudi', Mudamudi::query()->count())
                    ->chart([1, 1, 1])
                    ->chartColor('success'),
        ];
    }
}
