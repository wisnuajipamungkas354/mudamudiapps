<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use App\Models\Mudamudi;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;

class SearchPesertaKegiatan extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];
    public $kegiatan;
    public $title = 'Presensi Kehadiran QR';
    
    public function mount(Kegiatan $kegiatan): void
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        $this->form->fill();
        $this->kegiatan = $kegiatan;

        if(Carbon::parse($this->kegiatan->waktu_mulai)->addMinutes(-30) >= Carbon::now()){
            abort(403, 'Mohon Maaf Presensi Belum Dibuka!');
        }

        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())){
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup!');
        }
    }
    
    public function form(Form $form): Form
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())){
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup!');
        }
        
        return $form
            ->schema([
                Select::make('search')
                    ->label('Cari Nama atau ID')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Mudamudi::where('nama', 'LIKE', "%{$search}%")->orWhere('id', 'LIKE', "%{$search}%")->limit(10)->pluck('nama', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Mudamudi::find($value)?->name)
                    ->live()
                    ->preload()
                    ->placeholder('Masukkan Nama atau ID kamu')
                    ->afterStateUpdated(function(Set $set, $state) {
                        $resultData = Mudamudi::with('kelompok')->where('id', '=', $state)->first();
                        $kelompok = $resultData?->getRelations('kelompok');
                        
                        $set('id', $state ?? null);
                        $set('nama', $resultData->nama ?? null);
                        $set('jk', $resultData?->jk == 'L' ? 'Laki-laki' : 'Perempuan');
                        $set('status', $resultData->status ?? null);
                        $set('kelompok', $kelompok['kelompok']->nm_kelompok ?? null);
                    }),
                TextInput::make('id')
                    ->label('ID')
                    ->placeholder('Terisi otomatis')
                    ->readOnly(),
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->placeholder('Terisi otomatis')
                    ->readOnly(),
                TextInput::make('jk')
                    ->label('Jenis Kelamin')
                    ->placeholder('Terisi otomatis')
                    ->readOnly(),
                TextInput::make('status')
                    ->label('Status')
                    ->placeholder('Terisi otomatis')
                    ->readOnly(),
                TextInput::make('kelompok')
                    ->label('Kelompok')
                    ->placeholder('Terisi otomatis')
                    ->readOnly(),
            ])
            ->statePath('data');
    }


    public function render()
    {
        return view('livewire.search-peserta-kegiatan');
    }
}
