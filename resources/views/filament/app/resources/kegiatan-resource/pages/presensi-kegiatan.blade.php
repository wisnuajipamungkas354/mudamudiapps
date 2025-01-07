@push('styles')
    @vite('resources/css/app.css')
@endpush
<x-filament-panels::page>
    <div>
        {{ $this->infolist }}
    </div>
    <div class="flex flex-col lg:flex-row gap-2 w-full">
        <livewire:scan-qr-presensi :kegiatan='$this->record'/>
        <div class="w-full lg:w-3/5">
            {{ $this->table }}
        </div>
    </div>
    <hr>
    <h3 class="font-semibold text-xl text-center">List Registrasi</h3>
    <livewire:registrasi-kegiatan-table :kegiatan='$this->record' />
</x-filament-panels::page>
@push('script')
@vite('resources/js/app.js')
@endpush