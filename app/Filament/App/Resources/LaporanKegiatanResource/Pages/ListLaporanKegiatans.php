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
        // Mengambil Nama Role User
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first('id');
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
        }

        // nama role, id daerah, desa (id daerah, id desa), kelompok (id daerah, id desa, id kelompok) 
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }

    public function getTabs(): array 
    {
        $role = $this->getUserRole();
        
        if($role[0] == 'MM Daerah') {
            return [
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', $role[0])),
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('tingkatan_laporan', '=', 'MM Desa')),
            ];
        } elseif($role[0] == 'MM Desa') {
            $listKelompok = Kelompok::query()->where('desa_id', '=', $role[2]->id)->pluck('nm_kelompok')->toArray();
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
