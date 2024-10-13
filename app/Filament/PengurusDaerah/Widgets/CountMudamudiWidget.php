<?php

namespace App\Filament\PengurusDaerah\Widgets;

use App\Models\Mudamudi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CountMudamudiWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Muda-Mudi', Mudamudi::query()->count())
                    ->chart([4, 2, 8])
                    ->chartColor('success'),
        ];
    }
}
