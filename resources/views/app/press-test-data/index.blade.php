@extends('layouts.component.main')
@section('title', 'Press Test - Data')

@section('styles')
    <style>
        .page-content {
            padding: 24px 0;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: calc(100vh - 60px);
        }

        .card {
            border: none;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -3px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 850;
            color: #1e293b;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        #btnAdd {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }

        #btnAdd:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            filter: brightness(1.05);
        }

        #btnAdd:active {
            transform: translateY(0);
        }

        .table-responsive {
            padding: 16px;
        }

        #datatable {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100% !important;
        }

        #datatable thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            border: none;
            padding: 12px 16px;
        }

        #datatable tbody tr {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        #datatable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            background: #fafbfc;
        }

        #datatable tbody td {
            padding: 14px 16px;
            border: none;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 500;
        }

        #datatable tbody td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            font-weight: bold;
            color: #64748b;
        }

        #datatable tbody td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Action Buttons */
        #datatable #btnEdit {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #d97706 !important;
            border: 1px solid rgba(245, 158, 11, 0.15) !important;
            border-radius: 6px;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 0.72rem;
            transition: all 0.2s ease;
        }

        #datatable #btnEdit:hover {
            background: #f59e0b !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        #datatable #btnDelete {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.15) !important;
            border-radius: 6px;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 0.72rem;
            transition: all 0.2s ease;
        }

        #datatable #btnDelete:hover {
            background: #ef4444 !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        /* Modal styling */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
        }

        .modal-body {
            padding: 24px;
            background: #fff;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s ease-in-out;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background-color: #fff;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            height: 42px !important;
            background-color: #f8fafc !important;
            transition: all 0.2s ease-in-out !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 14px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
            background-color: #fff !important;
        }

        .modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        .modal-footer .btn-light {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 0.8rem;
        }

        .modal-footer .btn-light:hover {
            background: #f1f5f9;
        }

        #save {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 22px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        #save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            filter: brightness(1.05);
        }
    </style>
@endsection

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
                                            <th>Nama Analis Field</th>
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
                            <label for="nama_analis_field" class="form-label">Nama Analis Field <span
                                    style="color: red">*</span></label>
                            <input type="text" id="nama_analis_field" name="nama_analis_field" class="form-control" autofocus>
                            <small class="text-danger errorNamaAnalisField"></small>
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
                            <input type="text" inputmode="decimal" id="batas" name="batas" class="form-control">
                            <small class="text-danger errorBatas"></small>
                        </div>
                        <div class="mb-3">
                            <label for="mesin_press_test" class="form-label">Mesin Press Test <span
                                    style="color: red">*</span></label>
                            <select id="mesin_press_test" name="mesin_press_test" class="form-control select2">
                                <option value="">Pilih Mesin</option>
                                <option value="P5">P5</option>
                                <option value="P6">P6</option>
                                <option value="P7">P7</option>
                                <option value="P8">P8</option>
                                <option value="P9">P9</option>
                            </select>
                            <small class="text-danger errorMesinPressTest"></small>
                        </div>
                        <div class="mb-3">
                            <label for="mesin_retail" class="form-label">Mesin Retail (Cooming Soon)</label>
                            <select id="mesin_retail" name="mesin_retail" class="form-control select2">
                                <option value="">Pilih Mesin</option>
                                <option value="D1 : D">D1 : D</option>
                                <option value="D2 : E">D2 : E</option>
                                <option value="D3 : F">D3 : F</option>
                                <option value="D4 : G">D4 : G</option>
                                <option value="D5 : H">D5 : H</option>
                                <option value="D6 : I">D6 : I</option>
                                <option value="D7 : J">D7 : J</option>
                                <option value="D8 : K">D8 : K</option>
                                <option value="D9 : C">D9 : C</option>
                                <option value="D10 : L">D10 : L</option>
                                <option value="D11 : B">D11 : B</option>
                                <option value="D12 : AE">D12 : AE</option>
                                <option value="D13 : AF">D13 : AF</option>
                                <option value="D14 : AG">D14 : AG</option>
                                <option value="D15 : AH">D15 : AH</option>
                                <option value="D16 : AI">D16 : AI</option>
                                <option value="D17 : AJ">D17 : AJ</option>
                                <option value="C1 : O">C1 : O</option>
                                <option value="C2 : P">C2 : P</option>
                                <option value="C3 : R">C3 : R</option>
                                <option value="C7 : W">C7 : W</option>
                                <option value="F1 : V">F1 : V</option>
                                <option value="F2 : A">F2 : A</option>
                                <option value="F3 : U">F3 : U</option>
                            </select>
                            <small class="text-danger errorMesinRetail"></small>
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

            $('#batas').on('input', function() {
                let val = $(this).val();
                let cleaned = val.replace(/,/g, '.');
                cleaned = cleaned.replace(/[^0-9.]/g, '');
                let parts = cleaned.split('.');
                if (parts.length > 2) {
                    cleaned = parts[0] + '.' + parts.slice(1).join('');
                }
                if (val !== cleaned) {
                    $(this).val(cleaned);
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
                        data: 'nama_analis_field',
                        name: 'nama_analis_field'
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
                        data: 'mesin_press_test',
                        name: 'mesin_press_test'
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
                $('#mesin_press_test').val('').trigger('change');

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
                        $('#mesin_press_test').val(response.mesin_press_test).trigger('change');

                        $('#id').val(response.id);
                        $('#nama_analis_field').val(response.nama_analis_field);
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
                            if (errors.nama_analis_field) {
                                $('#nama_analis_field').addClass('is-invalid');
                                $('.errorNamaAnalisField').html(errors.nama_analis_field.join('<br>'));
                            }
                            if (errors.batas) {
                                $('#batas').addClass('is-invalid');
                                $('.errorBatas').html(errors.batas.join('<br>'));
                            }
                            if (errors.variant) {
                                $('#variant').addClass('is-invalid');
                                $('.errorVariant').html(errors.variant.join('<br>'));
                            }
                            if (errors.mesin_press_test) {
                                $('#mesin_press_test').addClass('is-invalid');
                                $('.errorMesinPressTest').html(errors.mesin_press_test.join(
                                    '<br>'));
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
