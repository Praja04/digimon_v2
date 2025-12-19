@extends('layouts.component.main')
@section('title', 'Analisa Blending Awal')
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
                                <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.index') }}">Analisa
                                        Blending Awal</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
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
                                                <h4>{{ $productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
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
                            <!--end card-body-->
                            <div class="card-body">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
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
                                                    // Tentukan class berdasarkan disposition
                                                    $dispositionUpper = strtoupper($blending->status ?? '');
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
                                                        {{ $productionBatch->po_number }}
                                                    </td>
                                                    <td>
                                                        @if ($blending->revisi != null)
                                                            {{ $blending->batch_range }} ❗
                                                        @else
                                                            {{ $blending->batch_range }}
                                                        @endif

                                                        @if ($blending->additional_batch_info)
                                                            @foreach ($blending->additional_batch_info as $relasi)
                                                                <span class="badge bg-info">{{ $relasi->batch }}</span>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ $blending->nomor_blending }}</td>
                                                    <td>{{ $blending->volume }}</td>
                                                    <td>{{ $blending->scanned_at ? \Carbon\Carbon::parse($blending->scanned_at)->format('d/m/Y H:i:s') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($blending->status)
                                                            <span
                                                                class="badge {{ match (strtoupper($blending->status)) {
                                                                    'OK' => 'bg-success',
                                                                    'NOT OK' => 'bg-danger',
                                                                    'ADJUSTMENT' => 'bg-warning text-dark',
                                                                    default => 'bg-secondary',
                                                                } }}">
                                                                {{ $blending->status }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $blending->disposition ?? '-' }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" id="btnDetail"
                                                            data-id="{{ $blending->id }}">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                        @if (auth()->user()->role == 'Foreman')
                                                            <button class="btn btn-sm btn-secondary ms-1" id="btnFormulasi"
                                                                data-id="{{ $blending->id }}">
                                                                <i class="ri-file-list-line"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (is_null($blending->status))
                                                            <button class="btn btn-sm btn-primary open-blending-modal"
                                                                data-id="{{ $blending->id }}">
                                                                Input Data
                                                            </button>
                                                        @else
                                                            @if (auth()->user()->role == 'Foreman')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning open-blending-modal-edit"
                                                                    data-id="{{ $blending->id }}">
                                                                    Kelola Data
                                                                </button>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line align-middle"></i> Lengkap
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="text-center">
                                                    <td colspan="9">Tidak ada data tersedia.</td>
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

    <!-- Modal input-->
    <div class="modal fade" id="inputBlendingModal" tabindex="-1" aria-labelledby="inputBlendingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="inputForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Data Blending Awal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="alert alert-danger d-none error-alert"></div>
                        <input type="hidden" name="id" id="id">
                        <div class="col-lg-4">
                            <label class="form-label">BRIX <span style="color: red">*</span></label>
                            <input type="text" name="brix" id="brix" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBrix"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">NACL <span style="color: red">*</span></label>
                            <input type="text" name="nacl" id="nacl" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorNacl"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Bj <span style="color: red">*</span></label>
                            <input type="text" name="bj" id="bj" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBj"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Visco <span style="color: red">*</span></label>
                            <input type="text" name="visco" id="visco" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorVisco"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Aw <span style="color: red">*</span></label>
                            <input type="text" name="aw" id="aw" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorAw"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">pH</label>
                            <input type="text" name="ph" id="ph" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorPh"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Buih</label>
                            <input type="text" name="buih" id="buih" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBuih"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Organo <span style="color: red">*</span></label>
                            <input type="text" name="organo" id="organo" class="form-control"
                                oninput="this.value = this.value.toUpperCase();">
                            <small class="text-danger errorOrgano"></small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Endapan</label>
                            <input type="text" name="endapan" id="endapan" class="form-control"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Warna <span style="color: red">*</span></label>
                            <select name="color" id="color" class="select2 form-control">
                                <option value="">-- Pilih Warna --</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}">
                                        {{ $color->name }} ({{ $color->code }})
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
                                <label class="form-label">Disposition</label>
                                <select name="disposition" id="disposition" class="form-control disposition-select">
                                    <option value="">-- Pilih Disposition --</option>
                                    <option value="Release">Release</option>
                                    <option value="Release Bersyarat">Release Bersyarat</option>
                                    <option value="Resampling">Resampling</option>
                                    <option value="Adjustment">Adjustment</option>
                                    <option value="Reject">Reject</option>
                                    <option value="Repro">Repro</option>
                                    <option value="Jalan Bareng">Jalan Bareng</option>
                                    <option value="Leveling">Leveling</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-lg-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="disposition_remark" id="disposition_remark" class="form-control" rows="2"
                                placeholder="Isi catatan jika diperlukan..." oninput="this.value = this.value.toUpperCase();"></textarea>
                        </div>

                        <div class="mb-3 d-none adjustment-qty-wrapper">
                            <h6 class="form-label fw-bold">Adjustment Qty</h6>
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Air (Liter)</label>
                                    <input type="text" name="adjustment_qty_air"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Caramel (Kg)</label>
                                    <input type="text" name="adjustment_qty_caramel"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Garam (Kg)</label>
                                    <input type="text" name="adjustment_qty_garam"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
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

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="detailModalLabel">Detail Data Monitoring Turun Blending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Informasi Produksi -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Produksi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Batch Range</span>
                                    <span class="fw-medium">: <span id="detail_batch_range">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Nomor Blending</span>
                                    <span class="fw-medium">: <span id="detail_nomor_blending">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Volume</span>
                                    <span class="fw-medium">: <span id="detail_volume">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Storage</span>
                                    <span class="fw-medium">: <span id="detail_storage">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Revisi</span>
                                    <span class="fw-medium">: <span id="detail_revisi">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Analisa -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Parameter Analisa</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Brix</span>
                                    <span class="fw-medium">: <span id="detail_brix">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">NaCl</span>
                                    <span class="fw-medium">: <span id="detail_nacl">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">BJ</span>
                                    <span class="fw-medium">: <span id="detail_bj">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Visco</span>
                                    <span class="fw-medium">: <span id="detail_visco">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">AW</span>
                                    <span class="fw-medium">: <span id="detail_aw">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">pH</span>
                                    <span class="fw-medium">: <span id="detail_ph">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Fisik -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Parameter Fisik</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Buih</span>
                                    <span class="fw-medium">: <span id="detail_buih">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Organo</span>
                                    <span class="fw-medium">: <span id="detail_organo">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Endapan</span>
                                    <span class="fw-medium">: <span id="detail_endapan">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Warna</span>
                                    <span class="fw-medium">: <span id="detail_color">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Disposisi -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Status & Disposisi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Status</span>
                                    <span class="fw-medium">: <span id="detail_status">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Disposisi</span>
                                    <span class="fw-medium">: <span id="detail_disposition">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Not Standard</span>
                                    <span class="fw-medium">: <span id="detail_not_standard">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan/Catatan -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Keterangan</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0" id="detail_remark" style="white-space: pre-wrap;">-</p>
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div>
                        <h6 class="border-bottom pb-2 mb-3">Informasi Tambahan</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Dibuat Oleh</span>
                                    <span class="fw-medium">: <span id="detail_created_by">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Tanggal Dibuat</span>
                                    <span class="fw-medium">: <span id="detail_created_at">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Terakhir Diupdate</span>
                                    <span class="fw-medium">: <span id="detail_updated_at">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Formulasi Dissolver -->
    <div class="modal fade" id="formulasiModal" tabindex="-1" aria-labelledby="formulasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formulasiModalLabel">Detail Formulasi Dissolver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="card border mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-semibold">Informasi Production Batch</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">PO Number</small>
                                    <p class="mb-2" id="formulasi-po-number">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Variant</small>
                                    <p class="mb-2" id="formulasi-variant">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Tanggal</small>
                                    <p class="mb-2" id="formulasi-date">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Batch Range</small>
                                    <p class="mb-2" id="formulasi-batch-range">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border d-none" id="formulasiSourceInfo">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line fs-4 text-secondary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="mb-0" id="formulasi-source-text">-</small>
                            </div>
                        </div>
                    </div>

                    <div class="card border">
                        <div class="card-header">
                            <h6 class="mb-0 fw-semibold">Formulasi Blending Awal</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" id="formulasiTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th style="width: 80px;">Slot</th>
                                            <th>Material Type</th>
                                            <th>Material Group</th>
                                            <th>SPB Number</th>
                                            <th>Jenis Premix</th>
                                            <th class="text-end" style="width: 120px;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="formulasiTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                Memuat data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Total Items: <span class="fw-semibold"
                                        id="formulasi-total">0</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary border-secondary d-none" id="formulasiEmptyState">
                        <div class="d-flex align-items-center">
                            <i class="ri-error-warning-line fs-4 me-2"></i>
                            <div>Data formulasi tidak ditemukan untuk batch ini.</div>
                        </div>
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
        function showFormulasiModal(response) {
            const data = response;

            // Reset semua field terlebih dahulu
            resetFormulasiModal();

            // Production Batch Info
            if (data.production_batch) {
                $('#formulasi-po-number').text(data.production_batch.po_number || '-');
                $('#formulasi-variant').text(data.production_batch.variant || '-');
                $('#formulasi-date').text(data.production_batch.date || '-');
                $('#formulasi-batch-range').text(data.production_batch.batch_range || '-');
            }

            // Formulasi Source Info (jika ada)
            if (data.formulasi_source && data.formulasi_source.found) {
                $('#formulasiSourceInfo').removeClass('d-none');
                const matchType = data.formulasi_source.matched_by_production_batch ?
                    'Production Batch ID' : 'Batch Number';
                const badgeClass = data.formulasi_source.matched_by_production_batch ?
                    'success' : 'warning';
                $('#formulasi-source-text').html(
                    `Formulasi ditemukan dari Blending Awal ID: <strong>${data.formulasi_source.blending_awal_id}</strong> ` +
                    `<span class="badge bg-${badgeClass} ms-2">Matched by ${matchType}</span>`
                );
            }

            // Tabel Formulasi
            if (data.formulasi && data.formulasi.length > 0) {
                let sortedFormulasi = sortFormulasi(data.formulasi);
                let tableRows = '';
                data.formulasi.forEach((item, index) => {
                    tableRows += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${item.slot_number}</td>
                        <td>${item.material_type || '-'}</td>
                        <td>${item.material_group || '-'}</td>
                        <td>${item.spb_number || '-'}</td>
                        <td>${item.jenis_premix || '-'}</td>
                        <td class="text-end">${parseFloat(item.quantity).toFixed(2)}</td>
                    </tr>
                `;
                });

                $('#formulasiTableBody').html(tableRows);
                $('#formulasi-total').text(data.formulasi.length);

                // Sembunyikan empty state
                $('#formulasiEmptyState').addClass('d-none');
                $('#formulasiTable').closest('.card').removeClass('d-none');
            } else {
                // Tampilkan empty state
                $('#formulasiEmptyState').removeClass('d-none');
                $('#formulasiTable').closest('.card').addClass('d-none');
            }

            // Tampilkan modal
            $('#formulasiModal').modal('show');

            // Simpan data untuk print
            window.formulasiData = data;
        }

        function resetFormulasiModal() {
            $('#formulasi-po-number, #formulasi-variant, #formulasi-date, #formulasi-batch-range').text('-');
            $('#formulasi-batch-number, #formulasi-dissolver-number, #formulasi-brix, #formulasi-nacl, #formulasi-organo')
                .text('-');
            $('#formulasi-status, #formulasi-disposition').text('-');
            $('#formulasi-prod-dissolver, #formulasi-line, #formulasi-jam-mulai, #formulasi-jam-transfer').text('-');

            // Reset table
            $('#formulasiTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Memuat data...
                    </td>
                </tr>
            `);
            $('#formulasi-total').text('0');

            // Hide optional sections
            $('#dissolverInfoCard').addClass('d-none');
            $('#formulasiSourceInfo').addClass('d-none');
            $('#formulasiEmptyState').addClass('d-none');
            $('#formulasiTable').closest('.card').removeClass('d-none');
        }

        function sortFormulasi(formulasi) {
            return formulasi.sort((a, b) => {
                const materialA = (a.material_type || '').toUpperCase();
                const materialB = (b.material_type || '').toUpperCase();

                if (materialA !== materialB) {
                    return materialA.localeCompare(materialB);
                }

                if (a.slot_number && b.slot_number) {
                    // Pastikan perbandingan dilakukan sebagai angka
                    return parseInt(a.slot_number) - parseInt(b.slot_number);
                }

                return 0;
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                dropdownParent: $('#inputBlendingModal')
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

            $('.open-blending-modal').on('click', function() {
                const id = $(this).data('id');

                $('#inputForm')[0].reset();
                $('.text-danger').html('');
                $('.form-control').removeClass('is-invalid');

                $('#id').val(id);

                $('#color').val('').trigger('change');
                $('#disposition').val('').trigger('change');
                $('#status_disposition').prop('disabled', false);
                $('#status_disposition').val('').trigger('change');

                $('.adjustment-qty-wrapper').addClass('d-none');
                $('.adjustment-qty').prop('required', false).val('');

                $('#inputBlendingModal').modal('show');
            });

            $('body').on('click', '.open-blending-modal-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('#inputForm')[0].reset();
                        $('.text-danger').html('');
                        $('.form-control').removeClass('is-invalid');
                    },
                    success: function(response) {
                        const userRole = "{{ auth()->user()->role }}";

                        $('#id').val(response.id);
                        $('#brix').val(response.brix);
                        $('#nacl').val(response.nacl);
                        $('#bj').val(response.bj);
                        $('#visco').val(response.visco);
                        $('#aw').val(response.aw);
                        $('#ph').val(response.ph);
                        $('#buih').val(response.buih);
                        $('#organo').val(response.organo);
                        $('#endapan').val(response.endapan);
                        $('#color').val(response.color_id).trigger('change');
                        $('#disposition_remark').val(response.disposition_remark || '');

                        $('#status_disposition').val(response.status);
                        if (userRole === 'Foreman') {
                            $('#status_disposition').val(response.status);
                            $('#status_disposition').prop('disabled', true);
                        } else {
                            $('#status_disposition').val(response.status);
                            $('#status_disposition').prop('disabled', false);
                        }
                        $('#disposition').val(response.disposition || '');

                        if (response.status === 'Adjustment') {
                            $('.adjustment-qty-wrapper').removeClass('d-none');
                            $('input[name="adjustment_qty_air"]').val(response
                                .adjustment_qty_air || '');
                            $('input[name="adjustment_qty_caramel"]').val(response
                                .adjustment_qty_caramel || '');
                            $('input[name="adjustment_qty_garam"]').val(response
                                .adjustment_qty_garam || '');

                            $('.adjustment-qty').prop('required', true);
                        } else {
                            $('.adjustment-qty-wrapper').addClass('d-none');
                            $('.adjustment-qty').prop('required', false).val('');
                        }

                        $('#inputBlendingModal').modal('show');
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

            $('body').on('click', '#btnDetail', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        // Reset semua field
                        $('#detail_batch_range, #detail_nomor_blending, #detail_volume, #detail_storage, #detail_revisi')
                            .text('-');
                        $('#detail_brix, #detail_nacl, #detail_bj, #detail_visco, #detail_aw, #detail_ph')
                            .text('-');
                        $('#detail_buih, #detail_organo, #detail_endapan, #detail_color').text(
                            '-');
                        $('#detail_status, #detail_disposition, #detail_not_standard, #detail_remark')
                            .text('-');
                        $('#detail_created_by, #detail_created_at, #detail_updated_at').text(
                            '-');
                    },
                    success: function(response) {
                        // Informasi Produksi
                        $('#detail_batch_range').text(response.batch_range || '-');
                        $('#detail_nomor_blending').text(response.nomor_blending || '-');
                        $('#detail_volume').text(response.volume ? response.volume + ' L' :
                            '-');
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_revisi').text(response.revisi || '-');

                        // Parameter Analisa
                        $('#detail_brix').text(response.brix || '-');
                        $('#detail_nacl').text(response.nacl || '-');
                        $('#detail_bj').text(response.bj || '-');
                        $('#detail_visco').text(response.visco || '-');
                        $('#detail_aw').text(response.aw || '-');
                        $('#detail_ph').text(response.ph || '-');

                        // Parameter Fisik
                        $('#detail_buih').text(response.buih || '-');
                        $('#detail_organo').text(response.organo || '-');
                        $('#detail_endapan').text(response.endapan || '-');

                        // Safe access untuk color - ini yang penting!
                        let colorText = '-';
                        if (response.color && response.color.name) {
                            colorText = response.color.name;
                        } else if (response.color && response.color.code) {
                            colorText = response.color.code;
                        } else if (response.color_id) {
                            colorText = 'ID: ' + response.color_id;
                        }
                        $('#detail_color').text(colorText);

                        // Status & Disposisi
                        $('#detail_status').text(response.status || '-');
                        $('#detail_disposition').text(response.disposition || '-');
                        $('#detail_not_standard').text(response.not_standard == 1 ? 'Ya' :
                            'Tidak');

                        // Keterangan
                        let remarkText = '-';
                        if (response.disposition_remark &&
                            response.disposition_remark != '-' &&
                            response.disposition != 'Adjustment') {
                            remarkText = response.disposition_remark;
                        } else if (response.disposition == 'Adjustment') {
                            remarkText =
                                `Adjustment:\n• Air: ${response.adjustment_qty_air || 0} Liter\n• Garam: ${response.adjustment_qty_garam || 0} Kg\n• Caramel: ${response.adjustment_qty_caramel || 0} Kg`;
                        } else if (response.is_adjustment == true) {
                            remarkText = 'Adjustment';
                        }
                        $('#detail_remark').text(remarkText);

                        // Informasi Tambahan - Safe access untuk user
                        let createdByText = '-';
                        if (response.user && response.user.name) {
                            createdByText = response.user.name;
                        } else if (response.user && response.user.username) {
                            createdByText = response.user.username;
                        } else if (response.created_by) {
                            createdByText = 'ID: ' + response.created_by;
                        }
                        $('#detail_created_by').text(createdByText);

                        $('#detail_created_at').text(response.created_at ? formatDateTime(
                            response.created_at) : '-');
                        $('#detail_updated_at').text(response.updated_at ? formatDateTime(
                            response.updated_at) : '-');

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

            $('body').on('click', '#btnFormulasi', function() {
                const id = $(this).data('id');

                $('#formulasiModal').modal('show');

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal.formulasi') }}",
                    data: {
                        id: id,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            showFormulasiModal(response);
                        } else {
                            $('#formulasiModal').modal('hide');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Tidak Ditemukan',
                                text: response.message ||
                                    'Data formulasi tidak ditemukan untuk batch ini',
                            });
                        }
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

            // Helper function untuk format tanggal
            function formatDateTime(dateString) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            $('#inputForm').submit(function(e) {
                e.preventDefault();

                const wasDisabled = $('#status_disposition').prop('disabled');
                if (wasDisabled) {
                    $('#status_disposition').prop('disabled', false);
                }

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('analisa.blending-awal.update') }}",
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
                        $('#inputBlendingModal').modal('hide');
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
                            if (errors.bj) {
                                $('#bj').addClass('is-invalid');
                                $('.errorBj').html(errors.bj.join('<br>'));
                            }
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
                            }
                            if (errors.organo) {
                                $('#organo').addClass('is-invalid');
                                $('.errorOrgano').html(errors.organo.join('<br>'));
                            }
                            if (errors.buih) {
                                $('#buih').addClass('is-invalid');
                                $('.errorBuih').html(errors.buih.join('<br>'));
                            }
                            if (errors.ph) {
                                $('#ph').addClass('is-invalid');
                                $('.errorPh').html(errors.ph.join('<br>'));
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
                            if (errors.adjustment_qty_caramel) {
                                $('input[name="adjustment_qty_caramel"]').addClass(
                                    'is-invalid');
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
