@extends('layouts.component.main')
@section('title', 'Form Analisa')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Form Analisa</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.index') }}">RMPM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.show', $identitas->id) }}">Detail</a></li>
                            <li class="breadcrumb-item active">Analisa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">

                    {{-- Card Header --}}
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0 fw-semibold text-dark">Input Analisa Bahan Baku</h5>
                            <p class="mb-0 small text-muted d-flex flex-wrap gap-3">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ri-price-tag-3-line me-1"></i>
                                    <strong>Jenis:</strong>&nbsp;{{ $identitas->jenis }}
                                </span>
                                <span class="d-inline-flex align-items-center">
                                    <i class="ri-file-list-3-line me-1"></i>
                                    <strong>SPB:</strong>&nbsp;{{ $identitas->no_spb }}
                                </span>
                                <span class="d-inline-flex align-items-center">
                                    <i class="ri-building-line me-1"></i>
                                    <strong>Supplier:</strong>&nbsp;{{ $identitas->supplier }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- STEP 1: Setup --}}
                        <div id="setupSection">
                            <div class="alert alert-info border-info" role="alert">
                                <strong>Info:</strong> Tentukan jenis analisa dan jumlah sampel terlebih dahulu,
                                kemudian isi data analisa per field atau gunakan fitur copy-paste dari Excel.
                            </div>

                            <div class="row g-3 align-items-end">
                                @if (in_array($identitas->jenis, ['Gula Tebu', 'Gula Kelapa']))
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Pilih Kategori / Jenis Analisa</label>
                                    <div class="row g-2">
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katIncoming" value="incoming" checked>
                                            <label class="btn btn-outline-primary w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katIncoming">
                                                <i class="ri-inbox-archive-line fs-5 mb-1"></i>
                                                <span class="fw-bold">1. Incoming</span>
                                                <small class="text-muted" style="font-size:11px;">Wajib Lengkap</small>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katSta" value="sta">
                                            <label class="btn btn-outline-info w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katSta">
                                                <i class="ri-flashlight-line fs-5 mb-1"></i>
                                                <span class="fw-bold">2. STA</span>
                                                <small class="text-muted" style="font-size:11px;">Fleksibel / Parsial</small>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katMonitoring" value="monitoring">
                                            <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katMonitoring">
                                                <i class="ri-line-chart-line fs-5 mb-1"></i>
                                                <span class="fw-bold">3. Monitoring</span>
                                                <small class="text-muted" style="font-size:11px;">Pemantauan Berkala</small>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katLongTerm" value="long-term">
                                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katLongTerm">
                                                <i class="ri-microscope-line fs-5 mb-1"></i>
                                                <span class="fw-bold">4. Long Term</span>
                                                <small class="text-muted" style="font-size:11px;">Uji Kristal</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6" id="jumlahWrapper">
                                    <label class="form-label fw-semibold">Jumlah Sampel</label>
                                    <input type="number" class="form-control" id="jumlahData" min="1" max="50" placeholder="Jumlah Sampel" value="1">
                                </div>

                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100" id="btnMulai">
                                        <i class="ri-play-line me-1"></i> Mulai Input
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('rmpm.show', $identitas->id) }}" class="btn btn-light w-100">Kembali</a>
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <hr id="dividerForm" style="display:none;" class="my-4">

                        {{-- STEP 2: Form --}}
                        <div id="analisaSection" style="display:none;">

                            {{-- Sub-header --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-primary fs-6" id="labelAnalisaType"></span>
                                    <div class="d-flex align-items-center gap-1" id="jumlahSampelBadgeWrapper">
                                        <span class="badge bg-secondary fs-6" id="labelJumlahSampel"></span>
                                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnEditJumlah" title="Edit jumlah sampel" style="font-size:12px;line-height:1.8;">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </div>
                                    <span id="draftStatusBadge" class="badge bg-warning text-dark fs-6" style="display:none;">
                                        <i class="mdi mdi-file-document-edit-outline me-1"></i> Status: Tersimpan Sementara (Draft)
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" id="btnReset">
                                    <i class="ri-refresh-line me-1"></i> Reset
                                </button>
                            </div>

                            {{-- Edit jumlah inline --}}
                            <div id="editJumlahWrapper" class="mb-3 p-3 bg-light rounded border" style="display:none;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <label class="form-label mb-0 fw-semibold">Ubah Jumlah Sampel:</label>
                                    <input type="number" class="form-control form-control-sm" id="editJumlahInput" min="1" max="50" style="max-width:100px;">
                                    <button class="btn btn-sm btn-primary" id="btnApplyJumlah">Terapkan</button>
                                    <button class="btn btn-sm btn-light" id="btnCancelJumlah">Batal</button>
                                </div>
                                <div class="form-text mt-1">
                                    <strong>
                                        <i class="ri-alert-line me-1"></i> Data yang sudah diisi akan tetap tersimpan pada slot yang masih ada.
                                    </strong>
                                </div>
                            </div>

                            {{-- Summary Statistics Bar (Average & Organo Percentage) for Short-Term / Garam-Gula --}}
                            <div id="summaryStatsBar" class="alert alert-light border shadow-sm p-3 mb-3" style="display:none;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold p-2">
                                            <i class="ri-calculator-line me-1 fs-6"></i> RINGKASAN DATA
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-4">
                                        <div class="stat-box">
                                            <span class="text-muted small d-block">Avg BRIX</span>
                                            <strong class="text-primary fs-6" id="statAvgBrix">-</strong>
                                        </div>
                                        <div class="stat-box">
                                            <span class="text-muted small d-block">Avg pH</span>
                                            <strong class="text-primary fs-6" id="statAvgPh">-</strong>
                                        </div>
                                        <div class="stat-box">
                                            <span class="text-muted small d-block">Avg Kotoran</span>
                                            <strong class="text-primary fs-6" id="statAvgKotoran">-</strong>
                                        </div>
                                        <div class="stat-box">
                                            <span class="text-muted small d-block">Avg KA (%)</span>
                                            <strong class="text-primary fs-6" id="statAvgKa">-</strong>
                                        </div>
                                        <div class="stat-box border-start ps-3" style="min-width: 200px;">
                                            <span class="text-muted small d-block mb-1">Persentase Organo:</span>
                                            <div id="statOrganoPercent" class="d-flex flex-wrap gap-1 align-items-center">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form id="formAnalisa" enctype="multipart/form-data" novalidate>
                                @csrf
                                <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                                <input type="hidden" name="jenis" value="{{ $identitas->jenis }}">
                                <input type="hidden" name="analisa_type" id="hiddenAnalisaType">
                                <input type="hidden" name="kategori" id="hiddenKategori" value="incoming">
                                <input type="hidden" name="save_action" id="saveAction" value="final">

                                <div id="analisaAccordion"></div>

                                <div class="mt-4 d-flex gap-2 justify-content-end flex-wrap">
                                    <a href="{{ route('rmpm.show', $identitas->id) }}" class="btn btn-light">
                                        <i class="ri-arrow-left-line me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-warning px-3" id="btnSimpanDraft">
                                        <i class="mdi mdi-content-save-edit-outline me-1"></i> Simpan Sementara
                                    </button>
                                    <button type="submit" class="btn btn-primary px-3" id="btnSimpan">
                                        <i class="ri-save-line me-1"></i> Simpan Analisa
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Detail Transaksi / History Analisa Long Term (Jika ada riwayat) --}}
                @if (isset($histories) && $histories->isNotEmpty())
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="ri-history-line text-primary me-1"></i> Riwayat & Detail Transaksi Analisa
                        </h5>
                        <span class="badge bg-info">{{ $histories->count() }} Riwayat</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Waktu</th>
                                        <th>User / Analis</th>
                                        <th>Aksi</th>
                                        <th>Uji Kristal</th>
                                        <th>Disposisi</th>
                                        <th>Group ABC</th>
                                        <th>Lampiran Foto</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($histories as $h)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                {{ \Carbon\Carbon::parse($h->created_at)->format('d M Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($h->created_at)->format('H:i') }} WIB
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">{{ $h->user->name ?? 'User' }}</span>
                                            <div class="small text-muted">{{ $h->user->role ?? '-' }}</div>
                                        </td>
                                        <td>
                                            @if ($h->action === 'Simpan Sementara')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="mdi mdi-content-save-edit-outline me-1"></i> Draft
                                                </span>
                                            @elseif ($h->action === 'Update Disposisi')
                                                <span class="badge bg-info">
                                                    <i class="ri-edit-2-line me-1"></i> Update Disposisi
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="ri-checkbox-circle-line me-1"></i> Simpan Final
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($h->uji_kristal === 'positif')
                                                <span class="badge bg-danger-subtle text-danger fw-semibold">Positif</span>
                                            @elseif ($h->uji_kristal === 'negatif')
                                                <span class="badge bg-success-subtle text-success fw-semibold">Negatif</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($h->disposisi === 'Release')
                                                <span class="badge bg-success">{{ $h->disposisi }}</span>
                                            @elseif ($h->disposisi === 'Release Bersyarat')
                                                <span class="badge bg-warning text-dark">{{ $h->disposisi }}</span>
                                            @elseif ($h->disposisi === 'Reject')
                                                <span class="badge bg-danger">{{ $h->disposisi }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($h->group))
                                                <span class="badge bg-primary fs-7">{{ $h->group }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $hPhotos = $h->photos; @endphp
                                            @if (!empty($hPhotos))
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    @foreach ($hPhotos as $pIdx => $photoPath)
                                                        <a href="{{ asset('storage/uploads/attachment_analisa/' . $photoPath) }}" target="_blank" class="d-inline-block border rounded overflow-hidden" style="width: 36px; height: 36px;">
                                                            <img src="{{ asset('storage/uploads/attachment_analisa/' . $photoPath) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                                                        </a>
                                                    @endforeach
                                                    <span class="badge bg-light text-muted border ms-1">{{ count($hPhotos) }} Foto</span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="small text-muted">{{ $h->keterangan ?? '-' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>

<style>
    /* Direct paste animation & focus styling */
    @keyframes pasteCellPulse {
        0% { background-color: #d1e7dd; border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); }
        50% { background-color: #badbcc; }
        100% { background-color: inherit; border-color: #dee2e6; box-shadow: none; }
    }
    .paste-highlight {
        animation: pasteCellPulse 1.2s ease-out !important;
    }
    .analisa-table td:focus-within {
        background-color: #f0f7ff;
    }

    /* Excel-style Multi-cell Selection & Block Highlighting */
    .analisa-table td.cell-selected {
        background-color: rgba(13, 110, 253, 0.14) !important;
        user-select: none;
    }
    .analisa-table td.cell-selected input,
    .analisa-table td.cell-selected select {
        background-color: rgba(13, 110, 253, 0.08) !important;
        border-color: #86b7fe !important;
    }
    .analisa-table td.cell-selected-top {
        border-top: 2px solid #0d6efd !important;
    }
    .analisa-table td.cell-selected-bottom {
        border-bottom: 2px solid #0d6efd !important;
    }
    .analisa-table td.cell-selected-left {
        border-left: 2px solid #0d6efd !important;
    }
    .analisa-table td.cell-selected-right {
        border-right: 2px solid #0d6efd !important;
    }

    #analisaAccordion .analisa-table thead tr th.th-data-col {
        cursor: pointer;
        user-select: none;
        transition: background 0.15s ease;
    }
    #analisaAccordion .analisa-table thead tr th.th-data-col:hover {
        background: #e9ecef;
    }
    #analisaAccordion .analisa-table thead tr th .btn-fill-col-down {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        color: #6c757d;
        background: #fff;
        border: 1px solid #ced4da;
        padding: 0;
        font-size: 12px;
        line-height: 1;
        vertical-align: middle;
        transition: all 0.15s ease;
    }
    #analisaAccordion .analisa-table thead tr th .btn-fill-col-down:hover {
        color: #fff;
        background: #0d6efd;
        border-color: #0d6efd;
        transform: scale(1.12);
    }

    /* Short-term & Garam-Gula table styles */
    #analisaAccordion .short-term-table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    #analisaAccordion .analisa-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: 0.875rem;
    }

    #analisaAccordion .analisa-table thead tr th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 12px;
        border-bottom: 2px solid #dee2e6;
        border-right: 1px solid #e9ecef;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    #analisaAccordion .analisa-table thead tr th:last-child {
        border-right: none;
    }

    #analisaAccordion .analisa-table thead tr th.th-sampel {
        background: #e8f0fe;
        color: #3d5afe;
        min-width: 70px;
        text-align: center;
    }

    #analisaAccordion .analisa-table tbody tr {
        transition: background 0.15s ease;
    }

    #analisaAccordion .analisa-table tbody tr:hover {
        background: #f8f9fa;
    }

    #analisaAccordion .analisa-table tbody td {
        padding: 6px 8px;
        border-bottom: 1px solid #e9ecef;
        border-right: 1px solid #e9ecef;
        vertical-align: middle;
    }

    #analisaAccordion .analisa-table tbody td:last-child {
        border-right: none;
    }

    #analisaAccordion .analisa-table tbody td.td-sampel {
        text-align: center;
        font-weight: 600;
        background: #f8f9fa;
        min-width: 70px;
    }

    #analisaAccordion .analisa-table tfoot td {
        padding: 10px 8px;
        border-top: 2px solid #dee2e6;
        border-right: 1px solid #e9ecef;
        vertical-align: middle;
        background: #f1f5f9;
    }

    #analisaAccordion .analisa-table tfoot td:last-child {
        border-right: none;
    }

    #analisaAccordion .analisa-table .sampel-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        background: #0d6efd;
        color: #fff;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
    }

    #analisaAccordion .analisa-table .form-control-sm,
    #analisaAccordion .analisa-table .form-select-sm {
        min-width: 100px;
        font-size: 0.82rem;
        border-color: #dee2e6;
    }

    #analisaAccordion .analisa-table .form-control-sm:focus,
    #analisaAccordion .analisa-table .form-select-sm:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .15);
    }

    #analisaAccordion .analisa-table .unit-badge {
        display: inline-block;
        font-size: 10px;
        background: #e9ecef;
        color: #6c757d;
        padding: 1px 5px;
        border-radius: 4px;
        margin-left: 3px;
        vertical-align: middle;
    }

    /* Disposisi section below table */
    .disposisi-section {
        margin-top: 20px;
        padding: 16px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }

    .disposisi-section .form-label {
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Photo Upload & Preview Thumbnails */
    .photo-preview-item {
        position: relative;
        width: 90px;
        height: 90px;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        overflow: hidden;
        background: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        transition: transform 0.15s ease;
    }

    .photo-preview-item:hover {
        transform: scale(1.03);
        border-color: #0d6efd;
    }

    .photo-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-preview-item .btn-remove-photo {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        padding: 0;
        transition: background 0.15s;
    }

    .photo-preview-item .btn-remove-photo:hover {
        background: #dc3545;
    }

    .photo-preview-item .photo-badge-idx {
        position: absolute;
        bottom: 2px;
        left: 2px;
        background: rgba(0, 0, 0, 0.65);
        color: #fff;
        font-size: 10px;
        padding: 1px 4px;
        border-radius: 4px;
    }
</style>
@endsection

@section('scripts')
<script>
    const JENIS = '{{ $identitas->jenis }}';
    const IDENTITAS_ID = '{{ $identitas->id }}';
    const DRAFT_KEY = `rmpm_analisa_${IDENTITAS_ID}`;
    const SERVER_EXISTING = @json($existingLongTerm ?? null);
    const SERVER_SHORT_TERM = @json($existingShortTerm ?? []);
    const SERVER_GARAM_GULA = @json($existingGaramGula ?? []);

    let currentType = null;
    let currentKategori = 'incoming';
    let currentJumlah = 0;
    let selectedFiles = [];       // array of File objects for newly added photos
    let existingPhotos = [];      // array of string filenames from server draft/record
    let parsedPasteData = [];     // parsed rows from excel paste modal

    // Master Data for Organo, Warna, Aroma based on Jenis Raw Material
    const MASTER_DATA = {
        'Gula Kelapa': {
            organo: [
                { label: 'OK', value: 'OK', isCustom: false },
                { label: 'Agak asam', value: 'Agak asam', isCustom: false },
                { label: 'Asam', value: 'Asam', isCustom: false },
                { label: 'Agak pahit', value: 'Agak pahit', isCustom: false },
                { label: 'Pahit', value: 'Pahit', isCustom: false },
                { label: 'Sagu', value: 'Sagu', isCustom: false },
                { label: 'Kapur', value: 'Kapur', isCustom: false },
                { label: 'Lain-lain (bisa input teks)', value: 'Lain-lain', isCustom: true },
                { label: 'Campuran (Bisa input teks)', value: 'Campuran', isCustom: true }
            ],
            warna: [
                'Coklat tua',
                'Coklat muda',
                'Coklat'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada'
            ]
        },
        'Gula Tebu': {
            organo: [
                { label: 'OK', value: 'OK', isCustom: false },
                { label: 'Pahit', value: 'Pahit', isCustom: false },
                { label: 'Agak pahit', value: 'Agak pahit', isCustom: false },
                { label: 'Asam dan pH < 5,5', value: 'Asam dan pH < 5,5', isCustom: false },
                { label: 'Agak asam dan pH normal', value: 'Agak asam dan pH normal', isCustom: false },
                { label: 'Lain-lain (bisa input teks)', value: 'Lain-lain', isCustom: true },
                { label: 'Campuran (Bisa input teks)', value: 'Campuran', isCustom: true }
            ],
            warna: [
                'Gelap',
                'Coklat',
                'Coklat tua',
                'Coklat muda'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada'
            ]
        },
        'default': {
            organo: [
                { label: 'OK', value: 'OK', isCustom: false },
                { label: 'Tidak Sesuai', value: 'Tidak Sesuai', isCustom: false },
                { label: 'Lain-lain (bisa input teks)', value: 'Lain-lain', isCustom: true }
            ],
            warna: [
                'Sesuai Standar',
                'Coklat',
                'Coklat muda',
                'Coklat tua',
                'Gelap',
                'Putih',
                'Kuning',
                'Tidak Sesuai'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada',
                'Khas',
                'Tidak Sesuai'
            ]
        }
    };

    function getMasterOptions(fieldKey) {
        const rawJenis = (JENIS || '').trim();
        const config = MASTER_DATA[rawJenis] || MASTER_DATA['default'];
        return config[fieldKey] || MASTER_DATA['default'][fieldKey] || [];
    }

    // Short-term fields definition
    const SHORT_TERM_FIELDS = [
        { key: 'brix[]', label: 'Brix', type: 'number', unit: '' },
        { key: 'ph[]', label: 'pH', type: 'number', unit: '' },
        { key: 'kotoran[]', label: 'Kotoran', type: 'number', unit: '' },
        { key: 'ka[]', label: 'KA', type: 'number', unit: '%' },
        { key: 'organo[]', label: 'Organo', type: 'organo-select', unit: '' },
        { key: 'warna[]', label: 'Warna', type: 'dropdown', masterKey: 'warna', unit: '' },
        { key: 'aroma[]', label: 'Aroma', type: 'dropdown', masterKey: 'aroma', unit: '' },
    ];

    // Garam-Gula table fields definition
    const GARAM_GULA_FIELDS = [
        { key: 'fisik[]', label: 'Fisik', type: 'text', unit: '' },
        { key: '%ka[]', label: '%KA', type: 'number', unit: '%' },
        { key: 'kotoran[]', label: 'Kotoran', type: 'number', unit: '' },
        { key: 'organo[]', label: 'Organo', type: 'organo-select', unit: '' },
        { key: 'warna[]', label: 'Warna', type: 'dropdown', masterKey: 'warna', unit: '' },
        { key: 'aroma[]', label: 'Aroma', type: 'dropdown', masterKey: 'aroma', unit: '' },
        { key: '%nacl[]', label: '%NaCl', type: 'number', unit: '%' },
        { key: 'gross_weight[]', label: 'Gross Weight', type: 'number', unit: 'kg' },
    ];

    const FIELD_DEFS = {
        'short-term': [
            ...SHORT_TERM_FIELDS,
            { key: 'disposisi', label: 'Disposisi', type: 'select', unit: '' },
        ],
        'long-term': [
            { key: 'uji_kristal', label: 'Uji Kristal', type: 'crystal', unit: '' },
            { key: 'disposisi', label: 'Disposisi & Group ABC', type: 'select-long', unit: '' },
        ],
        'garam-gula': [
            ...GARAM_GULA_FIELDS,
            { key: 'disposisi', label: 'Disposisi', type: 'select', unit: '' },
        ],
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        initFormData();

        $('input[name="analisa_type"]').on('change', function() {
            const val = $(this).val();
            const isLong = (val === 'long-term');
            $('#jumlahWrapper').toggle(!isLong);
            if (isLong) {
                $('#jumlahData').val('1');
            } else if (SERVER_SHORT_TERM && SERVER_SHORT_TERM.length > 0) {
                const matching = SERVER_SHORT_TERM.filter(item => (item.kategori || 'incoming') === val);
                if (matching.length > 0) {
                    $('#jumlahData').val(matching.length);
                } else {
                    $('#jumlahData').val(1);
                }
            } else {
                $('#jumlahData').val(1);
            }
        });

        $('#btnMulai').on('click', handleMulai);
        $('#btnReset').on('click', handleReset);

        $('#btnEditJumlah').on('click', function() {
            $('#editJumlahInput').val(currentJumlah);
            $('#editJumlahWrapper').slideDown(150);
            $(this).hide();
        });
        $('#btnCancelJumlah').on('click', function() {
            $('#editJumlahWrapper').slideUp(150);
            $('#btnEditJumlah').show();
        });
        $('#btnApplyJumlah').on('click', handleApplyJumlah);

        // Organo Dynamic Handlers (Dropdown + Custom Text Box)
        $(document).on('change', '.select-organo-dropdown', function() {
            const $container = $(this).closest('.organo-cell-container');
            const $customBox = $container.find('.organo-custom-input-box');
            const $customInput = $container.find('.input-organo-custom-text');
            const $finalInput = $container.find('.input-organo-final');
            const selectedOpt = $(this).find('option:selected');
            const isCustom = (selectedOpt.data('custom') == '1');
            const val = $(this).val();

            if (isCustom) {
                $customBox.slideDown(100);
                if (!$customInput.val()) {
                    $customInput.val(val ? (val + ': ') : '').focus();
                }
                $finalInput.val($customInput.val() || val).trigger('change');
            } else {
                $customBox.slideUp(100);
                $customInput.val('');
                $finalInput.val(val).trigger('change');
            }
            calculateStatistics();
            saveDraft();
        });

        $(document).on('input', '.input-organo-custom-text', function() {
            const $container = $(this).closest('.organo-cell-container');
            const $finalInput = $container.find('.input-organo-final');
            $finalInput.val($(this).val()).trigger('change');
            calculateStatistics();
            saveDraft();
        });

        $(document).on('click', '.btn-close-organo-custom', function() {
            const $container = $(this).closest('.organo-cell-container');
            const $select = $container.find('.select-organo-dropdown');
            const $customBox = $container.find('.organo-custom-input-box');
            const $customInput = $container.find('.input-organo-custom-text');
            const $finalInput = $container.find('.input-organo-final');

            $customBox.slideUp(100);
            $customInput.val('');
            $select.val('OK');
            $finalInput.val('OK').trigger('change');
            calculateStatistics();
            saveDraft();
        });

        // Long term dynamic handlers
        $(document).on('change', '#selectUjiKristal', handleCrystalChange);
        $(document).on('change', '#selectDisposisiLong', handleDisposisiLongChange);

        // Multi Photo Upload Handlers
        $(document).on('change', '#inputMultiAttachment', handlePhotoSelection);
        $(document).on('click', '#btnClearPhotos', handleClearAllPhotos);
        $(document).on('click', '.btn-remove-new-photo', function() {
            const idx = $(this).data('index');
            selectedFiles.splice(idx, 1);
            renderPhotoPreviews();
            saveDraft();
        });
        $(document).on('click', '.btn-remove-existing-photo', function() {
            const idx = $(this).data('index');
            existingPhotos.splice(idx, 1);
            renderPhotoPreviews();
            saveDraft();
        });

        // Recalculate Averages & Organo on change
        $(document).on('input change', '.calc-trigger', function() {
            calculateStatistics();
        });

        // Strict Numeric Restriction: ONLY digits (0-9) and at most one decimal point (. or ,)
        $(document).on('keydown', '.numeric-clean-input', function(e) {
            // Allow control, functional, and navigation keys
            if ([
                'Backspace', 'Delete', 'Tab', 'Enter', 'Escape',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                'Home', 'End'
            ].includes(e.key) || e.ctrlKey || e.metaKey || e.altKey) {
                return;
            }

            // Allow digits 0-9
            if (/^[0-9]$/.test(e.key)) {
                return;
            }

            // Allow decimal point (. or ,) only once
            if (e.key === '.' || e.key === ',') {
                const val = $(this).val();
                if (!val.includes('.') && !val.includes(',')) {
                    return;
                }
            }

            // Block ALL letters, spaces, and other symbols
            e.preventDefault();
        });

        // Auto-sanitize numeric fields on typing / pasting (comma to dot, strip all non-numeric chars)
        $(document).on('input', '.numeric-clean-input', function() {
            let val = $(this).val();
            if (!val) return;
            const pos = this.selectionStart;
            val = val.replace(/,/g, '.');
            val = val.replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts.slice(1).join('');
            }
            if ($(this).val() !== val) {
                $(this).val(val);
                if (pos !== null) {
                    try { this.setSelectionRange(pos, pos); } catch(err) {}
                }
            }
        });

        // Global Paste Capturing Listener (Excel / SAP direct paste support)
        window.addEventListener('paste', function(e) {
            if ($(e.target).is('textarea, #editJumlahInput, #jumlahData') && !$(e.target).closest('.analisa-table').length) {
                return;
            }
            const hasSelection = (tableSelection !== null);
            const inTable = $(e.target).closest('#analisaAccordion table, .analisa-table').length > 0 || $(document.activeElement).closest('#analisaAccordion table, .analisa-table').length > 0;
            const inAccordion = $(e.target).closest('#analisaAccordion').length > 0 || $(document.activeElement).closest('#analisaAccordion').length > 0;
            if (hasSelection || inTable || inAccordion) {
                handleDirectTablePaste(e);
            }
        }, true);

        // Capture Enter and Tab in window capture phase to guarantee downwards navigation
        window.addEventListener('keydown', function(e) {
            const target = e.target;
            const isInside = target && target.closest && target.closest('.analisa-table, #analisaAccordion table');
            if (isInside && (e.key === 'Tab' || e.key === 'Enter')) {
                e.preventDefault();
                e.stopPropagation();
                navigateTableVertical(target, e.shiftKey ? -1 : 1);
            }
        }, true);

        // Direct Table Cell Paste Listener (jQuery backup)
        $(document).on('paste', handleDirectTablePaste);

        // Excel-like Keyboard Navigation & Range Selection (Enter, Tab, Shift+Arrows, Ctrl+A, Delete, Ctrl+D)
        $(document).on('keydown', handleTableKeydown);
        $(document).on('mousedown', '#analisaAccordion table tbody td, #analisaAccordion table thead th', handleTableMouseDown);
        $(document).on('mouseenter', '#analisaAccordion table tbody td, #analisaAccordion table thead th', handleTableMouseEnter);
        $(document).on('mouseup', handleTableMouseUp);
        $(document).on('mousedown', handleDocMouseDown);
        $(document).on('copy', handleTableCopy);

        // Quick Fill Down Column Button Handler
        $(document).on('click', '.btn-fill-col-down', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const colIdx = parseInt($(this).data('col-idx'));
            const fields = getFields();
            const field = fields[colIdx];
            if (!field || currentJumlah <= 1) return;

            const $table = $('#analisaAccordion').find('table');
            const $rows = $table.find('tbody tr');
            const $firstRow = $rows.eq(0);
            let firstVal = '';

            if (field.type === 'organo-select') {
                firstVal = $firstRow.find('.input-organo-final').val() || '';
            } else {
                firstVal = $firstRow.find(`[name="${field.key}"]`).val() || '';
            }

            if (!firstVal) {
                Swal.fire({
                    icon: 'info',
                    text: `Isi nilai sampel ke-1 pada kolom "${field.label}" terlebih dahulu, lalu klik tombol ini untuk menyalin ke semua baris.`
                });
                return;
            }

            for (let r = 1; r < currentJumlah; r++) {
                const $row = $rows.eq(r);
                applyFieldValueToCell($row, field, firstVal);
            }

            calculateStatistics();
            saveDraft();
            setSelection(0, colIdx, currentJumlah - 1, colIdx);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Kolom ${field.label} disalin ke semua baris`,
                showConfirmButton: false,
                timer: 1500
            });
        });

        // Draft auto-save on input
        $(document).on('input change',
            '#formAnalisa input:not([type=file]), #formAnalisa select, #formAnalisa textarea',
            saveDraft);

        $(document).on('input', '.upper-input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Submit buttons
        $('#btnSimpanDraft').on('click', function() {
            $('#saveAction').val('draft');
            submitForm('draft');
        });

        $('#formAnalisa').on('submit', function(e) {
            e.preventDefault();
            $('#saveAction').val('final');
            submitForm('final');
        });
    });

    function applyOrganoValueToCell($container, val) {
        if (!$container || !$container.length) return;
        const $select = $container.find('.select-organo-dropdown');
        const $customBox = $container.find('.organo-custom-input-box');
        const $customInput = $container.find('.input-organo-custom-text');
        const $finalInput = $container.find('.input-organo-final');

        $finalInput.val(val || '');

        if (!val) {
            $select.val('');
            $customBox.hide();
            $customInput.val('');
            return;
        }

        const optMatches = $select.find(`option[value="${val}"]`);
        if (optMatches.length > 0 && optMatches.data('custom') != '1') {
            $select.val(val);
            $customBox.hide();
            $customInput.val('');
        } else {
            let selectTarget = 'Lain-lain';
            if (val.toUpperCase().startsWith('CAMPURAN')) {
                selectTarget = 'Campuran';
            }
            $select.val(selectTarget);
            $customBox.show();
            $customInput.val(val);
        }
    }

    function populateExistingLongTerm() {
        currentType = 'long-term';
        currentKategori = 'long-term';
        currentJumlah = 1;

        if (SERVER_EXISTING && SERVER_EXISTING.attachment) {
            if (Array.isArray(SERVER_EXISTING.attachment)) {
                existingPhotos = [...SERVER_EXISTING.attachment];
            } else if (typeof SERVER_EXISTING.attachment === 'string' && SERVER_EXISTING.attachment !== '-') {
                try {
                    const parsed = JSON.parse(SERVER_EXISTING.attachment);
                    existingPhotos = Array.isArray(parsed) ? parsed : [SERVER_EXISTING.attachment];
                } catch {
                    existingPhotos = [SERVER_EXISTING.attachment];
                }
            }
        }

        if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
            $(`input[name="analisa_type"][value="long-term"]`).prop('checked', true);
        }
        $('#jumlahData').val(1);
        startForm('long-term', 1, 'long-term');

        setTimeout(() => {
            if (SERVER_EXISTING) {
                if (SERVER_EXISTING.status === 'draft' || !SERVER_EXISTING.disposisi) {
                    $('#draftStatusBadge').show();
                } else {
                    $('#draftStatusBadge').hide();
                }
                if (SERVER_EXISTING.uji_kristal) {
                    $('#selectUjiKristal').val(SERVER_EXISTING.uji_kristal).trigger('change');
                }
                if (SERVER_EXISTING.disposisi) {
                    $('#selectDisposisiLong').val(SERVER_EXISTING.disposisi).trigger('change');
                }
                if (SERVER_EXISTING.group) {
                    $('#selectGroupLong').val(SERVER_EXISTING.group);
                }
                if (SERVER_EXISTING.keterangan) {
                    $('#keteranganLong').val(SERVER_EXISTING.keterangan);
                }
                renderPhotoPreviews();
            }
        }, 100);
        return true;
    }

    function populateExistingShortTerm(kategori) {
        if (!SERVER_SHORT_TERM || !SERVER_SHORT_TERM.length) return false;
        const matching = SERVER_SHORT_TERM.filter(item => (item.kategori || 'incoming') === (kategori || 'incoming'));
        if (!matching.length) return false;

        currentType = 'short-term';
        currentKategori = kategori;
        currentJumlah = matching.length;

        if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
            $(`input[name="analisa_type"][value="${kategori}"]`).prop('checked', true);
        }
        $('#jumlahData').val(matching.length);
        startForm('short-term', matching.length, kategori);

        setTimeout(() => {
            if (matching[0].disposisi) {
                $('select[name="disposisi"]').val(matching[0].disposisi).trigger('change');
                $('#draftStatusBadge').hide();
            } else {
                $('select[name="disposisi"]').val('').trigger('change');
                $('#draftStatusBadge').show();
            }
            if (matching[0].keterangan) {
                $('textarea[name="keterangan"]').val(matching[0].keterangan);
            }

            matching.forEach((rec, idx) => {
                if (rec.brix !== null && rec.brix !== undefined) {
                    $('input[name="brix[]"]').eq(idx).val(rec.brix);
                }
                if (rec.ph !== null && rec.ph !== undefined) {
                    $('input[name="ph[]"]').eq(idx).val(rec.ph);
                }
                if (rec.kotoran !== null && rec.kotoran !== undefined) {
                    $('input[name="kotoran[]"]').eq(idx).val(rec.kotoran);
                }
                if (rec.ka !== null && rec.ka !== undefined) {
                    $('input[name="ka[]"]').eq(idx).val(rec.ka);
                }
                if (rec.warna) {
                    $('select[name="warna[]"]').eq(idx).val(rec.warna);
                }
                if (rec.aroma) {
                    $('select[name="aroma[]"]').eq(idx).val(rec.aroma);
                }
                if (rec.organo) {
                    const $container = $('.organo-cell-container').eq(idx);
                    applyOrganoValueToCell($container, rec.organo);
                }
            });

            calculateStatistics();
        }, 100);
        return true;
    }

    function populateExistingGaramGula() {
        if (!SERVER_GARAM_GULA || !SERVER_GARAM_GULA.length) return false;

        currentType = 'garam-gula';
        currentKategori = 'incoming';
        currentJumlah = SERVER_GARAM_GULA.length;

        $('#jumlahData').val(SERVER_GARAM_GULA.length);
        startForm('garam-gula', SERVER_GARAM_GULA.length, 'incoming');

        setTimeout(() => {
            if (SERVER_GARAM_GULA[0].disposisi) {
                $('select[name="disposisi"]').val(SERVER_GARAM_GULA[0].disposisi).trigger('change');
                $('#draftStatusBadge').hide();
            } else {
                $('select[name="disposisi"]').val('').trigger('change');
                $('#draftStatusBadge').show();
            }
            if (SERVER_GARAM_GULA[0].keterangan) {
                $('textarea[name="keterangan"]').val(SERVER_GARAM_GULA[0].keterangan);
            }
            SERVER_GARAM_GULA.forEach((rec, idx) => {
                if (rec.fisik) $('input[name="fisik[]"]').eq(idx).val(rec.fisik);
                if (rec.ka) $('input[name="%ka[]"]').eq(idx).val(rec.ka);
                if (rec.kotoran) $('input[name="kotoran[]"]').eq(idx).val(rec.kotoran);
                if (rec.nacl) $('input[name="%nacl[]"]').eq(idx).val(rec.nacl);
                if (rec.gross_weight) $('input[name="gross_weight[]"]').eq(idx).val(rec.gross_weight);
                if (rec.warna) $('select[name="warna[]"]').eq(idx).val(rec.warna);
                if (rec.aroma) $('select[name="aroma[]"]').eq(idx).val(rec.aroma);
                if (rec.organo) {
                    const $container = $('.organo-cell-container').eq(idx);
                    applyOrganoValueToCell($container, rec.organo);
                }
            });
            calculateStatistics();
        }, 100);
        return true;
    }

    function initFormData() {
        const urlParams = new URLSearchParams(window.location.search);
        const reqKategori = urlParams.get('kategori');

        // Case 1: Specific category requested in URL (?kategori=sta, ?kategori=monitoring, ?kategori=incoming, ?kategori=long-term)
        if (reqKategori === 'long-term') {
            if (SERVER_EXISTING && SERVER_EXISTING.id) {
                populateExistingLongTerm();
            } else {
                currentType = 'long-term';
                currentKategori = 'long-term';
                currentJumlah = 1;
                if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
                    $(`input[name="analisa_type"][value="long-term"]`).prop('checked', true);
                }
                $('#jumlahData').val(1);
                startForm('long-term', 1, 'long-term');
            }
            return;
        }

        if (reqKategori && ['incoming', 'sta', 'monitoring'].includes(reqKategori)) {
            const hasData = populateExistingShortTerm(reqKategori);
            if (!hasData) {
                if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
                    $(`input[name="analisa_type"][value="${reqKategori}"]`).prop('checked', true);
                }
                $('#jumlahData').val(1);
                startForm('short-term', 1, reqKategori);
            }
            return;
        }

        // Case 2: No specific category in URL. Check database records.
        // If Short Term has data:
        if (SERVER_SHORT_TERM && SERVER_SHORT_TERM.length > 0) {
            const incomingExists = SERVER_SHORT_TERM.some(i => !i.kategori || i.kategori === 'incoming');
            const staExists = SERVER_SHORT_TERM.some(i => i.kategori === 'sta');
            const monitoringExists = SERVER_SHORT_TERM.some(i => i.kategori === 'monitoring');

            if (incomingExists) {
                populateExistingShortTerm('incoming');
                return;
            } else if (staExists) {
                populateExistingShortTerm('sta');
                return;
            } else if (monitoringExists) {
                populateExistingShortTerm('monitoring');
                return;
            }
        }

        // If Long Term has data:
        if (SERVER_EXISTING && SERVER_EXISTING.id) {
            populateExistingLongTerm();
            return;
        }

        // If Garam Gula has data:
        if (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0) {
            populateExistingGaramGula();
            return;
        }

        // Case 3: Fallback to localStorage draft
        restoreFromDraft();
    }

    function handleMulai() {
        const isGulaKristal = ['Gula Tebu', 'Gula Kelapa'].includes(JENIS);
        let selectedVal = isGulaKristal ? $('input[name="analisa_type"]:checked').val() : 'garam-gula';

        if (isGulaKristal && !selectedVal) {
            return Swal.fire({
                icon: 'warning',
                text: 'Pilih jenis analisa terlebih dahulu.'
            });
        }

        if (selectedVal === 'long-term') {
            if (SERVER_EXISTING && SERVER_EXISTING.id) {
                populateExistingLongTerm();
            } else {
                startForm('long-term', 1, 'long-term');
            }
            return;
        }

        if (selectedVal === 'garam-gula') {
            const jumlah = parseInt($('#jumlahData').val()) || 1;
            if (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0 && SERVER_GARAM_GULA.length === jumlah) {
                populateExistingGaramGula();
            } else {
                startForm('garam-gula', jumlah, 'incoming');
                populateExistingGaramGula();
            }
            return;
        }

        // Short-term: incoming, sta, monitoring
        const matching = SERVER_SHORT_TERM ? SERVER_SHORT_TERM.filter(item => (item.kategori || 'incoming') === selectedVal) : [];
        const inputJumlah = parseInt($('#jumlahData').val()) || 1;

        if (matching.length > 0 && inputJumlah === matching.length) {
            populateExistingShortTerm(selectedVal);
        } else {
            startForm('short-term', inputJumlah, selectedVal);
            populateExistingShortTerm(selectedVal);
        }
    }

    function handleApplyJumlah() {
        const newJumlah = parseInt($('#editJumlahInput').val());
        if (!newJumlah || newJumlah <= 0) {
            return Swal.fire({
                icon: 'warning',
                text: 'Masukkan jumlah sampel yang valid.'
            });
        }
        const savedValues = collectArrayValues();

        currentJumlah = newJumlah;
        renderAccordion(currentType, currentJumlah);
        updateLabels();
        restoreArrayValues(savedValues, newJumlah);

        $('#editJumlahWrapper').slideUp(150);
        $('#btnEditJumlah').show();
        calculateStatistics();
        saveDraft();
    }

    function collectArrayValues() {
        const saved = {};
        $('#formAnalisa').find('input, select').each(function() {
            const name = $(this).attr('name');
            if (!name || !name.endsWith('[]')) return;
            if (!saved[name]) saved[name] = [];
            saved[name].push($(this).val());
        });
        return saved;
    }

    function restoreArrayValues(saved, maxCount) {
        for (const [name, values] of Object.entries(saved)) {
            if (name === 'organo[]') {
                $('.organo-cell-container').each(function(i) {
                    if (i >= maxCount) return;
                    const val = values[i] || '';
                    const $select = $(this).find('.select-organo-dropdown');
                    const $customBox = $(this).find('.organo-custom-input-box');
                    const $customInput = $(this).find('.input-organo-custom-text');
                    const $finalInput = $(this).find('.input-organo-final');

                    $finalInput.val(val);

                    if (!val) {
                        $select.val('');
                        $customBox.hide();
                        $customInput.val('');
                        return;
                    }

                    const optMatches = $select.find(`option[value="${val}"]`);
                    if (optMatches.length > 0 && optMatches.data('custom') != '1') {
                        $select.val(val);
                        $customBox.hide();
                        $customInput.val('');
                    } else {
                        let selectTarget = 'Lain-lain';
                        if (val.toUpperCase().startsWith('CAMPURAN')) {
                            selectTarget = 'Campuran';
                        }
                        $select.val(selectTarget);
                        $customBox.show();
                        $customInput.val(val);
                    }
                });
            } else {
                $(`[name="${name}"]`).each(function(i) {
                    if (i < maxCount && values[i] !== undefined) $(this).val(values[i]);
                });
            }
        }
    }

    function handleReset() {
        Swal.fire({
            icon: 'question',
            title: 'Reset Form?',
            text: 'Semua data yang belum disimpan final akan dihapus dari form.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (r.isConfirmed) {
                clearDraft();
                location.reload();
            }
        });
    }

    function startForm(type, jumlah, kategori = 'incoming') {
        currentType = type;
        currentJumlah = jumlah;
        currentKategori = kategori;
        $('#hiddenAnalisaType').val(type);
        $('#hiddenKategori').val(kategori);
        renderAccordion(type, jumlah);
        $('#setupSection').hide();
        $('#dividerForm, #analisaSection').show();
        updateLabels();

        if (type === 'long-term') {
            $('#jumlahSampelBadgeWrapper').hide();
            $('#summaryStatsBar').hide();
        } else {
            $('#jumlahSampelBadgeWrapper').show();
            $('#summaryStatsBar').show();
            calculateStatistics();
        }
        saveDraft();
    }

    function updateLabels() {
        let typeBadge = '';
        if (currentType === 'short-term') {
            if (currentKategori === 'sta') {
                typeBadge = '<i class="ri-flashlight-line me-1"></i> STA (Short Term Analisa)';
                $('#labelAnalisaType').removeClass().addClass('badge bg-info text-white fs-6').html(typeBadge);
            } else if (currentKategori === 'monitoring') {
                typeBadge = '<i class="ri-line-chart-line me-1"></i> Monitoring Berkala';
                $('#labelAnalisaType').removeClass().addClass('badge bg-success text-white fs-6').html(typeBadge);
            } else {
                typeBadge = '<i class="ri-inbox-archive-line me-1"></i> Incoming (Short Term)';
                $('#labelAnalisaType').removeClass().addClass('badge bg-primary text-white fs-6').html(typeBadge);
            }
        } else if (currentType === 'long-term') {
            typeBadge = '<i class="ri-microscope-line me-1"></i> Long Term';
            $('#labelAnalisaType').removeClass().addClass('badge bg-dark text-white fs-6').html(typeBadge);
        } else {
            typeBadge = 'Analisa Standar';
            $('#labelAnalisaType').removeClass().addClass('badge bg-primary text-white fs-6').html(typeBadge);
        }

        $('#labelJumlahSampel').text(currentJumlah + ' Sampel');
    }

    // ─────────────────────────────────────────────
    // RENDER ACCORDION / TABLES
    // ─────────────────────────────────────────────
    function renderAccordion(type, jumlah) {
        if (type === 'short-term') {
            $('#analisaAccordion').html(renderShortTermTable(jumlah));
            calculateStatistics();
            return;
        }

        if (type === 'garam-gula') {
            $('#analisaAccordion').html(renderGaramGulaTable(jumlah));
            calculateStatistics();
            return;
        }

        // long-term: accordion
        const fields = FIELD_DEFS[type] || [];
        let html = '<div class="accordion accordion-flush">';

        fields.forEach((field, idx) => {
            const accId = 'acc-' + field.key.replace(/[\[\]%.]/g, '-').replace(/-+/g, '-');
            const isOpen = true;

            html += `
                <div class="accordion-item border rounded mb-3 shadow-none">
                    <h2 class="accordion-header">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'} bg-light fw-semibold text-dark"
                            type="button" data-bs-toggle="collapse" data-bs-target="#${accId}">
                            <i class="ri-checkbox-blank-circle-fill text-primary fs-8 me-2"></i> ${field.label}
                        </button>
                    </h2>
                    <div id="${accId}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}">
                        <div class="accordion-body p-3">
                            ${renderFieldBody(field, jumlah)}
                        </div>
                    </div>
                </div>`;
        });

        html += '</div>';
        $('#analisaAccordion').html(html);
    }

    function renderOrganoCell(fieldKey) {
        const organoOpts = getMasterOptions('organo');
        let optHtml = `<option value="">-- Pilih Organo --</option>`;
        organoOpts.forEach(opt => {
            const label = typeof opt === 'object' ? opt.label : opt;
            const val = typeof opt === 'object' ? opt.value : opt;
            const isCustom = typeof opt === 'object' ? opt.isCustom : (val.includes('Lain-lain') || val.includes('Campuran'));
            optHtml += `<option value="${val}" data-custom="${isCustom ? '1' : '0'}">${label}</option>`;
        });

        return `
            <div class="organo-cell-container">
                <select class="form-select form-select-sm select-organo-dropdown calc-trigger">
                    ${optHtml}
                </select>
                <div class="organo-custom-input-box mt-1" style="display:none;">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm input-organo-custom-text calc-trigger" placeholder="Tulis rincian..." />
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-close-organo-custom" title="Kembali ke pilihan dropdown">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="${fieldKey}" class="input-organo-final calc-trigger" value="" />
            </div>`;
    }

    function renderDropdownCell(field) {
        const opts = getMasterOptions(field.masterKey);
        let optHtml = `<option value="">-- Pilih ${field.label} --</option>`;
        opts.forEach(opt => {
            const val = typeof opt === 'object' ? opt.value : opt;
            const label = typeof opt === 'object' ? opt.label : opt;
            optHtml += `<option value="${val}">${label}</option>`;
        });
        return `
            <select class="form-select form-select-sm select-${field.key.replace('[]','')} calc-trigger" name="${field.key}">
                ${optHtml}
            </select>`;
    }

    // ─────────────────────────────────────────────
    // SHORT-TERM TABLE RENDER (NUMERIC + DROPDOWN + AVERAGE ROW)
    // ─────────────────────────────────────────────
    function renderShortTermTable(jumlah) {
        let thCols = `<th class="th-sampel">Sampel</th>`;
        SHORT_TERM_FIELDS.forEach((f, cIdx) => {
            const unit = f.unit ? `<span class="unit-badge">${f.unit}</span>` : '';
            const fillBtn = `<button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="${cIdx}" title="Salin nilai baris 1 ke semua baris (${f.label})"><i class="ri-arrow-down-double-line"></i></button>`;
            thCols += `<th class="th-data-col" data-col-idx="${cIdx}" title="Klik kolom untuk blok & copy-paste / Ctrl+D"><div class="d-flex align-items-center justify-content-between gap-1"><span>${f.label}${unit}</span>${fillBtn}</div></th>`;
        });

        let rows = '';
        for (let i = 1; i <= jumlah; i++) {
            let tds = `<td class="td-sampel"><span class="sampel-badge">${i}</span></td>`;
            SHORT_TERM_FIELDS.forEach(f => {
                if (f.type === 'organo-select') {
                    tds += `<td>${renderOrganoCell(f.key)}</td>`;
                } else if (f.type === 'dropdown') {
                    tds += `<td>${renderDropdownCell(f)}</td>`;
                } else {
                    // number inputs (Brix, pH, Kotoran, KA)
                    tds += `
                        <td>
                            <input type="text"
                                inputmode="decimal"
                                class="form-control form-control-sm calc-trigger numeric-clean-input"
                                name="${f.key}"
                                placeholder="0.00"
                                autocomplete="off">
                        </td>`;
                }
            });
            rows += `<tr>${tds}</tr>`;
        }

        // Table footer for Averages & Organo %
        const tfoot = `
            <tfoot class="table-light">
                <tr class="fw-bold">
                    <td class="text-center">
                        <span class="badge bg-primary px-2 py-1">AVERAGE</span>
                    </td>
                    <td><span id="tblAvgBrix" class="text-primary fw-bold">-</span></td>
                    <td><span id="tblAvgPh" class="text-primary fw-bold">-</span></td>
                    <td><span id="tblAvgKotoran" class="text-primary fw-bold">-</span></td>
                    <td><span id="tblAvgKa" class="text-primary fw-bold">-</span></td>
                    <td>
                        <div id="tblOrganoPercent" class="d-flex flex-wrap gap-1 align-items-center">-</div>
                    </td>
                    <td><span class="text-muted small">-</span></td>
                    <td><span class="text-muted small">-</span></td>
                </tr>
            </tfoot>`;

        const disposisiHtml = `
                <div class="disposisi-section">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold mb-1">
                                <i class="ri-check-double-line me-1 text-primary"></i>
                                Disposisi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" name="disposisi">
                                <option value="">-- Pilih Disposisi --</option>
                                <option value="Release">Release</option>
                                <option value="Reject">Reject</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold mb-1">
                                <i class="ri-file-text-line me-1 text-secondary"></i>
                                Keterangan
                                <span class="text-muted fw-normal" style="font-size:11px;">(opsional)</span>
                            </label>
                            <textarea class="form-control form-control-sm" name="keterangan"
                                rows="2" placeholder="Tambahkan catatan atau keterangan analisa..."></textarea>
                        </div>
                    </div>
                </div>`;

        let bannerHtml = '';
        if (currentKategori === 'sta') {
            bannerHtml = `
                <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center border-info">
                    <i class="ri-flashlight-line fs-5 text-info me-2"></i>
                    <div>
                        <strong>Mode STA (Short Term Analisa):</strong> Pengisian parameter bersifat fleksibel / parsial (misal: Brix & pH saja). Parameter yang tidak diuji dapat dikosongkan. Disposisi tetap wajib dipilih.
                    </div>
                </div>`;
        } else if (currentKategori === 'monitoring') {
            bannerHtml = `
                <div class="alert alert-success py-2 px-3 mb-2 small d-flex align-items-center border-success">
                    <i class="ri-line-chart-line fs-5 text-success me-2"></i>
                    <div>
                        <strong>Mode Monitoring:</strong> Digunakan untuk pemantauan berkala saat proses produksi. Pengisian parameter bersifat fleksibel sesuai kebutuhan. Disposisi tetap wajib dipilih.
                    </div>
                </div>`;
        } else {
            bannerHtml = `
                <div class="alert alert-primary py-2 px-3 mb-2 small d-flex align-items-center border-primary">
                    <i class="ri-inbox-archive-line fs-5 text-primary me-2"></i>
                    <div>
                        <strong>Mode Analisa Incoming:</strong> Seluruh parameter wajib diisi lengkap untuk verifikasi penerimaan bahan baku.
                    </div>
                </div>`;
        }

        const tipsBanner = `
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 p-2 bg-light rounded border text-muted" style="font-size: 11.5px;">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span><i class="ri-keyboard-line text-primary me-1"></i><strong>Enter / Tab:</strong> Ke sampel berikutnya (bawah)</span>
                    <span><i class="ri-file-copy-line text-primary me-1"></i><strong>Ctrl+C / Ctrl+V:</strong> Copy-Paste per sel atau blok</span>
                    <span><i class="ri-arrow-down-line text-primary me-1"></i><strong>Ctrl+D:</strong> Fill Down (Salin ke bawah)</span>
                    <span><i class="ri-arrow-down-double-line text-primary me-1"></i><strong>Tombol <i class="ri-arrow-down-double-line"></i>:</strong> Salin baris 1 ke semua</span>
                </div>
            </div>`;

        return `
                ${bannerHtml}
                ${tipsBanner}
                <div class="short-term-table-wrapper">
                    <table class="analisa-table" id="tableAnalisaShortTerm">
                        <thead>
                            <tr>${thCols}</tr>
                        </thead>
                        <tbody>${rows}</tbody>
                        ${tfoot}
                    </table>
                </div>
                ${disposisiHtml}`;
    }

    // ─────────────────────────────────────────────
    // GARAM-GULA TABLE RENDER
    // ─────────────────────────────────────────────
    function renderGaramGulaTable(jumlah) {
        let thCols = `<th class="th-sampel">Sampel</th>`;
        GARAM_GULA_FIELDS.forEach((f, cIdx) => {
            const unit = f.unit ? `<span class="unit-badge">${f.unit}</span>` : '';
            const fillBtn = `<button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="${cIdx}" title="Salin nilai baris 1 ke semua baris (${f.label})"><i class="ri-arrow-down-double-line"></i></button>`;
            thCols += `<th class="th-data-col" data-col-idx="${cIdx}" title="Klik kolom untuk blok & copy-paste / Ctrl+D"><div class="d-flex align-items-center justify-content-between gap-1"><span>${f.label}${unit}</span>${fillBtn}</div></th>`;
        });

        let rows = '';
        for (let i = 1; i <= jumlah; i++) {
            let tds = `<td class="td-sampel"><span class="sampel-badge">${i}</span></td>`;
            GARAM_GULA_FIELDS.forEach(f => {
                if (f.type === 'organo-select') {
                    tds += `<td>${renderOrganoCell(f.key)}</td>`;
                } else if (f.type === 'dropdown') {
                    tds += `<td>${renderDropdownCell(f)}</td>`;
                } else if (f.type === 'text') {
                    tds += `
                        <td>
                            <input type="text"
                                class="form-control form-control-sm upper-input"
                                name="${f.key}"
                                placeholder="-"
                                autocomplete="off">
                        </td>`;
                } else {
                    // number inputs (%KA, Kotoran, %NaCl, Gross Weight)
                    tds += `
                        <td>
                            <input type="text"
                                inputmode="decimal"
                                class="form-control form-control-sm calc-trigger numeric-clean-input"
                                name="${f.key}"
                                placeholder="0.00"
                                autocomplete="off">
                        </td>`;
                }
            });
            rows += `<tr>${tds}</tr>`;
        }

        const tfoot = `
            <tfoot class="table-light">
                <tr class="fw-bold">
                    <td class="text-center">
                        <span class="badge bg-primary px-2 py-1">AVERAGE</span>
                    </td>
                    <td><span class="text-muted small">-</span></td>
                    <td><span id="tblAvgKaGG" class="text-primary fw-bold">-</span></td>
                    <td><span id="tblAvgKotoranGG" class="text-primary fw-bold">-</span></td>
                    <td>
                        <div id="tblOrganoPercentGG" class="d-flex flex-wrap gap-1 align-items-center">-</div>
                    </td>
                    <td><span class="text-muted small">-</span></td>
                    <td><span class="text-muted small">-</span></td>
                    <td><span id="tblAvgNaclGG" class="text-primary fw-bold">-</span></td>
                    <td><span id="tblAvgWeightGG" class="text-primary fw-bold">-</span></td>
                </tr>
            </tfoot>`;

        const disposisiHtml = `
                <div class="disposisi-section">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold mb-1">
                                <i class="ri-check-double-line me-1 text-primary"></i>
                                Disposisi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" name="disposisi">
                                <option value="">-- Pilih Disposisi --</option>
                                <option value="Release">Release</option>
                                <option value="Reject">Reject</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold mb-1">
                                <i class="ri-file-text-line me-1 text-secondary"></i>
                                Keterangan
                                <span class="text-muted fw-normal" style="font-size:11px;">(opsional)</span>
                            </label>
                            <textarea class="form-control form-control-sm" name="keterangan"
                                rows="2" placeholder="Tambahkan catatan atau keterangan analisa..."></textarea>
                        </div>
                    </div>
                </div>`;

        return `
                <div class="short-term-table-wrapper">
                    <table class="analisa-table" id="tableAnalisaGaramGula">
                        <thead>
                            <tr>${thCols}</tr>
                        </thead>
                        <tbody>${rows}</tbody>
                        ${tfoot}
                    </table>
                </div>
                ${disposisiHtml}`;
    }

    // ─────────────────────────────────────────────
    // FIELD BODY (LONG TERM)
    // ─────────────────────────────────────────────
    function renderFieldBody(field, jumlah) {
        switch (field.type) {

            case 'select-long':
                return `
                    <div class="p-3 border rounded bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div id="disposisiPlaceholder" class="text-muted small fst-italic py-2">
                                    Disposisi akan muncul setelah memilih hasil uji kristal.
                                </div>

                                <div id="disposisiAutoWrapper" style="display:none;">
                                    <label class="form-label small fw-semibold">Disposisi</label>
                                    <input class="form-control form-control-sm bg-light fw-semibold text-success" value="Release (otomatis)" readonly>
                                </div>

                                <div id="disposisiManualWrapper" style="display:none;">
                                    <label class="form-label small fw-semibold">Pilih Disposisi <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="disposisi" id="selectDisposisiLong">
                                        <option value="">-- Pilih Disposisi --</option>
                                        <option value="Release">Release</option>
                                        <option value="Release Bersyarat">Release Bersyarat</option>
                                        <option value="Reject">Reject</option>
                                    </select>
                                    <div class="form-text text-muted">Wajib diisi karena uji kristal positif.</div>
                                </div>

                                {{-- Field Group ABC (Muncul jika Disposisi Release atau Release Bersyarat) --}}
                                <div id="groupLongWrapper" class="mt-3 p-3 border border-primary-subtle rounded bg-light" style="display:none;">
                                    <label class="form-label small fw-semibold text-primary d-flex align-items-center mb-1">
                                        <i class="ri-node-tree me-1 fs-6"></i> Group ABC <span class="text-danger ms-1">*</span>
                                    </label>
                                    <select class="form-select form-select-sm border-primary" name="group" id="selectGroupLong">
                                        <option value="">-- Pilih Group ABC --</option>
                                        <option value="Group A">Group A</option>
                                        <option value="Group B">Group B</option>
                                        <option value="Group C">Group C</option>
                                    </select>
                                    <div class="form-text small text-muted mt-1">
                                        <i class="ri-information-line"></i> Group ABC muncul karena disposisi adalah Release / Release Bersyarat.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    Keterangan
                                    <span class="text-muted fw-normal" style="font-size:11px;">(opsional)</span>
                                </label>
                                <textarea class="form-control form-control-sm" name="keterangan" id="keteranganLong"
                                    rows="4" placeholder="Tambahkan catatan atau keterangan analisa..."></textarea>
                            </div>
                        </div>
                    </div>`;

            case 'crystal':
                return `
                    <div class="p-3 border rounded bg-white">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Hasil Uji Kristal <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="uji_kristal" id="selectUjiKristal">
                                    <option value="">-- Pilih Hasil --</option>
                                    <option value="negatif">Negatif</option>
                                    <option value="positif">Positif</option>
                                </select>
                            </div>

                            <div class="col-md-7" id="attachmentWrapper" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-semibold mb-0">
                                        <i class="ri-image-add-line me-1 text-primary"></i> Lampiran Gambar Kristal <span class="text-danger">*</span>
                                    </label>
                                    <span id="photoCountBadge" class="badge bg-secondary" style="font-size:11px;">0 / 5 Foto</span>
                                </div>

                                <div class="input-group input-group-sm mb-2">
                                    <input type="file" class="form-control form-control-sm" id="inputMultiAttachment" accept="image/jpeg,image/png,image/jpg,image/webp" multiple>
                                    <button class="btn btn-outline-danger" type="button" id="btnClearPhotos" title="Hapus semua foto">
                                        <i class="ri-delete-bin-line me-1"></i> Hapus Semua
                                    </button>
                                </div>
                                <div class="form-text small text-muted mb-2">
                                    Maksimal 5 foto. Format: JPG/JPEG/PNG/WEBP (maks 5MB/foto).
                                </div>

                                {{-- Container for preview thumbnails --}}
                                <div id="photoPreviewContainer" class="d-flex flex-wrap gap-2 pt-1"></div>
                            </div>
                        </div>
                    </div>`;

            default:
                return '';
        }
    }

    // ─────────────────────────────────────────────
    // LIVE CALCULATIONS (AVERAGE & ORGANO %)
    // ─────────────────────────────────────────────
    function calculateStatistics() {
        if (currentType === 'short-term') {
            // 1. Average Brix
            const avgBrix = calcColumnAvg('brix[]');
            $('#statAvgBrix').text(avgBrix !== null ? avgBrix.toFixed(2) : '-');
            $('#tblAvgBrix').text(avgBrix !== null ? avgBrix.toFixed(2) : '-');

            // 2. Average pH
            const avgPh = calcColumnAvg('ph[]');
            $('#statAvgPh').text(avgPh !== null ? avgPh.toFixed(2) : '-');
            $('#tblAvgPh').text(avgPh !== null ? avgPh.toFixed(2) : '-');

            // 3. Average Kotoran
            const avgKotoran = calcColumnAvg('kotoran[]');
            $('#statAvgKotoran').text(avgKotoran !== null ? avgKotoran.toFixed(3) : '-');
            $('#tblAvgKotoran').text(avgKotoran !== null ? avgKotoran.toFixed(3) : '-');

            // 4. Average KA
            const avgKa = calcColumnAvg('ka[]');
            $('#statAvgKa').text(avgKa !== null ? avgKa.toFixed(2) + '%' : '-');
            $('#tblAvgKa').text(avgKa !== null ? avgKa.toFixed(2) + '%' : '-');

            // 5. Organo Percentage Breakdown
            const organoStats = calcOrganoPercentage('organo[]');
            if (organoStats.total > 0) {
                let summaryHtml = '';
                let tfootHtml = '';
                organoStats.list.forEach(item => {
                    const isOk = (item.category.toUpperCase() === 'OK');
                    const badgeClass = isOk ? 'bg-success' : 'bg-warning text-dark';
                    summaryHtml += `<span class="badge ${badgeClass} fs-7" title="${item.count} dari ${organoStats.total} sampel">
                        ${item.category}: <strong>${item.percent.toFixed(0)}%</strong> (${item.count}/${organoStats.total})
                    </span>`;
                    tfootHtml += `<span class="badge ${badgeClass} fs-8" title="${item.count} dari ${organoStats.total} sampel">
                        ${item.category}: ${item.percent.toFixed(0)}%
                    </span>`;
                });
                $('#statOrganoPercent').html(summaryHtml);
                $('#tblOrganoPercent').html(tfootHtml);
            } else {
                $('#statOrganoPercent').html('<span class="text-muted">-</span>');
                $('#tblOrganoPercent').html('<span class="text-muted small">-</span>');
            }
        } else if (currentType === 'garam-gula') {
            const avgKa = calcColumnAvg('%ka[]');
            $('#tblAvgKaGG').text(avgKa !== null ? avgKa.toFixed(2) + '%' : '-');

            const avgKotoran = calcColumnAvg('kotoran[]');
            $('#tblAvgKotoranGG').text(avgKotoran !== null ? avgKotoran.toFixed(3) : '-');

            const avgNacl = calcColumnAvg('%nacl[]');
            $('#tblAvgNaclGG').text(avgNacl !== null ? avgNacl.toFixed(2) + '%' : '-');

            const avgWeight = calcColumnAvg('gross_weight[]');
            $('#tblAvgWeightGG').text(avgWeight !== null ? avgWeight.toFixed(2) + ' kg' : '-');

            const organoStats = calcOrganoPercentage('organo[]');
            if (organoStats.total > 0) {
                let summaryHtml = '';
                organoStats.list.forEach(item => {
                    const isOk = (item.category.toUpperCase() === 'OK');
                    const badgeClass = isOk ? 'bg-success' : 'bg-warning text-dark';
                    summaryHtml += `<span class="badge ${badgeClass} fs-8">
                        ${item.category}: ${item.percent.toFixed(0)}%
                    </span>`;
                });
                $('#tblOrganoPercentGG').html(summaryHtml);
            } else {
                $('#tblOrganoPercentGG').html('<span class="text-muted small">-</span>');
            }
        }
    }

    function calcColumnAvg(colName) {
        let sum = 0;
        let count = 0;
        $(`[name="${colName}"]`).each(function() {
            let val = $(this).val();
            if (val !== undefined && val !== null && val.toString().trim() !== '') {
                val = val.toString().replace(/,/g, '.').trim();
                const num = parseFloat(val);
                if (!isNaN(num)) {
                    sum += num;
                    count++;
                }
            }
        });
        return count > 0 ? (sum / count) : null;
    }

    function calcOrganoPercentage(colName) {
        let total = 0;
        const counts = {};

        $(`[name="${colName}"]`).each(function() {
            const val = ($(this).val() || '').toString().trim();
            if (val) {
                total++;
                let cat = val;
                if (val.toUpperCase().startsWith('LAIN-LAIN')) {
                    cat = 'Lain-lain';
                } else if (val.toUpperCase().startsWith('CAMPURAN')) {
                    cat = 'Campuran';
                } else if (val.toUpperCase() === 'OK' || val.toUpperCase() === 'SESUAI' || val.toUpperCase() === 'NORMAL') {
                    cat = 'OK';
                }
                counts[cat] = (counts[cat] || 0) + 1;
            }
        });

        const list = [];
        for (const [category, count] of Object.entries(counts)) {
            const percent = total > 0 ? ((count / total) * 100) : 0;
            list.push({
                category,
                count,
                percent
            });
        }

        list.sort((a, b) => {
            if (a.category === 'OK') return -1;
            if (b.category === 'OK') return 1;
            return b.count - a.count;
        });

        return {
            total,
            list
        };
    }

    // ─────────────────────────────────────────────
    // HELPER: CLEAN NUMERIC, DELIMITERS & DETECT HEADERS
    // ─────────────────────────────────────────────
    function splitLineColumns(line) {
        if (!line) return [];
        let trimmed = line.trim();
        if (trimmed.includes('\t')) {
            return line.split('\t').map(c => c.replace(/^["']|["']$/g, '').trim());
        }
        if (trimmed.includes(';')) {
            return trimmed.split(';').map(c => c.replace(/^["']|["']$/g, '').trim());
        }
        if (/\s{2,}/.test(trimmed)) {
            return trimmed.split(/\s{2,}/).map(c => c.replace(/^["']|["']$/g, '').trim());
        }
        return [trimmed.replace(/^["']|["']$/g, '')];
    }

    function cleanNumericValue(val) {
        if (val === undefined || val === null) return '';
        let str = val.toString().trim();
        if (!str) return '';
        // Remove outer quotes e.g. "65,5"
        str = str.replace(/^["']|["']$/g, '').trim();
        // Replace commas with dots
        str = str.replace(/,/g, '.');
        // Extract numeric digits with optional decimal point (e.g. "65.5 °Bx" -> "65.5", "pH 5.5" -> "5.5", "0.05%" -> "0.05")
        const match = str.match(/-?\d+(?:\.\d+)?/);
        return match ? match[0] : str.replace(/[^0-9.-]/g, '');
    }

    function isHeaderRow(rowCols) {
        if (!rowCols || !rowCols.length) return false;
        const headerKeywords = [
            'BRIX', 'PH', 'KOTORAN', 'KA', '%KA', 'ORGANO', 'WARNA', 'AROMA',
            'SAMPEL', 'SAMPLE', 'NO', 'NO.', '#', 'FISIK', 'NACL', '%NACL', 'GROSS WEIGHT', 'WEIGHT'
        ];
        let matchCount = 0;
        for (const col of rowCols) {
            const clean = (col || '').toString().trim().toUpperCase();
            if (headerKeywords.includes(clean)) {
                matchCount++;
            }
        }
        return matchCount > 0 && rowCols.some(c => isNaN(parseFloat((c || '').toString().replace(/,/g, '.'))));
    }

    function hasSampleNoColumn(rowCols, headerCols = null) {
        if (!rowCols || !rowCols.length) return false;
        if (headerCols && headerCols.length > 0) {
            const firstHeader = (headerCols[0] || '').toString().trim().toUpperCase();
            const sampleKeywords = ['NO', 'NO.', '#', 'SAMPEL', 'SAMPLE', 'ID', 'NUM', 'BARIS', 'URUT', 'N0'];
            if (sampleKeywords.includes(firstHeader)) {
                return true;
            }
        }
        const firstVal = (rowCols[0] || '').toString().trim().replace(/^#/, '');
        const num = parseInt(firstVal, 10);
        if (!isNaN(num) && num >= 1 && num <= 100 && firstVal === num.toString() && rowCols.length > 1) {
            const secondVal = (rowCols[1] || '').toString().trim().replace(/,/g, '.');
            const secondNum = parseFloat(secondVal);
            if (!isNaN(secondNum) && (secondNum > 3 || secondVal.includes('.'))) {
                return true;
            }
        }
        return false;
    }

    // ─────────────────────────────────────────────
    // EXCEL-STYLE TABLE SELECTION & NAVIGATION SYSTEM
    // ─────────────────────────────────────────────
    let tableSelection = null; // { startRow, startCol, endRow, endCol, minRow, maxRow, minCol, maxCol }
    let isMouseDragging = false;
    let selectionAnchor = null;

    function getFields() {
        return currentType === 'short-term' ? SHORT_TERM_FIELDS : (currentType === 'garam-gula' ? GARAM_GULA_FIELDS : []);
    }

    function getCellCoords(el) {
        const $el = $(el);
        const $td = $el.closest('td');
        const $tr = $el.closest('tr');
        if (!$td.length || !$tr.length || !$tr.parent().is('tbody')) return null;
        const colIdx = $td.index() - 1; // 0-indexed data column (index 0 in DOM is td.td-sampel)
        const rowIdx = $tr.index();
        if (colIdx < 0) return null;
        return { row: rowIdx, col: colIdx };
    }

    function getCellTd(row, col) {
        return $('#analisaAccordion').find('table tbody tr').eq(row).find('td').eq(col + 1);
    }

    function getCellInput(row, col) {
        const $td = getCellTd(row, col);
        if (!$td.length) return $();
        if ($td.find('.organo-cell-container').length) {
            const $customBox = $td.find('.organo-custom-input-box');
            if ($customBox.is(':visible')) {
                return $td.find('.input-organo-custom-text');
            }
            return $td.find('.select-organo-dropdown');
        }
        return $td.find('input, select').first();
    }

    function focusCell(row, col, doSelectText = true) {
        const fields = getFields();
        if (!fields.length) return;
        if (row < 0) row = 0;
        if (row >= currentJumlah) row = currentJumlah - 1;
        if (col < 0) col = 0;
        if (col >= fields.length) col = fields.length - 1;

        const $input = getCellInput(row, col);
        if ($input.length) {
            $input.focus();
            if (doSelectText && $input.is('input:not([type=checkbox]):not([type=radio])')) {
                try { $input[0].select(); } catch(e) {}
            }
            selectionAnchor = { type: 'cell', row, col };
            setSelection(row, col, row, col);
        }
    }

    function setSelection(startRow, startCol, endRow, endCol) {
        tableSelection = {
            startRow,
            startCol,
            endRow,
            endCol,
            minRow: Math.min(startRow, endRow),
            maxRow: Math.max(startRow, endRow),
            minCol: Math.min(startCol, endCol),
            maxCol: Math.max(startCol, endCol)
        };
        renderSelection();
    }

    function clearSelection() {
        tableSelection = null;
        $('#analisaAccordion table td').removeClass('cell-selected cell-selected-top cell-selected-bottom cell-selected-left cell-selected-right');
    }

    function renderSelection() {
        $('#analisaAccordion table td').removeClass('cell-selected cell-selected-top cell-selected-bottom cell-selected-left cell-selected-right');
        if (!tableSelection) return;

        const { minRow, maxRow, minCol, maxCol } = tableSelection;
        const isMulti = (minRow !== maxRow || minCol !== maxCol);

        if (!isMulti) return; // single cell selection uses standard focus outline

        for (let r = minRow; r <= maxRow; r++) {
            for (let c = minCol; c <= maxCol; c++) {
                const $td = getCellTd(r, c);
                $td.addClass('cell-selected');
                if (r === minRow) $td.addClass('cell-selected-top');
                if (r === maxRow) $td.addClass('cell-selected-bottom');
                if (c === minCol) $td.addClass('cell-selected-left');
                if (c === maxCol) $td.addClass('cell-selected-right');
            }
        }
    }

    function clearSelectedCellsContent() {
        if (!tableSelection) return;
        const { minRow, maxRow, minCol, maxCol } = tableSelection;
        const fields = getFields();

        for (let r = minRow; r <= maxRow; r++) {
            for (let c = minCol; c <= maxCol; c++) {
                const field = fields[c];
                if (!field) continue;
                const $td = getCellTd(r, c);
                if (field.type === 'organo-select') {
                    const $container = $td.find('.organo-cell-container');
                    applyOrganoValueToCell($container, '');
                } else {
                    const $el = $td.find(`[name="${field.key}"]`);
                    if ($el.length) {
                        $el.val('').trigger('input').trigger('change');
                    }
                }
            }
        }
        calculateStatistics();
        saveDraft();
    }

    // ─────────────────────────────────────────────
    // FOOLPROOF VERTICAL TABLE NAVIGATION (ENTER / TAB)
    // ─────────────────────────────────────────────
    function navigateTableVertical(currentEl, direction) {
        const $el = $(currentEl);
        const $td = $el.closest('td');
        const $tr = $td.closest('tr');
        const $tbody = $tr.closest('tbody');
        
        if (!$td.length || !$tr.length || !$tbody.length) return;

        const colIdx = $td.index(); // 0 is td.td-sampel, 1 is Col 1, 2 is Col 2...
        const rowIdx = $tr.index(); // 0 is Sample 1, 1 is Sample 2...
        const totalRows = $tbody.find('tr').length;
        const totalCols = $tr.find('td').length;

        let targetRow = rowIdx + direction;
        let targetCol = colIdx;

        if (direction > 0) {
            // Moving DOWN
            if (targetRow >= totalRows) {
                // Reached last sample row: wrap to top (row 0) of NEXT data column
                targetRow = 0;
                targetCol = colIdx + 1;
                if (targetCol >= totalCols) {
                    // Reached end of table: focus Disposisi dropdown
                    $('select[name="disposisi"]').focus();
                    return;
                }
            }
        } else {
            // Moving UP
            if (targetRow < 0) {
                // Reached top row: wrap to bottom of PREVIOUS data column
                targetRow = totalRows - 1;
                targetCol = colIdx - 1;
                if (targetCol <= 0) {
                    targetRow = 0;
                    targetCol = 1;
                }
            }
        }

        const $targetRowEl = $tbody.find('tr').eq(targetRow);
        const $targetTd = $targetRowEl.find('td').eq(targetCol);

        if ($targetTd.length) {
            let $targetInput = $targetTd.find('.organo-cell-container select, .organo-cell-container input:visible, select, input').filter(':visible').first();
            if (!$targetInput.length) {
                $targetInput = $targetTd.find('input, select').first();
            }

            if ($targetInput.length) {
                $targetInput.focus();
                if ($targetInput.is('input:not([type=checkbox]):not([type=radio])')) {
                    try { $targetInput[0].select(); } catch(err) {}
                }
                const dataColIdx = targetCol - 1;
                setSelection(targetRow, dataColIdx, targetRow, dataColIdx);
            }
        }
    }

    // ─────────────────────────────────────────────
    // KEYBOARD NAVIGATION (Enter/Tab Downwards, Shift+Arrows, Ctrl+D, Ctrl+A)
    // ─────────────────────────────────────────────
    function handleTableKeydown(e) {
        const isInsideTable = $(e.target).closest('#analisaAccordion table, .analisa-table').length > 0;
        const hasSelection = (tableSelection !== null);

        if (!isInsideTable && !hasSelection) return;

        // 1. ENTER / TAB: Strictly Navigate Downwards (Shift: Upwards)
        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            e.stopPropagation();
            navigateTableVertical(e.target, e.shiftKey ? -1 : 1);
            return false;
        }

        // 3. CTRL + D: Fill Down (Copy top row / above row value to all selected rows)
        if ((e.ctrlKey || e.metaKey) && (e.key === 'd' || e.key === 'D')) {
            e.preventDefault();
            e.stopPropagation();

            let minR, maxR, minC, maxC;
            if (tableSelection) {
                minR = tableSelection.minRow;
                maxR = tableSelection.maxRow;
                minC = tableSelection.minCol;
                maxC = tableSelection.maxCol;
            } else {
                minR = row;
                maxR = row;
                minC = col;
                maxC = col;
            }

            const $table = $('#analisaAccordion').find('table');
            const $rows = $table.find('tbody tr');

            if (minR === maxR) {
                // If only 1 row is selected and row > 0, copy from row above (minR - 1)
                if (minR > 0) {
                    for (let c = minC; c <= maxC; c++) {
                        const field = fields[c];
                        if (!field) continue;
                        const $sourceRow = $rows.eq(minR - 1);
                        let sourceVal = '';
                        if (field.type === 'organo-select') {
                            sourceVal = $sourceRow.find('.input-organo-final').val() || '';
                        } else {
                            sourceVal = $sourceRow.find(`[name="${field.key}"]`).val() || '';
                        }
                        const $targetRow = $rows.eq(minR);
                        applyFieldValueToCell($targetRow, field, sourceVal);
                    }
                }
            } else {
                // Multi-row selection: copy top row (minR) to all rows below it (minR + 1 to maxR)
                for (let c = minC; c <= maxC; c++) {
                    const field = fields[c];
                    if (!field) continue;
                    const $sourceRow = $rows.eq(minR);
                    let sourceVal = '';
                    if (field.type === 'organo-select') {
                        sourceVal = $sourceRow.find('.input-organo-final').val() || '';
                    } else {
                        sourceVal = $sourceRow.find(`[name="${field.key}"]`).val() || '';
                    }

                    for (let r = minR + 1; r <= maxR; r++) {
                        const $targetRow = $rows.eq(r);
                        applyFieldValueToCell($targetRow, field, sourceVal);
                    }
                }
            }

            renderSelection();
            calculateStatistics();
            saveDraft();
            return false;
        }

        // 4. ARROW KEYS (Up / Down) without shift
        if (e.key === 'ArrowDown' && !e.shiftKey && !e.altKey && !e.ctrlKey) {
            if ($(e.target).is('select')) {
                return; // Let select change option natively
            }
            if (row < currentJumlah - 1) {
                e.preventDefault();
                focusCell(row + 1, col);
            }
            return;
        }
        if (e.key === 'ArrowUp' && !e.shiftKey && !e.altKey && !e.ctrlKey) {
            if ($(e.target).is('select')) {
                return; // Let select change option natively
            }
            if (row > 0) {
                e.preventDefault();
                focusCell(row - 1, col);
            }
            return;
        }

        // 5. ARROW KEYS (Left / Right) without shift (Horizontal cell jump at boundaries)
        if (e.key === 'ArrowRight' && !e.shiftKey && !e.altKey && !e.ctrlKey) {
            const isSelect = $(e.target).is('select');
            const isInput = $(e.target).is('input');
            const isAtEnd = isInput && e.target.selectionStart === (e.target.value || '').length && e.target.selectionEnd === (e.target.value || '').length;
            if (isSelect || isAtEnd) {
                if (col < fields.length - 1) {
                    e.preventDefault();
                    focusCell(row, col + 1);
                }
            }
            return;
        }
        if (e.key === 'ArrowLeft' && !e.shiftKey && !e.altKey && !e.ctrlKey) {
            const isSelect = $(e.target).is('select');
            const isInput = $(e.target).is('input');
            const isAtStart = isInput && e.target.selectionStart === 0 && e.target.selectionEnd === 0;
            if (isSelect || isAtStart) {
                if (col > 0) {
                    e.preventDefault();
                    focusCell(row, col - 1);
                }
            }
            return;
        }

        // 6. SHIFT + ARROW KEYS: Expand / Contract Block Selection
        if (e.shiftKey && ['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
            if (!selectionAnchor || selectionAnchor.row === undefined) selectionAnchor = { type: 'cell', row, col };
            let endRow = tableSelection ? tableSelection.endRow : row;
            let endCol = tableSelection ? tableSelection.endCol : col;

            if (e.key === 'ArrowDown') endRow = Math.min(currentJumlah - 1, endRow + 1);
            if (e.key === 'ArrowUp') endRow = Math.max(0, endRow - 1);
            if (e.key === 'ArrowRight') endCol = Math.min(fields.length - 1, endCol + 1);
            if (e.key === 'ArrowLeft') endCol = Math.max(0, endCol - 1);

            setSelection(selectionAnchor.row, selectionAnchor.col, endRow, endCol);
            return;
        }

        // 7. CTRL + A: Select All Data Cells in Table
        if ((e.ctrlKey || e.metaKey) && (e.key === 'a' || e.key === 'A') && isInsideTable) {
            e.preventDefault();
            selectionAnchor = { type: 'cell', row: 0, col: 0 };
            setSelection(0, 0, currentJumlah - 1, fields.length - 1);
            return;
        }

        // 8. DELETE / BACKSPACE: Clear Selected Block
        if ((e.key === 'Delete' || e.key === 'Backspace') && tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol)) {
            e.preventDefault();
            clearSelectedCellsContent();
            return;
        }
    }

    // ─────────────────────────────────────────────
    // MOUSE DRAG SELECTION HANDLERS
    // ─────────────────────────────────────────────
    function handleTableMouseDown(e) {
        if (e.which !== 1) return; // left click only
        const $target = $(this);

        // Header click (select entire column or columns)
        if ($target.is('th') || $target.hasClass('th-data-col') || $target.closest('th').length) {
            const $th = $target.is('th') ? $target : $target.closest('th');
            const colIdx = $th.index() - 1;
            if (colIdx >= 0) {
                selectionAnchor = { type: 'col', col: colIdx };
                isMouseDragging = true;
                setSelection(0, colIdx, currentJumlah - 1, colIdx);
            }
            return;
        }

        // Row header click (sample number: select entire row)
        if ($target.hasClass('td-sampel') || $target.closest('.td-sampel').length) {
            const $td = $target.hasClass('td-sampel') ? $target : $target.closest('.td-sampel');
            const rowIdx = $td.closest('tr').index();
            if (rowIdx >= 0) {
                const fields = getFields();
                selectionAnchor = { type: 'row', row: rowIdx };
                isMouseDragging = true;
                setSelection(rowIdx, 0, rowIdx, fields.length - 1);
            }
            return;
        }

        // Data cell click
        const coords = getCellCoords(this);
        if (!coords) return;

        if (e.shiftKey && selectionAnchor && selectionAnchor.row !== undefined) {
            e.preventDefault();
            setSelection(selectionAnchor.row, selectionAnchor.col, coords.row, coords.col);
        } else {
            selectionAnchor = { type: 'cell', ...coords };
            isMouseDragging = true;
            setSelection(coords.row, coords.col, coords.row, coords.col);
        }
    }

    function handleTableMouseEnter(e) {
        if (!isMouseDragging || !selectionAnchor) return;
        const $target = $(this);

        if (selectionAnchor.type === 'col') {
            const $th = $target.is('th') ? $target : $target.closest('th');
            if ($th.length) {
                const colIdx = $th.index() - 1;
                if (colIdx >= 0) {
                    setSelection(0, selectionAnchor.col, currentJumlah - 1, colIdx);
                }
            }
            return;
        }

        if (selectionAnchor.type === 'row') {
            const $td = $target.is('td') ? $target : $target.closest('td');
            if ($td.length) {
                const rowIdx = $td.closest('tr').index();
                if (rowIdx >= 0) {
                    const fields = getFields();
                    setSelection(selectionAnchor.row, 0, rowIdx, fields.length - 1);
                }
            }
            return;
        }

        const coords = getCellCoords(this);
        if (!coords) return;
        setSelection(selectionAnchor.row, selectionAnchor.col, coords.row, coords.col);
    }

    function handleTableMouseUp() {
        isMouseDragging = false;
    }

    function handleDocMouseDown(e) {
        if (!$(e.target).closest('#analisaAccordion table').length) {
            clearSelection();
        }
    }

    // ─────────────────────────────────────────────
    // COPY HANDLER (Ctrl+C on selected range / focused cell)
    // ─────────────────────────────────────────────
    function handleTableCopy(e) {
        const fields = getFields();
        if (!fields.length) return;

        let targetRange = null;

        if (tableSelection) {
            targetRange = tableSelection;
        } else {
            const coords = getCellCoords(document.activeElement);
            if (coords) {
                targetRange = {
                    minRow: coords.row,
                    maxRow: coords.row,
                    minCol: coords.col,
                    maxCol: coords.col
                };
            }
        }

        if (!targetRange) return;

        const { minRow, maxRow, minCol, maxCol } = targetRange;
        const rowsText = [];

        for (let r = minRow; r <= maxRow; r++) {
            const rowVals = [];
            for (let c = minCol; c <= maxCol; c++) {
                const field = fields[c];
                let val = '';
                if (field) {
                    const $td = getCellTd(r, c);
                    if (field.type === 'organo-select') {
                        val = $td.find('.input-organo-final').val() || '';
                    } else {
                        val = $td.find(`[name="${field.key}"]`).val() || '';
                    }
                }
                rowVals.push(val);
            }
            rowsText.push(rowVals.join('\t'));
        }

        const copyString = rowsText.join('\r\n');
        const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
        if (clipboardData) {
            e.preventDefault();
            clipboardData.setData('text/plain', copyString);

            // Highlight copied cells
            for (let r = minRow; r <= maxRow; r++) {
                for (let c = minCol; c <= maxCol; c++) {
                    const $td = getCellTd(r, c);
                    highlightCell($td.find('input, select'));
                }
            }
        }
    }

    function getPasteTargetCoords(e) {
        // 1. If custom tableSelection exists:
        if (tableSelection) {
            return { row: tableSelection.minRow, col: tableSelection.minCol };
        }

        // 2. Check window.getSelection() anchorNode (for native browser text drag / selection across table / th headers)
        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            const anchorNode = sel.anchorNode;
            if (anchorNode) {
                const $node = $(anchorNode.nodeType === 3 ? anchorNode.parentElement : anchorNode);
                const $th = $node.closest('th');
                if ($th.length) {
                    const colIdx = Math.max(0, $th.index() - 1);
                    return { row: 0, col: colIdx };
                }
                const $td = $node.closest('td');
                if ($td.length && $td.closest('tbody').length) {
                    const colIdx = Math.max(0, $td.index() - 1);
                    const rowIdx = $td.closest('tr').index();
                    return { row: Math.max(0, rowIdx), col: Math.max(0, colIdx) };
                }
            }
        }

        // 3. Check event target or activeElement
        let $target = $(e.target).length ? $(e.target) : $(document.activeElement);
        if ($target.length) {
            const $th = $target.closest('th');
            if ($th.length) {
                const colIdx = Math.max(0, $th.index() - 1);
                return { row: 0, col: colIdx };
            }
            const $td = $target.closest('td');
            if ($td.length && $td.closest('tbody').length) {
                const colIdx = Math.max(0, $td.index() - 1);
                const rowIdx = $td.closest('tr').index();
                return { row: Math.max(0, rowIdx), col: Math.max(0, colIdx) };
            }
            const colName = $target.attr('name');
            if (colName) {
                const fields = getFields();
                const colIdx = fields.findIndex(f => f.key === colName);
                if (colIdx !== -1) {
                    const rowIdx = $target.closest('tr').index();
                    return { row: Math.max(0, rowIdx), col: colIdx };
                }
            }
        }

        // 4. Fallback: row 0, col 0 (first data cell)
        return { row: 0, col: 0 };
    }

    // ─────────────────────────────────────────────
    // DIRECT TABLE CELL PASTE LISTENER (Ctrl+V)
    // ─────────────────────────────────────────────
    function handleDirectTablePaste(e) {
        if ($(e.target).is('textarea, #editJumlahInput, #jumlahData') && !$(e.target).closest('.analisa-table').length) {
            return;
        }

        const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
        if (!clipboardData) return;

        let text = clipboardData.getData('text/plain') || clipboardData.getData('text') || '';
        if (!text || !text.trim()) return;

        const coords = getPasteTargetCoords(e);
        let startRowIdx = coords.row;
        let startColIdx = coords.col;

        const fields = getFields();
        if (!fields.length) return;

        let lines = text.split(/\r?\n/).filter(l => l.trim().length > 0);
        if (!lines.length) return;

        let rawRows = lines.map(l => splitLineColumns(l));

        // Strip leading/trailing empty cols (SAP / Excel copy artifact)
        if (rawRows.every(r => r.length > 1 && r[0] === '')) {
            rawRows = rawRows.map(r => r.slice(1));
        }
        if (rawRows.every(r => r.length > 1 && r[r.length - 1] === '')) {
            rawRows = rawRows.map(r => r.slice(0, -1));
        }

        // Header detection (only when multiple rows are pasted)
        let headerRow = null;
        if (rawRows.length > 1 && isHeaderRow(rawRows[0])) {
            headerRow = rawRows[0];
            rawRows = rawRows.slice(1);
        }

        // If user pastes starting from column 0 and data includes Sample No column (1, 2, 3...)
        if (startColIdx === 0 && hasSampleNoColumn(rawRows[0], headerRow)) {
            rawRows = rawRows.map(r => r.slice(1));
        }

        e.preventDefault();
        e.stopPropagation();

        const $table = $('#analisaAccordion').find('table');
        const $rows = $table.find('tbody tr');

        // CASE 1: USER BLOCKED MULTIPLE CELLS (e.g. 4 columns x 3 rows)
        // In Excel, when pasting into a blocked range, data is tiled/replicated across all blocked rows & cols
        const hasMultiCellSelection = tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol);

        if (hasMultiCellSelection) {
            const { minRow, maxRow, minCol, maxCol } = tableSelection;

            for (let r = minRow; r <= maxRow; r++) {
                if (r >= currentJumlah) break;
                const $row = $rows.eq(r);
                if (!$row.length) continue;

                const rOffset = r - minRow;
                const clipRow = rawRows[rOffset % rawRows.length];

                for (let c = minCol; c <= maxCol; c++) {
                    if (c >= fields.length) break;
                    const cOffset = c - minCol;
                    const cellVal = clipRow[cOffset % clipRow.length];
                    const field = fields[c];

                    applyFieldValueToCell($row, field, cellVal);
                }
            }

            renderSelection();
            calculateStatistics();
            saveDraft();
            return;
        }

        // CASE 2: USER FOCUSED ON A SINGLE CELL (PASTE ACCORDING TO DATA DIMENSIONS)
        let maxColsCount = 1;
        let appliedRowsCount = 0;

        rawRows.forEach((rowCols, rOffset) => {
            const targetRowIdx = startRowIdx + rOffset;
            if (targetRowIdx >= currentJumlah) return; // Strictly ignore rows beyond current sample count
            const $row = $rows.eq(targetRowIdx);
            if (!$row.length) return;

            appliedRowsCount++;
            maxColsCount = Math.max(maxColsCount, rowCols.length);

            rowCols.forEach((cellVal, cOffset) => {
                const targetColIdx = startColIdx + cOffset;
                if (targetColIdx >= fields.length) return;
                const field = fields[targetColIdx];

                applyFieldValueToCell($row, field, cellVal);
            });
        });

        if (appliedRowsCount > 0) {
            setSelection(startRowIdx, startColIdx, Math.min(currentJumlah - 1, startRowIdx + appliedRowsCount - 1), Math.min(fields.length - 1, startColIdx + maxColsCount - 1));
        }

        calculateStatistics();
        saveDraft();
    }

    function applyFieldValueToCell($row, field, cellVal) {
        if (!$row || !$row.length || !field) return;
        let cleanVal = (cellVal || '').toString().trim();

        if (field.type === 'organo-select') {
            const $container = $row.find('.organo-cell-container');
            if ($container.length) {
                applyOrganoValueToCell($container, cleanVal);
                highlightCell($container.find('select, input'));
            }
        } else {
            const $targetEl = $row.find(`[name="${field.key}"]`);
            if ($targetEl.length) {
                if (field.type === 'dropdown') {
                    cleanVal = matchDropdownOption(cleanVal, getMasterOptions(field.masterKey));
                } else if (field.type === 'text') {
                    cleanVal = cleanVal.toUpperCase();
                } else {
                    cleanVal = cleanNumericValue(cleanVal);
                }
                $targetEl.val(cleanVal).trigger('input').trigger('change');
                highlightCell($targetEl);
            }
        }
    }

    function highlightCell($el) {
        if (!$el || !$el.length) return;
        $el.addClass('paste-highlight');
        setTimeout(() => {
            $el.removeClass('paste-highlight');
        }, 1200);
    }

    function applyOrganoValueToCell($container, cleanVal) {
        const $select = $container.find('.select-organo-dropdown');
        const $customBox = $container.find('.organo-custom-input-box');
        const $customInput = $container.find('.input-organo-custom-text');
        const $finalInput = $container.find('.input-organo-final');

        if (!cleanVal) {
            $select.val('');
            $customBox.hide();
            $customInput.val('');
            $finalInput.val('');
            return;
        }

        cleanVal = cleanVal.toString().replace(/^["']|["']$/g, '').trim();
        const organoOpts = getMasterOptions('organo');
        const matched = matchDropdownOption(cleanVal, organoOpts, true);

        if (matched === 'Lain-lain' || matched === 'Campuran') {
            $select.val(matched);
            $customBox.show();
            $customInput.val(cleanVal);
            $finalInput.val(cleanVal);
        } else {
            $select.val(matched);
            $customBox.hide();
            $customInput.val('');
            $finalInput.val(matched);
        }
    }

    function matchDropdownOption(val, options, isOrgano = false) {
        if (!val) return '';
        let clean = val.toString().replace(/^["']|["']$/g, '').trim();
        const upper = clean.toUpperCase();

        for (const opt of options) {
            const optVal = (typeof opt === 'object' ? opt.value : opt).toString();
            if (optVal.toUpperCase() === upper) return optVal;
        }

        if (upper === 'OK' || upper === 'SESUAI' || upper === 'NORMAL' || upper === 'BAIK' || upper === 'SESUAI STANDAR') {
            const match = options.find(o => {
                const str = (typeof o === 'object' ? o.value : o).toUpperCase();
                return str === 'OK' || str.includes('SESUAI');
            });
            if (match) return typeof match === 'object' ? match.value : match;
        }

        if (upper.includes('TIDAK') || upper === 'NOT OK' || upper === 'NOK' || upper === 'KURANG') {
            const match = options.find(o => {
                const str = (typeof o === 'object' ? o.value : o).toUpperCase();
                return str.includes(upper) || upper.includes(str);
            });
            if (match) return typeof match === 'object' ? match.value : match;
        }

        for (const opt of options) {
            const optVal = (typeof opt === 'object' ? opt.value : opt).toString();
            if (optVal.toUpperCase().includes(upper) || upper.includes(optVal.toUpperCase())) {
                return optVal;
            }
        }

        if (isOrgano) {
            if (upper.startsWith('CAMPURAN')) return 'Campuran';
            return 'Lain-lain';
        }

        const first = options[0];
        return typeof first === 'object' ? first.value : (first || clean);
    }

    // ─────────────────────────────────────────────
    // CRYSTAL & DISPOSISI DYNAMIC LOGIC (LONG TERM)
    // ─────────────────────────────────────────────
    function handleCrystalChange() {
        const val = $(this).val();

        if (val === 'negatif') {
            $('#attachmentWrapper').hide();
            $('#disposisiPlaceholder').hide();
            $('#disposisiAutoWrapper').show();
            $('#disposisiManualWrapper').hide();
            $('#selectDisposisiLong').val('Release');
            $('#groupLongWrapper').slideDown(150);
        } else if (val === 'positif') {
            $('#attachmentWrapper').show();
            $('#disposisiPlaceholder').hide();
            $('#disposisiAutoWrapper').hide();
            $('#disposisiManualWrapper').show();

            const dispVal = $('#selectDisposisiLong').val();
            if (dispVal === 'Release' || dispVal === 'Release Bersyarat') {
                $('#groupLongWrapper').slideDown(150);
            } else {
                $('#groupLongWrapper').hide();
                $('#selectGroupLong').val('');
            }
        } else {
            $('#attachmentWrapper').hide();
            $('#disposisiPlaceholder').show();
            $('#disposisiAutoWrapper').hide();
            $('#disposisiManualWrapper').hide();
            $('#groupLongWrapper').hide();
            $('#selectGroupLong').val('');
        }

        saveDraft();
    }

    function handleDisposisiLongChange() {
        const dispVal = $(this).val();
        if (dispVal === 'Release' || dispVal === 'Release Bersyarat') {
            $('#groupLongWrapper').slideDown(150);
        } else {
            $('#groupLongWrapper').slideUp(150);
            $('#selectGroupLong').val('');
        }
        saveDraft();
    }

    // ─────────────────────────────────────────────
    // MULTI-PHOTO UPLOAD & PREVIEW (MAX 5 FOTO)
    // ─────────────────────────────────────────────
    function handlePhotoSelection(e) {
        const files = Array.from(e.target.files);
        if (!files.length) return;

        const maxTotal = 5;
        let currentTotal = selectedFiles.length + existingPhotos.length;

        for (const file of files) {
            if (currentTotal >= maxTotal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maksimal 5 Foto',
                    text: 'Anda hanya dapat mengunggah maksimal 5 foto lampiran.'
                });
                break;
            }

            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ukuran Terlalu Besar',
                    text: `File "${file.name}" melebihi ukuran maksimal 5MB.`
                });
                continue;
            }

            selectedFiles.push(file);
            currentTotal++;
        }

        $('#inputMultiAttachment').val('');
        renderPhotoPreviews();
        saveDraft();
    }

    function handleClearAllPhotos() {
        if (selectedFiles.length === 0 && existingPhotos.length === 0) return;

        Swal.fire({
            title: 'Hapus Semua Foto?',
            text: 'Semua foto lampiran kristal akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(res => {
            if (res.isConfirmed) {
                selectedFiles = [];
                existingPhotos = [];
                renderPhotoPreviews();
                saveDraft();
            }
        });
    }

    function renderPhotoPreviews() {
        const container = $('#photoPreviewContainer');
        container.empty();

        const totalPhotos = existingPhotos.length + selectedFiles.length;
        $('#photoCountBadge').text(`${totalPhotos} / 5 Foto`);
        if (totalPhotos >= 5) {
            $('#photoCountBadge').removeClass('bg-secondary').addClass('bg-success');
        } else {
            $('#photoCountBadge').removeClass('bg-success').addClass('bg-secondary');
        }

        let currentIdx = 1;

        existingPhotos.forEach((photoPath, i) => {
            const url = `{{ asset('storage/uploads/attachment_analisa') }}/${photoPath}`;
            const html = `
                <div class="photo-preview-item" title="${photoPath}">
                    <a href="${url}" target="_blank">
                        <img src="${url}" alt="Foto ${currentIdx}">
                    </a>
                    <button type="button" class="btn-remove-photo btn-remove-existing-photo" data-index="${i}" title="Hapus foto ini">
                        <i class="ri-close-line"></i>
                    </button>
                    <span class="photo-badge-idx">#${currentIdx}</span>
                </div>`;
            container.append(html);
            currentIdx++;
        });

        selectedFiles.forEach((file, i) => {
            const blobUrl = URL.createObjectURL(file);
            const html = `
                <div class="photo-preview-item" title="${file.name}">
                    <a href="${blobUrl}" target="_blank">
                        <img src="${blobUrl}" alt="Foto ${currentIdx}">
                    </a>
                    <button type="button" class="btn-remove-photo btn-remove-new-photo" data-index="${i}" title="Hapus foto ini">
                        <i class="ri-close-line"></i>
                    </button>
                    <span class="photo-badge-idx">#${currentIdx}</span>
                </div>`;
            container.append(html);
            currentIdx++;
        });
    }

    // ─────────────────────────────────────────────
    // SUBMIT (FINAL vs DRAFT)
    // ─────────────────────────────────────────────
    function submitForm(actionType) {
        const isDraft = (actionType === 'draft');

        if (!isDraft) {
            if (currentType === 'short-term') {
                let hasEmpty = false;
                const emptyFields = new Set();

                if (currentKategori === 'incoming') {
                    // For Incoming: ensure Brix and pH are filled on all samples
                    $('#formAnalisa').find('.analisa-table tbody tr').each(function(rowIdx) {
                        const $row = $(this);
                        const sampelNo = rowIdx + 1;

                        const $brix = $row.find('input[name="brix[]"]');
                        if ($brix.length && (!$brix.val() || !$brix.val().trim())) {
                            hasEmpty = true;
                            emptyFields.add('Brix (Sampel ' + sampelNo + ')');
                            $brix.addClass('is-invalid');
                        } else {
                            $brix.removeClass('is-invalid');
                        }

                        const $ph = $row.find('input[name="ph[]"]');
                        if ($ph.length && (!$ph.val() || !$ph.val().trim())) {
                            hasEmpty = true;
                            emptyFields.add('pH (Sampel ' + sampelNo + ')');
                            $ph.addClass('is-invalid');
                        } else {
                            $ph.removeClass('is-invalid');
                        }
                    });
                } else {
                    // STA & Monitoring: Flexible! Only Disposisi is required.
                    $('#formAnalisa').find('.analisa-table input, .analisa-table select').removeClass('is-invalid');
                }

                const $disp = $('select[name="disposisi"]');
                if (!$disp.val()) {
                    hasEmpty = true;
                    emptyFields.add('Disposisi');
                    $disp.addClass('is-invalid');
                } else {
                    $disp.removeClass('is-invalid');
                }

                if (hasEmpty) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        html: (currentKategori === 'incoming'
                            ? 'Untuk analisa Incoming, parameter Brix, pH, dan Disposisi wajib diisi:<br><br>'
                            : 'Disposisi wajib dipilih:<br><br>') +
                            [...emptyFields].map(f => `<span class="badge bg-danger me-1 mb-1">${f}</span>`).join(''),
                        confirmButtonText: 'Oke',
                    });
                }
            } else if (currentType === 'garam-gula') {
                let hasEmpty = false;
                const emptyFields = new Set();

                $('#formAnalisa').find('.analisa-table tbody tr').each(function(rowIdx) {
                    const $row = $(this);
                    const sampelNo = rowIdx + 1;
                    const $fisik = $row.find('input[name="fisik[]"]');
                    if ($fisik.length && (!$fisik.val() || !$fisik.val().trim())) {
                        hasEmpty = true;
                        emptyFields.add('Fisik (Sampel ' + sampelNo + ')');
                        $fisik.addClass('is-invalid');
                    } else {
                        $fisik.removeClass('is-invalid');
                    }
                });

                const $disp = $('select[name="disposisi"]');
                if (!$disp.val()) {
                    hasEmpty = true;
                    emptyFields.add('Disposisi');
                    $disp.addClass('is-invalid');
                } else {
                    $disp.removeClass('is-invalid');
                }

                if (hasEmpty) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        html: 'Field berikut masih kosong:<br><br>' + [...emptyFields].map(f => `<span class="badge bg-danger me-1 mb-1">${f}</span>`).join(''),
                        confirmButtonText: 'Oke',
                    });
                }
            } else if (currentType === 'long-term') {
                const ujiKristal = $('#selectUjiKristal').val();
                if (!ujiKristal) {
                    return Swal.fire({
                        icon: 'warning',
                        text: 'Pilih hasil uji kristal terlebih dahulu.'
                    });
                }

                if (ujiKristal === 'positif') {
                    const totalPhotos = selectedFiles.length + existingPhotos.length;
                    if (totalPhotos === 0) {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'Lampiran Foto Diperlukan',
                            text: 'Lampirkan minimal 1 foto kristal (maksimal 5 foto) untuk uji kristal positif.'
                        });
                    }

                    const disposisi = $('#selectDisposisiLong').val();
                    if (!disposisi) {
                        return Swal.fire({
                            icon: 'warning',
                            text: 'Pilih disposisi terlebih dahulu.'
                        });
                    }

                    if (['Release', 'Release Bersyarat'].includes(disposisi)) {
                        const group = $('#selectGroupLong').val();
                        if (!group) {
                            return Swal.fire({
                                icon: 'warning',
                                text: 'Pilih Group ABC untuk disposisi Release / Release Bersyarat.'
                            });
                        }
                    }
                } else if (ujiKristal === 'negatif') {
                    const group = $('#selectGroupLong').val();
                    if (!group) {
                        return Swal.fire({
                            icon: 'warning',
                            text: 'Pilih Group ABC untuk hasil analisa ini.'
                        });
                    }
                }
            }
        }

        const formData = new FormData(document.getElementById('formAnalisa'));
        formData.set('save_action', actionType);
        formData.set('kategori', currentKategori);

        if (currentType === 'long-term') {
            const ujiKristalVal = $('#selectUjiKristal').val();
            formData.set('uji_kristal', ujiKristalVal || '');

            if (ujiKristalVal === 'negatif') {
                formData.set('disposisi', 'Release');
            } else {
                formData.set('disposisi', $('#selectDisposisiLong').val() || '');
            }

            formData.set('group', $('#selectGroupLong').val() || '');
            formData.set('keterangan', $('#keteranganLong').val() || '');

            selectedFiles.forEach(file => {
                formData.append('attachments[]', file);
            });
            formData.set('existing_attachments', JSON.stringify(existingPhotos));
        }

        const urlMap = {
            'short-term': "{{ route('rmpm.store.short-term') }}",
            'long-term': "{{ route('rmpm.store.long-term') }}",
            'garam-gula': "{{ route('rmpm.store.garam-gula') }}",
        };

        Swal.fire({
            title: isDraft ? 'Menyimpan Sementara...' : 'Menyimpan Analisa...',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: urlMap[currentType],
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                clearDraft();
                if (isDraft) {
                    $('#draftStatusBadge').show();
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan Sementara',
                        text: resp.message || 'Data analisa berhasil disimpan sementara (Draft).'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: resp.message || 'Data analisa berhasil disimpan!'
                    }).then(() => {
                        window.location.href = "{{ route('rmpm.show', $identitas->id) }}";
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
                });
            },
        });
    }

    // ─────────────────────────────────────────────
    // LOCALSTORAGE DRAFT CACHE
    // ─────────────────────────────────────────────
    function saveDraft() {
        if (!currentType) return;
        const fields = {};

        $('#formAnalisa').find('input:not([type=file]), select, textarea').each(function() {
            const name = $(this).attr('name');
            if (!name || name === '_token') return;
            if (name.endsWith('[]')) {
                if (!fields[name]) fields[name] = [];
                fields[name].push($(this).val());
            } else {
                fields[name] = $(this).val();
            }
        });

        localStorage.setItem(DRAFT_KEY, JSON.stringify({
            setup: {
                type: currentType,
                kategori: currentKategori,
                jumlah: currentJumlah
            },
            fields,
        }));
    }

    function loadFieldsFromDraft(fields) {
        setTimeout(() => {
            for (const [name, value] of Object.entries(fields)) {
                if (['_token', 'id_identitas', 'jenis', 'analisa_type', 'kategori'].includes(name)) continue;
                if (name === 'organo[]' && Array.isArray(value)) {
                    $('.organo-cell-container').each(function(i) {
                        if (value[i] !== undefined) {
                            applyOrganoValueToCell($(this), value[i]);
                        }
                    });
                } else {
                    const $els = $(`[name="${name}"]`);
                    if (!$els.length) continue;
                    if (Array.isArray(value)) {
                        $els.each(function(i) {
                            if (value[i] !== undefined) $(this).val(value[i]);
                        });
                    } else {
                        $els.val(value);
                    }
                }
            }
            if ($('#selectUjiKristal').length && $('#selectUjiKristal').val()) {
                $('#selectUjiKristal').trigger('change');
            }
            if ($('#selectDisposisiLong').length && $('#selectDisposisiLong').val()) {
                $('#selectDisposisiLong').trigger('change');
            }
            calculateStatistics();
        }, 150);
    }

    function restoreFromDraft() {
        let draft;
        try {
            draft = JSON.parse(localStorage.getItem(DRAFT_KEY));
        } catch {
            return;
        }
        if (!draft?.setup?.type) return;

        currentType = draft.setup.type;
        currentKategori = draft.setup.kategori || 'incoming';
        currentJumlah = draft.setup.jumlah;

        if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
            const radioVal = currentType === 'long-term' ? 'long-term' : currentKategori;
            $(`input[name="analisa_type"][value="${radioVal}"]`).prop('checked', true).trigger('change');
        }
        $('#jumlahData').val(currentJumlah);

        startForm(currentType, currentJumlah, currentKategori);

        if (draft.fields) loadFieldsFromDraft(draft.fields);
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }
</script>
@endsection