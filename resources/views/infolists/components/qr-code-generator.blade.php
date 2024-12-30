<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex flex-col justify-center items-center">
        {!! QrCode::size(150)->errorCorrection('H')->generate($getRecord()->id . ' | ' . $getRecord()->nama)->format('png'); !!}
        <span class="text-center mt-2 font-semibold">MM-ID : {{ $getRecord()->id }}</span>
    </div>
</x-dynamic-component>
