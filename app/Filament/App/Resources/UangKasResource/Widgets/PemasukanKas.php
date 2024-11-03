<?php

namespace App\Filament\App\Resources\UangKasResource\Widgets;

use App\Models\UangKas;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PemasukanKas extends StatsOverviewWidget
{

    protected function getStats(): array
    { 
        $pemasukan = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('tahun', Carbon::now()->format('Y'))->where('bulan', Carbon::now()->format('m'))->where('jenis_kas', 'Pemasukan')->sum('nominal');
        $pengeluaran = UangKas::query()->where('role', auth()->user()->roles[0]->name)->where('tingkatan', auth()->user()->detail)->where('tahun', Carbon::now()->format('Y'))->where('bulan', Carbon::now()->format('m'))->where('jenis_kas', 'Pengeluaran')->sum('nominal');
        $total_kas = $pemasukan - $pengeluaran;
        return [
            Stat::make('Sisa Kas', 'Rp ' . number_format($total_kas))
            ->chart([1,1,1])
            ->color('info')
            ->description('Sisa kas bulan ini'),
            Stat::make('Total Pemasukan', 'Rp ' . number_format($pemasukan))
            ->chart([1,1,1])
            ->color('success')
            ->description('Pemasukan bulan ini'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($pengeluaran))
            ->chart([1,1,1])
            ->color('danger')
            ->description('Pengeluaran bulan ini'),
        ];
    }
}
