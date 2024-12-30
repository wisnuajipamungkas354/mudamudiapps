<div id="reader" class="grow w-full lg:w-2/5 flex flex-col justify-center items-center gap-3 border-4">
    <h3 class="text-center font-semibold">Scan QR Code</h3>

    <div id="camera-canvas" class="w-full flex flex-col items-center"></div>

    <select id="select-device-list" required></select>
    <span id="scanned-qr-text" hidden></span>

    <button id="start-scanning-cam" type="button" color="warning">Mulai Scanning</button>
    <button id="stop-scanning-cam" type="button" color="warning" hidden>Stop Scanning</button>

    <input type="text" hidden/>
</div>
