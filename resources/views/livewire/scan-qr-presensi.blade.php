<section class="grow w-full lg:w-2/5 flex flex-col justify-between items-center border-2 rounded-lg pb-3">
    <header class="bg-emerald-800 flex flex-row w-full justify-center p-3 rounded-t-lg">
        <h3 class="text-center font-bold text-white">Scan QR Code</h3>
    </header>

    <div id="reader" class="w-full flex flex-col justify-center items-center">
        <div id="camera-canvas" class="w-full flex flex-col items-center"></div>
    </div>

    <div class="flex flex-col items-center">
        <span id="text-select-camera" class="font-semibold pt-2" hidden>Pilih Kamera</span>
        <select id="select-device-list" class="rounded-lg" hidden required></select>
        <span id="scanned-qr-text" hidden></span>
        
        <button id="request-access-cam" type="button" class="bg-blue-600 p-2 text-white rounded-lg text-sm mt-3"><x-heroicon-o-camera width="1.2rem" class="inline pb-1" /> Minta Izin Akses Kamera</button>
        <button id="start-scanning-cam" type="button" class="bg-blue-600 p-2 text-white rounded-lg text-sm mt-3"><x-heroicon-o-camera width="1.2rem" class="inline pb-1" hidden /> Mulai Scan</button>
        <button id="stop-scanning-cam" type="button" class="bg-red-600 p-2 text-white rounded-lg text-sm mt-3" hidden><x-heroicon-s-stop width="1.2rem" class="inline pb-1"/> Stop Scanning</button>
    </div>

    <input type="text" hidden/>
</section>
