@extends('layouts.component.main')
@section('title', 'Monitoring Turun Blending')
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
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="status_filter" class="form-label">Status</label>
                                    <select id="status_filter" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="complete">Selesai Analisa</option>
                                        <option value="progress">On Progress</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-secondary flex-fill">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <!-- End Filter Section -->

                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PO</th>
                                            <th>Varian</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
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

    <!-- modal -->
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLabel"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="id" id="id">
                            <label for="po_number" class="form-label">Nomor PO <span style="color: red">*</span></label>
                            <input type="text" id="po_number" name="po_number" class="form-control">
                            <small class="text-danger errorPONumber"></small>
                        </div>
                        <div class="mb-3">
                            <label for="variant" class="form-label">Varian <span style="color: red">*</span></label>
                            <select id="variant" name="variant" class="select2 form-control">
                                <option value="">-- Pilih Varian --</option>
                                <option value="SS1">SS1</option>
                                <option value="SS2">SS2</option>
                                <option value="BB">BB</option>
                                <option value="MSD NR1">MSD NR1</option>
                                <option value="MSD NR2">MSD NR2</option>
                            </select>
                            <small class="text-danger errorVariant"></small>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal Produksi <span
                                    style="color: red">*</span></label>
                            <input type="date" name="date" id="date" class="form-control" />
                            <small class="text-danger errorDate"></small>
                        </div>
                        <div class="mb-3">
                            <label for="batch_range" class="form-label">Rentang Batch Masak <span
                                    style="color: red">*</span></label>
                            <input type="text" name="batch_range" class="form-control" placeholder="Contoh: 1-10"
                                id="batch_range" />
                            <small class="text-danger errorBatchRange"></small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" name="description" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('scripts')
    <script>
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
                    url: "{{ route('monitoring-turun-blending.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status',
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
                $('#status_filter').val('');
                table.ajax.reload();
            });

            // Filter otomatis saat tekan Enter pada input tanggal
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) {
                    table.ajax.reload();
                }
            });

            // Filter otomatis saat status berubah
            $('#status_filter').on('change', function() {
                table.ajax.reload();
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-turun-blending.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#modalLabel').html("Edit Data");
                        $('#save').val("edit-data");
                        $('#modal').modal('show');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#role').val('').trigger('change');
                        $('#position').val('').trigger('change');

                        $('#id').val(response.id);
                        $('#po_number').val(response.po_number);
                        $('#variant').val(response.variant).trigger('change');
                        $('#date').val(response.date);
                        $('#batch_range').val(response.batch_range);
                        $('#description').val(response.description);
                    }
                });
            })

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-turun-blending.update') }}",
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
                            if (errors.po_number) {
                                $('#po_number').addClass('is-invalid');
                                $('.errorPONumber').html(errors.po_number.join('<br>'));
                            }

                            if (errors.variant) {
                                $('#variant').addClass('is-invalid');
                                $('.errorVariant').html(errors.variant.join('<br>'));
                            }

                            if (errors.date) {
                                $('#date').addClass('is-invalid');
                                $('.errorDate').html(errors.date.join('<br>'));
                            }

                            if (errors.batch_range) {
                                $('#batch_range').addClass('is-invalid');
                                $('.errorBatchRange').html(errors.batch_range.join('<br>'));
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
                            url: "monitoring-turun-blending/" + id,
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
