<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use Livewire\Component;

class FormKegiatan extends Component
{
    public $kegiatan;
    public $title = 'Presensi Kegiatan';
    public $peserta = '';

    public function mount(Kegiatan $kegiatan) 
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        $this->kegiatan = $kegiatan;
        $peserta = '';

        if($this->kegiatan->kategori_peserta[0] === 'age'){
            $strUsia = $this->kegiatan->kategori_peserta[1] . ' s/d ' . $this->kegiatan->kategori_peserta[2];
            $this->peserta = 'Generus Usia ' . $strUsia . ' tahun';
        } elseif ($this->kegiatan->kategori_peserta[0] ===  'all') {
            $this->peserta = 'Seluruh Muda-Mudi';
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

        if(strtotime($this->kegiatan->waktu_selesai) <= strtotime(now())) {
            abort(403, 'Mohon Maaf Presensi Sudah Ditutup');
        }
    }

    public function render()
    {
        return view('livewire.form-kegiatan');
    }
}
