<?php

namespace App\Filament\App\Resources\UangKasResource\Pages;

use App\Filament\App\Resources\UangKasResource;
use App\Filament\App\Resources\UangKasResource\Widgets\PemasukanKas;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ManageUangKas extends ManageRecords
{

    protected static string $resource = UangKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data')
            ->mutateFormDataUsing(function(array $data): array {
                $data['role'] = auth()->user()->roles[0]->name;
                $data['tingkatan'] = auth()->user()->detail;
                $data['tahun'] = Carbon::parse($data['tgl'])->format('Y');
                $data['bulan'] = Carbon::parse($data['tgl'])->format('m');

                return $data;
            }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PemasukanKas::class
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Data Uang Kas";
    }
}
