<?php

namespace App\Livewire;

use App\Filament\App\Resources\KegiatanResource\Pages\PresensiKegiatan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Js;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class ScanQrPresensi extends Component
{
    public $kegiatan;

    public function mount($kegiatan) {
        $this->kegiatan = $kegiatan;
    }

    #[Renderless]
    public function hadir($decodedText) {
        $id = substr($decodedText, 0, 8);
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

        if (DB::table('presensis')->where('kegiatan_id', $this->kegiatan->id)->where('mudamudi_id', $id)->exists()) {
            DB::table('presensis')->where('kegiatan_id', $this->kegiatan->id)->where('mudamudi_id', $id)->update([
                'keterangan' => 'Hadir',
                'kedatangan' => $kedatangan,
                'updated_at' => $now
            ]);
        } else {
            Presensi::create([
                'kegiatan_id' => $this->kegiatan->id,
                'mudamudi_id' => $id,
                'keterangan' => 'Hadir',
                'kedatangan' => $kedatangan,
            ]);
        }

        $this->dispatch('refresh-table')->to(PresensiKegiatan::class);
    }

    public function render()
    {
        return view('livewire.scan-qr-presensi');
    }
}
