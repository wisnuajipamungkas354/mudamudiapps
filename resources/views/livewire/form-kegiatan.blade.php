<div class="flex flex-row items-center justify-center h-screen">
  <x-slot:title>{{ $this->title }}</x-slot:title>
  <section class="flex flex-col justify-center w-full p-10 m-4 bg-white rounded-md shadow-xl md:w-2/4 lg:1/2">
    <h1 class="my-3 text-2xl font-semibold text-center">{{ $this->kegiatan->nm_kegiatan }}</h1>
    <p><x-heroicon-o-map-pin class="inline" width="1.3rem" /> {{ $this->kegiatan->tempat_kegiatan }}</p>
    <p><x-heroicon-o-calendar class="inline" width="1.3rem"/> {{ strftime("%A, %d %B %Y", strtotime($this->kegiatan->waktu_mulai)) }}</p>
    <p><x-heroicon-o-clock class="inline" width="1.3rem"/> {{ date('H:i', strtotime($this->kegiatan->waktu_mulai)) }} s/d Selesai</p>
    <p><x-heroicon-o-users class="inline" width="1.3rem"/> {{ $this->kegiatan->kategori_peserta[1] }}</p>
    
    <p class="mt-3 text-center">Silahkan pilih dibawah ini :</p>
    <div class="flex flex-col w-full gap-2 mt-5">
      @if(Carbon\Carbon::parse($this->kegiatan->waktu_mulai)->addMinutes(-30) <= Carbon\Carbon::now())
        <x-filament::button color="info" href="{{ Request::url() }}/hadir" tag="a" wire:navigate>Hadir</x-filament::button>
      @else
        <x-filament::button color="info" href="{{ Request::url() }}/hadir" tag="a" wire:navigate disabled>Hadir</x-filament::button>
      @endif
      <x-filament::button color="warning" href="{{ Request::url() }}/izin" tag="a" wire:navigate>Izin</x-filament::button>
    </div>
  </section>
</div>
