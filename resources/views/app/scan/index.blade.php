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
                                Mengalihkan halaman...
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
        $(document).ready(function() {
            let html5QrCode;
            let isScanning = false;
            let isSwitchingCamera = false;

            const typeNames = {
                'gga': 'GGA',
                'ggas': 'GGAS',
                'blending-awal': 'Blending Awal',
                'blending-awal-mikro': 'Blending Awal Mikro',
                'monitoring-turun-blending': 'Monitoring Turun Blending',
                'monitoring-pasteurisasi': 'Monitoring Pasteurisasi',
                'monitoring-storage-kimia': 'Monitoring Storage Kimia',
                'monitoring-storage-mikro': 'Monitoring Storage Mikro',
                'monitoring-storage-before-use': 'Monitoring Storage Before Use',
                'monitoring-daily-tank-kimia': 'Monitoring Daily Tank - Kimia',
                'monitoring-daily-tank-mikro': 'Monitoring Daily Tank - Mikro',
                'monitoring-ongoing-kimia': 'Monitoring Ongoing - Kimia',
                'monitoring-ongoing-mikro': 'Monitoring Ongoing - Mikro',
                'shelf-life-sampling': 'Shelf Life Analisis'
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            initializeScanner();

            $('#manualScanForm').on('submit', function(e) {
                e.preventDefault();
                const url = $('#manualUrl').val().trim();
                if (url) {
                    processQRCode(url);
                }
            });

            $('#cameraSelect').on('change', function() {
                if (isSwitchingCamera) return;

                const newCameraId = $(this).val();
                switchCamera(newCameraId);
            });

            function initializeScanner() {
                html5QrCode = new Html5Qrcode("reader");

                Html5Qrcode.getCameras().then(function(cameras) {
                    if (!cameras || cameras.length === 0) {
                        showError("Tidak ada kamera ditemukan");
                        return;
                    }

                    updateCameraList(cameras);

                    let selectedCamera = cameras[0].id;
                    for (let i = 0; i < cameras.length; i++) {
                        if (cameras[i].label.toLowerCase().includes('back') ||
                            cameras[i].label.toLowerCase().includes('environment')) {
                            selectedCamera = cameras[i].id;
                            break;
                        }
                    }

                    $('#cameraSelect').val(selectedCamera);
                    startScanning(selectedCamera);

                }).catch(function(err) {
                    console.error("Error getting cameras:", err);
                    showError("Gagal mengakses kamera");
                });
            }

            function updateCameraList(cameras) {
                const $select = $('#cameraSelect');
                $select.empty();

                $.each(cameras, function(index, camera) {
                    let label = camera.label || ('Camera ' + (index + 1));

                    if (label.toLowerCase().includes('back') || label.toLowerCase().includes(
                            'environment')) {
                        label = '📷 Kamera Belakang';
                    } else if (label.toLowerCase().includes('front') || label.toLowerCase().includes(
                            'user')) {
                        label = '🤳 Kamera Depan';
                    }

                    $select.append($('<option></option>').val(camera.id).text(label));
                });
            }

            function switchCamera(cameraId) {
                if (isSwitchingCamera) return;

                isSwitchingCamera = true;
                updateScannerStatus("Mengganti kamera...", false);
                $('#cameraSelect').prop('disabled', true);

                stopScanning().then(function() {
                    setTimeout(function() {
                        startScanning(cameraId).finally(function() {
                            isSwitchingCamera = false;
                            $('#cameraSelect').prop('disabled', false);
                        });
                    }, 500);
                }).catch(function(err) {
                    console.error("Error switching camera:", err);
                    isSwitchingCamera = false;
                    $('#cameraSelect').prop('disabled', false);
                    showError("Gagal mengganti kamera");
                });
            }

            function startScanning(cameraId) {
                if (isScanning) {
                    return Promise.reject("Already scanning");
                }

                return html5QrCode.start(
                    cameraId, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        },
                        aspectRatio: 1.0,
                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                    },
                    onScanSuccess
                ).then(function() {
                    isScanning = true;
                    updateScannerStatus("Kamera aktif, arahkan ke QR Code", false);
                }).catch(function(err) {
                    console.error("Scanner start error:", err);
                    showError("Scanner gagal dijalankan: " + err);
                    throw err;
                });
            }

            function stopScanning() {
                if (!isScanning || !html5QrCode) {
                    return Promise.resolve();
                }

                return html5QrCode.stop().then(function() {
                    isScanning = false;
                    console.log("Scanner stopped successfully");
                }).catch(function(err) {
                    console.error("Scanner stop error:", err);
                    isScanning = false;
                    throw err;
                });
            }

            function onScanSuccess(decodedText) {
                if (!isScanning) return;

                stopScanning().then(function() {
                    processQRCode(decodedText);
                });
            }

            function processQRCode(url) {
                updateScannerStatus("Memproses QR Code...", false);

                $.ajax({
                    url: "{{ route('scan.store') }}",
                    type: "POST",
                    data: {
                        url: url
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showScanResult(response.qc_type, response.qc_id, response.redirect_url);
                        } else {
                            showError(response.message || "QR Code tidak valid");
                            restartCamera();
                        }
                    },
                    error: function(xhr) {
                        let message = "QR gagal diproses";

                        if (xhr.status === 403 && xhr.responseJSON) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'warning',
                                title: 'Akses Ditolak',
                                text: response.message,
                            }).then(function(result) {
                                restartCamera();
                            });
                            return;
                        }

                        if (xhr.status === 404) {
                            message = "Data tidak ditemukan";
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        showError(message);
                        restartCamera();
                    }
                });
            }

            function showScanResult(type, id, redirectUrl) {
                const typeName = typeNames[type] || type;

                $('#resultType').text(typeName);
                $('#resultId').text('#' + id);
                $('#resultTime').text(new Date().toLocaleString('id-ID'));

                $('#resultCard').slideDown(200);

                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 500);
            }

            function restartCamera() {
                setTimeout(function() {
                    const selectedCamera = $('#cameraSelect').val();
                    if (selectedCamera && !isScanning) {
                        updateScannerStatus("Mengaktifkan kembali kamera...", false);
                        startScanning(selectedCamera);
                    }
                }, 2000);
            }

            function updateScannerStatus(message, isError) {
                const $statusEl = $('#scannerStatus');
                $statusEl.text(message);

                if (isError) {
                    $statusEl.addClass('alert-error');
                } else {
                    $statusEl.removeClass('alert-error');
                }
            }

            function showError(message) {
                updateScannerStatus(message, true);
                $('#resultCard').slideUp(200);
            }
        });
    </script>
@endsection
