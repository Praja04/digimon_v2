@extends('layouts.component.main')

@section('title', 'Analisa Blending Awal')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <h4 class="mb-sm-0">
                        @yield('title')
                    </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">
                                    Menu
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('analisa.blending-awal.index') }}">
                                    Analisa Blending Awal
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
                                                {{ $blending->productionBatch->po_number }}
                                                (Nomor PO)
                                            </h4>

                                            <div class="hstack gap-3 flex-wrap">

                                                <div>
                                                    <a
                                                        href="#"
                                                        class="text-primary d-block"
                                                    >
                                                        {{ Session::get('username') }}
                                                    </a>
                                                </div>

                                                <div class="vr"></div>

                                                <div class="text-muted">
                                                    Tanggal Produksi :

                                                    <span class="text-body fw-medium">
                                                        {{ $blending->productionBatch->date }}
                                                    </span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>


                                    <div class="row mt-4">

                                        <div class="col-lg-6 col-sm-6">

                                            <div class="p-2 border border-dashed rounded">

                                                <div class="d-flex align-items-center">

                                                    <div class="avatar-sm me-2">

                                                        <div class="avatar-title rounded bg-transparent text-success fs-24">

                                                            <i class="ri-drop-fill"></i>

                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <p class="text-muted mb-1">
                                                            Variant :
                                                        </p>

                                                        <h5 class="mb-0">
                                                            {{ $blending->productionBatch->variant }}
                                                        </h5>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="col-lg-6 col-sm-6">

                                            <div class="p-2 border border-dashed rounded">

                                                <div class="d-flex align-items-center">

                                                    <div class="avatar-sm me-2">

                                                        <div class="avatar-title rounded bg-transparent text-success fs-24">

                                                            <i class="ri-arrow-left-right-line"></i>

                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <p class="text-muted mb-1">
                                                            Batch Range :
                                                        </p>

                                                        <h5 class="mb-0">
                                                            {{ $blending->productionBatch->batch_range }}
                                                        </h5>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="col-lg-6 col-sm-6 mt-3">

                                            <div class="p-2 border border-dashed rounded">

                                                <div class="d-flex align-items-center">

                                                    <div class="avatar-sm me-2">

                                                        <div class="avatar-title rounded bg-transparent text-success fs-24">

                                                            <i class="ri-list-ordered"></i>

                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <p class="text-muted mb-1">
                                                            Batch :
                                                        </p>

                                                        <h5 class="mb-0">

                                                            {{ $blending->batch_range }}

                                                            @if ($blending->additionalBatches)

                                                                @foreach ($blending->additionalBatches as $relasi)

                                                                    @if (
                                                                        !in_array(
                                                                            (int) $relasi->batch,
                                                                            array_map(
                                                                                'intval',
                                                                                explode(
                                                                                    '-',
                                                                                    $blending->batch_range
                                                                                )
                                                                            )
                                                                        )
                                                                    )

                                                                        -{{ $relasi->batch }}

                                                                    @endif

                                                                @endforeach

                                                            @endif

                                                        </h5>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="col-lg-6 col-sm-6 mt-3">

                                            <div class="p-2 border border-dashed rounded">

                                                <div class="d-flex align-items-center">

                                                    <div class="avatar-sm me-2">

                                                        <div class="avatar-title rounded bg-transparent text-success fs-24">

                                                            <i class="ri-hashtag"></i>

                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <p class="text-muted mb-1">
                                                            Nomor Blending :
                                                        </p>

                                                        <h5 class="mb-0">
                                                            {{ $blending->nomor_blending }}
                                                        </h5>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="col-lg-6 col-sm-6 mt-3">

                                            <div class="p-2 border border-dashed rounded">

                                                <div class="d-flex align-items-center">

                                                    <div class="avatar-sm me-2">

                                                        <div class="avatar-title rounded bg-transparent text-success fs-24">

                                                            <i class="ri-drop-line"></i>

                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <p class="text-muted mb-1">
                                                            Volume Blending :
                                                        </p>

                                                        <h5 class="mb-0">
                                                            {{ $blending->volume }}
                                                            Liter
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
                                            {{ $blending->productionBatch->description ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>


                <div class="card">
                    <div class="card-body">

                        {{-- DRAFT ANALIS --}}
                        @if (
                            auth()->user()->role === 'Analis Kimia'
                            && $draft
                        )

                            <div class="alert alert-warning d-flex align-items-center mb-4">

                                <i class="ri-draft-line fs-4 me-2"></i>

                                <div>
                                    Data ini memiliki
                                    <strong>draft sementara Analis Kimia</strong>.
                                    Data draft sudah dimuat kembali ke form.
                                </div>

                            </div>

                        @endif


                        {{-- DRAFT FOREMAN --}}
                        @if (
                            auth()->user()->role === 'Foreman'
                            && $foremanDraft
                        )

                            <div class="alert alert-warning d-flex align-items-center mb-4">

                                <i class="ri-draft-line fs-4 me-2"></i>

                                <div>
                                    Data ini memiliki
                                    <strong>draft sementara Foreman</strong>.
                                    Data draft sudah dimuat kembali ke form.
                                </div>

                            </div>

                        @endif


                        <form id="form">

                            @csrf

                            <input
                                type="hidden"
                                name="id"
                                id="id"
                                value="{{ $blending->id }}"
                            >


                            <div class="row g-3">


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        BRIX
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="brix"
                                        id="brix"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->brix ?? $blending->brix ?? '') }}"
                                    >

                                    <small class="text-danger errorBrix"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Visco
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="visco"
                                        id="visco"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->visco ?? $blending->visco ?? '') }}"
                                    >

                                    <small class="text-danger errorVisco"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        NACL
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="nacl"
                                        id="nacl"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->nacl ?? $blending->nacl ?? '') }}"
                                    >

                                    <small class="text-danger errorNacl"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Bj
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="bj"
                                        id="bj"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->bj ?? $blending->bj ?? '') }}"
                                    >

                                    <small class="text-danger errorBj"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        pH
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="ph"
                                        id="ph"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->ph ?? $blending->ph ?? '') }}"
                                    >

                                    <small class="text-danger errorPh"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Aw
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="aw"
                                        id="aw"
                                        class="form-control comma-input"
                                        placeholder="Contoh: 0,00"
                                        value="{{ str_replace('.', ',', $draft?->aw ?? $blending->aw ?? '') }}"
                                    >

                                    <small class="text-danger errorAw"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Organo
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="organo"
                                        id="organo"
                                        class="form-control"
                                        value="{{ $draft?->organo ?? $blending->organo ?? '' }}"
                                        oninput="this.value = this.value.toUpperCase();"
                                    >

                                    <small class="text-danger errorOrgano"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Aroma
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="aroma"
                                        id="aroma"
                                        class="form-control"
                                        value="{{ $draft?->aroma ?? $blending->aroma ?? '' }}"
                                        oninput="this.value = this.value.toUpperCase();"
                                    >

                                    <small class="text-danger errorAroma"></small>

                                </div>


                                <div class="col-lg-4">

                                    <label class="form-label">
                                        Warna
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="color"
                                        id="color"
                                        class="select2 form-control"
                                    >

                                        <option value="">
                                            -- Pilih Warna --
                                        </option>

                                        @foreach ($colors as $color)

                                            <option
                                                value="{{ $color->id }}"
                                                {{
                                                    (string) (
                                                        $draft?->color_id
                                                        ?? $blending->color_id
                                                        ?? ''
                                                    )
                                                    === (string) $color->id
                                                    ? 'selected'
                                                    : ''
                                                }}
                                            >
                                                {{ $color->name }}
                                                ({{ $color->code }})
                                            </option>

                                        @endforeach

                                    </select>

                                    <small class="text-danger errorColor"></small>

                                </div>


                                <div class="col-lg-12">

                                    @php
                                        $selectedStatus =
                                            $draft?->status_disposition
                                            ?? $blending->status
                                            ?? '';
                                    @endphp

                                    <label class="form-label">
                                        Status
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="status_disposition"
                                        id="status_disposition"
                                        class="form-control disposition-select"
                                    >

                                        <option value="">
                                            -- Pilih Status --
                                        </option>

                                        <option
                                            value="OK"
                                            {{ $selectedStatus === 'OK' ? 'selected' : '' }}
                                        >
                                            OK
                                        </option>

                                        <option
                                            value="NOT OK"
                                            {{ $selectedStatus === 'NOT OK' ? 'selected' : '' }}
                                        >
                                            NOT OK
                                        </option>

                                        <option
                                            value="Adjustment"
                                            {{ $selectedStatus === 'Adjustment' ? 'selected' : '' }}
                                        >
                                            Adjustment
                                        </option>

                                    </select>

                                    <small class="text-danger errorStatusDisposition"></small>

                                </div>


                                @if (auth()->user()->role === 'Foreman')

                                    @php
                                        $selectedDisposition =
                                            $foremanDraft?->disposition
                                            ?? $blending->disposition
                                            ?? '';
                                    @endphp


                                    <div class="col-lg-12">

                                        <label class="form-label">
                                            Disposition
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="disposition"
                                            id="disposition"
                                            class="form-control disposition-select"
                                        >

                                            <option value="">
                                                -- Pilih Disposition --
                                            </option>

                                            <option
                                                value="Release"
                                                {{ $selectedDisposition === 'Release' ? 'selected' : '' }}
                                            >
                                                Release
                                            </option>

                                            <option
                                                value="Release Bersyarat"
                                                {{ $selectedDisposition === 'Release Bersyarat' ? 'selected' : '' }}
                                            >
                                                Release Bersyarat
                                            </option>

                                            <option
                                                value="Resampling"
                                                {{ $selectedDisposition === 'Resampling' ? 'selected' : '' }}
                                            >
                                                Resampling
                                            </option>

                                            <option
                                                value="Adjustment"
                                                {{ $selectedDisposition === 'Adjustment' ? 'selected' : '' }}
                                            >
                                                Adjustment
                                            </option>

                                            <option
                                                value="Reject"
                                                {{ $selectedDisposition === 'Reject' ? 'selected' : '' }}
                                            >
                                                Reject
                                            </option>

                                            <option
                                                value="Repro"
                                                {{ $selectedDisposition === 'Repro' ? 'selected' : '' }}
                                            >
                                                Repro
                                            </option>

                                            <option
                                                value="Jalan Bareng"
                                                {{ $selectedDisposition === 'Jalan Bareng' ? 'selected' : '' }}
                                            >
                                                Jalan Bareng
                                            </option>

                                            <option
                                                value="Leveling"
                                                {{ $selectedDisposition === 'Leveling' ? 'selected' : '' }}
                                            >
                                                Leveling
                                            </option>

                                        </select>

                                        <small class="text-danger errorDisposition"></small>

                                    </div>

                                @endif


                                <div class="col-lg-12">

                                    @php
                                        if (
                                            auth()->user()->role
                                            === 'Foreman'
                                        ) {
                                            $selectedRemark =
                                                $foremanDraft?->disposition_remark
                                                ?? $blending->disposition_remark
                                                ?? '';
                                        } else {
                                            $selectedRemark =
                                                $draft?->disposition_remark
                                                ?? $blending->disposition_remark
                                                ?? '';
                                        }
                                    @endphp

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
                                    >{{ $selectedRemark }}</textarea>

                                </div>


                                @php
                                    if (
                                        auth()->user()->role
                                        === 'Foreman'
                                    ) {
                                        $adjustmentAir =
                                            $foremanDraft?->adjustment_qty_air
                                            ?? $blending->adjustment_qty_air
                                            ?? '';

                                        $adjustmentCaramel =
                                            $foremanDraft?->adjustment_qty_caramel
                                            ?? $blending->adjustment_qty_caramel
                                            ?? '';

                                        $adjustmentGaram =
                                            $foremanDraft?->adjustment_qty_garam
                                            ?? $blending->adjustment_qty_garam
                                            ?? '';
                                    } else {
                                        $adjustmentAir =
                                            $draft?->adjustment_qty_air
                                            ?? $blending->adjustment_qty_air
                                            ?? '';

                                        $adjustmentCaramel =
                                            $draft?->adjustment_qty_caramel
                                            ?? $blending->adjustment_qty_caramel
                                            ?? '';

                                        $adjustmentGaram =
                                            $draft?->adjustment_qty_garam
                                            ?? $blending->adjustment_qty_garam
                                            ?? '';
                                    }
                                @endphp


                                <div class="col-lg-12 d-none adjustment-qty-wrapper">

                                    <h6 class="form-label fw-bold">
                                        Adjustment Qty
                                    </h6>

                                    <div class="row g-3">


                                        <div class="col-lg-4">

                                            <label class="form-label">
                                                Air (Liter)
                                            </label>

                                            <input
                                                type="text"
                                                name="adjustment_qty_air"
                                                class="form-control adjustment-qty comma-input"
                                                placeholder="0,00"
                                                value="{{ str_replace('.', ',', $adjustmentAir) }}"
                                            >

                                        </div>


                                        <div class="col-lg-4">

                                            <label class="form-label">
                                                Caramel (Kg)
                                            </label>

                                            <input
                                                type="text"
                                                name="adjustment_qty_caramel"
                                                class="form-control adjustment-qty comma-input"
                                                placeholder="0,00"
                                                value="{{ str_replace('.', ',', $adjustmentCaramel) }}"
                                            >

                                        </div>


                                        <div class="col-lg-4">

                                            <label class="form-label">
                                                Garam (Kg)
                                            </label>

                                            <input
                                                type="text"
                                                name="adjustment_qty_garam"
                                                class="form-control adjustment-qty comma-input"
                                                placeholder="0,00"
                                                value="{{ str_replace('.', ',', $adjustmentGaram) }}"
                                            >

                                        </div>


                                    </div>

                                </div>


                                <div class="d-flex justify-content-end gap-2 mt-4">


                                    @if (auth()->user()->role === 'Analis Kimia')

                                        <button
                                            type="button"
                                            class="btn btn-warning"
                                            id="saveDraft"
                                        >
                                            <i class="ri-save-3-line me-1"></i>
                                            Simpan Sementara
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                            id="saveFinal"
                                            disabled
                                        >
                                            <i class="ri-check-line me-1"></i>
                                            Simpan Final
                                        </button>


                                    @elseif (auth()->user()->role === 'Foreman')

                                        <button
                                            type="button"
                                            class="btn btn-warning"
                                            id="saveForemanDraft"
                                        >
                                            <i class="ri-save-3-line me-1"></i>
                                            Simpan Sementara
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                            id="saveFinal"
                                            disabled
                                        >
                                            <i class="ri-check-line me-1"></i>
                                            Simpan Final
                                        </button>


                                    @else

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                            id="saveFinal"
                                        >
                                            Simpan
                                        </button>

                                    @endif


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

    $('.select2').select2({
        placeholder: '-- Pilih Opsi --'
    });


    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]')
                        .attr('content')
            }
        });


        /*
        |--------------------------------------------------------------------------
        | FORMAT KOMA
        |--------------------------------------------------------------------------
        */
        document
            .querySelectorAll('.comma-input')
            .forEach(function(el) {

                el.addEventListener(
                    'input',
                    function() {

                        const value =
                            this.value;

                        if (value.includes('.')) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Format Salah!',
                                text:
                                    'Gunakan tanda koma (,) untuk desimal, bukan titik (.)',
                                confirmButtonText:
                                    'Mengerti',
                                confirmButtonColor:
                                    '#3085d6'
                            });

                            this.value =
                                value.replace(
                                    /\./g,
                                    ','
                                );
                        }
                    }
                );

            });


        /*
        |--------------------------------------------------------------------------
        | CEK SIMPAN FINAL
        |--------------------------------------------------------------------------
        */
        function checkFinalForm() {

            const brix =
                $('#brix').val().trim();

            const visco =
                $('#visco').val().trim();

            const nacl =
                $('#nacl').val().trim();

            const bj =
                $('#bj').val().trim();

            const ph =
                $('#ph').val().trim();

            const aw =
                $('#aw').val().trim();

            const organo =
                $('#organo').val().trim();

            const aroma =
                $('#aroma').val().trim();

            const color =
                $('#color').val();

            const status =
                $('#status_disposition').val();


            let complete =
                brix !== '' &&
                visco !== '' &&
                nacl !== '' &&
                bj !== '' &&
                ph !== '' &&
                aw !== '' &&
                organo !== '' &&
                aroma !== '' &&
                color !== '' &&
                color !== null &&
                status !== '' &&
                status !== null;


            /*
             * NOT OK / Adjustment
             * wajib catatan.
             */
            if (
                status === 'NOT OK' ||
                status === 'Adjustment'
            ) {
                const remark =
                    $('#disposition_remark')
                        .val()
                        .trim();

                if (remark === '') {
                    complete =
                        false;
                }
            }


            /*
             * Foreman wajib pilih disposition.
             */
            @if (auth()->user()->role === 'Foreman')

                const disposition =
                    $('#disposition').val();

                if (
                    disposition === '' ||
                    disposition === null
                ) {
                    complete =
                        false;
                }

            @endif


            $('#saveFinal')
                .prop(
                    'disabled',
                    !complete
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ADJUSTMENT FIELD
        |--------------------------------------------------------------------------
        */
        function toggleAdjustmentFields(
            showOnly = false
        ) {

            const status =
                $('#status_disposition').val();

            const disposition =
                $('#disposition').val();

            const isAdjustment =
                status === 'Adjustment'
                ||
                disposition === 'Adjustment';


            if (isAdjustment) {

                $('.adjustment-qty-wrapper')
                    .removeClass('d-none');

            } else {

                $('.adjustment-qty-wrapper')
                    .addClass('d-none');

                if (!showOnly) {
                    $('.adjustment-qty')
                        .val('');
                }
            }


            checkFinalForm();
        }


        /*
        |--------------------------------------------------------------------------
        | FIRST LOAD
        |--------------------------------------------------------------------------
        */
        toggleAdjustmentFields(true);

        checkFinalForm();


        /*
        |--------------------------------------------------------------------------
        | EVENT FIELD
        |--------------------------------------------------------------------------
        */
        $(
            '#brix, #visco, #nacl, #bj, #ph, #aw, #organo, #aroma, #disposition_remark'
        )
        .on(
            'input change',
            function() {
                checkFinalForm();
            }
        );


        $('#color')
            .on(
                'change',
                function() {
                    checkFinalForm();
                }
            );


        $('#status_disposition')
            .on(
                'change',
                function() {

                    toggleAdjustmentFields();

                    checkFinalForm();
                }
            );


        $('#disposition')
            .on(
                'change',
                function() {

                    toggleAdjustmentFields();

                    checkFinalForm();
                }
            );


        $('.adjustment-qty')
            .on(
                'input change',
                function() {
                    checkFinalForm();
                }
            );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SEMENTARA ANALIS
        |--------------------------------------------------------------------------
        */
        $('#saveDraft')
            .on(
                'click',
                function() {

                    const button =
                        $(this);


                    $.ajax({

                        data:
                            $('#form')
                                .serialize(),

                        url:
                            "{{ route('analisa.blending-awal.draft.store') }}",

                        type:
                            'POST',

                        dataType:
                            'json',


                        beforeSend:
                            function() {

                                button
                                    .prop(
                                        'disabled',
                                        true
                                    )
                                    .html(
                                        '<i class="mdi mdi-loading mdi-spin me-2"></i> Menyimpan...'
                                    );
                            },


                        complete:
                            function() {

                                button
                                    .prop(
                                        'disabled',
                                        false
                                    )
                                    .html(
                                        '<i class="ri-save-3-line me-1"></i> Simpan Sementara'
                                    );

                                checkFinalForm();
                            },


                        success:
                            function(response) {

                                Swal.fire({
                                    icon:
                                        'success',

                                    title:
                                        'Draft Tersimpan',

                                    text:
                                        response.message
                                });
                            },


                        error:
                            function(xhr) {

                                const response =
                                    xhr.responseJSON;

                                Swal.fire({
                                    icon:
                                        xhr.status === 422
                                            ? 'warning'
                                            : 'error',

                                    title:
                                        'Gagal',

                                    text:
                                        response?.message
                                        ?? 'Draft gagal disimpan.'
                                });
                            }

                    });

                }
            );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SEMENTARA FOREMAN
        |--------------------------------------------------------------------------
        */
        $('#saveForemanDraft')
            .on(
                'click',
                function() {

                    const button =
                        $(this);


                    $.ajax({

                        data:
                            $('#form')
                                .serialize(),

                        url:
                            "{{ route('analisa.blending-awal.foreman-draft.store') }}",

                        type:
                            'POST',

                        dataType:
                            'json',


                        beforeSend:
                            function() {

                                button
                                    .prop(
                                        'disabled',
                                        true
                                    )
                                    .html(
                                        '<i class="mdi mdi-loading mdi-spin me-2"></i> Menyimpan...'
                                    );
                            },


                        complete:
                            function() {

                                button
                                    .prop(
                                        'disabled',
                                        false
                                    )
                                    .html(
                                        '<i class="ri-save-3-line me-1"></i> Simpan Sementara'
                                    );

                                checkFinalForm();
                            },


                        success:
                            function(response) {

                                Swal.fire({
                                    icon:
                                        'success',

                                    title:
                                        'Draft Foreman Tersimpan',

                                    text:
                                        response.message
                                });
                            },


                        error:
                            function(xhr) {

                                const response =
                                    xhr.responseJSON;

                                Swal.fire({
                                    icon:
                                        xhr.status === 422
                                            ? 'warning'
                                            : 'error',

                                    title:
                                        'Gagal',

                                    text:
                                        response?.message
                                        ?? 'Draft Foreman gagal disimpan.'
                                });
                            }

                    });

                }
            );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN FINAL
        |--------------------------------------------------------------------------
        */
        $('#form')
            .submit(
                function(e) {

                    e.preventDefault();


                    if (
                        $('#saveFinal')
                            .prop('disabled')
                    ) {
                        return;
                    }


                    $.ajax({

                        data:
                            $(this)
                                .serialize(),

                        url:
                            "{{ route('analisa.blending-awal.update') }}",

                        type:
                            'POST',

                        dataType:
                            'json',


                        beforeSend:
                            function() {

                                $('#saveFinal')
                                    .prop(
                                        'disabled',
                                        true
                                    )
                                    .html(
                                        '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                                    );

                                $('#saveDraft')
                                    .prop(
                                        'disabled',
                                        true
                                    );

                                $('#saveForemanDraft')
                                    .prop(
                                        'disabled',
                                        true
                                    );

                                $('.form-control')
                                    .removeClass(
                                        'is-invalid'
                                    );

                                $('.text-danger')
                                    .html('');
                            },


                        complete:
                            function() {

                                $('#saveFinal')
                                    .html(
                                        '<i class="ri-check-line me-1"></i> Simpan Final'
                                    );

                                $('#saveDraft')
                                    .prop(
                                        'disabled',
                                        false
                                    );

                                $('#saveForemanDraft')
                                    .prop(
                                        'disabled',
                                        false
                                    );

                                checkFinalForm();
                            },


                        success:
                            function(response) {

                                Swal.fire({
                                    icon:
                                        'success',

                                    title:
                                        'Sukses',

                                    text:
                                        response.message
                                })
                                .then(
                                    () => {

                                        window.location.href =
                                            "{{ route(
                                                'analisa.blending-awal.show',
                                                $blending->productionBatch->id
                                            ) }}";

                                    }
                                );
                            },


                        error:
                            function(xhr) {

                                const response =
                                    xhr.responseJSON;


                                if (
                                    xhr.status === 409 &&
                                    response?.message
                                ) {

                                    Swal.fire({
                                        icon:
                                            'warning',

                                        title:
                                            'Gagal Disimpan',

                                        text:
                                            response.message
                                    });

                                    return;
                                }


                                if (
                                    xhr.status === 403 &&
                                    response?.message
                                ) {

                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Akses Ditolak',

                                        text:
                                            response.message
                                    });

                                    return;
                                }


                                if (
                                    xhr.status === 422 &&
                                    response?.errors
                                ) {

                                    const errors =
                                        response.errors;


                                    if (errors.brix) {

                                        $('#brix')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorBrix')
                                            .html(
                                                errors.brix.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.nacl) {

                                        $('#nacl')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorNacl')
                                            .html(
                                                errors.nacl.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.bj) {

                                        $('#bj')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorBj')
                                            .html(
                                                errors.bj.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.visco) {

                                        $('#visco')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorVisco')
                                            .html(
                                                errors.visco.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.aw) {

                                        $('#aw')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorAw')
                                            .html(
                                                errors.aw.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.organo) {

                                        $('#organo')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorOrgano')
                                            .html(
                                                errors.organo.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.aroma) {

                                        $('#aroma')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorAroma')
                                            .html(
                                                errors.aroma.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.ph) {

                                        $('#ph')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorPh')
                                            .html(
                                                errors.ph.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (errors.color) {

                                        $('#color')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorColor')
                                            .html(
                                                errors.color.join(
                                                    '<br>'
                                                )
                                            );
                                    }


                                    if (
                                        errors.status_disposition
                                    ) {

                                        $('#status_disposition')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorStatusDisposition')
                                            .html(
                                                errors
                                                    .status_disposition
                                                    .join(
                                                        '<br>'
                                                    )
                                            );
                                    }


                                    if (errors.disposition) {

                                        $('#disposition')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorDisposition')
                                            .html(
                                                errors
                                                    .disposition
                                                    .join(
                                                        '<br>'
                                                    )
                                            );
                                    }


                                    return;
                                }


                                Swal.fire({
                                    icon:
                                         'error',
                                    title:
                                        'Kesalahan',

                                    text:
                                        response?.message
                                        ?? 'Terjadi kesalahan, silakan coba lagi.'
                                });
                            }

                    });

                }
            );

    });

</script>
@endsection