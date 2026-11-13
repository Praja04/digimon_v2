@extends('layouts.component.main')
@section('title', 'Dashboard - RMPM')
@section('styles')
    <style>
        .stats-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: 700;
            margin: 12px 0 4px 0;
            line-height: 1;
        }

        .stats-label {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stats-subtitle {
            font-size: 12px;
            color: #adb5bd;
            margin-top: 4px;
        }

        .chart-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .chart-header {
            padding: 20px 20px 0 20px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 12px 0;
        }

        .filter-card {
            background: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-card .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .filter-card .form-select {
            font-size: 14px;
            border-color: #dee2e6;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-accept {
            background-color: #d1f4e0;
            color: #0f7b3f;
        }

        .status-reject {
            background-color: #ffe0e0;
            color: #c41e3a;
        }

        .status-pending {
            background-color: #fff4e6;
            color: #cc6600;
        }

        .bg-primary-light {
            background-color: #e8eaf6;
            color: #3f51b5;
        }

        .bg-success-light {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .bg-warning-light {
            background-color: #fff3e0;
            color: #e65100;
        }

        .bg-info-light {
            background-color: #e1f5fe;
            color: #0277bd;
        }

        .bg-danger-light {
            background-color: #ffebee;
            color: #c62828;
        }

        .bg-secondary-light {
            background-color: #f5f5f5;
            color: #616161;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }

        /* Gaya Khusus Rekomendasi Gula */
        .sugar-batch-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .sugar-batch-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
            border-color: #e2e8f0;
            border-left-color: #dc2626;
        }
    </style>
@endsection
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
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Filter Section -->
            <div class="filter-card">
                <div class="row g-3 justify-content-end align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Kategori Bahan</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="all" selected>Semua (RM & PM)</option>
                            <option value="rm">Raw Material (RM)</option>
                            <option value="pm">Packaging Material (PM)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Kedatangan</label>
                        <input type="date" class="form-control" id="dateFilter" value="{{ $defaultDate ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="applyFilter">
                            <i class="mdi mdi-filter me-1"></i>Terapkan Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-light w-100" id="resetFilter">
                            <i class="ri-restart-line me-1"></i> Atur Ulang
                        </button>
                    </div>
                </div>
            </div>
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary-light">
                                    <i class="ri-inbox-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Total Kedatangan</p>
                                </div>
                            </div>
                            <h3 class="stats-value" id="totalKedatangan">-</h3>
                            <p class="stats-subtitle mb-0">Bahan masuk</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success-light">
                                    <i class="ri-check-double-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Tingkat Penerimaan</p>
                                </div>
                            </div>
                            <h3 class="stats-value" id="acceptanceRate">-</h3>
                            <p class="stats-subtitle mb-0">Bahan diterima</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning-light">
                                    <i class="ri-close-circle-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Tingkat Penolakan</p>
                                </div>
                            </div>
                            <h3 class="stats-value" id="rejectionRate">-</h3>
                            <p class="stats-subtitle mb-0">Bahan ditolak</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-secondary-light">
                                    <i class="ri-file-list-3-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Kelengkapan Dokumen</p>
                                </div>
                            </div>
                            <h3 class="stats-value" id="documentCompleteness">-</h3>
                            <p class="stats-subtitle mb-0">COA, Surat Jalan, dll</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Tren Kedatangan Bahan</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="trendChart"></div>
                            <div id="emptyStateTrend" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="trendLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Distribusi Disposisi</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="dispositionChart"></div>
                            <div id="emptyStateDisposition" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="dispositionLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row: Analisa Ketahanan Gula (Raw Material) -->
            <div class="row g-3 mb-4" id="sugarStabilitySection">
                <!-- Card Kiri: Diagram Donut Ketahanan Gula -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center pb-3">
                            <div>
                                <h5 class="chart-title mb-1">Ketahanan Simpan Gula (Uji Kristal)</h5>
                                <p class="text-muted mb-0" style="font-size: 12px;">Klasifikasi masa simpan bahan baku Gula Kelapa & Gula Tebu</p>
                            </div>
                            <span class="badge bg-light text-dark border px-2 py-1" id="totalSugarBatchesBadge" style="font-size: 11px;">0 Batch</span>
                        </div>
                        <div class="card-body position-relative">
                            <div id="sugarDonutChart"></div>
                            <div id="emptyStateSugarDonut" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#ddd;"></i>
                                <h5 class="mt-2 text-muted" style="font-size: 14px;">Data Gula Tidak Tersedia</h5>
                                <p class="text-muted small">Tidak ada data kedatangan gula pada tanggal ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Kanan: Rekomendasi Prioritas Pemakaian Gula -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center pb-3">
                            <div>
                                <h5 class="chart-title mb-1">Rekomendasi Pemakaian Gula</h5>
                                <p class="text-muted mb-0" style="font-size: 12px;">Batch rentan kristal (Grup C) yang harus diprioritaskan ke produksi</p>
                            </div>
                            <span class="badge bg-danger" id="criticalAlertCountBadge" style="font-size: 11px;">0 Kritis</span>
                        </div>
                        <div class="card-body position-relative d-flex flex-column justify-content-between" style="min-height: 330px;">
                            <!-- Alert Status Banner -->
                            <div id="sugarAlertBanner" class="alert alert-success py-2 px-3 d-flex align-items-center mb-3" style="font-size: 13px;">
                                <i class="ri-checkbox-circle-line fs-5 me-2 text-success" id="sugarAlertBannerIcon"></i>
                                <span id="sugarAlertBannerText">Seluruh persediaan gula dalam kondisi stabil.</span>
                            </div>

                            <!-- List Batch Mendesak -->
                            <div id="criticalAlertContainer" style="max-height: 210px; overflow-y: auto;" class="flex-grow-1">
                                <!-- Dynamic Batch Cards -->
                            </div>

                            <!-- Catatan SOP Standar QC -->
                            <div class="mt-3 pt-2 border-top text-muted d-flex justify-content-between align-items-center" style="font-size: 11px;">
                                <span><i class="ri-information-line me-1"></i> <strong>Standar Simpan:</strong> Grup A (~30 hr) | Grup B (~14 hr) | Grup C (3–7 hr)</span>
                                <span class="fw-semibold text-dark">QC Raw Material</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">5 Bahan Paling Sering Datang</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="topMaterialsChart"></div>
                            <div id="emptyStateTopMaterials" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="topMaterialsLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Frekuensi Supplier</h5>
                            <p class="text-muted mb-0 small">Frekuensi pengiriman per pemasok (SPB)</p>
                        </div>
                        <div class="card-body position-relative">
                            <div id="supplierChart"></div>
                            <div id="emptyStateSupplier" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="supplierLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 3 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Kondisi Kendaraan</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="vehicleChart"></div>
                            <div id="emptyStateVehicle" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="vehicleLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Temuan Fisik Kemasan</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="packagingFindingsChart"></div>
                            <div id="emptyStatePackagingFindings" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div class="loading-overlay d-none" id="packagingLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Data Table -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Data Kedatangan Bahan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Pemasok</th>
                                            <th>Lot / Batch</th>
                                            <th>Jumlah</th>
                                            <th>Jenis</th>
                                            <th>Disposisi</th>
                                            <th>Status Dokumen</th>
                                            <th>Estimasi Ketahanan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentDataTable">
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Memuat data...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Global variables
        let trendChart, dispositionChart, topMaterialsChart, supplierChart,
            vehicleChart, packagingFindingsChart, sugarDonutChart;

        const defaultFilterDate = '{{ $defaultDate ?? date('Y-m-d') }}';

        // Initialize on page load
        $(document).ready(function() {
            if (!$('#dateFilter').val()) {
                $('#dateFilter').val(defaultFilterDate);
            }

            loadAllData();

            // Event listeners
            $('#applyFilter').on('click', function() {
                loadAllData();
            });

            $('#categoryFilter').on('change', function() {
                loadAllData();
            });

            $('#resetFilter').on('click', function() {
                $('#dateFilter').val(defaultFilterDate);
                $('#categoryFilter').val('all');
                loadAllData();
            });
        });

        function showEmptyState(chartId, emptyId) {
            $(chartId).hide();
            $(emptyId).removeClass('d-none');
        }


        function showChart(chartId, emptyId) {
            $(emptyId).addClass('d-none');
            $(chartId).show();
        }

        // Load all dashboard data
        function loadAllData() {
            const params = getFilterParams();

            loadStatistics(params);
            loadTrendChart(params);
            loadDispositionChart(params);
            loadSugarStability(params);
            loadTopMaterialsChart(params);
            loadSupplierChart(params);
            loadVehicleChart(params);
            loadPackagingFindingsChart(params);
            loadRecentData(params);
        }

        // Get filter parameters
        function getFilterParams() {
            const date = $('#dateFilter').val();
            const category = $('#categoryFilter').val() || 'all';
            const params = {
                category: category
            };
            if (date) {
                params.date = date;
            }
            return params;
        }

        // Load Statistics
        function loadStatistics(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/statistics',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#totalKedatangan').text(data.total_kedatangan);
                        $('#acceptanceRate').text(data.acceptance_rate + '%');
                        $('#rejectionRate').text(data.rejection_rate + '%');
                        $('#documentCompleteness').text(data.document_completeness + '%');
                    }
                }
            });
        }

        // Load Trend Chart
        function loadTrendChart(params) {
            $('#trendLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/trend-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#trendLoading').addClass('d-none');

                    if (
                        !response.success ||
                        response.data.labels.length === 0
                    ) {
                        if (trendChart) trendChart.destroy();
                        showEmptyState('#trendChart', '#emptyStateTrend');
                        return;
                    }


                    showChart('#trendChart', '#emptyStateTrend');

                    if (response.success) {
                        const options = {
                            series: [{
                                name: 'Raw Material',
                                data: response.data.raw_material
                            }, {
                                name: 'Packaging Material',
                                data: response.data.packaging
                            }],
                            chart: {
                                type: 'area',
                                height: 300,
                                toolbar: {
                                    show: false
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            xaxis: {
                                categories: response.data.labels
                            },
                            colors: ['#3f51b5', '#2e7d32'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.4,
                                    opacityTo: 0.1,
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right'
                            }
                        };

                        if (trendChart) {
                            trendChart.destroy();
                        }
                        trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
                        trendChart.render();
                    }
                }
            });
        }

        // Load Disposition Chart
        function loadDispositionChart(params) {
            $('#dispositionLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/disposition-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#dispositionLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (dispositionChart) dispositionChart.destroy();
                        showEmptyState('#dispositionChart', '#emptyStateDisposition');
                        return;
                    }
                    showChart('#dispositionChart', '#emptyStateDisposition');

                    if (response.success) {
                        const options = {
                            series: response.data.values,
                            chart: {
                                type: 'donut',
                                height: 300
                            },
                            labels: response.data.labels,
                            colors: ['#2e7d32', '#c62828', '#1565c0', '#e65100'],
                            legend: {
                                position: 'bottom'
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%'
                                    }
                                }
                            }
                        };

                        if (dispositionChart) {
                            dispositionChart.destroy();
                        }
                        dispositionChart = new ApexCharts(document.querySelector("#dispositionChart"), options);
                        dispositionChart.render();
                    }
                }
            });
        }

        // Load Top Materials Chart
        function loadTopMaterialsChart(params) {
            $('#topMaterialsLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/top-materials',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#topMaterialsLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (topMaterialsChart) topMaterialsChart.destroy();
                        showEmptyState('#topMaterialsChart', '#emptyStateTopMaterials');
                        return;
                    }
                    showChart('#topMaterialsChart', '#emptyStateTopMaterials');

                    if (response.success) {
                        const options = {
                            series: [{
                                name: 'Frekuensi',
                                data: response.data.values
                            }],
                            chart: {
                                type: 'bar',
                                height: 300,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    borderRadius: 4,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                offsetX: 30,
                                style: {
                                    fontSize: '12px',
                                    colors: ['#304758']
                                }
                            },
                            xaxis: {
                                categories: response.data.labels
                            },
                            colors: ['#3f51b5']
                        };

                        if (topMaterialsChart) {
                            topMaterialsChart.destroy();
                        }
                        topMaterialsChart = new ApexCharts(document.querySelector("#topMaterialsChart"),
                            options);
                        topMaterialsChart.render();
                    }
                }
            });
        }

        // Load Supplier Frequency Chart
        function loadSupplierChart(params) {
            $('#supplierLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/supplier-performance',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#supplierLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (supplierChart) supplierChart.destroy();
                        showEmptyState('#supplierChart', '#emptyStateSupplier');
                        return;
                    }
                    showChart('#supplierChart', '#emptyStateSupplier');

                    const chartData = (response.data.totals && response.data.totals.length > 0)
                        ? response.data.totals
                        : response.data.values;

                    const options = {
                        series: [{
                            name: 'Frekuensi Pengiriman (SPB)',
                            data: chartData
                        }],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                dataLabels: {
                                    position: 'top'
                                },
                                columnWidth: '45%'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val + " SPB";
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '11px',
                                colors: ["#304758"],
                                fontWeight: 600
                            }
                        },
                        xaxis: {
                            categories: response.data.labels,
                            labels: {
                                rotate: -35,
                                trim: true,
                                maxHeight: 60,
                                style: { fontSize: '11px' }
                            }
                        },
                        yaxis: {
                            forceNiceScale: true,
                            min: 0,
                            labels: {
                                formatter: function(val) {
                                    return Math.round(val) + ' SPB';
                                }
                            }
                        },
                        colors: ['#059669'],
                        tooltip: {
                            y: {
                                formatter: function(val, opt) {
                                    let str = val + ' Pengiriman (SPB)';
                                    if (response.data.values && response.data.values[opt.dataPointIndex] !== undefined) {
                                        str += ' • Mutu: ' + response.data.values[opt.dataPointIndex] + '% Lulus';
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (supplierChart) {
                        supplierChart.destroy();
                    }
                    supplierChart = new ApexCharts(document.querySelector("#supplierChart"), options);
                    supplierChart.render();
                },
                error: function() {
                    $('#supplierLoading').addClass('d-none');
                    if (supplierChart) supplierChart.destroy();
                    showEmptyState('#supplierChart', '#emptyStateSupplier');
                }
            });
        }

        // Load Vehicle Condition Chart
        function loadVehicleChart(params) {
            $('#vehicleLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/vehicle-condition',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#vehicleLoading').addClass('d-none');

                    if (!response.data) {
                        if (vehicleChart) vehicleChart.destroy();
                        showEmptyState('#vehicleChart', '#emptyStateVehicle');
                        return;
                    }
                    showChart('#vehicleChart', '#emptyStateVehicle');

                    const options = {
                        series: [{
                            name: 'Tingkat Kepatuhan',
                            data: response.data.values
                        }],
                        chart: {
                            height: 300,
                            type: 'radar',
                            toolbar: {
                                show: false
                            }
                        },
                        xaxis: {
                            categories: response.data.labels
                        },
                        yaxis: {
                            min: 0,
                            max: 100
                        }
                    };

                    if (vehicleChart) vehicleChart.destroy();
                    vehicleChart = new ApexCharts(
                        document.querySelector("#vehicleChart"),
                        options
                    );
                    vehicleChart.render();
                }
            });
        }

        // Load Packaging Findings Chart
        function loadPackagingFindingsChart(params) {
            $('#packagingLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/packaging-findings',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#packagingLoading').addClass('d-none');

                     if (!response.success || response.data.values.length === 0) {
                        if (packagingFindingsChart) packagingFindingsChart.destroy();
                        showEmptyState('#packagingFindingsChart', '#emptyStatePackagingFindings');
                        return;
                    }
                    showChart('#packagingFindingsChart', '#emptyStatePackagingFindings');

                    if (response.success) {
                        const options = {
                            series: [{
                                name: 'Jumlah Temuan',
                                data: response.data.values
                            }],
                            chart: {
                                type: 'bar',
                                height: 300,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 4,
                                    horizontal: false,
                                    distributed: true
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: response.data.labels
                            },
                            colors: ['#c62828', '#d84315', '#f4511e', '#ff6f00', '#ff8f00', '#ffa000'],
                            legend: {
                                show: false
                            }
                        };

                        if (packagingFindingsChart) {
                            packagingFindingsChart.destroy();
                        }
                        packagingFindingsChart = new ApexCharts(document.querySelector(
                            "#packagingFindingsChart"), options);
                        packagingFindingsChart.render();
                    }
                }
            });
        }

        // Load Sugar Stability (Analisa Ketahanan & Kualitas Gula - Donut Bersih & Actionable)
        function loadSugarStability(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/sugar-stability',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (!response.success || !response.is_available) {
                        if (params.category === 'pm') {
                            $('#sugarStabilitySection').slideUp(200);
                        } else {
                            $('#sugarStabilitySection').slideDown(200);
                        }
                        return;
                    }

                    $('#sugarStabilitySection').slideDown(200);
                    const data = response.data;

                    // Header Badges
                    $('#totalSugarBatchesBadge').text(data.total_batches + ' Batch Teruji');
                    $('#criticalAlertCountBadge').text(data.group_c + ' Kritis');

                    // 1. Donut Chart (Kiri)
                    if (data.total_batches === 0) {
                        if (sugarDonutChart) sugarDonutChart.destroy();
                        $('#sugarDonutChart').hide();
                        $('#emptyStateSugarDonut').removeClass('d-none');
                    } else {
                        $('#emptyStateSugarDonut').addClass('d-none');
                        $('#sugarDonutChart').show();

                        const donutOptions = {
                            series: data.values,
                            chart: {
                                type: 'donut',
                                height: 280
                            },
                            labels: [
                                'Grup A (~30 Hari)',
                                'Grup B (~14 Hari)',
                                'Grup C (3–7 Hari)'
                            ],
                            colors: ['#10b981', '#f59e0b', '#ef4444'],
                            legend: {
                                position: 'bottom',
                                fontSize: '12px',
                                markers: { radius: 12 },
                                formatter: function(seriesName, opts) {
                                    const val = opts.w.globals.series[opts.seriesIndex];
                                    return seriesName + ': ' + val + ' Batch';
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return Math.round(val) + "%";
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '68%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total Teruji',
                                                fontSize: '13px',
                                                fontWeight: 600,
                                                color: '#64748b',
                                                formatter: function() {
                                                    return data.total_batches + ' Batch';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        const pct = data.total_batches > 0 ? Math.round((val / data.total_batches) * 100) : 0;
                                        return val + ' Batch (' + pct + '%)';
                                    }
                                }
                            }
                        };

                        if (sugarDonutChart) sugarDonutChart.destroy();
                        sugarDonutChart = new ApexCharts(document.querySelector("#sugarDonutChart"), donutOptions);
                        sugarDonutChart.render();
                    }

                    // 2. Status Banner & Rekomendasi (Kanan)
                    if (data.group_c > 0) {
                        $('#sugarAlertBanner')
                            .removeClass('alert-success')
                            .addClass('alert-warning')
                            .html(`
                                <i class="ri-alert-line fs-5 me-2 text-danger"></i>
                                <div>
                                    <strong>Perhatian Operasional:</strong> Ditemukan <strong>${data.group_c} batch Grup C</strong> yang cepat mengkristal dan wajib diprioritaskan ke tangki pelarutan.
                                </div>
                            `);
                    } else {
                        $('#sugarAlertBanner')
                            .removeClass('alert-warning')
                            .addClass('alert-success')
                            .html(`
                                <i class="ri-checkbox-circle-line fs-5 me-2 text-success"></i>
                                <div>
                                    <strong>Kondisi Baik:</strong> Seluruh persediaan gula saat ini dalam kondisi stabil (Grup A & B).
                                </div>
                            `);
                    }

                    // Render List Batch Mendesak
                    let listHtml = '';
                    if (!data.critical_alerts || data.critical_alerts.length === 0) {
                        listHtml = `
                            <div class="text-center py-4 my-auto">
                                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                                    <i class="ri-check-double-line fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-success mb-1">Tidak Ada Antrean Mendesak</h6>
                                <p class="text-muted small mb-0">Masa simpan persediaan gula masih aman sesuai standar mutu laboratorium.</p>
                            </div>
                        `;
                    } else {
                        data.critical_alerts.forEach(function(item, idx) {
                            listHtml += `
                                <div class="sugar-batch-card d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-danger" style="font-size: 10px;">Prioritas #${idx + 1}</span>
                                            <span class="fw-bold text-dark" style="font-size: 13px;">${item.spb}</span>
                                            <span class="badge bg-light text-muted border">${item.jenis}</span>
                                        </div>
                                        <div class="small text-muted">
                                            Pemasok: <strong>${item.supplier}</strong> &bull; Kedatangan: ${item.tanggal}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-danger-subtle text-danger mb-1" style="font-size: 11px;">
                                            <i class="ri-time-line me-1"></i> Aging: ${item.aging_hari} Hari
                                        </div>
                                        <div>
                                            <span class="badge bg-danger" style="font-size: 10px;">Segera Larutkan</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#criticalAlertContainer').html(listHtml);
                }
            });
        }

        // Load Recent Data Table
        function loadRecentData(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/recent-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (response.success) {
                        let rows = '';

                        if (response.data.length === 0) {
                            rows = '<tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>';
                        } else {
                            response.data.forEach(function(item) {
                                let disposisiBadge = '';
                                switch (item.disposisi.toUpperCase()) {
                                    case 'RELEASE':
                                        disposisiBadge =
                                            '<span class="status-badge status-accept">Release</span>';
                                        break;
                                    case 'REJECT':
                                        disposisiBadge =
                                            '<span class="status-badge status-reject">Reject</span>';
                                        break;
                                    default:
                                        disposisiBadge =
                                            '<span class="status-badge status-pending">Pending</span>';
                                }

                                let dokumenBadge = item.dokumen_status === 'Lengkap' ?
                                    '<span class="status-badge status-accept">Lengkap</span>' :
                                    '<span class="status-badge status-pending">' + item.dokumen_status +
                                    '</span>';

                                let ketahananBadge = '<span class="text-muted" style="font-size: 11px;">-</span>';
                                if (item.is_sugar) {
                                    let badgeClass = 'status-pending';
                                    if (item.sugar_badge === 'danger') {
                                        badgeClass = 'status-reject';
                                    } else if (item.sugar_badge === 'success') {
                                        badgeClass = 'status-accept';
                                    }
                                    ketahananBadge = `<span class="status-badge ${badgeClass}" title="${item.sugar_shelf_life}">${item.sugar_group}: ${item.sugar_shelf_life}</span>`;
                                }

                                rows += `
                                <tr>
                                    <td>${item.tanggal}</td>
                                    <td>${item.supplier}</td>
                                    <td>${item.lot_batch}</td>
                                    <td>${item.jumlah}</td>
                                    <td>${item.jenis}</td>
                                    <td>${disposisiBadge}</td>
                                    <td>${dokumenBadge}</td>
                                    <td>${ketahananBadge}</td>
                                </tr>
                            `;
                            });
                        }

                        $('#recentDataTable').html(rows);
                    }
                }
            });
        };
    </script>
@endsection
