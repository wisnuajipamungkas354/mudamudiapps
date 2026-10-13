<x-filament-panels::page>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Pantau error log, exceptions, dan aktivitas sistem aplikasi secara real-time.
        </p>
        <x-filament::button
            tag="a"
            href="{{ url(config('log-viewer.route_path', 'log-viewer')) }}"
            target="_blank"
            icon="heroicon-m-arrow-top-right-on-square"
            color="gray"
        >
            Buka di Tab Penuh
        </x-filament::button>
    </div>

    <div class="w-full bg-white dark:bg-gray-900 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-sm">
        <iframe
            src="{{ url(config('log-viewer.route_path', 'log-viewer')) }}"
            class="w-full border-0"
            style="height: calc(100vh - 240px); min-height: 650px;"
            title="Log Viewer"
        ></iframe>
    </div>
</x-filament-panels::page>
