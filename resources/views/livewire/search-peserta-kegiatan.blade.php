<div class="flex flex-row items-center justify-center">
    <x-slot:title>{{ $this->title }}</x-slot:title>
    <section class="flex flex-col justify-center w-full p-10 m-4 bg-white rounded-md shadow-xl md:w-2/4 lg:2/5">
        <x-filament::icon-button icon="heroicon-c-arrow-long-left" href="/presensi-mudamudi/{{ $this->kegiatan->id }}" tag="a" label="Filament" size="lg" color="gray" wire:navigate/>
        <h1 class="my-3 text-2xl font-semibold text-center">{{ $this->kegiatan->nm_kegiatan }}</h1>
        <p><x-heroicon-o-map-pin class="inline" width="1.3rem" /> {{ $this->kegiatan->tempat_kegiatan }}</p>
        <p><x-heroicon-o-calendar class="inline" width="1.3rem"/> {{ strftime("%A, %d %B %Y", strtotime($this->kegiatan->waktu_mulai)) }}</p>
        <p><x-heroicon-o-clock class="inline" width="1.3rem"/> {{ date('H:i', strtotime($this->kegiatan->waktu_mulai)) }} s/d Selesai</p>
        <p><x-heroicon-o-users class="inline" width="1.3rem"/> {{ $this->peserta }}</p>

        <form wire:submit="hadirAction" class="my-5">
            {{ $this->form }}
            
            <x-filament::button color="info" class="w-full mt-5" type="submit">
                Hadir
            </x-filament::button>
        </form>

        <p class="text-center text-gray-400">Nama kamu tidak ada ? Isi form registrasi terlebih dahulu yaa! <br>Klik link dibawah ini!</p>
        <x-filament::link icon="heroicon-m-sparkles" color="success" :href="route('form-registrasi')">
            Form Registrasi
        </x-filament::link>
  </section>
</div>
