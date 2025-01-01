<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex flex-col justify-center items-center">
        <img src="{{ Storage::url('public/qr-images/mudamudi/' . $getRecord()->id . '.png'); }}" width="150px">
        <span class="text-center mt-2 font-semibold">MM-ID : {{ $getRecord()->id }}</span>
    </div>
</x-dynamic-component>
