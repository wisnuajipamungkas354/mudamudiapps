<?php

namespace App\Filament\App\Resources\MudamudiappResource\Pages;

use App\Filament\App\Resources\MudamudiappResource;
use App\Filament\App\Resources\MudamudiappResource\Widgets\CounterStat;
use App\Filament\Exports\MudamudiExporter;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\User;
use App\Models\Mudamudi;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListMudamudiapps extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = MudamudiappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus-circle'),
            Actions\ExportAction::make()
                ->label('Export')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->exporter(MudamudiExporter::class)
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Muda-Mudi';
    }

    public function getHeaderWidgets() :array {
        $total = '';
        $l = '';
        $p = '';

        return [
            CounterStat::class,
        ];
    }

    public function getTabs(): array
    {
        $role = $this->getUserRole();
        if ($role[0] == 'MM Daerah') {
            return [
                'Semua' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[1]->id))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->count()),
                'SMP' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[1]->id)->where('status', '=', 'Pelajar SMP'))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->where('status', '=', 'Pelajar SMP')->count()),
                'SMA/K' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[1]->id)->whereIn('status', ['Pelajar SMA', 'Pelajar SMK'])->latest())
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->whereIn('status', ['Pelajar SMA', 'Pelajar SMK'])->count()),
                'Mahasiswa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[1]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%')->latest())
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%')->count()),
                'Lepas Pelajar' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[1]->id)->whereNot('status', 'LIKE', 'Pelajar %'))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[1]->id)->whereNot('status', 'LIKE', 'Pelajar %')->count()),
            ];
        } elseif ($role[0] == 'MM Desa') {
             return [
                'Semua' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->count()),
                'SMP' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', '=', 'Pelajar SMP'))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', '=', 'Pelajar SMP')->count()),
                'SMA/K' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereIn('status', ['Pelajar SMA', 'Pelajar SMK']))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereIn('status', ['Pelajar SMA', 'Pelajar SMK'])->count()),
                'Mahasiswa' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%'))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%')->count()),
                'Lepas Pelajar' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'Pelajar %'))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'Pelajar %')->count()),
                'Se-Daerah' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id))
                    ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->count()),
            ];
        } elseif ($role[0] == 'MM Kelompok') {
            return [
                'Kelompok' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->count()),
                'Desa' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->count()),
                'Daerah' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('daerah_id', '=', $role[2]->daerah_id))
                ->badge(Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->count()),
            ];
        }
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
}
