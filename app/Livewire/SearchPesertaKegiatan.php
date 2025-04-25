<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use App\Models\Mudamudi;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SearchPesertaKegiatan extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];
    public $kegiatan;
    public $title = 'Presensi Kehadiran';
    public $peserta = '';
    
    public function mount(Kegiatan $kegiatan): void
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        $this->form->fill();
        $this->kegiatan = $kegiatan;
        $peserta = '';
        
        if($this->kegiatan->kategori_peserta[0] === 'age'){
            $strUsia = $this->kegiatan->kategori_peserta[1] . ' s/d ' . $this->kegiatan->kategori_peserta[2];
            $this->peserta = 'Generus Usia ' . $strUsia . ' tahun';
        } elseif ($this->kegiatan->kategori_peserta[0] ===  'all') {
            $this->peserta = 'Seluruh Muda-mudi';
        } elseif($this->kegiatan->kategori_peserta[0] === 'category') {
            $length = count($this->kegiatan->kategori_peserta);
            for($i = 0; $i < $length; $i++) {
                if($i == 0) $peserta = '';
                elseif($i == $length - 1) $peserta .= ' dan ' . $this->kegiatan->kategori_peserta[$i];
                elseif($i == 1) $peserta .= $this->kegiatan->kategori_peserta[$i];
                else $peserta .= ', ' . $this->kegiatan->kategori_peserta[$i];
            }
            $this->peserta = $peserta;  
        }

        if(Carbon::parse($this->kegiatan->waktu_mulai)->addMinutes(-30) >= Carbon::now()) {
            abort(403, 'Mohon Maaf Presensi Belum Dibuka!');
        }

        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())) {
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
                    ->getSearchResultsUsing(function (string $search): array {
                        return Mudamudi::where('nama', 'LIKE', "%{$search}%")
                            ->orWhere('id', 'LIKE', "%{$search}%")
                            ->limit(10)->pluck('nama', 'id')->toArray();
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => Mudamudi::find($value)?->name)
                    ->live()
                    ->preload()
                    ->placeholder('Masukkan Nama atau ID kamu')
                    ->afterStateUpdated(function(Set $set, $state) {
                        $resultData = Mudamudi::with('kelompok')->where('id', '=', $state)->first();
                        $kelompok = $resultData?->getRelations('kelompok');
                        $jk = '';

                        if($resultData?->jk == 'L') {
                            $jk = 'Laki-laki';
                        } elseif($resultData?->jk == 'P') {
                            $jk = 'Perempuan';
                        }
                                                
                        $set('id', $state ?? null);
                        $set('nama', $resultData->nama ?? null);
                        $set('jk', $jk);
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

    public function hadirAction() :void {
        $dataHadir = $this->form->getState();
        
        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())){
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup!');
        }

        $now = Carbon::now();
        $waktuKegiatan = '';
        $onTime = '';
        $kedatangan = '';

        // Penentu Waktu Mulai Kegiatan
        $waktuKegiatan = Carbon::parse($this->kegiatan->waktu_mulai);
        $onTime = Carbon::parse($this->kegiatan->waktu_mulai)->addMinutes(15);

        // Penentuan Kategori Kedatangan
        if ($now < $waktuKegiatan) {
            $kedatangan = 'In Time';
        } elseif ($waktuKegiatan <= $now && $now <= $onTime) {
            $kedatangan = 'On Time';
        } elseif ($onTime < $now) {
            $kedatangan = 'Overtime';
        }

        if (DB::table('presensis')->where('kegiatan_id', $this->kegiatan->id)->where('mudamudi_id', $dataHadir['id'])->exists()) {
            DB::table('presensis')->where('kegiatan_id', $this->kegiatan->id)->where('mudamudi_id', $dataHadir['id'])->update([
                'keterangan' => 'Hadir',
                'kedatangan' => $kedatangan,
                'kategori_izin' => null,
                'ket_izin' => null,
                'updated_at' => $now
            ]);
        } else {
            Presensi::create([
                'kegiatan_id' => $this->kegiatan->id,
                'mudamudi_id' => $dataHadir['id'],
                'keterangan' => 'Hadir',
                'kedatangan' => $kedatangan,
            ]);
        }

        redirect('/presensi-mudamudi/' . $this->kegiatan->id);

        Notification::make('success_notification')
        ->title('Presensi Berhasil!')
        ->body('Alhamdulillah Jazakumullohu Khoiro! Silahkan mengikuti kegiatan hingga selesai. Semoga sukses lancar & barokah!')
        ->success()
        ->color('success')
        ->seconds(6)
        ->send();
    }


    public function render()
    {
        return view('livewire.search-peserta-kegiatan');
    }
}
