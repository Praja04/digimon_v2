@extends('layouts.component.main')
@section('title', 'Pelarutan 1')
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
                                                <h4>{{ $pelarutan_1->productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ auth()->user()->name }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Tanggal Produksi : <span
                                                            class="text-body fw-medium">{{ $pelarutan_1->productionBatch->date }}</span>
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
                                                            <h5 class="mb-0">{{ $pelarutan_1->productionBatch->variant }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                                            <h5 class="mb-0">{{ $pelarutan_1->productionBatch->batch_range }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-hashtag"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Batch :</p>
                                                            <h5 class="mb-0">{{ $pelarutan_1->batch_number }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-user-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Dissolver :</p>
                                                            <h5 class="mb-0">{{ $pelarutan_1->dissolver_number }}</h5>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-temp-hot-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Suhu Pelarutan (dari PRD) :</p>
                                                            <h5 class="mb-0">{{ !empty($pelarutan_1->suhu) ? $pelarutan_1->suhu . ' °C' : '-' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-time-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Waktu Input Suhu (dari PRD) :</p>
                                                            <h5 class="mb-0">{{ !empty($pelarutan_1->jam_mulai) ? \Carbon\Carbon::parse($pelarutan_1->jam_mulai)->format('d/m/Y H:i') : '-' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- end row -->

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{{ $pelarutan_1->productionBatch->description ?? '-' }}</p>
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
                                <form id="form">
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <input type="hidden" name="id" id="id" value="{{ $pelarutan_1->id }}">
                                            <label class="form-label">BRIX <span style="color: red">*</span></label>
                                            <input type="text" name="brix" id="brix"
                                                class="form-control comma-input" placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $pelarutan_1->brix ?? '') }}">

                                            <small class="text-danger errorBrix"></small>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">NACL <span style="color: red">*</span></label>
                                            <input type="text" name="nacl" id="nacl"
                                                class="form-control comma-input" placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $pelarutan_1->nacl ?? '') }}">
                                            <small class="text-danger errorNacl"></small>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Organo <span style="color: red">*</span></label>
                                            <input type="text" name="organo" id="organo" class="form-control"
                                                oninput="this.value = this.value.toUpperCase();"
                                                value="{{ $pelarutan_1->organo ?? '' }}">
                                            <small class="text-danger errorOrgano"></small>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Status <span style="color: red">*</span></label>
                                            <select name="status_disposition" id="status_disposition"
                                                class="form-control disposition-select">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="OK" {{ $pelarutan_1->status == 'OK' ? 'selected' : '' }}>OK
                                                </option>
                                                <option value="NOT OK" {{ $pelarutan_1->status == 'NOT OK' ? 'selected' : '' }}>
                                                    NOT OK
                                                </option>
                                                <option value="Adjustment"
                                                    {{ $pelarutan_1->status == 'Adjustment' ? 'selected' : '' }}>
                                                    Adjustment</option>
                                            </select>
                                            <small class="text-danger errorStatusDisposition"></small>
                                        </div>
                                        @if (auth()->user()->role == 'Foreman')
                                            <div class="col-lg-12">
                                                <label class="form-label">Disposisi</label>
                                                <select name="disposition" id="disposition"
                                                    class="form-control disposition-select">
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
                                            <label class="form-label">Catatan</label>
                                            <textarea name="disposition_remark" class="form-control" rows="2" placeholder="Isi catatan jika diperlukan..."
                                                oninput="this.value = this.value.toUpperCase();">{{ $pelarutan_1->disposition_remark ?? '' }}</textarea>
                                        </div>

                                        <div class="col-lg-12 d-none adjustment-qty-wrapper">
                                            <h6 class="form-label fw-bold">Adjustment Qty</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Gula Tebu (Kg)</label>
                                                    <input type="text" name="adjustment_qty_gula_tebu"
                                                        class="form-control adjustment-qty comma-input" value="0"
                                                        placeholder="Contoh: 0,00"
                                                        value="{{ $pelarutan_1->adjustment_qty_gula_tebu ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Gula Kelapa (Kg)</label>
                                                    <input type="text" name="adjustment_qty_gula_kelapa"
                                                        class="form-control adjustment-qty comma-input" value="0"
                                                        placeholder="Contoh: 0,00"
                                                        value="{{ $pelarutan_1->adjustment_qty_gula_kelapa ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

            // Initialize Select2
            $('.select2').select2();

            // ==========================================
            // Validate Comma Input (Prevent Dot)
            // ==========================================
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

            // ==========================================
            // Helper: Toggle Adjustment Fields
            // ==========================================
            function toggleAdjustmentFields(status, showOnly = false) {
                const qtyWrapper = $('.adjustment-qty-wrapper');
                const qtyInput = $('.adjustment-qty');

                if (status === 'Adjustment') {
                    qtyWrapper.removeClass('d-none');
                    qtyInput.prop('required', true);
                } else {
                    qtyWrapper.addClass('d-none');
                    qtyInput.prop('required', false);

                    // Clear values hanya jika bukan mode showOnly
                    if (!showOnly) {
                        qtyInput.val('');
                    }
                }
            }

            // ==========================================
            // Event: Status Disposition Change
            // ==========================================
            $('#status_disposition').on('change', function() {
                const selected = $(this).val();
                toggleAdjustmentFields(selected);
            });

            // ==========================================
            // Form Submit
            // ==========================================
            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('pelarutan-1.update') }}",
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
                                "{{ route('pelarutan-1.show', '') }}/" +
                                {{ $pelarutan_1->productionBatch->id }}
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
                            if (errors.organo) {
                                $('#organo').addClass('is-invalid');
                                $('.errorOrgano').html(errors.organo.join('<br>'));
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
                            if (errors.adjustment_qty_gula_tebu) {
                                $('input[name="adjustment_qty_gula_tebu"]').addClass(
                                    'is-invalid');
                            }
                            if (errors.adjustment_qty_gula_kelapa) {
                                $('input[name="adjustment_qty_gula_kelapa"]').addClass(
                                    'is-invalid');
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
