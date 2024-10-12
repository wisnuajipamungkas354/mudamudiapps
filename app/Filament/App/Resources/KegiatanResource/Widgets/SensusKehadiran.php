<?php

namespace App\Filament\App\Resources\KegiatanResource\Widgets;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Presensi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class SensusKehadiran extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;
    protected static ?string $heading = 'Sensus Kehadiran';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '400px';
    public ?string $filter = '';

    protected function getFilters(): ?array
    {
        if (auth()->user()->roles[0]->name == 'MM Daerah') {
            return [
                'Desa' => 'Desa',
                'Kelompok' => 'Kelompok',
            ];
        } else {
            return [];
        }
    }

    public function getLabels()
    {
        $role  = auth()->user()->roles[0]->name;
        $labels = [];
        $ids = [];

        $filterChart = $this->filter;
        if ($role == "MM Daerah") {
            if ($filterChart == 'Desa') {
                $daerah = Daerah::query()->where('nm_daerah', auth()->user()->detail)->value('id');
                $labels = Desa::query()->where('daerah_id', $daerah)->pluck('nm_desa');
                $ids = Desa::query()->where('daerah_id', $daerah)->pluck('id');
            } else {
                $daerah = Daerah::query()->where('nm_daerah', auth()->user()->detail)->value('id');
                $desa = Desa::query()->where('daerah_id', $daerah)->pluck('id');

                foreach ($desa as $d) {
                    $labels[] = Kelompok::query()->where('desa_id', $d)->pluck('nm_kelompok')->toArray();
                    $ids[] = Kelompok::query()->where('desa_id', $d)->pluck('id')->toArray();
                }

                $labels = array_merge(...$labels);
                $ids = array_merge(...$ids);
            }
        } elseif ($role == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', auth()->user()->detail)->value('id');
            $labels = Kelompok::query()->where('desa_id', $desa)->pluck('nm_kelompok');
            $ids = Kelompok::query()->where('desa_id', $desa)->pluck('id');
        }
        return [$ids, $labels];
    }

    protected function getData(): array
    {
        [$ids, $labels] = $this->getLabels();

        $idKegiatan = $this->filters['kegiatan'];
        $sesi = intval($this->filters['sesi'] == null ? 1 : $this->filters['sesi']);
        $filterChart = $this->filter;
        $laki = [];
        $perempuan = [];
        $total = [];
        if (auth()->user()->roles[0]->name == 'MM Daerah') {
            if ($filterChart == 'Desa') {
                foreach ($ids as $id) {
                    $laki[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.desa_id', $id)->where('mudamudis.jk', 'L')->where('presensis.keterangan', 'Hadir')->count();
                    $perempuan[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.desa_id', $id)->where('mudamudis.jk', 'P')->where('presensis.keterangan', 'Hadir')->count();
                    $total[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.desa_id', $id)->where('presensis.keterangan', 'Hadir')->count();;
                }
            } elseif ($filterChart == 'Kelompok') {
                foreach ($ids as $id) {
                    $laki[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.kelompok_id', $id)->where('mudamudis.jk', 'L')->where('presensis.keterangan', 'Hadir')->count();
                    $perempuan[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.kelompok_id', $id)->where('mudamudis.jk', 'P')->where('presensis.keterangan', 'Hadir')->count();
                    $total[] = DB::table('presensis')
                        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                        ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                        ->where('mudamudis.kelompok_id', $id)->where('presensis.keterangan', 'Hadir')->count();;
                }
            }
        } elseif (auth()->user()->roles[0]->name == 'MM Desa') {
            foreach ($ids as $id) {
                $laki[] = DB::table('presensis')
                    ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                    ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                    ->where('mudamudis.kelompok_id', $id)->where('mudamudis.jk', 'L')->where('presensis.keterangan', 'Hadir')->count();
                $perempuan[] = DB::table('presensis')
                    ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                    ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                    ->where('mudamudis.kelompok_id', $id)->where('mudamudis.jk', 'P')->where('presensis.keterangan', 'Hadir')->count();
                $total[] = DB::table('presensis')
                    ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
                    ->where('presensis.kegiatan_id', $idKegiatan)->where('presensis.sesi', $sesi)
                    ->where('mudamudis.kelompok_id', $id)->where('presensis.keterangan', 'Hadir')->count();;
            }
        }


        return [
            'datasets' => [
                [
                    'label' => 'Laki-laki',
                    'data' => $laki,
                    'backgroundColor' => [
                        'rgba(0, 132, 255, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(89, 175, 255, 1)',
                    ],
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $perempuan,
                    'backgroundColor' => [
                        'rgba(255, 17, 0, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(255, 79, 79, 1)',
                    ],
                ],
                [
                    'label' => 'Total Hadir',
                    'data' => $total,
                    'backgroundColor' => [
                        'rgba(255, 166, 0, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(255, 205, 79, 1)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
