<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\KegiatanResource\Widgets\JkWidget;
use App\Filament\App\Resources\KegiatanResource\Widgets\KehadiranStat;
use App\Filament\App\Resources\KegiatanResource\Widgets\PresensiTableWidget;
use App\Filament\App\Resources\KegiatanResource\Widgets\SensusKehadiran;
use App\Filament\App\Resources\KegiatanResource\Widgets\TepatWaktuWidget;
use App\Filament\App\Widgets\StatusWidget;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Kegiatan;
use App\Models\SesiKegiatan;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\DB;

class RekapitulasiKegiatanDashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'rekapitulasi-kegiatan';
    protected static ?string $title = 'Rekapitulasi Kegiatan';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Presensi';
    protected static ?string $navigationLabel = 'Rekapitulasi';
    protected static ?int $navigationSort = 2;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options(function () {
                                if (auth()->user()->roles[0]->name == 'MM Daerah') {
                                    return [
                                        'MM Daerah' => 'MM Daerah',
                                        'MM Desa' => 'MM Desa',
                                        'MM Kelompok' => 'MM Kelompok'
                                    ];
                                } elseif (auth()->user()->roles[0]->name == 'MM Desa') {
                                    return [
                                        'MM Desa' => 'MM Desa',
                                        'MM Kelompok' => 'MM Kelompok'
                                    ];
                                } else {
                                    return [
                                        'MM Kelompok' => 'MM Kelompok'
                                    ];
                                }
                            })
                            ->searchable()
                            ->afterStateUpdated(function (Set $set) {
                                $set('tingkatan', null);
                                $set('kegiatan', null);
                                $set('sesi', null);
                            })
                            ->live()
                            ->preload()
                            ->default(null),
                        Select::make('tingkatan')
                            ->label('Tingkatan')
                            ->options(function (Get $get) {
                                if ($get('role') == 'MM Daerah') {
                                    return Daerah::query()->where('nm_daerah', auth()->user()->detail)->pluck('nm_daerah', 'nm_daerah');
                                } elseif ($get('role') == 'MM Desa') {
                                    if (auth()->user()->roles[0]->name == 'MM Daerah') {
                                        $idDaerah = Daerah::query()->where('nm_daerah', auth()->user()->detail)->value('id');
                                        return Desa::query()->where('daerah_id', $idDaerah)->pluck('nm_desa', 'nm_desa');
                                    } else {
                                        return Desa::query()->where('nm_desa', auth()->user()->detail)->pluck('nm_desa', 'nm_desa');
                                    }
                                } elseif ($get('role') == 'MM Kelompok') {
                                    if (auth()->user()->roles[0]->name == 'MM Daerah') {
                                        return Kelompok::query()->pluck('nm_kelompok', 'nm_kelompok');
                                    } elseif (auth()->user()->roles[0]->name == 'MM Desa') {
                                        $idDesa = Desa::query()->where('nm_desa', auth()->user()->detail)->value('id');
                                        return Kelompok::query()->where('desa_id', $idDesa)->pluck('nm_kelompok', 'nm_kelompok');
                                    } else {
                                        return Kelompok::query()->where('nm_kelompok', auth()->user()->detail)->pluck('nm_kelompok', 'nm_kelompok');
                                    }
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('kegiatan', null)),
                        Select::make('kegiatan')
                            ->label('Kegiatan')
                            ->options(function (Get $get) {
                                return Kegiatan::query()->where('tingkatan_kegiatan', $get('role'))->where('detail_tingkatan', $get('tingkatan'))->pluck('nm_kegiatan', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('sesi', null)),
                        Select::make('sesi')
                            ->label('Sesi')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->options(function (Get $get) {
                                return SesiKegiatan::query()->where('kegiatan_id', $get('kegiatan'))->pluck('nm_sesi', 'sesi');
                            })
                            ->visible(function (Get $get) {
                                $sesi = Kegiatan::query()->where('id', $get('kegiatan'))->value('jml_sesi');
                                if ($sesi < 2) {
                                    return false;
                                } else {
                                    return true;
                                };
                            })
                    ])
                    ->columns(3)
            ]);
    }


    public function getWidgets(): array
    {
        return [
            KehadiranStat::class,
            TepatWaktuWidget::class,
            JkWidget::class,
            SensusKehadiran::class,
            PresensiTableWidget::class,
        ];
    }
}
