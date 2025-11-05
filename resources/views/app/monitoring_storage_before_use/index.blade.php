@extends('layouts.component.main')
@section('title', 'Monitoring Storage Before Use')
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
                                            <th>Jenis Sample</th>
                                            <th>Waktu Selesai</th>
                                            <th>Estimasi Kadaluarsa</th>
                                            <th>Hasil</th>
                                            <th>Detail</th>
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
                        <h5 class="modal-title">Input Monitoring Before Use</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-12">
                            <input type="hidden" name="id" id="id">
                            <label for="storage" class="form-label">Storage <span style="color: red;">*</span></label>
                            <select name="storage" id="storage" class="form-select">
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

                        <div class="col-lg-12">
                            <label for="jenis_sample" class="form-label">Jenis
                                Sample <span style="color: red;">*</span></label>
                            <select id="jenis_sample" name="jenis_sample" id="jenis_sample" class="form-select">
                                <option value="">-- Pilih Jenis Sample --</option>
                                <option value="Before Tiban">Before Tiban</option>
                                <option value="Flushing">Flushing</option>
                            </select>
                            <small class="text-danger errorJenisSample"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Waktu Selesai Pemakaian <span style="color: red;">*</span></label>
                            <input type="datetime-local" name="waktu_selesai_pemakaian" id="waktu_selesai_pemakaian"
                                class="form-control">
                            <small class="text-danger errorWaktuSelesaiPemakaian"></small>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Estimasi Kadaluarsa <span style="color: red;">*</span></label>
                            <input type="datetime-local" name="estimasi_kadaluarsa" id="estimasi_kadaluarsa"
                                class="form-control">
                            <small class="text-danger errorEstimasiKadaluarsa"></small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formAnalisa">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Analisa Storage</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-12">
                            <input type="hidden" name="id_analisa" id="id_analisa">
                            <label class="form-label">Visco <span style="color: red;">*</span></label>
                            <input type="text" name="visco" id="visco" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorVisco"></small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Brix <span style="color: red;">*</span></label>
                            <input type="text" name="brix" id="brix" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBrix"></small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">AW <span style="color: red;">*</span></label>
                            <input type="text" name="aw" id="aw" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorAw"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="saveAnalisa">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">Detail Monitoring Storage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- QR Code Section -->
                    <div class="text-center mb-3" id="qrPrintAreaDetail">
                        <div style="display: inline-block;" id="qr_code_container">
                            <!-- QR Code akan dimuat di sini -->
                        </div>
                        <p class="mt-2 mb-2 small" id="qr_code_text">-</p>
                    </div>

                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-sm btn-primary" onclick="printQR('qrPrintAreaDetail')">
                            <i class="mdi mdi-printer"></i> Cetak QR
                        </button>
                    </div>

                    <hr class="my-2">

                    <!-- Detail Data dalam Grid 2 Kolom -->
                    <div class="row g-2 small">
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Storage</span>
                                <strong id="detail_storage">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Jenis Sample</span>
                                <strong id="detail_jenis_sample">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Waktu Selesai</span>
                                <strong id="detail_waktu_selesai">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Estimasi Kadaluarsa</span>
                                <strong id="detail_estimasi">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Hasil Analisa dalam Grid 3 Kolom -->
                    <h6 class="mb-2 small">Hasil Analisa</h6>
                    <div class="row g-2 small">
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Visco</span>
                                <strong id="detail_visco">-</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Brix</span>
                                <strong id="detail_brix">-</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">AW</span>
                                <strong id="detail_aw">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Hasil</span>
                                <span id="detail_hasil">-</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <span class="text-muted d-block">Dibuat Pada</span>
                                <strong id="detail_created_at">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            win.document.write('<html><head><title>Print QR Code</title>');
            win.document.write('<style>body{text-align:center; font-size:14px; padding:20px;}</style>');
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

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('monitoring-storage-before-use.index') }}",
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
                        data: 'jenis_sample',
                        name: 'jenis_sample'
                    },
                    {
                        data: 'waktu_selesai_pemakaian',
                        name: 'waktu_selesai_pemakaian'
                    },
                    {
                        data: 'estimasi_kadaluarsa',
                        name: 'estimasi_kadaluarsa'
                    },
                    {
                        data: 'hasil',
                        name: 'hasil',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                $('#jenis_sample').val('').trigger('change');

                $('.form-control, .form-select').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-storage-before-use.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");
                        $('#modal').modal('show');

                        $('#storage').val('').trigger('change');
                        $('#jenis_sample').val('').trigger('change');

                        $('.form-control, .form-select').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#storage').val(response.storage).trigger('change');
                        $('#jenis_sample').val(response.jenis_sample).trigger('change');
                        $('#waktu_selesai_pemakaian').val(response.waktu_selesai_pemakaian);
                        $('#estimasi_kadaluarsa').val(response.estimasi_kadaluarsa);
                    }
                });
            })

            $('body').on('click', '#btnAnalisa', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-storage-before-use.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#modalAnalisa').modal('show');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id_analisa').val(response.id);
                        $('#visco').val(response.visco);
                        $('#brix').val(response.brix);
                        $('#aw').val(response.aw);
                    }
                });
            })

            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-storage-before-use.show', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        // Isi data ke modal
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_jenis_sample').text(response.jenis_sample || '-');
                        $('#detail_waktu_selesai').text(response
                            .waktu_selesai_pemakaian_formatted || '-');
                        $('#detail_estimasi').text(response.estimasi_kadaluarsa_formatted ||
                            '-');

                        // Untuk angka, cek null/undefined saja, 0 tetap ditampilkan
                        $('#detail_visco').text(response.visco !== null && response.visco !==
                            undefined ? response.visco : '-');
                        $('#detail_brix').text(response.brix !== null && response.brix !==
                            undefined ? response.brix : '-');
                        $('#detail_aw').text(response.aw !== null && response.aw !== undefined ?
                            response.aw : '-');

                        $('#detail_created_at').text(response.created_at_formatted || '-');

                        // Tampilkan badge hasil
                        let hasilBadge = '-';
                        if (response.hasil) {
                            let badgeClass = 'bg-secondary';
                            if (response.hasil === 'OK') badgeClass = 'bg-success';
                            else if (response.hasil === 'NOT OK') badgeClass = 'bg-danger';
                            else if (response.hasil === 'PENDING') badgeClass =
                                'bg-warning text-dark';

                            hasilBadge = '<span class="badge ' + badgeClass + '">' + response
                                .hasil + '</span>';
                        }
                        $('#detail_hasil').html(hasilBadge);

                        // Tampilkan QR Code
                        if (response.qr_code) {
                            $('#qr_code_container').html('<img src="data:image/png;base64,' +
                                response.qr_code + '" alt="QR Code">');
                        } else {
                            $('#qr_code_container').html(
                                '<p class="text-muted">QR Code tidak tersedia</p>');
                        }

                        // Format text untuk QR Code
                        let qrText = 'STORAGE-' + response.storage + '/' + response
                            .jenis_sample + '/' + response.id;
                        $('#qr_code_text').text(qrText);

                        // Tampilkan modal
                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data detail.',
                        });
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-storage-before-use.store') }}",
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
                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage.join('<br>'));
                            }

                            if (errors.jenis_sample) {
                                $('#jenis_sample').addClass('is-invalid');
                                $('.errorJenisSample').html(errors.jenis_sample.join('<br>'));
                            }

                            if (errors.jenis_sample) {
                                $('#waktu_selesai_pemakaian').addClass('is-invalid');
                                $('.errorWaktuSelesaiPemakaian').html(errors
                                    .waktu_selesai_pemakaian.join('<br>'));
                            }

                            if (errors.jenis_sample) {
                                $('#estimasi_kadaluarsa').addClass('is-invalid');
                                $('.errorEstimasiKadaluarsa').html(errors
                                    .estimasi_kadaluarsa.join('<br>'));
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

            $('#formAnalisa').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-storage-before-use.storeAnalisa') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveAnalisa').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveAnalisa').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#modalAnalisa').modal('hide');
                        $('#formAnalisa').trigger("reset");
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
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
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
                            url: "monitoring-storage-before-use/" + id,
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
        });
    </script>
@endsection
