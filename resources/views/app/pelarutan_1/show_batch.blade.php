@extends('layouts.component.main')

@section('title', 'Pelarutan 1')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- PAGE TITLE --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Menu</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @yield('title')
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    {{-- INFORMASI PELARUTAN --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">

                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>
                                                    {{ $pelarutan_1->productionBatch->po_number }}
                                                    (Nomor PO)
                                                </h4>

                                                <div class="hstack gap-3 flex-wrap">
                                                    <div>
                                                        <a href="#" class="text-primary d-block">
                                                            {{ auth()->user()->name }}
                                                        </a>
                                                    </div>

                                                    <div class="vr"></div>

                                                    <div class="text-muted">
                                                        Tanggal Produksi :
                                                        <span class="text-body fw-medium">
                                                            {{ $pelarutan_1->productionBatch->date }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">

                                            {{-- VARIANT --}}
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
                                                            <p class="text-muted mb-1">
                                                                Variant :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pelarutan_1->productionBatch->variant }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- BATCH RANGE --}}
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
                                                            <p class="text-muted mb-1">
                                                                Batch Range :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pelarutan_1->productionBatch->batch_range }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- BATCH --}}
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
                                                            <p class="text-muted mb-1">
                                                                Batch :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pelarutan_1->batch_number }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- DISSOLVER --}}
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
                                                            <p class="text-muted mb-1">
                                                                Dissolver :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pelarutan_1->dissolver_number }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SUHU --}}
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">

                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-temp-hot-line"></i>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">
                                                                Suhu Pelarutan (dari PRD) :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ !empty($pelarutan_1->suhu)
                                                                    ? $pelarutan_1->suhu . ' °C'
                                                                    : '-' }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- WAKTU INPUT SUHU --}}
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">

                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-time-line"></i>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">
                                                                Waktu Input Suhu (dari PRD) :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ !empty($pelarutan_1->jam_mulai)
                                                                    ? \Carbon\Carbon::parse($pelarutan_1->jam_mulai)->format('d/m/Y H:i')
                                                                    : '-' }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- DESCRIPTION --}}
                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">
                                                Description :
                                            </h5>

                                            <p>
                                                {{ $pelarutan_1->productionBatch->description ?? '-' }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FORM ANALISA --}}
                    <div class="card">
                        <div class="card-body">

                            {{-- ALERT DRAFT --}}
                            @if ($draft)
                                <div class="alert alert-warning border-warning mb-4">
                                    <div class="d-flex align-items-center">

                                        <i class="ri-draft-line fs-4 me-2"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Data sementara ditemukan
                                            </div>

                                            <small>
                                                Data terakhir disimpan sementara
                                                {{ $draft->updated_at
                                                    ? $draft->updated_at->format('d/m/Y H:i:s')
                                                    : '' }}.
                                                Lengkapi seluruh field wajib untuk
                                                mengaktifkan Simpan Final.
                                            </small>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            <form id="form">
                                @csrf

                                <div class="row g-3">

                                    {{-- ID --}}
                                    <input
                                        type="hidden"
                                        name="id"
                                        id="id"
                                        value="{{ $pelarutan_1->id }}"
                                    >

                                    {{-- BRIX --}}
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            BRIX
                                            <span style="color:red">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="brix"
                                            id="brix"
                                            class="form-control comma-input required-final"
                                            placeholder="Contoh: 0,00"
                                            value="{{ str_replace(
                                                '.',
                                                ',',
                                                $draft->brix ?? $pelarutan_1->brix ?? ''
                                            ) }}"
                                        >

                                        <small class="text-danger errorBrix"></small>
                                    </div>

                                    {{-- NACL --}}
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            NACL
                                            <span style="color:red">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="nacl"
                                            id="nacl"
                                            class="form-control comma-input required-final"
                                            placeholder="Contoh: 0,00"
                                            value="{{ str_replace(
                                                '.',
                                                ',',
                                                $draft->nacl ?? $pelarutan_1->nacl ?? ''
                                            ) }}"
                                        >

                                        <small class="text-danger errorNacl"></small>
                                    </div>

                                    {{-- ORGANO --}}
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            Organo
                                            <span style="color:red">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="organo"
                                            id="organo"
                                            class="form-control required-final"
                                            oninput="this.value = this.value.toUpperCase();"
                                            value="{{ $draft->organo ?? $pelarutan_1->organo ?? '' }}"
                                        >

                                        <small class="text-danger errorOrgano"></small>
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="col-lg-6">

                                        <label class="form-label">
                                            Status
                                            <span style="color:red">*</span>
                                        </label>

                                        @php
                                            $currentStatus =
                                                $draft->status_disposition
                                                ?? $pelarutan_1->status
                                                ?? '';
                                        @endphp

                                        <select
                                            name="status_disposition"
                                            id="status_disposition"
                                            class="form-control disposition-select required-final"
                                        >
                                            <option value="">
                                                -- Pilih Status --
                                            </option>

                                            <option
                                                value="OK"
                                                {{ $currentStatus === 'OK' ? 'selected' : '' }}
                                            >
                                                OK
                                            </option>

                                            <option
                                                value="NOT OK"
                                                {{ $currentStatus === 'NOT OK' ? 'selected' : '' }}
                                            >
                                                NOT OK
                                            </option>

                                            <option
                                                value="Adjustment"
                                                {{ $currentStatus === 'Adjustment' ? 'selected' : '' }}
                                            >
                                                Adjustment
                                            </option>
                                        </select>

                                        <small class="text-danger errorStatusDisposition"></small>
                                    </div>

                                    {{-- DISPOSISI FOREMAN --}}
                                    @if (auth()->user()->role == 'Foreman')
                                        <div class="col-lg-12">

                                            <label class="form-label">
                                                Disposisi
                                            </label>

                                            <select
                                                name="disposition"
                                                id="disposition"
                                                class="form-control disposition-select"
                                            >
                                                <option value="">
                                                    -- Pilih Disposisi --
                                                </option>

                                                <option
                                                    value="Release"
                                                    {{ $pelarutan_1->disposition === 'Release' ? 'selected' : '' }}
                                                >
                                                    Release
                                                </option>

                                                <option
                                                    value="Release Bersyarat"
                                                    {{ $pelarutan_1->disposition === 'Release Bersyarat' ? 'selected' : '' }}
                                                >
                                                    Release Bersyarat
                                                </option>

                                                <option
                                                    value="Resampling"
                                                    {{ $pelarutan_1->disposition === 'Resampling' ? 'selected' : '' }}
                                                >
                                                    Resampling
                                                </option>

                                                <option
                                                    value="Reject"
                                                    {{ $pelarutan_1->disposition === 'Reject' ? 'selected' : '' }}
                                                >
                                                    Reject
                                                </option>

                                                <option
                                                    value="Repro"
                                                    {{ $pelarutan_1->disposition === 'Repro' ? 'selected' : '' }}
                                                >
                                                    Repro
                                                </option>

                                                <option
                                                    value="Adjustment"
                                                    {{ $pelarutan_1->disposition === 'Adjustment' ? 'selected' : '' }}
                                                >
                                                    Adjustment
                                                </option>

                                            </select>

                                            <small class="text-danger errorDisposition"></small>
                                        </div>
                                    @endif

                                    {{-- CATATAN --}}
                                    <div class="col-lg-12">

                                        <label class="form-label">
                                            Catatan
                                        </label>

                                        <textarea
                                            name="disposition_remark"
                                            id="disposition_remark"
                                            class="form-control"
                                            rows="2"
                                            placeholder="Isi catatan jika diperlukan..."
                                            oninput="this.value = this.value.toUpperCase();"
                                        >{{ $draft->disposition_remark ?? $pelarutan_1->disposition_remark ?? '' }}</textarea>

                                    </div>

                                    {{-- ADJUSTMENT --}}
                                    @php
                                        $adjustmentTebu =
                                            $draft->adjustment_qty_gula_tebu
                                            ?? $pelarutan_1->adjustment_qty_gula_tebu
                                            ?? '';

                                        $adjustmentKelapa =
                                            $draft->adjustment_qty_gula_kelapa
                                            ?? $pelarutan_1->adjustment_qty_gula_kelapa
                                            ?? '';
                                    @endphp

                                    <div
                                        class="col-lg-12 adjustment-qty-wrapper
                                        {{ $currentStatus === 'Adjustment' ? '' : 'd-none' }}"
                                    >

                                        <h6 class="form-label fw-bold">
                                            Adjustment Qty
                                        </h6>

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Gula Tebu (Kg)
                                                </label>

                                                <input
                                                    type="text"
                                                    name="adjustment_qty_gula_tebu"
                                                    class="form-control adjustment-qty comma-input"
                                                    placeholder="Contoh: 0,00"
                                                    value="{{ $adjustmentTebu !== ''
                                                        ? str_replace('.', ',', $adjustmentTebu)
                                                        : '0' }}"
                                                >

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Gula Kelapa (Kg)
                                                </label>

                                                <input
                                                    type="text"
                                                    name="adjustment_qty_gula_kelapa"
                                                    class="form-control adjustment-qty comma-input"
                                                    placeholder="Contoh: 0,00"
                                                    value="{{ $adjustmentKelapa !== ''
                                                        ? str_replace('.', ',', $adjustmentKelapa)
                                                        : '0' }}"
                                                >

                                            </div>

                                        </div>
                                    </div>

                                    {{-- BUTTON --}}
                                    <div class="col-lg-12">

                                        <hr class="my-2">

                                        <div
                                            class="d-flex justify-content-end align-items-center gap-2 flex-wrap"
                                        >

                                            @if (auth()->user()->role === 'Analis Kimia')
                                                <button
                                                    type="button"
                                                    class="btn btn-warning"
                                                    id="saveDraft"
                                                >
                                                    <i class="ri-save-3-line me-1"></i>
                                                    Simpan Sementara
                                                </button>
                                            @endif

                                            <button
                                                type="submit"
                                                class="btn btn-primary"
                                                id="saveFinal"
                                                disabled
                                            >
                                                <i class="ri-checkbox-circle-line me-1"></i>
                                                Simpan Final
                                            </button>

                                        </div>

                                        <div
                                            id="finalHelper"
                                            class="text-end text-muted small mt-2"
                                        >
                                            Lengkapi BRIX, NACL, Organo, dan Status
                                            untuk mengaktifkan Simpan Final.
                                        </div>

                                    </div>

                                </div>
                            </form>

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

            /*
            |--------------------------------------------------------------------------
            | AJAX CSRF
            |--------------------------------------------------------------------------
            */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */
            $('.select2').select2();


            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT DESIMAL
            |--------------------------------------------------------------------------
            */
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

                    updateFinalButtonState();
                });
            });


            /*
            |--------------------------------------------------------------------------
            | TOGGLE ADJUSTMENT FIELD
            |--------------------------------------------------------------------------
            */
            function toggleAdjustmentFields(status, clearValue = false) {

                const qtyWrapper = $('.adjustment-qty-wrapper');
                const qtyInput = $('.adjustment-qty');

                if (status === 'Adjustment') {

                    qtyWrapper.removeClass('d-none');

                } else {

                    qtyWrapper.addClass('d-none');

                    if (clearValue) {
                        qtyInput.val('');
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CEK FIELD WAJIB
            |--------------------------------------------------------------------------
            */
            function isFinalFormComplete() {

                const brix = $.trim($('#brix').val());
                const nacl = $.trim($('#nacl').val());
                const organo = $.trim($('#organo').val());
                const status = $.trim($('#status_disposition').val());

                return (
                    brix !== '' &&
                    nacl !== '' &&
                    organo !== '' &&
                    status !== ''
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STATUS TOMBOL FINAL
            |--------------------------------------------------------------------------
            */
            function updateFinalButtonState() {

                const complete = isFinalFormComplete();

                $('#saveFinal').prop('disabled', !complete);

                if (complete) {

                    $('#finalHelper')
                        .removeClass('text-muted text-danger')
                        .addClass('text-success')
                        .html(
                            '<i class="ri-checkbox-circle-line me-1"></i>' +
                            'Semua field wajib sudah terisi. Data siap disimpan final.'
                        );

                } else {

                    $('#finalHelper')
                        .removeClass('text-success text-danger')
                        .addClass('text-muted')
                        .html(
                            'Lengkapi BRIX, NACL, Organo, dan Status ' +
                            'untuk mengaktifkan Simpan Final.'
                        );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT FIELD WAJIB
            |--------------------------------------------------------------------------
            */
            $('#brix, #nacl, #organo, #status_disposition')
                .on('input change keyup', function() {

                    updateFinalButtonState();

                });


            /*
            |--------------------------------------------------------------------------
            | STATUS BERUBAH
            |--------------------------------------------------------------------------
            */
            $('#status_disposition').on('change', function() {

                const selected = $(this).val();

                toggleAdjustmentFields(selected, true);

                updateFinalButtonState();
            });


            /*
            |--------------------------------------------------------------------------
            | KONDISI AWAL HALAMAN
            |--------------------------------------------------------------------------
            */
            toggleAdjustmentFields(
                $('#status_disposition').val(),
                false
            );

            updateFinalButtonState();


            /*
            |--------------------------------------------------------------------------
            | CLEAR VALIDATION ERROR
            |--------------------------------------------------------------------------
            */
            function clearErrors() {

                $('.form-control').removeClass('is-invalid');

                $('.errorBrix').html('');
                $('.errorNacl').html('');
                $('.errorOrgano').html('');
                $('.errorStatusDisposition').html('');
                $('.errorDisposition').html('');
            }


            /*
            |--------------------------------------------------------------------------
            | SHOW VALIDATION ERROR
            |--------------------------------------------------------------------------
            */
            function showValidationErrors(errors) {

                if (errors.brix) {

                    $('#brix').addClass('is-invalid');

                    $('.errorBrix').html(
                        errors.brix.join('<br>')
                    );
                }

                if (errors.nacl) {

                    $('#nacl').addClass('is-invalid');

                    $('.errorNacl').html(
                        errors.nacl.join('<br>')
                    );
                }

                if (errors.organo) {

                    $('#organo').addClass('is-invalid');

                    $('.errorOrgano').html(
                        errors.organo.join('<br>')
                    );
                }

                if (errors.status_disposition) {

                    $('#status_disposition')
                        .addClass('is-invalid');

                    $('.errorStatusDisposition').html(
                        errors.status_disposition.join('<br>')
                    );
                }

                if (errors.disposition) {

                    $('#disposition')
                        .addClass('is-invalid');

                    $('.errorDisposition').html(
                        errors.disposition.join('<br>')
                    );
                }

                if (errors.adjustment_qty_gula_tebu) {

                    $('input[name="adjustment_qty_gula_tebu"]')
                        .addClass('is-invalid');
                }

                if (errors.adjustment_qty_gula_kelapa) {

                    $('input[name="adjustment_qty_gula_kelapa"]')
                        .addClass('is-invalid');
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN SEMENTARA
            |--------------------------------------------------------------------------
            */
            $('#saveDraft').on('click', function() {

                const button = $(this);

                clearErrors();

                $('#saveFinal').prop('disabled', true);

                $.ajax({

                    data: $('#form').serialize(),

                    url: "{{ route('pelarutan-1.draft.store') }}",

                    type: "POST",

                    dataType: "json",

                    beforeSend: function() {

                        button
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-2"></i>' +
                                'Menyimpan...'
                            );
                    },

                    complete: function() {

                        button
                            .prop('disabled', false)
                            .html(
                                '<i class="ri-save-3-line me-1"></i>' +
                                'Simpan Sementara'
                            );

                        updateFinalButtonState();
                    },

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan Sementara',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });

                    },

                    error: function(xhr) {

                        const response = xhr.responseJSON || {};

                        if (
                            xhr.status === 422 &&
                            response.errors
                        ) {

                            showValidationErrors(
                                response.errors
                            );

                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Belum Valid',
                                text: response.message ||
                                    'Periksa kembali data yang diisi.'
                            });

                            return;
                        }

                        if (
                            xhr.status === 403 ||
                            xhr.status === 409
                        ) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak Dapat Disimpan',
                                text: response.message ||
                                    'Data tidak dapat disimpan sementara.'
                            });

                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: response.message ||
                                'Terjadi kesalahan saat menyimpan sementara.'
                        });
                    }
                });
            });


            /*
            |--------------------------------------------------------------------------
            | SIMPAN FINAL
            |--------------------------------------------------------------------------
            */
            $('#form').on('submit', function(e) {

                e.preventDefault();

                if (!isFinalFormComplete()) {

                    updateFinalButtonState();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'BRIX, NACL, Organo, dan Status wajib diisi sebelum Simpan Final.'
                    });

                    return;
                }

                const button = $('#saveFinal');

                clearErrors();

                $.ajax({

                    data: $(this).serialize(),

                    url: "{{ route('pelarutan-1.update') }}",

                    type: "POST",

                    dataType: "json",

                    beforeSend: function() {

                        button
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-2"></i>' +
                                'Menyimpan Final...'
                            );

                        $('#saveDraft').prop(
                            'disabled',
                            true
                        );
                    },

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {

                            window.location.href =
                                "{{ route('pelarutan-1.show', '') }}/" +
                                "{{ $pelarutan_1->productionBatch->id }}";
                        });
                    },

                    error: function(xhr) {

                        const response =
                            xhr.responseJSON || {};

                        if (
                            xhr.status === 409 &&
                            response.message
                        ) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Disimpan',
                                text: response.message,
                            });

                            return;
                        }

                        if (
                            xhr.status === 403 &&
                            response.message
                        ) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Akses Ditolak',
                                text: response.message,
                            });

                            return;
                        }

                        if (
                            xhr.status === 419
                        ) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Session Berakhir',
                                text: 'Session atau CSRF token sudah tidak valid. Silakan refresh halaman lalu coba kembali.',
                            });

                            return;
                        }

                        if (
                            xhr.status === 422 &&
                            response.errors
                        ) {

                            showValidationErrors(
                                response.errors
                            );

                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text:
                                response.error ||
                                response.message ||
                                'Terjadi kesalahan, silakan coba lagi.',
                        });
                    },

                    complete: function() {

                        button.html(
                            '<i class="ri-checkbox-circle-line me-1"></i>' +
                            'Simpan Final'
                        );

                        $('#saveDraft').prop(
                            'disabled',
                            false
                        );

                        updateFinalButtonState();
                    }
                });
            });

        });
    </script>
@endsection