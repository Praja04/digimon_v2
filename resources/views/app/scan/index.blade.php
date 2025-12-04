@extends('layouts.component.main')
@section('title', 'QR Code Scanner')

@section('styles')
    <style>
        #reader {
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .scanner-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .alert-simple {
            background: #f1f1f1;
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .alert-error {
            background: #ffe5e5;
            border: 1px solid #ffcccc;
            color: #cc0000;
        }

        #resultCard {
            border: 1px solid #cce5cc;
        }

        #resultCard .card-header {
            background: #e5f6e5;
            border-bottom: 1px solid #cce5cc;
        }
    </style>
@endsection


@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    {{-- Scanner Card --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Scan QR Code</h5>
                        </div>

                        <div class="card-body">
                            <div class="scanner-container">

                                <div id="scannerStatus" class="alert-simple">
                                    Menginisialisasi kamera...
                                </div>

                                <div id="reader" class="mb-3"></div>

                                <label class="form-label mb-1 small text-muted">Pilih Kamera:</label>
                                <select id="cameraSelect" class="form-select form-select-sm mb-3">
                                    <option value="">Memuat kamera...</option>
                                </select>

                                <button type="button" class="btn btn-sm btn-light w-100" data-bs-toggle="collapse"
                                    data-bs-target="#manualInput">
                                    Input Manual
                                </button>

                                <div class="collapse mt-3" id="manualInput">
                                    <form id="manualScanForm">
                                        <input type="text" class="form-control form-control-sm mb-2" id="manualUrl"
                                            placeholder="Paste URL QR di sini">
                                        <button type="submit" class="btn btn-dark btn-sm w-100">
                                            Proses
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Hasil Scan --}}
                    <div class="card mt-3" id="resultCard" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0">Scan Berhasil</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1 small text-muted">Detail:</p>

                            <div class="mb-1 small">
                                <strong>Tipe:</strong> <span id="resultType"></span>
                            </div>
                            <div class="mb-1 small">
                                <strong>ID:</strong> <span id="resultId"></span>
                            </div>
                            <div class="mb-1 small">
                                <strong>Waktu:</strong> <span id="resultTime"></span>
                            </div>

                            <div class="alert-simple mt-3">
                                Mengalihkan dalam <strong><span id="countdown">3</span></strong> detik...
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode;
        let isScanning = false;
        let redirectTimer = null;

        const typeNames = {
            'gga': 'GGA',
            'ggas': 'GGAS',
            'blending_awal': 'Blending Awal',
            'blending_after_adjust': 'Blending After Adjust'
        };

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            initializeScanner();

            $('#manualScanForm').on('submit', function(e) {
                e.preventDefault();
                const url = $('#manualUrl').val().trim();
                if (url) processQRCode(url);
            });

            $('#cameraSelect').on('change', function() {
                stopScanning();
                startScanning($(this).val());
            });
        });

        function initializeScanner() {
            html5QrCode = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(cameras => {
                if (!cameras.length) {
                    return showError("Tidak ada kamera ditemukan");
                }

                updateCameraList(cameras);
                const selected = cameras[0].id;
                $('#cameraSelect').val(selected);
                startScanning(selected);

            }).catch(() => showError("Gagal mengakses kamera"));
        }

        function updateCameraList(cameras) {
            const select = $('#cameraSelect');
            select.empty();
            cameras.forEach((cam, i) => {
                select.append(new Option(cam.label || `Camera ${i+1}`, cam.id));
            });
        }

        function startScanning(cameraId) {
            if (isScanning) return;

            html5QrCode.start(
                cameraId, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                onScanSuccess
            ).then(() => {
                isScanning = true;
                updateScannerStatus("Kamera aktif, arahkan ke QR Code", false);
            }).catch(() => showError("Scanner gagal dijalankan"));
        }

        function stopScanning() {
            if (isScanning) {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                }).catch(() => {
                    isScanning = false;
                });
            }
        }

        function onScanSuccess(decodedText) {
            stopScanning();
            processQRCode(decodedText);
        }

        function processQRCode(url) {
            updateScannerStatus("Memproses QR Code...", false);

            $.post("{{ route('scan.store') }}", {
                    url: url
                })
                .done(res => {
                    if (res.status === 'success') {
                        showScanResult(res.qc_type, res.qc_id, res.redirect_url);
                    } else {
                        showError(res.message || "QR Code tidak valid");
                        restartCamera();
                    }
                })
                .fail((xhr) => {
                    const message = xhr.responseJSON?.message || "QR gagal diproses";
                    showError(message);
                    restartCamera();
                });
        }

        function showScanResult(type, id, redirectUrl) {
            $('#resultType').text(typeNames[type] || type);
            $('#resultId').text('#' + id);
            $('#resultTime').text(new Date().toLocaleString('id-ID'));

            $('#resultCard').slideDown(200);

            startRedirect(redirectUrl);
        }

        function startRedirect(url) {
            let time = 3;
            const el = $('#countdown');
            el.text(time);

            // Clear previous timer if exists
            if (redirectTimer) {
                clearInterval(redirectTimer);
            }

            redirectTimer = setInterval(() => {
                time--;
                el.text(time);

                if (time <= 0) {
                    clearInterval(redirectTimer);
                    redirectTimer = null;
                    window.location.href = url;
                }
            }, 1000);
        }

        function restartCamera() {
            // Wait 2 seconds before restarting camera
            setTimeout(() => {
                const selectedCamera = $('#cameraSelect').val();
                if (selectedCamera) {
                    updateScannerStatus("Mengaktifkan kembali kamera...", false);
                    startScanning(selectedCamera);
                }
            }, 2000);
        }

        function updateScannerStatus(msg, isError = false) {
            const statusEl = $('#scannerStatus');
            statusEl.text(msg);

            if (isError) {
                statusEl.addClass('alert-error');
            } else {
                statusEl.removeClass('alert-error');
            }
        }

        function showError(msg) {
            updateScannerStatus(msg, true);

            // Hide result card if visible
            $('#resultCard').slideUp(200);

            // Clear redirect timer if exists
            if (redirectTimer) {
                clearInterval(redirectTimer);
                redirectTimer = null;
            }
        }
    </script>
@endsection
