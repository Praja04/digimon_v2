@extends('layouts.component.main')
@section('title', 'RMPM')
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
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="jenis" class="form-label">Jenis</label>
                                    <select id="jenis" class="form-select">
                                        <option value="">-- Semua --</option>
                                        <option value="Gula Tebu">Gula Tebu</option>
                                        <option value="Gula Kelapa">Gula Kelapa</option>
                                        <option value="Gula">Gula</option>
                                        <option value="Garam">Garam</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2 d-flex align-items-end gap-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-secondary flex-fill">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end">
                                    <button type="button" id="btnAdd" class="btn btn-success w-100">
                                        <i class="mdi mdi-plus"></i> Tambah Data
                                    </button>
                                </div>
                            </div>
                            <!-- End Filter Section -->

                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>No SPB</th>
                                            <th>Jenis</th>
                                            <th>Supplier</th>
                                            <th>Tanggal Kedatangan</th>
                                            <th>Asal Bahan</th>
                                            <th>Jumlah Kedatangan</th>
                                            <th>Selesai Analisa</th>
                                            <th>QR Code</th>
                                            <th>Aksi</th>
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

    <!-- modal -->
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header border-0 pb-2">
                        <h5 class="modal-title" id="modalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jenis" class="form-label">Jenis Bahan <span
                                        style="color: red;">*</span></label>
                                <select class="form-control" name="jenis" id="jenis">
                                    <option value="">-- Pilih Jenis Bahan --</option>
                                    <option value="Gula Tebu">Gula Tebu</option>
                                    <option value="Gula Kelapa">Gula Kelapa</option>
                                    <option value="Gula">Gula</option>
                                    <option value="Garam">Garam</option>
                                </select>
                                <small class="text-danger errorJenis"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_kedatangan" class="form-label">Tanggal & Jam Kedatangan <span
                                        style="color: red;">*</span></label>
                                <input type="datetime-local" class="form-control" id="tanggal_kedatangan"
                                    name="tanggal_kedatangan" value="{{ now()->format('Y-m-d\TH:i') }}">
                                <small class="text-danger errorTanggalKedatangan"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier / Manufactur <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="supplier" name="supplier">
                                <small class="text-danger errorSupplier"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="asal_bahan" class="form-label">Asal Bahan <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="asal_bahan" name="asal_bahan">
                                <small class="text-danger errorAsalBahan"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="no_plat" class="form-label">No Plat <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="no_plat" name="no_plat">
                                <small class="text-danger errorNoPlat"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="no_spb" class="form-label">No SPB <span
                                        style="color: red;">*</span></label>
                                <input type="number" class="form-control" id="no_spb" name="no_spb">
                                <small class="text-danger errorNoSPB"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah_kedatangan" class="form-label">Jumlah Kedatangan (kg) <span
                                        style="color: red;">*</span></label>
                                <input type="number" class="form-control" id="jumlah_kedatangan"
                                    name="jumlah_kedatangan" placeholder="dalam kilogram">
                                <small class="text-danger errorJumlahKedatangan"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="lot_batch" class="form-label">Lot / Batch <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="lot_batch" name="lot_batch">
                                <small class="text-danger errorLotBatch"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="qrModalLabel"></h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="qrPrintArea">
                    <div style="display: inline-block; padding: 20px;" id="qrImageArea">
                        <!-- QR Image will be injected here -->
                    </div>
                    <div class="mt-3" id="qrLabelText"></div>
                </div>
                <div class="modal-footer justify-content-center py-2">
                    <button onclick="printQR('qrPrintArea')" class="btn btn-sm btn-success">
                        <span class="mdi mdi-printer"></span> Print
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printQR(id) {
            const content = document.getElementById(id).innerHTML;
            const win = window.open('', '', 'height=600,width=600');
            win.document.write('<html><head><title>Print QR</title>');
            win.document.write('<style>body{text-align:center; font-size:12px;}</style>');
            win.document.write('</head><body>');
            win.document.write(content);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            win.print();
            win.close();
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
                    url: "{{ route('rmpm.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.jenis = $('#jenis').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_spb',
                        name: 'no_spb'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'tanggal_kedatangan',
                        name: 'tanggal_kedatangan'
                    },
                    {
                        data: 'asal_bahan',
                        name: 'asal_bahan'
                    },
                    {
                        data: 'jumlah_kedatangan',
                        name: 'jumlah_kedatangan'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'qr_code',
                        name: 'qr_code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
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
                $('#id').val('');
                $('#modalLabel').html("Tambah Data - Identitas RM");
                $('#modal').modal('show');
                $('#form').trigger("reset");

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
            });

            $('body').on('click', '#btnQRCode', function() {
                let id = $(this).data('id');
                $('#id').val(id);
                $('#qrModalLabel').html("Cetak QR Code - Identitas RM " + id);

                // Show loading
                $('#qrImageArea').html(
                    '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>'
                );
                $('#qrLabelText').html('Memuat QR Code...');

                $('#qrModal').modal('show');

                // Fetch QR Code from server
                $.ajax({
                    url: "{{ route('rmpm.qrcode', '') }}/" + id,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Display QR Code
                            $('#qrImageArea').html('<img src="data:image/png;base64,' + response
                                .qrCode + '" alt="QR Code" style="max-width: 300px;">');
                            $('#qrLabelText').html('<strong>' + response.label +
                                '</strong><br>' +
                                'Tanggal: ' + response.tanggal);
                        } else {
                            $('#qrImageArea').html(
                                '<p class="text-danger">Gagal memuat QR Code</p>');
                            $('#qrLabelText').html('');
                        }
                    },
                    error: function(xhr) {
                        $('#qrImageArea').html(
                            '<p class="text-danger">Terjadi kesalahan saat memuat QR Code</p>'
                        );
                        $('#qrLabelText').html('');
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('rmpm.store') }}",
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
                        $('#datatable').DataTable().ajax.reload()
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.jenis) {
                                $('#jenis').addClass('is-invalid');
                                $('.errorJenis').html(errors.jenis.join('<br>'));
                            }
                            if (errors.tanggal_kedatangan) {
                                $('#tanggal_kedatangan').addClass('is-invalid');
                                $('.errorTanggalKedatangan').html(errors.tanggal_kedatangan
                                    .join('<br>'));
                            }
                            if (errors.supplier) {
                                $('#supplier').addClass('is-invalid');
                                $('.errorSupplier').html(errors.supplier
                                    .join('<br>'));
                            }
                            if (errors.asal_bahan) {
                                $('#asal_bahan').addClass('is-invalid');
                                $('.errorAsalBahan').html(errors.asal_bahan
                                    .join('<br>'));
                            }
                            if (errors.no_plat) {
                                $('#no_plat').addClass('is-invalid');
                                $('.errorNoPlat').html(errors.no_plat
                                    .join('<br>'));
                            }
                            if (errors.no_spb) {
                                $('#no_spb').addClass('is-invalid');
                                $('.errorNoSPB').html(errors.no_spb
                                    .join('<br>'));
                            }
                            if (errors.jumlah_kedatangan) {
                                $('#jumlah_kedatangan').addClass('is-invalid');
                                $('.errorJumlahKedatangan').html(errors.jumlah_kedatangan
                                    .join('<br>'));
                            }
                            if (errors.lot_batch) {
                                $('#lot_batch').addClass('is-invalid');
                                $('.errorLotBatch').html(errors.lot_batch
                                    .join('<br>'));
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
        });
    </script>
@endsection
