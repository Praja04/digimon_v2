@extends('layouts.component.main')

@section('title', 'Laporan Scan QC Kempu')

@section('styles')
    <style>
        .progress-reused {
            height: 6px;
            border-radius: 3px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .nav-custom-pills .nav-link {
            color: #475569;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-custom-pills .nav-link:hover {
            background-color: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .nav-custom-pills .nav-link.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25);
        }

        .nav-custom-pills .nav-link .badge-counter {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 9999px;
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .nav-custom-pills .nav-link.active .badge-counter {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        .filter-container {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .filter-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            display: block;
        }

        .date-chip {
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
            padding: 4px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease-in-out;
            user-select: none;
            background-color: #f8fafc;
        }

        .date-chip:hover {
            border-color: #cbd5e1;
            background-color: #f1f5f9;
        }

        .date-chip.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            font-weight: 600;
        }

        .timeline-container {
            position: relative;
            padding-left: 28px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            top: 5px;
            bottom: 5px;
            left: 10px;
            width: 2px;
            background: #cbd5e1;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #2563eb;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .timeline-dot.success {
            background: #16a34a;
            box-shadow: 0 0 0 2px #bbf7d0;
        }

        .timeline-dot.warning {
            background: #f59e0b;
            box-shadow: 0 0 0 2px #fde68a;
        }

        .timeline-dot.danger {
            background: #ef4444;
            box-shadow: 0 0 0 2px #fca5a5;
        }

        .timeline-dot.dark {
            background: #475569;
            box-shadow: 0 0 0 2px #cbd5e1;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Breadcrumb Header -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 fw-bold">Laporan Scan QC Kempu</h4>
                            <span class="text-muted fs-12">Monitoring & rekap riwayat pemeriksaan Quality Control (QC PM & QC
                                Proses)</span>
                        </div>
                        <div class="page-title-right d-flex align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
                            <a href="{{ route('scan-kempu.pm.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-package-line me-1"></i> Buka QC PM
                            </a>
                            <a href="{{ route('scan-kempu.proses.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="ri-flask-line me-1"></i> Buka QC Proses
                            </a>
                            <button type="button" class="btn btn-success btn-sm" id="btnExportCsv">
                                <i class="ri-file-excel-2-line me-1"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                                <i class="ri-printer-line me-1"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Total Scan QC -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate border-0 shadow-sm h-100"
                        style="border-left: 4px solid #2563eb !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Total Scan QC Hari Ini</p>
                                    <h3 class="fw-bold mb-0 text-primary" id="kpiTotalQcToday">
                                        {{ $statsData['total_qc_today'] ?? 0 }} <span
                                            class="fs-13 fw-normal text-muted">Scan</span></h3>
                                    <span class="text-muted fs-11 mt-1 d-block">Keseluruhan: <strong
                                            id="kpiTotalQcAll">{{ $statsData['total_qc_all'] ?? 0 }}</strong> Scan</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle fs-20"
                                        style="background: #dbeafe; color: #1e40af;">
                                        <i class="ri-qr-scan-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: QC Packaging Material (PM) -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate border-0 shadow-sm h-100"
                        style="border-left: 4px solid #16a34a !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">QC PM (Incoming)</p>
                                    <h3 class="fw-bold mb-0 text-success" id="kpiTotalQcPmToday">
                                        {{ $statsData['total_qc_pm_today'] ?? 0 }} <span
                                            class="fs-13 fw-normal text-muted">Hari Ini</span></h3>
                                    <span class="badge bg-soft-success text-success fs-11 mt-1">Total: <span
                                            id="kpiTotalQcPmAll">{{ $statsData['total_qc_pm_all'] ?? 0 }}</span> Scan</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle fs-20"
                                        style="background: #dcfce7; color: #16a34a;">
                                        <i class="ri-shield-check-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: QC Proses (Pre Cuci & After Filling) -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate border-0 shadow-sm h-100"
                        style="border-left: 4px solid #0d9488 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">QC Proses</p>
                                    <h3 class="fw-bold mb-0" style="color: #0f766e;" id="kpiTotalQcProsesToday">
                                        {{ $statsData['total_qc_proses_today'] ?? 0 }} <span
                                            class="fs-13 fw-normal text-muted">Hari Ini</span></h3>
                                    <span class="badge fs-11 mt-1" style="background: #ccfbf1; color: #0f766e;">Total: <span
                                            id="kpiTotalQcProsesAll">{{ $statsData['total_qc_proses_all'] ?? 0 }}</span>
                                        Scan</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle fs-20"
                                        style="background: #ccfbf1; color: #0f766e;">
                                        <i class="ri-flask-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Masalah / Reject & Hold -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate border-0 shadow-sm h-100"
                        style="border-left: 4px solid #ef4444 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Reject &amp; Hold QC</p>
                                    <h3 class="fw-bold mb-0 text-danger" id="kpiTotalRejectAll">
                                        {{ ($statsData['total_reject_all'] ?? 0) + ($statsData['total_hold_all'] ?? 0) }}
                                        <span class="fs-13 fw-normal text-muted">Kasus</span>
                                    </h3>
                                    <span class="text-muted fs-11 mt-1 d-block">Reject: <strong class="text-danger"
                                            id="kpiDetailReject">{{ $statsData['total_reject_all'] ?? 0 }}</strong> | Hold:
                                        <strong class="text-warning"
                                            id="kpiDetailHold">{{ $statsData['total_hold_all'] ?? 0 }}</strong></span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle fs-20"
                                        style="background: #fee2e2; color: #dc2626;">
                                        <i class="ri-error-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation & Main Content Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 px-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <!-- Navigation Custom Pills -->
                        <ul class="nav nav-custom-pills gap-2" id="reportTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tabHistoryLink" data-bs-toggle="tab" href="#paneHistory"
                                    role="tab">
                                    <i class="ri-history-line"></i>
                                    Log Riwayat Scan QC
                                    <span class="badge-counter" id="badgeCounterHistory">-</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tabCurrentLink" data-bs-toggle="tab" href="#paneCurrent"
                                    role="tab">
                                    <i class="ri-archive-line"></i>
                                    Kempu Aktif di Area QC
                                    <span class="badge-counter" id="badgeCounterCurrent">-</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Right Stats Pill Info -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-soft-primary text-primary px-3 py-2 fs-12 rounded-pill">
                                <i class="ri-shield-flash-line me-1"></i> Force Scan: <strong
                                    id="kpiForceScanBadge">{{ $statsData['total_force_all'] ?? 0 }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Filter Box Container -->
                    <div class="filter-container mb-4">
                        <div class="row g-3 align-items-end">
                            <!-- Filter 1: Klasifikasi QC (QC PM / QC Proses / Force Scan) -->
                            <div class="col-lg-3 col-md-6">
                                <label class="filter-label">
                                    <i class="ri-filter-3-line text-primary me-1"></i> Klasifikasi QC
                                </label>
                                <select class="form-select form-select-sm fw-semibold" id="filterQcCategory">
                                    <option value="all">Semua Klasifikasi QC</option>
                                    <option value="qc-pm">QC PM (Packaging Material)</option>
                                    <option value="qc-proses">QC Proses (Pre Cuci &amp; Filling)</option>
                                    <option value="qc-force">Force Scan (Manual Override)</option>
                                </select>
                            </div>

                            <!-- Filter 2: Hasil Keputusan -->
                            <div class="col-lg-3 col-md-6">
                                <label class="filter-label">
                                    <i class="ri-checkbox-circle-line text-success me-1"></i> Hasil Keputusan
                                </label>
                                <select class="form-select form-select-sm" id="filterActionResult">
                                    <option value="all">Semua Hasil</option>
                                    <option value="OK">Lolos / Release (OK)</option>
                                    <option value="HOLD">Tahan / Evaluasi (HOLD)</option>
                                    <option value="NOT_OK">Reject / Kirim Workshop</option>
                                    <option value="SCRAPPED">Scrap</option>
                                </select>
                            </div>

                            <!-- Filter 3: Quick Date Chips -->
                            <div class="col-lg-4 col-md-8">
                                <label class="filter-label">
                                    <i class="ri-calendar-line text-info me-1"></i> Periode Waktu
                                </label>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="date-chip active" data-range="today">Hari Ini</span>
                                    <span class="date-chip" data-range="7days">7 Hari</span>
                                    <span class="date-chip" data-range="this_month">Bulan Ini</span>
                                    <span class="date-chip" data-range="all">Semua</span>
                                </div>
                            </div>

                            <!-- Filter 4: Reset Button -->
                            <div class="col-lg-2 col-md-4 text-lg-end">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                    id="btnResetFilter">
                                    <i class="ri-refresh-line me-1"></i> Reset Filter
                                </button>
                            </div>
                        </div>

                        <!-- Secondary Row: Date Pickers & Search -->
                        <div class="row g-3 align-items-end mt-1 pt-3">
                            <div class="col-md-3 col-sm-6">
                                <label class="filter-label">Dari Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filterStartDate"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="filter-label">Sampai Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filterEndDate"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 col-sm-8">
                                <label class="filter-label">Pencarian Cepat</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i
                                            class="ri-search-line text-muted"></i></span>
                                    <input type="text" class="form-control" id="filterSearch"
                                        placeholder="Cari ID Kempu, RFID, No SPB, Catatan, atau Petugas...">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="filter-label">Tampilkan</label>
                                <select class="form-select form-select-sm" id="filterPerPage">
                                    <option value="10">10 Baris</option>
                                    <option value="20" selected>20 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="100">100 Baris</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="reportTabsContent">
                        <!-- PANE 1: Riwayat Log Scan QC -->
                        <div class="tab-pane fade show active" id="paneHistory" role="tabpanel">
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-hover align-middle table-nowrap mb-0" id="tableHistory">
                                    <thead class="table-light">
                                        <tr class="fs-12 text-uppercase text-muted">
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th style="width: 140px;">Waktu Scan</th>
                                            <th>ID Kempu &amp; RFID</th>
                                            <th>Klasifikasi QC</th>
                                            <th>Aksi Pemeriksaan</th>
                                            <th>Perpindahan Lokasi</th>
                                            <th class="text-center">Siklus Reused</th>
                                            <th class="text-center">Hasil</th>
                                            <th>Catatan</th>
                                            <th>Petugas QC</th>
                                            <th class="text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHistory">
                                        <tr>
                                            <td colspan="11" class="text-center py-5 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Memuat data riwayat QC...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Container -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                                <div class="text-muted fs-13" id="paginationInfoHistory">
                                    Menampilkan 0 dari 0 data
                                </div>
                                <ul class="pagination pagination-sm mb-0" id="paginationLinksHistory"></ul>
                            </div>
                        </div>

                        <!-- PANE 2: Kempu Aktif di Lokasi QC -->
                        <div class="tab-pane fade" id="paneCurrent" role="tabpanel">
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-hover align-middle table-nowrap mb-0" id="tableCurrent">
                                    <thead class="table-light">
                                        <tr class="fs-12 text-uppercase text-muted">
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th>ID Kempu</th>
                                            <th>RFID</th>
                                            <th>Lokasi Fisik</th>
                                            <th>Status Saat Ini</th>
                                            <th class="text-center">Siklus Reused</th>
                                            <th>Terakhir Diperiksa</th>
                                            <th>Keterangan / SPB</th>
                                            <th class="text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyCurrent">
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Memuat data kempu aktif...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Container -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                                <div class="text-muted fs-13" id="paginationInfoCurrent">
                                    Menampilkan 0 dari 0 data
                                </div>
                                <ul class="pagination pagination-sm mb-0" id="paginationLinksCurrent"></ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail Timeline History Kempu -->
    <div class="modal fade" id="modalTimelineHistory" tabindex="-1" aria-labelledby="modalTimelineTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalTimelineTitle">
                            <i class="ri-history-line text-primary me-1"></i> Riwayat Traceability Kempu: <span
                                id="modalKempuIdTitle" class="font-monospace text-primary"></span>
                        </h5>
                        <div class="text-muted fs-12 mt-1">
                            RFID: <strong id="modalKempuRfidDetail" class="font-monospace">-</strong> &bull; Lokasi
                            Terkini: <span class="badge bg-primary" id="modalKempuLocDetail">-</span> &bull; Status: <span
                                class="badge bg-secondary" id="modalKempuStatusDetail">-</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="timeline-container" id="timelineContainer">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Memuat riwayat tracking kempu...
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const WAREHOUSE_API = "{{ env('WAREHOUSE_API_URL', 'http://127.0.0.1:8000/api') }}";
        const ROUTE_REPORT_DATA = "{{ route('scan-kempu.report.data') }}";
        const ROUTE_REPORT_EXPORT = "{{ route('scan-kempu.report.export') }}";
        const ROUTE_REPORT_STATS = "{{ route('scan-kempu.report.stats') }}";
        const ROUTE_HISTORY_BASE = "{{ url('/scan-kempu/traceability/history') }}";

        $(document).ready(function() {
            let currentViewMode = 'history'; // 'history' | 'current'
            let currentPageHistory = 1;
            let currentPageCurrent = 1;
            let searchDebounceTimer = null;

            const timelineModal = new bootstrap.Modal(document.getElementById('modalTimelineHistory'));

            // Load KPI stats awal
            loadStats();

            // Load Data awal
            loadReportData();

            // Tab switch event
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                if (target === '#paneHistory') {
                    currentViewMode = 'history';
                    loadReportData();
                } else if (target === '#paneCurrent') {
                    currentViewMode = 'current';
                    loadReportData();
                }
            });

            // Date chip quick filter
            $('.date-chip').on('click', function() {
                $('.date-chip').removeClass('active');
                $(this).addClass('active');

                const range = $(this).data('range');
                const today = new Date();
                const formatDate = (d) => d.toISOString().split('T')[0];

                if (range === 'today') {
                    $('#filterStartDate').val(formatDate(today));
                    $('#filterEndDate').val(formatDate(today));
                } else if (range === '7days') {
                    const past = new Date();
                    past.setDate(today.getDate() - 7);
                    $('#filterStartDate').val(formatDate(past));
                    $('#filterEndDate').val(formatDate(today));
                } else if (range === 'this_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    $('#filterStartDate').val(formatDate(firstDay));
                    $('#filterEndDate').val(formatDate(today));
                } else if (range === 'all') {
                    $('#filterStartDate').val('');
                    $('#filterEndDate').val('');
                }

                resetPage();
                loadReportData();
            });

            // Filter changes
            $('#filterQcCategory, #filterActionResult, #filterPerPage').on('change', function() {
                resetPage();
                loadReportData();
            });

            $('#filterStartDate, #filterEndDate').on('change', function() {
                $('.date-chip').removeClass('active');
                resetPage();
                loadReportData();
            });

            $('#filterSearch').on('keyup', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    resetPage();
                    loadReportData();
                }, 350);
            });

            // Reset Filter
            $('#btnResetFilter').on('click', function() {
                $('#filterQcCategory').val('all');
                $('#filterActionResult').val('all');
                $('#filterSearch').val('');
                $('#filterPerPage').val('20');

                // Set ke today
                $('.date-chip').removeClass('active');
                $('.date-chip[data-range="today"]').addClass('active');
                const todayStr = new Date().toISOString().split('T')[0];
                $('#filterStartDate').val(todayStr);
                $('#filterEndDate').val(todayStr);

                resetPage();
                loadReportData();
                loadStats();
            });

            // Export CSV
            $('#btnExportCsv').on('click', function() {
                const params = buildQueryParams();
                window.location.href = ROUTE_REPORT_EXPORT + '?' + $.param(params);
            });

            function resetPage() {
                currentPageHistory = 1;
                currentPageCurrent = 1;
            }

            function buildQueryParams() {
                return {
                    view_mode: currentViewMode,
                    qc_category: $('#filterQcCategory').val(),
                    action_result: $('#filterActionResult').val(),
                    start_date: $('#filterStartDate').val(),
                    end_date: $('#filterEndDate').val(),
                    search: $('#filterSearch').val().trim(),
                    per_page: $('#filterPerPage').val(),
                    page: (currentViewMode === 'history' ? currentPageHistory : currentPageCurrent)
                };
            }

            // AJAX: Load Stats KPI
            function loadStats() {
                $.ajax({
                    url: ROUTE_REPORT_STATS,
                    method: 'GET',
                    success: function(res) {
                        if (res.status && res.data) {
                            const d = res.data;
                            $('#kpiTotalQcToday').html(
                                `${d.total_qc_today || 0} <span class="fs-13 fw-normal text-muted">Scan</span>`
                            );
                            $('#kpiTotalQcAll').text(d.total_qc_all || 0);
                            $('#kpiTotalQcPmToday').html(
                                `${d.total_qc_pm_today || 0} <span class="fs-13 fw-normal text-muted">Hari Ini</span>`
                            );
                            $('#kpiTotalQcPmAll').text(d.total_qc_pm_all || 0);
                            $('#kpiTotalQcProsesToday').html(
                                `${d.total_qc_proses_today || 0} <span class="fs-13 fw-normal text-muted">Hari Ini</span>`
                            );
                            $('#kpiTotalQcProsesAll').text(d.total_qc_proses_all || 0);
                            $('#kpiTotalRejectAll').html(
                                `${(d.total_reject_all || 0) + (d.total_hold_all || 0)} <span class="fs-13 fw-normal text-muted">Kasus</span>`
                            );
                            $('#kpiDetailReject').text(d.total_reject_all || 0);
                            $('#kpiDetailHold').text(d.total_hold_all || 0);
                            $('#kpiForceScanBadge').text(d.total_force_all || 0);
                        }
                    }
                });
            }

            // AJAX: Load Report Data
            function loadReportData() {
                const params = buildQueryParams();
                const tbody = currentViewMode === 'history' ? $('#tbodyHistory') : $('#tbodyCurrent');
                const colSpan = currentViewMode === 'history' ? 11 : 9;

                tbody.html(`
                    <tr>
                        <td colspan="${colSpan}" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat data laporan...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: ROUTE_REPORT_DATA,
                    method: 'GET',
                    data: params,
                    success: function(res) {
                        if (res.status && res.data) {
                            if (currentViewMode === 'history') {
                                renderHistoryTable(res.data, res.pagination);
                            } else {
                                renderCurrentTable(res.data, res.pagination);
                            }
                        } else {
                            tbody.html(`
                                <tr>
                                    <td colspan="${colSpan}" class="text-center py-5 text-muted">
                                        <i class="ri-inbox-line fs-32 d-block text-muted mb-2"></i>
                                        Tidak ada data yang sesuai filter.
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr) {
                        tbody.html(`
                            <tr>
                                <td colspan="${colSpan}" class="text-center py-4 text-danger">
                                    <i class="ri-error-warning-line fs-24 d-block mb-1"></i>
                                    Gagal memuat data dari server Warehouse.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Render Table: Riwayat Log Scan QC
            function renderHistoryTable(items, pagination) {
                const tbody = $('#tbodyHistory');
                tbody.empty();

                $('#badgeCounterHistory').text(pagination ? pagination.total : items.length);

                if (!items || items.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="ri-inbox-line fs-32 d-block text-muted mb-2"></i>
                                Tidak ada catatan riwayat scan QC ditemukan.
                            </td>
                        </tr>
                    `);
                    $('#paginationInfoHistory').text('Menampilkan 0 dari 0 data');
                    $('#paginationLinksHistory').empty();
                    return;
                }

                const startIndex = ((pagination.current_page - 1) * pagination.per_page);

                items.forEach((item, idx) => {
                    const dt = item.created_at ? new Date(item.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                    // Klasifikasi QC Badge
                    let qcCategoryBadge =
                        '<span class="badge bg-soft-primary text-primary px-2 py-1 fs-11">QC PM</span>';
                    if (item.stage === 'QC_PROSES') {
                        qcCategoryBadge =
                            '<span class="badge bg-soft-success text-success px-2 py-1 fs-11">QC Proses</span>';
                    } else if (item.stage === 'QC_FORCE') {
                        qcCategoryBadge =
                            '<span class="badge bg-soft-danger text-danger px-2 py-1 fs-11"><i class="ri-shield-flash-line me-1"></i>Force Scan</span>';
                    }

                    // Hasil Badge
                    let resultBadge = '<span class="badge bg-success fs-11">OK / Release</span>';
                    if (item.action_result === 'HOLD') {
                        resultBadge = '<span class="badge bg-warning text-dark fs-11">HOLD (Tahan)</span>';
                    } else if (item.action_result === 'NOT_OK') {
                        resultBadge = '<span class="badge bg-danger fs-11">Reject</span>';
                    } else if (item.action_result === 'SCRAPPED') {
                        resultBadge = '<span class="badge bg-dark fs-11">Scrap / Afkir</span>';
                    }

                    // Reused Progress
                    const reused = item.reused_count || 0;
                    const pct = Math.min(100, Math.round((reused / 21) * 100));
                    let reusedColor = 'bg-success';
                    if (reused >= 21) reusedColor = 'bg-danger';
                    else if (reused >= 18) reusedColor = 'bg-warning';

                    // Actor Operator
                    const actor = item.operator_display_name ||
                        (item.created_by && (item.created_by.nama_lengkap || item.created_by.username)) ||
                        (item.metadata && (item.metadata.operator_name || item.metadata.operator_email)) ||
                        'System';

                    const row = `
                        <tr>
                            <td class="text-center text-muted fs-12">${startIndex + idx + 1}</td>
                            <td class="fs-12 text-muted font-monospace">${dt}</td>
                            <td>
                                <strong class="font-monospace text-primary fs-13 d-block">${item.id_kempu}</strong>
                                <span class="fs-11 text-muted font-monospace">${item.master_kempu?.rfid || '-'}</span>
                            </td>
                            <td>${qcCategoryBadge}</td>
                            <td><strong class="text-dark fs-12">${item.action || '-'}</strong></td>
                            <td class="fs-12 text-muted">
                                <span>${item.from_location || '-'}</span> &rarr; <strong class="text-dark">${item.to_location || '-'}</strong>
                            </td>
                            <td class="text-center" style="min-width: 90px;">
                                <span class="fw-bold fs-12">${reused}/21x</span>
                                <div class="progress-reused mt-1">
                                    <div class="progress-bar ${reusedColor}" style="width: ${pct}%"></div>
                                </div>
                            </td>
                            <td class="text-center">${resultBadge}</td>
                            <td class="fs-12 text-muted" style="max-width: 220px; white-space: normal;">
                                ${item.notes ? `<em>"${item.notes}"</em>` : '-'}
                            </td>
                            <td class="fs-12">
                                <span class="fw-semibold text-body"><i class="ri-user-line text-muted me-1"></i>${actor}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-soft-primary btn-sm btn-timeline" data-id="${item.id_kempu}" title="Lihat Riwayat Pelacakan">
                                    <i class="ri-timeline-view"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                renderPagination(pagination, 'History', (p) => {
                    currentPageHistory = p;
                    loadReportData();
                });
            }

            // Render Table: Kempu Aktif di Lokasi QC
            function renderCurrentTable(items, pagination) {
                const tbody = $('#tbodyCurrent');
                tbody.empty();

                $('#badgeCounterCurrent').text(pagination ? pagination.total : items.length);

                if (!items || items.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="ri-inbox-line fs-32 d-block text-muted mb-2"></i>
                                Tidak ada kempu aktif di lokasi QC saat ini.
                            </td>
                        </tr>
                    `);
                    $('#paginationInfoCurrent').text('Menampilkan 0 dari 0 data');
                    $('#paginationLinksCurrent').empty();
                    return;
                }

                const startIndex = ((pagination.current_page - 1) * pagination.per_page);

                items.forEach((item, idx) => {
                    const main = item.main || {};
                    const loc = main.current_location || item.current_location || '-';
                    const status = main.current_status || item.current_status || '-';
                    const reused = main.reused_count !== undefined ? main.reused_count : (item
                        .reused_count || 0);

                    const pct = Math.min(100, Math.round((reused / 21) * 100));
                    let reusedColor = 'bg-success';
                    if (reused >= 21) reusedColor = 'bg-danger';
                    else if (reused >= 18) reusedColor = 'bg-warning';

                    const lastScanned = main.last_scanned_at ? new Date(main.last_scanned_at)
                        .toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';

                    const row = `
                        <tr>
                            <td class="text-center text-muted fs-12">${startIndex + idx + 1}</td>
                            <td><strong class="font-monospace text-primary fs-13">${item.id_kempu}</strong></td>
                            <td class="font-monospace fs-12 text-muted">${item.rfid || '-'}</td>
                            <td><span class="badge bg-light text-dark border px-2 py-1 fs-12">${loc}</span></td>
                            <td><span class="badge bg-soft-primary text-primary px-2 py-1 fs-12">${status}</span></td>
                            <td class="text-center" style="min-width: 90px;">
                                <span class="fw-bold fs-12">${reused}/21x</span>
                                <div class="progress-reused mt-1">
                                    <div class="progress-bar ${reusedColor}" style="width: ${pct}%"></div>
                                </div>
                            </td>
                            <td class="fs-12 text-muted font-monospace">${lastScanned}</td>
                            <td class="fs-12 text-muted">
                                ${item.no_spb ? `<span class="badge bg-light text-muted border me-1">SPB: ${item.no_spb}</span>` : ''}
                                ${item.keterangan || '-'}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-soft-primary btn-sm btn-timeline" data-id="${item.id_kempu}" title="Lihat Riwayat Pelacakan">
                                    <i class="ri-timeline-view"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                renderPagination(pagination, 'Current', (p) => {
                    currentPageCurrent = p;
                    loadReportData();
                });
            }

            // Pagination Component
            function renderPagination(p, mode, onPageClick) {
                const infoEl = $(`#paginationInfo${mode}`);
                const linksEl = $(`#paginationLinks${mode}`);
                linksEl.empty();

                if (!p || p.total === 0) {
                    infoEl.text('Menampilkan 0 dari 0 data');
                    return;
                }

                const from = ((p.current_page - 1) * p.per_page) + 1;
                const to = Math.min(p.total, p.current_page * p.per_page);
                infoEl.text(`Menampilkan ${from} - ${to} dari total ${p.total} data`);

                if (p.last_page <= 1) return;

                // Tombol Prev
                const prevDisabled = p.current_page === 1 ? 'disabled' : '';
                linksEl.append(`
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link" href="#" data-page="${p.current_page - 1}">&laquo;</a>
                    </li>
                `);

                // Nomor Halaman
                let startPage = Math.max(1, p.current_page - 2);
                let endPage = Math.min(p.last_page, p.current_page + 2);

                if (startPage > 1) {
                    linksEl.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
                    if (startPage > 2) linksEl.append(
                        `<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === p.current_page ? 'active' : '';
                    linksEl.append(`
                        <li class="page-item ${activeClass}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (endPage < p.last_page) {
                    if (endPage < p.last_page - 1) linksEl.append(
                        `<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    linksEl.append(
                        `<li class="page-item"><a class="page-link" href="#" data-page="${p.last_page}">${p.last_page}</a></li>`
                    );
                }

                // Tombol Next
                const nextDisabled = p.current_page === p.last_page ? 'disabled' : '';
                linksEl.append(`
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link" href="#" data-page="${p.current_page + 1}">&raquo;</a>
                    </li>
                `);

                linksEl.find('a.page-link').on('click', function(e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'));
                    if (page && page >= 1 && page <= p.last_page) {
                        onPageClick(page);
                    }
                });
            }

            // Click: Buka Modal Timeline History
            $(document).on('click', '.btn-timeline', function() {
                const idKempu = $(this).data('id');
                $('#modalKempuIdTitle').text(idKempu);
                $('#timelineContainer').html(`
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Memuat riwayat tracking kempu ${idKempu}...
                    </div>
                `);

                timelineModal.show();

                $.ajax({
                    url: ROUTE_HISTORY_BASE + '/' + encodeURIComponent(idKempu),
                    method: 'GET',
                    success: function(res) {
                        if (res.status && res.kempu) {
                            const k = res.kempu;
                            const hList = res.histories || [];

                            $('#modalKempuRfidDetail').text(k.rfid || '-');
                            $('#modalKempuLocDetail').text(k.main?.current_location || k
                                .current_location || '-');
                            $('#modalKempuStatusDetail').text(k.main?.current_status || k
                                .current_status || '-');

                            if (hList.length === 0) {
                                $('#timelineContainer').html(
                                    '<div class="text-center py-4 text-muted">Belum ada catatan riwayat tracking untuk kempu ini.</div>'
                                );
                                return;
                            }

                            let html = '';
                            hList.forEach(h => {
                                const dt = h.created_at ? new Date(h.created_at)
                                    .toLocaleString('id-ID', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : '-';

                                let dotClass = '';
                                if (h.action_result === 'OK') dotClass = 'success';
                                else if (h.action_result === 'HOLD') dotClass =
                                    'warning';
                                else if (h.action_result === 'NOT_OK') dotClass =
                                    'danger';
                                else if (h.action_result === 'SCRAPPED') dotClass =
                                    'dark';

                                const actor = h.operator_display_name ||
                                    (h.created_by && (h.created_by.nama_lengkap || h
                                        .created_by.username)) ||
                                    (h.metadata && (h.metadata.operator_name || h
                                        .metadata.operator_email)) ||
                                    'System';

                                html += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"></div>
                                        <div class="card shadow-none border bg-light mb-2">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                                    <div>
                                                        <span class="badge bg-secondary fs-11 me-1">${h.stage || 'STAGE'}</span>
                                                        <strong class="text-dark fs-13">${h.action || 'Aksi'}</strong>
                                                    </div>
                                                    <span class="text-muted fs-11 font-monospace"><i class="ri-time-line me-1"></i>${dt}</span>
                                                </div>
                                                <div class="fs-12 text-muted mb-2">
                                                    <span>Lokasi: <strong>${h.from_location || '-'}</strong> &rarr; <strong>${h.to_location || '-'}</strong></span>
                                                    &bull; <span class="badge bg-light text-dark border">Reused: ${h.reused_count || 0}/21x</span>
                                                    &bull; <span class="badge bg-light text-dark border">Kondisi: ${h.condition || 'OK'}</span>
                                                </div>
                                                ${h.notes ? `<div class="fs-12 text-muted mb-2 fst-italic">"${h.notes}"</div>` : ''}
                                                <div class="fs-11 text-muted">
                                                    <i class="ri-user-line me-1"></i> Petugas: <strong class="text-dark">${actor}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            $('#timelineContainer').html(html);
                        } else {
                            $('#timelineContainer').html(
                                '<div class="text-center py-4 text-danger">Gagal memuat detail kempu.</div>'
                            );
                        }
                    },
                    error: function() {
                        $('#timelineContainer').html(
                            '<div class="text-center py-4 text-danger">Terjadi kesalahan koneksi ke server.</div>'
                        );
                    }
                });
            });
        });
    </script>
@endsection
