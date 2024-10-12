<?php

namespace App\Filament\App\Resources\KegiatanResource\Widgets;

use App\Models\Presensi;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KehadiranStat extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $id = $this->filters['kegiatan'];
        $sesi = intval($this->filters['sesi']);

        if ($id != null && $sesi != 0) {
            $hadir = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Hadir')->count();
            $izin = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Izin')->count();
            $alfa = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->where('keterangan', 'Alfa')->count();
            $total = Presensi::query()->where('kegiatan_id', $id)->where('sesi', $sesi)->count();
            if ($total != 0) {
                $persenHadir = round((float)$hadir / $total * 100) . '%';
                $persenIzin = round((float)$izin / $total * 100) . '%';
                $persenAlfa = round((float)$alfa / $total * 100) . '%';
            } else {
                $persenHadir = 0;
                $persenIzin = 0;
                $persenAlfa = 0;
            }
        } elseif ($id != null && $sesi == 0) {
            $hadir = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Hadir')->count();
            $izin = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Izin')->count();
            $alfa = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->where('keterangan', 'Alfa')->count();
            $total = Presensi::query()->where('kegiatan_id', $id)->where('sesi', 1)->count();
            if ($total != 0) {
                $persenHadir = round((float)$hadir / $total * 100) . '%';
                $persenIzin = round((float)$izin / $total * 100) . '%';
                $persenAlfa = round((float)$alfa / $total * 100) . '%';
            } else {
                $persenHadir = 0;
                $persenIzin = 0;
                $persenAlfa = 0;
            }
        } else {
            $hadir = 0;
            $izin = 0;
            $alfa = 0;
            $total = 0;
        }

        if ($id != null) {
            return [
                Stat::make('Kehadiran', $hadir)
                    ->chart([1, 1, 1, 1])
                    ->color('success')
                    ->description($persenHadir . ' dari total peserta')
                    ->descriptionIcon('heroicon-o-check-circle', IconPosition::Before),
                Stat::make('Izin', $izin)
                    ->chart([1, 1, 1, 1])
                    ->color('warning')
                    ->description($persenIzin . ' dari total peserta')
                    ->descriptionIcon('heroicon-o-information-circle', IconPosition::Before),
                Stat::make('Tanpa Keterangan', $alfa)
                    ->chart([1, 1, 1, 1])
                    ->color('danger')
                    ->description($persenAlfa . ' dari total peserta')
                    ->descriptionIcon('heroicon-o-x-circle', IconPosition::Before),
            ];
        } else {
            return [
                Stat::make('Kehadiran', '0')
                    ->chart([1, 1, 1, 1])
                    ->color('success'),
                Stat::make('Izin', '0')
                    ->chart([1, 1, 1, 1])
                    ->color('warning'),
                Stat::make('Tanpa Keterangan', '0')
                    ->chart([1, 1, 1, 1])
                    ->color('danger'),
            ];
        }
    }
}
