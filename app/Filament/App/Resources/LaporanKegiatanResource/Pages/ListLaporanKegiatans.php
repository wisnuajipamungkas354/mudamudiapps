<?php

namespace App\Filament\App\Resources\LaporanKegiatanResource\Pages;

use App\Filament\App\Resources\LaporanKegiatanResource;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLaporanKegiatans extends ListRecords
{
    protected static string $resource = LaporanKegiatanResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Laporan Kegiatan';
    }

    public function getUserRole()
    {
        $roleName = Auth::user()->getAppRoleName();
        $daerah = '';
        $desa = '';
        $kelompok = '';

        if ($roleName == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first('id');
        } elseif ($roleName == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
            $daerah = $desa ? Daerah::query()->where('id', $desa->daerah_id)->first('id') : null;
        } elseif ($roleName == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = $kelompok ? Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']) : null;
            $daerah = $desa ? Daerah::query()->where('id', $desa->daerah_id)->first('id') : null;
        }

        return [$roleName, $daerah, $desa, $kelompok];
    }

    public function getTabs(): array 
    {
        $role = $this->getUserRole();
        
        if ($role[0] == 'MM Daerah') {
            $listDesa = $role[1] ? Desa::query()->where('daerah_id', '=', $role[1]->id)->pluck('nm_desa')->toArray() : [];
            return [
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', $role[0])->where('detail_tingkatan', '=', auth()->user()->detail)),
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', 'MM Desa')->whereIn('detail_tingkatan', $listDesa)),
            ];
        } elseif ($role[0] == 'MM Desa') {
            $listKelompok = $role[2] ? Kelompok::query()->where('desa_id', '=', $role[2]->id)->pluck('nm_kelompok')->toArray() : [];
            return [
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', $role[0])->where('detail_tingkatan', '=', auth()->user()->detail)),
                'Kelompok' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', 'MM Kelompok')->whereIn('detail_tingkatan', $listKelompok))
            ];
        } else {
            return [
                'Kelompok' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', $role[0])->where('detail_tingkatan', '=', auth()->user()->detail)),
            ];
        }
    }
}
