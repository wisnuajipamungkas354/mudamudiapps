<x-filament-panels::page>
    <div>
        {{ $this->infolist }}
    </div>
    <div class="flex flex-col lg:flex-row gap-2 justify-center align-center w-full">
        <div id="reader" class="grow"></div>
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
