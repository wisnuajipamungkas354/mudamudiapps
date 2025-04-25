<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use App\Models\Mudamudi;
use App\Models\Presensi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Livewire\Component;

class FormPerizinanKegiatan extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $kegiatan;
    public $title = 'Form Perizinan';
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
        return $form
            ->schema([ 
                Select::make('search')
                    ->label('Cari Nama atau ID')
                    ->searchable()
                    ->live()
                    ->preload()
                    ->getSearchResultsUsing(function(string $search): array {
                        return Mudamudi::where('nama', 'LIKE', "%{$search}%")
                        ->orWhere('id', 'LIKE', "%{$search}%")
                        ->limit(10)->pluck('nama', 'id')->toArray();
                    })
                    ->getOptionLabelUsing(fn($value): ?string => Mudamudi::find($value)?->name)
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
                        $set('jk', $jk ?? null);
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
                Select::make('kategori_izin')
                    ->label('Kategori Izin')
                    ->placeholder('Pilih Kategori Izin')
                    ->live()
                    ->options([
                        'Sakit' => 'Sakit',
                        'Kerja' => 'Kerja',
                        'Kuliah' => 'Kuliah',
                        'Acara Sekolah' => 'Acara Sekolah',
                        'Acara Keluarga' => 'Acara Keluarga',
                        'Acara Mendesak' => 'Acara Mendesak',
                    ])
                    ->required(),
                Textarea::make('ket_izin')
                    ->label('Keterangan Izin')
                    ->placeholder(function(Get $get) {
                        $result = '';
                        switch($get('kategori_izin')) {
                            case 'Sakit' :
                                $result = 'Sebutkan nama penyakit (Demam, Tipes, dll)';
                                break;
                            case 'Kerja' :
                                $result = 'Lembur, Family Gathering, Kerja Sistem Off, dll';
                                break;
                            case 'Kuliah' :
                                 $result = 'Ada matkul, UAS, Acara Fakultas dll';
                                 break;
                            case 'Acara Sekolah' :
                                $result = 'Study Tour, Rekreasi, Ekstrakurikuler';
                                break;
                            case 'Acara Keluarga' :
                                $result = 'Nikahan Sepupu, Ada keluarga yang meninggal, dll';
                                break;
                            case 'Acara Mendesak' :
                                $result = 'Ada tamu, Renovasi rumah, dll';
                                break;
                        }

                        return $result;
                    })
                    ->required()
            ])
            ->statePath('data');
    }

    public function create() {
        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())){
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup!');
        }

        $kategoriPeserta = array_slice($this->kegiatan->kategori_peserta, 1);
        if(!Mudamudi::query()->where('id', $this->data['id'])->whereIn('status', $kategoriPeserta)->exists()) {
            return Notification::make('fail_notification')
                ->title('Form Perizinan Gagal!')
                ->body('Kamu tidak termasuk dalam kategori peserta dalam acara ini, jadi tidak perlu izin yaa!')
                ->danger()
                ->color('danger')
                ->seconds(10)
                ->send();
        }

        
        $formData = [
            'kegiatan_id' => $this->kegiatan->id,
            'mudamudi_id' => $this->data['id'],
            'keterangan' => 'Izin',
            'kedatangan' => 'Tidak Datang',
            'kategori_izin' => $this->data['kategori_izin'],
            'ket_izin' => $this->data['ket_izin'],
        ];

        if(Presensi::where('kegiatan_id', '=', $this->kegiatan->id)->where('mudamudi_id', '=', $this->data['id'])->exists()) {
            Presensi::where('kegiatan_id', '=', $this->kegiatan->id)->where('mudamudi_id', '=', $this->data['id'])->update($formData);
        } else {
            Presensi::create($formData);
        }

        redirect('/presensi-mudamudi/' . $this->kegiatan->id);

        Notification::make('success_notification')
        ->title('Form Perizinan Berhasil Dikirim!')
        ->body('Semoga Alloh memberikan kesehatan, kelancaran & kebarokahan!')
        ->success()
        ->color('success')
        ->seconds(6)
        ->send();
    }

    public function render()
    {
        return view('livewire.form-perizinan-kegiatan');
    }
}
