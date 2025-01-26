<?php

namespace App\Filament\App\Resources\MudamudiappResource\Widgets;

use App\Filament\App\Resources\MudamudiappResource\Pages\ListMudamudiapps;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CounterStat extends BaseWidget
{

    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListMudamudiapps::class;
    }

    protected function getStats(): array
    {
        $total = $this->getPageTableQuery()->count();
        $l = $this->getPageTableQuery()->where('jk', 'L')->count();
        $p = $this->getPageTableQuery()->where('jk', 'P')->count();

        return [
            Stat::make('Total', $total)
            ->icon('heroicon-s-users')
            ->chart([1,1])
            ->chartColor('gray'),
            Stat::make('Laki-laki', $l)
            ->icon('heroicon-s-user')
            ->chart([1,1])
            ->chartColor('info'),
            Stat::make('Perempuan', $p)
            ->icon('heroicon-s-user')
            ->chart([1,1])
            ->chartColor('danger'),
        ];
    }
}
