@extends('layouts.component.main')
@section('title', 'GGA')
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
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ auth()->user()->name }}</a>
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
                                            <!-- end col -->
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
                            <div class="card-body">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0 text-center">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th>No Batch</th>
                                                <th>Dissolver</th>
                                                <th>BRIX</th>
                                                <th>NACL</th>
                                                <th>Warna</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Status</th>
                                                <th>Disposisi</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @forelse ($productionBatch->gga as $gga)
                                                @php
                                                    // Tentukan class berdasarkan disposition
                                                    $dispositionUpper = strtoupper($gga->status ?? '');
                                                    $rowClass = match ($dispositionUpper) {
                                                        'NOT OK' => 'table-danger',
                                                        'ADJUSTMENT' => 'table-warning',
                                                        default => '',
                                                    };

                                                    $badgeClass = match ($dispositionUpper) {
                                                        'NOT OK' => 'bg-danger',
                                                        'ADJUSTMENT' => 'bg-warning text-dark',
                                                        'OK' => 'bg-success',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <tr class="{{ $rowClass }}">
                                                    <td>
                                                        {{ $gga->batch_number }}
                                                    </td>
                                                    <td>{{ $gga->dissolver_number }}</td>
                                                    <td>{{ $gga->brix ?? '-' }}</td>
                                                    <td>{{ $gga->nacl ?? '-' }}</td>
                                                    <td>{{ $gga->color->name ?? '-' }} <small
                                                            class="text-muted">({{ $gga->color->code ?? '-' }})</small>
                                                    </td>
                                                    <td>{{ $gga->user->name ?? '-' }}</td>
                                                    <td>{{ $gga->status ?? '-' }}</td>
                                                    <td>{{ $gga->disposition ?? '-' }}</td>

                                                    <td>
                                                        <button class="btn btn-sm btn-info" id="btnDetail"
                                                            data-id="{{ $gga->id }}">
                                                            <i class="ri-eye-line"></i> Lihat
                                                        </button>
                                                    </td>
                                                    <td>
                                                        @if (is_null($gga->status))
                                                            <button class="btn btn-sm btn-primary open-gga-modal"
                                                                data-id="{{ $gga->id }}">
                                                                Input GGA
                                                            </button>
                                                        @else
                                                            @if (auth()->user()->role == 'Foreman')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning open-gga-modal-edit"
                                                                    data-id="{{ $gga->id }}">
                                                                    Edit GGA
                                                                </button>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line"></i> Lengkap
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">
                                                        <i class="ri-inbox-line fs-1 d-block mb-2 opacity-50"></i>
                                                        Tidak ada data tersedia.
                                                    </td>
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

    <!-- Modal input GGA -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Data GGA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="alert alert-danger d-none error-alert"></div>
                        <div class="col-lg-12">
                            <input type="hidden" name="id" id="id">
                            <label class="form-label">BRIX <span style="color: red">*</span></label>
                            <input type="text" name="brix" id="brix" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBrix"></small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">NACL <span style="color: red">*</span></label>
                            <input type="text" name="nacl" id="nacl" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorNacl"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Warna <span style="color: red">*</span></label>
                            <select name="color" id="color" class="select2 form-control">
                                <option value="">-- Pilih Warna --</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}">
                                        {{ $color->name . ' - ' . $color->code }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger errorColor"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Status <span style="color: red">*</span></label>
                            <select name="status_disposition" id="status_disposition"
                                class="form-control disposition-select">
                                <option value="">-- Pilih Status --</option>
                                <option value="OK">OK</option>
                                <option value="NOT OK">NOT OK</option>
                                <option value="Adjustment">Adjustment</option>
                            </select>
                            <small class="text-danger errorStatusDisposition"></small>
                        </div>
                        @if (auth()->user()->role == 'Foreman')
                            <div class="col-lg-12">
                                <label class="form-label">Disposisi</label>
                                <select name="disposition" id="disposition" class="form-control disposition-select">
                                    <option value="">-- Pilih Disposisi --</option>
                                    <option value="Release">Release</option>
                                    <option value="Release Bersyarat">Release Bersyarat</option>
                                    <option value="Resampling">Resampling</option>
                                    <option value="Reject">Reject</option>
                                    <option value="Repro">Repro</option>
                                </select>
                                <small class="text-danger errorDisposition"></small>
                            </div>
                        @endif
                        <div class="col-lg-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="disposition_remark" id="disposition_remark" class="form-control" rows="2"
                                placeholder="Isi remarks jika diperlukan..." oninput="this.value = this.value.toUpperCase();"></textarea>
                        </div>

                        <div class="col-lg-12 d-none adjustment-qty-wrapper">
                            <h6 class="form-label fw-bold">Adjustment Qty</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Air (Liter)</label>
                                    <input type="text" name="adjustment_qty_air"
                                        class="form-control adjustment-qty comma-input" placeholder="Contoh: 0,00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gula (Kg)</label>
                                    <input type="text" name="adjustment_qty_gula"
                                        class="form-control adjustment-qty comma-input" placeholder="Contoh: 0,00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Garam (Kg)</label>
                                    <input type="text" name="adjustment_qty_garam"
                                        class="form-control adjustment-qty comma-input" placeholder="Contoh: 0,00">
                                </div>
                            </div>
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

    <!-- Modal Detail Keterangan GGA -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail GGA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-lg-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="disposition_remark_detail" id="disposition_remark_detail" class="form-control" rows="2"
                            disabled></textarea>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                dropdownParent: $('#modal')
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

            function toggleAdjustmentFields(status, showOnly = false) {
                const qtyWrapper = $('.adjustment-qty-wrapper');
                const qtyInput = $('.adjustment-qty');

                if (status === 'Adjustment') {
                    qtyWrapper.removeClass('d-none');
                    qtyInput.prop('required', true);
                } else {
                    qtyWrapper.addClass('d-none');
                    qtyInput.prop('required', false);

                    if (!showOnly) {
                        qtyInput.val('');
                    }
                }
            }

            $('#status_disposition').on('change', function() {
                const selected = $(this).val();
                toggleAdjustmentFields(selected);
            });

            $('.open-gga-modal').on('click', function() {
                const id = $(this).data('id');

                $('#form')[0].reset();
                $('.text-danger').html('');
                $('.form-control').removeClass('is-invalid');
                $('.modal-title').text('Input Data GGA');
                $('#id').val(id);

                $('#color').val('').trigger('change');
                $('#status_disposition').val('').trigger('change');
                $('#disposition').val('').trigger('change');

                $('.adjustment-qty-wrapper').addClass('d-none');
                $('.adjustment-qty').prop('required', false).val('');

                $('#modal').modal('show');
            });

            $('body').on('click', '#btnDetail', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('gga.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('#form')[0].reset();
                        $('.text-danger').html('');
                        $('.form-control').removeClass('is-invalid');
                    },
                    success: function(response) {
                        let remarkText = '';

                        if (response.disposition_remark != null &&
                            response.disposition_remark != '-' &&
                            response.disposition != 'Adjustment') {
                            remarkText = response.disposition_remark;
                        } else if (response.disposition == 'Adjustment') {
                            remarkText =
                                `Adjustment Air: ${response.adjustment_qty_air || 0} Liter, Garam: ${response.adjustment_qty_garam || 0} Kg, Gula: ${response.adjustment_qty_gula || 0} Kg`;
                        } else if (response.is_adjustment == true) {
                            remarkText = 'Adjustment';
                        } else {
                            remarkText = '-';
                        }

                        $('#disposition_remark_detail').val(remarkText);

                        $('#detailModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memuat data. Silakan coba lagi.',
                        });
                    }
                });
            });

            $('body').on('click', '.open-gga-modal-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('gga.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('#form')[0].reset();
                        $('.text-danger').html('');
                        $('.form-control').removeClass('is-invalid');
                    },
                    success: function(response) {
                        $('.modal-title').text('Edit Data GGA');

                        $('#id').val(response.id);
                        $('#brix').val(response.brix);
                        $('#nacl').val(response.nacl);
                        $('#color').val(response.color_id).trigger('change');
                        $('#disposition_remark').val(response.disposition_remark || '');

                        $('#status_disposition').val(response.status);
                        $('#disposition').val(response.disposition || '');

                        if (response.status === 'Adjustment') {
                            $('.adjustment-qty-wrapper').removeClass('d-none');
                            $('input[name="adjustment_qty_air"]').val(response
                                .adjustment_qty_air || '');
                            $('input[name="adjustment_qty_gula"]').val(response
                                .adjustment_qty_gula || '');
                            $('input[name="adjustment_qty_garam"]').val(response
                                .adjustment_qty_garam || '');

                            $('.adjustment-qty').prop('required', true);
                        } else {
                            $('.adjustment-qty-wrapper').addClass('d-none');
                            $('.adjustment-qty').prop('required', false).val('');
                        }

                        $('#modal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memuat data. Silakan coba lagi.',
                        });
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('gga.update') }}",
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

                        if (xhr.status === 403 && response && response.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Akses Ditolak',
                                text: response.message,
                            });
                            return;
                        }

                        if (xhr.status === 422 && response && response.errors) {
                            let errors = response.errors;

                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.nacl) {
                                $('#nacl').addClass('is-invalid');
                                $('.errorNacl').html(errors.nacl.join('<br>'));
                            }
                            if (errors.color) {
                                $('#color').addClass('is-invalid');
                                $('.errorColor').html(errors.color.join('<br>'));
                            }
                            if (errors.status_disposition) {
                                $('#status_disposition').addClass('is-invalid');
                                $('.errorStatusDisposition').html(errors.status_disposition
                                    .join('<br>'));
                            }
                            if (errors.disposition) {
                                $('#disposition').addClass('is-invalid');
                                $('.errorDisposition').html(errors.disposition.join('<br>'));
                            }
                            if (errors.adjustment_qty_air) {
                                $('input[name="adjustment_qty_air"]').addClass('is-invalid');
                            }
                            if (errors.adjustment_qty_gula) {
                                $('input[name="adjustment_qty_gula"]').addClass('is-invalid');
                            }
                            if (errors.adjustment_qty_garam) {
                                $('input[name="adjustment_qty_garam"]').addClass('is-invalid');
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
