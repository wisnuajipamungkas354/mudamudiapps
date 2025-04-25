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
        
        if($this->kegiatan->kategori_peserta[0] === 'age'){
            $strUsia = $this->kegiatan->kategori_peserta[1] . ' s/d ' . $this->kegiatan->kategori_peserta[2];
            $this->kegiatan->kategori_peserta = 'Generus Usia ' . $strUsia . ' tahun';
        } elseif ($this->kegiatan->kategori_peserta[0] ===  'all') {
            $this->kegiatan->kategori_peserta = 'Seluruh Muda-Mudi';
        } elseif($this->kegiatan->kategori_peserta[0] === 'category') {
            $len = count($this->kegiatan->kategori_peserta);
            $strKategori = '';
            $newKategori = array_slice($this->kegiatan->kategori_peserta, 1);

            $this->kegiatan->kategori_peserta = $newKategori;        
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
