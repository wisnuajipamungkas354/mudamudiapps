<?php

namespace App\Livewire;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Presensi;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListIzinTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $kegiatan;

    public function mount($kegiatan) {
        $this->kegiatan = $kegiatan;
    }

    public function table(Table $table): Table
    {
    
        return $table
            ->query(Presensi::query()->where('kegiatan_id', '=', $this->kegiatan->id)->where('keterangan', '=', 'Izin')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->latest('presensis.updated_at'))
            ->headerActions([
                Action::make('refresh')
                    ->label('Refresh')
            ])
            ->columns([
                TextColumn::make('mudamudi.kelompok.nm_kelompok')
                    ->searchable(),
                TextColumn::make('mudamudi.nama')
                    ->searchable(),
                TextColumn::make('mudamudi.jk')
                    ->label('L/P'),
                TextColumn::make('status'),
                TextColumn::make('kategori_izin'),
                TextColumn::make('ket_izin')
                    ->label('Keterangan Izin')
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // 
            ])
            ->bulkActions([
                // ...
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum ada yang registrasi');
    }

    public function render()
    {
        return view('livewire.list-izin-table');
    }
}