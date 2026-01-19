@extends('layouts.component.main')
@section('title', 'Press Test - Data')
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
                            <button id="btnAdd" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Data
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Analis</th>
                                            <th>Shift</th>
                                            <th>Variant</th>
                                            <th>Batas</th>
                                            <th>Mesin</th>
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
                            <label for="nama_analis" class="form-label">Nama Analis <span
                                    style="color: red">*</span></label>
                            <input type="text" id="nama_analis" name="nama_analis" class="form-control" autofocus>
                            <small class="text-danger errorNamaAnalis"></small>
                        </div>
                        <div class="mb-3">
                            <label for="variant" class="form-label">Variant <span style="color: red">*</span></label>
                            <select id="variant" name="variant" class="form-control select2">
                                <option value="">Pilih Variant</option>
                                <option value="P 77">P 77</option>
                                <option value="P 250">P 250</option>
                                <option value="P 270">P 270</option>
                                <option value="P 550">P 550</option>
                                <option value="P 700">P 700</option>
                                <option value="P 725">P 725</option>
                                <option value="P 1000">P 1000</option>
                            </select>
                            <small class="text-danger errorVariant"></small>
                        </div>
                        <div class="mb-3">
                            <label for="batas" class="form-label">Batas (Cm) <span style="color: red">*</span></label>
                            <input type="text" id="batas" name="batas" class="form-control" readonly>
                            <small class="text-danger errorBatas"></small>
                        </div>
                        <div class="mb-3">
                            <label for="mesin" class="form-label">Mesin <span style="color: red">*</span></label>
                            <select id="mesin" name="mesin" class="form-control select2">
                                <option value="">Pilih Mesin</option>
                                <option value="Mesin 1">Mesin 1</option>
                                <option value="Mesin 2">Mesin 2</option>
                                <option value="Mesin 3">Mesin 3</option>
                                <option value="Mesin 4">Mesin 4</option>
                                <option value="Mesin 5">Mesin 5</option>
                            </select>
                            <small class="text-danger errorMesin"></small>
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
        $('.select2').select2({
            placeholder: '-- Pilih Opsi --',
            dropdownParent: $('#modal')
        });

        const variantRanges = {
            'P 77': {
                min: 0.4,
                max: 0.9
            },
            'P 250': {
                min: 1.45,
                max: 1.60
            },
            'P 270': {
                min: 1.45,
                max: 1.60
            },
            'P 550': {
                min: 1.9,
                max: 2.3
            },
            'P 700': {
                min: 2.3,
                max: 2.6
            },
            'P 725': {
                min: 2.35,
                max: 3.05
            },
            'P 1000': {
                min: 2.7,
                max: 3.3
            }
        };

        function generateBatas(variant) {
            if (variantRanges[variant]) {
                const range = variantRanges[variant];
                const batas = range.min + 2;
                return batas;
            }
            return '';
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#variant').on('change', function() {
                const selectedVariant = $(this).val();
                if (selectedVariant) {
                    const batasValue = generateBatas(selectedVariant);
                    $('#batas').val(batasValue);
                } else {
                    $('#batas').val('');
                }
            });

            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('press-test-data.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_analis',
                        name: 'nama_analis'
                    },
                    {
                        data: 'shift',
                        name: 'shift'
                    },
                    {
                        data: 'variant',
                        name: 'variant'
                    },
                    {
                        data: 'batas',
                        name: 'batas'
                    },
                    {
                        data: 'mesin',
                        name: 'mesin'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('body').on('click', '#btnAdd', function() {
                $('#id').val('');
                $('#modalLabel').html("Tambah Data");
                $('#modal').modal('show');
                $('#form').trigger("reset");

                $('#variant').val('').trigger('change');
                $('#mesin').val('').trigger('change');

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('press-test-data.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#modalLabel').html("Edit Data");
                        $('#save').val("edit-data");
                        $('#modal').modal('show');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#variant').val(response.variant).trigger('change');
                        $('#mesin').val(response.mesin).trigger('change');

                        $('#id').val(response.id);
                        $('#nama_analis').val(response.nama_analis);
                        $('#batas').val(response.batas);
                    }
                });
            })

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('press-test-data.store') }}",
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
                            if (errors.nama_analis) {
                                $('#nama_analis').addClass('is-invalid');
                                $('.errorNamaAnalis').html(errors.nama_analis.join('<br>'));
                            }
                            if (errors.batas) {
                                $('#batas').addClass('is-invalid');
                                $('.errorBatas').html(errors.batas.join('<br>'));
                            }
                            if (errors.variant) {
                                $('#variant').addClass('is-invalid');
                                $('.errorVariant').html(errors.variant.join('<br>'));
                            }
                            if (errors.mesin) {
                                $('#mesin').addClass('is-invalid');
                                $('.errorMesin').html(errors.mesin.join('<br>'));
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
                            url: "press-test-data/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                });
                                $('#datatable').DataTable().ajax.reload()
                            }
                        });
                    }
                })
            })
        });
    </script>
@endsection
