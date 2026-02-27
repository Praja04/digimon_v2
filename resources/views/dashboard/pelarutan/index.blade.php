@extends('layouts.component.main')
@section('title', 'Dashboard - Analisis Pelarutan')
@section('styles')
    <!-- Custom CSS for enhanced styling -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75);
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .apex-charts {
            min-height: 320px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            .page-title-box {
                text-align: center;
            }

            .page-title-box .page-title-right {
                margin-top: 1rem;
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation: spin 1s linear infinite;
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

            <!-- Main Analysis Charts Section -->
            <div class="row g-4 mb-3">
                <!-- Pelarutan 1 Chart -->
                <div class="col-xl-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-dark fw-bold">Pelarutan 1 Analysis</h5>
                                    <p class="text-muted mb-0 small">Brix & NaCl monitoring trends</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0"
                                        style="min-width: 280px;">
                                        <h6 class="dropdown-header text-uppercase fw-bold text-primary mb-3">
                                            <i class="ri-calendar-line me-1"></i>Date Range Filter
                                        </h6>
                                        <div class="mb-3">
                                            <label for="start_date_pelarutan1" class="form-label small fw-semibold">Start
                                                Date</label>
                                            <input type="date" id="start_date_pelarutan1"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date_pelarutan1" class="form-label small fw-semibold">End
                                                Date</label>
                                            <input type="date" id="end_date_pelarutan1"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="variant_pelarutan1"
                                                class="form-label small fw-semibold">Variant</label>
                                            <select id="variant_pelarutan1" class="form-select form-select-sm">
                                                <option value="">-- All Variants --</option>
                                                <option value="SS1">SS1</option>
                                                <option value="SS2">SS2</option>
                                                <option value="BB">BB</option>
                                                <option value="MSD NR1">MSD NR1</option>
                                                <option value="MSD NR2">MSD NR2</option>
                                                <option value="JB">JB</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100 btn-sm" id="filter_pelarutan1">
                                            <i class="ri-search-line me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div id="chart-pelarutan1" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <!-- Pelarutan 2 Chart -->
                <div class="col-xl-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-dark fw-bold">Pelarutan 2 Analysis</h5>
                                    <p class="text-muted mb-0 small">Brix & NaCl monitoring trends</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0"
                                        style="min-width: 280px;">
                                        <h6 class="dropdown-header text-uppercase fw-bold text-success mb-3">
                                            <i class="ri-calendar-line me-1"></i>Date Range Filter
                                        </h6>
                                        <div class="mb-3">
                                            <label for="start_date_pelarutan2" class="form-label small fw-semibold">Start
                                                Date</label>
                                            <input type="date" id="start_date_pelarutan2"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date_pelarutan2" class="form-label small fw-semibold">End
                                                Date</label>
                                            <input type="date" id="end_date_pelarutan2"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="variant_pelarutan2"
                                                class="form-label small fw-semibold">Variant</label>
                                            <select id="variant_pelarutan2" class="form-select form-select-sm">
                                                <option value="">-- All Variants --</option>
                                                <option value="SS1">SS1</option>
                                                <option value="SS2">SS2</option>
                                                <option value="BB">BB</option>
                                                <option value="MSD NR1">MSD NR1</option>
                                                <option value="MSD NR2">MSD NR2</option>
                                                <option value="JB">JB</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-success w-100 btn-sm" id="filter_pelarutan2">
                                            <i class="ri-search-line me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div id="chart-pelarutan2" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disposition Analysis Section -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 text-dark fw-bold">
                                <i class="ri-pie-chart-line text-warning me-2"></i>Disposition Analysis
                            </h5>
                            <p class="text-muted mb-0">Distribution analysis by disposition types</p>
                        </div>
                        <div class="badge bg-soft-info text-info px-3 py-2">
                            <i class="ri-information-line me-1"></i>Real-time Data
                        </div>
                    </div>
                    <hr class="my-3">
                </div>

                <!-- Pelarutan 1 Disposition -->
                <div class="col-xl-6 mb-3">
                    <div class="card border-0 shadow-lg h-100 position-relative">
                        <div class="position-absolute top-0 start-0 w-100 bg-gradient-primary rounded-top"
                            style="height: 4px;"></div>
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-dark fw-bold">
                                        <i class="ri-bar-chart-box-line text-primary me-2"></i>Pelarutan 1 Disposition
                                    </h5>
                                    <p class="text-muted mb-0 small">Distribution breakdown analysis</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0"
                                        style="min-width: 280px;">
                                        <h6 class="dropdown-header text-uppercase fw-bold text-primary mb-3">
                                            <i class="ri-calendar-line me-1"></i>Date Range Filter
                                        </h6>
                                        <div class="mb-3">
                                            <label for="start_date_pelarutan1_disposisi"
                                                class="form-label small fw-semibold">Start Date</label>
                                            <input type="date" id="start_date_pelarutan1_disposisi"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date_pelarutan1_disposisi"
                                                class="form-label small fw-semibold">End
                                                Date</label>
                                            <input type="date" id="end_date_pelarutan1_disposisi"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="variant_pelarutan1_disposisi"
                                                class="form-label small fw-semibold">Variant</label>
                                            <select id="variant_pelarutan1_disposisi" class="form-select form-select-sm">
                                                <option value="">-- All Variants --</option>
                                                <option value="SS1">SS1</option>
                                                <option value="SS2">SS2</option>
                                                <option value="BB">BB</option>
                                                <option value="MSD NR1">MSD NR1</option>
                                                <option value="MSD NR2">MSD NR2</option>
                                                <option value="JB">JB</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100 btn-sm" id="filter_pelarutan1_disposisi">
                                            <i class="ri-search-line me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div id="disposition-pelarutan1" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <!-- Pelarutan 2 Disposition -->
                <div class="col-xl-6 mb-3">
                    <div class="card border-0 shadow-lg h-100 position-relative">
                        <div class="position-absolute top-0 start-0 w-100 bg-gradient-success rounded-top"
                            style="height: 4px;"></div>
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-dark fw-bold">
                                        <i class="ri-bar-chart-box-line text-success me-2"></i>Pelarutan 2 Disposition
                                    </h5>
                                    <p class="text-muted mb-0 small">Distribution breakdown analysis</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0"
                                        style="min-width: 280px;">
                                        <h6 class="dropdown-header text-uppercase fw-bold text-success mb-3">
                                            <i class="ri-calendar-line me-1"></i>Date Range Filter
                                        </h6>
                                        <div class="mb-3">
                                            <label for="start_date_pelarutan2_disposisi"
                                                class="form-label small fw-semibold">Start Date</label>
                                            <input type="date" id="start_date_pelarutan2_disposisi"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date_pelarutan2_disposisi"
                                                class="form-label small fw-semibold">End
                                                Date</label>
                                            <input type="date" id="end_date_pelarutan2_disposisi"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label for="variant_pelarutan2_disposisi"
                                                class="form-label small fw-semibold">Variant</label>
                                            <select id="variant_pelarutan2_disposisi" class="form-select form-select-sm">
                                                <option value="">-- All Variants --</option>
                                                <option value="SS1">SS1</option>
                                                <option value="SS2">SS2</option>
                                                <option value="BB">BB</option>
                                                <option value="MSD NR1">MSD NR1</option>
                                                <option value="MSD NR2">MSD NR2</option>
                                                <option value="JB">JB</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-success w-100 btn-sm" id="filter_pelarutan2_disposisi">
                                            <i class="ri-search-line me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div id="disposition-pelarutan2" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
    <script>
        $(document).ready(function() {
            // Global variables to store chart instances
            let chartInstances = {};

            // Loading spinner function
            function showLoading(selector) {
                $(selector).html(`
            <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
            }

            // Fetch chart data function
            function fetchChartData(type, startDate = null, endDate = null, variant = null) {
                let url = "{{ route('api.dashboard.pelarutan.analisa') }}";
                let params = [];

                if (startDate && endDate) {
                    params.push(`start_date=${startDate}&end_date=${endDate}`);
                }
                if (variant) {
                    params.push(`variant=${encodeURIComponent(variant)}`);
                }

                if (params.length > 0) {
                    url += `?${params.join('&')}`;
                }

                return $.getJSON(url)
                    .done(function(response) {
                        const data = type === 'pelarutan1' ? response.pelarutan1 : response.pelarutan2;
                        renderChart(`#chart-${type}`, data, type.toUpperCase());
                    })
                    .fail(function(xhr) {
                        console.error(`Error fetching ${type} chart data:`, xhr);
                        $(`#chart-${type}`).html(`
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        <div>Error loading ${type.toUpperCase()} chart data. Please try again.</div>
                    </div>
                `);
                    });
            }

            // Render line chart function
            function renderChart(selector, data, title) {
                // Destroy existing chart instance
                if (chartInstances[selector]) {
                    chartInstances[selector].destroy();
                    delete chartInstances[selector];
                }

                // Handle empty data
                if (!data || !Array.isArray(data) || data.length === 0) {
                    $(selector).html(`
                <div class="alert alert-info d-flex align-items-center justify-content-center" role="alert" style="height:300px;">
                    <i class="ri-information-line me-2"></i>
                    <div>No ${title} data available for the selected filters.</div>
                </div>
            `);
                    return;
                }

                const categories = data.map(item =>
                    `Batch ${item.batch_number} - ${item.variant} (PO: ${item.po_number})`);
                const brixSeries = data.map(item => parseFloat(item.brix) || 0);
                const naclSeries = data.map(item => parseFloat(item.nacl) || 0);
                const metaData = data.map(item => ({
                    po: item.po_number,
                    variant: item.variant,
                    batch: item.batch_number,
                    label: `Batch ${item.batch_number} - ${item.variant} (PO: ${item.po_number})`
                }));

                const options = {
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: false,
                                reset: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    series: [{
                        name: 'Brix',
                        data: brixSeries,
                        color: '#667eea'
                    }, {
                        name: 'NaCl',
                        data: naclSeries,
                        color: '#11998e'
                    }],
                    xaxis: {
                        categories: categories,
                        type: 'category',
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Concentration (°Bx)',
                            style: {
                                fontWeight: 600
                            }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 6,
                        hover: {
                            size: 8
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 3
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontWeight: 600
                    },
                    title: {
                        text: `${title} Brix & NaCl Analysis`,
                        align: 'left',
                        style: {
                            fontSize: '16px',
                            fontWeight: 700,
                            color: '#2d3748'
                        }
                    },
                    tooltip: {
                        shared: true,
                        custom: function({
                            series,
                            dataPointIndex
                        }) {
                            if (dataPointIndex < 0 || !metaData[dataPointIndex]) return '';

                            const brix = series[0][dataPointIndex];
                            const nacl = series[1][dataPointIndex];
                            const meta = metaData[dataPointIndex];

                            return `
                        <div class="apex-tooltip p-3 bg-white shadow-lg border-0 rounded">
                            <div class="fw-bold text-dark mb-2">${meta.label}</div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="bg-primary rounded me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Brix: <strong>${brix.toFixed(2)} °Bx</strong></span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="bg-success rounded me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">NaCl: <strong>${nacl.toFixed(2)} °Bx</strong></span>
                            </div>
                            <div class="small text-muted mt-2">
                                <div>PO: ${meta.po}</div>
                                <div>Variant: ${meta.variant}</div>
                            </div>
                        </div>
                    `;
                        }
                    }
                };

                $(selector).html('');
                chartInstances[selector] = new ApexCharts(document.querySelector(selector), options);
                chartInstances[selector].render();
            }

            // Fetch disposition data function
            function fetchDispositionData(type, startDate = null, endDate = null, variant = null) {
                let url = "{{ route('api.dashboard.pelarutan.analisa.disposisi') }}";
                let params = [];

                if (startDate && endDate) {
                    params.push(`start_date=${startDate}&end_date=${endDate}`);
                }
                if (variant) {
                    params.push(`variant=${encodeURIComponent(variant)}`);
                }

                if (params.length > 0) {
                    url += `?${params.join('&')}`;
                }

                return $.getJSON(url)
                    .done(function(response) {
                        const data = type === 'pelarutan1' ? response.pelarutan1 : response.pelarutan2;
                        renderDispositionChart(`#disposition-${type}`, data, type.toUpperCase());
                    })
                    .fail(function(xhr) {
                        console.error(`Error fetching ${type} disposition data:`, xhr);
                        $(`#disposition-${type}`).html(`
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        <div>Error loading ${type.toUpperCase()} disposition data. Please try again.</div>
                    </div>
                `);
                    });
            }

            // Render disposition pie chart function - FIXED VERSION
            function renderDispositionChart(selector, data, title) {
                // Destroy existing chart instance
                if (chartInstances[selector]) {
                    chartInstances[selector].destroy();
                    delete chartInstances[selector];
                }

                // Handle empty data
                if (!data || Object.keys(data).length === 0) {
                    $(selector).html(`
            <div class="alert alert-info d-flex align-items-center justify-content-center" role="alert" style="height:300px;">
                <i class="ri-information-line me-2"></i>
                <div>No ${title} disposition data available for the selected filters.</div>
            </div>
        `);
                    return;
                }

                // Convert object to arrays for ApexCharts
                const labels = Object.keys(data);
                const series = Object.values(data);
                const total = series.reduce((a, b) => a + b, 0);

                // Define colors for different disposition types
                const colorMap = {
                    'Release': '#667eea',
                    'RELEASE': '#667eea',
                    'Reject': '#fc8181',
                    'REJECT': '#fc8181',
                    'Hold': '#f6ad55',
                    'HOLD': '#f6ad55',
                    'Resampling': '#11998e',
                    'RESAMPLING': '#11998e',
                    'Rework': '#9f7aea',
                    'REWORK': '#9f7aea',
                    'Pending': '#63b3ed',
                    'PENDING': '#63b3ed'
                };

                // Assign colors based on disposition names
                const colors = labels.map(label => colorMap[label] || '#48bb78');

                const options = {
                    chart: {
                        type: 'pie',
                        height: 380,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: {
                                enabled: true,
                                delay: 150
                            },
                            dynamicAnimation: {
                                enabled: true,
                                speed: 350
                            }
                        }
                    },
                    series: series,
                    labels: labels,
                    colors: colors,
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        floating: false,
                        fontSize: '14px',
                        fontWeight: 600,
                        markers: {
                            width: 14,
                            height: 14,
                            radius: 6
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 5
                        },
                        formatter: function(seriesName, opts) {
                            const value = opts.w.globals.series[opts.seriesIndex];
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${seriesName}: ${value} (${percentage}%)`;
                        }
                    },
                    title: {
                        text: `${title} Disposition Distribution`,
                        align: 'left',
                        margin: 10,
                        offsetY: 0,
                        style: {
                            fontSize: '18px',
                            fontWeight: 700,
                            color: '#2d3748'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            // Show percentage in the slice
                            return `${val.toFixed(1)}%`;
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: 700,
                            colors: ['#fff']
                        },
                        dropShadow: {
                            enabled: true,
                            top: 1,
                            left: 1,
                            blur: 2,
                            color: '#000',
                            opacity: 0.45
                        }
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: true,
                            customScale: 1,
                            offsetX: 0,
                            offsetY: 0,
                            dataLabels: {
                                offset: 0,
                                minAngleToShowLabel: 10
                            }
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    tooltip: {
                        enabled: true,
                        theme: 'light',
                        fillSeriesColor: false,
                        style: {
                            fontSize: '14px'
                        },
                        onDatasetHover: {
                            highlightDataSeries: true
                        },
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const label = w.globals.labels[seriesIndex];
                            const value = series[seriesIndex];
                            const percentage = ((value / total) * 100).toFixed(1);
                            const color = w.config.colors[seriesIndex];

                            return `
                    <div style="padding: 12px 16px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px; font-family: inherit;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                            <div style="width: 16px; height: 16px; border-radius: 4px; background-color: ${color}; margin-right: 10px;"></div>
                            <span style="font-weight: 700; font-size: 15px; color: #2d3748;">${label}</span>
                        </div>
                        <div style="font-size: 13px; color: #4a5568;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span>Total Count:</span>
                                <strong style="color: #2d3748;">${value}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Percentage:</span>
                                <strong style="color: #2d3748;">${percentage}%</strong>
                            </div>
                        </div>
                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #718096;">
                            Total Items: ${total}
                        </div>
                    </div>
                `;
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'lighten',
                                value: 0.15
                            }
                        },
                        active: {
                            allowMultipleDataPointsSelection: false,
                            filter: {
                                type: 'darken',
                                value: 0.35
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 350
                            },
                            legend: {
                                position: 'bottom',
                                fontSize: '12px'
                            },
                            dataLabels: {
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        }
                    }, {
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 300
                            },
                            legend: {
                                position: 'bottom',
                                fontSize: '11px'
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    // Only show percentage on mobile
                                    return val > 5 ? `${val.toFixed(0)}%` : '';
                                }
                            }
                        }
                    }]
                };

                $(selector).html('');
                chartInstances[selector] = new ApexCharts(document.querySelector(selector), options);
                chartInstances[selector].render();
            }

            // Initialize dashboard
            function initializeDashboard() {
                console.log('Initializing dashboard...'); // Debug log

                showLoading('#chart-pelarutan1');
                showLoading('#chart-pelarutan2');
                showLoading('#disposition-pelarutan1');
                showLoading('#disposition-pelarutan2');

                fetchChartData('pelarutan1');
                fetchChartData('pelarutan2');
                fetchDispositionData('pelarutan1');
                fetchDispositionData('pelarutan2');
            }

            // Event handlers for filters
            $('#filter_pelarutan1').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="ri-loader-4-line spin me-1"></i>Loading...').prop('disabled', true);

                showLoading('#chart-pelarutan1');

                const start = $('#start_date_pelarutan1').val();
                const end = $('#end_date_pelarutan1').val();
                const variant = $('#variant_pelarutan1').val();

                fetchChartData('pelarutan1', start, end, variant).always(() => {
                    $btn.html(originalText).prop('disabled', false);
                });
            });

            $('#filter_pelarutan2').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="ri-loader-4-line spin me-1"></i>Loading...').prop('disabled', true);

                showLoading('#chart-pelarutan2');

                const start = $('#start_date_pelarutan2').val();
                const end = $('#end_date_pelarutan2').val();
                const variant = $('#variant_pelarutan2').val();

                fetchChartData('pelarutan2', start, end, variant).always(() => {
                    $btn.html(originalText).prop('disabled', false);
                });
            });

            $('#filter_pelarutan1_disposisi').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="ri-loader-4-line spin me-1"></i>Loading...').prop('disabled', true);

                showLoading('#disposition-pelarutan1');

                const start = $('#start_date_pelarutan1_disposisi').val();
                const end = $('#end_date_pelarutan1_disposisi').val();
                const variant = $('#variant_pelarutan1_disposisi').val();

                fetchDispositionData('pelarutan1', start, end, variant).always(() => {
                    $btn.html(originalText).prop('disabled', false);
                });
            });

            $('#filter_pelarutan2_disposisi').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="ri-loader-4-line spin me-1"></i>Loading...').prop('disabled', true);

                showLoading('#disposition-pelarutan2');

                const start = $('#start_date_pelarutan2_disposisi').val();
                const end = $('#end_date_pelarutan2_disposisi').val();
                const variant = $('#variant_pelarutan2_disposisi').val();

                fetchDispositionData('pelarutan2', start, end, variant).always(() => {
                    $btn.html(originalText).prop('disabled', false);
                });
            });

            // Initialize the dashboard when document is ready
            initializeDashboard();
        });
    </script>
@endsection
