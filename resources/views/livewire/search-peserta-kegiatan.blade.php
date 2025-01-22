<div class="flex flex-row justify-center items-center">
    <x-slot:title>{{ $this->title }}</x-slot:title>
    <section class="flex flex-col justify-center bg-white p-10 rounded-md shadow-xl m-4 w-full md:w-2/4 lg:2/5">
        <x-filament::icon-button icon="heroicon-c-arrow-long-left" href="/presensi-mudamudi/{{ $this->kegiatan->id }}" tag="a" label="Filament" size="lg" color="gray" wire:navigate/>
        <h1 class="text-2xl text-center font-semibold my-3">{{ $this->kegiatan->nm_kegiatan }}</h1>
        <p><x-heroicon-o-map-pin class="inline" width="1.3rem" /> {{ $this->kegiatan->tempat_kegiatan }}</p>
        <p><x-heroicon-o-calendar class="inline" width="1.3rem"/> {{ strftime("%A, %d %B %Y", strtotime($this->kegiatan->waktu_mulai)) }}</p>
        <p><x-heroicon-o-clock class="inline" width="1.3rem"/> {{ date('H:i', strtotime($this->kegiatan->waktu_mulai)) }} s/d Selesai</p>
        <!-- <p><x-heroicon-o-users class="inline" width="1.3rem"/> {{ $this->kegiatan->kategori_peserta }}</p> -->

        <div class="my-5">
            {{ $this->form }}
        </div>

        @if($this->form->getState('search')['id'] !== null)
        <div class="mt-3 flex flex-col justify-center items-center">
            <h3 class="text-center font-bold">QR-Code</h3>
                <p class="my-2 text-center">Tunjukkan QR-Code ini ke tim registrasi untuk melakukan presensi!</p>
                <img src="{{ Storage::url('public/qr-images/mudamudi/' . $this->form->getState()['id'] . '.png'); }}" />
                <x-filament::button color="info" href="{{ Storage::url('public/qr-images/mudamudi/' . $this->form->getState()['id']. '.png'); }}" tag="a" download="{{ $this->form->getState()['nama']}}.png" class="my-2">
                <x-heroicon-o-qr-code width="1.3rem" class="inline"/>
                    Simpan QR-Code
                </x-filament::button>
            </div>
        @endif

        <p class="text-center text-gray-400">Nama kamu tidak ada ? Isi form registrasi terlebih dahulu yaa! <br>Klik link dibawah ini!</p>
        <x-filament::link icon="heroicon-m-sparkles" color="success" :href="route('form-registrasi')">
            Form Registrasi
        </x-filament::link>
  </section>
</div>
