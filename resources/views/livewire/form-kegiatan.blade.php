<div class="flex flex-row justify-center items-center h-screen">
  <section class="flex flex-col justify-center bg-white p-10 rounded-md shadow-xl m-4 w-full md:w-2/4 lg:1/2">
    <h1 class="text-2xl text-center font-semibold my-3">{{ $this->kegiatan->nm_kegiatan }}</h1>
    <p><x-heroicon-o-map-pin class="inline" width="1.3rem" /> {{ $this->kegiatan->tempat_kegiatan }}</p>
    <p><x-heroicon-o-calendar class="inline" width="1.3rem"/> {{ strftime("%A, %d %B %Y", strtotime($this->kegiatan->waktu_mulai)) }}</p>
    <p><x-heroicon-o-clock class="inline" width="1.3rem"/> {{ date('H:i', strtotime($this->kegiatan->waktu_mulai)) }} s/d Selesai</p>
    <p><x-heroicon-o-users class="inline" width="1.3rem"/> {{ $this->kegiatan->kategori_peserta }}</p>
    
    <p class="text-center mt-3">Silahkan pilih dibawah ini :</p>
    <div class="flex flex-col gap-2 mt-5 w-full">
      <x-filament::button color="info" href="{{ Request::url() }}/hadir" tag="a" wire:navigate>Hadir</x-filament::button>
      <x-filament::button color="warning" href="{{ Request::url() }}/izin" tag="a" wire:navigate>Izin</x-filament::button>
    </div>
  </section>
</div>
