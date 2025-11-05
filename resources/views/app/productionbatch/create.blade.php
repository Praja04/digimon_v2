@extends('layouts.component.main')
@section('title', 'Tambah PO Masak')
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
                                <li class="breadcrumb-item"><a href="{{ route('productionbatch.index') }}">Persiapan
                                        Masak</a></li>
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
                        <div class="card-body">
                            <div>
                                <form id="form">
                                    <div class="mb-3">
                                        <label for="po_number" class="form-label">Nomor PO <span
                                                style="color: red">*</span></label>
                                        <input type="text" id="po_number" name="po_number" class="form-control">
                                        <small class="text-danger errorPONumber"></small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="variant" class="form-label">Varian <span
                                                style="color: red">*</span></label>
                                        <select id="variant" name="variant" class="select2 form-control">
                                            <option value="">-- Pilih Varian --</option>
                                            <option value="SS1">SS1</option>
                                            <option value="SS2">SS2</option>
                                            <option value="BB">BB</option>
                                            <option value="MSD NR1">MSD NR1</option>
                                            <option value="MSD NR2">MSD NR2</option>
                                            <option value="JB">JB</option>
                                        </select>
                                        <small class="text-danger errorVariant"></small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Tanggal Produksi <span
                                                style="color: red">*</span></label>
                                        <input type="date" name="date" id="date" class="form-control"
                                            value="{{ now()->format('Y-m-d') }}" />
                                        <small class="text-danger errorDate"></small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="batch_range" class="form-label">Rentang Batch Masak <span
                                                style="color: red">*</span></label>
                                        <input type="text" name="batch_range" class="form-control"
                                            placeholder="Contoh: 1-10" id="batch_range" />
                                        <small class="text-danger errorBatchRange"></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Keterangan</label>
                                        <input type="text" name="description" class="form-control" />
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('productionbatch.store') }}",
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.href =
                                "{{ route('productionbatch.index') }}";
                        });
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
        });
    </script>
@endsection
