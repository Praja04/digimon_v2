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

        .format-example {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            color: #495057;
        }
    </style>
@endsection


@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="ri-qr-scan-2-line me-2"></i>Scanner
                            </h5>
                        </div>
                        <div class="card-body">

                            <form id="manualScanForm">
                                <div class="input-group">
                                    <input type="text" id="manualUrl" class="form-control form-control-lg"
                                        placeholder="Scan atau ketik kode..." autocomplete="off"
                                        style="text-transform:uppercase;">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ri-send-plane-line"></i>
                                    </button>
                                </div>
                            </form>

                            <div id="scanFeedback" class="mt-2" style="display:none;">
                                <div id="feedbackContent" class="p-2 rounded small"></div>
                            </div>

                            <button class="btn btn-sm btn-outline-primary w-100 mt-3" data-bs-toggle="collapse"
                                data-bs-target="#cameraSection">
                                <i class="ri-camera-line me-1"></i> Gunakan Kamera
                            </button>

                            <div class="collapse mt-2" id="cameraSection">
                                <div id="scannerStatus" class="alert-simple">
                                    Menginisialisasi kamera...
                                </div>
                                <div id="reader" class="mb-2"></div>
                                <label class="form-label mb-1 small text-muted">Pilih Kamera:</label>
                                <select id="cameraSelect" class="form-select form-select-sm"></select>
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

            let html5QrCode, isScanning = false,
                isSwitchingCamera = false;
            const STORAGE_KEY = 'preferredCameraLabel';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#manualUrl').focus();

            $('#manualUrl').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            $('#cameraSection').on('show.bs.collapse', function() {
                if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
                if (!isScanning) initializeScanner();
            });
            $('#cameraSection').on('hide.bs.collapse', function() {
                stopScanning();
            });

            $('#manualScanForm').on('submit', function(e) {
                e.preventDefault();
                const input = $('#manualUrl').val().trim().toUpperCase();
                if (!input) {
                    $('#manualUrl').focus();
                    return;
                }

                const barcodeMap = {
                    'P1': 'PELARUTAN-1',
                    'P2': 'PELARUTAN-2',
                    'BA': 'BLENDING-AWAL',
                    'BM': 'BLENDING-AWAL-MIKRO',
                    'TB': 'MONITORING-TURUN-BLENDING',
                    'AC': 'MONITORING-PASTEURISASI',
                    'SK': 'MONITORING-STORAGE-KIMIA',
                    'SM': 'MONITORING-STORAGE-MIKRO',
                };

                if (barcodeMap[input.substring(0, 2)] && !input.includes('/')) {
                    processQRCode(input);
                    return;
                }

                if (!input.startsWith('HTTP')) {
                    const parts = input.split('/');
                    const prefix = parts[0];
                    const fiveSegment = ['PELARUTAN-1', 'PELARUTAN-2', 'BLENDING-AWAL',
                        'BLENDING-AFTER-ADJUST-MIKRO', 'MONITORING-TURUN-BLENDING',
                        'MONITORING-PASTEURISASI'
                    ];
                    const expected = fiveSegment.includes(prefix) ? 5 : 4;

                    if (parts.length !== expected) {
                        showFeedback('error',
                            'Format tidak valid. Contoh: PELARUTAN-1/2901005/2026-01-29/1-2/192');
                        $('#manualUrl').select();
                        return;
                    }
                }

                processQRCode(input);
            });

            function processQRCode(url) {
                showFeedback('loading', 'Memproses...');

                $.ajax({
                    url: "{{ route('scan.store') }}",
                    type: "POST",
                    data: {
                        url: url
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showFeedback('success', response.name + ' &nbsp;#' + response.qc_id);
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1000);
                        } else {
                            showFeedback('error', response.message);
                            $('#manualUrl').val('').focus();
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal memproses kode';
                        if (xhr.status === 403) {
                            showFeedback('error', 'Akses ditolak: ' + msg);
                        } else {
                            showFeedback('error', msg);
                        }
                        $('#manualUrl').val('').focus();

                        if (isScanning) {
                            stopScanning().then(() => {
                                setTimeout(() => startScanning($('#cameraSelect').val()), 1500);
                            });
                        }
                    }
                });
            }

            function showFeedback(type, message) {
                const colors = {
                    loading: 'bg-light text-primary border border-primary',
                    success: 'bg-success bg-opacity-10 text-success border border-success',
                    error: 'bg-danger bg-opacity-10 text-danger border border-danger',
                };
                const icons = {
                    loading: '<i class="mdi mdi-loading mdi-spin me-1"></i>',
                    success: '<i class="ri-checkbox-circle-line me-1"></i>',
                    error: '<i class="ri-error-warning-line me-1"></i>',
                };
                $('#feedbackContent')
                    .attr('class', 'p-2 rounded small ' + colors[type])
                    .html(icons[type] + message);
                $('#scanFeedback').show();
            }

            function initializeScanner() {
                Html5Qrcode.getCameras().then(function(cameras) {
                    if (!cameras || cameras.length === 0) {
                        showFeedback('error', 'Kamera tidak ditemukan');
                        return;
                    }
                    updateCameraList(cameras);
                    const savedLabel = localStorage.getItem(STORAGE_KEY) || '';
                    let selectedCamera = cameras[0].id;
                    cameras.forEach(c => {
                        if ((c.label || '').toLowerCase() === savedLabel.toLowerCase())
                            selectedCamera = c.id;
                        if ((c.label || '').toLowerCase().includes('back')) selectedCamera = c.id;
                    });
                    $('#cameraSelect').val(selectedCamera);
                    startScanning(selectedCamera);
                }).catch(() => showFeedback('error', 'Gagal mengakses kamera'));
            }

            function updateCameraList(cameras) {
                const $sel = $('#cameraSelect').empty();
                cameras.forEach((c, i) => {
                    const raw = c.label || ('Camera ' + (i + 1));
                    let label = raw;
                    if (raw.toLowerCase().includes('back') || raw.toLowerCase().includes('environment'))
                        label = 'Kamera Belakang';
                    else if (raw.toLowerCase().includes('front') || raw.toLowerCase().includes('user'))
                        label = 'Kamera Depan';
                    $sel.append($('<option>').val(c.id).text(label).data('raw-label', raw));
                });
            }

            function startScanning(cameraId) {
                if (isScanning) return Promise.reject();
                return html5QrCode.start(cameraId, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        },
                        aspectRatio: 1.0,
                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE, Html5QrcodeSupportedFormats
                            .CODE_128
                        ]
                    },
                    function(text) {
                        stopScanning().then(() => processQRCode(text));
                    }
                ).then(() => {
                    isScanning = true;
                });
            }

            function stopScanning() {
                if (!isScanning || !html5QrCode) return Promise.resolve();
                return html5QrCode.stop().then(() => {
                    isScanning = false;
                }).catch(() => {
                    isScanning = false;
                });
            }

            $('#cameraSelect').on('change', function() {
                if (isSwitchingCamera) return;
                isSwitchingCamera = true;
                const id = $(this).val();
                localStorage.setItem(STORAGE_KEY, $(this).find(':selected').data('raw-label') || '');
                stopScanning().then(() => setTimeout(() => {
                    startScanning(id).finally(() => {
                        isSwitchingCamera = false;
                    });
                }, 500));
            });

        });
    </script>
@endsection
