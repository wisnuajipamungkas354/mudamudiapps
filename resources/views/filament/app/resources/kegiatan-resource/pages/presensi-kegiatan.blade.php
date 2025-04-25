@push('styles')
    @vite('resources/css/app.css')
@endpush
<x-filament-panels::page>
    <div>
        {{ $this->infolist }}
    </div>
    <h3 class="text-xl font-semibold text-center">List Peserta</h3>
    <div id="presensi-table" class="w-full">
        {{ $this->table }}
    </div>
    <hr>
    <h3 class="text-xl font-semibold text-center">List Izin</h3>
    <livewire:list-izin-table :kegiatan='$this->record' />
    <hr>
    <h3 class="text-xl font-semibold text-center">List Registrasi</h3>
    <livewire:registrasi-kegiatan-table :kegiatan='$this->record' />
</x-filament-panels::page>
@push('script')
@vite('resources/js/app.js')
@endpush