<?php

namespace App\Filament\App\Resources\PengurusAppResource\Pages;

use App\Filament\App\Resources\PengurusAppResource;
use App\Filament\Exports\PengurusExporter;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Pengurus;
use Closure;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListPengurusApps extends ListRecords
{
    protected static string $resource = PengurusAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus-circle'),
            Actions\ExportAction::make()
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->exporter(PengurusExporter::class)
                ->chunkSize(250),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Pengurus Muda-Mudi';
    }

    public function getTableModelLabel(): ?string
    {
        return 'Data Pengurus';
    }

    public function getUserRole()
    {
        $roleName = Auth::user()->getAppRoleName();
        $daerah = '';
        $desa = '';
        $kelompok = '';

        if ($roleName == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first('nm_daerah');
        } elseif ($roleName == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['nm_desa', 'daerah_id']);
            $daerah = $desa ? Daerah::query()->where('id', $desa->daerah_id)->first('nm_daerah') : null;
        } elseif ($roleName == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['nm_kelompok', 'desa_id']);
            $desa = $kelompok ? Desa::query()->where('id', '=', $kelompok->desa_id)->first(['nm_desa', 'daerah_id']) : null;
            $daerah = $desa ? Daerah::query()->where('id', $desa->daerah_id)->first('nm_daerah') : null;
        }

        return [$roleName, $daerah, $desa, $kelompok];
    }

    public function getTabs(): array
    {
        $role = $this->getUserRole();
        $baseQueryCount = $this->getResource()::getEloquentQuery()->count();

        if ($role[0] == 'MM Daerah') {
            return [
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[1]->nm_daerah))
                    ->badge(Pengurus::query()->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[1]->nm_daerah)->count()),
                'Semua' => Tab::make()
                    ->badge($baseQueryCount),
            ];
        } elseif ($role[0] == 'MM Desa') {
            return [
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[2]->nm_desa))
                    ->badge(Pengurus::query()->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[2]->nm_desa)->count()),
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', 'MM Daerah')->where('nm_tingkatan', '=', $role[1]->nm_daerah))
                    ->badge(Pengurus::query()->where('role', '=', 'MM Daerah')->where('nm_tingkatan', '=', $role[1]->nm_daerah)->count()),
                'Semua' => Tab::make()
                    ->badge($baseQueryCount),
            ];
        } elseif ($role[0] == 'MM Kelompok') {
            return [
                'Kelompok' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[3]->nm_kelompok))
                    ->badge(Pengurus::query()->where('role', '=', $role[0])->where('nm_tingkatan', '=', $role[3]->nm_kelompok)->count()),
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', 'MM Desa')->where('nm_tingkatan', '=', $role[2]->nm_desa))
                    ->badge(Pengurus::query()->where('role', '=', 'MM Desa')->where('nm_tingkatan', '=', $role[2]->nm_desa)->count()),
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('role', '=', 'MM Daerah')->where('nm_tingkatan', '=', $role[1]->nm_daerah))
                    ->badge(Pengurus::query()->where('role', '=', 'MM Daerah')->where('nm_tingkatan', '=', $role[1]->nm_daerah)->count()),
                'Semua' => Tab::make()
                    ->badge($baseQueryCount),
            ];
        }

        return [];
    }

    // public function isTableRecordSelectable(): ?Closure
    // {
    //     return function (Model $record): bool {
    //         if ($record->role === 'MM Kelompok' && $record->nm_tingkatan == auth()->user()->detail) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     };
    // }
}
