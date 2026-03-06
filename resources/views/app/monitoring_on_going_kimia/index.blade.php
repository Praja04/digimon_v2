@extends('layouts.component.main')
@section('title', 'Monitoring On Going Kimia')
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
                                <li class="breadcrumb-item"><a href="{{ route('monitoring-daily-tank.menu') }}">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Daftar @yield('title')</h5>
                        </div>
                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-secondary flex-fill">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                                @if (Auth::user()->role === 'Analis Field')
                                    <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end">
                                        <button type="button" id="btnAdd" class="btn btn-success w-100">
                                            <i class="mdi mdi-plus"></i> Tambah Data
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <!-- End Filter Section -->

                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Storage</th>
                                            <th>Nomor PO</th>
                                            <th>Variant</th>
                                            <th>Tanggal Filling</th>
                                            <th>Status</th>
                                            <th>Detail</th>
                                            <th>Analisa</th>
                                            <th width="1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Monitoring On Going Kimia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-4">
                            <input type="hidden" name="id" id="id">
                            <label for="tanggal_produksi" class="form-label">Tanggal Produksi <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="tanggal_produksi" id="tanggal_produksi" class="form-control">
                            <small class="errorTanggalProduksi text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="storage" class="form-label">Storage <span style="color: red;">*</span></label>
                            <select name="storage" id="storage" class="form-control">
                                <option value="">-- Pilih Storage --</option>
                                <optgroup label="A">
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A3">A3</option>
                                    <option value="A4">A4</option>
                                    <option value="A5">A5</option>
                                </optgroup>
                                <optgroup label="B">
                                    <option value="B1">B1</option>
                                    <option value="B2">B2</option>
                                    <option value="B3">B3</option>
                                    <option value="B4">B4</option>
                                    <option value="B5">B5</option>
                                </optgroup>
                                <optgroup label="C">
                                    <option value="C1">C1</option>
                                    <option value="C2">C2</option>
                                    <option value="C3">C3</option>
                                    <option value="C4">C4</option>
                                    <option value="C5">C5</option>
                                </optgroup>
                                <optgroup label="D">
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="D5">D5</option>
                                </optgroup>
                            </select>
                            <small class="text-danger errorStorage"></small>
                        </div>

                        <div class="col-lg-4">
                            <label for="nomor_po" class="form-label">Nomor PO <span style="color: red;">*</span></label>
                            <select id="nomor_po" name="nomor_po" class="select2 form-control">
                                <option value="">-- Pilih Nomor PO --</option>
                            </select>
                            <small class="text-danger errorNomorPO"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="variant" class="form-label">Variant <span style="color: red;">*</span></label>
                            <select id="variant" name="variant" class="select2 form-control">
                                <option value="">-- Pilih Variant --</option>
                            </select>
                            <small class="text-danger errorVariant"></small>
                        </div>
                        <div class="col-lg-6">
                            <label for="filling_date" class="form-label">Tanggal Filling <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="filling_date" id="filling_date" class="form-control">
                            <small class="text-danger errorFillingDate"></small>
                        </div>
                        <div class="col-lg-6">
                            <label for="jam_koding" class="form-label">Jam Koding <span
                                    style="color: red;">*</span></label>
                            <input type="time" name="jam_koding" id="jam_koding" class="form-control">
                            <small class="text-danger errorJamKoding"></small>
                        </div>
                        <div class="col-lg-6">
                            <label for="koding" class="form-label">Koding <span style="color: red;">*</span></label>
                            <input type="text" name="koding" id="koding" class="form-control"
                                oninput="this.value = this.value.toUpperCase();">
                            <small class="text-danger errorKoding"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Monitoring Ongoing Kimia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- QR Code -->
                    <div class="text-center mb-3 pb-3 border-bottom" id="qrPrintAreaDetail">
                        <div id="qr_code_container" class="mb-2"></div>
                        <p class="small text-muted mb-2" id="qr_code_text">-</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="printQR('qrPrintAreaDetail')">
                            Cetak QR
                        </button>
                    </div>

                    <!-- Informasi Dasar -->
                    <div class="mb-3">
                        <h6 class="mb-2 fw-bold">Informasi Dasar</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="35%">Storage</td>
                                <td width="5%">:</td>
                                <td><strong id="detail_storage">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nomor PO</td>
                                <td>:</td>
                                <td><strong id="detail_po_number">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Variant</td>
                                <td>:</td>
                                <td><strong id="detail_variant">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Koding</td>
                                <td>:</td>
                                <td><strong id="detail_koding">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Filling</td>
                                <td>:</td>
                                <td><strong id="detail_filling_date">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jam Koding</td>
                                <td>:</td>
                                <td><strong id="detail_jam_koding">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat Pada</td>
                                <td>:</td>
                                <td><strong id="detail_created_at">-</strong></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Data Analisa Lab Kimia -->
                    <div class="mb-3 pt-3 border-top">
                        <h6 class="mb-2 fw-bold">Data Analisa Lab Kimia</h6>
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted d-block">Shift</small>
                                <strong id="detail_shift">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Analis</small>
                                <strong id="detail_analis">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Discan Pada</small>
                                <strong id="detail_scan_at">-</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Uji Kimia -->
                    <div class="mb-3 pt-3 border-top">
                        <h6 class="mb-2 fw-bold">Parameter Uji Kimia</h6>
                        <div class="row g-2">
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">BRIX</small>
                                    <strong id="detail_brix">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">NACL</small>
                                    <strong id="detail_nacl">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">BJ</small>
                                    <strong id="detail_berat_jenis">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Visco</small>
                                    <strong id="detail_visco">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">AW</small>
                                    <strong id="detail_aw">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">pH</small>
                                    <strong id="detail_ph">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Warna</small>
                                    <strong id="detail_color">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Organo</small>
                                    <strong id="detail_organo">-</strong>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted d-block">Status Parameter</small>
                            <div id="detail_status">-</div>
                        </div>
                    </div>

                    <!-- Hasil & Disposisi -->
                    <div class="pt-3 border-top">
                        <h6 class="mb-2 fw-bold">Hasil & Disposisi</h6>
                        <div class="mb-2">
                            <small class="text-muted d-block">Disposisi</small>
                            <strong id="detail_disposition">-</strong>
                        </div>
                        <div id="remarks_section" class="mb-2" style="display: none;">
                            <small class="text-muted d-block">Catatan</small>
                            <div class="border rounded p-2 bg-light">
                                <em id="detail_remarks" class="small">-</em>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Diupdate Pada</small>
                            <strong id="detail_updated_at">-</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.select2').select2({
            placeholder: '-- Pilih Opsi --',
            dropdownParent: $('#modal')
        });

        function loadVariantByPo(production_batch_id) {
            const $variant = $('#variant');

            // Reset variant dropdown
            $variant.empty().append('<option value="">-- Pilih Variant --</option>');
            $('.errorVariant').html('');

            if (production_batch_id) {
                $.ajax({
                    url: "{{ route('monitoring-ongoing-kimia.get-variant') }}",
                    type: "POST",
                    data: {
                        production_batch_id: production_batch_id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $variant.prop('disabled', true);
                        $variant.empty().append('<option value="">Memuat variant...</option>');
                    },
                    success: function(response) {
                        $variant.prop('disabled', false);

                        if (response.status === 'success' && response.count > 0) {
                            $variant.empty().append(
                                '<option value="">-- Pilih Variant --</option>');

                            response.variant_list.forEach(item => {
                                $variant.append(
                                    `<option value="${item.display_name}">${item.display_name}</option>`
                                );
                            });

                            // Auto-select jika hanya ada 1 variant
                            if (response.count === 1) {
                                $variant.val(response.variant_list[0].display_name).trigger(
                                    'change');
                            }
                        } else {
                            $variant.empty().append(
                                '<option value="">-- Tidak Ada Variant --</option>');
                            $('.errorVariant').html(
                                '<small class="text-danger">Tidak ada variant yang tersedia untuk PO ini.</small>'
                            );
                        }
                    },
                    error: function(xhr) {
                        $variant.prop('disabled', false);
                        $variant.empty().append(
                            '<option value="">-- Gagal mengambil data --</option>');
                        $('.errorVariant').html(
                            '<small class="text-danger">Terjadi kesalahan saat mengambil data variant.</small>'
                        );

                        console.error('Error:', xhr.responseJSON);
                    }
                });
            } else {
                $variant.prop('disabled', true);
            }
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
            const printWindow = window.open('', '_blank', 'width=320,height=400');

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
                            size: 75mm 100mm;
                            margin: 0;
                        }
                        
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        
                        html, body {
                            width: 75mm;
                            height: 100mm;
                            margin: 0 auto;
                            font-family: Arial, sans-serif;
                            background: white;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        
                        .container {
                            width: 75mm;
                            height: 100mm;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            padding: 4mm;
                            page-break-after: avoid;
                            page-break-inside: avoid;
                            break-after: avoid;
                            break-inside: avoid;
                        }

                        .qr-image {
                            width: 58mm;
                            height: 58mm;
                            display: block;
                            flex-shrink: 0;
                        }

                        .qr-label {
                            font-size: 8pt;
                            color: #000;
                            word-wrap: break-word;
                            line-height: 1.4;
                            margin-top: 3mm;
                            width: 100%;
                            overflow: hidden; 
                        }
                        
                        @media print {
                            html, body {
                                width: 75mm;
                                height: 100mm;
                                overflow: hidden;
                            }
                            .container {
                                page-break-after: avoid;
                                break-after: avoid;
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

            canvas.width = 302;
            canvas.height = 378;

            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                const qrSize = 220;
                const qrX = (canvas.width - qrSize) / 2;
                const qrY = (canvas.height - qrSize) / 2 - 15;

                ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

                ctx.fillStyle = 'black';
                ctx.font = 'bold 11px Arial';
                ctx.textAlign = 'center';
                const labelText = qrLabel ? qrLabel.textContent.trim() : '';
                const maxWidth = 270;
                const lineHeight = 15;
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
                    if (navigator.share && navigator.canShare && navigator.canShare({
                            files: [new File([blob], 'qr.png', {
                                type: 'image/png'
                            })]
                        })) {
                        navigator.share({
                            files: [new File([blob], 'qr-code.png', {
                                type: 'image/png'
                            })],
                            title: 'Print QR Code'
                        }).catch(() => fallbackPrint(blob));
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

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('monitoring-ongoing-kimia.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'storage',
                        name: 'storage'
                    },
                    {
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'variant',
                        name: 'variant'
                    },
                    {
                        data: 'filling_date',
                        name: 'filling_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'analisa',
                        name: 'analisa',
                        orderable: false,
                        searchable: false,
                        visible: {{ in_array(auth()->user()->role, ['Foreman', 'Analis Kimia', 'Analis Mikro']) ? 'true' : 'false' }}
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: {{ in_array(auth()->user()->role, ['Analis Field']) ? 'true' : 'false' }}
                    }
                ]
            });

            // Tombol Filter
            $('#btnFilter').click(function() {
                table.ajax.reload();
            });

            // Tombol Reset
            $('#btnReset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

            // Filter otomatis saat tekan Enter pada input tanggal
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) {
                    table.ajax.reload();
                }
            });

            $('body').on('click', '#btnAdd', function() {
                $('#form').trigger("reset");
                $('#id').val('');

                $('#storage').val('').trigger('change');
                $('#nomor_po').val('').trigger('change').prop('disabled', true);
                $('#variant').val('').trigger('change').prop('disabled', true);

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-ongoing-kimia.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");

                        $('#form').trigger("reset");
                        $('#id').val('');

                        $('#storage').val('').trigger('change');
                        $('#nomor_po').val('').trigger('change').prop('disabled', true);
                        $('#variant').val('').trigger('change').prop('disabled', true);

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#storage').val(response.storage).trigger('change');
                        $('#nomor_po').val(response.production_batch_id).trigger('change');
                        $('#variant').val(response.variant).trigger('change');
                        $('#filling_date').val(response.filling_date);
                        $('#jam_koding').val(response.jam_koding);

                        $('#modal').modal('show');
                    }
                });
            })

            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-ongoing-kimia.show', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        // Reset semua field ke default
                        $('#detail_storage, #detail_po_number, #detail_koding, #detail_variant, #detail_filling_date, #detail_jam_koding, #detail_created_at')
                            .text('-');
                        $('#detail_analis, #detail_shift, #detail_scan_at').text('-');
                        $('#detail_berat_jenis, #detail_visco, #detail_brix, #detail_aw, #detail_nacl, #detail_ph, #detail_color, #detail_organo')
                            .text('-');
                        $('#detail_disposition, #detail_remarks, #detail_updated_at').text('-');
                    },
                    success: function(response) {
                        // QR Code
                        if (response.qr_code) {
                            $('#qr_code_container').html('<img src="data:image/png;base64,' +
                                response.qr_code +
                                '" alt="QR Code" style="max-width: 150px;">');
                            let qrText = 'MONITORING-ONGOING-KIMIA/' + response.po_number +
                                '/' + response
                                .date + '/' + response.id;
                            $('#qr_code_text').text(qrText);
                        } else {
                            $('#qr_code_container').html(
                                '<p class="text-muted small">QR Code tidak tersedia</p>');
                            $('#qr_code_text').text('-');
                        }

                        // Informasi Dasar
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_po_number').text(response.po_number || '-');
                        $('#detail_variant').text(response.variant || '-');
                        $('#detail_koding').text(response.koding || '-');
                        $('#detail_filling_date').text(response.filling_date_formatted || '-');
                        $('#detail_jam_koding').text(response.jam_koding || '-');
                        $('#detail_created_at').text(response.created_at_formatted || '-');

                        // Data Analisa
                        $('#detail_shift').text(response.shift ? "Shift " + response.shift :
                            '-');
                        $('#detail_analis').text(response.analis_name || '-');
                        $('#detail_scan_at').text(response.scan_at_formatted || '-');

                        // Parameter Analisa
                        $('#detail_brix').text(response.brix || '-');
                        $('#detail_nacl').text(response.nacl || '-');
                        $('#detail_berat_jenis').text(response.berat_jenis || '-');
                        $('#detail_visco').text(response.visco || '-');
                        $('#detail_aw').text(response.aw || '-');
                        $('#detail_ph').text(response.ph || '-');
                        $('#detail_color').text(response.color_name ? response.color_name +
                            ' (' + response.color_code + ')' : '-');
                        $('#detail_organo').text(response.organo || '-');

                        // Status Parameter
                        let statusHtml = '-';
                        if (response.status === 'OK') {
                            statusHtml = '<span class="badge bg-success">OK</span>';
                        } else if (response.status === 'NOT OK') {
                            statusHtml = '<span class="badge bg-danger">NOT OK</span>';
                        }
                        $('#detail_status').html(statusHtml);

                        // Disposisi
                        $('#detail_disposition').text(response.disposition || '-');

                        // Catatan (tampilkan hanya jika ada)
                        if (response.remarks) {
                            $('#remarks_section').show();
                            $('#detail_remarks').text(response.remarks);
                        } else {
                            $('#remarks_section').hide();
                        }

                        $('#detail_updated_at').text(response.updated_at_formatted || '-');

                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal memuat detail data.',
                        });
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-ongoing-kimia.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#modal').modal('hide');
                        $('#form').trigger("reset");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.tanggal_produksi) {
                                $('#tanggal_produksi').addClass('is-invalid');
                                $('.errorTanggalProduksi').html(errors.tanggal_produksi.join(
                                    '<br>'));
                            }
                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage.join('<br>'));
                            }
                            if (errors.nomor_po) {
                                $('#nomor_po').addClass('is-invalid');
                                $('.errorNomorPO').html(errors.nomor_po.join('<br>'));
                            }

                            if (errors.variant) {
                                $('#variant').addClass('is-invalid');
                                $('.errorVariant').html(errors.variant.join('<br>'));
                            }

                            if (errors.filling_date) {
                                $('#filling_date').addClass('is-invalid');
                                $('.errorFillingDate').html(errors.filling_date.join('<br>'));
                            }

                            if (errors.jam_koding) {
                                $('#jam_koding').addClass('is-invalid');
                                $('.errorJamKoding').html(errors.jam_koding.join('<br>'));
                            }

                            if (errors.koding) {
                                $('#koding').addClass('is-invalid');
                                $('.errorKoding').html(errors.koding.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                })
            })

            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "monitoring-ongoing-kimia/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                });
                                table.ajax.reload();
                            }
                        });
                    }
                })
            })

            $('#nomor_po').on('change', function() {
                const production_batch_id = $(this).val();
                loadVariantByPo(production_batch_id);
            });

            $('#tanggal_produksi, #storage').on('change', function() {
                const tanggal_produksi = $('#tanggal_produksi').val();
                const storage = $('#storage').val();
                const $nomorPO = $('#nomor_po');

                $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                if (tanggal_produksi && storage) {
                    $.ajax({
                        url: "{{ route('monitoring-ongoing-kimia.get-po') }}",
                        type: "POST",
                        data: {
                            tanggal_produksi: tanggal_produksi,
                            storage: storage
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $nomorPO.prop('disabled', true);
                        },
                        success: function(response) {
                            $nomorPO.prop('disabled', false);

                            if (response.status === 'success' && response.count > 0) {

                                response.po_list.forEach(item => {
                                    $nomorPO.append(
                                        `<option value="${item.id}">${item.po_number}</option>`
                                    );
                                });

                                if (response.count === 1) {
                                    $nomorPO.val(response.selected_id).trigger('change');
                                    loadVariantByPo(response.selected_id);
                                } else if (response.count > 1) {
                                    $nomorPO.val('').trigger('change');
                                }
                            } else {
                                $nomorPO.empty().append(
                                        '<option value="">-- Tidak Ada PO Release --</option>')
                                    .val('');
                                $('.errorNomorPO').html(
                                    '<small class="text-danger">Tidak ada Nomor PO yang Release.</small>'
                                );
                            }
                        },
                        error: function() {
                            $nomorPO.prop('disabled', false);
                            $nomorPO.empty().append(
                                '<option value="">-- Gagal mengambil data --</option>');
                            $('.errorNomorPO').html(
                                '<small class="text-danger">Terjadi kesalahan saat mengambil data PO.</small>'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
