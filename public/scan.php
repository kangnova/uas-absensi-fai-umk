<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absensi - UAS FAI UMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        #reader { width: 100%; max-width: 600px; margin: 0 auto; }
        .result-container { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>Scanner Absensi</h4>
            </div>
            <div class="card-body text-center">
                <div id="reader"></div>
                
                <div class="result-container">
                    <h5 id="scan-status" class="text-secondary">Siap melakukan scan...</h5>
                    <div id="scan-result" class="alert d-none"></div>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">Kembali ke Home</a>
                    <a href="manual_attendance.php" class="btn btn-outline-primary">Absen Manual</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const scanStatus = document.getElementById('scan-status');
        const scanResult = document.getElementById('scan-result');
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;

            scanStatus.innerText = "Memproses...";
            
            // Send to backend
            fetch('process-scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'token=' + encodeURIComponent(decodedText)
            })
            .then(response => response.json())
            .then(data => {
                scanResult.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
                
                if (data.status === 'success') {
                    scanResult.classList.add('alert-success');
                    scanResult.innerHTML = `<strong>Sukses!</strong> ${data.message}<br>Nama: ${data.detail.nama}`;
                    
                    // Play success beep
                    let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                    audio.play();

                } else if (data.status === 'error') {
                     scanResult.classList.add('alert-danger');
                     scanResult.innerText = data.message;
                } else {
                    scanResult.classList.add('alert-warning'); // Duplicate usually
                    scanResult.innerText = data.message;
                    
                    // TTS for duplicate
                    let msg = new SpeechSynthesisUtterance("sudah scan");
                    msg.lang = 'id-ID';
                    msg.rate = 0.9;
                    
                    // Attempt to pick an Indonesian voice if available
                    let voices = window.speechSynthesis.getVoices();
                    let idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID');
                    if (idVoice) {
                        msg.voice = idVoice;
                    }

                    window.speechSynthesis.speak(msg);
                }
                
                scanStatus.innerText = "Scan lagi dalam 3 detik...";
                
                // Cooldown
                setTimeout(() => {
                    isProcessing = false;
                    scanStatus.innerText = "Siap melakukan scan...";
                    scanResult.classList.add('d-none');
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                isProcessing = false;
                scanStatus.innerText = "Error koneksi. Coba lagi.";
            });
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</body>
</html>
