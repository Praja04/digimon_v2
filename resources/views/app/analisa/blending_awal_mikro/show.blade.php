@extends('layouts.component.main')
@section('title', 'Analisa Blending Awal')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('analisa.blending-awal-mikro.index') }}">Analisa
                                        Blending Awal - Mikro</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ Session::get('username') }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Tanggal Produksi : <span
                                                            class="text-body fw-medium">{{ $productionBatch->date }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-drop-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Variant :</p>
                                                            <h5 class="mb-0">{{ $productionBatch->variant }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-arrow-left-right-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Batch Range :</p>
                                                            <h5 class="mb-0">{{ $productionBatch->batch_range }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{{ $productionBatch->description ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="col-lg-12">
                        <div class="card">
                            <!--end card-body-->
                            <div class="card-body">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th>Nomor PO</th>
                                                <th>Batch Range</th>
                                                <th>No Blending</th>
                                                <th>Volume</th>
                                                <th>Nama Analis</th>
                                                <th>Shift Analis</th>
                                                <th>EB</th>
                                                <th>TPC</th>
                                                <th>YM</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($productionBatch->blendingAfterAdjustMikro as $blending)
                                                @php
                                                    $po = $productionBatch->po_number;
                                                    $poCompact =
                                                        strlen($po) === 7
                                                            ? substr($po, 0, 4) . sprintf('%02d', (int) substr($po, 4))
                                                            : $po;

                                                    $qrStringBM =
                                                        'BLENDING-AFTER-ADJUST-MIKRO/' .
                                                        $po .
                                                        '/' .
                                                        $productionBatch->date .
                                                        '/' .
                                                        $blending->batch_range .
                                                        '/' .
                                                        $blending->id;
                                                    $barcodeStringBM = 'BM' . $poCompact . $blending->id;
                                                @endphp
                                                <tr>
                                                    <td>{{ $productionBatch->po_number }}</td>
                                                    <td>{{ $blending->batch_range }}</td>
                                                    <td>{{ $blending->nomor_blending }}</td>
                                                    <td>{{ $blending->volume }}</td>
                                                    <td>{{ $blending->nama_analis ?? '-' }}</td>
                                                    <td>{{ $blending->shift ? 'Shift ' . $blending->shift : '-' }}
                                                    </td>
                                                    <td>{{ $blending->eb ?? '-' }}</td>
                                                    <td>{{ $blending->tpc ?? '-' }}</td>
                                                    <td>{{ $blending->ym ?? '-' }}</td>
                                                    @if (auth()->user()->role == 'Analis Mikro')
                                                        <td>
                                                            @if (is_null($blending->ym))
                                                                <button class="btn btn-sm btn-primary" id="btnAnalisa"
                                                                    blending-id="{{ $blending->id }}">Input
                                                                    Analisa</button>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line"></i> Lengkap
                                                                </span>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1"
                                                                style="font-size: 12px;" data-bs-toggle="modal"
                                                                data-bs-target="#qrModalAfterAdjust{{ $blending->id }}">
                                                                <i class="ri-printer-line"></i> Cetak Kode
                                                            </button>

                                                            <div class="modal fade"
                                                                id="qrModalAfterAdjust{{ $blending->id }}" tabindex="-1"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content shadow-sm">
                                                                        <div class="modal-header bg-light py-2">
                                                                            <h6 class="modal-title">Blending After Adjust
                                                                                #{{ $blending->nomor_blending }}</h6>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="px-3 pt-3">
                                                                            <div class="btn-group w-100" role="group">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-primary aidc-toggle"
                                                                                    data-target="qr-bm-{{ $blending->id }}">
                                                                                    <i class="ri-qr-code-line me-1"></i> QR
                                                                                    Code
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-primary aidc-toggle"
                                                                                    data-target="barcode-bm-{{ $blending->id }}">
                                                                                    <i class="ri-barcode-line me-1"></i>
                                                                                    Barcode
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-body text-center p-3">
                                                                            <div id="qr-bm-{{ $blending->id }}"
                                                                                class="aidc-panel">
                                                                                <div
                                                                                    id="qrPrintAreaAfterAdjust{{ $blending->id }}">
                                                                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($qrStringBM, 'QRCODE') }}"
                                                                                        class="img-fluid mb-2"
                                                                                        style="max-width:180px;">
                                                                                    <div class="small text-muted">
                                                                                        {{ $qrStringBM }}</div>
                                                                                </div>
                                                                            </div>
                                                                            <div id="barcode-bm-{{ $blending->id }}"
                                                                                class="aidc-panel" style="display:none;">
                                                                                <div
                                                                                    id="barcodePrintAreaAfterAdjust{{ $blending->id }}">
                                                                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcodeStringBM, 'C128') }}"
                                                                                        class="img-fluid mb-2"
                                                                                        style="max-width:280px; height:80px;">
                                                                                    <div class="small text-muted">
                                                                                        {{ $barcodeStringBM }}</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer bg-light py-2">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light"
                                                                                data-bs-dismiss="modal">Tutup</button>
                                                                            <button class="btn btn-sm btn-primary"
                                                                                onclick="printActiveAidc('bm-{{ $blending->id }}')">
                                                                                Cetak
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">Tidak ada data tersedia.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal input GGA tunggal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Input Data Analisa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none error-alert"></div>
                        <input type="hidden" name="id" id="id">

                        <!-- ✅ Info Status -->
                        <div class="mb-3">
                            <div class="alert alert-info" id="statusInfo">
                                <strong>Status:</strong> <span id="statusText"></span>
                            </div>
                        </div>

                        <!-- EB Field -->
                        <div id="ebContainer" class="mb-3 d-none">
                            <label class="form-label">EB <span style="color: red;">*</span></label>
                            <input type="text" name="eb" id="eb" class="form-control comma-input"
                                placeholder="Masukkan nilai EB">
                            <small class="text-danger errorEb"></small>
                        </div>

                        <!-- TPC Field -->
                        <div id="tpcContainer" class="mb-3 d-none">
                            <label class="form-label">TPC <span style="color: red;">*</span></label>
                            <input type="text" name="tpc" id="tpc" class="form-control comma-input"
                                placeholder="Masukkan nilai TPC">
                            <small class="text-danger errorTpc"></small>
                        </div>

                        <!-- YM Field -->
                        <div id="ymContainer" class="mb-3 d-none">
                            <label class="form-label">YM <span style="color: red;">*</span></label>
                            <input type="text" name="ym" id="ym" class="form-control comma-input"
                                placeholder="Masukkan nilai YM">
                            <small class="text-danger errorYm"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.aidc-toggle');
            if (!btn) return;

            const modal = btn.closest('.modal-content');
            modal.querySelectorAll('.aidc-panel').forEach(p => p.style.display = 'none');
            document.getElementById(btn.dataset.target).style.display = 'block';

            modal.querySelectorAll('.aidc-toggle').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
        });

        function printActiveAidc(id) {
            const qrPanel = document.getElementById('qr-' + id);
            const isQR = qrPanel && qrPanel.style.display !== 'none';
            const modal = (qrPanel || document.getElementById('barcode-' + id)).closest('.modal-body');
            const printEl = modal.querySelector(isQR ? '[id^="qrPrintArea"]' : '[id^="barcodePrintArea"]');
            if (printEl) printQR(printEl.id);
        }

        function printQR(id) {
            const printArea = document.getElementById(id);

            if (!printArea) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Area print tidak ditemukan'
                });
                return;
            }

            const qrImage = printArea.querySelector('img');
            const qrLabel = printArea.querySelector('.small.text-muted');

            if (!qrImage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'QR Code tidak ditemukan'
                });
                return;
            }

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (isMobile) {
                printQRMobile(qrImage, qrLabel);
            } else {
                printQRDesktop(qrImage, qrLabel);
            }
        }

        function printQRDesktop(qrImage, qrLabel) {
            const printWindow = window.open('', '_blank', 'width=300,height=400');

            if (!printWindow) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pop-up Diblokir',
                    text: 'Mohon izinkan pop-up untuk print.'
                });
                return;
            }

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Print QR</title>
                    <style>
                        @page {
                            size: 58mm auto;
                            margin: 0;
                        }
                        
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        
                        body {
                            width: 58mm;
                            margin: 0 auto;
                            padding: 5mm 3mm;
                            font-family: Arial, sans-serif;
                            background: white;
                        }
                        
                        .container {
                            text-align: center;
                            width: 100%;
                        }
                        
                        .qr-image {
                            width: 45mm;
                            height: 45mm;
                            display: block;
                            margin: 0 auto 3mm auto;
                        }
                        
                        .qr-label {
                            font-size: 8pt;
                            color: #000;
                            word-wrap: break-word;
                            line-height: 1.3;
                        }
                        
                        @media print {
                            body {
                                padding: 2mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <img src="${qrImage.src}" alt="QR" class="qr-image">
                        <div class="qr-label"><strong>${qrLabel ? qrLabel.textContent.trim() : ''}</strong></div>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();

            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.focus();
                    printWindow.print();
                    setTimeout(function() {
                        printWindow.close();
                    }, 500);
                }, 250);
            };
        }

        function printQRMobile(qrImage, qrLabel) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = 220;
            canvas.height = 280;

            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                const qrSize = 170;
                const qrX = (canvas.width - qrSize) / 2;
                const qrY = 10;
                ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

                ctx.fillStyle = 'black';
                ctx.font = 'bold 10px Arial';
                ctx.textAlign = 'center';
                const labelText = qrLabel ? qrLabel.textContent.trim() : '';

                const maxWidth = 200;
                const lineHeight = 14;
                const words = labelText.split('/');
                let line = '';
                let y = qrY + qrSize + 20;

                words.forEach((word, index) => {
                    if (index > 0) line += '/';
                    const testLine = line + word;
                    const metrics = ctx.measureText(testLine);

                    if (metrics.width > maxWidth && index > 0) {
                        ctx.fillText(line, canvas.width / 2, y);
                        line = word;
                        y += lineHeight;
                    } else {
                        line = testLine;
                    }
                });
                ctx.fillText(line, canvas.width / 2, y);

                canvas.toBlob(function(blob) {
                    if (navigator.share && isMobileDevice()) {
                        const file = new File([blob], 'qr-code.png', {
                            type: 'image/png'
                        });

                        navigator.share({
                            files: [file],
                            title: 'Print QR Code',
                            text: 'QR Code untuk print'
                        }).catch(err => {
                            fallbackPrint(blob);
                        });
                    } else {
                        fallbackPrint(blob);
                    }
                }, 'image/png');
            };

            img.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat QR code'
                });
            };

            img.src = qrImage.src;
        }

        function fallbackPrint(blob) {
            const url = URL.createObjectURL(blob);
            const printWindow = window.open(url, '_blank');

            if (!printWindow) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pop-up Diblokir',
                    text: 'Mohon izinkan pop-up untuk print.'
                });
                return;
            }

            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 500);
            };
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            document.querySelectorAll('.comma-input').forEach(function(el) {
                el.addEventListener('input', function() {
                    const value = this.value;
                    if (value.includes('.')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Format Salah!',
                            text: 'Gunakan tanda koma (,) untuk desimal, bukan titik (.)',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = value.replace(/\./g, ',');
                    }
                });
            });

            let currentBlendingData = null;

            $('body').on('click', '#btnAnalisa', function() {
                let blendingId = $(this).attr('blending-id');

                // Reset form
                $('#form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');

                // Set ID
                $('#id').val(blendingId);

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal-mikro.getBlendingData') }}",
                    data: {
                        id: blendingId
                    },
                    dataType: "json",
                    success: function(response) {
                        currentBlendingData = response.data;
                        showNextField();
                        $('#modal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data blending.'
                        });
                    }
                });
            });

            function showNextField() {
                let eb = currentBlendingData.eb;
                let tpc = currentBlendingData.tpc;
                let ym = currentBlendingData.ym;

                // Sembunyikan semua field dulu
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#eb, #tpc, #ym').val('').prop('disabled', true);

                // ✅ Logika urutan: EB → TPC → YM (cek null/undefined secara eksplisit)
                if (eb === null || eb === undefined) {
                    // Jika EB belum diisi
                    $('#modalTitle').text('Input Data Analisa - EB');
                    $('#statusText').text('Langkah 1/3 - Input EB terlebih dahulu');
                    $('#ebContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false).focus();

                } else if (tpc === null || tpc === undefined) {
                    // Jika EB sudah, tapi TPC belum
                    $('#modalTitle').text('Input Data Analisa - TPC');
                    $('#statusText').html(`EB sudah diisi: <strong>${eb}</strong><br>Langkah 2/3 - Input TPC`);
                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();

                } else if (ym === null || ym === undefined) {
                    // Jika EB & TPC sudah, tapi YM belum
                    $('#modalTitle').text('Input Data Analisa - YM');
                    $('#statusText').html(
                        `EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong><br>Langkah 3/3 - Input YM (terakhir)`
                    );
                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();

                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Lengkap',
                        text: 'Semua parameter analisa sudah diisi.'
                    });
                    $('#modal').modal('hide');
                }
            }

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('analisa.blending-awal-mikro.update') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSave').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;

                        if (xhr.status === 409 && response && response.message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Disimpan',
                                text: response.message,
                            });
                            return;
                        }

                        if (xhr.status === 422 && response && response.errors) {
                            let errors = response.errors;

                            if (errors.eb) {
                                $('#eb').addClass('is-invalid');
                                $('.errorEb').html(errors.eb.join('<br>'));
                            }
                            if (errors.tpc) {
                                $('#tpc').addClass('is-invalid');
                                $('.errorTpc').html(errors.tpc.join('<br>'));
                            }
                            if (errors.ym) {
                                $('#ym').addClass('is-invalid');
                                $('.errorYm').html(errors.ym.join('<br>'));
                            }
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>
@endsection
