const buttonStartScan = document.getElementById('start-scanning-cam');
const buttonStopScan = document.getElementById('stop-scanning-cam');
const textSelectCamera = document.getElementById('text-select-camera');
const selectListDevices = document.getElementById('select-device-list');
const textScannedCamera = document.getElementById('text-scanned-camera');
const scannedQrText = document.getElementById('scanned-qr-text');

const START_CAM = 'start-camera';
const STOP_CAM = 'stop-camera';
  
const html5QrCode = new Html5Qrcode(/* element id */ "camera-canvas");
let devices;
let cameraId;

document.addEventListener('DOMContentLoaded', async() => {
  try {
    /**
     * devices would be an array of objects of type:
     * { id: "id", label: "label" }
     */
    const devices = await Html5Qrcode.getCameras();
    
    if (devices && devices.length) {
      buttonStopScan.setAttribute('hidden', 'hidden');
      const optionList = [];
      devices.forEach((device, i) => {
        optionList[i] = document.createElement('option');
        optionList[i].innerText = device.label;
        optionList[i].value = device.id;
        selectListDevices.appendChild(optionList[i]);
      });
      selectListDevices.removeAttribute('hidden');

      cameraId = devices[0].id;
    }
  } catch(e) {
      // 
  }
});

buttonStartScan.addEventListener('click', () => {
  buttonStartScan.setAttribute('hidden', 'hidden');
  buttonStopScan.removeAttribute('hidden');
  textScannedCamera.removeAttribute('hidden');
  selectListDevices.setAttribute('hidden', 'hidden');
  textSelectCamera.setAttribute('hidden', 'hidden');
  document.dispatchEvent(new Event(START_CAM));
});

buttonStopScan.addEventListener('click', () => {
  buttonStopScan.setAttribute('hidden', 'hidden');
  scannedQrText.setAttribute('hidden', 'hidden');
  textScannedCamera.setAttribute('hidden', 'hidden');
  buttonStartScan.removeAttribute('hidden');
  selectListDevices.removeAttribute('hidden');
  textSelectCamera.removeAttribute('hidden');
  document.dispatchEvent(new Event(STOP_CAM));
});

selectListDevices.addEventListener('change', () => {
  cameraId = selectListDevices.value;
});

document.addEventListener(START_CAM, () => {
      html5QrCode.start(cameraId, 
        {fps: 15, qrbox: {width: 250, height: 250}}, 
        (decodedText, decodedResult) => {
          const inputChange = document.getElementById('scanned-value');
          scannedQrText.removeAttribute('hidden');
          
          scannedQrText.innerText = decodedText.substring(11);
          inputChange.setAttribute('wire:change',  `hadir("${decodedText}")`);
          inputChange.dispatchEvent(new Event('change'));
        },
        (errorMessage) => {throw new Error('Gagal')}
      );
});

document.addEventListener(STOP_CAM, async () => {
  try{
    await html5QrCode.stop();
  } catch(err) {
    // Stop failed, handle it.
  }
});


  // const inputChange = document.querySelector('#reader input');

  // function onScanSuccess(decodedText, decodedResult) {
  //   // handle the scanned code as you like, for example:
  //   inputChange.value = decodedText;
  //   inputChange.dispatchEvent(new Event('change'));
  // }
  
  // function onScanFailure(error) {
  //   // handle scan failure, usually better to ignore and keep scanning.
  // }
  
  // let html5QrcodeScanner = new Html5QrcodeScanner(
  //   "reader",
  //   { fps: 10, qrbox: {width: 250, height: 250} });
  // html5QrcodeScanner.render(onScanSuccess, onScanFailure);