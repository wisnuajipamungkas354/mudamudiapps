<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex justify-center">
        {!! QrCode::size(150)->generate(Request::path()); !!}
    </div>
</x-dynamic-component>
