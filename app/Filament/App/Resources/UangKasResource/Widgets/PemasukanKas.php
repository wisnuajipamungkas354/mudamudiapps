<?php

namespace App\Filament\App\Resources\UangKasResource\Widgets;

use App\Filament\App\Resources\UangKasResource\Pages\ListUangKas;
use App\Models\UangKas;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Request;

class PemasukanKas extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListUangKas::class;
    }

    protected function getStats(): array
    { 
        $pemasukan = $this->getPageTableQuery()->where('jenis_kas', 'Pemasukan')->sum('nominal');
        $pengeluaran = $this->getPageTableQuery()->where('jenis_kas', 'Pengeluaran')->sum('nominal');
        $totalPemasukan = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('jenis_kas', '=', 'Pemasukan')->sum('nominal');
        $totalPengeluaran = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('jenis_kas', '=', 'Pengeluaran')->sum('nominal');
        $totalKas = $totalPemasukan - $totalPengeluaran;

        return [
            Stat::make('Sisa Kas', 'Rp ' . number_format($totalKas))
            ->chart([1,1,1])
            ->color('info')
            ->icon('heroicon-o-banknotes'),
            Stat::make('Total Pemasukan', 'Rp ' . number_format($pemasukan))
            ->chart([1,1,1])
            ->color('success')
            ->icon('heroicon-o-arrow-trending-up'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($pengeluaran))
            ->chart([1,1,1])
            ->color('danger')
            ->icon('heroicon-o-arrow-trending-down'),
        ];
    }
}
