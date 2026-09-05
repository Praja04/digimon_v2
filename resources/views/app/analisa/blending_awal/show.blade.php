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
                                                {{ $productionBatch->po_number }}
                                                (Nomor PO)
                                            </h4>

                                            <div class="hstack gap-3 flex-wrap">

                                                <div class="text-muted">
                                                    Tanggal Produksi :
                                                    <span class="text-body fw-medium">
                                                        {{ $productionBatch->date }}
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
                                                            {{ $productionBatch->variant }}
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
                                                            {{ $productionBatch->batch_range }}
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
                                            {{ $productionBatch->description ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-lg-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive table-card mb-4">

                                <table
                                    class="table align-middle table-nowrap mb-0"
                                    id="tasksTable"
                                >

                                    <thead class="table-light text-muted">

                                        <tr>
                                            <th>Nomor PO</th>
                                            <th>Batch Range</th>
                                            <th>No Blending</th>
                                            <th>Volume</th>
                                            <th>Waktu Scan</th>
                                            <th>Status</th>
                                            <th>Disposisi</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>

                                    </thead>

                                    <tbody class="list form-check-all">

                                        @forelse ($productionBatch->BlendingAwal as $blending)

                                            @php
                                                $dispositionUpper =
                                                    strtoupper(
                                                        $blending->status ?? ''
                                                    );

                                                $rowClass =
                                                    match ($dispositionUpper) {
                                                        'NOT OK' =>
                                                            'table-danger',

                                                        'ADJUSTMENT' =>
                                                            'table-warning',

                                                        default =>
                                                            '',
                                                    };

                                                $badgeClass =
                                                    match ($dispositionUpper) {
                                                        'NOT OK' =>
                                                            'bg-danger',

                                                        'ADJUSTMENT' =>
                                                            'bg-warning text-dark',

                                                        'OK' =>
                                                            'bg-success',

                                                        default =>
                                                            'bg-secondary',
                                                    };
                                            @endphp


                                            <tr class="{{ $rowClass }}">

                                                <td>

                                                    {{ $productionBatch->po_number }}

                                                    @if ($blending->revisi != null)

                                                        <span
                                                            class="badge {{ $badgeClass }} ms-1"
                                                            title="Revisi ke-{{ $blending->revisi }}"
                                                        >

                                                            Rev. {{ $blending->revisi }}

                                                        </span>

                                                    @endif

                                                </td>


                                                <td>

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

                                                </td>


                                                <td>
                                                    {{ $blending->nomor_blending }}
                                                </td>


                                                <td>
                                                    {{ $blending->volume }}
                                                </td>


                                                <td>

                                                    {{
                                                        $blending->scanned_at
                                                            ? \Carbon\Carbon::parse(
                                                                $blending->scanned_at
                                                            )->format(
                                                                'd/m/Y H:i:s'
                                                            )
                                                            : '-'
                                                    }}

                                                </td>


                                                <td>

                                                    @if ($blending->status)

                                                        <span
                                                            class="badge {{
                                                                match (
                                                                    strtoupper(
                                                                        $blending->status
                                                                    )
                                                                ) {
                                                                    'OK' =>
                                                                        'bg-success',

                                                                    'NOT OK' =>
                                                                        'bg-danger',

                                                                    'ADJUSTMENT' =>
                                                                        'bg-warning text-dark',

                                                                    default =>
                                                                        'bg-secondary',
                                                                }
                                                            }}"
                                                        >

                                                            {{ $blending->status }}

                                                        </span>

                                                    @else

                                                        <span class="text-muted">
                                                            -
                                                        </span>

                                                    @endif

                                                </td>


                                                <td>
                                                    {{ $blending->disposition ?? '-' }}
                                                </td>


                                                <td>

                                                    <button
                                                        class="btn btn-sm btn-info"
                                                        id="btnDetail"
                                                        data-id="{{ $blending->id }}"
                                                    >
                                                        <i class="ri-eye-line"></i>
                                                    </button>


                                                    @if (
                                                        auth()->user()->role
                                                        == 'Foreman'
                                                    )

                                                        <button
                                                            class="btn btn-sm btn-secondary ms-1"
                                                            id="btnFormulasi"
                                                            data-id="{{ $blending->id }}"
                                                        >
                                                            <i class="ri-file-list-line"></i>
                                                        </button>

                                                    @endif

                                                </td>


                                                <td>

                                                    @if (is_null($blending->status))

                                                        <a
                                                            href="{{ route(
                                                                'analisa.blending-awal.show_batch',
                                                                $blending->id
                                                            ) }}"
                                                            class="btn btn-sm btn-primary"
                                                        >
                                                            Analisa Data
                                                        </a>

                                                    @else

                                                        @if (
                                                            auth()->user()->role
                                                            == 'Foreman'
                                                        )

                                                            @if (is_null($blending->disposition))

                                                                <button
                                                                    type="button"
                                                                    class="btn btn-sm btn-warning open-blending-modal-edit"
                                                                    data-id="{{ $blending->id }}"
                                                                >
                                                                    Kelola Data
                                                                </button>

                                                            @else

                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line align-middle"></i>
                                                                    Lengkap
                                                                </span>

                                                            @endif

                                                        @else

                                                            <span class="badge bg-success-subtle text-success">
                                                                <i class="ri-check-line align-middle"></i>
                                                                Lengkap
                                                            </span>

                                                        @endif

                                                    @endif

                                                </td>

                                            </tr>

                                        @empty

                                            <tr class="text-center">

                                                <td colspan="9">
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


{{-- MODAL KELOLA DATA --}}
<div
    class="modal fade"
    id="inputBlendingModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <form id="inputForm">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Kelola Data Blending Awal
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body row g-3">

                    <div class="alert alert-danger d-none error-alert"></div>

                    <div
                        class="alert alert-warning d-none"
                        id="foremanDraftAlert"
                    >
                        <i class="ri-draft-line me-1"></i>
                        Draft Foreman ditemukan dan sudah dimuat kembali.
                    </div>


                    <input
                        type="hidden"
                        name="id"
                        id="id"
                    >


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
                            oninput="this.value = this.value.toUpperCase();"
                        >

                        <small class="text-danger errorOrgano"></small>

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

                                <option value="{{ $color->id }}">

                                    {{ $color->name }}
                                    ({{ $color->code }})

                                </option>

                            @endforeach

                        </select>

                        <small class="text-danger errorColor"></small>

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
                            oninput="this.value = this.value.toUpperCase();"
                        >

                        <small class="text-danger errorAroma"></small>

                    </div>


                    <div class="col-lg-12">

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

                            <option value="OK">
                                OK
                            </option>

                            <option value="NOT OK">
                                NOT OK
                            </option>

                            <option value="Adjustment">
                                Adjustment
                            </option>

                        </select>

                        <small class="text-danger errorStatusDisposition"></small>

                    </div>


                    @if (auth()->user()->role == 'Foreman')

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

                                <option value="Release">
                                    Release
                                </option>

                                <option value="Release Bersyarat">
                                    Release Bersyarat
                                </option>

                                <option value="Resampling">
                                    Resampling
                                </option>

                                <option value="Adjustment">
                                    Adjustment
                                </option>

                                <option value="Reject">
                                    Reject
                                </option>

                                <option value="Repro">
                                    Repro
                                </option>

                                <option value="Jalan Bareng">
                                    Jalan Bareng
                                </option>

                                <option value="Leveling">
                                    Leveling
                                </option>

                            </select>

                            <small class="text-danger errorDisposition"></small>

                        </div>

                    @endif


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
                        ></textarea>

                    </div>


                    <div class="mb-3 d-none adjustment-qty-wrapper">

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
                                >

                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Tutup
                    </button>


                    @if (auth()->user()->role === 'Foreman')

                        <button
                            type="button"
                            class="btn btn-warning"
                            id="saveForemanDraft"
                        >
                            <i class="ri-save-3-line me-1"></i>
                            Simpan Sementara
                        </button>

                    @endif


                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveFinal"
                    >
                        <i class="ri-check-line me-1"></i>
                        Simpan Final
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- MODAL DETAIL --}}
<div
    class="modal fade"
    id="detailModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-light">

                <h5 class="modal-title">
                    Detail Data Blending Awal
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="mb-4">

                    <h6 class="border-bottom pb-2 mb-3">
                        Informasi Produksi
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-4">
                            Batch Range :
                            <strong id="detail_batch_range">-</strong>
                        </div>

                        <div class="col-md-4">
                            Nomor Blending :
                            <strong id="detail_nomor_blending">-</strong>
                        </div>

                        <div class="col-md-4">
                            Volume :
                            <strong id="detail_volume">-</strong>
                        </div>

                        <div class="col-md-4">
                            Storage :
                            <strong id="detail_storage">-</strong>
                        </div>

                        <div class="col-md-4">
                            Revisi :
                            <strong id="detail_revisi">-</strong>
                        </div>

                    </div>

                </div>


                <div class="mb-4">

                    <h6 class="border-bottom pb-2 mb-3">
                        Parameter Analisa
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-4">
                            Brix :
                            <strong id="detail_brix">-</strong>
                        </div>

                        <div class="col-md-4">
                            NaCl :
                            <strong id="detail_nacl">-</strong>
                        </div>

                        <div class="col-md-4">
                            BJ :
                            <strong id="detail_bj">-</strong>
                        </div>

                        <div class="col-md-4">
                            Visco :
                            <strong id="detail_visco">-</strong>
                        </div>

                        <div class="col-md-4">
                            AW :
                            <strong id="detail_aw">-</strong>
                        </div>

                        <div class="col-md-4">
                            pH :
                            <strong id="detail_ph">-</strong>
                        </div>

                    </div>

                </div>


                <div class="mb-4">

                    <h6 class="border-bottom pb-2 mb-3">
                        Parameter Fisik
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-4">
                            Aroma :
                            <strong id="detail_aroma">-</strong>
                        </div>

                        <div class="col-md-4">
                            Organo :
                            <strong id="detail_organo">-</strong>
                        </div>

                        <div class="col-md-4">
                            Warna :
                            <strong id="detail_color">-</strong>
                        </div>

                    </div>

                </div>


                <div class="mb-4">

                    <h6 class="border-bottom pb-2 mb-3">
                        Status & Disposisi
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-4">
                            Status :
                            <strong id="detail_status">-</strong>
                        </div>

                        <div class="col-md-4">
                            Disposisi :
                            <strong id="detail_disposition">-</strong>
                        </div>

                        <div class="col-md-4">
                            Not Standard :
                            <strong id="detail_not_standard">-</strong>
                        </div>

                    </div>

                </div>


                <div class="mb-4">

                    <h6 class="border-bottom pb-2 mb-3">
                        Keterangan
                    </h6>

                    <div class="bg-light p-3 rounded">

                        <p
                            class="mb-0"
                            id="detail_remark"
                            style="white-space:pre-wrap;"
                        >
                            -
                        </p>

                    </div>

                </div>


                <div>

                    <h6 class="border-bottom pb-2 mb-3">
                        Informasi Tambahan
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-6">
                            Dibuat Oleh :
                            <strong id="detail_created_by">-</strong>
                        </div>

                        <div class="col-md-6">
                            Tanggal Dibuat :
                            <strong id="detail_created_at">-</strong>
                        </div>

                        <div class="col-md-6">
                            Terakhir Diupdate :
                            <strong id="detail_updated_at">-</strong>
                        </div>

                    </div>

                </div>

            </div>


            <div class="modal-footer bg-light">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</div>


{{-- MODAL FORMULASI --}}
<div
    class="modal fade"
    id="formulasiModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Detail Formulasi Dissolver
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="card border mb-4">

                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">
                            Informasi Production Batch
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    PO Number
                                </small>
                                <p id="formulasi-po-number">-</p>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    Variant
                                </small>
                                <p id="formulasi-variant">-</p>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    Tanggal
                                </small>
                                <p id="formulasi-date">-</p>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    Batch Range
                                </small>
                                <p id="formulasi-batch-range">-</p>
                            </div>

                        </div>

                    </div>

                </div>


                <div
                    class="alert alert-light border d-none"
                    id="formulasiSourceInfo"
                >

                    <small id="formulasi-source-text">
                        -
                    </small>

                </div>


                <div class="card border">

                    <div class="card-header">

                        <h6 class="mb-0 fw-semibold">
                            Formulasi Blending Awal
                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table
                                class="table table-bordered table-sm mb-0"
                                id="formulasiTable"
                            >

                                <thead class="table-light">

                                    <tr>
                                        <th>No</th>
                                        <th>Slot</th>
                                        <th>Material Type</th>
                                        <th>Material Group</th>
                                        <th>SPB Number</th>
                                        <th>Jenis Premix</th>
                                        <th>Qty</th>
                                    </tr>

                                </thead>

                                <tbody id="formulasiTableBody"></tbody>

                            </table>

                        </div>

                        <div class="mt-3">

                            <small class="text-muted">
                                Total Items:
                                <span
                                    class="fw-semibold"
                                    id="formulasi-total"
                                >
                                    0
                                </span>
                            </small>

                        </div>

                    </div>

                </div>


                <div
                    class="alert alert-secondary d-none"
                    id="formulasiEmptyState"
                >
                    Data formulasi tidak ditemukan.
                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')
<script>

    function formatDecimal(value) {

        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            return '';
        }

        return String(value)
            .replace(/\./g, ',');

    }


    function formatDateTime(dateString) {

        const date =
            new Date(dateString);

        return date.toLocaleDateString(
            'id-ID',
            {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }
        );

    }


    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]')
                        .attr('content')
            }
        });


        $('.select2').select2({
            placeholder: '-- Pilih Opsi --',
            dropdownParent:
                $('#inputBlendingModal')
        });


        document
            .querySelectorAll('.comma-input')
            .forEach(function(el) {

                el.addEventListener(
                    'input',
                    function() {

                        if (
                            this.value.includes('.')
                        ) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Format Salah!',
                                text: 'Gunakan tanda koma (,) untuk desimal, bukan titik (.)',
                            });

                            this.value =
                                this.value.replace(
                                    /\./g,
                                    ','
                                );
                        }
                    }
                );

            });


        function toggleAdjustmentFields(
            showOnly = false
        ) {

            const status =
                $('#status_disposition')
                    .val();

            const disposition =
                $('#disposition')
                    .val();

            const show =
                status === 'Adjustment'
                ||
                disposition === 'Adjustment';

            if (show) {

                $('.adjustment-qty-wrapper')
                    .removeClass('d-none');

                $('.adjustment-qty')
                    .prop(
                        'required',
                        true
                    );

            } else {

                $('.adjustment-qty-wrapper')
                    .addClass('d-none');

                $('.adjustment-qty')
                    .prop(
                        'required',
                        false
                    );

                if (!showOnly) {
                    $('.adjustment-qty')
                        .val('');
                }
            }
        }


        function updateFinalButtonState() {

            @if (auth()->user()->role === 'Foreman')

                const disposition =
                    $('#disposition')
                        .val();

                $('#saveFinal')
                    .prop(
                        'disabled',
                        !disposition
                    );

            @endif
        }


        $('#status_disposition')
            .on(
                'change',
                function() {

                    toggleAdjustmentFields();
                    updateFinalButtonState();

                }
            );


        $('#disposition')
            .on(
                'change',
                function() {

                    toggleAdjustmentFields();
                    updateFinalButtonState();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL KELOLA DATA
        |--------------------------------------------------------------------------
        */
        $('body')
            .on(
                'click',
                '.open-blending-modal-edit',
                function() {

                    const id =
                        $(this).data('id');

                    $.ajax({

                        type: 'GET',

                        url:
                            "{{ route('analisa.blending-awal.edit', '') }}/"
                            + id,

                        dataType:
                            'json',

                        beforeSend:
                            function() {

                                $('#inputForm')[0]
                                    .reset();

                                $('.text-danger')
                                    .html('');

                                $('.form-control')
                                    .removeClass(
                                        'is-invalid'
                                    );

                                $('#foremanDraftAlert')
                                    .addClass(
                                        'd-none'
                                    );

                                $('.adjustment-qty-wrapper')
                                    .addClass(
                                        'd-none'
                                    );
                            },

                        success:
                            function(response) {

                                $('#id')
                                    .val(
                                        response.id
                                    );

                                $('#brix')
                                    .val(
                                        formatDecimal(
                                            response.brix
                                        )
                                    );

                                $('#nacl')
                                    .val(
                                        formatDecimal(
                                            response.nacl
                                        )
                                    );

                                $('#bj')
                                    .val(
                                        formatDecimal(
                                            response.bj
                                        )
                                    );

                                $('#visco')
                                    .val(
                                        formatDecimal(
                                            response.visco
                                        )
                                    );

                                $('#aw')
                                    .val(
                                        formatDecimal(
                                            response.aw
                                        )
                                    );

                                $('#ph')
                                    .val(
                                        formatDecimal(
                                            response.ph
                                        )
                                    );

                                $('#organo')
                                    .val(
                                        response.organo
                                        || ''
                                    );

                                $('#aroma')
                                    .val(
                                        response.aroma
                                        || ''
                                    );

                                $('#color')
                                    .val(
                                        response.color_id
                                        || ''
                                    )
                                    .trigger(
                                        'change'
                                    );

                                $('#status_disposition')
                                    .val(
                                        response.status
                                        || ''
                                    );


                                /*
                                 * Load draft Foreman.
                                 */
                                const foremanDraft =
                                    response.foreman_draft
                                    || null;


                                if (foremanDraft) {

                                    $('#foremanDraftAlert')
                                        .removeClass(
                                            'd-none'
                                        );

                                    $('#disposition')
                                        .val(
                                            foremanDraft.disposition
                                            || ''
                                        );

                                    $('#disposition_remark')
                                        .val(
                                            foremanDraft.disposition_remark
                                            || ''
                                        );

                                    $(
                                        'input[name="adjustment_qty_air"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            foremanDraft.adjustment_qty_air
                                        )
                                    );

                                    $(
                                        'input[name="adjustment_qty_garam"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            foremanDraft.adjustment_qty_garam
                                        )
                                    );

                                    $(
                                        'input[name="adjustment_qty_caramel"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            foremanDraft.adjustment_qty_caramel
                                        )
                                    );

                                } else {

                                    $('#disposition')
                                        .val(
                                            response.disposition
                                            || ''
                                        );

                                    $('#disposition_remark')
                                        .val(
                                            response.disposition_remark
                                            || ''
                                        );

                                    $(
                                        'input[name="adjustment_qty_air"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            response.adjustment_qty_air
                                        )
                                    );

                                    $(
                                        'input[name="adjustment_qty_garam"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            response.adjustment_qty_garam
                                        )
                                    );

                                    $(
                                        'input[name="adjustment_qty_caramel"]'
                                    )
                                    .val(
                                        formatDecimal(
                                            response.adjustment_qty_caramel
                                        )
                                    );
                                }


                                toggleAdjustmentFields(
                                    true
                                );

                                updateFinalButtonState();


                                $('#inputBlendingModal')
                                    .modal(
                                        'show'
                                    );
                            },

                        error:
                            function() {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal memuat data.'
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
                            $('#inputForm')
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
                            },

                        success:
                            function(response) {

                                $('#foremanDraftAlert')
                                    .removeClass(
                                        'd-none'
                                    );

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Draft Tersimpan',
                                    text: response.message,
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
                                        || 'Draft gagal disimpan.'
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
        $('#inputForm')
            .submit(
                function(e) {

                    e.preventDefault();

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

                                $('#saveForemanDraft')
                                    .prop(
                                        'disabled',
                                        false
                                    );

                                updateFinalButtonState();
                            },

                        success:
                            function(response) {

                                $('#inputBlendingModal')
                                    .modal(
                                        'hide'
                                    );

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                })
                                .then(
                                    () => {
                                        window.location.reload();
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
                                        icon: 'warning',
                                        title: 'Gagal Disimpan',
                                        text: response.message,
                                    });

                                    return;
                                }

                                if (
                                    xhr.status === 403 &&
                                    response?.message
                                ) {

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Akses Ditolak',
                                        text: response.message,
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


                                    if (
                                        errors.disposition
                                    ) {

                                        $('#disposition')
                                            .addClass(
                                                'is-invalid'
                                            );

                                        $('.errorDisposition')
                                            .html(
                                                errors.disposition.join(
                                                    '<br>'
                                                )
                                            );
                                    }

                                    return;
                                }


                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text:
                                        response?.message
                                        || 'Terjadi kesalahan, silakan coba lagi.',
                                });
                            }

                    });

                }
            );


        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */
        $('body')
            .on(
                'click',
                '#btnDetail',
                function() {

                    const id =
                        $(this).data('id');

                    $.ajax({

                        type: 'GET',

                        url:
                            "{{ route('analisa.blending-awal.edit', '') }}/"
                            + id,

                        dataType:
                            'json',

                        success:
                            function(response) {

                                $('#detail_batch_range')
                                    .text(
                                        response.batch_range
                                        || '-'
                                    );

                                $('#detail_nomor_blending')
                                    .text(
                                        response.nomor_blending
                                        || '-'
                                    );

                                $('#detail_volume')
                                    .text(
                                        response.volume
                                            ? response.volume + ' L'
                                            : '-'
                                    );

                                $('#detail_storage')
                                    .text(
                                        response.storage
                                        || '-'
                                    );

                                $('#detail_revisi')
                                    .text(
                                        response.revisi
                                        || '-'
                                    );

                                $('#detail_brix')
                                    .text(
                                        response.brix
                                        || '-'
                                    );

                                $('#detail_nacl')
                                    .text(
                                        response.nacl
                                        || '-'
                                    );

                                $('#detail_bj')
                                    .text(
                                        response.bj
                                        || '-'
                                    );

                                $('#detail_visco')
                                    .text(
                                        response.visco
                                        || '-'
                                    );

                                $('#detail_aw')
                                    .text(
                                        response.aw
                                        || '-'
                                    );

                                $('#detail_ph')
                                    .text(
                                        response.ph
                                        || '-'
                                    );

                                $('#detail_aroma')
                                    .text(
                                        response.aroma
                                        || '-'
                                    );

                                $('#detail_organo')
                                    .text(
                                        response.organo
                                        || '-'
                                    );

                                $('#detail_color')
                                    .text(
                                        response.color?.name
                                        || '-'
                                    );

                                $('#detail_status')
                                    .text(
                                        response.status
                                        || '-'
                                    );

                                $('#detail_disposition')
                                    .text(
                                        response.disposition
                                        || '-'
                                    );

                                $('#detail_not_standard')
                                    .text(
                                        response.not_standard == 1
                                            ? 'Ya'
                                            : 'Tidak'
                                    );

                                $('#detail_remark')
                                    .text(
                                        response.disposition_remark
                                        || '-'
                                    );

                                $('#detail_created_by')
                                    .text(
                                        response.user?.name
                                        || '-'
                                    );

                                $('#detail_created_at')
                                    .text(
                                        response.created_at
                                            ? formatDateTime(
                                                response.created_at
                                            )
                                            : '-'
                                    );

                                $('#detail_updated_at')
                                    .text(
                                        response.updated_at
                                            ? formatDateTime(
                                                response.updated_at
                                            )
                                            : '-'
                                    );

                                $('#detailModal')
                                    .modal(
                                        'show'
                                    );
                            }

                    });

                }
            );


        /*
        |--------------------------------------------------------------------------
        | FORMULASI
        |--------------------------------------------------------------------------
        */
        $('body')
            .on(
                'click',
                '#btnFormulasi',
                function() {

                    const id =
                        $(this).data('id');

                    $.ajax({

                        type:
                            'GET',

                        url:
                            "{{ route('analisa.blending-awal.formulasi') }}",

                        data: {
                            id: id
                        },

                        dataType:
                            'json',

                        success:
                            function(response) {

                                if (!response.success) {

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Data Tidak Ditemukan',
                                        text: response.message
                                    });

                                    return;
                                }


                                const pb =
                                    response.production_batch
                                    || {};


                                $('#formulasi-po-number')
                                    .text(
                                        pb.po_number
                                        || '-'
                                    );

                                $('#formulasi-variant')
                                    .text(
                                        pb.variant
                                        || '-'
                                    );

                                $('#formulasi-date')
                                    .text(
                                        pb.date
                                        || '-'
                                    );

                                $('#formulasi-batch-range')
                                    .text(
                                        pb.batch_range
                                        || '-'
                                    );


                                const formulasi =
                                    response.formulasi
                                    || [];


                                if (
                                    formulasi.length === 0
                                ) {

                                    $('#formulasiEmptyState')
                                        .removeClass(
                                            'd-none'
                                        );

                                    $('#formulasiTable')
                                        .closest('.card')
                                        .addClass(
                                            'd-none'
                                        );

                                } else {

                                    $('#formulasiEmptyState')
                                        .addClass(
                                            'd-none'
                                        );

                                    $('#formulasiTable')
                                        .closest('.card')
                                        .removeClass(
                                            'd-none'
                                        );


                                    let rows =
                                        '';


                                    formulasi.forEach(
                                        function(item, index) {

                                            rows += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${item.slot_number ?? '-'}</td>
                                                    <td>${item.material_type ?? '-'}</td>
                                                    <td>${item.material_group ?? '-'}</td>
                                                    <td>${item.spb_number ?? '-'}</td>
                                                    <td>${item.jenis_premix ?? '-'}</td>
                                                    <td>${item.quantity ?? '-'}</td>
                                                </tr>
                                            `;
                                        }
                                    );


                                    $('#formulasiTableBody')
                                        .html(
                                            rows
                                        );

                                    $('#formulasi-total')
                                        .text(
                                            formulasi.length
                                        );
                                }


                                $('#formulasiModal')
                                    .modal(
                                        'show'
                                    );
                            },

                        error:
                            function() {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal memuat formulasi.'
                                });
                            }

                    });

                }
            );

    });

</script>
@endsection