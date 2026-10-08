@extends('layouts.component.main')
@section('title', 'Form Analisa')

@section('content')
@php
    $jenisUpper = strtoupper(trim($identitas->jenis ?? ''));
    $isGaram = ($jenisUpper === 'GARAM');
    $isGulaKristal = (!$isGaram && (str_contains($jenisUpper, 'GULA') || str_contains($jenisUpper, 'TEBU') || str_contains($jenisUpper, 'KELAPA')));
@endphp
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
                                @if ($isGulaKristal)
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Pilih Kategori / Jenis Analisa</label>
                                    <div class="row g-2">
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katIncoming" value="incoming" checked>
                                            <label class="btn btn-outline-primary w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katIncoming">
                                                <i class="ri-inbox-archive-line fs-5 mb-1"></i>
                                                <span class="fw-bold">1. Incoming</span>
                                                <small class="text-muted" style="font-size:11px;">Wajib Lengkap</small>
                                                <span id="setupBadgeIncoming" class="badge bg-primary text-white mt-1" style="display:none; font-size:10px;"></span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katSta" value="sta">
                                            <label class="btn btn-outline-info w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katSta">
                                                <i class="ri-flashlight-line fs-5 mb-1"></i>
                                                <span class="fw-bold">2. STA</span>
                                                <small class="text-muted" style="font-size:11px;">Fleksibel / Parsial</small>
                                                <span id="setupBadgeSta" class="badge bg-info text-white mt-1" style="display:none; font-size:10px;"></span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katMonitoring" value="monitoring">
                                            <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katMonitoring">
                                                <i class="ri-line-chart-line fs-5 mb-1"></i>
                                                <span class="fw-bold">3. Monitoring</span>
                                                <small class="text-muted" style="font-size:11px;">Pemantauan Berkala</small>
                                                <span id="setupBadgeMonitoring" class="badge bg-success text-white mt-1" style="display:none; font-size:10px;"></span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <input type="radio" class="btn-check" name="analisa_type" id="katLongTerm" value="long-term">
                                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="katLongTerm">
                                                <i class="ri-microscope-line fs-5 mb-1"></i>
                                                <span class="fw-bold">4. Long Term</span>
                                                <small class="text-muted" style="font-size:11px;">Uji Kristal</small>
                                                <span id="setupBadgeLongTerm" class="badge bg-dark text-white mt-1" style="display:none; font-size:10px;"></span>
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
                                    <button type="button" class="btn btn-primary w-100" id="btnMulai">
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

                            @if ($isGulaKristal)
                            {{-- Category Switcher Bar --}}
                            <div class="card bg-light border p-2 mb-3 rounded-3" id="categorySwitcherNav">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="btnBackToSetup" title="Kembali ke pemilihan kategori">
                                            <i class="ri-arrow-left-line me-1"></i> Pilih Kategori Lain
                                        </button>
                                        <span class="text-muted small fw-bold text-uppercase me-2 d-none d-md-inline">
                                            <i class="ri-folder-transfer-line text-primary me-1"></i> Pindah Kategori:
                                        </span>
                                    </div>
                                    <div class="btn-group btn-group-sm flex-wrap" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-category-switch" data-cat="incoming">
                                            <i class="ri-inbox-archive-line me-1"></i> 1. Incoming <span id="badgeCatIncoming" class="badge bg-primary text-white ms-1" style="display:none;"></span>
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-category-switch" data-cat="sta">
                                            <i class="ri-flashlight-line me-1"></i> 2. STA <span id="badgeCatSta" class="badge bg-info text-white ms-1" style="display:none;"></span>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-category-switch" data-cat="monitoring">
                                            <i class="ri-line-chart-line me-1"></i> 3. Monitoring <span id="badgeCatMonitoring" class="badge bg-success text-white ms-1" style="display:none;"></span>
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-category-switch" data-cat="long-term">
                                            <i class="ri-microscope-line me-1"></i> 4. Long Term <span id="badgeCatLongTerm" class="ms-1" style="display:none;"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif

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
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnReset">
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



                            <form id="formAnalisa" enctype="multipart/form-data" novalidate>
                                @csrf
                                <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                                <input type="hidden" name="jenis" value="{{ $identitas->jenis }}">
                                <input type="hidden" name="analisa_type" id="hiddenAnalisaType">
                                <input type="hidden" name="kategori" id="hiddenKategori" value="incoming">
                                <input type="hidden" name="save_action" id="saveAction" value="final">

                                <div id="analisaAccordion"></div>

                                <div class="mt-4 d-flex gap-2 justify-content-end flex-wrap" id="mainFormActions">
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

<!-- Modal Log & Riwayat Transaksi Pemakaian Glassware (Audit Anti-Bentrok) -->
<div class="modal fade" id="modalGlasswareTransactions" tabindex="-1" aria-labelledby="modalGlasswareTransactionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="ri-history-line fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalGlasswareTransactionsLabel">
                            Log Transaksi Pemakaian Glassware
                        </h5>
                        <p class="mb-0 text-white-50 small">
                            Riwayat pemakaian Beaker (1-90) dan Cawan (1-240) hari ini per SPB untuk menghindari bentrok/double-booking antar analis & device.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- KPI / Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="ri-flask-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-medium">Total Pemakaian Beaker</div>
                                    <div class="fs-5 fw-bold text-dark" id="modalKpiBeaker">0 / 90</div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 10.5px;" id="modalKpiBeakerRemain">Sisa: 90 Unit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="ri-contrast-drop-2-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-medium">Total Pemakaian Cawan</div>
                                    <div class="fs-5 fw-bold text-dark" id="modalKpiCawan">0 / 240</div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 10.5px;" id="modalKpiCawanRemain">Sisa: 240 Unit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="ri-file-list-3-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-medium">Total Transaksi Hari Ini</div>
                                    <div class="fs-5 fw-bold text-dark" id="modalKpiTotalLogs">0 Transaksi</div>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace" style="font-size: 10.5px;">{{ \Carbon\Carbon::today()->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls & Search Bar -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <ul class="nav nav-pills gap-1" id="glasswareFilterTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active py-1.5 px-3 btn-gw-tab" data-filter="all" type="button">
                                        <i class="ri-apps-2-line me-1"></i> Semua Transaksi <span class="badge bg-secondary ms-1" id="cntAllGw">0</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1.5 px-3 btn-gw-tab" data-filter="beaker" type="button">
                                        <i class="ri-flask-line me-1"></i> Beaker (1-90) <span class="badge bg-primary ms-1" id="cntBeakerGw">0</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1.5 px-3 btn-gw-tab" data-filter="cawan" type="button">
                                        <i class="ri-contrast-drop-2-line me-1"></i> Cawan (1-240) <span class="badge bg-success ms-1" id="cntCawanGw">0</span>
                                    </button>
                                </li>
                            </ul>
                            <div class="d-flex align-items-center gap-2" style="min-width: 280px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="ri-search-line text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="inputSearchGwLogs" placeholder="Cari No. Alat, No. SPB, Analis...">
                                    <button class="btn btn-outline-secondary" type="button" id="btnRefreshGwLogs" title="Muat Ulang Data">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table of Glassware Transactions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                    <div class="table-responsive" style="max-height: 460px;">
                        <table class="table table-hover align-middle mb-0" id="tableGwTransactions">
                            <thead class="table-light sticky-top border-bottom">
                                <tr class="text-muted small text-uppercase">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th style="width: 110px;">Waktu</th>
                                    <th style="width: 160px;">No. Alat</th>
                                    <th>No. SPB / Identitas</th>
                                    <th>Bahan & Supplier</th>
                                    <th style="width: 110px;">Kategori</th>
                                    <th>Data Pengukuran</th>
                                    <th>Analis / PIC</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyGwTransactions">
                                <!-- Rendered dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-2.5 px-4 border-top">
                <div class="text-muted small me-auto">
                    <i class="ri-information-line me-1 text-primary"></i> Data otomatis disinkronkan secara real-time dengan transaksi QC & Lab.
                </div>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
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

    /* Worksheet Tabs & Calculation UI Styles */
    .nav-worksheet {
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fa;
        padding: 6px 8px 0 8px;
        border-radius: 8px 8px 0 0;
    }
    .nav-worksheet .nav-link {
        color: #495057;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 8px 16px;
        border: 1px solid transparent;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        margin-bottom: -2px;
        transition: all 0.2s ease;
    }
    .nav-worksheet .nav-link:hover {
        color: #0d6efd;
        background: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
    }
    .nav-worksheet .nav-link.active {
        color: #0d6efd;
        background: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        border-bottom: 2px solid #0d6efd;
    }
    .formula-banner {
        background: #f0f7ff;
        border: 1px solid #cce5ff;
        border-left: 4px solid #0d6efd;
        border-radius: 6px;
        padding: 10px 14px;
    }
    .formula-badge {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 11.5px;
        background: #e7f1ff;
        color: #0b5ed7;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #b6d4fe;
    }
    .highlight-out-of-spec {
        background-color: #ffebe9 !important;
        border-color: #ff8182 !important;
        color: #cf222e !important;
        font-weight: 700 !important;
    }
    .highlight-warning-spec {
        background-color: #fff8c5 !important;
        border-color: #d4a72c !important;
        color: #9a6700 !important;
        font-weight: 700 !important;
    }
    .tare-readonly-input {
        background-color: #f6f8fa !important;
        color: #57606a !important;
        font-family: monospace;
        font-size: 0.82rem;
        cursor: not-allowed;
    }
    .glassware-widget-card {
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .shadow-xs {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .quick-action-btn {
        transition: all 0.15s ease-in-out;
    }
    .quick-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    /* Wizard Stepper Styles */
    .wizard-stepper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        gap: 8px;
    }
    .wizard-step {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 8px 14px;
        border-radius: 10px;
        transition: all 0.2s ease;
        user-select: none;
        flex: 1;
        border: 1px solid transparent;
    }
    .wizard-step:hover {
        background: #f8fafc;
    }
    .wizard-step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .wizard-step-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .wizard-step-title {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wizard-step-desc {
        font-size: 11px;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Active Step */
    .wizard-step.active {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    .wizard-step.active .wizard-step-circle {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.2);
    }
    .wizard-step.active .wizard-step-title {
        color: #1d4ed8;
        font-weight: 700;
    }
    .wizard-step.active .wizard-step-desc {
        color: #3b82f6;
    }
    /* Completed Step */
    .wizard-step.completed .wizard-step-circle {
        background: #10b981;
        color: #ffffff;
    }
    .wizard-step.completed .wizard-step-title {
        color: #065f46;
    }
    /* Connector Line */
    .wizard-step-connector {
        width: 24px;
        height: 2px;
        background: #e2e8f0;
        flex-shrink: 0;
    }
    /* Highlight Flash Animation for Sync */
    @keyframes highlightFlash {
        0% { background-color: #fef08a; }
        50% { background-color: #bbf7d0; }
        100% { background-color: transparent; }
    }
    .highlight-flash {
        animation: highlightFlash 1.4s ease-in-out;
    }

    /* Automatically hide main bottom form action buttons when Tab 2 (Kotoran) or Tab 3 (Kadar Air) is active */
    #formAnalisa:has(#paneKotoran.active) #mainFormActions,
    #formAnalisa:has(#paneKadarAir.active) #mainFormActions {
        display: none !important;
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
    const SERVER_INITIAL_GLASSWARE = @json($initialGlassware ?? null);
    const SERVER_INITIAL_STANDARDS = @json($initialStandards ?? null);
    const SERVER_INITIAL_PARAMETERS = @json($initialParameters ?? null);

    function isGulaKristalMaterial(jenisStr) {
        if (!jenisStr) return false;
        const j = jenisStr.toUpperCase().trim();
        if (j === 'GARAM') return false;
        return j.includes('GULA') || j.includes('TEBU') || j.includes('KELAPA');
    }

    let currentType = null;
    let currentKategori = 'incoming';
    let currentJumlah = 0;
    // Independent sample count tracking per category
    let categorySampleCounts = {
        'incoming': 1,
        'sta': 1,
        'monitoring': 1,
        'long-term': 1,
        'garam-gula': 1
    };
    let selectedFiles = [];       // array of File objects for newly added photos
    let existingPhotos = [];      // array of string filenames from server draft/record
    let parsedPasteData = [];     // parsed rows from excel paste modal
    let isBroadcastingSelection = false; // guard to prevent recursive change event loops on selection broadcast

    function getDraftKey(kategori = null) {
        const cat = kategori || currentKategori || 'incoming';
        return `rmpm_analisa_${IDENTITAS_ID}_${cat}`;
    }

    function initCategorySampleCounts() {
        ['incoming', 'sta', 'monitoring'].forEach(cat => {
            const matching = (SERVER_SHORT_TERM || []).filter(item => (item.kategori || 'incoming') === cat);
            if (matching.length > 0) {
                categorySampleCounts[cat] = matching.length;
                return;
            }

            let draft;
            try {
                draft = JSON.parse(localStorage.getItem(getDraftKey(cat)));
                if (!draft) {
                    const legacy = JSON.parse(localStorage.getItem(DRAFT_KEY));
                    if (legacy && legacy.setup && legacy.setup.kategori === cat) draft = legacy;
                }
            } catch (e) {}

            if (draft && draft.setup && draft.setup.jumlah) {
                categorySampleCounts[cat] = parseInt(draft.setup.jumlah) || 1;
                return;
            }

            categorySampleCounts[cat] = 1;
        });

        if (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0) {
            categorySampleCounts['garam-gula'] = SERVER_GARAM_GULA.length;
        }
    }

    // Master Glassware Active Data & Daily Quota Tracking State (Preloaded Server Fallback)
    let glasswareData = (SERVER_INITIAL_GLASSWARE && SERVER_INITIAL_GLASSWARE.status) ? SERVER_INITIAL_GLASSWARE : {
        beaker_500: {},
        beaker_250: {},
        cawan: {},
        total_today: { beaker: 0, cawan: 0 },
        usage: { beaker: {}, cawan: {} },
        max_limits: { beaker: 8, cawan: 2 },
        beaker_transactions: [],
        cawan_transactions: []
    };

    // Master Standar Mutu RM Active Standards State (CRUD SPV Sync)
    let dynamicStandards = {
        kotoran: { max: 10.0, min: null, label: '10.0%' },
        ka: { max: (JENIS && JENIS.toLowerCase().includes('garam')) ? 8.0 : 3.0, min: null, label: (JENIS && JENIS.toLowerCase().includes('garam')) ? '8.0%' : '3.0%' },
        raw: {}
    };

    if (SERVER_INITIAL_STANDARDS && SERVER_INITIAL_STANDARDS.status && SERVER_INITIAL_STANDARDS.standards) {
        dynamicStandards.raw = SERVER_INITIAL_STANDARDS.standards;
        for (const [key, std] of Object.entries(SERVER_INITIAL_STANDARDS.standards)) {
            const upper = key.toUpperCase();
            if (upper.includes('KOTORAN')) {
                dynamicStandards.kotoran = {
                    min: std.min !== null ? parseFloat(std.min) : null,
                    max: std.max !== null ? parseFloat(std.max) : 10.0,
                    label: std.max !== null ? `${std.max}%` : (std.target_text || '10.0%')
                };
            } else if (upper.includes('KADAR AIR') || upper === 'KA' || upper === '%KA') {
                const defaultMaxKa = (JENIS && JENIS.toLowerCase().includes('garam')) ? 8.0 : 3.0;
                dynamicStandards.ka = {
                    min: std.min !== null ? parseFloat(std.min) : null,
                    max: std.max !== null ? parseFloat(std.max) : defaultMaxKa,
                    label: std.max !== null ? `${std.max}%` : (std.target_text || `${defaultMaxKa}%`)
                };
            }
        }
    }

    function updateGlasswareUsageDisplay() {
        const beakerUsed = glasswareData.total_today?.beaker || 0;
        const cawanUsed = glasswareData.total_today?.cawan || 0;
        const beakerRemain = Math.max(0, 90 - beakerUsed);
        const cawanRemain = Math.max(0, 240 - cawanUsed);

        $('#txtBeakerUsage').text(beakerUsed);
        $('#txtBeakerRemaining').text(beakerRemain);
        $('#txtCawanUsage').text(cawanUsed);
        $('#txtCawanRemaining').text(cawanRemain);

        $('#badgeBeakerUsed').text(`${beakerUsed}/90`);
        $('#badgeBeakerRemain').text(beakerRemain);
        $('#badgeCawanUsed').text(`${cawanUsed}/240`);
        $('#badgeCawanRemain').text(cawanRemain);
    }

    function fetchGlasswareData(callback) {
        $.ajax({
            url: "{{ route('master-glassware.active-data') }}",
            type: "GET",
            data: { exclude_identitas_id: IDENTITAS_ID },
            dataType: "json",
            success: function(resp) {
                if (resp && resp.status) {
                    glasswareData = {
                        beaker_500: resp.beaker_500 || {},
                        beaker_250: resp.beaker_250 || {},
                        cawan: resp.cawan || {},
                        total_today: resp.total_today || { beaker: 0, cawan: 0 },
                        usage: resp.usage || { beaker: {}, cawan: {} },
                        max_limits: resp.max_limits || { beaker: 8, cawan: 2 },
                        beaker_transactions: resp.beaker_transactions || [],
                        cawan_transactions: resp.cawan_transactions || []
                    };
                    updateGlasswareUsageDisplay();
                }
                if (typeof callback === 'function') callback();
            },
            error: function() {
                console.warn('Gagal memuat data master glassware, menggunakan data fallback.');
                if (typeof callback === 'function') callback();
            }
        });
    }

    function fetchMasterStandards(callback) {
        $.ajax({
            url: "{{ route('master-standar-rm.get-by-jenis') }}",
            type: "GET",
            data: { jenis: JENIS },
            dataType: "json",
            success: function(resp) {
                if (resp && resp.status && resp.standards) {
                    dynamicStandards.raw = resp.standards;
                    // Find Kotoran and Kadar Air standards
                    for (const [key, std] of Object.entries(resp.standards)) {
                        const upper = key.toUpperCase();
                        if (upper.includes('KOTORAN')) {
                            dynamicStandards.kotoran = {
                                min: std.min !== null ? parseFloat(std.min) : null,
                                max: std.max !== null ? parseFloat(std.max) : 10.0,
                                label: std.max !== null ? `${std.max}%` : (std.target_text || '10.0%')
                            };
                        } else if (upper.includes('KADAR AIR') || upper === 'KA' || upper === '%KA') {
                            const defaultMaxKa = JENIS.toLowerCase().includes('garam') ? 8.0 : 3.0;
                            dynamicStandards.ka = {
                                min: std.min !== null ? parseFloat(std.min) : null,
                                max: std.max !== null ? parseFloat(std.max) : defaultMaxKa,
                                label: std.max !== null ? `${std.max}%` : (std.target_text || `${defaultMaxKa}%`)
                            };
                        }
                    }
                }
                if (typeof callback === 'function') callback();
            },
            error: function() {
                console.warn('Gagal memuat data master standar mutu RM, menggunakan data fallback.');
                if (typeof callback === 'function') callback();
            }
        });
    }

    function fetchMasterParameterOptions(callback) {
        $.ajax({
            url: "{{ route('master-parameter-rm.active-options') }}",
            type: "GET",
            data: { jenis: JENIS },
            dataType: "json",
            success: function(resp) {
                if (resp && resp.status && resp.data) {
                    if (resp.data.warna && resp.data.warna.length > 0) {
                        MASTER_DATA['default'].warna = resp.data.warna;
                        if (MASTER_DATA['Gula Kelapa']) MASTER_DATA['Gula Kelapa'].warna = resp.data.warna;
                        if (MASTER_DATA['Gula Tebu']) MASTER_DATA['Gula Tebu'].warna = resp.data.warna;
                    }
                    if (resp.data.aroma && resp.data.aroma.length > 0) {
                        MASTER_DATA['default'].aroma = resp.data.aroma;
                        if (MASTER_DATA['Gula Kelapa']) MASTER_DATA['Gula Kelapa'].aroma = resp.data.aroma;
                        if (MASTER_DATA['Gula Tebu']) MASTER_DATA['Gula Tebu'].aroma = resp.data.aroma;
                    }
                    if (resp.data.organo && resp.data.organo.length > 0) {
                        const rawJenis = (JENIS || '').trim().toUpperCase();
                        if (rawJenis.includes('KELAPA') && MASTER_DATA['Gula Kelapa']) {
                            MASTER_DATA['Gula Kelapa'].organo = resp.data.organo.map(o => ({
                                label: o.label,
                                value: o.value,
                                isCustom: o.isCustom
                            }));
                        } else if (rawJenis.includes('TEBU') && MASTER_DATA['Gula Tebu']) {
                            MASTER_DATA['Gula Tebu'].organo = resp.data.organo.map(o => ({
                                label: o.label,
                                value: o.value,
                                isCustom: o.isCustom
                            }));
                        } else {
                            MASTER_DATA['default'].organo = resp.data.organo.map(o => ({
                                label: o.label,
                                value: o.value,
                                isCustom: o.isCustom
                            }));
                        }
                    }
                }
                if (typeof callback === 'function') callback();
            },
            error: function() {
                console.warn('Gagal memuat data master parameter organoleptik, menggunakan data default.');
                if (typeof callback === 'function') callback();
            }
        });
    }

    function getPhysicalNoFromCode(code) {
        if (!code) return '';
        return code.toString().split('.')[0].trim();
    }

    // Smart Glassware Auto-Assignment based on other SPB transactions today
    function getSmartBeakerForSample(sampleIdx) {
        const usageMap = glasswareData.usage?.beaker || {};
        const maxLimit = glasswareData.max_limits?.beaker || 8;
        
        let availableList = [];
        // First priority: completely clean beakers (used == 0) starting sequentially from 1..90
        for (let i = 1; i <= 90; i++) {
            const noStr = i.toString();
            const u = usageMap[noStr] || 0;
            if (u === 0) {
                availableList.push(noStr);
            }
        }
        // Second priority: if all 90 are used at least once, use beakers with lowest usage < maxLimit
        if (availableList.length < 90) {
            for (let i = 1; i <= 90; i++) {
                const noStr = i.toString();
                const u = usageMap[noStr] || 0;
                if (u > 0 && u < maxLimit) {
                    availableList.push(noStr);
                }
            }
        }
        
        if (sampleIdx < availableList.length) {
            return availableList[sampleIdx];
        }
        return ((sampleIdx % 90) + 1).toString();
    }

    function getSmartCawanForSample(sampleIdx) {
        const usageMap = glasswareData.usage?.cawan || {};
        const maxLimit = glasswareData.max_limits?.cawan || 2;
        
        let availableList = [];
        // First priority: completely clean cawans (used == 0) starting sequentially from 1..240
        for (let i = 1; i <= 240; i++) {
            const noStr = i.toString();
            const u = usageMap[noStr] || 0;
            if (u === 0) {
                availableList.push(noStr);
            }
        }
        // Second priority: if all 240 are used at least once, use cawans with lowest usage < maxLimit
        if (availableList.length < 240) {
            for (let i = 1; i <= 240; i++) {
                const noStr = i.toString();
                const u = usageMap[noStr] || 0;
                if (u > 0 && u < maxLimit) {
                    availableList.push(noStr);
                }
            }
        }
        
        if (sampleIdx < availableList.length) {
            return availableList[sampleIdx];
        }
        return ((sampleIdx % 240) + 1).toString();
    }

    function getBeakerOptions(selectedVal) {
        let html = '<option value="">-- Pilih No. Beaker --</option>';
        const maxLimit = glasswareData.max_limits?.beaker || 8;
        const usageMap = glasswareData.usage?.beaker || {};
        const selectedPhys = getPhysicalNoFromCode(selectedVal);

        for (let i = 1; i <= 90; i++) {
            const noStr = i.toString();
            const used = usageMap[noStr] || 0;
            const isSelected = (selectedPhys === noStr);
            const isFull = (used >= maxLimit && !isSelected);
            
            let label = `Beaker No. ${i}`;
            if (isFull) {
                label += ' (Penuh / Cuci)';
            } else if (used === 0) {
                label += ' (Bersih)';
            } else {
                label += ` (Terpakai ${used}x)`;
            }
            html += `<option value="${noStr}" ${isSelected ? 'selected' : ''} ${isFull ? 'disabled' : ''} data-used="${used}">${label}</option>`;
        }
        return html;
    }

    function getCawanOptions(selectedVal) {
        let html = '<option value="">-- Pilih No. Cawan --</option>';
        const maxLimit = glasswareData.max_limits?.cawan || 2;
        const usageMap = glasswareData.usage?.cawan || {};
        const selectedPhys = getPhysicalNoFromCode(selectedVal);

        for (let i = 1; i <= 240; i++) {
            const noStr = i.toString();
            const used = usageMap[noStr] || 0;
            const isSelected = (selectedPhys === noStr);
            const isFull = (used >= maxLimit && !isSelected);
            
            let label = `Cawan No. ${i}`;
            if (isFull) {
                label += ' (Penuh / Cuci)';
            } else if (used === 0) {
                label += ' (Bersih)';
            } else {
                label += ` (Terpakai ${used}x)`;
            }
            html += `<option value="${noStr}" ${isSelected ? 'selected' : ''} ${isFull ? 'disabled' : ''} data-used="${used}">${label}</option>`;
        }
        return html;
    }

    function getAromaPengotorOptions(selectedVal) {
        const list = ['OK', 'Bau Asam', 'Bau Sangit', 'Bau Apek', 'Bau Kimia', 'Lain-lain'];
        let html = '<option value="">-- Pilih Aroma --</option>';
        list.forEach(item => {
            const isSelected = (selectedVal && selectedVal.toString().toUpperCase() === item.toUpperCase());
            html += `<option value="${item}" ${isSelected ? 'selected' : ''}>${item}</option>`;
        });
        return html;
    }

    // Rapid Numeric Parser: Automatically divides by 100 for rapid typing without dot if >= 1000 (e.g. 22838 -> 228.38, 3985 -> 39.85)
    function parseRapidNumericValue(rawVal) {
        if (rawVal === undefined || rawVal === null) return NaN;
        let s = rawVal.toString().trim();
        if (!s) return NaN;
        s = s.replace(/,/g, '.');
        if (s.includes('.')) {
            return parseFloat(s);
        }
        const num = parseFloat(s);
        if (isNaN(num)) return NaN;
        if (num >= 1000) {
            return num / 100.0;
        }
        return num;
    }

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
                'Coklat',
                'Coklat tua',
                'Coklat muda',
                'Gelap'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada',
                'Tidak Sesuai'
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
                'Coklat',
                'Coklat tua',
                'Coklat muda',
                'Gelap'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada',
                'Tidak Sesuai'
            ]
        },
        'default': {
            organo: [
                { label: 'OK', value: 'OK', isCustom: false },
                { label: 'Tidak Sesuai', value: 'Tidak Sesuai', isCustom: false },
                { label: 'Lain-lain (bisa input teks)', value: 'Lain-lain', isCustom: true }
            ],
            warna: [
                'Coklat',
                'Coklat tua',
                'Coklat muda',
                'Gelap'
            ],
            aroma: [
                'OK',
                'Kurang',
                'Tidak Ada',
                'Tidak Sesuai'
            ]
        }
    };

    if (SERVER_INITIAL_PARAMETERS && SERVER_INITIAL_PARAMETERS.status && SERVER_INITIAL_PARAMETERS.data) {
        const pData = SERVER_INITIAL_PARAMETERS.data;
        if (pData.warna && pData.warna.length > 0) {
            MASTER_DATA['default'].warna = pData.warna;
            if (MASTER_DATA['Gula Kelapa']) MASTER_DATA['Gula Kelapa'].warna = pData.warna;
            if (MASTER_DATA['Gula Tebu']) MASTER_DATA['Gula Tebu'].warna = pData.warna;
        }
        if (pData.aroma && pData.aroma.length > 0) {
            MASTER_DATA['default'].aroma = pData.aroma;
            if (MASTER_DATA['Gula Kelapa']) MASTER_DATA['Gula Kelapa'].aroma = pData.aroma;
            if (MASTER_DATA['Gula Tebu']) MASTER_DATA['Gula Tebu'].aroma = pData.aroma;
        }
        if (pData.organo && pData.organo.length > 0) {
            const rawJenis = (JENIS || '').trim().toUpperCase();
            if (rawJenis.includes('KELAPA') && MASTER_DATA['Gula Kelapa']) {
                MASTER_DATA['Gula Kelapa'].organo = pData.organo.map(o => ({
                    label: o.label,
                    value: o.value,
                    isCustom: o.isCustom
                }));
            } else if (rawJenis.includes('TEBU') && MASTER_DATA['Gula Tebu']) {
                MASTER_DATA['Gula Tebu'].organo = pData.organo.map(o => ({
                    label: o.label,
                    value: o.value,
                    isCustom: o.isCustom
                }));
            } else {
                MASTER_DATA['default'].organo = pData.organo.map(o => ({
                    label: o.label,
                    value: o.value,
                    isCustom: o.isCustom
                }));
            }
        }
    }

    function getMasterOptions(fieldKey) {
        if (fieldKey === 'aroma_pengotor') {
            return ['OK', 'Bau Asam', 'Bau Sangit', 'Bau Apek', 'Bau Kimia', 'Lain-lain'];
        }
        const rawJenis = (JENIS || '').trim().toUpperCase();
        if (rawJenis.includes('KELAPA')) {
            return (MASTER_DATA['Gula Kelapa'] && MASTER_DATA['Gula Kelapa'][fieldKey]) || MASTER_DATA['default'][fieldKey] || [];
        }
        if (rawJenis.includes('TEBU')) {
            return (MASTER_DATA['Gula Tebu'] && MASTER_DATA['Gula Tebu'][fieldKey]) || MASTER_DATA['default'][fieldKey] || [];
        }
        const config = MASTER_DATA[JENIS] || MASTER_DATA['default'];
        return config[fieldKey] || MASTER_DATA['default'][fieldKey] || [];
    }

    function setSelectDropdownVal($select, val, masterKey = null, triggerChange = false) {
        if (!$select || !$select.length) return;
        if (val === undefined || val === null) {
            $select.val('');
            if (triggerChange) $select.trigger('change');
            return;
        }
        const rawVal = val.toString().replace(/^["']|["']$/g, '').trim();
        if (!rawVal) {
            $select.val('');
            if (triggerChange) $select.trigger('change');
            return;
        }

        // 1. Exact match (case-insensitive) against option values and text
        let matchedVal = null;
        $select.find('option').each(function() {
            const optVal = ($(this).val() || '').toString().trim();
            const optText = ($(this).text() || '').toString().trim();
            if (optVal.toLowerCase() === rawVal.toLowerCase() || optText.toLowerCase() === rawVal.toLowerCase()) {
                matchedVal = optVal;
                return false;
            }
        });

        if (matchedVal !== null) {
            $select.val(matchedVal);
            if (triggerChange) $select.trigger('change');
            return;
        }

        // 2. Loose / partial match (e.g. OK -> OK, Sesuai -> Sesuai Standar, Tidak OK -> Tidak Sesuai)
        $select.find('option').each(function() {
            const optVal = ($(this).val() || '').toString().trim();
            if (!optVal) return;
            const optLower = optVal.toLowerCase();
            const rawLower = rawVal.toLowerCase();

            if (rawLower === 'ok' && (optLower === 'ok' || optLower.includes('sesuai'))) {
                matchedVal = optVal;
                return false;
            }
            if ((rawLower.includes('tidak') || rawLower === 'nok' || rawLower === 'not ok' || rawLower === 'kurang') && optLower.includes('tidak')) {
                matchedVal = optVal;
                return false;
            }
            if (optLower.includes(rawLower) || rawLower.includes(optLower)) {
                matchedVal = optVal;
                return false;
            }
        });

        if (matchedVal !== null) {
            $select.val(matchedVal);
            if (triggerChange) $select.trigger('change');
            return;
        }

        // 3. Fallback: Dynamically append the option so the value is NEVER lost
        $select.append(`<option value="${rawVal}">${rawVal}</option>`);
        $select.val(rawVal);
        if (triggerChange) $select.trigger('change');
    }

    // Short-term fields definition (7 columns in Resume Table - Tab 1)
    const SHORT_TERM_FIELDS = [
        { key: 'brix[]', label: 'Brix', type: 'number', unit: '' },
        { key: 'ph[]', label: 'pH', type: 'number', unit: '' },
        { key: 'kotoran[]', label: '% Kotoran', type: 'number', unit: '%' },
        { key: 'ka[]', label: '% KA', type: 'number', unit: '%' },
        { key: 'organo[]', label: 'Organo', type: 'organo-select', unit: '' },
        { key: 'warna[]', label: 'Warna', type: 'dropdown', masterKey: 'warna', unit: '' },
        { key: 'aroma[]', label: 'Aroma', type: 'dropdown', masterKey: 'aroma', unit: '' },
    ];

    // Tab 2: Lembar % Kotoran fields definition (4 data columns)
    const TAB_KOTORAN_FIELDS = [
        { key: 'no_beaker[]', label: 'No. Beaker', type: 'dropdown', unit: '' },
        { key: 'timbang_a[]', label: 'Beaker 500g', type: 'number', unit: 'gr' },
        { key: 'timbang_b[]', label: 'Beaker 250g', type: 'number', unit: 'gr' },
        { key: 'kotoran_calc', label: 'Hasil % Kotoran', type: 'readonly', unit: '%' },
    ];

    // Tab 3: Lembar % Kadar Air fields definition (3 data columns)
    const TAB_KA_FIELDS = [
        { key: 'no_cawan[]', label: 'No. Cawan', type: 'dropdown', unit: '' },
        { key: 'timbang_aa[]', label: 'Bobot Akhir (AA)', type: 'number', unit: 'gr' },
        { key: 'ka_calc', label: 'Hasil % Kadar Air', type: 'readonly', unit: '%' },
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

    // Helper to update visibility of main submit buttons based on active tab
    function updateMainFormActionsVisibility(explicitTarget = null) {
        if (currentType === 'short-term') {
            const activeTab = explicitTarget || $('.nav-worksheet .nav-link.active').attr('data-bs-target') || ($('#paneRingkasan').hasClass('active') ? '#paneRingkasan' : ($('#paneKotoran').hasClass('active') ? '#paneKotoran' : '#paneKadarAir'));
            if (activeTab === '#paneKotoran' || activeTab === '#paneKadarAir') {
                $('#mainFormActions').hide();
            } else {
                $('#mainFormActions').show();
            }
        } else {
            $('#mainFormActions').show();
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize form immediately with preloaded master data (instant response)
        initFormData();
        updateGlasswareUsageDisplay();

        // Refresh master data & quota logs asynchronously in background
        fetchGlasswareData();
        fetchMasterStandards();
        fetchMasterParameterOptions();

        // Tab switching handler for worksheet
        $(document).on('click', '.btn-switch-tab', function(e) {
            e.preventDefault();
            const targetTab = $(this).data('target-tab');
            if (targetTab) {
                const tabTrigger = document.querySelector(`button[data-bs-target="${targetTab}"]`);
                if (tabTrigger) {
                    const tab = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                    tab.show();
                }
                updateMainFormActionsVisibility(targetTab);
            }
        });

        // Tab switching listener for main actions visibility
        $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function(e) {
            const target = $(this).attr('data-bs-target') || $(this).data('bs-target');
            updateMainFormActionsVisibility(target);
        });

        // Simpan Sementara from Worksheet Tab (Tab 2 & Tab 3)
        $(document).on('click', '.btn-draft-worksheet', function(e) {
            e.preventDefault();
            $('#saveAction').val('draft');
            submitForm('draft');
        });

        // Terapkan Hasil ke Ringkasan from Worksheet Tab (Tab 2 & Tab 3)
        $(document).on('click', '.btn-apply-worksheet', function(e) {
            e.preventDefault();
            const target = $(this).data('target'); // 'kotoran' or 'ka'

            // Force recalculate all rows to ensure Tab 1 is 100% updated
            if (target === 'kotoran') {
                $('#tableLembarKotoran tbody tr').each(function(idx) {
                    calculateRowKotoran(idx);
                });
            } else if (target === 'ka') {
                $('#tableLembarKa tbody tr').each(function(idx) {
                    calculateRowKa(idx);
                });
            }

            calculateStatistics();
            saveDraft();

            // Switch back to Tab 1 (Ringkasan & Parameter)
            const tabTrigger = document.querySelector('button[data-bs-target="#paneRingkasan"]');
            if (tabTrigger) {
                const tab = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                tab.show();
            }

            // Pulse highlight target input column in Ringkasan
            const targetSelector = target === 'kotoran' ? 'input[name="kotoran[]"]' : 'input[name="ka[]"]';
            const $targetInputs = $('#tableAnalisaShortTerm tbody ' + targetSelector);
            $targetInputs.closest('td').addClass('highlight-flash');
            setTimeout(() => {
                $targetInputs.closest('td').removeClass('highlight-flash');
            }, 1500);

            const fieldLabel = target === 'kotoran' ? '% Kotoran' : '% Kadar Air';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Hasil ${fieldLabel} berhasil diterapkan ke Ringkasan!`,
                showConfirmButton: false,
                timer: 2000
            });
        });

        // Glassware Selection & Calculation Handlers
        $(document).on('change', '.select-no-beaker', function() {
            const $tr = $(this).closest('tr');
            const rowIdx = $tr.index();
            const no = $(this).val();

            if (no) {
                const phys = getPhysicalNoFromCode(no);
                const maxLimit = glasswareData.max_limits?.beaker || 8;
                const used = (glasswareData.usage?.beaker && glasswareData.usage.beaker[phys]) ? glasswareData.usage.beaker[phys] : 0;
                if (used >= maxLimit) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Pemakaian Tercapai!',
                        text: `Beaker No. ${phys} sudah digunakan ${used}x hari ini (Maksimal ${maxLimit}x). Silakan gunakan nomor beaker lain yang masih bersih.`,
                        confirmButtonText: 'Mengerti'
                    });
                }
                const tare500 = (glasswareData.beaker_500 && glasswareData.beaker_500[phys] !== undefined) ? glasswareData.beaker_500[phys] : '';
                const tare250 = (glasswareData.beaker_250 && glasswareData.beaker_250[phys] !== undefined) ? glasswareData.beaker_250[phys] : '';
                $tr.find('input[name="berat_beaker_500[]"]').val(tare500);
                $tr.find('input[name="berat_beaker_250[]"]').val(tare250);
                $tr.find('.val-tare-500').text(tare500 ? tare500 + 'g' : '-');
                $tr.find('.val-tare-250').text(tare250 ? tare250 + 'g' : '-');

                // Auto-sequence subsequent rows if row 0 was changed
                if (rowIdx === 0 && !isBroadcastingSelection) {
                    const startNum = parseInt(phys, 10);
                    if (!isNaN(startNum)) {
                        const $allBeakerRows = $('#tableLembarKotoran tbody tr');
                        for (let k = 1; k < $allBeakerRows.length; k++) {
                            const nextPhys = (((startNum - 1 + k) % 90) + 1).toString();
                            const $nextTr = $allBeakerRows.eq(k);
                            const $select = $nextTr.find('.select-no-beaker');
                            $select.val(nextPhys);
                            const t500 = (glasswareData.beaker_500 && glasswareData.beaker_500[nextPhys] !== undefined) ? glasswareData.beaker_500[nextPhys] : '';
                            const t250 = (glasswareData.beaker_250 && glasswareData.beaker_250[nextPhys] !== undefined) ? glasswareData.beaker_250[nextPhys] : '';
                            $nextTr.find('input[name="berat_beaker_500[]"]').val(t500);
                            $nextTr.find('input[name="berat_beaker_250[]"]').val(t250);
                            $nextTr.find('.val-tare-500').text(t500 ? t500 + 'g' : '-');
                            $nextTr.find('.val-tare-250').text(t250 ? t250 + 'g' : '-');
                            calculateRowKotoran(k);
                        }
                    }
                }
            } else {
                $tr.find('input[name="berat_beaker_500[]"]').val('');
                $tr.find('input[name="berat_beaker_250[]"]').val('');
                $tr.find('.val-tare-500').text('-');
                $tr.find('.val-tare-250').text('-');
            }
            calculateRowKotoran(rowIdx);
            saveDraft();
        });

        $(document).on('input change', '.calc-trigger-kotoran', function() {
            const rowIdx = $(this).closest('tr').index();
            calculateRowKotoran(rowIdx);
            saveDraft();
        });

        $(document).on('blur', '.calc-trigger-kotoran', function() {
            const val = $(this).val();
            if (val && val.trim() !== '') {
                const parsed = parseRapidNumericValue(val);
                if (!isNaN(parsed)) {
                    $(this).val(parsed.toFixed(2));
                }
            }
            const rowIdx = $(this).closest('tr').index();
            calculateRowKotoran(rowIdx);
            saveDraft();
        });

        $(document).on('change', '.select-no-cawan', function() {
            const $tr = $(this).closest('tr');
            const rowIdx = $tr.index();
            const no = $(this).val();

            if (no) {
                const phys = getPhysicalNoFromCode(no);
                const maxLimit = glasswareData.max_limits?.cawan || 2;
                const used = (glasswareData.usage?.cawan && glasswareData.usage.cawan[phys]) ? glasswareData.usage.cawan[phys] : 0;
                if (used >= maxLimit) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Pemakaian Tercapai!',
                        text: `Cawan No. ${phys} sudah digunakan ${used}x hari ini (Maksimal ${maxLimit}x). Silakan gunakan nomor cawan lain yang masih bersih.`,
                        confirmButtonText: 'Mengerti'
                    });
                }
                const tareCawan = (glasswareData.cawan && glasswareData.cawan[phys] !== undefined) ? glasswareData.cawan[phys] : '';
                $tr.find('input[name="berat_cawan[]"]').val(tareCawan);
                $tr.find('.val-tare-cawan').text(tareCawan ? tareCawan + 'g' : '-');

                // Auto-sequence subsequent rows if row 0 was changed
                if (rowIdx === 0 && !isBroadcastingSelection) {
                    const startNum = parseInt(phys, 10);
                    if (!isNaN(startNum)) {
                        const $allCawanRows = $('#tableLembarKa tbody tr');
                        for (let k = 1; k < $allCawanRows.length; k++) {
                            const nextPhys = (((startNum - 1 + k) % 240) + 1).toString();
                            const $nextTr = $allCawanRows.eq(k);
                            const $select = $nextTr.find('.select-no-cawan');
                            $select.val(nextPhys);
                            const tCawan = (glasswareData.cawan && glasswareData.cawan[nextPhys] !== undefined) ? glasswareData.cawan[nextPhys] : '';
                            $nextTr.find('input[name="berat_cawan[]"]').val(tCawan);
                            $nextTr.find('.val-tare-cawan').text(tCawan ? tCawan + 'g' : '-');
                            calculateRowKa(k);
                        }
                    }
                }
            } else {
                $tr.find('input[name="berat_cawan[]"]').val('');
                $tr.find('.val-tare-cawan').text('-');
            }
            calculateRowKa(rowIdx);
            saveDraft();
        });

        $(document).on('input change', '.calc-trigger-ka', function() {
            const rowIdx = $(this).closest('tr').index();
            calculateRowKa(rowIdx);
            saveDraft();
        });

        $(document).on('blur', '.calc-trigger-ka', function() {
            const val = $(this).val();
            if (val && val.trim() !== '') {
                const parsed = parseRapidNumericValue(val);
                if (!isNaN(parsed)) {
                    $(this).val(parsed.toFixed(2));
                }
            }
            const rowIdx = $(this).closest('tr').index();
            calculateRowKa(rowIdx);
            saveDraft();
        });

        // Glassware Transaction Modal Handlers
        $(document).on('click', '.btn-open-glassware-modal', function() {
            const defFilter = $(this).data('default-filter') || 'all';
            currentGwFilter = defFilter;
            $('#glasswareFilterTabs .btn-gw-tab').removeClass('active');
            $(`#glasswareFilterTabs .btn-gw-tab[data-filter="${defFilter}"]`).addClass('active');
            $('#inputSearchGwLogs').val('');
            currentGwSearch = '';

            fetchGlasswareData(function() {
                renderGlasswareTransactionsModal();
                const gwModalEl = document.getElementById('modalGlasswareTransactions');
                if (gwModalEl) {
                    const gwModal = bootstrap.Modal.getOrCreateInstance(gwModalEl);
                    gwModal.show();
                }
            });
        });

        $(document).on('click', '.btn-gw-tab', function() {
            $('#glasswareFilterTabs .btn-gw-tab').removeClass('active');
            $(this).addClass('active');
            currentGwFilter = $(this).data('filter');
            renderGlasswareTransactionsModal();
        });

        $(document).on('input', '#inputSearchGwLogs', function() {
            currentGwSearch = $(this).val();
            renderGlasswareTransactionsModal();
        });

        $(document).on('click', '#btnRefreshGwLogs', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).find('i').addClass('ri-spin');
            fetchGlasswareData(function() {
                renderGlasswareTransactionsModal();
                $btn.prop('disabled', false).find('i').removeClass('ri-spin');
            });
        });

        $(document).on('click', '#btnQuickSetAllOrganoOk', function() {
            $('.organo-cell-container').each(function() {
                applyOrganoValueToCell($(this), 'OK');
            });
            $('.select-rasa-tab2').val('OK');
            calculateStatistics();
            saveDraft();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Seluruh sampel Rasa/Organo diset "OK"',
                showConfirmButton: false,
                timer: 1800
            });
        });

        $(document).on('click', '#btnQuickSetAllWarnaAroma', function() {
            $('select[name="warna[]"]').each(function() {
                setSelectDropdownVal($(this), 'Coklat', 'warna');
            });
            $('select[name="aroma[]"]').each(function() {
                setSelectDropdownVal($(this), 'OK', 'aroma');
            });
            calculateStatistics();
            saveDraft();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Warna diset "Coklat" & Aroma diset "OK"',
                showConfirmButton: false,
                timer: 1800
            });
        });

        $(document).on('change', '.select-rasa-tab2', function() {
            const rowIdx = $(this).closest('tr').index();
            const rasaVal = $(this).val();
            const $ringkasanContainer = $('#tableAnalisaShortTerm tbody tr').eq(rowIdx).find('.organo-cell-container');
            if ($ringkasanContainer.length && rasaVal) {
                applyOrganoValueToCell($ringkasanContainer, rasaVal);
            }
            calculateStatistics();
            saveDraft();
        });

        $('#btnMulai').on('click', handleMulai);
        $('#btnReset').on('click', handleReset);
        $(document).on('click', '#btnBackToSetup', function(e) {
            e.preventDefault();
            saveDraft();
            currentType = null;
            $('#analisaSection, #dividerForm').hide();
            updateSetupBadges();
            $('#setupSection').slideDown(150);
            const activeRadio = $('input[name="analisa_type"]:checked').val() || currentKategori || 'incoming';
            const showCount = categorySampleCounts[activeRadio] || 1;
            $('#jumlahData').val(showCount);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete('kategori');
            window.history.replaceState({}, '', newUrl.pathname);
        });
        $(document).on('click', '.btn-category-switch', function() {
            const targetCat = $(this).data('cat');
            if (targetCat) {
                switchCategoryDirectly(targetCat);
            }
        });

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

        // Organo Dynamic Handlers (Dropdown + Custom Text Box + Multi-cell Selection Broadcast)
        $(document).on('change', '.select-organo-dropdown', function() {
            if (isBroadcastingSelection) return;

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
                $finalInput.val($customInput.val() || val);
            } else {
                $customBox.slideUp(100);
                $customInput.val('');
                $finalInput.val(val);
            }

            // Broadcast to selected block if multi-cell selection is active
            const coords = getCellCoords(this);
            const activeSel = (tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol))
                ? tableSelection
                : (lastMultiSelection && (lastMultiSelection.minRow !== lastMultiSelection.maxRow || lastMultiSelection.minCol !== lastMultiSelection.maxCol) ? lastMultiSelection : null);

            if (activeSel && coords && coords.col >= activeSel.minCol && coords.col <= activeSel.maxCol && coords.row >= activeSel.minRow && coords.row <= activeSel.maxRow) {
                const $table = $('#analisaAccordion').find('table');
                const $rows = $table.find('tbody tr');
                const count = activeSel.maxRow - activeSel.minRow + 1;
                if (count > 1) {
                    isBroadcastingSelection = true;
                    try {
                        for (let r = activeSel.minRow; r <= activeSel.maxRow; r++) {
                            if (r === coords.row) continue;
                            const $c = $rows.eq(r).find('.organo-cell-container');
                            applyOrganoValueToCell($c, val);
                            highlightCell($c.find('select, input'));
                        }
                    } finally {
                        isBroadcastingSelection = false;
                    }
                    tableSelection = { ...activeSel };
                    renderSelection();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `${count} sampel Organo diset "${val || '-'}"`,
                        showConfirmButton: false,
                        timer: 1600
                    });
                }
            }

            calculateStatistics();
            saveDraft();
        });

        $(document).on('input', '.input-organo-custom-text', function() {
            if (isBroadcastingSelection) return;

            const $container = $(this).closest('.organo-cell-container');
            const $finalInput = $container.find('.input-organo-final');
            const val = $(this).val();
            $finalInput.val(val);

            const coords = getCellCoords(this);
            const activeSel = (tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol))
                ? tableSelection
                : (lastMultiSelection && (lastMultiSelection.minRow !== lastMultiSelection.maxRow || lastMultiSelection.minCol !== lastMultiSelection.maxCol) ? lastMultiSelection : null);

            if (activeSel && coords && coords.col >= activeSel.minCol && coords.col <= activeSel.maxCol && coords.row >= activeSel.minRow && coords.row <= activeSel.maxRow) {
                const $table = $('#analisaAccordion').find('table');
                const $rows = $table.find('tbody tr');
                isBroadcastingSelection = true;
                try {
                    for (let r = activeSel.minRow; r <= activeSel.maxRow; r++) {
                        if (r === coords.row) continue;
                        const $c = $rows.eq(r).find('.organo-cell-container');
                        applyOrganoValueToCell($c, val);
                    }
                } finally {
                    isBroadcastingSelection = false;
                }
            }

            calculateStatistics();
            saveDraft();
        });

        $(document).on('click', '.btn-close-organo-custom', function() {
            if (isBroadcastingSelection) return;

            const $container = $(this).closest('.organo-cell-container');
            const $select = $container.find('.select-organo-dropdown');
            const $customBox = $container.find('.organo-custom-input-box');
            const $customInput = $container.find('.input-organo-custom-text');
            const $finalInput = $container.find('.input-organo-final');

            $customBox.slideUp(100);
            $customInput.val('');
            $select.val('OK');
            $finalInput.val('OK');

            const coords = getCellCoords(this);
            const activeSel = (tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol))
                ? tableSelection
                : (lastMultiSelection && (lastMultiSelection.minRow !== lastMultiSelection.maxRow || lastMultiSelection.minCol !== lastMultiSelection.maxCol) ? lastMultiSelection : null);

            if (activeSel && coords && coords.col >= activeSel.minCol && coords.col <= activeSel.maxCol && coords.row >= activeSel.minRow && coords.row <= activeSel.maxRow) {
                const $table = $('#analisaAccordion').find('table');
                const $rows = $table.find('tbody tr');
                isBroadcastingSelection = true;
                try {
                    for (let r = activeSel.minRow; r <= activeSel.maxRow; r++) {
                        if (r === coords.row) continue;
                        const $c = $rows.eq(r).find('.organo-cell-container');
                        applyOrganoValueToCell($c, 'OK');
                        highlightCell($c.find('select, input'));
                    }
                } finally {
                    isBroadcastingSelection = false;
                }
                tableSelection = { ...activeSel };
                renderSelection();
            }

            calculateStatistics();
            saveDraft();
        });

        // Dropdown cells multi-cell broadcast (Warna, Aroma, Rasa, etc.)
        $(document).on('change', '#analisaAccordion select:not(.select-organo-dropdown):not(.select-no-beaker):not(.select-no-cawan):not([name="disposisi"])', function() {
            if (isBroadcastingSelection) return;

            const name = $(this).attr('name');
            if (!name) return;
            const val = $(this).val();
            const coords = getCellCoords(this);
            const activeSel = (tableSelection && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol))
                ? tableSelection
                : (lastMultiSelection && (lastMultiSelection.minRow !== lastMultiSelection.maxRow || lastMultiSelection.minCol !== lastMultiSelection.maxCol) ? lastMultiSelection : null);

            if (activeSel && coords && coords.col >= activeSel.minCol && coords.col <= activeSel.maxCol && coords.row >= activeSel.minRow && coords.row <= activeSel.maxRow) {
                const $table = coords.$table || (activeSel.tableId ? $('#' + activeSel.tableId) : getActiveTable(this));
                const $rows = $table.find('tbody tr');
                const count = activeSel.maxRow - activeSel.minRow + 1;
                if (count > 1) {
                    const fields = getTableFields($table);
                    const field = fields[coords.col];
                    const masterKey = field ? field.masterKey : (name.includes('warna') ? 'warna' : (name.includes('rasa') ? 'organo' : 'aroma'));

                    isBroadcastingSelection = true;
                    try {
                        for (let r = activeSel.minRow; r <= activeSel.maxRow; r++) {
                            if (r === coords.row) continue;
                            const $targetSelect = $rows.eq(r).find(`select[name="${name}"]`);
                            if ($targetSelect.length) {
                                setSelectDropdownVal($targetSelect, val, masterKey, false);
                                highlightCell($targetSelect);
                            }
                            if (name === 'rasa[]') {
                                const $ringkasanContainer = $('#tableAnalisaShortTerm tbody tr').eq(r).find('.organo-cell-container');
                                if ($ringkasanContainer.length && val) {
                                    applyOrganoValueToCell($ringkasanContainer, val);
                                }
                            }
                        }
                    } finally {
                        isBroadcastingSelection = false;
                    }
                    tableSelection = { ...activeSel };
                    renderSelection();

                    const colLabel = field ? field.label : name.replace('[]', '');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `${count} sampel ${colLabel} diset "${val || '-'}"`,
                        showConfirmButton: false,
                        timer: 1600
                    });
                }
            }

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

        // Capture Enter and Tab in window capture phase to guarantee downwards navigation on PC & Tablets
        window.addEventListener('keydown', function(e) {
            const target = e.target;
            const isInside = target && target.closest && target.closest('.analisa-table, #analisaAccordion table');
            const isNavKey = (e.key === 'Tab' || e.key === 'Enter' || e.keyCode === 9 || e.keyCode === 13 || e.which === 9 || e.which === 13);
            if (isInside && isNavKey) {
                e.preventDefault();
                e.stopPropagation();
                navigateTableVertical(target, e.shiftKey ? -1 : 1);
            }
        }, true);

        // Tablet / Mobile virtual keyboard Action / Enter / Next key support
        $(document).on('keypress', '.analisa-table input, .analisa-table select, #analisaAccordion table input, #analisaAccordion table select', function(e) {
            if (e.keyCode === 13 || e.which === 13 || e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                navigateTableVertical(this, e.shiftKey ? -1 : 1);
            }
        });

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
            const $table = $(this).closest('table.analisa-table');
            const fields = getTableFields($table);
            const field = fields[colIdx];
            if (!field || currentJumlah <= 1) return;

            const $rows = $table.find('tbody tr');
            const $firstRow = $rows.eq(0);
            let firstVal = '';

            if (field.type === 'organo-select') {
                firstVal = $firstRow.find('.input-organo-final').val() || '';
            } else if (field.type === 'readonly') {
                return;
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
                applyFieldValueToCell($row, field, firstVal, $table);
            }

            calculateStatistics();
            saveDraft();
            setSelection(0, colIdx, currentJumlah - 1, colIdx, $table);

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

        $(document).on('input change', '#jumlahData', function() {
            const val = parseInt($(this).val());
            if (val && val > 0) {
                const selectedCat = $('input[name="analisa_type"]:checked').val() || currentKategori || 'incoming';
                categorySampleCounts[selectedCat] = val;
            }
        });

        // Category selection in setup screen
        $(document).on('change', 'input[name="analisa_type"]', function() {
            const val = $(this).val();
            const isLong = (val === 'long-term');
            $('#jumlahWrapper').toggle(!isLong);
            if (isLong) {
                $('#jumlahData').val(1);
            } else {
                const countForCat = categorySampleCounts[val] || 1;
                $('#jumlahData').val(countForCat);
            }
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

    function populateExistingLongTerm() {
        currentType = 'long-term';
        currentKategori = 'long-term';
        currentJumlah = 1;

        if (SERVER_EXISTING && SERVER_EXISTING.attachment) {
            let rawAtt = SERVER_EXISTING.attachment;
            if (typeof rawAtt === 'string' && rawAtt !== '-') {
                try {
                    rawAtt = JSON.parse(rawAtt);
                } catch (e) {
                    rawAtt = [rawAtt];
                }
            }
            if (Array.isArray(rawAtt)) {
                existingPhotos = rawAtt.map(p => {
                    if (typeof p === 'string') {
                        return p.split('/').pop().split('\\').pop();
                    }
                    return p;
                }).filter(Boolean);
            }
        }

        if (isGulaKristalMaterial(JENIS)) {
            $(`input[name="analisa_type"][value="long-term"]`).prop('checked', true);
        }
        $('#jumlahData').val(1);
        startForm('long-term', 1, 'long-term');

        setTimeout(() => {
            if (SERVER_EXISTING) {
                if (SERVER_EXISTING.status === 'draft') {
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

    function updateDraftBadgeStatus() {
        let isDraft = false;
        if (currentType === 'long-term') {
            isDraft = (SERVER_EXISTING && SERVER_EXISTING.status === 'draft');
        } else if (currentType === 'short-term') {
            const matching = (SERVER_SHORT_TERM || []).filter(item => (item.kategori || 'incoming') === (currentKategori || 'incoming'));
            isDraft = (matching.length > 0 && matching[0].status === 'draft');
        } else if (currentType === 'garam-gula') {
            isDraft = (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0 && SERVER_GARAM_GULA[0].status === 'draft');
        }

        if (isDraft) {
            $('#draftStatusBadge').show();
        } else {
            $('#draftStatusBadge').hide();
        }
    }

    function updateSetupBadges() {
        if (!isGulaKristalMaterial(JENIS)) return;
        const incoming = (SERVER_SHORT_TERM || []).filter(item => (item.kategori || 'incoming') === 'incoming');
        const sta = (SERVER_SHORT_TERM || []).filter(item => item.kategori === 'sta');
        const monitoring = (SERVER_SHORT_TERM || []).filter(item => item.kategori === 'monitoring');
        const hasLongTerm = (SERVER_EXISTING && SERVER_EXISTING.id);

        if (incoming.length > 0) {
            const isDraft = (incoming[0].status === 'draft');
            $('#setupBadgeIncoming').text(isDraft ? `(Draft - ${incoming.length} Sampel)` : `(${incoming.length} Sampel)`).show();
        } else {
            $('#setupBadgeIncoming').hide();
        }

        if (sta.length > 0) {
            const isDraft = (sta[0].status === 'draft');
            $('#setupBadgeSta').text(isDraft ? `(Draft - ${sta.length} Sampel)` : `(${sta.length} Sampel)`).show();
        } else {
            $('#setupBadgeSta').hide();
        }

        if (monitoring.length > 0) {
            const isDraft = (monitoring[0].status === 'draft');
            $('#setupBadgeMonitoring').text(isDraft ? `(Draft - ${monitoring.length} Sampel)` : `(${monitoring.length} Sampel)`).show();
        } else {
            $('#setupBadgeMonitoring').hide();
        }

        if (hasLongTerm) {
            const isDraft = (SERVER_EXISTING.status === 'draft');
            $('#setupBadgeLongTerm').html(isDraft ? '<i class="ri-file-edit-line"></i> Draft' : '<i class="ri-check-line"></i> Tersimpan').show();
        } else {
            $('#setupBadgeLongTerm').hide();
        }
    }

    function populateExistingShortTerm(kategori, customJumlah = null) {
        if (!SERVER_SHORT_TERM || !SERVER_SHORT_TERM.length) return false;
        const matching = SERVER_SHORT_TERM.filter(item => (item.kategori || 'incoming') === (kategori || 'incoming'));
        if (!matching.length) return false;

        const count = (customJumlah && customJumlah > 0) ? customJumlah : (categorySampleCounts[kategori] || (matching.length > 0 ? matching.length : 1));
        categorySampleCounts[kategori] = count;
        currentType = 'short-term';
        currentKategori = kategori;
        currentJumlah = count;

        if (isGulaKristalMaterial(JENIS)) {
            $(`input[name="analisa_type"][value="${kategori}"]`).prop('checked', true);
        }
        $('#jumlahData').val(count);
        startForm('short-term', count, kategori);

        setTimeout(() => {
            if (matching[0].disposisi) {
                $('select[name="disposisi"]').val(matching[0].disposisi).trigger('change');
            } else {
                $('select[name="disposisi"]').val('').trigger('change');
            }
            updateDraftBadgeStatus();
            if (matching[0].keterangan) {
                $('textarea[name="keterangan"]').val(matching[0].keterangan);
            }

            matching.forEach((rec, idx) => {
                if (idx >= count) return;
                if (rec.brix !== null && rec.brix !== undefined) {
                    $('input[name="brix[]"]').eq(idx).val(rec.brix);
                }
                if (rec.ph !== null && rec.ph !== undefined) {
                    $('input[name="ph[]"]').eq(idx).val(rec.ph);
                }
                if (rec.no_beaker) {
                    $('select[name="no_beaker[]"]').eq(idx).html(getBeakerOptions(rec.no_beaker)).val(rec.no_beaker);
                }
                if (rec.berat_beaker_500 !== null && rec.berat_beaker_500 !== undefined) {
                    $('input[name="berat_beaker_500[]"]').eq(idx).val(rec.berat_beaker_500);
                }
                if (rec.berat_beaker_250 !== null && rec.berat_beaker_250 !== undefined) {
                    $('input[name="berat_beaker_250[]"]').eq(idx).val(rec.berat_beaker_250);
                }
                if (rec.timbang_a !== null && rec.timbang_a !== undefined) {
                    $('input[name="timbang_a[]"]').eq(idx).val(rec.timbang_a);
                }
                if (rec.timbang_b !== null && rec.timbang_b !== undefined) {
                    $('input[name="timbang_b[]"]').eq(idx).val(rec.timbang_b);
                }
                if (rec.kotoran !== null && rec.kotoran !== undefined) {
                    $('input[name="kotoran[]"]').eq(idx).val(rec.kotoran);
                }
                if (rec.rasa) {
                    $('select[name="rasa[]"]').eq(idx).val(rec.rasa);
                }
                if (rec.aroma_pengotor) {
                    $('select[name="aroma_pengotor[]"]').eq(idx).val(rec.aroma_pengotor);
                }
                if (rec.no_cawan) {
                    $('select[name="no_cawan[]"]').eq(idx).html(getCawanOptions(rec.no_cawan)).val(rec.no_cawan);
                }
                if (rec.berat_cawan !== null && rec.berat_cawan !== undefined) {
                    $('input[name="berat_cawan[]"]').eq(idx).val(rec.berat_cawan);
                }
                if (rec.timbang_aa !== null && rec.timbang_aa !== undefined) {
                    $('input[name="timbang_aa[]"]').eq(idx).val(rec.timbang_aa);
                }
                if (rec.ka !== null && rec.ka !== undefined) {
                    $('input[name="ka[]"]').eq(idx).val(rec.ka);
                }
                if (rec.warna !== null && rec.warna !== undefined && rec.warna !== '') {
                    setSelectDropdownVal($('select[name="warna[]"]').eq(idx), rec.warna, 'warna');
                }
                if (rec.aroma !== null && rec.aroma !== undefined && rec.aroma !== '') {
                    setSelectDropdownVal($('select[name="aroma[]"]').eq(idx), rec.aroma, 'aroma');
                }
                if (rec.organo) {
                    const $container = $('.organo-cell-container').eq(idx);
                    applyOrganoValueToCell($container, rec.organo);
                }

                calculateRowKotoran(idx);
                calculateRowKa(idx);
            });

            calculateStatistics();
        }, 100);
        return true;
    }

    function populateExistingGaramGula(customJumlah = null) {
        if (!SERVER_GARAM_GULA || !SERVER_GARAM_GULA.length) return false;

        const count = (customJumlah && customJumlah > 0) ? customJumlah : SERVER_GARAM_GULA.length;
        currentType = 'garam-gula';
        currentKategori = 'incoming';
        currentJumlah = count;

        $('#jumlahData').val(count);
        startForm('garam-gula', count, 'incoming');

        setTimeout(() => {
            if (SERVER_GARAM_GULA[0].disposisi) {
                $('select[name="disposisi"]').val(SERVER_GARAM_GULA[0].disposisi).trigger('change');
            } else {
                $('select[name="disposisi"]').val('').trigger('change');
            }
            updateDraftBadgeStatus();
            if (SERVER_GARAM_GULA[0].keterangan) {
                $('textarea[name="keterangan"]').val(SERVER_GARAM_GULA[0].keterangan);
            }
            SERVER_GARAM_GULA.forEach((rec, idx) => {
                if (idx >= count) return;
                if (rec.fisik) $('input[name="fisik[]"]').eq(idx).val(rec.fisik);
                if (rec.ka) $('input[name="%ka[]"]').eq(idx).val(rec.ka);
                if (rec.kotoran) $('input[name="kotoran[]"]').eq(idx).val(rec.kotoran);
                if (rec.nacl) $('input[name="%nacl[]"]').eq(idx).val(rec.nacl);
                if (rec.gross_weight) $('input[name="gross_weight[]"]').eq(idx).val(rec.gross_weight);
                if (rec.warna !== null && rec.warna !== undefined && rec.warna !== '') {
                    setSelectDropdownVal($('select[name="warna[]"]').eq(idx), rec.warna, 'warna');
                }
                if (rec.aroma !== null && rec.aroma !== undefined && rec.aroma !== '') {
                    setSelectDropdownVal($('select[name="aroma[]"]').eq(idx), rec.aroma, 'aroma');
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

    function initFormData() {
        initCategorySampleCounts();
        updateSetupBadges();

        // If user already started a form before AJAX callbacks finished, don't reset!
        if (currentType) {
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const reqKategori = urlParams.get('kategori');

        // Priority 1: If URL explicitly specifies a category (?kategori=incoming, sta, monitoring, long-term)
        if (reqKategori) {
            if (reqKategori === 'long-term') {
                if (isGulaKristalMaterial(JENIS)) {
                    $(`input[name="analisa_type"][value="long-term"]`).prop('checked', true);
                }
                if (SERVER_EXISTING && SERVER_EXISTING.id) {
                    populateExistingLongTerm();
                    return;
                }
                startForm('long-term', 1, 'long-term');
                return;
            }

            if (['incoming', 'sta', 'monitoring'].includes(reqKategori)) {
                if (isGulaKristalMaterial(JENIS)) {
                    $(`input[name="analisa_type"][value="${reqKategori}"]`).prop('checked', true);
                }

                let draft;
                try {
                    draft = JSON.parse(localStorage.getItem(getDraftKey(reqKategori)));
                    if (!draft) {
                        const legacy = JSON.parse(localStorage.getItem(DRAFT_KEY));
                        if (legacy && legacy.setup && legacy.setup.kategori === reqKategori) draft = legacy;
                    }
                } catch (e) {}

                const matching = (SERVER_SHORT_TERM || []).filter(item => (item.kategori || 'incoming') === reqKategori);
                const draftCount = (draft && draft.setup && draft.setup.jumlah) ? parseInt(draft.setup.jumlah) : null;
                const sampleCount = draftCount || (matching.length > 0 ? matching.length : (categorySampleCounts[reqKategori] || 1));

                categorySampleCounts[reqKategori] = sampleCount;
                $('#jumlahData').val(sampleCount);

                const hasDb = populateExistingShortTerm(reqKategori, sampleCount);
                if (hasDb) return;

                if (draft && draft.setup && draft.setup.type === 'short-term' && draft.setup.kategori === reqKategori) {
                    restoreFromDraft(reqKategori);
                    return;
                }

                startForm('short-term', sampleCount, reqKategori);
                return;
            }
        }

        // Priority 2: For non-Gula material (Garam, etc.) that don't have multiple category selection cards
        if (!isGulaKristalMaterial(JENIS)) {
            if (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0) {
                populateExistingGaramGula();
                return;
            }
            startForm('garam-gula', 1, 'incoming');
            return;
        }

        // Priority 3: For Gula without ?kategori param in URL:
        // Always present the Setup Section so the user can choose category & input sample count freely!
        $('#setupSection').show();
        $('#dividerForm, #analisaSection').hide();
        const activeRadio = $('input[name="analisa_type"]:checked').val() || 'incoming';
        $('#jumlahData').val(categorySampleCounts[activeRadio] || 1);
    }

    function handleMulai(e) {
        if (e) e.preventDefault();
        const isGulaKristal = isGulaKristalMaterial(JENIS);
        let selectedVal = isGulaKristal ? $('input[name="analisa_type"]:checked').val() : 'garam-gula';

        if (isGulaKristal && !selectedVal) {
            return Swal.fire({
                icon: 'warning',
                text: 'Pilih jenis analisa terlebih dahulu.'
            });
        }

        const inputJumlah = parseInt($('#jumlahData').val()) || categorySampleCounts[selectedVal] || 1;
        categorySampleCounts[selectedVal] = inputJumlah;

        if (selectedVal === 'long-term') {
            if (SERVER_EXISTING && SERVER_EXISTING.id) {
                populateExistingLongTerm();
            } else {
                startForm('long-term', 1, 'long-term');
            }
            return;
        }

        if (selectedVal === 'garam-gula') {
            if (SERVER_GARAM_GULA && SERVER_GARAM_GULA.length > 0) {
                populateExistingGaramGula(inputJumlah);
            } else {
                startForm('garam-gula', inputJumlah, 'incoming');
            }
            return;
        }

        // Short-term: incoming, sta, monitoring
        const matching = SERVER_SHORT_TERM ? SERVER_SHORT_TERM.filter(item => (item.kategori || 'incoming') === selectedVal) : [];

        if (matching.length > 0) {
            populateExistingShortTerm(selectedVal, inputJumlah);
        } else {
            // Check draft for this category
            let draft;
            try {
                draft = JSON.parse(localStorage.getItem(getDraftKey(selectedVal)));
                if (!draft) {
                    const legacy = JSON.parse(localStorage.getItem(DRAFT_KEY));
                    if (legacy && legacy.setup && legacy.setup.kategori === selectedVal) draft = legacy;
                }
            } catch (e) {}

            if (draft && draft.setup && draft.setup.type === 'short-term' && draft.setup.kategori === selectedVal) {
                draft.setup.jumlah = inputJumlah;
                restoreFromDraft(selectedVal);
            } else {
                startForm('short-term', inputJumlah, selectedVal);
            }
        }
    }

    function handleApplyJumlah() {
        const newJumlah = parseInt($('#editJumlahInput').val());
        if (!newJumlah || newJumlah <= 0) {
            return Swal.fire({
                icon: 'warning',
                text: 'Masukkan jumlah sampel yang valid (minimal 1).'
            });
        }
        const savedValues = collectArrayValues();

        categorySampleCounts[currentKategori] = newJumlah;
        currentJumlah = newJumlah;
        $('#jumlahData').val(newJumlah);
        renderAccordion(currentType, currentJumlah);
        updateLabels();
        restoreArrayValues(savedValues, newJumlah);

        for (let i = 0; i < newJumlah; i++) {
            calculateRowKotoran(i);
            calculateRowKa(i);
        }

        $('#editJumlahWrapper').slideUp(150);
        $('#btnEditJumlah').show();
        calculateStatistics();
        saveDraft();

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Jumlah sampel ${currentKategori.toUpperCase()} diubah menjadi ${newJumlah}`,
            showConfirmButton: false,
            timer: 1500
        });
    }

    function collectArrayValues() {
        const saved = {};
        $('#formAnalisa').find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (!name || name === '_token') return;
            if (name.endsWith('[]')) {
                if (!saved[name]) saved[name] = [];
                saved[name].push($(this).val());
            } else {
                saved[name] = $(this).val();
            }
        });
        return saved;
    }

    function restoreArrayValues(saved, maxCount) {
        for (const [name, values] of Object.entries(saved)) {
            if (!name.endsWith('[]')) {
                const $el = $(`[name="${name}"]`);
                if ($el.length && values !== undefined && values !== null) {
                    if ($el.is('select')) {
                        $el.val(values).trigger('change');
                    } else {
                        $el.val(values);
                    }
                }
                continue;
            }

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
            } else if (name === 'no_beaker[]') {
                $('select[name="no_beaker[]"]').each(function(i) {
                    if (i < maxCount && values[i]) {
                        $(this).html(getBeakerOptions(values[i])).val(values[i]);
                    }
                });
            } else if (name === 'no_cawan[]') {
                $('select[name="no_cawan[]"]').each(function(i) {
                    if (i < maxCount && values[i]) {
                        $(this).html(getCawanOptions(values[i])).val(values[i]);
                    }
                });
            } else {
                $(`[name="${name}"]`).each(function(i) {
                    if (i < maxCount && values[i] !== undefined && values[i] !== null) {
                        if ($(this).is('select')) {
                            setSelectDropdownVal($(this), values[i]);
                        } else {
                            $(this).val(values[i]);
                        }
                    }
                });
            }
        }
    }

    function handleReset(e) {
        if (e) e.preventDefault();
        const targetType = currentType;
        const targetKategori = currentKategori;

        Swal.fire({
            icon: 'question',
            title: 'Reset Form?',
            text: 'Semua isian form dan draft sementara akan dikosongkan dan kembali ke pemilihan kategori.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (r.isConfirmed) {
                clearDraft();

                Swal.fire({
                    title: 'Mereset form...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('rmpm.analisa.reset-draft', $identitas->id) }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: targetType,
                        kategori: targetKategori,
                    },
                    complete: function() {
                        clearDraft();
                        window.location.href = "{{ route('rmpm.analisa', $identitas->id) }}";
                    }
                });
            }
        });
    }

    function startForm(type, jumlah, kategori = 'incoming') {
        currentType = type;
        currentJumlah = jumlah;
        currentKategori = kategori;
        $('#hiddenAnalisaType').val(type);
        $('#hiddenKategori').val(kategori);

        const newUrl = new URL(window.location.href);
        if (type === 'long-term') {
            newUrl.searchParams.set('kategori', 'long-term');
        } else if (type === 'short-term') {
            newUrl.searchParams.set('kategori', kategori);
        }
        window.history.replaceState({}, '', newUrl);

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
        updateDraftBadgeStatus();
        updateMainFormActionsVisibility();
        saveDraft();
    }

    function updateCategorySwitcherBadges() {
        if (!isGulaKristalMaterial(JENIS)) return;
        const incoming = (SERVER_SHORT_TERM || []).filter(item => (item.kategori || 'incoming') === 'incoming');
        const sta = (SERVER_SHORT_TERM || []).filter(item => item.kategori === 'sta');
        const monitoring = (SERVER_SHORT_TERM || []).filter(item => item.kategori === 'monitoring');
        const hasLongTerm = (SERVER_EXISTING && SERVER_EXISTING.id);

        if (incoming.length > 0) {
            const isDraft = (incoming[0].status === 'draft');
            if (isDraft) {
                $('#badgeCatIncoming').html('<span class="badge bg-warning text-dark py-0 px-1" style="font-size:10px;">Draft</span>').show();
            } else {
                $('#badgeCatIncoming').html(`<span class="badge bg-success text-white py-0 px-1" style="font-size:10px;"><i class="ri-check-line"></i></span>`).show();
            }
        } else {
            $('#badgeCatIncoming').hide();
        }

        if (sta.length > 0) {
            const isDraft = (sta[0].status === 'draft');
            if (isDraft) {
                $('#badgeCatSta').html('<span class="badge bg-warning text-dark py-0 px-1" style="font-size:10px;">Draft</span>').show();
            } else {
                $('#badgeCatSta').html(`<span class="badge bg-success text-white py-0 px-1" style="font-size:10px;"><i class="ri-check-line"></i></span>`).show();
            }
        } else {
            $('#badgeCatSta').hide();
        }

        if (monitoring.length > 0) {
            const isDraft = (monitoring[0].status === 'draft');
            if (isDraft) {
                $('#badgeCatMonitoring').html('<span class="badge bg-warning text-dark py-0 px-1" style="font-size:10px;">Draft</span>').show();
            } else {
                $('#badgeCatMonitoring').html(`<span class="badge bg-success text-white py-0 px-1" style="font-size:10px;"><i class="ri-check-line"></i></span>`).show();
            }
        } else {
            $('#badgeCatMonitoring').hide();
        }

        if (hasLongTerm) {
            const isDraft = (SERVER_EXISTING.status === 'draft');
            if (isDraft) {
                $('#badgeCatLongTerm').html('<span class="badge bg-warning text-dark py-0 px-1" style="font-size:10px;">Draft</span>').show();
            } else {
                $('#badgeCatLongTerm').html('<span class="badge bg-success text-white py-0 px-1" style="font-size:10px;"><i class="ri-check-line"></i></span>').show();
            }
        } else {
            $('#badgeCatLongTerm').hide();
        }
    }

    function switchCategoryDirectly(targetCat) {
        const activeKey = currentType === 'long-term' ? 'long-term' : (currentKategori || 'incoming');
        if (targetCat === activeKey) {
            return;
        }

        // Save current category draft before switching
        saveDraft();

        // Update URL query param quietly without full reload
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('kategori', targetCat);
        window.history.replaceState({}, '', newUrl);

        if (targetCat === 'long-term') {
            if (isGulaKristalMaterial(JENIS)) {
                $(`input[name="analisa_type"][value="long-term"]`).prop('checked', true);
            }
            if (SERVER_EXISTING && SERVER_EXISTING.id) {
                populateExistingLongTerm();
            } else {
                startForm('long-term', 1, 'long-term');
            }
            return;
        }

        // Short term categories: incoming, sta, monitoring
        if (isGulaKristalMaterial(JENIS)) {
            $(`input[name="analisa_type"][value="${targetCat}"]`).prop('checked', true);
        }

        const targetJumlah = categorySampleCounts[targetCat] || 1;

        const hasDb = populateExistingShortTerm(targetCat, targetJumlah);
        if (!hasDb) {
            let draft;
            try {
                draft = JSON.parse(localStorage.getItem(getDraftKey(targetCat)));
                if (!draft) {
                    const legacy = JSON.parse(localStorage.getItem(DRAFT_KEY));
                    if (legacy && legacy.setup && legacy.setup.kategori === targetCat) draft = legacy;
                }
            } catch (e) {}

            if (draft && draft.setup && draft.setup.type === 'short-term' && draft.setup.kategori === targetCat) {
                restoreFromDraft(targetCat);
            } else {
                startForm('short-term', targetJumlah, targetCat);
            }
        }
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

        // Update active category switcher pill
        const activeKey = currentType === 'long-term' ? 'long-term' : (currentKategori || 'incoming');
        $('.btn-category-switch').removeClass('active');
        $(`.btn-category-switch[data-cat="${activeKey}"]`).addClass('active');
        updateCategorySwitcherBadges();
    }

    // ─────────────────────────────────────────────
    // RENDER ACCORDION / TABLES
    // ─────────────────────────────────────────────
    function renderAccordion(type, jumlah) {
        if (type === 'short-term') {
            $('#analisaAccordion').html(renderShortTermTable(jumlah));
            calculateStatistics();
            updateMainFormActionsVisibility();
            return;
        }

        if (type === 'garam-gula') {
            $('#analisaAccordion').html(renderGaramGulaTable(jumlah));
            calculateStatistics();
            updateMainFormActionsVisibility();
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

    function renderOrganoCell(fieldKey, tabIndex = null) {
        const organoOpts = getMasterOptions('organo');
        let optHtml = `<option value="">-- Pilih Organo --</option>`;
        organoOpts.forEach(opt => {
            const label = typeof opt === 'object' ? opt.label : opt;
            const val = typeof opt === 'object' ? opt.value : opt;
            const isCustom = typeof opt === 'object' ? opt.isCustom : (val.includes('Lain-lain') || val.includes('Campuran'));
            optHtml += `<option value="${val}" data-custom="${isCustom ? '1' : '0'}">${label}</option>`;
        });

        const tabAttr = tabIndex ? `tabindex="${tabIndex}"` : '';

        return `
            <div class="organo-cell-container">
                <select class="form-select form-select-sm select-organo-dropdown calc-trigger" ${tabAttr}>
                    ${optHtml}
                </select>
                <div class="organo-custom-input-box mt-1" style="display:none;">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm input-organo-custom-text calc-trigger" ${tabAttr} placeholder="Tulis rincian..." />
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-close-organo-custom" title="Kembali ke pilihan dropdown">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="${fieldKey}" class="input-organo-final calc-trigger" value="" />
            </div>`;
    }

    function renderDropdownCell(field, tabIndex = null) {
        const labelText = field.label || (field.masterKey ? (field.masterKey.charAt(0).toUpperCase() + field.masterKey.slice(1)) : 'Opsi');
        const opts = getMasterOptions(field.masterKey);
        let optHtml = `<option value="">-- Pilih ${labelText} --</option>`;
        opts.forEach(opt => {
            const val = typeof opt === 'object' ? opt.value : opt;
            const label = typeof opt === 'object' ? opt.label : opt;
            optHtml += `<option value="${val}">${label}</option>`;
        });
        const tabAttr = tabIndex ? `tabindex="${tabIndex}"` : '';
        return `
            <select class="form-select form-select-sm select-${field.key.replace('[]','')} calc-trigger" name="${field.key}" ${tabAttr}>
                ${optHtml}
            </select>`;
    }

    // ─────────────────────────────────────────────
    // SHORT-TERM TABLE RENDER (3-TAB WORKSHEET WORKFLOW)
    // ─────────────────────────────────────────────
    function renderShortTermTable(jumlah) {
        let rowsRingkasan = '';
        let rowsKotoran = '';
        let rowsKa = '';

        for (let i = 1; i <= jumlah; i++) {
            const rowIdx = i - 1;

            // Smart Auto-Assign Glassware: Automatically sequence clean available glassware based on other SPBs today
            const defaultBeaker = getSmartBeakerForSample(rowIdx);
            const tare500 = (glasswareData.beaker_500 && glasswareData.beaker_500[defaultBeaker] !== undefined) ? glasswareData.beaker_500[defaultBeaker] : '';
            const tare250 = (glasswareData.beaker_250 && glasswareData.beaker_250[defaultBeaker] !== undefined) ? glasswareData.beaker_250[defaultBeaker] : '';

            const defaultCawan = getSmartCawanForSample(rowIdx);
            const tareCawan = (glasswareData.cawan && glasswareData.cawan[defaultCawan] !== undefined) ? glasswareData.cawan[defaultCawan] : '';

            // 1. Tab 1: Ringkasan & Parameter (Resume)
            rowsRingkasan += `
                <tr>
                    <td class="td-sampel"><span class="sampel-badge">${i}</span></td>
                    <td style="min-width: 110px;">
                        <input type="text"
                            inputmode="decimal"
                            class="form-control form-control-sm calc-trigger numeric-clean-input input-brix"
                            name="brix[]"
                            tabindex="${100 + i}"
                            placeholder="0.00"
                            autocomplete="off">
                    </td>
                    <td style="min-width: 110px;">
                        <input type="text"
                            inputmode="decimal"
                            class="form-control form-control-sm calc-trigger numeric-clean-input input-ph"
                            name="ph[]"
                            tabindex="${200 + i}"
                            placeholder="0.00"
                            autocomplete="off">
                    </td>
                    <td style="min-width: 120px;">
                        <div class="input-group input-group-sm">
                            <input type="text"
                                inputmode="decimal"
                                class="form-control form-control-sm calc-trigger numeric-clean-input input-kotoran"
                                name="kotoran[]"
                                tabindex="${300 + i}"
                                placeholder="0.00"
                                autocomplete="off">
                            <span class="input-group-text px-1 text-muted" style="font-size:10px;" title="Terhitung otomatis dari Lembar % Kotoran">%</span>
                        </div>
                    </td>
                    <td style="min-width: 120px;">
                        <div class="input-group input-group-sm">
                            <input type="text"
                                inputmode="decimal"
                                class="form-control form-control-sm calc-trigger numeric-clean-input input-ka"
                                name="ka[]"
                                tabindex="${400 + i}"
                                placeholder="0.00"
                                autocomplete="off">
                            <span class="input-group-text px-1 text-muted" style="font-size:10px;" title="Terhitung otomatis dari Lembar % Kadar Air">%</span>
                        </div>
                    </td>
                    <td style="min-width: 160px;">
                        ${renderOrganoCell('organo[]', 500 + i)}
                    </td>
                    <td style="min-width: 130px;">
                        ${renderDropdownCell({ key: 'warna[]', masterKey: 'warna' }, 600 + i)}
                    </td>
                    <td style="min-width: 130px;">
                        ${renderDropdownCell({ key: 'aroma[]', masterKey: 'aroma' }, 700 + i)}
                    </td>
                </tr>`;

            // 2. Tab 2: Lembar % Kotoran
            rowsKotoran += `
                <tr>
                    <td class="td-sampel"><span class="sampel-badge">${i}</span></td>
                    <td style="min-width: 170px;">
                        <select class="form-select form-select-sm select-no-beaker" name="no_beaker[]" tabindex="${100 + i}">
                            ${getBeakerOptions(defaultBeaker)}
                        </select>
                        <input type="hidden" name="berat_beaker_500[]" value="${tare500}">
                        <input type="hidden" name="berat_beaker_250[]" value="${tare250}">
                    </td>
                    <td style="min-width: 140px;">
                        <input type="text" inputmode="decimal" class="form-control form-control-sm calc-trigger-kotoran numeric-clean-input" name="timbang_a[]" tabindex="${200 + i}" placeholder="0.00" autocomplete="off">
                        <small class="text-muted d-block" style="font-size:10px;margin-top:2px;">Tare B500: <span class="val-tare-500">${tare500 ? tare500 + 'g' : '-'}</span></small>
                    </td>
                    <td style="min-width: 140px;">
                        <input type="text" inputmode="decimal" class="form-control form-control-sm calc-trigger-kotoran numeric-clean-input" name="timbang_b[]" tabindex="${300 + i}" placeholder="0.00" autocomplete="off">
                        <small class="text-muted d-block" style="font-size:10px;margin-top:2px;">Tare B250: <span class="val-tare-250">${tare250 ? tare250 + 'g' : '-'}</span></small>
                    </td>
                    <td style="min-width: 140px;" class="text-center align-middle">
                        <span class="badge badge-kotoran-calc bg-light text-muted fs-7">-%</span>
                    </td>
                </tr>`;

            // 3. Tab 3: Lembar % Kadar Air
            rowsKa += `
                <tr>
                    <td class="td-sampel"><span class="sampel-badge">${i}</span></td>
                    <td style="min-width: 170px;">
                        <select class="form-select form-select-sm select-no-cawan" name="no_cawan[]" tabindex="${100 + i}">
                            ${getCawanOptions(defaultCawan)}
                        </select>
                        <input type="hidden" name="berat_cawan[]" value="${tareCawan}">
                    </td>
                    <td style="min-width: 150px;">
                        <input type="text" inputmode="decimal" class="form-control form-control-sm calc-trigger-ka numeric-clean-input" name="timbang_aa[]" tabindex="${200 + i}" placeholder="0.00" autocomplete="off">
                        <small class="text-muted d-block" style="font-size:10px;margin-top:2px;">Tare Cawan: <span class="val-tare-cawan">${tareCawan ? tareCawan + 'g' : '-'}</span></small>
                    </td>
                    <td style="min-width: 150px;" class="text-center align-middle">
                        <span class="badge badge-ka-calc bg-light text-muted fs-7">-%</span>
                    </td>
                </tr>`;
        }

        const beakerUsed = glasswareData.total_today?.beaker || 0;
        const cawanUsed = glasswareData.total_today?.cawan || 0;
        const beakerRemain = Math.max(0, 90 - beakerUsed);
        const cawanRemain = Math.max(0, 240 - cawanUsed);

        const disposisiHtml = `
            <div class="disposisi-section mt-4 p-3 bg-light rounded border">
                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-1.5">
                    <i class="ri-checkbox-circle-fill text-success"></i> Keputusan Akhir Analisa
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1">
                            Disposisi <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="disposisi" required>
                            <option value="">-- Pilih Disposisi --</option>
                            <option value="Release">Release</option>
                            <option value="Reject">Reject</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold mb-1">
                            Keterangan <span class="text-muted fw-normal" style="font-size:11px;">(opsional)</span>
                        </label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Tambahkan catatan hasil analisa atau disposisi..."></textarea>
                    </div>
                </div>
            </div>`;

        return `
            <!-- Navigation Tabs -->
            <ul class="nav nav-worksheet mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tabRingkasanBtn" data-bs-toggle="tab" data-bs-target="#paneRingkasan" type="button" role="tab">
                        <i class="ri-table-line me-1"></i> 1. Ringkasan & Parameter (Resume)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabKotoranBtn" data-bs-toggle="tab" data-bs-target="#paneKotoran" type="button" role="tab">
                        <i class="ri-flask-line me-1"></i> 2. Lembar % Kotoran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabKadarAirBtn" data-bs-toggle="tab" data-bs-target="#paneKadarAir" type="button" role="tab">
                        <i class="ri-drop-line me-1"></i> 3. Lembar % Kadar Air
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="shortTermTabContent">

                <!-- TAB 1: Ringkasan & Parameter (Resume Table) -->
                <div class="tab-pane fade show active" id="paneRingkasan" role="tabpanel">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-info btn-switch-tab" data-target-tab="#paneKotoran" title="Buka Lembar Kalkulasi % Kotoran">
                                <i class="ri-flask-line me-1"></i> Lembar % Kotoran
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success btn-switch-tab" data-target-tab="#paneKadarAir" title="Buka Lembar Kalkulasi % Kadar Air">
                                <i class="ri-drop-line me-1"></i> Lembar % Kadar Air
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5" id="btnQuickSetAllOrganoOk" title="Set seluruh sampel Organo/Rasa menjadi 'OK'">
                                <i class="ri-checkbox-circle-line me-1"></i> Set Rasa "OK"
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5" id="btnQuickSetAllWarnaAroma" title="Set seluruh Warna ke standar dan Aroma ke 'OK'">
                                <i class="ri-palette-line me-1"></i> Set Warna & Aroma
                            </button>
                        </div>
                    </div>

                    <div class="short-term-table-wrapper mb-3">
                        <table class="analisa-table" id="tableAnalisaShortTerm">
                            <thead>
                                <tr>
                                    <th class="th-sampel">Sampel</th>
                                    <th class="th-data-col" data-col-idx="0" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>Brix</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="0" title="Salin nilai baris 1 ke semua baris (Brix)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="1" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>pH</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="1" title="Salin nilai baris 1 ke semua baris (pH)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="2" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>% Kotoran</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="2" title="Salin nilai baris 1 ke semua baris (% Kotoran)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="3" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>% KA</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="3" title="Salin nilai baris 1 ke semua baris (% KA)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="4" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>Organo (Rasa)</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="4" title="Salin nilai baris 1 ke semua baris (Organo)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="5" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>Warna</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="5" title="Salin nilai baris 1 ke semua baris (Warna)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                    <th class="th-data-col" data-col-idx="6" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span>Aroma</span>
                                            <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="6" title="Salin nilai baris 1 ke semua baris (Aroma)"><i class="ri-arrow-down-double-line"></i></button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>${rowsRingkasan}</tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td class="text-center"><span class="badge bg-primary px-2 py-1">AVERAGE</span></td>
                                    <td><span id="tblAvgBrix" class="text-primary fw-bold">-</span></td>
                                    <td><span id="tblAvgPh" class="text-primary fw-bold">-</span></td>
                                    <td><span id="tblAvgKotoran" class="text-primary fw-bold">-</span></td>
                                    <td><span id="tblAvgKa" class="text-primary fw-bold">-</span></td>
                                    <td><div id="tblOrganoPercent" class="d-flex flex-wrap gap-1 align-items-center">-</div></td>
                                    <td><span class="text-muted small">-</span></td>
                                    <td><span class="text-muted small">-</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    ${disposisiHtml}
                </div>

                <!-- TAB 2: Lembar % Kotoran -->
                <div class="tab-pane fade" id="paneKotoran" role="tabpanel">
                    <div class="card border shadow-sm mb-3">
                        <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-primary d-flex align-items-center gap-1.5">
                                    <i class="ri-flask-line"></i> Lembar Uji % Kotoran (Larutan Gula)
                                </h6>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                    <span class="formula-badge">% Kotoran = [ (A - Tare B500) - (B - Tare B250) ] / (A - Tare B500) &times; 100%</span>
                                    <span class="text-muted small">(Maks 10.00%)</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px;">
                                    🧪 Beaker: <strong class="text-primary" id="badgeBeakerUsed">${beakerUsed}/90</strong> <span class="text-muted">(Sisa: <span id="badgeBeakerRemain">${beakerRemain}</span>)</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5 btn-open-glassware-modal" data-default-filter="beaker">
                                    <i class="ri-history-line me-1"></i> Log Transaksi Beaker
                                </button>
                                <button type="button" class="btn btn-sm btn-primary py-1 px-2.5 btn-switch-tab" data-target-tab="#paneRingkasan">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Ringkasan
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="short-term-table-wrapper border-0">
                                <table class="analisa-table" id="tableLembarKotoran">
                                    <thead>
                                        <tr>
                                            <th class="th-sampel">Sampel</th>
                                            <th class="th-data-col" data-col-idx="0" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>No. Beaker <span class="badge bg-secondary ms-1">1-90</span></span>
                                                    <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="0" title="Salin nilai baris 1 ke semua baris (No. Beaker)"><i class="ri-arrow-down-double-line"></i></button>
                                                </div>
                                            </th>
                                            <th class="th-data-col" data-col-idx="1" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Beaker 500g <span class="unit-badge">gr</span></span>
                                                    <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="1" title="Salin nilai baris 1 ke semua baris (Beaker 500g)"><i class="ri-arrow-down-double-line"></i></button>
                                                </div>
                                            </th>
                                            <th class="th-data-col" data-col-idx="2" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Beaker 250g <span class="unit-badge">gr</span></span>
                                                    <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="2" title="Salin nilai baris 1 ke semua baris (Beaker 250g)"><i class="ri-arrow-down-double-line"></i></button>
                                                </div>
                                            </th>
                                            <th class="text-center">Hasil % Kotoran</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rowsKotoran}</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2.5 px-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="text-muted small">
                                <i class="ri-information-line me-1 text-primary"></i> Data kalkulasi % Kotoran otomatis disinkronkan ke Ringkasan (Tab 1).
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-warning px-3 btn-draft-worksheet">
                                    <i class="mdi mdi-content-save-edit-outline me-1"></i> Simpan Sementara
                                </button>
                                <button type="button" class="btn btn-sm btn-success px-3 btn-apply-worksheet" data-target="kotoran">
                                    <i class="ri-check-double-line me-1"></i> Terapkan Hasil ke Ringkasan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Lembar % Kadar Air -->
                <div class="tab-pane fade" id="paneKadarAir" role="tabpanel">
                    <div class="card border shadow-sm mb-3">
                        <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-success d-flex align-items-center gap-1.5">
                                    <i class="ri-drop-line"></i> Lembar Uji % Kadar Air (Oven Pengeringan)
                                </h6>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                    <span class="formula-badge" style="background:#dcfce7; color:#15803d; border-color:#86efac;">% KA = [ 5.00 - (AA - Tare Cawan) ] / 5.00 &times; 100%</span>
                                    <span class="text-muted small">(Maks 3.00%)</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px;">
                                    🥣 Cawan: <strong class="text-success" id="badgeCawanUsed">${cawanUsed}/240</strong> <span class="text-muted">(Sisa: <span id="badgeCawanRemain">${cawanRemain}</span>)</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5 btn-open-glassware-modal" data-default-filter="cawan">
                                    <i class="ri-history-line me-1"></i> Log Transaksi Cawan
                                </button>
                                <button type="button" class="btn btn-sm btn-primary py-1 px-2.5 btn-switch-tab" data-target-tab="#paneRingkasan">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Ringkasan
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="short-term-table-wrapper border-0">
                                <table class="analisa-table" id="tableLembarKa">
                                    <thead>
                                        <tr>
                                            <th class="th-sampel">Sampel</th>
                                            <th class="th-data-col" data-col-idx="0" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>No. Cawan <span class="badge bg-secondary ms-1">1-240</span></span>
                                                    <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="0" title="Salin nilai baris 1 ke semua baris (No. Cawan)"><i class="ri-arrow-down-double-line"></i></button>
                                                </div>
                                            </th>
                                            <th class="th-data-col" data-col-idx="1" title="Klik kolom untuk blok & copy-paste / Ctrl+D">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Bobot Akhir (AA) <span class="unit-badge">gr</span></span>
                                                    <button type="button" class="btn btn-fill-col-down ms-1" data-col-idx="1" title="Salin nilai baris 1 ke semua baris (Bobot Akhir AA)"><i class="ri-arrow-down-double-line"></i></button>
                                                </div>
                                            </th>
                                            <th class="text-center">Hasil % Kadar Air</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rowsKa}</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2.5 px-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="text-muted small">
                                <i class="ri-information-line me-1 text-success"></i> Data kalkulasi % Kadar Air otomatis disinkronkan ke Ringkasan (Tab 1).
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-warning px-3 btn-draft-worksheet">
                                    <i class="mdi mdi-content-save-edit-outline me-1"></i> Simpan Sementara
                                </button>
                                <button type="button" class="btn btn-sm btn-success px-3 btn-apply-worksheet" data-target="ka">
                                    <i class="ri-check-double-line me-1"></i> Terapkan Hasil ke Ringkasan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>`;
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
            GARAM_GULA_FIELDS.forEach((f, cIdx) => {
                const tabIndex = (cIdx + 1) * 100 + i;
                if (f.type === 'organo-select') {
                    tds += `<td>${renderOrganoCell(f.key, tabIndex)}</td>`;
                } else if (f.type === 'dropdown') {
                    tds += `<td>${renderDropdownCell(f, tabIndex)}</td>`;
                } else if (f.type === 'text') {
                    tds += `
                        <td>
                            <input type="text"
                                class="form-control form-control-sm upper-input"
                                name="${f.key}"
                                tabindex="${tabIndex}"
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
                                tabindex="${tabIndex}"
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
    // GLASSWARE TRANSACTION AUDIT & REAL-TIME LOG TABLE
    // ─────────────────────────────────────────────
    let currentGwFilter = 'all';
    let currentGwSearch = '';

    function renderGlasswareTransactionsModal() {
        const beakers = glasswareData.beaker_transactions || [];
        const cawans = glasswareData.cawan_transactions || [];
        const totalBeakerUsed = glasswareData.total_today?.beaker || 0;
        const totalCawanUsed = glasswareData.total_today?.cawan || 0;
        const beakerRemain = Math.max(0, 90 - totalBeakerUsed);
        const cawanRemain = Math.max(0, 240 - totalCawanUsed);

        // Update KPIs in modal
        $('#modalKpiBeaker').text(`${totalBeakerUsed} / 90`);
        $('#modalKpiBeakerRemain').text(`Sisa: ${beakerRemain} Unit`);
        $('#modalKpiCawan').text(`${totalCawanUsed} / 240`);
        $('#modalKpiCawanRemain').text(`Sisa: ${cawanRemain} Unit`);
        $('#modalKpiTotalLogs').text(`${beakers.length + cawans.length} Transaksi`);

        $('#cntAllGw').text(beakers.length + cawans.length);
        $('#cntBeakerGw').text(beakers.length);
        $('#cntCawanGw').text(cawans.length);

        // Build list of all transactions
        let list = [];
        beakers.forEach(item => {
            list.push({
                type: 'beaker',
                id: item.id,
                tool_no: item.no_beaker,
                time: item.created_at,
                spb: item.no_spb || '-',
                jenis: item.jenis || '-',
                supplier: item.supplier || '-',
                kategori: item.kategori || '-',
                analyst: item.analyst_name || 'Analis QC',
                measure_data: `Timbang A: <strong>${item.timbang_a || '-'}g</strong> | B: <strong>${item.timbang_b || '-'}g</strong> | % Kotoran: <strong>${item.kotoran ? item.kotoran + '%' : '-'}</strong>`
            });
        });

        cawans.forEach(item => {
            list.push({
                type: 'cawan',
                id: item.id,
                tool_no: item.no_cawan,
                time: item.created_at,
                spb: item.no_spb || '-',
                jenis: item.jenis || '-',
                supplier: item.supplier || '-',
                kategori: item.kategori || '-',
                analyst: item.analyst_name || 'Analis QC',
                measure_data: `Bobot Akhir (AA): <strong>${item.timbang_aa || '-'}g</strong> | % KA: <strong>${item.ka ? item.ka + '%' : '-'}</strong>`
            });
        });

        // Sort by time descending
        list.sort((a, b) => new Date(b.time) - new Date(a.time));

        // Apply filter
        if (currentGwFilter === 'beaker') {
            list = list.filter(x => x.type === 'beaker');
        } else if (currentGwFilter === 'cawan') {
            list = list.filter(x => x.type === 'cawan');
        }

        // Apply search
        if (currentGwSearch && currentGwSearch.trim() !== '') {
            const q = currentGwSearch.toLowerCase().trim();
            list = list.filter(x => 
                (x.tool_no && x.tool_no.toString().toLowerCase().includes(q)) ||
                (x.spb && x.spb.toLowerCase().includes(q)) ||
                (x.jenis && x.jenis.toLowerCase().includes(q)) ||
                (x.supplier && x.supplier.toLowerCase().includes(q)) ||
                (x.analyst && x.analyst.toLowerCase().includes(q)) ||
                (x.kategori && x.kategori.toLowerCase().includes(q))
            );
        }

        const $tbody = $('#tbodyGwTransactions');
        $tbody.empty();

        if (list.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="ri-inbox-line fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <span class="fw-semibold">Tidak ada data transaksi pemakaian glassware yang ditemukan.</span>
                        <p class="small text-muted mb-0 mt-1">Nomor beaker atau cawan belum digunakan hari ini atau tidak sesuai kata kunci pencarian.</p>
                    </td>
                </tr>
            `);
            return;
        }

        list.forEach((item, idx) => {
            let timeStr = '-';
            if (item.time) {
                try {
                    const d = new Date(item.time);
                    if (!isNaN(d.getTime())) {
                        const hh = String(d.getHours()).padStart(2, '0');
                        const mm = String(d.getMinutes()).padStart(2, '0');
                        timeStr = `${hh}:${mm}`;
                    } else if (item.time.includes(' ')) {
                        timeStr = item.time.split(' ')[1].substring(0, 5);
                    }
                } catch (e) {
                    timeStr = item.time.substring(0, 5);
                }
            }
            
            let toolBadge = '';
            if (item.type === 'beaker') {
                const used = (glasswareData.usage?.beaker && glasswareData.usage.beaker[item.tool_no]) ? glasswareData.usage.beaker[item.tool_no] : 1;
                toolBadge = `
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-7">
                        <i class="ri-flask-line me-1"></i> Beaker No. <strong>${item.tool_no}</strong>
                    </span>
                    <span class="badge bg-light text-muted border ms-1 font-monospace" style="font-size:10px;" title="Total pemakaian hari ini">[${used}/8x]</span>
                `;
            } else {
                const used = (glasswareData.usage?.cawan && glasswareData.usage.cawan[item.tool_no]) ? glasswareData.usage.cawan[item.tool_no] : 1;
                toolBadge = `
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-7">
                        <i class="ri-contrast-drop-2-line me-1"></i> Cawan No. <strong>${item.tool_no}</strong>
                    </span>
                    <span class="badge bg-light text-muted border ms-1 font-monospace" style="font-size:10px;" title="Total pemakaian hari ini">[${used}/2x]</span>
                `;
            }

            const katBadge = item.kategori.toLowerCase() === 'incoming' 
                ? '<span class="badge bg-info-subtle text-info border border-info-subtle">Incoming</span>' 
                : `<span class="badge bg-secondary-subtle text-secondary border">${item.kategori}</span>`;

            const tr = `
                <tr>
                    <td class="text-center fw-medium text-muted">${idx + 1}</td>
                    <td>
                        <span class="fw-semibold text-dark">${timeStr}</span> <small class="text-muted">WIB</small>
                    </td>
                    <td>${toolBadge}</td>
                    <td>
                        <span class="fw-semibold text-dark">${item.spb}</span>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">${item.jenis}</div>
                        <small class="text-muted">${item.supplier}</small>
                    </td>
                    <td>${katBadge}</td>
                    <td>
                        <div class="small text-secondary">${item.measure_data}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="ri-user-3-line text-muted"></i>
                            <span class="fw-medium text-dark">${item.analyst}</span>
                        </div>
                    </td>
                </tr>
            `;
            $tbody.append(tr);
        });
    }

    function calculateRowKotoran(rowIdx) {
        const $tabKotoranRow = $('#tableLembarKotoran tbody tr').eq(rowIdx);
        if (!$tabKotoranRow.length) return;

        const tare500Str = $tabKotoranRow.find('input[name="berat_beaker_500[]"]').val();
        const tare250Str = $tabKotoranRow.find('input[name="berat_beaker_250[]"]').val();
        const timbangAStr = $tabKotoranRow.find('input[name="timbang_a[]"]').val();
        const timbangBStr = $tabKotoranRow.find('input[name="timbang_b[]"]').val();

        const t500 = parseRapidNumericValue(tare500Str);
        const t250 = parseRapidNumericValue(tare250Str);
        const a = parseRapidNumericValue(timbangAStr);
        const b = parseRapidNumericValue(timbangBStr);

        const $badge = $tabKotoranRow.find('.badge-kotoran-calc');
        const $tabRingkasanInput = $('#tableAnalisaShortTerm tbody tr').eq(rowIdx).find('input[name="kotoran[]"]');
        const maxKotoran = (dynamicStandards && dynamicStandards.kotoran && dynamicStandards.kotoran.max !== null) ? dynamicStandards.kotoran.max : 10.0;
        const maxKotoranLabel = (dynamicStandards && dynamicStandards.kotoran && dynamicStandards.kotoran.label) ? dynamicStandards.kotoran.label : `${maxKotoran}%`;

        if (!isNaN(a) && !isNaN(b) && a > 0 && b > 0) {
            if (isNaN(t500) || t500 <= 0 || isNaN(t250) || t250 <= 0) {
                $tabRingkasanInput.val('');
                $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html('<i class="ri-alert-line me-1"></i>Pilih No. Beaker');
            } else if (a <= t500) {
                $tabRingkasanInput.val('');
                $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>Beaker 500g (${a}g) &le; Tare (${t500.toFixed(2)}g)`);
            } else if (b <= t250) {
                $tabRingkasanInput.val('');
                $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>Beaker 250g (${b}g) &le; Tare (${t250.toFixed(2)}g)`);
            } else {
                const netA = a - t500;
                const netB = b - t250;

                if (netA <= 0) {
                    $tabRingkasanInput.val('');
                    $badge.removeClass().addClass('badge bg-warning text-dark fs-7').text('Net Beaker 500g <= 0');
                } else if (netB > netA) {
                    $tabRingkasanInput.val('');
                    $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>Anomali (Net B250 ${netB.toFixed(2)}g > Net B500 ${netA.toFixed(2)}g)`);
                } else {
                    const kotoranVal = ((netA - netB) / netA) * 100;
                    const formatted = kotoranVal.toFixed(2);

                    $tabRingkasanInput.val(formatted);

                    if (kotoranVal > maxKotoran) {
                        $badge.removeClass().addClass('badge bg-danger fs-7').html(`<i class="ri-error-warning-line me-1"></i>${formatted}% (Over Limit > ${maxKotoranLabel})`);
                    } else if (kotoranVal < 0) {
                        $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>${formatted}% (Anomali < 0%)`);
                    } else {
                        $badge.removeClass().addClass('badge bg-success fs-7').html(`<i class="ri-checkbox-circle-line me-1"></i>${formatted}% (OK)`);
                    }
                }
            }
        } else {
            if (timbangAStr || timbangBStr) {
                $tabRingkasanInput.val('');
            }
            $badge.removeClass().addClass('badge bg-light text-muted fs-7').text('-%');
        }
        calculateStatistics();
    }

    function calculateRowKa(rowIdx) {
        const $tabKaRow = $('#tableLembarKa tbody tr').eq(rowIdx);
        if (!$tabKaRow.length) return;

        const tareCawanStr = $tabKaRow.find('input[name="berat_cawan[]"]').val();
        const timbangAaStr = $tabKaRow.find('input[name="timbang_aa[]"]').val();

        const tCawan = parseRapidNumericValue(tareCawanStr);
        const aa = parseRapidNumericValue(timbangAaStr);

        const $badge = $tabKaRow.find('.badge-ka-calc');
        const $tabRingkasanInput = $('#tableAnalisaShortTerm tbody tr').eq(rowIdx).find('input[name="ka[]"]');
        const defaultKaMax = JENIS.toLowerCase().includes('garam') ? 8.0 : 3.0;
        const maxKa = (dynamicStandards && dynamicStandards.ka && dynamicStandards.ka.max !== null) ? dynamicStandards.ka.max : defaultKaMax;
        const maxKaLabel = (dynamicStandards && dynamicStandards.ka && dynamicStandards.ka.label) ? dynamicStandards.ka.label : `${maxKa}%`;

        if (!isNaN(aa) && aa > 0) {
            if (isNaN(tCawan) || tCawan <= 0) {
                $tabRingkasanInput.val('');
                $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html('<i class="ri-alert-line me-1"></i>Pilih No. Cawan');
            } else if (aa <= tCawan) {
                $tabRingkasanInput.val('');
                $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>AA (${aa}g) &le; Tare (${tCawan.toFixed(2)}g)`);
            } else {
                const netBobot = aa - tCawan;
                if (netBobot > 5.00) {
                    $tabRingkasanInput.val('');
                    $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>Anomali (Net ${netBobot.toFixed(2)}g > 5.00g)`);
                } else {
                    const kaVal = ((5.00 - netBobot) / 5.00) * 100;
                    const formatted = kaVal.toFixed(2);

                    $tabRingkasanInput.val(formatted);

                    if (kaVal > maxKa) {
                        $badge.removeClass().addClass('badge bg-danger fs-7').html(`<i class="ri-error-warning-line me-1"></i>${formatted}% (Over Limit > ${maxKaLabel})`);
                    } else if (kaVal < 0) {
                        $badge.removeClass().addClass('badge bg-warning text-dark fs-7').html(`<i class="ri-alert-line me-1"></i>${formatted}% (Anomali < 0%)`);
                    } else {
                        $badge.removeClass().addClass('badge bg-success fs-7').html(`<i class="ri-checkbox-circle-line me-1"></i>${formatted}% (OK)`);
                    }
                }
            }
        } else {
            if (timbangAaStr) {
                $tabRingkasanInput.val('');
            }
            $badge.removeClass().addClass('badge bg-light text-muted fs-7').text('-%');
        }
        calculateStatistics();
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
            $('#statAvgKotoran').text(avgKotoran !== null ? avgKotoran.toFixed(2) + '%' : '-');
            $('#tblAvgKotoran').text(avgKotoran !== null ? avgKotoran.toFixed(2) + '%' : '-');

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
            $('#tblAvgKotoranGG').text(avgKotoran !== null ? avgKotoran.toFixed(2) + '%' : '-');

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
            'SAMPEL', 'SAMPLE', 'NO', 'NO.', '#', 'FISIK', 'NACL', '%NACL', 'GROSS WEIGHT', 'WEIGHT',
            'TIMBANG', 'TIMBANG A', 'TIMBANG B', 'TIMBANG AA', 'BOBOT AKHIR', 'BEAKER', 'CAWAN', 'RASA', 'PENGOTOR'
        ];
        let matchCount = 0;
        for (const col of rowCols) {
            const clean = (col || '').toString().toUpperCase().trim();
            if (headerKeywords.some(k => clean === k || clean.includes(k))) {
                matchCount++;
            }
        }
        return matchCount >= Math.min(2, rowCols.length);
    }

    // ─────────────────────────────────────────────
    // EXCEL-STYLE TABLE SELECTION & NAVIGATION SYSTEM
    // ─────────────────────────────────────────────
    let tableSelection = null; // { tableId, startRow, startCol, endRow, endCol, minRow, maxRow, minCol, maxCol }
    let lastMultiSelection = null;
    let isMouseDragging = false;
    let selectionAnchor = null;

    function getTableFields($table) {
        if (!$table || !$table.length) {
            $table = getActiveTable();
        }
        const tableId = $table ? $table.attr('id') : '';
        if (tableId === 'tableLembarKotoran') {
            return TAB_KOTORAN_FIELDS;
        }
        if (tableId === 'tableLembarKa') {
            return TAB_KA_FIELDS;
        }
        if (currentType === 'garam-gula' || tableId === 'tableAnalisaGaramGula') {
            return GARAM_GULA_FIELDS;
        }
        return SHORT_TERM_FIELDS;
    }

    function getFields() {
        return getTableFields(getActiveTable());
    }

    function getActiveTable(contextEl) {
        if (contextEl) {
            const $t = $(contextEl).closest('table.analisa-table');
            if ($t.length) return $t;
        }
        const $focusedT = $(document.activeElement).closest('table.analisa-table');
        if ($focusedT.length) return $focusedT;

        if (tableSelection && tableSelection.tableId) {
            const $selT = $('#' + tableSelection.tableId);
            if ($selT.length) return $selT;
        }

        const $visibleT = $('#shortTermTabContent .tab-pane.active table.analisa-table, #analisaAccordion table.analisa-table:visible');
        if ($visibleT.length) return $visibleT.first();

        return $('#analisaAccordion table.analisa-table').first();
    }

    function getCellCoords(el) {
        const $el = $(el);
        const $td = $el.closest('td');
        const $tr = $td.closest('tr');
        const $table = $tr.closest('table.analisa-table');
        if (!$td.length || !$tr.length || !$tr.parent().is('tbody') || !$table.length) return null;
        const colIdx = $td.index() - 1; // 0-indexed data column (index 0 in DOM is td.td-sampel)
        const rowIdx = $tr.index();
        if (colIdx < 0) return null;
        return { row: rowIdx, col: colIdx, tableId: $table.attr('id'), $table: $table };
    }

    function getCellTd(row, col, $table) {
        if (!$table || !$table.length) {
            $table = (tableSelection && tableSelection.tableId) ? $('#' + tableSelection.tableId) : getActiveTable();
        }
        return $table.find('tbody tr').eq(row).find('td').eq(col + 1);
    }

    function getCellInput(row, col, $table) {
        const $td = getCellTd(row, col, $table);
        if (!$td.length) return $();
        if ($td.find('.organo-cell-container').length) {
            const $customBox = $td.find('.organo-custom-input-box');
            if ($customBox.is(':visible')) {
                return $td.find('.input-organo-custom-text');
            }
            return $td.find('.select-organo-dropdown');
        }
        return $td.find('input:not([type=hidden]), select').first();
    }

    function focusCell(row, col, doSelectText = true, $table) {
        if (!$table || !$table.length) {
            $table = getActiveTable();
        }
        const fields = getTableFields($table);
        if (!fields.length) return;
        if (row < 0) row = 0;
        if (row >= currentJumlah) row = currentJumlah - 1;
        if (col < 0) col = 0;
        if (col >= fields.length) col = fields.length - 1;

        if (fields[col].type === 'readonly') {
            selectionAnchor = { type: 'cell', row, col, tableId: $table.attr('id') };
            setSelection(row, col, row, col, $table);
            return;
        }

        const $input = getCellInput(row, col, $table);
        if ($input.length) {
            $input.focus();
            if (doSelectText && $input.is('input:not([type=checkbox]):not([type=radio])')) {
                try { $input[0].select(); } catch(e) {}
            }
            selectionAnchor = { type: 'cell', row, col, tableId: $table.attr('id') };
            setSelection(row, col, row, col, $table);
        }
    }

    function setSelection(startRow, startCol, endRow, endCol, $table) {
        if (!$table || !$table.length) {
            $table = getActiveTable();
        }
        tableSelection = {
            tableId: $table.attr('id') || 'tableAnalisaShortTerm',
            startRow,
            startCol,
            endRow,
            endCol,
            minRow: Math.min(startRow, endRow),
            maxRow: Math.max(startRow, endRow),
            minCol: Math.min(startCol, endCol),
            maxCol: Math.max(startCol, endCol)
        };
        if (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol) {
            lastMultiSelection = { ...tableSelection };
        }
        renderSelection();
    }

    function clearSelection() {
        tableSelection = null;
        lastMultiSelection = null;
        $('#analisaAccordion table td').removeClass('cell-selected cell-selected-top cell-selected-bottom cell-selected-left cell-selected-right');
    }

    function renderSelection() {
        $('#analisaAccordion table td').removeClass('cell-selected cell-selected-top cell-selected-bottom cell-selected-left cell-selected-right');
        if (!tableSelection) return;

        const { minRow, maxRow, minCol, maxCol, tableId } = tableSelection;
        const isMulti = (minRow !== maxRow || minCol !== maxCol);

        if (!isMulti) return; // single cell selection uses standard focus outline

        const $table = tableId ? $('#' + tableId) : getActiveTable();
        for (let r = minRow; r <= maxRow; r++) {
            for (let c = minCol; c <= maxCol; c++) {
                const $td = getCellTd(r, c, $table);
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
        const { minRow, maxRow, minCol, maxCol, tableId } = tableSelection;
        const $table = tableId ? $('#' + tableId) : getActiveTable();
        const fields = getTableFields($table);

        for (let r = minRow; r <= maxRow; r++) {
            const $row = $table.find('tbody tr').eq(r);
            for (let c = minCol; c <= maxCol; c++) {
                const field = fields[c];
                if (!field || field.type === 'readonly') continue;
                const $td = getCellTd(r, c, $table);
                if (field.type === 'organo-select') {
                    const $container = $td.find('.organo-cell-container');
                    applyOrganoValueToCell($container, '');
                } else {
                    const $el = $td.find(`[name="${field.key}"]`);
                    if ($el.length) {
                        $el.val('').trigger('input').trigger('change');
                    }
                }
                if (field.key === 'timbang_a[]' || field.key === 'timbang_b[]') {
                    calculateRowKotoran(r);
                } else if (field.key === 'timbang_aa[]') {
                    calculateRowKa(r);
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
            let $targetInput = $targetTd.find('.organo-cell-container select, .organo-cell-container input:visible, select, input:not([type=hidden])').filter(':visible').first();
            if (!$targetInput.length) {
                $targetInput = $targetTd.find('input:not([type=hidden]), select').first();
            }

            if ($targetInput.length) {
                $targetInput.focus();
                if ($targetInput.is('input:not([type=checkbox]):not([type=radio])')) {
                    try { $targetInput[0].select(); } catch(err) {}
                }
                const dataColIdx = targetCol - 1;
                const $tbl = $tbody.closest('table.analisa-table');
                setSelection(targetRow, dataColIdx, targetRow, dataColIdx, $tbl);
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

        const $table = getActiveTable(e.target);
        const fields = getTableFields($table);
        if (!fields.length) return;

        const coords = getCellCoords(e.target) || (tableSelection ? { row: tableSelection.startRow, col: tableSelection.startCol, $table: $table, tableId: $table.attr('id') } : { row: 0, col: 0, $table: $table, tableId: $table.attr('id') });
        const row = coords ? coords.row : 0;
        const col = coords ? coords.col : 0;

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
            if (tableSelection && tableSelection.tableId === $table.attr('id')) {
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

            const $rows = $table.find('tbody tr');

            if (minR === maxR) {
                // If only 1 row is selected and row > 0, copy from row above (minR - 1)
                if (minR > 0) {
                    for (let c = minC; c <= maxC; c++) {
                        const field = fields[c];
                        if (!field || field.type === 'readonly') continue;
                        const $sourceRow = $rows.eq(minR - 1);
                        let sourceVal = '';
                        if (field.type === 'organo-select') {
                            sourceVal = $sourceRow.find('.input-organo-final').val() || '';
                        } else {
                            sourceVal = $sourceRow.find(`[name="${field.key}"]`).val() || '';
                        }
                        const $targetRow = $rows.eq(minR);
                        applyFieldValueToCell($targetRow, field, sourceVal, $table);
                    }
                }
            } else {
                // Multi-row selection: copy top row (minR) to all rows below it (minR + 1 to maxR)
                for (let c = minC; c <= maxC; c++) {
                    const field = fields[c];
                    if (!field || field.type === 'readonly') continue;
                    const $sourceRow = $rows.eq(minR);
                    let sourceVal = '';
                    if (field.type === 'organo-select') {
                        sourceVal = $sourceRow.find('.input-organo-final').val() || '';
                    } else {
                        sourceVal = $sourceRow.find(`[name="${field.key}"]`).val() || '';
                    }

                    for (let r = minR + 1; r <= maxR; r++) {
                        const $targetRow = $rows.eq(r);
                        applyFieldValueToCell($targetRow, field, sourceVal, $table);
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
                focusCell(row + 1, col, true, $table);
            }
            return;
        }
        if (e.key === 'ArrowUp' && !e.shiftKey && !e.altKey && !e.ctrlKey) {
            if ($(e.target).is('select')) {
                return; // Let select change option natively
            }
            if (row > 0) {
                e.preventDefault();
                focusCell(row - 1, col, true, $table);
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
                    focusCell(row, col + 1, true, $table);
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
                    focusCell(row, col - 1, true, $table);
                }
            }
            return;
        }

        // 6. SHIFT + ARROW KEYS: Expand / Contract Block Selection
        if (e.shiftKey && ['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
            if (!selectionAnchor || selectionAnchor.row === undefined || selectionAnchor.tableId !== $table.attr('id')) {
                selectionAnchor = { type: 'cell', row, col, tableId: $table.attr('id') };
            }
            let endRow = tableSelection ? tableSelection.endRow : row;
            let endCol = tableSelection ? tableSelection.endCol : col;

            if (e.key === 'ArrowDown') endRow = Math.min(currentJumlah - 1, endRow + 1);
            if (e.key === 'ArrowUp') endRow = Math.max(0, endRow - 1);
            if (e.key === 'ArrowRight') endCol = Math.min(fields.length - 1, endCol + 1);
            if (e.key === 'ArrowLeft') endCol = Math.max(0, endCol - 1);

            setSelection(selectionAnchor.row, selectionAnchor.col, endRow, endCol, $table);
            return;
        }

        // 7. CTRL + A: Select All Data Cells in Table
        if ((e.ctrlKey || e.metaKey) && (e.key === 'a' || e.key === 'A') && isInsideTable) {
            e.preventDefault();
            selectionAnchor = { type: 'cell', row: 0, col: 0, tableId: $table.attr('id') };
            setSelection(0, 0, currentJumlah - 1, fields.length - 1, $table);
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

        // Ignore header click if clicking the fill-down button itself
        if ($(e.target).closest('.btn-fill-col-down').length) {
            return;
        }

        const $table = $target.closest('table.analisa-table');
        if (!$table.length) return;

        // Header click (select entire column or columns)
        if ($target.is('th') || $target.hasClass('th-data-col') || $target.closest('th').length) {
            const $th = $target.is('th') ? $target : $target.closest('th');
            const colIdx = $th.index() - 1;
            if (colIdx >= 0) {
                selectionAnchor = { type: 'col', col: colIdx, tableId: $table.attr('id') };
                isMouseDragging = true;
                setSelection(0, colIdx, currentJumlah - 1, colIdx, $table);
            }
            return;
        }

        // Row header click (sample number: select entire row)
        if ($target.hasClass('td-sampel') || $target.closest('.td-sampel').length) {
            const $td = $target.hasClass('td-sampel') ? $target : $target.closest('.td-sampel');
            const rowIdx = $td.closest('tr').index();
            if (rowIdx >= 0) {
                const fields = getTableFields($table);
                selectionAnchor = { type: 'row', row: rowIdx, tableId: $table.attr('id') };
                isMouseDragging = true;
                setSelection(rowIdx, 0, rowIdx, fields.length - 1, $table);
            }
            return;
        }

        // Data cell click
        const coords = getCellCoords(this);
        if (!coords) return;

        // If user is clicking a <select> or child inside an active multi-cell selection, DO NOT clear the multi-cell selection on mousedown
        const isClickingSelect = $(e.target).is('select, option') || $(e.target).closest('.organo-cell-container, select').length > 0;
        const isInsideActiveSelection = tableSelection && tableSelection.tableId === $table.attr('id') && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol) &&
            coords.row >= tableSelection.minRow && coords.row <= tableSelection.maxRow &&
            coords.col >= tableSelection.minCol && coords.col <= tableSelection.maxCol;

        if (isClickingSelect && isInsideActiveSelection) {
            return; // Keep existing multi-selection intact so change event can broadcast
        }

        if (e.shiftKey && selectionAnchor && selectionAnchor.row !== undefined && selectionAnchor.tableId === $table.attr('id')) {
            e.preventDefault();
            setSelection(selectionAnchor.row, selectionAnchor.col, coords.row, coords.col, $table);
        } else {
            selectionAnchor = { type: 'cell', ...coords, tableId: $table.attr('id') };
            isMouseDragging = true;
            setSelection(coords.row, coords.col, coords.row, coords.col, $table);
        }
    }

    function handleTableMouseEnter(e) {
        if (!isMouseDragging || !selectionAnchor) return;
        const $target = $(this);
        const $table = $target.closest('table.analisa-table');
        if (!$table.length || (selectionAnchor.tableId && selectionAnchor.tableId !== $table.attr('id'))) return;

        if (selectionAnchor.type === 'col') {
            const $th = $target.is('th') ? $target : $target.closest('th');
            if ($th.length) {
                const colIdx = $th.index() - 1;
                if (colIdx >= 0) {
                    setSelection(0, selectionAnchor.col, currentJumlah - 1, colIdx, $table);
                }
            }
            return;
        }

        if (selectionAnchor.type === 'row') {
            const $td = $target.is('td') ? $target : $target.closest('td');
            if ($td.length) {
                const rowIdx = $td.closest('tr').index();
                if (rowIdx >= 0) {
                    const fields = getTableFields($table);
                    setSelection(selectionAnchor.row, 0, rowIdx, fields.length - 1, $table);
                }
            }
            return;
        }

        const coords = getCellCoords(this);
        if (!coords) return;
        setSelection(selectionAnchor.row, selectionAnchor.col, coords.row, coords.col, $table);
    }

    function handleTableMouseUp() {
        isMouseDragging = false;
    }

    function handleDocMouseDown(e) {
        if (!$(e.target).closest('#analisaAccordion table, .analisa-table').length) {
            clearSelection();
        }
    }

    // ─────────────────────────────────────────────
    // COPY HANDLER (Ctrl+C on selected range / focused cell)
    // ─────────────────────────────────────────────
    function handleTableCopy(e) {
        let $table = getActiveTable(document.activeElement);
        if (tableSelection && tableSelection.tableId) {
            $table = $('#' + tableSelection.tableId);
        }
        const fields = getTableFields($table);
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
                    const $td = getCellTd(r, c, $table);
                    if (field.type === 'organo-select') {
                        val = $td.find('.input-organo-final').val() || '';
                    } else if (field.type === 'readonly') {
                        val = $td.find('.badge').text().replace(/[^0-9.-]/g, '') || $td.text().trim();
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
                    const $td = getCellTd(r, c, $table);
                    highlightCell($td.find('input, select, .badge'));
                }
            }
        }
    }

    function getPasteTargetCoords(e) {
        let $target = $(e.target).length ? $(e.target) : $(document.activeElement);
        let $table = $target.closest('table.analisa-table');
        if (!$table.length && tableSelection && tableSelection.tableId) {
            $table = $('#' + tableSelection.tableId);
        }
        if (!$table.length) {
            $table = getActiveTable();
        }

        // 1. If custom tableSelection exists:
        if (tableSelection && (!tableSelection.tableId || tableSelection.tableId === $table.attr('id'))) {
            return { row: tableSelection.minRow, col: tableSelection.minCol, $table: $table, tableId: $table.attr('id') };
        }

        // 2. Check window.getSelection() anchorNode (for native browser text drag / selection across table / th headers)
        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            const anchorNode = sel.anchorNode;
            if (anchorNode) {
                const $node = $(anchorNode.nodeType === 3 ? anchorNode.parentElement : anchorNode);
                const $th = $node.closest('th');
                if ($th.length) {
                    const nodeTable = $th.closest('table.analisa-table');
                    const colIdx = Math.max(0, $th.index() - 1);
                    return { row: 0, col: colIdx, $table: nodeTable.length ? nodeTable : $table, tableId: (nodeTable.length ? nodeTable : $table).attr('id') };
                }
                const $td = $node.closest('td');
                if ($td.length && $td.closest('tbody').length) {
                    const nodeTable = $td.closest('table.analisa-table');
                    const colIdx = Math.max(0, $td.index() - 1);
                    const rowIdx = $td.closest('tr').index();
                    return { row: Math.max(0, rowIdx), col: Math.max(0, colIdx), $table: nodeTable.length ? nodeTable : $table, tableId: (nodeTable.length ? nodeTable : $table).attr('id') };
                }
            }
        }

        // 3. Check event target or activeElement
        if ($target.length) {
            const $th = $target.closest('th');
            if ($th.length) {
                const colIdx = Math.max(0, $th.index() - 1);
                return { row: 0, col: colIdx, $table: $table, tableId: $table.attr('id') };
            }
            const $td = $target.closest('td');
            if ($td.length && $td.closest('tbody').length) {
                const colIdx = Math.max(0, $td.index() - 1);
                const rowIdx = $td.closest('tr').index();
                return { row: Math.max(0, rowIdx), col: Math.max(0, colIdx), $table: $table, tableId: $table.attr('id') };
            }
            const colName = $target.attr('name');
            if (colName) {
                const fields = getTableFields($table);
                const colIdx = fields.findIndex(f => f.key === colName);
                if (colIdx !== -1) {
                    const rowIdx = $target.closest('tr').index();
                    return { row: Math.max(0, rowIdx), col: colIdx, $table: $table, tableId: $table.attr('id') };
                }
            }
        }

        // 4. Fallback: row 0, col 0 (first data cell)
        return { row: 0, col: 0, $table: $table, tableId: $table.attr('id') };
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
        let $table = coords.$table || getActiveTable(e.target);

        const fields = getTableFields($table);
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

        const $rows = $table.find('tbody tr');

        // CASE 1: USER BLOCKED MULTIPLE CELLS (e.g. 4 columns x 3 rows)
        // In Excel, when pasting into a blocked range, data is tiled/replicated across all blocked rows & cols
        const hasMultiCellSelection = tableSelection && tableSelection.tableId === $table.attr('id') && (tableSelection.minRow !== tableSelection.maxRow || tableSelection.minCol !== tableSelection.maxCol);

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

                    applyFieldValueToCell($row, field, cellVal, $table);
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

                applyFieldValueToCell($row, field, cellVal, $table);
            });
        });

        if (appliedRowsCount > 0) {
            setSelection(startRowIdx, startColIdx, Math.min(currentJumlah - 1, startRowIdx + appliedRowsCount - 1), Math.min(fields.length - 1, startColIdx + maxColsCount - 1), $table);
        }

        calculateStatistics();
        saveDraft();
    }

    function applyFieldValueToCell($row, field, cellVal, $table) {
        if (!$row || !$row.length || !field) return;
        if (field.type === 'readonly') return; // Do not overwrite calculated readonly cells
        let cleanVal = (cellVal || '').toString().trim();
        const rowIdx = $row.index();

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
                    setSelectDropdownVal($targetEl, cleanVal, field.masterKey, true);
                } else if (field.type === 'text') {
                    cleanVal = cleanVal.toUpperCase();
                    $targetEl.val(cleanVal).trigger('input').trigger('change');
                } else {
                    cleanVal = cleanNumericValue(cleanVal);
                    $targetEl.val(cleanVal).trigger('input').trigger('change');
                }
                highlightCell($targetEl);
            }
        }

        // Trigger calculations and synchronization
        if (field.key === 'timbang_a[]' || field.key === 'timbang_b[]' || field.key === 'no_beaker[]') {
            calculateRowKotoran(rowIdx);
        } else if (field.key === 'timbang_aa[]' || field.key === 'no_cawan[]') {
            calculateRowKa(rowIdx);
        } else if (field.key === 'rasa[]') {
            const $ringkasanContainer = $('#tableAnalisaShortTerm tbody tr').eq(rowIdx).find('.organo-cell-container');
            if ($ringkasanContainer.length && cleanVal) {
                applyOrganoValueToCell($ringkasanContainer, cleanVal);
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
            const $tr = $container.closest('tr');
            if ($tr.length && $tr.parent().is('tbody')) {
                const rowIdx = $tr.index();
                const $tab2Select = $('#tableLembarKotoran tbody tr').eq(rowIdx).find('.select-rasa-tab2');
                if ($tab2Select.length) $tab2Select.val('');
            }
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

        // Sync corresponding row in Tab 2 Lembar % Kotoran if exists
        const $tr = $container.closest('tr');
        if ($tr.length && $tr.parent().is('tbody')) {
            const rowIdx = $tr.index();
            const $tab2Select = $('#tableLembarKotoran tbody tr').eq(rowIdx).find('.select-rasa-tab2');
            if ($tab2Select.length) {
                $tab2Select.val(matched);
            }
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
            const cleanName = typeof photoPath === 'string' ? photoPath.split('/').pop().split('\\').pop() : photoPath;
            const url = (typeof photoPath === 'string' && photoPath.startsWith('http'))
                ? photoPath
                : `{{ asset('storage/uploads/attachment_analisa') }}/${cleanName}`;
            const html = `
                <div class="photo-preview-item" title="${cleanName}">
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
                    $('#tableAnalisaShortTerm tbody tr').each(function(rowIdx) {
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

            const cleanExisting = existingPhotos.map(p => {
                if (typeof p === 'string') {
                    return p.split('/').pop().split('\\').pop();
                }
                return p;
            }).filter(Boolean);

            formData.set('existing_attachments', JSON.stringify(cleanExisting));

            selectedFiles.forEach(file => {
                formData.append('attachments[]', file);
            });
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

        function executeAjaxSubmit(isRetry) {
            const currentToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
            formData.set('_token', currentToken);

            $.ajax({
                url: urlMap[currentType],
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': currentToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(resp) {
                    clearDraft();
                    if (isDraft) {
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
                    if (xhr.status === 419 && !isRetry) {
                        // Attempt automatic token refresh and retry once
                        $.getJSON("{{ route('csrf.token') }}")
                            .done(function(data) {
                                if (data && data.token) {
                                    $('meta[name="csrf-token"]').attr('content', data.token);
                                    $('input[name="_token"]').val(data.token);
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': data.token
                                        }
                                    });
                                    executeAjaxSubmit(true);
                                    return;
                                }
                                handleAjaxError(xhr);
                            })
                            .fail(function() {
                                handleAjaxError(xhr);
                            });
                        return;
                    }
                    handleAjaxError(xhr);
                },
            });
        }

        function handleAjaxError(xhr) {
            let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.';
            if (xhr.status === 419) {
                errorMsg = 'Sesi telah kedaluwarsa (CSRF token mismatch). Data Anda tetap tersimpan di draft lokal. Silakan muat ulang halaman ini untuk memperbarui sesi, lalu simpan kembali.';
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: errorMsg
            });
        }

        executeAjaxSubmit(false);
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

        const draftData = {
            setup: {
                type: currentType,
                kategori: currentKategori,
                jumlah: currentJumlah
            },
            fields,
            existingPhotos: existingPhotos,
        };

        // Save per category and to legacy key
        localStorage.setItem(getDraftKey(currentKategori), JSON.stringify(draftData));
        localStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
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
                } else if (name === 'no_beaker[]' && Array.isArray(value)) {
                    $('select[name="no_beaker[]"]').each(function(i) {
                        if (value[i]) {
                            $(this).html(getBeakerOptions(value[i])).val(value[i]);
                        }
                    });
                } else if (name === 'no_cawan[]' && Array.isArray(value)) {
                    $('select[name="no_cawan[]"]').each(function(i) {
                        if (value[i]) {
                            $(this).html(getCawanOptions(value[i])).val(value[i]);
                        }
                    });
                } else {
                    const $els = $(`[name="${name}"]`);
                    if (!$els.length) continue;
                    if (Array.isArray(value)) {
                        $els.each(function(i) {
                            if (value[i] !== undefined && value[i] !== null) {
                                if ($(this).is('select')) {
                                    setSelectDropdownVal($(this), value[i]);
                                } else {
                                    $(this).val(value[i]);
                                }
                            }
                        });
                    } else {
                        if ($els.is('select')) {
                            setSelectDropdownVal($els, value);
                        } else {
                            $els.val(value);
                        }
                    }
                }
            }
            if ($('#selectUjiKristal').length && $('#selectUjiKristal').val()) {
                $('#selectUjiKristal').trigger('change');
            }
            if ($('#selectDisposisiLong').length && $('#selectDisposisiLong').val()) {
                $('#selectDisposisiLong').trigger('change');
            }

            for (let i = 0; i < currentJumlah; i++) {
                calculateRowKotoran(i);
                calculateRowKa(i);
            }
            calculateStatistics();
        }, 150);
    }

    function restoreFromDraft(targetCat = null) {
        const cat = targetCat || currentKategori || 'incoming';
        let draft;
        try {
            draft = JSON.parse(localStorage.getItem(getDraftKey(cat)));
            if (!draft) {
                const legacy = JSON.parse(localStorage.getItem(DRAFT_KEY));
                if (legacy && legacy.setup && legacy.setup.kategori === cat) draft = legacy;
            }
        } catch (e) {
            return;
        }
        if (!draft?.setup?.type) return;

        currentType = draft.setup.type;
        currentKategori = draft.setup.kategori || cat;
        currentJumlah = draft.setup.jumlah || categorySampleCounts[cat] || parseInt($('#jumlahData').val()) || 1;
        categorySampleCounts[currentKategori] = currentJumlah;

        if (draft.existingPhotos && Array.isArray(draft.existingPhotos)) {
            existingPhotos = [...draft.existingPhotos];
        }

        if (isGulaKristalMaterial(JENIS)) {
            const radioVal = currentType === 'long-term' ? 'long-term' : currentKategori;
            $(`input[name="analisa_type"][value="${radioVal}"]`).prop('checked', true);
            $('#jumlahWrapper').toggle(radioVal !== 'long-term');
        }
        $('#jumlahData').val(currentJumlah);

        startForm(currentType, currentJumlah, currentKategori);

        if (draft.fields) loadFieldsFromDraft(draft.fields);
        renderPhotoPreviews();
    }

    function clearDraft(targetCat = null) {
        if (targetCat) {
            localStorage.removeItem(getDraftKey(targetCat));
        } else {
            localStorage.removeItem(DRAFT_KEY);
            ['incoming', 'sta', 'monitoring', 'long-term', 'garam-gula'].forEach(cat => {
                localStorage.removeItem(getDraftKey(cat));
            });
        }
    }
</script>
@endsection