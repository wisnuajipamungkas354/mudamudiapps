<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex flex-col justify-center items-center">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->margin(5)->generate($getRecord()->id . ' | ' . $getRecord()->nama)); !!} ">
        <span class="text-center mt-2 font-semibold">MM-ID : {{ $getRecord()->id }}</span>
        <a href="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->margin(5)->generate($getRecord()->id . ' | ' . $getRecord()->nama)); !!} " download="{{ $getRecord()->nama }}">Download QR Code</a>
    </div>
</x-dynamic-component>
