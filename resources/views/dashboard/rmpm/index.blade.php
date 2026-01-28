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
                <div class="row g-3 justify-content-end">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Kedatangan</label>
                        <input type="date" class="form-control" id="dateFilter" value="">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="applyFilter">
                            <i class="mdi mdi-filter me-1"></i>Apply Filter
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-light w-100" id="resetFilter">
                            <i class="ri-restart-line me-1"></i> Reset
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
                                    <p class="stats-label mb-0">Acceptance Rate</p>
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
                                    <p class="stats-label mb-0">Rejection Rate</p>
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
                                    <p class="stats-label mb-0">Kelengkapan Dok</p>
                                </div>
                            </div>
                            <h3 class="stats-value" id="documentCompleteness">-</h3>
                            <p class="stats-subtitle mb-0">COA, SJ, dll</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Trend Kedatangan Bahan</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="trendChart"></div>
                            <div class="loading-overlay d-none" id="trendLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
                            <div class="loading-overlay d-none" id="dispositionLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
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
                            <h5 class="chart-title">Top 5 Bahan Paling Sering Datang</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="topMaterialsChart"></div>
                            <div class="loading-overlay d-none" id="topMaterialsLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">Performance Supplier (Acceptance Rate)</h5>
                        </div>
                        <div class="card-body position-relative">
                            <div id="supplierChart"></div>
                            <div class="loading-overlay d-none" id="supplierLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
                            <div class="loading-overlay d-none" id="vehicleLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
                            <div class="loading-overlay d-none" id="packagingLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
                                            <th>Supplier</th>
                                            <th>Lot/Batch</th>
                                            <th>Jumlah</th>
                                            <th>Jenis</th>
                                            <th>Disposisi</th>
                                            <th>Status Dokumen</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentDataTable">
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
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
            vehicleChart, packagingFindingsChart;

        // Initialize on page load
        $(document).ready(function() {
            // Set default date to today
            $('#dateFilter').val(new Date().toISOString().split('T')[0]);

            loadAllData();

            // Event listeners
            $('#applyFilter').on('click', function() {
                loadAllData();
            });

            $('#resetFilter').on('click', function() {
                $('#dateFilter').val('');
                loadAllData();
            });
        });

        // Load all dashboard data
        function loadAllData() {
            const params = getFilterParams();

            loadStatistics(params);
            loadTrendChart(params);
            loadDispositionChart(params);
            loadTopMaterialsChart(params);
            loadSupplierChart(params);
            loadVehicleChart(params);
            loadPackagingFindingsChart(params);
            loadRecentData(params);
        }

        // Get filter parameters
        function getFilterParams() {
            const date = $('#dateFilter').val();
            return date ? {
                date: date
            } : {};
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

        // Load Supplier Performance Chart
        function loadSupplierChart(params) {
            $('#supplierLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/supplier-performance',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#supplierLoading').addClass('d-none');

                    if (response.success) {
                        const options = {
                            series: [{
                                name: 'Acceptance Rate',
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
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return val + "%";
                                },
                                offsetY: -20,
                                style: {
                                    fontSize: '12px',
                                    colors: ["#304758"]
                                }
                            },
                            xaxis: {
                                categories: response.data.labels,
                                labels: {
                                    rotate: -45,
                                    rotateAlways: true
                                }
                            },
                            yaxis: {
                                min: 0,
                                max: 100,
                                title: {
                                    text: 'Acceptance Rate (%)'
                                }
                            },
                            colors: ['#2e7d32']
                        };

                        if (supplierChart) {
                            supplierChart.destroy();
                        }
                        supplierChart = new ApexCharts(document.querySelector("#supplierChart"), options);
                        supplierChart.render();
                    }
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
                        $('#vehicleChart').html(
                            '<p class="text-center text-muted mt-4">Tidak ada data kondisi kendaraan</p>'
                        );
                        return;
                    }

                    const options = {
                        series: [{
                            name: 'Compliance Rate',
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
                            rows = '<tr><td colspan="7" class="text-center py-4">Tidak ada data</td></tr>';
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

                                rows += `
                                <tr>
                                    <td>${item.tanggal}</td>
                                    <td>${item.supplier}</td>
                                    <td>${item.lot_batch}</td>
                                    <td>${item.jumlah}</td>
                                    <td>${item.jenis}</td>
                                    <td>${disposisiBadge}</td>
                                    <td>${dokumenBadge}</td>
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
