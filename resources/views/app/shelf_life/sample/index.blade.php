@extends('layouts.component.main')
@section('title', 'Masuk Sample - Shelf Life')
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
                                <li class="breadcrumb-item"><a href="{{ route('shelf-life.index') }}">Menu</a></li>
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
                                            <th>Tanggal Produksi</th>
                                            <th>Nomor PO</th>
                                            <th>Storage</th>
                                            <th>Progress</th>
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
                        <h5 class="modal-title">Input Sample</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-12">
                            <input type="hidden" name="id" id="id">
                            <label for="tanggal_produksi" class="form-label">Tanggal Produksi <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="tanggal_produksi" id="tanggal_produksi" class="form-control">
                            <small class="errorTanggalProduksi text-danger"></small>
                        </div>

                        <div class="col-lg-12">
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
                                <optgroup abel="D">
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
                            <label for="nomor_po" class="form-label">Nomor PO <span style="color: red;">*</span></label>
                            <select name="nomor_po" id="nomor_po" class="form-control">
                                <option value="">-- Pilih Nomor PO --</option>
                            </select>
                            <small class="text-danger errorNomorPO"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                    url: "{{ route('shelf-life.sample.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data. Silakan refresh halaman.',
                        });
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_produksi',
                        name: 'tanggal_produksi'
                    },
                    {
                        data: 'nomor_po',
                        name: 'nomor_po'
                    },
                    {
                        data: 'storage',
                        name: 'storage'
                    },
                    {
                        data: 'progress',
                        name: 'progress',
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
            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            // Tombol Reset
            $('#btnReset').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

            // Enter key untuk filter
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // Tombol Tambah Data
            $(document).on('click', '#btnAdd', function() {
                $('#form')[0].reset();
                $('#id').val('');
                $('#storage').val('');
                $('#tanggal_produksi').val('');
                $('#nomor_po').empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            // Event handler untuk load PO saat tanggal produksi atau storage berubah
            $('#tanggal_produksi, #storage').on('change', function() {
                var tanggal_produksi = $('#tanggal_produksi').val();
                var storage = $('#storage').val();
                var $nomorPO = $('#nomor_po');

                $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                if (tanggal_produksi && storage) {
                    $.ajax({
                        url: "{{ route('shelf-life.sample.get-po') }}",
                        type: "POST",
                        data: {
                            tanggal_produksi: tanggal_produksi,
                            storage: storage
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $nomorPO.prop('disabled', true);
                            $nomorPO.html('<option value="">Loading...</option>');
                        },
                        success: function(response) {
                            $nomorPO.prop('disabled', false);
                            $nomorPO.empty().append(
                                '<option value="">-- Pilih Nomor PO --</option>');

                            if (response.status === 'success' && response.count > 0) {
                                $.each(response.po_list, function(index, item) {
                                    $nomorPO.append($('<option>', {
                                        value: item.id,
                                        text: item.po_number
                                    }));
                                });

                                // Auto-select jika hanya ada 1 PO
                                if (response.count === 1 && response.selected_id) {
                                    $nomorPO.val(response.selected_id);
                                }
                            } else {
                                $nomorPO.append(
                                    '<option value="">-- Tidak Ada PO Release --</option>');
                                $('.errorNomorPO').html(
                                    '<small class="text-danger">Tidak ada Nomor PO yang Release.</small>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Get PO Error:', xhr.responseText);
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

            // Tombol Edit
            $(document).on('click', '#btnEdit', function() {
                var id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ url('shelf-life/sample/edit') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        Swal.close();

                        $('#save').val("edit-data");
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        // Set form values
                        $('#id').val(response.id);
                        $('#tanggal_produksi').val(response.production_batch.date);
                        $('#storage').val(response.storage);

                        // Load PO dengan trigger change, lalu set nilai setelah selesai
                        var $nomorPO = $('#nomor_po');
                        var selectedPO = response.production_batch_id;

                        $.ajax({
                            url: "{{ route('shelf-life.sample.get-po') }}",
                            type: "POST",
                            data: {
                                tanggal_produksi: response.production_batch.date,
                                storage: response.storage
                            },
                            dataType: 'json',
                            beforeSend: function() {
                                $nomorPO.prop('disabled', true);
                                $nomorPO.html(
                                    '<option value="">Loading...</option>');
                            },
                            success: function(poResponse) {
                                $nomorPO.prop('disabled', false);
                                $nomorPO.empty().append(
                                    '<option value="">-- Pilih Nomor PO --</option>'
                                );

                                if (poResponse.status === 'success' && poResponse
                                    .count > 0) {
                                    $.each(poResponse.po_list, function(index,
                                        item) {
                                        $nomorPO.append($('<option>', {
                                            value: item.id,
                                            text: item.po_number
                                        }));
                                    });
                                    // Set selected PO
                                    $nomorPO.val(selectedPO);
                                }
                            }
                        });

                        $('#modal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Edit Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data.',
                        });
                    }
                });
            });

            // Submit Form
            $('#form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('shelf-life.sample.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...');
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#modal').modal('hide');
                        $('#form')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        console.error('Submit Error:', xhr.responseText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            if (errors.tanggal_produksi) {
                                $('#tanggal_produksi').addClass('is-invalid');
                                $('.errorTanggalProduksi').html(errors.tanggal_produksi[0]);
                            }
                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage[0]);
                            }
                            if (errors.nomor_po) {
                                $('#nomor_po').addClass('is-invalid');
                                $('.errorNomorPO').html(errors.nomor_po[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: xhr.responseJSON && xhr.responseJSON.message ? xhr
                                    .responseJSON.message :
                                    'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                });
            });

            // Tombol Delete
            $(document).on('click', '#btnDelete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('shelf-life/sample') }}/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                console.error('Delete Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menghapus data.',
                                });
                            }
                        });
                    }
                });
            });

            // Reset form saat modal ditutup
            $('#modal').on('hidden.bs.modal', function() {
                $('#form')[0].reset();
                $('#id').val('');
                $('#nomor_po').empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
            });
        });
    </script>
@endsection
