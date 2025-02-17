<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use Livewire\Component;

class FormKegiatan extends Component
{
    public $kegiatan;
    public $title = 'Presensi Kegiatan';

    public function mount(Kegiatan $kegiatan) 
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        $this->kegiatan = $kegiatan;
        
        if(str_contains($this->kegiatan->kategori_peserta, 'Kustom Usia')){
            $strUsia = substr($this->kegiatan->kategori_peserta, 14);
            $this->kegiatan->kategori_peserta = 'Generus Usia ' . $strUsia . ' tahun';
        }

        if($this->kegiatan->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())) {
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup');
        }
    }

    public function render()
    {
        return view('livewire.form-kegiatan');
    }
}
