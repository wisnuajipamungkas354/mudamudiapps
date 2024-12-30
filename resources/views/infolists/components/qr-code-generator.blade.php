<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex flex-col justify-center items-center">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate('$getRecord()->id . ' | ' . $getRecord()->nama!')) !!} ">
        <span class="text-center mt-2 font-semibold">MM-ID : {{ $getRecord()->id }}</span>
    </div>
</x-dynamic-component>
