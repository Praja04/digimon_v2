@extends('layouts.component.main')

@section('title', 'Analisa Pasteurisasi')

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

                                <li class="breadcrumb-item">
                                    <a href="{{ route('analisa.monitoring-pasteurisasi.index') }}">
                                        Analisa Monitoring Pasteurisasi
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    @yield('title')
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMASI BATCH --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row gx-lg-5">
                                <div class="col-xl-12">

                                    <div class="mt-xl-0 mt-5">

                                        <div class="d-flex">
                                            <div class="flex-grow-1">

                                                <h4>
                                                    {{ $pasteurisasi->productionBatch->po_number }}
                                                    (Nomor PO)
                                                </h4>

                                                <div class="hstack gap-3 flex-wrap">

                                                    <div>
                                                        <a href="#"
                                                            class="text-primary d-block">
                                                            {{ Session::get('username') }}
                                                        </a>
                                                    </div>

                                                    <div class="vr"></div>

                                                    <div class="text-muted">
                                                        Tanggal Produksi :
                                                        <span class="text-body fw-medium">
                                                            {{ $pasteurisasi->productionBatch->date }}
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
                                                                {{ $pasteurisasi->productionBatch->variant }}
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
                                                                {{ $pasteurisasi->productionBatch->batch_range }}
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
                                                                <i class="ri-list-ordered"></i>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">
                                                                Batch :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pasteurisasi->batch_range }}

                                                                @if ($pasteurisasi->additionalBatches)
                                                                    @foreach ($pasteurisasi->additionalBatches as $relasi)
                                                                        -{{ $relasi->batch }}
                                                                    @endforeach
                                                                @endif
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- NOMOR BLENDING --}}
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
                                                                Nomor Blending :
                                                            </p>

                                                            <h5 class="mb-0">
                                                                {{ $pasteurisasi->nomor_blending }}
                                                            </h5>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">
                                                Description :
                                            </h5>

                                            <p>
                                                {{ $pasteurisasi->productionBatch->description ?? '-' }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- FORM ANALISA --}}
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                {{-- INFORMASI DRAFT --}}
                                @if ($draft)
                                    <div class="alert alert-warning d-flex align-items-center mb-4"
                                        role="alert">
                                        <i class="mdi mdi-content-save-clock-outline fs-20 me-2"></i>

                                        <div>
                                            Data sementara ditemukan.
                                            Silakan lanjutkan pengisian lalu pilih
                                            <strong>Simpan Sementara</strong>
                                            atau
                                            <strong>Simpan Final</strong>.
                                        </div>
                                    </div>
                                @endif

                                <form id="form">

                                    <div class="row g-3">

                                        {{-- ID --}}
                                        <input type="hidden"
                                            name="id"
                                            id="id"
                                            value="{{ $pasteurisasi->id }}">

                                        {{-- BRIX --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                BRIX
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="brix"
                                                id="brix"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->brix ?? $pasteurisasi->brix ?? '') }}">

                                            <small class="text-danger errorBrix"></small>
                                        </div>

                                        {{-- VISCO --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Visco
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="visco"
                                                id="visco"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->visco ?? $pasteurisasi->visco ?? '') }}">

                                            <small class="text-danger errorVisco"></small>
                                        </div>

                                        {{-- NACL --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                NACL
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="nacl"
                                                id="nacl"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->nacl ?? $pasteurisasi->nacl ?? '') }}">

                                            <small class="text-danger errorNacl"></small>
                                        </div>

                                        {{-- BJ --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Bj
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="bj"
                                                id="bj"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->bj ?? $pasteurisasi->bj ?? '') }}">

                                            <small class="text-danger errorBj"></small>
                                        </div>

                                        {{-- PH --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                pH
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="ph"
                                                id="ph"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->ph ?? $pasteurisasi->ph ?? '') }}">

                                            <small class="text-danger errorPh"></small>
                                        </div>

                                        {{-- AW --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Aw
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="aw"
                                                id="aw"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->aw ?? $pasteurisasi->aw ?? '') }}">

                                            <small class="text-danger errorAw"></small>
                                        </div>

                                        {{-- ORGANO --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Organo
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="organo"
                                                id="organo"
                                                class="form-control"
                                                oninput="this.value = this.value.toUpperCase();"
                                                value="{{ $draft?->organo ?? $pasteurisasi->organo ?? '' }}">

                                            <small class="text-danger errorOrgano"></small>
                                        </div>

                                        {{-- BUIH --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Buih
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="buih"
                                                id="buih"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->buih ?? $pasteurisasi->buih ?? '') }}">

                                            <small class="text-danger errorBuih"></small>
                                        </div>

                                        {{-- AROMA --}}
                                        <div class="col-lg-4">
                                            <label class="form-label">
                                                Aroma
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="aroma"
                                                id="aroma"
                                                class="form-control"
                                                oninput="this.value = this.value.toUpperCase();"
                                                value="{{ $draft?->aroma ?? $pasteurisasi->aroma ?? '' }}">

                                            <small class="text-danger errorAroma"></small>
                                        </div>

                                        {{-- ENDAPAN --}}
                                        <div class="col-lg-6">
                                            <label class="form-label">
                                                Endapan
                                                <span style="color: red">*</span>
                                            </label>

                                            <input type="text"
                                                name="endapan"
                                                id="endapan"
                                                class="form-control comma-input"
                                                placeholder="Contoh: 0,00"
                                                value="{{ str_replace('.', ',', $draft?->endapan ?? $pasteurisasi->endapan ?? '') }}">

                                            <small class="text-danger errorEndapan"></small>
                                        </div>

                                        {{-- STATUS --}}
                                        <div class="col-lg-6">
                                            <label class="form-label">
                                                Status
                                                <span style="color: red">*</span>
                                            </label>

                                            <select name="status_disposition"
                                                id="status_disposition"
                                                class="form-control disposition-select">

                                                <option value="">
                                                    -- Pilih Status --
                                                </option>

                                                <option value="OK"
                                                    {{ ($draft?->status_disposition ?? $pasteurisasi->status) == 'OK' ? 'selected' : '' }}>
                                                    OK
                                                </option>

                                                <option value="NOT OK"
                                                    {{ ($draft?->status_disposition ?? $pasteurisasi->status) == 'NOT OK' ? 'selected' : '' }}>
                                                    NOT OK
                                                </option>

                                                <option value="Adjustment"
                                                    {{ ($draft?->status_disposition ?? $pasteurisasi->status) == 'Adjustment' ? 'selected' : '' }}>
                                                    Adjustment
                                                </option>

                                            </select>

                                            <small class="text-danger errorStatusDisposition"></small>
                                        </div>

                                        {{-- DISPOSITION FOREMAN --}}
                                        @if (auth()->user()->role == 'Foreman')
                                            <div class="col-lg-12">

                                                <label class="form-label">
                                                    Disposition
                                                </label>

                                                <select name="disposition"
                                                    id="disposition"
                                                    class="form-control disposition-select">

                                                    <option value="">
                                                        -- Pilih Disposition --
                                                    </option>

                                                    <option value="Release"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Release' ? 'selected' : '' }}>
                                                        Release
                                                    </option>

                                                    <option value="Release Bersyarat"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Release Bersyarat' ? 'selected' : '' }}>
                                                        Release Bersyarat
                                                    </option>

                                                    <option value="Resampling"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Resampling' ? 'selected' : '' }}>
                                                        Resampling
                                                    </option>

                                                    <option value="Reject"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Reject' ? 'selected' : '' }}>
                                                        Reject
                                                    </option>

                                                    <option value="Repro"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Repro' ? 'selected' : '' }}>
                                                        Repro
                                                    </option>

                                                    <option value="Jalan Bareng"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Jalan Bareng' ? 'selected' : '' }}>
                                                        Jalan Bareng
                                                    </option>

                                                    <option value="Leveling"
                                                        {{ ($draft?->disposition ?? $pasteurisasi->disposition) == 'Leveling' ? 'selected' : '' }}>
                                                        Leveling
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

                                            <textarea name="disposition_remark"
                                                id="disposition_remark"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Isi catatan jika diperlukan..."
                                                oninput="this.value = this.value.toUpperCase();">{{ $draft?->disposition_remark ?? $pasteurisasi->disposition_remark ?? '' }}</textarea>

                                        </div>

                                        {{-- ADJUSTMENT --}}
                                        <div class="mb-3 d-none adjustment-qty-wrapper">

                                            <h6 class="form-label fw-bold">
                                                Adjustment Qty
                                            </h6>

                                            <div class="row g-3">

                                                {{-- AIR --}}
                                                <div class="col-lg-4">

                                                    <label class="form-label">
                                                        Air (Liter)
                                                    </label>

                                                    <input type="text"
                                                        name="adjustment_qty_air"
                                                        class="form-control adjustment-qty comma-input"
                                                        placeholder="0,00"
                                                        value="{{ str_replace('.', ',', $draft?->adjustment_qty_air ?? $pasteurisasi->adjustment_qty_air ?? '') }}">

                                                </div>

                                                {{-- GULA --}}
                                                <div class="col-lg-4">

                                                    <label class="form-label">
                                                        Gula (Kg)
                                                    </label>

                                                    <input type="text"
                                                        name="adjustment_qty_gula"
                                                        class="form-control adjustment-qty comma-input"
                                                        placeholder="0,00"
                                                        value="{{ str_replace('.', ',', $draft?->adjustment_qty_gula ?? $pasteurisasi->adjustment_qty_gula ?? '') }}">

                                                </div>

                                                {{-- GARAM --}}
                                                <div class="col-lg-4">

                                                    <label class="form-label">
                                                        Garam (Kg)
                                                    </label>

                                                    <input type="text"
                                                        name="adjustment_qty_garam"
                                                        class="form-control adjustment-qty comma-input"
                                                        placeholder="0,00"
                                                        value="{{ str_replace('.', ',', $draft?->adjustment_qty_garam ?? $pasteurisasi->adjustment_qty_garam ?? '') }}">

                                                </div>

                                            </div>
                                        </div>

                                        {{-- BUTTON --}}
                                        <div class="d-flex justify-content-end gap-2 mt-3">

                                            <button type="button"
                                                class="btn btn-warning"
                                                id="saveDraft">

                                                <i class="mdi mdi-content-save-outline me-1"></i>
                                                Simpan Sementara
                                            </button>

                                            <button type="submit"
                                                class="btn btn-primary"
                                                id="saveFinal">

                                                <i class="mdi mdi-check-circle-outline me-1"></i>
                                                Simpan Final
                                            </button>

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
        $('.select2').select2({
            placeholder: '-- Pilih Opsi --'
        });

        $(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | AJAX SETUP
            |--------------------------------------------------------------------------
            */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });


            /*
            |--------------------------------------------------------------------------
            | INPUT DECIMAL KOMA
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

                });

            });


            /*
            |--------------------------------------------------------------------------
            | ADJUSTMENT FIELD
            |--------------------------------------------------------------------------
            */
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


            /*
            |--------------------------------------------------------------------------
            | STATUS CHANGE
            |--------------------------------------------------------------------------
            */
            $('#status_disposition').on('change', function() {

                const selected = $(this).val();

                toggleAdjustmentFields(selected);

            });


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN ADJUSTMENT SAAT PAGE LOAD
            |--------------------------------------------------------------------------
            |
            | showOnly = true agar value draft tidak dikosongkan.
            */
            toggleAdjustmentFields(
                $('#status_disposition').val(),
                true
            );


            /*
            |--------------------------------------------------------------------------
            | HELPER ERROR JSON
            |--------------------------------------------------------------------------
            */
            function getJsonErrorMessage(xhr) {

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    return xhr.responseJSON.message;
                }

                const contentType =
                    xhr.getResponseHeader('content-type') || '';

                if (!contentType.includes('application/json')) {

                    return 'Server tidak mengembalikan response JSON. '
                        + 'HTTP Status: '
                        + xhr.status
                        + '. Silakan cek storage/logs/laravel.log.';
                }

                return 'Terjadi kesalahan pada server.';
            }


            /*
            |--------------------------------------------------------------------------
            | RESET VALIDATION
            |--------------------------------------------------------------------------
            */
            function resetValidationErrors() {

                $('.form-control')
                    .removeClass('is-invalid');

                $('.text-danger')
                    .html('');
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN SEMENTARA / DRAFT
            |--------------------------------------------------------------------------
            */
            $('#saveDraft').on('click', function() {

                const button = $(this);

                $.ajax({

                    url: "{{ route('analisa.monitoring-pasteurisasi.draft.store') }}",

                    type: "POST",

                    data: $('#form').serialize(),

                    dataType: 'json',

                    headers: {
                        'Accept': 'application/json'
                    },

                    beforeSend: function() {

                        button
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-2"></i>'
                                + 'Menyimpan...'
                            );

                        $('#saveFinal')
                            .prop('disabled', true);

                        resetValidationErrors();
                    },

                    complete: function() {

                        button
                            .prop('disabled', false)
                            .html(
                                '<i class="mdi mdi-content-save-outline me-1"></i>'
                                + 'Simpan Sementara'
                            );

                        $('#saveFinal')
                            .prop('disabled', false);
                    },

                    success: function(response) {

                        if (
                            !response ||
                            response.status !== 'success'
                        ) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan Draft',
                                text: response?.message ??
                                    'Response server tidak sesuai.'
                            });

                            return;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Tersimpan',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });

                    },

                    error: function(xhr) {

                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION DRAFT
                        |--------------------------------------------------------------------------
                        */
                        if (
                            xhr.status === 422 &&
                            xhr.responseJSON &&
                            xhr.responseJSON.errors
                        ) {

                            const errors =
                                xhr.responseJSON.errors;

                            const firstError =
                                Object.values(errors)[0];

                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Draft Tidak Valid',
                                text: Array.isArray(firstError) ?
                                    firstError[0] :
                                    getJsonErrorMessage(xhr)
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ACCESS
                        |--------------------------------------------------------------------------
                        */
                        if (xhr.status === 403) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Akses Ditolak',
                                text: getJsonErrorMessage(xhr)
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | NON JSON / SERVER ERROR
                        |--------------------------------------------------------------------------
                        */
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan Draft',
                            text: getJsonErrorMessage(xhr)
                        });

                    }

                });

            });


            /*
            |--------------------------------------------------------------------------
            | SIMPAN FINAL
            |--------------------------------------------------------------------------
            */
            $('#form').submit(function(e) {

                e.preventDefault();

                $.ajax({

                    data: $(this).serialize(),

                    url: "{{ route('analisa.monitoring-pasteurisasi.update') }}",

                    type: "POST",

                    dataType: 'json',

                    headers: {
                        'Accept': 'application/json'
                    },

                    beforeSend: function() {

                        $('#saveFinal')
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-2"></i>'
                                + 'Proses...'
                            );

                        $('#saveDraft')
                            .prop('disabled', true);

                        resetValidationErrors();
                    },

                    complete: function() {

                        $('#saveFinal')
                            .prop('disabled', false)
                            .html(
                                '<i class="mdi mdi-check-circle-outline me-1"></i>'
                                + 'Simpan Final'
                            );

                        $('#saveDraft')
                            .prop('disabled', false);
                    },

                    success: function(response) {

                        if (
                            !response ||
                            response.status !== 'success'
                        ) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Disimpan',
                                text: response?.message ??
                                    'Response server tidak sesuai.'
                            });

                            return;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message
                        }).then(() => {

                            window.location.href =
                                "{{ route('analisa.monitoring-pasteurisasi.show', '') }}/"
                                + {{ $pasteurisasi->productionBatch->id }};

                        });

                    },

                    error: function(xhr) {

                        const response =
                            xhr.responseJSON;


                        /*
                        |--------------------------------------------------------------------------
                        | BUSINESS VALIDATION
                        |--------------------------------------------------------------------------
                        */
                        if (
                            xhr.status === 409 &&
                            response &&
                            response.message
                        ) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Disimpan',
                                text: response.message
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ROLE / ACCESS
                        |--------------------------------------------------------------------------
                        */
                        if (
                            xhr.status === 403 &&
                            response &&
                            response.message
                        ) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Akses Ditolak',
                                text: response.message
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION FINAL
                        |--------------------------------------------------------------------------
                        */
                        if (
                            xhr.status === 422 &&
                            response &&
                            response.errors
                        ) {

                            const errors =
                                response.errors;


                            if (errors.brix) {

                                $('#brix')
                                    .addClass('is-invalid');

                                $('.errorBrix')
                                    .html(
                                        errors.brix.join('<br>')
                                    );
                            }


                            if (errors.nacl) {

                                $('#nacl')
                                    .addClass('is-invalid');

                                $('.errorNacl')
                                    .html(
                                        errors.nacl.join('<br>')
                                    );
                            }


                            if (errors.bj) {

                                $('#bj')
                                    .addClass('is-invalid');

                                $('.errorBj')
                                    .html(
                                        errors.bj.join('<br>')
                                    );
                            }


                            if (errors.visco) {

                                $('#visco')
                                    .addClass('is-invalid');

                                $('.errorVisco')
                                    .html(
                                        errors.visco.join('<br>')
                                    );
                            }


                            if (errors.aw) {

                                $('#aw')
                                    .addClass('is-invalid');

                                $('.errorAw')
                                    .html(
                                        errors.aw.join('<br>')
                                    );
                            }


                            if (errors.organo) {

                                $('#organo')
                                    .addClass('is-invalid');

                                $('.errorOrgano')
                                    .html(
                                        errors.organo.join('<br>')
                                    );
                            }


                            if (errors.buih) {

                                $('#buih')
                                    .addClass('is-invalid');

                                $('.errorBuih')
                                    .html(
                                        errors.buih.join('<br>')
                                    );
                            }


                            if (errors.ph) {

                                $('#ph')
                                    .addClass('is-invalid');

                                $('.errorPh')
                                    .html(
                                        errors.ph.join('<br>')
                                    );
                            }


                            if (errors.aroma) {

                                $('#aroma')
                                    .addClass('is-invalid');

                                $('.errorAroma')
                                    .html(
                                        errors.aroma.join('<br>')
                                    );
                            }


                            if (errors.endapan) {

                                $('#endapan')
                                    .addClass('is-invalid');

                                $('.errorEndapan')
                                    .html(
                                        errors.endapan.join('<br>')
                                    );
                            }


                            if (errors.status_disposition) {

                                $('#status_disposition')
                                    .addClass('is-invalid');

                                $('.errorStatusDisposition')
                                    .html(
                                        errors.status_disposition
                                        .join('<br>')
                                    );
                            }


                            if (errors.disposition) {

                                $('#disposition')
                                    .addClass('is-invalid');

                                $('.errorDisposition')
                                    .html(
                                        errors.disposition.join('<br>')
                                    );
                            }


                            if (errors.adjustment_qty_air) {

                                $('input[name="adjustment_qty_air"]')
                                    .addClass('is-invalid');
                            }


                            if (errors.adjustment_qty_gula) {

                                $('input[name="adjustment_qty_gula"]')
                                    .addClass('is-invalid');
                            }


                            if (errors.adjustment_qty_garam) {

                                $('input[name="adjustment_qty_garam"]')
                                    .addClass('is-invalid');
                            }


                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Belum Lengkap',
                                text: response.message ??
                                    'Lengkapi seluruh field wajib sebelum Simpan Final.'
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR LAIN / NON JSON
                        |--------------------------------------------------------------------------
                        */
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: getJsonErrorMessage(xhr)
                        });

                    }

                });

            });

        });
    </script>
@endsection