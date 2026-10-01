@extends('layouts.component.main')

@section('title', 'Dashboard Monitoring & Traceability Kempu')

@section('styles')
    <style>
        .location-badge-WPM {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .location-badge-QC_PM {
            background-color: #fef3c7;
            color: #92400e;
        }

        .location-badge-ENGINEERING_WORKSHOP,
        .location-badge-ENG {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .location-badge-PRODUKSI {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .location-badge-QC_PROSES {
            background-color: #fef08a;
            color: #854d0e;
        }

        .location-badge-WFG {
            background-color: #ccfbf1;
            color: #115e59;
        }

        .location-badge-WAREHOUSE_PAS,
        .location-badge-PAS {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .location-badge-SCRAP {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .progress-reused {
            height: 8px;
            border-radius: 4px;
            background-color: #e2e8f0;
            overflow: hidden;
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
            box-shadow: 0 0 0 2px #93c5fd;
        }

        .timeline-dot.scrap {
            background: #ef4444;
            box-shadow: 0 0 0 2px #fca5a5;
        }

        .timeline-dot.warning {
            background: #f59e0b;
            box-shadow: 0 0 0 2px #fde68a;
        }

        .timeline-dot.success {
            background: #10b981;
            box-shadow: 0 0 0 2px #a7f3d0;
        }

        .card-stat-glow {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-stat-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title Header -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-body">Dashboard Monitoring &amp; Traceability Kempu</h4>
                            <p class="text-muted fs-12 mb-0">Ringkasan Status, Lokasi Terkini, Siklus Reused 21x &amp;
                                Riwayat Audit Trail</p>
                        </div>
                        <div class="page-title-right d-flex align-items-center gap-2 mt-2 mt-sm-0">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnRefreshStats">
                                <i class="ri-refresh-line me-1"></i> Refresh
                            </button>
                            <a href="{{ route('scan-kempu.pm.index') }}" class="btn btn-sm btn-soft-primary">
                                <i class="mdi mdi-package-variant me-1"></i> QC PM
                            </a>
                            <a href="{{ route('scan-kempu.proses.index') }}" class="btn btn-sm btn-soft-success">
                                <i class="mdi mdi-archive-check-outline me-1"></i> QC Proses
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top KPI Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-stat-glow border-start border-primary border-3 h-100 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-1 fs-11">Total Kempu
                                        Aktif</p>
                                    <h4 class="fs-22 fw-bold mb-0 text-primary" id="statTotalActive">0</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-20">
                                        <i class="ri-archive-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-stat-glow border-start border-info border-3 h-100 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-1 fs-11">Di Produksi /
                                        Proses</p>
                                    <h4 class="fs-22 fw-bold mb-0 text-info" id="statProduksi">0</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-3 fs-20">
                                        <i class="ri-settings-4-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-stat-glow border-start border-success border-3 h-100 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-1 fs-11">Di Gudang (WPM
                                        &amp; WFG)</p>
                                    <h4 class="fs-22 fw-bold mb-0 text-success" id="statGudang">0</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-3 fs-20">
                                        <i class="ri-building-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-6 col-sm-6">
                    <div class="card card-stat-glow border-start border-warning border-3 h-100 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-1 fs-11">Mendekati Max
                                        (&ge;18x)</p>
                                    <h4 class="fs-22 fw-bold mb-0 text-warning" id="statNearMax">0</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-20">
                                        <i class="ri-alarm-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-6 col-sm-6">
                    <div class="card card-stat-glow border-start border-danger border-3 h-100 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-1 fs-11">Scrap / Afkir
                                    </p>
                                    <h4 class="fs-22 fw-bold mb-0 text-danger" id="statScrap">0</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded-3 fs-20">
                                        <i class="ri-delete-bin-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <!-- Chart 1: Distribusi Lokasi Kempu -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow-sm border-0 h-100 mb-0">
                        <div
                            class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                            <h5 class="card-title mb-0 fs-14 fw-bold">
                                <i class="ri-pie-chart-2-line text-primary me-1"></i> Distribusi Kempu per Lokasi
                            </h5>
                            <span class="badge bg-light text-muted border fs-11">Semua Kempu</span>
                        </div>
                        <div class="card-body">
                            <div id="chartLocationDistribution" style="min-height: 290px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Siklus Reused Breakdown -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow-sm border-0 h-100 mb-0">
                        <div
                            class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                            <h5 class="card-title mb-0 fs-14 fw-bold">
                                <i class="ri-bar-chart-grouped-line text-success me-1"></i> Distribusi Siklus Reused (Maks.
                                21x)
                            </h5>
                            <span class="badge bg-light text-muted border fs-11">Kempu Aktif</span>
                        </div>
                        <div class="card-body">
                            <div id="chartReusedDistribution" style="min-height: 290px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                            <!-- Search -->
                            <div class="position-relative" style="min-width: 220px;">
                                <input type="text" id="searchInput" class="form-control form-control-sm ps-4"
                                    placeholder="Cari ID Barcode / Tipe...">
                                <i
                                    class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-2 text-muted fs-14"></i>
                            </div>

                            <!-- Filter Lokasi -->
                            <select id="filterLocation" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Lokasi</option>
                                @foreach ($locations as $locKey => $locName)
                                    <option value="{{ $locKey }}">{{ $locName }}</option>
                                @endforeach
                            </select>

                            <!-- Filter Reused -->
                            <select id="filterReused" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Siklus Reused</option>
                                <option value="normal">Normal (&lt; 18x)</option>
                                <option value="warning">Mendekati Batas (18 - 20x)</option>
                                <option value="max">Maksimal (&ge; 21x)</option>
                            </select>

                            <!-- Filter Status -->
                            <select id="filterStatus" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Status</option>
                                <option value="active">Kempu Aktif (Beroperasi)</option>
                                <option value="scrap">Scrap / Afkir</option>
                            </select>

                            <button id="btnResetFilter" class="btn btn-sm btn-light border text-muted">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                        </div>

                        <div>
                            <span class="badge bg-soft-info text-info border border-info-subtle px-3 py-2 fs-12"
                                id="kempuTotalBadge">
                                Memuat data...
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Data Kempu & Traceability -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fs-14 fw-bold d-flex align-items-center gap-2">
                        <i class="ri-file-list-3-line text-primary"></i> Data Master &amp; Riwayat Alur Kempu
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0" id="tableTraceability">
                            <thead class="table-light text-muted fs-12">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>ID / Barcode Kempu</th>
                                    <th>Lokasi Sekarang</th>
                                    <th>Status Saat Ini</th>
                                    <th style="min-width: 160px;">Siklus Reused (Max 21x)</th>
                                    <th>Kondisi</th>
                                    <th>Terakhir Diproses</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="traceabilityTbody" class="fs-13">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        </div>
                                        Memuat data traceability kempu...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="text-muted fs-13" id="tableInfo">Menampilkan data...</div>
                        <div id="tablePagination"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail & History Timeline (Audit Trail) -->
    <div class="modal fade" id="modalTimeline" tabindex="-1" aria-labelledby="modalTimelineLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-route-line fs-20"></i>
                        <div>
                            <h5 class="modal-title text-white mb-0" id="modalTimelineLabel">Traceability &amp; Riwayat
                                Kempu</h5>
                            <span class="fs-12 text-white-50" id="modalSubtitle">ID Kempu: -</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Info Kempu Card -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-sm-3 col-6">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold">ID Barcode</div>
                                    <div class="fw-bold fs-15 text-primary font-monospace" id="modalInfoId">-</div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold">Lokasi Terkini</div>
                                    <div id="modalInfoLocation" class="mt-1">-</div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold">Status Siklus</div>
                                    <div id="modalInfoStatus" class="mt-1">-</div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold">Siklus Reused</div>
                                    <div id="modalInfoReused" class="mt-1">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                        <i class="ri-history-line text-primary me-2"></i> Perjalanan &amp; Log Riwayat (Audit Trail)
                    </h6>

                    <div class="timeline-container" id="timelineList">
                        <div class="text-center py-4 text-muted">Memuat riwayat...</div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function() {
            let allData = [];
            let filteredData = [];
            let chartLocation = null;
            let chartReused = null;

            const timelineModal = new bootstrap.Modal(document.getElementById('modalTimeline'), {
                backdrop: 'static'
            });

            // Initial Stats Data from Blade Controller
            const initialStats = @json($statsData);

            // Inisialisasi Charts
            function initCharts(stats) {
                if (!stats || !stats.charts) return;

                // 1. Chart Lokasi (Donut)
                const locData = stats.charts.locations || {
                    labels: [],
                    series: []
                };
                const locOptions = {
                    series: locData.series || [],
                    labels: locData.labels || [],
                    chart: {
                        type: 'donut',
                        height: 290,
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#4f46e5', '#f59e0b', '#2563eb', '#84cc16', '#0d9488', '#9333ea', '#ea580c',
                        '#dc2626'
                    ],
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        markers: {
                            radius: 12
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Kempu',
                                        fontSize: '13px',
                                        fontWeight: 600,
                                        color: '#64748b',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        colors: ['#ffffff']
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' kempu';
                            }
                        }
                    }
                };

                if (chartLocation) {
                    chartLocation.destroy();
                }
                const locEl = document.querySelector("#chartLocationDistribution");
                if (locEl) {
                    chartLocation = new ApexCharts(locEl, locOptions);
                    chartLocation.render();
                }

                // 2. Chart Siklus Reused (Bar)
                const reusedData = stats.charts.reused || {
                    labels: [],
                    series: []
                };
                const reusedOptions = {
                    series: [{
                        name: 'Jumlah Kempu',
                        data: reusedData.series || []
                    }],
                    chart: {
                        type: 'bar',
                        height: 290,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            distributed: true,
                            borderRadius: 6,
                            columnWidth: '50%',
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    colors: ['#10b981', '#06b6d4', '#3b82f6', '#f59e0b', '#ef4444'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    legend: {
                        show: false
                    },
                    xaxis: {
                        categories: reusedData.labels || [],
                        labels: {
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Kempu'
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.round(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' kempu';
                            }
                        }
                    }
                };

                if (chartReused) {
                    chartReused.destroy();
                }
                const reusedEl = document.querySelector("#chartReusedDistribution");
                if (reusedEl) {
                    chartReused = new ApexCharts(reusedEl, reusedOptions);
                    chartReused.render();
                }
            }

            // Update Top Cards
            function updateKpiCards(kpi) {
                if (!kpi) return;
                $('#statTotalActive').text(kpi.total_active || 0);
                $('#statProduksi').text(kpi.total_produksi || 0);
                $('#statGudang').text((kpi.total_wpm || 0) + (kpi.total_wfg || 0));
                $('#statNearMax').text(kpi.total_near_max || 0);
                $('#statScrap').text(kpi.total_scrap || 0);
            }

            // Fetch Stats API
            function fetchStats() {
                $.ajax({
                    url: "{{ route('scan-kempu.traceability.stats') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.status && res.data) {
                            updateKpiCards(res.data.kpi);
                            initCharts(res.data);
                        }
                    }
                });
            }

            // Inisialisasi awal dengan data stats controller
            if (initialStats && initialStats.kpi) {
                updateKpiCards(initialStats.kpi);
                initCharts(initialStats);
            } else {
                fetchStats();
            }

            $('#btnRefreshStats').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Memuat...');
                fetchStats();
                loadTableData();
                setTimeout(() => {
                    btn.prop('disabled', false).html(
                        '<i class="ri-refresh-line me-1"></i> Refresh');
                }, 800);
            });

            // Load Data Table
            function loadTableData() {
                $('#traceabilityTbody').html(`
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat data traceability kempu...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: "{{ route('scan-kempu.traceability.data') }}",
                    type: "GET",
                    data: {
                        search: $('#searchInput').val().trim(),
                        location: $('#filterLocation').val(),
                        status_siklus: $('#filterStatus').val(),
                        reused_status: $('#filterReused').val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status && res.data) {
                            allData = res.data;
                            filteredData = allData;
                            renderTable();
                        } else {
                            $('#traceabilityTbody').html(`
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-danger">
                                        Gagal memuat data kempu.
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        $('#traceabilityTbody').html(`
                            <tr>
                                <td colspan="9" class="text-center py-4 text-danger">
                                    Terjadi kesalahan server saat mengambil data.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            function renderTable() {
                if (filteredData.length === 0) {
                    $('#traceabilityTbody').html(`
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Tidak ada data kempu yang sesuai dengan filter.
                            </td>
                        </tr>
                    `);
                    $('#tableInfo').text('Menampilkan 0 data');
                    $('#kempuTotalBadge').text('0 Kempu Ditemukan');
                    return;
                }

                $('#kempuTotalBadge').text(filteredData.length + ' Kempu Ditemukan');
                $('#tableInfo').text(`Menampilkan ${filteredData.length} kempu`);

                let html = '';
                filteredData.forEach((item, index) => {
                    const main = item.main || {};
                    const loc = main.current_location || item.current_location || '-';
                    const status = main.current_status || item.current_status || 'REGISTERED';
                    const reused = parseInt(main.reused_count !== undefined ? main.reused_count : (item
                        .reused_count || 0));
                    const cond = main.condition || item.condition || 'OK';
                    const lastAction = main.last_action || '-';
                    const lastScanned = main.last_scanned_at ? formatDateTime(main.last_scanned_at) : (item
                        .updated_at ? formatDateTime(item.updated_at) : '-');

                    // Progress bar color
                    let progressColor = 'bg-success';
                    if (reused >= 21) {
                        progressColor = 'bg-danger';
                    } else if (reused >= 18) {
                        progressColor = 'bg-warning';
                    } else if (reused >= 12) {
                        progressColor = 'bg-info';
                    }
                    const progressPercent = Math.min(100, Math.round((reused / 21) * 100));

                    // Location badge class
                    const locClass = 'location-badge-' + (loc.replace(/\s+/g, '_'));

                    // Condition badge
                    const condBadge = cond === 'OK' ?
                        `<span class="badge bg-soft-success text-success"><i class="ri-check-line me-1"></i>OK</span>` :
                        `<span class="badge bg-soft-danger text-danger"><i class="ri-close-line me-1"></i>NOT OK</span>`;

                    html += `
                        <tr>
                            <td class="text-center text-muted">${index + 1}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary fs-14">${item.id_kempu}</span>
                                ${item.rfid ? `<div class="text-muted fs-11 font-monospace">RFID: ${item.rfid}</div>` : ''}
                            </td>
                            <td>
                                <span class="badge ${locClass} px-2 py-1 fs-12 border">
                                    <i class="ri-map-pin-line me-1"></i>${formatLocationName(loc)}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fs-11">${status}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-between mb-1 fs-11">
                                    <span class="fw-bold ${reused >= 21 ? 'text-danger' : (reused >= 18 ? 'text-warning' : 'text-body')}">
                                        ${reused} / 21x
                                    </span>
                                    <span class="text-muted">${progressPercent}%</span>
                                </div>
                                <div class="progress progress-reused">
                                    <div class="progress-bar ${progressColor}" role="progressbar"
                                        style="width: ${progressPercent}%;" aria-valuenow="${reused}" aria-valuemin="0" aria-valuemax="21">
                                    </div>
                                </div>
                            </td>
                            <td>${condBadge}</td>
                            <td>
                                <div class="text-body fw-medium fs-12">${lastAction}</div>
                                <span class="text-muted fs-11">${lastScanned}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-soft-primary waves-effect waves-light btn-view-history"
                                    data-id="${item.id}" data-kempu="${item.id_kempu}">
                                    <i class="ri-route-line me-1"></i> Riwayat
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $('#traceabilityTbody').html(html);
            }

            function formatLocationName(loc) {
                const map = {
                    'WPM': 'WPM',
                    'QC_PM': 'QC PM',
                    'ENGINEERING_WORKSHOP': 'Workshop Eng',
                    'ENG': 'Workshop Eng',
                    'PRODUKSI': 'Produksi',
                    'QC_PROSES': 'QC Proses',
                    'WFG': 'WFG',
                    'WAREHOUSE_PAS': 'PT PAS',
                    'PAS': 'PT PAS',
                    'SCRAP': 'Scrap'
                };
                return map[loc] || loc;
            }

            function formatDateTime(str) {
                if (!str) return '-';
                try {
                    const d = new Date(str);
                    if (isNaN(d.getTime())) return str;
                    return d.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return str;
                }
            }

            // Filter Handlers
            $('#filterLocation, #filterStatus, #filterReused').on('change', function() {
                loadTableData();
            });

            let searchTimeout = null;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadTableData();
                }, 350);
            });

            $('#btnResetFilter').on('click', function() {
                $('#searchInput').val('');
                $('#filterLocation').val('');
                $('#filterStatus').val('');
                $('#filterReused').val('');
                loadTableData();
            });

            // View History Modal
            $(document).on('click', '.btn-view-history', function() {
                const id = $(this).data('id');
                const kempuId = $(this).data('kempu');

                $('#modalInfoId').text(kempuId);
                $('#modalSubtitle').text('ID Kempu: ' + kempuId);
                $('#timelineList').html(`
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Memuat riwayat audit trail kempu...
                    </div>
                `);

                timelineModal.show();

                $.ajax({
                    url: "{{ url('/scan-kempu/traceability/history') }}/" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.status && res.kempu) {
                            const k = res.kempu;
                            const m = k.main || {};

                            $('#modalInfoLocation').html(`
                                <span class="badge location-badge-${(m.current_location || k.current_location || '').replace(/\s+/g, '_')} px-2 py-1 fs-11 border">
                                    ${formatLocationName(m.current_location || k.current_location || '-')}
                                </span>
                            `);
                            $('#modalInfoStatus').html(`
                                <span class="badge bg-light text-dark border px-2 py-1 fs-11">${m.current_status || k.current_status || '-'}</span>
                            `);
                            $('#modalInfoReused').html(`
                                <span class="badge bg-primary px-2 py-1 fs-11">${m.reused_count || k.reused_count || 0} / 21x</span>
                            `);

                            const histories = res.histories || [];
                            if (histories.length === 0) {
                                $('#timelineList').html(`
                                    <div class="text-center py-4 text-muted">
                                        Belum ada riwayat audit trail untuk kempu ini.
                                    </div>
                                `);
                                return;
                            }

                            let tHtml = '';
                            histories.forEach((h) => {
                                const dt = formatDateTime(h.created_at);
                                let dotClass = 'success';
                                let badgeColor = 'success';

                                if (h.action_result === 'NOT_OK' || (h.action && h
                                        .action.toLowerCase().includes('reject'))) {
                                    dotClass = 'scrap';
                                    badgeColor = 'danger';
                                } else if (h.action_result === 'HOLD' || (h.action && h
                                        .action.toLowerCase().includes('hold'))) {
                                    dotClass = 'warning';
                                    badgeColor = 'warning text-dark';
                                } else if (h.action_result === 'SCRAPPED' || (h
                                        .action && h.action.toLowerCase().includes(
                                            'scrap'))) {
                                    dotClass = 'scrap';
                                    badgeColor = 'dark';
                                }

                                const actor = h.operator_display_name
                                    || (h.created_by && (h.created_by.nama_lengkap || h.created_by.username))
                                    || (h.metadata && (h.metadata.operator_name || h.metadata.operator_email))
                                    || 'System';

                                tHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"></div>
                                        <div class="card shadow-none border bg-white mb-2">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                    <div>
                                                        <span class="badge bg-${badgeColor} me-1 fs-11">${h.action_result || 'LOG'}</span>
                                                        <strong class="text-body fs-13">${h.action || 'Pergerakan Kempu'}</strong>
                                                    </div>
                                                    <span class="text-muted fs-11"><i class="ri-time-line me-1"></i>${dt}</span>
                                                </div>

                                                <div class="row g-2 fs-12 text-muted mb-2 bg-light p-2 rounded">
                                                    <div class="col-sm-6">
                                                        <i class="ri-arrow-right-line text-primary me-1"></i>
                                                        <strong>Lokasi:</strong> ${h.from_location || '-'} &rarr; <strong>${h.to_location || '-'}</strong>
                                                    </div>
                                                    <div class="col-sm-6 text-sm-end">
                                                        <strong>Siklus Reused:</strong> <span class="badge bg-secondary fs-11">${h.reused_count || 0}x</span>
                                                    </div>
                                                </div>

                                                ${h.notes ? `<div class="fs-12 text-body mb-2"><i class="ri-chat-1-line text-muted me-1"></i><em>"${h.notes}"</em></div>` : ''}

                                                <div class="fs-11 text-muted d-flex align-items-center gap-1">
                                                    <i class="ri-user-3-line"></i> Petugas: <span class="fw-semibold text-dark">${actor}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            $('#timelineList').html(tHtml);
                        } else {
                            $('#timelineList').html(`
                                <div class="text-center py-4 text-danger">
                                    Gagal memuat riwayat perjalanan kempu.
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#timelineList').html(`
                            <div class="text-center py-4 text-danger">
                                Terjadi kesalahan saat memuat data riwayat kempu.
                            </div>
                        `);
                    }
                });
            });

            // Initial load table
            loadTableData();
        });
    </script>
@endsection
