@extends('layouts.component.main')
@section('title', 'Pelarutan 2')

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
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Menu</a>
                                </li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">

                    <!-- INFORMASI PRODUCTION BATCH -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">

                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>
                                                    {{ $productionBatch->po_number }} (Nomor PO)
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

                    <!-- TABEL PELARUTAN 2 -->
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
                                                <th>Visco</th>
                                                <th>Organo</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Waktu Scan</th>
                                                <th>Status</th>
                                                <th>Disposisi</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody class="list form-check-all">

                                            @forelse ($productionBatch->pelarutan_2 as $pelarutan_2)

                                                @php
                                                    $dispositionUpper = strtoupper($pelarutan_2->status ?? '');

                                                    $rowClass = match ($dispositionUpper) {
                                                        'NOT OK' => 'table-danger',
                                                        default => '',
                                                    };

                                                    $badgeClass = match ($dispositionUpper) {
                                                        'NOT OK' => 'bg-danger',
                                                        'OK' => 'bg-success',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp

                                                <tr class="{{ $rowClass }}">

                                                    <td>
                                                        {{ $pelarutan_2->batch_number }}

                                                        @if ($pelarutan_2->revisi != null)
                                                            <span
                                                                class="badge bg-secondary ms-1"
                                                                title="Revisi ke-{{ $pelarutan_2->revisi }}">
                                                                Rev. {{ $pelarutan_2->revisi }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div>
                                                            {{ $pelarutan_2->dissolver_number }}
                                                        </div>

                                                        @php
                                                            $batchSuhu = $suhuData[$pelarutan_2->id] ?? null;
                                                        @endphp

                                                        @if ($batchSuhu && !empty($batchSuhu['suhu']))
                                                            <div
                                                                class="text-muted small"
                                                                style="font-size:11px;"
                                                                title="Suhu Pelarutan / Waktu Input">

                                                                Suhu:
                                                                {{ $batchSuhu['suhu'] }} °C

                                                                @if (!empty($batchSuhu['jam_mulai']))
                                                                    <br>
                                                                    ({{ \Carbon\Carbon::parse($batchSuhu['jam_mulai'])->format('d/m/Y H:i') }})
                                                                @endif

                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->brix ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->nacl ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->visco ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->organo ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->user->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->scanned_at
                                                            ? \Carbon\Carbon::parse($pelarutan_2->scanned_at)->format('d/m/Y H:i:s')
                                                            : '-' }}
                                                    </td>

                                                    <td>
                                                        @if ($pelarutan_2->status)

                                                            <span
                                                                class="badge {{ match (strtoupper($pelarutan_2->status)) {
                                                                    'OK' => 'bg-success',
                                                                    'NOT OK' => 'bg-danger',
                                                                    'ADJUSTMENT' => 'bg-warning text-dark',
                                                                    default => 'bg-secondary',
                                                                } }}">
                                                                {{ $pelarutan_2->status }}
                                                            </span>

                                                        @else

                                                            <span class="text-muted">
                                                                -
                                                            </span>

                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $pelarutan_2->disposition ?? '-' }}
                                                    </td>

                                                    <td>
                                                        <button
                                                            class="btn btn-sm btn-info"
                                                            id="btnDetail"
                                                            data-id="{{ $pelarutan_2->id }}">

                                                            <i class="ri-eye-line"></i>
                                                        </button>

                                                        @if (auth()->user()->role == 'Foreman')

                                                            <button
                                                                class="btn btn-sm btn-secondary ms-1"
                                                                id="btnFormulasi"
                                                                data-id="{{ $pelarutan_2->id }}">

                                                                <i class="ri-file-list-line"></i>
                                                            </button>

                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if (is_null($pelarutan_2->status))

                                                            <button
                                                                class="btn btn-sm btn-primary open-pelarutan-2-modal"
                                                                data-id="{{ $pelarutan_2->id }}">

                                                                Input Data
                                                            </button>

                                                        @else

                                                            @if (auth()->user()->role == 'Foreman')

                                                                <button
                                                                    type="button"
                                                                    class="btn btn-sm btn-warning open-pelarutan-2-modal-edit"
                                                                    data-id="{{ $pelarutan_2->id }}">

                                                                    Kelola Data
                                                                </button>

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

                                                <tr>
                                                    <td
                                                        colspan="12"
                                                        class="text-center text-muted py-4">

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


    <!-- ====================================================== -->
    <!-- MODAL INPUT / KELOLA PELARUTAN 2 -->
    <!-- ====================================================== -->
    <div
        class="modal fade"
        id="modal"
        tabindex="-1"
        aria-labelledby="modalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <form id="form">
                @csrf

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Kelola Data Pelarutan 2
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup">
                        </button>

                    </div>

                    <div class="modal-body row g-3">

                        <div class="alert alert-danger d-none error-alert"></div>

                        <!-- INFO DRAFT -->
                        <div
                            class="col-12 d-none"
                            id="draftInfo">

                            <div class="alert alert-warning mb-0">
                                <i class="ri-information-line me-1"></i>

                                Data sementara ditemukan.

                                <strong>
                                    Data sebelumnya sudah dimuat kembali.
                                </strong>
                            </div>

                        </div>

                        <input
                            type="hidden"
                            name="id"
                            id="id">

                        <!-- BRIX -->
                        <div class="col-lg-6">

                            <label class="form-label">
                                BRIX
                                <span style="color:red">*</span>
                            </label>

                            <input
                                type="text"
                                name="brix"
                                id="brix"
                                class="form-control comma-input"
                                placeholder="Contoh: 0,00">

                            <small class="text-danger errorBrix"></small>

                        </div>

                        <!-- NACL -->
                        <div class="col-lg-6">

                            <label class="form-label">
                                NACL
                            </label>

                            <input
                                type="text"
                                name="nacl"
                                id="nacl"
                                class="form-control comma-input"
                                placeholder="Contoh: 0,00">

                            <small class="text-danger errorNacl"></small>

                        </div>

                        <!-- VISCO -->
                        <div class="col-lg-6">

                            <label class="form-label">
                                Visco
                            </label>

                            <input
                                type="text"
                                name="visco"
                                id="visco"
                                class="form-control comma-input"
                                placeholder="Contoh: 0,00">

                            <small class="text-danger errorVisco"></small>

                        </div>

                        <!-- ORGANO -->
                        <div class="col-lg-6">

                            <label class="form-label">
                                Organo
                                <span style="color:red">*</span>
                            </label>

                            <input
                                type="text"
                                name="organo"
                                id="organo"
                                class="form-control"
                                oninput="this.value = this.value.toUpperCase();">

                            <small class="text-danger errorOrgano"></small>

                        </div>

                        <!-- STATUS -->
                        <div class="col-lg-12">

                            <label class="form-label">
                                Status
                                <span style="color:red">*</span>
                            </label>

                            <select
                                name="status_disposition"
                                id="status_disposition"
                                class="form-control disposition-select">

                                <option value="">
                                    -- Pilih Status --
                                </option>

                                <option value="OK">
                                    OK
                                </option>

                                <option value="NOT OK">
                                    NOT OK
                                </option>

                            </select>

                            <small class="text-danger errorStatusDisposition"></small>

                        </div>


                        @if (auth()->user()->role == 'Foreman')

                            <!-- DISPOSISI -->
                            <div class="col-lg-12">

                                <label class="form-label">
                                    Disposisi
                                </label>

                                <select
                                    name="disposition"
                                    id="disposition"
                                    class="form-control disposition-select">

                                    <option value="">
                                        -- Pilih Disposisi --
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

                                    <option value="Reject">
                                        Reject
                                    </option>

                                    <option value="Repro">
                                        Repro
                                    </option>

                                </select>

                                <small class="text-danger errorDisposition"></small>

                            </div>

                        @endif


                        <!-- CATATAN -->
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
                                oninput="this.value = this.value.toUpperCase();"></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                            Tutup
                        </button>


                        @if (auth()->user()->role === 'Analis Kimia')

                            <button
                                type="button"
                                class="btn btn-warning"
                                id="saveDraft">

                                <i class="ri-save-line me-1"></i>

                                Simpan Sementara
                            </button>

                        @endif


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="saveFinal"
                            disabled>

                            Simpan Final
                        </button>

                    </div>

                </div>
            </form>

        </div>
    </div>


    <!-- ====================================================== -->
    <!-- MODAL DETAIL PELARUTAN 2 -->
    <!-- ====================================================== -->
    <div
        class="modal fade"
        id="detailModal"
        tabindex="-1"
        aria-labelledby="detailModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Detail Pelarutan 2
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup">
                    </button>

                </div>

                <div class="modal-body row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Suhu Pelarutan (dari PRD)
                        </label>

                        <input
                            type="text"
                            id="suhu_detail"
                            class="form-control"
                            disabled>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Waktu Input Suhu (dari PRD)
                        </label>

                        <input
                            type="text"
                            id="waktu_input_suhu_detail"
                            class="form-control"
                            disabled>

                    </div>

                    <div class="col-lg-12">

                        <label class="form-label">
                            Catatan
                        </label>

                        <textarea
                            name="disposition_remark_detail"
                            id="disposition_remark_detail"
                            class="form-control"
                            rows="2"
                            disabled></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup
                    </button>

                </div>

            </div>

        </div>
    </div>


    <!-- ====================================================== -->
    <!-- MODAL FORMULASI DISSOLVER -->
    <!-- ====================================================== -->
    <div
        class="modal fade"
        id="formulasiModal"
        tabindex="-1"
        aria-labelledby="formulasiModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="formulasiModalLabel">

                        Detail Formulasi Dissolver
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- PRODUCTION BATCH -->
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

                                    <p
                                        class="mb-2"
                                        id="formulasi-po-number">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        Variant
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-variant">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        Tanggal
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-date">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        Batch Range
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-batch-range">
                                        -
                                    </p>
                                </div>

                            </div>

                        </div>
                    </div>


                    <!-- PELARUTAN 2 -->
                    <div class="card border mb-4">

                        <div class="card-header">
                            <h6 class="mb-0 fw-semibold">
                                Informasi Pelarutan 2
                            </h6>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        Batch Number
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-batch-number">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted d-block">
                                        Dissolver Number
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-dissolver-number">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-2">
                                    <small class="text-muted d-block">
                                        Brix
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-brix">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-2">
                                    <small class="text-muted d-block">
                                        NaCl
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-nacl">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-2">
                                    <small class="text-muted d-block">
                                        Visco
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-visco">
                                        -
                                    </p>
                                </div>

                                <div class="col-md-2">
                                    <small class="text-muted d-block">
                                        Organo
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-organo">
                                        -
                                    </p>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Status
                                    </small>

                                    <p
                                        class="mb-0"
                                        id="formulasi-status">
                                        -
                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Disposition
                                    </small>

                                    <p
                                        class="mb-0"
                                        id="formulasi-disposition">
                                        -
                                    </p>

                                </div>

                            </div>

                        </div>
                    </div>


                    <!-- DISSOLVER INFO -->
                    <div
                        class="card border mb-4 d-none"
                        id="dissolverInfoCard">

                        <div class="card-header">
                            <h6 class="mb-0 fw-semibold">
                                Informasi Dissolver Produksi
                            </h6>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Dissolver Number
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-prod-dissolver">
                                        -
                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Line Pasteurisasi
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-line">
                                        -
                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Jam Mulai Transfer
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-jam-mulai">
                                        -
                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Jam Mulai Transfer
                                    </small>

                                    <p
                                        class="mb-2"
                                        id="formulasi-jam-transfer">
                                        -
                                    </p>

                                </div>

                            </div>

                        </div>
                    </div>


                    <!-- SOURCE INFO -->
                    <div
                        class="alert alert-light border d-none"
                        id="formulasiSourceInfo">

                        <div class="d-flex align-items-center">

                            <div class="flex-shrink-0">
                                <i class="ri-information-line fs-4 text-secondary"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">

                                <small
                                    class="mb-0"
                                    id="formulasi-source-text">
                                    -
                                </small>

                            </div>

                        </div>
                    </div>


                    <!-- FORMULASI TABLE -->
                    <div class="card border">

                        <div class="card-header">
                            <h6 class="mb-0 fw-semibold">
                                Formulasi Dissolver
                            </h6>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered table-sm mb-0"
                                    id="formulasiTable">

                                    <thead class="table-light">
                                        <tr>

                                            <th
                                                class="text-center"
                                                style="width:50px;">
                                                No
                                            </th>

                                            <th style="width:80px;">
                                                Slot
                                            </th>

                                            <th>
                                                Material Type
                                            </th>

                                            <th>
                                                Material Group
                                            </th>

                                            <th>
                                                SPB Number
                                            </th>

                                            <th>
                                                Variant
                                            </th>

                                            <th
                                                class="text-end"
                                                style="width:120px;">
                                                Qty
                                            </th>

                                        </tr>
                                    </thead>

                                    <tbody id="formulasiTableBody">

                                        <tr>
                                            <td
                                                colspan="7"
                                                class="text-center text-muted py-4">

                                                <div
                                                    class="spinner-border spinner-border-sm me-2"
                                                    role="status">

                                                    <span class="visually-hidden">
                                                        Loading...
                                                    </span>

                                                </div>

                                                Memuat data...
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div class="mt-3">

                                <small class="text-muted">

                                    Total Items:

                                    <span
                                        class="fw-semibold"
                                        id="formulasi-total">
                                        0
                                    </span>

                                </small>

                            </div>

                        </div>
                    </div>


                    <!-- EMPTY STATE -->
                    <div
                        class="alert alert-secondary border-secondary d-none"
                        id="formulasiEmptyState">

                        <div class="d-flex align-items-center">

                            <i class="ri-error-warning-line fs-4 me-2"></i>

                            <div>
                                Data formulasi tidak ditemukan untuk batch ini.
                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('scripts')

    <script>

        /*
        |--------------------------------------------------------------------------
        | FORMULASI MODAL
        |--------------------------------------------------------------------------
        */
        function showFormulasiModal(response) {

            const data =
                response;


            resetFormulasiModal();


            if (data.production_batch) {

                $('#formulasi-po-number')
                    .text(
                        data.production_batch.po_number || '-'
                    );

                $('#formulasi-variant')
                    .text(
                        data.production_batch.variant || '-'
                    );

                $('#formulasi-date')
                    .text(
                        data.production_batch.date || '-'
                    );

                $('#formulasi-batch-range')
                    .text(
                        data.production_batch.batch_range || '-'
                    );
            }


            if (data.pelarutan_2_info) {

                const pelarutan_2_info =
                    data.pelarutan_2_info;


                $('#formulasi-batch-number')
                    .text(
                        pelarutan_2_info.batch_number || '-'
                    );

                $('#formulasi-dissolver-number')
                    .text(
                        pelarutan_2_info.dissolver_number || '-'
                    );

                $('#formulasi-brix')
                    .text(
                        pelarutan_2_info.brix || '-'
                    );

                $('#formulasi-nacl')
                    .text(
                        pelarutan_2_info.nacl || '-'
                    );

                $('#formulasi-visco')
                    .text(
                        pelarutan_2_info.visco || '-'
                    );

                $('#formulasi-organo')
                    .text(
                        pelarutan_2_info.organo || '-'
                    );


                if (pelarutan_2_info.status) {

                    const statusClass =
                        pelarutan_2_info.status === 'OK'
                            ? 'success'
                            : pelarutan_2_info.status === 'NOT OK'
                                ? 'danger'
                                : 'warning';


                    $('#formulasi-status').html(
                        `<span class="badge bg-${statusClass}">
                            ${pelarutan_2_info.status}
                        </span>`
                    );

                } else {

                    $('#formulasi-status')
                        .text('-');
                }


                $('#formulasi-disposition')
                    .text(
                        pelarutan_2_info.disposition || '-'
                    );
            }


            if (data.dissolver_info) {

                $('#dissolverInfoCard')
                    .removeClass('d-none');


                $('#formulasi-prod-dissolver')
                    .text(
                        data.dissolver_info.dissolver_number || '-'
                    );

                $('#formulasi-line')
                    .text(
                        data.dissolver_info.line_pasteurisasi || '-'
                    );

                $('#formulasi-jam-mulai')
                    .text(
                        data.dissolver_info.jam_mulai_proses || '-'
                    );

                $('#formulasi-jam-transfer')
                    .text(
                        data.dissolver_info.jam_mulai_transfer || '-'
                    );
            }


            if (
                data.formulasi_source &&
                data.formulasi_source.found
            ) {

                $('#formulasiSourceInfo')
                    .removeClass('d-none');


                const matchType =
                    data.formulasi_source.matched_by_production_batch
                        ? 'Production Batch ID'
                        : 'Batch Number';


                const badgeClass =
                    data.formulasi_source.matched_by_production_batch
                        ? 'success'
                        : 'warning';


                $('#formulasi-source-text').html(
                    `Formulasi ditemukan dari Dissolver ID:
                    <strong>
                        ${data.formulasi_source.dissolver_id}
                    </strong>

                    <span class="badge bg-${badgeClass} ms-2">
                        Matched by ${matchType}
                    </span>`
                );
            }


            if (
                data.formulasi &&
                data.formulasi.length > 0
            ) {

                const sortedFormulasi =
                    sortFormulasi(
                        data.formulasi
                    );


                let tableRows =
                    '';


                sortedFormulasi.forEach(
                    (item, index) => {

                        tableRows += `
                            <tr>

                                <td class="text-center">
                                    ${index + 1}
                                </td>

                                <td>
                                    ${item.slot_number || '-'}
                                </td>

                                <td>
                                    ${item.material_type || '-'}
                                </td>

                                <td>
                                    ${item.material_group || '-'}
                                </td>

                                <td>
                                    ${item.spb_number || '-'}
                                </td>

                                <td>
                                    ${item.variant || '-'}
                                </td>

                                <td class="text-end">
                                    ${
                                        item.quantity !== null &&
                                        item.quantity !== undefined
                                            ? parseFloat(item.quantity).toFixed(2)
                                            : '0.00'
                                    }
                                </td>

                            </tr>
                        `;
                    }
                );


                $('#formulasiTableBody')
                    .html(
                        tableRows
                    );


                $('#formulasi-total')
                    .text(
                        sortedFormulasi.length
                    );


                $('#formulasiEmptyState')
                    .addClass(
                        'd-none'
                    );


                $('#formulasiTable')
                    .closest('.card')
                    .removeClass(
                        'd-none'
                    );

            } else {

                $('#formulasiEmptyState')
                    .removeClass(
                        'd-none'
                    );


                $('#formulasiTable')
                    .closest('.card')
                    .addClass(
                        'd-none'
                    );
            }


            $('#formulasiModal')
                .modal(
                    'show'
                );


            window.formulasiData =
                data;
        }


        /*
        |--------------------------------------------------------------------------
        | RESET FORMULASI MODAL
        |--------------------------------------------------------------------------
        */
        function resetFormulasiModal() {

            $('#formulasi-po-number, #formulasi-variant, #formulasi-date, #formulasi-batch-range')
                .text('-');


            $('#formulasi-batch-number, #formulasi-dissolver-number, #formulasi-brix, #formulasi-nacl, #formulasi-visco, #formulasi-organo')
                .text('-');


            $('#formulasi-status, #formulasi-disposition')
                .text('-');


            $('#formulasi-prod-dissolver, #formulasi-line, #formulasi-jam-mulai, #formulasi-jam-transfer')
                .text('-');


            $('#formulasiTableBody').html(`
                <tr>

                    <td
                        colspan="7"
                        class="text-center text-muted py-4">

                        <div
                            class="spinner-border spinner-border-sm me-2"
                            role="status">

                            <span class="visually-hidden">
                                Loading...
                            </span>

                        </div>

                        Memuat data...
                    </td>

                </tr>
            `);


            $('#formulasi-total')
                .text(
                    '0'
                );


            $('#dissolverInfoCard')
                .addClass(
                    'd-none'
                );


            $('#formulasiSourceInfo')
                .addClass(
                    'd-none'
                );


            $('#formulasiEmptyState')
                .addClass(
                    'd-none'
                );


            $('#formulasiTable')
                .closest('.card')
                .removeClass(
                    'd-none'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SORT FORMULASI
        |--------------------------------------------------------------------------
        */
        function sortFormulasi(formulasi) {

            return formulasi.sort(
                (a, b) => {

                    const materialA =
                        (a.material_type || '')
                            .toUpperCase();


                    const materialB =
                        (b.material_type || '')
                            .toUpperCase();


                    if (
                        materialA !==
                        materialB
                    ) {

                        return materialA.localeCompare(
                            materialB
                        );
                    }


                    if (
                        a.slot_number &&
                        b.slot_number
                    ) {

                        return (
                            parseInt(a.slot_number) -
                            parseInt(b.slot_number)
                        );
                    }


                    return 0;
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT DECIMAL
        |--------------------------------------------------------------------------
        */
        function formatDecimal(
            value,
            forDatabase = false
        ) {

            if (
                value === null ||
                value === undefined ||
                value === ''
            ) {

                return '';
            }


            let stringValue =
                String(value);


            if (forDatabase) {

                return stringValue
                    .replace(/\./g, '')
                    .replace(',', '.');

            } else {

                return stringValue
                    .replace(/\./g, ',');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CEK FIELD WAJIB FINAL
        |--------------------------------------------------------------------------
        |
        | BRIX   = wajib
        | Organo = wajib
        | Status = wajib
        |
        | NACL   = optional
        | Visco  = optional
        |
        */
        function isPelarutan2FinalComplete() {

            const brix =
                String(
                    $('#brix').val() || ''
                ).trim();


            const organo =
                String(
                    $('#organo').val() || ''
                ).trim();


            const status =
                String(
                    $('#status_disposition').val() || ''
                ).trim();


            return (
                brix !== '' &&
                organo !== '' &&
                status !== ''
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE BUTTON FINAL
        |--------------------------------------------------------------------------
        */
        function updatePelarutan2FinalButton() {

            const userRole =
                "{{ auth()->user()->role }}";


            /*
             * Foreman tetap bisa simpan disposisi.
             */
            if (
                userRole ===
                'Foreman'
            ) {

                $('#saveFinal')
                    .prop(
                        'disabled',
                        false
                    );

                return;
            }


            $('#saveFinal')
                .prop(
                    'disabled',
                    !isPelarutan2FinalComplete()
                );
        }


        /*
        |--------------------------------------------------------------------------
        | RESET ERROR
        |--------------------------------------------------------------------------
        */
        function resetPelarutan2Errors() {

            $('.text-danger')
                .html('');


            $('.form-control')
                .removeClass(
                    'is-invalid'
                );


            $('.error-alert')
                .addClass(
                    'd-none'
                )
                .html('');
        }


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN VALIDATION ERROR
        |--------------------------------------------------------------------------
        */
        function showPelarutan2ValidationErrors(errors) {

            if (errors.brix) {

                $('#brix')
                    .addClass(
                        'is-invalid'
                    );


                $('.errorBrix')
                    .html(
                        errors.brix.join('<br>')
                    );
            }


            if (errors.nacl) {

                $('#nacl')
                    .addClass(
                        'is-invalid'
                    );


                $('.errorNacl')
                    .html(
                        errors.nacl.join('<br>')
                    );
            }


            if (errors.visco) {

                $('#visco')
                    .addClass(
                        'is-invalid'
                    );


                $('.errorVisco')
                    .html(
                        errors.visco.join('<br>')
                    );
            }


            if (errors.organo) {

                $('#organo')
                    .addClass(
                        'is-invalid'
                    );


                $('.errorOrgano')
                    .html(
                        errors.organo.join('<br>')
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
                        errors.status_disposition
                            .join('<br>')
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
                        errors.disposition
                            .join('<br>')
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT READY
        |--------------------------------------------------------------------------
        */
        $(document).ready(
            function() {


                /*
                |--------------------------------------------------------------------------
                | AJAX CSRF
                |--------------------------------------------------------------------------
                */
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN':
                            $('meta[name="csrf-token"]')
                                .attr('content')
                    }
                });


                /*
                |--------------------------------------------------------------------------
                | SELECT2
                |--------------------------------------------------------------------------
                */
                $('.select2').select2({
                    placeholder:
                        '-- Pilih Opsi --',

                    dropdownParent:
                        $('#modal')
                });


                /*
                |--------------------------------------------------------------------------
                | FORMAT INPUT DESIMAL
                |--------------------------------------------------------------------------
                */
                document
                    .querySelectorAll(
                        '.comma-input'
                    )
                    .forEach(
                        function(el) {

                            el.addEventListener(
                                'input',
                                function() {

                                    const value =
                                        this.value;


                                    if (
                                        value.includes('.')
                                    ) {

                                        Swal.fire({
                                            icon:
                                                'warning',

                                            title:
                                                'Format Salah!',

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


                                    updatePelarutan2FinalButton();
                                }
                            );
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | LISTENER FIELD FINAL
                |--------------------------------------------------------------------------
                */
                $(document).on(
                    'input change',
                    '#brix, #organo, #status_disposition',
                    function() {

                        updatePelarutan2FinalButton();
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INPUT DATA - LOAD DRAFT
                |--------------------------------------------------------------------------
                */
                $('body').on(
                    'click',
                    '.open-pelarutan-2-modal',
                    function() {


                        const id =
                            $(this)
                                .data(
                                    'id'
                                );


                        $('#form')[0]
                            .reset();


                        resetPelarutan2Errors();


                        $('#draftInfo')
                            .addClass(
                                'd-none'
                            );


                        $('.modal-title')
                            .text(
                                'Kelola Data Pelarutan 2'
                            );


                        $('#id')
                            .val(
                                id
                            );


                        $('#saveFinal')
                            .prop(
                                'disabled',
                                true
                            );


                        $.ajax({

                            type:
                                "GET",

                            url:
                                "{{ route('pelarutan-2.edit', '') }}/"
                                + id,

                            dataType:
                                "json",


                            success:
                                function(response) {


                                    const draft =
                                        response.draft;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | ADA DRAFT
                                    |--------------------------------------------------------------------------
                                    */
                                    if (draft) {

                                        $('#draftInfo')
                                            .removeClass(
                                                'd-none'
                                            );


                                        $('#brix')
                                            .val(
                                                formatDecimal(
                                                    draft.brix
                                                )
                                            );


                                        $('#nacl')
                                            .val(
                                                formatDecimal(
                                                    draft.nacl
                                                )
                                            );


                                        $('#visco')
                                            .val(
                                                formatDecimal(
                                                    draft.visco
                                                )
                                            );


                                        $('#organo')
                                            .val(
                                                draft.organo || ''
                                            );


                                        $('#status_disposition')
                                            .val(
                                                draft.status_disposition || ''
                                            )
                                            .trigger(
                                                'change'
                                            );


                                        $('#disposition_remark')
                                            .val(
                                                draft.disposition_remark || ''
                                            );

                                    /*
                                    |--------------------------------------------------------------------------
                                    | TIDAK ADA DRAFT
                                    |--------------------------------------------------------------------------
                                    */
                                    } else {

                                        $('#draftInfo')
                                            .addClass(
                                                'd-none'
                                            );


                                        $('#brix')
                                            .val('');


                                        $('#nacl')
                                            .val('');


                                        $('#visco')
                                            .val('');


                                        $('#organo')
                                            .val('');


                                        $('#status_disposition')
                                            .val('')
                                            .trigger(
                                                'change'
                                            );


                                        $('#disposition_remark')
                                            .val('');
                                    }


                                    $('#status_disposition')
                                        .prop(
                                            'disabled',
                                            false
                                        );


                                    @if (auth()->user()->role == 'Foreman')

                                        $('#disposition')
                                            .val('')
                                            .trigger(
                                                'change'
                                            );

                                    @endif


                                    updatePelarutan2FinalButton();


                                    $('#modal')
                                        .modal(
                                            'show'
                                        );
                                },


                            error:
                                function(xhr) {


                                    const response =
                                        xhr.responseJSON;


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Gagal',

                                        text:
                                            response?.message
                                            || 'Gagal memuat data Pelarutan 2.'
                                    });
                                }
                        });
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | FOREMAN - KELOLA DATA FINAL
                |--------------------------------------------------------------------------
                */
                $('body').on(
                    'click',
                    '.open-pelarutan-2-modal-edit',
                    function() {


                        const id =
                            $(this)
                                .data(
                                    'id'
                                );


                        $.ajax({

                            type:
                                "GET",

                            url:
                                "{{ route('pelarutan-2.edit', '') }}/"
                                + id,

                            dataType:
                                "json",


                            beforeSend:
                                function() {


                                    $('#form')[0]
                                        .reset();


                                    resetPelarutan2Errors();


                                    $('#draftInfo')
                                        .addClass(
                                            'd-none'
                                        );
                                },


                            success:
                                function(response) {


                                    $('.modal-title')
                                        .text(
                                            'Kelola Data Pelarutan 2'
                                        );


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


                                    $('#visco')
                                        .val(
                                            formatDecimal(
                                                response.visco
                                            )
                                        );


                                    $('#organo')
                                        .val(
                                            response.organo || ''
                                        );


                                    $('#disposition_remark')
                                        .val(
                                            response.disposition_remark || ''
                                        );


                                    $('#status_disposition')
                                        .val(
                                            response.status || ''
                                        )
                                        .trigger(
                                            'change'
                                        );


                                    $('#status_disposition')
                                        .prop(
                                            'disabled',
                                            false
                                        );


                                    @if (auth()->user()->role == 'Foreman')

                                        $('#disposition')
                                            .val(
                                                response.disposition || ''
                                            )
                                            .trigger(
                                                'change'
                                            );

                                    @endif


                                    updatePelarutan2FinalButton();


                                    $('#modal')
                                        .modal(
                                            'show'
                                        );
                                },


                            error:
                                function(xhr) {


                                    const response =
                                        xhr.responseJSON;


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Gagal',

                                        text:
                                            response?.message
                                            || 'Gagal memuat data. Silakan coba lagi.'
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
                $('body').on(
                    'click',
                    '#btnDetail',
                    function() {


                        const id =
                            $(this)
                                .data(
                                    'id'
                                );


                        $.ajax({

                            type:
                                "GET",

                            url:
                                "{{ route('pelarutan-2.edit', '') }}/"
                                + id,

                            dataType:
                                "json",


                            beforeSend:
                                function() {


                                    $('#disposition_remark_detail')
                                        .val('');
                                },


                            success:
                                function(response) {


                                    let remarkText =
                                        '-';


                                    if (
                                        response.disposition_remark != null &&
                                        response.disposition_remark != '-'
                                    ) {

                                        remarkText =
                                            response.disposition_remark;
                                    }


                                    $('#suhu_detail')
                                        .val(
                                            response.suhu
                                                ? response.suhu + ' °C'
                                                : '-'
                                        );


                                    let formatJamMulai =
                                        '-';


                                    if (
                                        response.jam_mulai
                                    ) {


                                        const date =
                                            new Date(
                                                response.jam_mulai
                                            );


                                        const day =
                                            String(
                                                date.getDate()
                                            ).padStart(
                                                2,
                                                '0'
                                            );


                                        const month =
                                            String(
                                                date.getMonth() + 1
                                            ).padStart(
                                                2,
                                                '0'
                                            );


                                        const year =
                                            date.getFullYear();


                                        const hours =
                                            String(
                                                date.getHours()
                                            ).padStart(
                                                2,
                                                '0'
                                            );


                                        const minutes =
                                            String(
                                                date.getMinutes()
                                            ).padStart(
                                                2,
                                                '0'
                                            );


                                        formatJamMulai =
                                            `${day}/${month}/${year} ${hours}:${minutes}`;
                                    }


                                    $('#waktu_input_suhu_detail')
                                        .val(
                                            formatJamMulai
                                        );


                                    $('#disposition_remark_detail')
                                        .val(
                                            remarkText
                                        );


                                    $('#detailModal')
                                        .modal(
                                            'show'
                                        );
                                },


                            error:
                                function(xhr) {


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Gagal',

                                        text:
                                            'Gagal memuat data. Silakan coba lagi.'
                                    });
                                }
                        });
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | FORMULASI
                |--------------------------------------------------------------------------
                */
                $('body').on(
                    'click',
                    '#btnFormulasi',
                    function() {


                        const id =
                            $(this)
                                .data(
                                    'id'
                                );


                        $('#formulasiModal')
                            .modal(
                                'show'
                            );


                        $.ajax({

                            type:
                                "GET",

                            url:
                                "{{ route('pelarutan-2.formulasi') }}",

                            data: {
                                id:
                                    id
                            },

                            dataType:
                                "json",


                            success:
                                function(response) {


                                    if (
                                        response.success
                                    ) {

                                        showFormulasiModal(
                                            response
                                        );

                                    } else {


                                        $('#formulasiModal')
                                            .modal(
                                                'hide'
                                            );


                                        Swal.fire({
                                            icon:
                                                'warning',

                                            title:
                                                'Data Tidak Ditemukan',

                                            text:
                                                response.message
                                                || 'Data formulasi tidak ditemukan untuk batch ini'
                                        });
                                    }
                                },


                            error:
                                function(xhr) {


                                    const response =
                                        xhr.responseJSON;


                                    $('#formulasiModal')
                                        .modal(
                                            'hide'
                                        );


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Gagal',

                                        text:
                                            response?.message
                                            || 'Gagal memuat data. Silakan coba lagi.'
                                    });
                                }
                        });
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SIMPAN SEMENTARA
                |--------------------------------------------------------------------------
                */
                $('body').on(
                    'click',
                    '#saveDraft',
                    function() {


                        resetPelarutan2Errors();


                        $.ajax({

                            data:
                                $('#form')
                                    .serialize(),

                            url:
                                "{{ route('pelarutan-2.draft.store') }}",

                            type:
                                "POST",

                            dataType:
                                "json",


                            beforeSend:
                                function() {


                                    $('#saveDraft')
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


                                    $('#saveDraft')
                                        .prop(
                                            'disabled',
                                            false
                                        )
                                        .html(
                                            '<i class="ri-save-line me-1"></i> Simpan Sementara'
                                        );


                                    updatePelarutan2FinalButton();
                                },


                            success:
                                function(response) {


                                    $('#draftInfo')
                                        .removeClass(
                                            'd-none'
                                        );


                                    Swal.fire({
                                        icon:
                                            'success',

                                        title:
                                            'Tersimpan',

                                        text:
                                            response.message
                                    });
                                },


                            error:
                                function(xhr) {


                                    const response =
                                        xhr.responseJSON;


                                    if (
                                        xhr.status === 422 &&
                                        response &&
                                        response.errors
                                    ) {


                                        showPelarutan2ValidationErrors(
                                            response.errors
                                        );


                                        return;
                                    }


                                    if (
                                        xhr.status === 403 &&
                                        response &&
                                        response.message
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
                                        xhr.status === 409 &&
                                        response &&
                                        response.message
                                    ) {


                                        Swal.fire({
                                            icon:
                                                'warning',

                                            title:
                                                'Tidak Dapat Disimpan',

                                            text:
                                                response.message
                                        });


                                        return;
                                    }


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Gagal',

                                        text:
                                            response?.message
                                            || 'Data sementara gagal disimpan.'
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
                $('#form').submit(
                    function(e) {


                        e.preventDefault();


                        resetPelarutan2Errors();


                        const userRole =
                            "{{ auth()->user()->role }}";


                        /*
                        |--------------------------------------------------------------------------
                        | CEK FIELD WAJIB ANALIS
                        |--------------------------------------------------------------------------
                        */
                        if (
                            userRole === 'Analis Kimia' &&
                            !isPelarutan2FinalComplete()
                        ) {


                            Swal.fire({
                                icon:
                                    'warning',

                                title:
                                    'Data Belum Lengkap',

                                text:
                                    'BRIX, Organo, dan Status wajib diisi sebelum Simpan Final.'
                            });


                            updatePelarutan2FinalButton();


                            return;
                        }


                        $.ajax({

                            data:
                                $(this)
                                    .serialize(),

                            url:
                                "{{ route('pelarutan-2.update') }}",

                            type:
                                "POST",

                            dataType:
                                "json",


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
                                },


                            complete:
                                function() {


                                    $('#saveFinal')
                                        .text(
                                            'Simpan Final'
                                        );


                                    updatePelarutan2FinalButton();
                                },


                            success:
                                function(response) {


                                    $('#modal')
                                        .modal(
                                            'hide'
                                        );


                                    $('#form')
                                        .trigger(
                                            'reset'
                                        );


                                    $('#draftInfo')
                                        .addClass(
                                            'd-none'
                                        );


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


                                            window.location
                                                .reload();
                                        }
                                    );
                                },


                            error:
                                function(xhr) {


                                    const response =
                                        xhr.responseJSON;


                                    if (
                                        xhr.status === 409 &&
                                        response &&
                                        response.message
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
                                        response &&
                                        response.message
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
                                        response &&
                                        response.errors
                                    ) {


                                        showPelarutan2ValidationErrors(
                                            response.errors
                                        );


                                        return;
                                    }


                                    Swal.fire({
                                        icon:
                                            'error',

                                        title:
                                            'Kesalahan',

                                        text:
                                            response?.error
                                            || response?.message
                                            || 'Terjadi kesalahan, silakan coba lagi.'
                                    });
                                }
                        });
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INITIAL BUTTON STATE
                |--------------------------------------------------------------------------
                */
                updatePelarutan2FinalButton();

            }
        );

    </script>

@endsection