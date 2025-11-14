@extends('layouts.component.main')
@section('title', 'Dashboard - Analisis Blending Awal')
@section('styles')
    <!-- Custom CSS for enhanced styling -->
    <style>
        .bg-gradient-blending {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75);
        }

        .text-purple {
            color: #6f42c1;
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .bg-soft-primary {
            background-color: rgba(102, 126, 234, 0.1);
        }

        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .bg-soft-purple {
            background-color: rgba(111, 66, 193, 0.1);
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

            <!-- Advanced Filter Section -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-dark fw-bold">
                                        <i class="ri-filter-3-line text-primary me-2"></i>Advanced Filters
                                    </h5>
                                    <p class="text-muted mb-0 small">Filter data by date range and variant to analyze
                                        specific
                                        periods</p>
                                </div>
                                <div class="badge bg-soft-primary text-primary px-3 py-2">
                                    <i class="ri-calendar-check-line me-1"></i>Advanced Filter
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label fw-semibold">
                                        <i class="ri-calendar-line me-1"></i>Start Date
                                    </label>
                                    <input type="date" id="start_date" class="form-control form-control-lg">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label fw-semibold">
                                        <i class="ri-calendar-line me-1"></i>End Date
                                    </label>
                                    <input type="date" id="end_date" class="form-control form-control-lg">
                                </div>
                                <div class="col-md-3">
                                    <label for="variant" class="form-label fw-semibold">
                                        <i class="ri-flask-line me-1"></i>Variant
                                    </label>
                                    <select id="variant" class="form-select form-select-lg">
                                        <option value="">-- All Variants --</option>
                                        <option value="SS1">SS1</option>
                                        <option value="SS2">SS2</option>
                                        <option value="BB">BB</option>
                                        <option value="MSD NR1">MSD NR1</option>
                                        <option value="MSD NR2">MSD NR2</option>
                                        <option value="JB">JB</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-secondary btn-lg flex-fill" id="reset-filter">
                                            <i class="ri-refresh-line me-1"></i>Reset
                                        </button>
                                        <button class="btn btn-primary btn-lg flex-fill" id="filter-data">
                                            <i class="ri-search-line me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disposition Analysis Section -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="card border-0 shadow-lg position-relative">
                        <div class="position-absolute top-0 start-0 w-100 bg-gradient-warning rounded-top"
                            style="height: 4px;">
                        </div>
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="mb-1 text-dark fw-bold">
                                        <i class="ri-bar-chart-box-line text-warning me-2"></i>Disposition Analysis
                                    </h5>
                                    <p class="text-muted mb-0 small">Distribution breakdown by disposition types</p>
                                </div>
                                <div class="badge bg-soft-warning text-warning px-3 py-2">
                                    <i class="ri-pie-chart-line me-1"></i>Summary View
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div id="chart-disposition-blending" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parameter Analysis Section -->
            <div class="row g-4">
                <div class="col-12 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 text-dark fw-bold">
                                <i class="ri-line-chart-line text-success me-2"></i>Parameter Trend Analysis
                            </h5>
                            <p class="text-muted mb-0">Detailed analysis of blending parameters over time</p>
                        </div>
                        <div class="badge bg-soft-success text-success px-3 py-2">
                            <i class="ri-trending-up-line me-1"></i>Live Monitoring
                        </div>
                    </div>
                    <hr class="my-3">
                </div>

                @php
                    $parameters = [
                        'brix' => ['name' => 'Brix', 'icon' => 'ri-drop-line', 'color' => 'primary'],
                        'nacl' => ['name' => 'NaCl', 'icon' => 'ri-contrast-drop-line', 'color' => 'info'],
                        'bj' => ['name' => 'BJ', 'icon' => 'ri-test-tube-line', 'color' => 'success'],
                        'visco' => ['name' => 'Viscosity', 'icon' => 'ri-water-percent-line', 'color' => 'warning'],
                        'aw' => ['name' => 'AW', 'icon' => 'ri-reactjs-line', 'color' => 'danger'],
                        'buih' => ['name' => 'Buih', 'icon' => 'ri-bubble-chart-line', 'color' => 'secondary'],
                        'organo' => ['name' => 'Organo', 'icon' => 'ri-flask-line', 'color' => 'dark'],
                        'ph' => ['name' => 'pH', 'icon' => 'ri-equalizer-line', 'color' => 'purple'],
                    ];
                @endphp

                @foreach ($parameters as $param => $config)
                    <div class="col-xl-6 mb-4">
                        <div class="card border-0 shadow-lg h-100 position-relative">
                            <div class="position-absolute top-0 start-0 w-100 bg-gradient-{{ $config['color'] }} rounded-top"
                                style="height: 4px;"></div>
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-1 text-dark fw-bold">
                                            <i
                                                class="{{ $config['icon'] }} text-{{ $config['color'] }} me-2"></i>{{ $config['name'] }}
                                            Analysis
                                        </h6>
                                        <p class="text-muted mb-0 small">Monitoring {{ strtolower($config['name']) }}
                                            parameter
                                            trends</p>
                                    </div>
                                    <div
                                        class="badge bg-soft-{{ $config['color'] }} text-{{ $config['color'] }} px-2 py-1 small">
                                        {{ strtoupper($param) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-3">
                                <div id="chart-{{ $param }}" class="apex-charts"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Enhanced JavaScript with loading states -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const $start = document.getElementById("start_date");
            const $end = document.getElementById("end_date");
            const $variant = document.getElementById("variant");
            const $btnFilter = document.getElementById("filter-data");
            const $btnReset = document.getElementById("reset-filter");

            let chartDisposition = null;
            let paramCharts = {};

            // Loading spinner function
            function showLoading(selector) {
                const el = document.querySelector(selector);
                if (el) {
                    el.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
                }
            }

            // Get URL parameters
            function getParams() {
                const params = {};
                if ($start.value) params.start_date = $start.value;
                if ($end.value) params.end_date = $end.value;
                if ($variant.value) params.variant = $variant.value;
                return new URLSearchParams(params).toString();
            }

            // Load Disposition Chart
            async function loadDisposition() {
                const container = document.querySelector("#chart-disposition-blending");
                showLoading("#chart-disposition-blending");

                try {
                    const url = "{{ route('api.dashboard.blending-awal.analisa.disposisi') }}?" + getParams();
                    console.log('Fetching disposition from:', url);

                    const res = await fetch(url);

                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }

                    const data = await res.json();
                    console.log('Disposition response:', data);

                    const dispositionData = data.disposition_summary || {};
                    const labels = Object.keys(dispositionData);
                    const series = Object.values(dispositionData);

                    if (series.length === 0 || series.every(v => v === 0)) {
                        if (chartDisposition) {
                            chartDisposition.destroy();
                            chartDisposition = null;
                        }
                        container.innerHTML = `
                    <div class="alert alert-info d-flex align-items-center justify-content-center" role="alert" style="height:300px;">
                        <i class="ri-information-line me-2"></i>
                        <div>No disposition data available for the selected filters.</div>
                    </div>
                `;
                        return;
                    }

                    // Destroy existing chart
                    if (chartDisposition) {
                        chartDisposition.destroy();
                        chartDisposition = null;
                    }

                    // Color mapping for disposition types
                    const colorMap = {
                        'Release': '#28a745',
                        'RELEASE': '#28a745',
                        'Reject': '#dc3545',
                        'REJECT': '#dc3545',
                        'Hold': '#ffc107',
                        'HOLD': '#ffc107',
                        'Adjustment': '#17a2b8',
                        'ADJUSTMENT': '#17a2b8',
                        'Resampling': '#6f42c1',
                        'RESAMPLING': '#6f42c1',
                        'Pending': '#6c757d',
                        'PENDING': '#6c757d'
                    };

                    const colors = labels.map(label => colorMap[label] || '#007bff');
                    const total = series.reduce((a, b) => a + b, 0);

                    const options = {
                        series: series,
                        chart: {
                            type: "donut",
                            height: 380,
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 1000,
                                animateGradually: {
                                    enabled: true,
                                    delay: 200
                                },
                                dynamicAnimation: {
                                    enabled: true,
                                    speed: 500
                                }
                            },
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true
                                }
                            }
                        },
                        labels: labels,
                        colors: colors,
                        legend: {
                            position: "bottom",
                            horizontalAlign: 'center',
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
                            text: 'Disposition Distribution',
                            align: 'left',
                            margin: 10,
                            style: {
                                fontSize: '18px',
                                fontWeight: 700,
                                color: '#2d3748'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
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
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Total Batches',
                                            fontSize: '16px',
                                            fontWeight: 600,
                                            color: '#2d3748',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b,
                                                0);
                                            }
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '24px',
                                            fontWeight: 700,
                                            color: '#2d3748'
                                        }
                                    }
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
                            custom: function({
                                series,
                                seriesIndex,
                                w
                            }) {
                                const label = w.globals.labels[seriesIndex];
                                const value = series[seriesIndex];
                                const percentage = ((value / total) * 100).toFixed(1);
                                const color = w.config.colors[seriesIndex];

                                return `
                            <div style="padding: 12px 16px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
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
                                    Total Batches: ${total}
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
                                }
                            }
                        }]
                    };

                    container.innerHTML = "";
                    chartDisposition = new ApexCharts(container, options);
                    chartDisposition.render();

                } catch (err) {
                    console.error("Error loading disposition:", err);
                    container.innerHTML = `
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    <div>Error loading disposition data. Please try again.</div>
                </div>
            `;
                }
            }

            // Load Parameter Charts
            async function loadParameters() {
                const params = ["brix", "nacl", "bj", "visco", "aw", "buih", "organo", "ph"];

                // Show loading for all charts
                params.forEach(param => {
                    showLoading(`#chart-${param}`);
                });

                try {
                    const url = "{{ route('api.dashboard.blending-awal.analisa') }}?" + getParams();
                    console.log('Fetching parameters from:', url);

                    const res = await fetch(url);

                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }

                    const data = await res.json();
                    console.log('Parameters response:', data);

                    const blendingAwal = data.blending_awal || [];

                    // Sort by created_at
                    blendingAwal.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                    params.forEach(param => {
                        const el = document.querySelector(`#chart-${param}`);
                        if (!el) return;

                        const validData = blendingAwal.filter(item => item[param] != null && item[
                            param] !== '');

                        if (validData.length === 0) {
                            if (paramCharts[param]) {
                                paramCharts[param].destroy();
                                paramCharts[param] = null;
                            }
                            el.innerHTML = `
                        <div class="alert alert-info d-flex align-items-center justify-content-center" role="alert" style="height:280px;">
                            <i class="ri-information-line me-2"></i>
                            <div>No data available for ${param.toUpperCase()} parameter.</div>
                        </div>
                    `;
                            return;
                        }

                        const seriesData = validData.map(item => ({
                            x: new Date(item.created_at).getTime(),
                            y: parseFloat(item[param]),
                            meta: item
                        }));

                        // Destroy existing chart
                        if (paramCharts[param]) {
                            paramCharts[param].destroy();
                            paramCharts[param] = null;
                        }

                        const options = {
                            series: [{
                                name: param.toUpperCase(),
                                data: seriesData.map(d => [d.x, d.y])
                            }],
                            chart: {
                                type: "line",
                                height: 280,
                                zoom: {
                                    enabled: true,
                                    type: "x"
                                },
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: true,
                                        zoom: true,
                                        zoomin: true,
                                        zoomout: true,
                                        pan: true,
                                        reset: true
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            stroke: {
                                curve: "smooth",
                                width: 3
                            },
                            xaxis: {
                                type: "datetime",
                                labels: {
                                    datetimeFormatter: {
                                        year: 'yyyy',
                                        month: 'MMM yyyy',
                                        day: 'dd MMM',
                                        hour: 'HH:mm'
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    formatter: val => val != null ? val.toFixed(2) : "-"
                                },
                                title: {
                                    text: param.toUpperCase(),
                                    style: {
                                        fontWeight: 600
                                    }
                                }
                            },
                            markers: {
                                size: 5,
                                hover: {
                                    size: 7
                                }
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                                strokeDashArray: 3
                            },
                            tooltip: {
                                x: {
                                    format: "dd MMM yyyy HH:mm"
                                },
                                custom: function({
                                    series,
                                    seriesIndex,
                                    dataPointIndex
                                }) {
                                    const item = validData[dataPointIndex];
                                    if (!item) return '';

                                    const value = series[seriesIndex][dataPointIndex];
                                    const date = new Date(item.created_at).toLocaleString(
                                        "id-ID");

                                    return `
                                <div class="p-3 bg-white shadow-lg border-0 rounded" style="min-width: 220px;">
                                    <div class="fw-bold text-dark mb-2 pb-2 border-bottom">
                                        ${param.toUpperCase()}: <span class="text-primary">${value.toFixed(2)}</span>
                                    </div>
                                    <div class="small text-muted">
                                        <div class="mb-1"><strong>Variant:</strong> ${item.variant || "-"}</div>
                                        <div class="mb-1"><strong>PO:</strong> ${item.po_number || "-"}</div>
                                        <div class="mb-1"><strong>Batch:</strong> ${item.batch_range || "-"}</div>
                                        <div class="mb-1"><strong>Disposition:</strong> 
                                            <span class="badge bg-${item.disposition === 'Release' ? 'success' : item.disposition === 'Reject' ? 'danger' : 'warning'} badge-sm">
                                                ${item.disposition || "-"}
                                            </span>
                                        </div>
                                        <div class="mt-2 pt-2 border-top"><strong>Date:</strong> ${date}</div>
                                    </div>
                                </div>
                            `;
                                }
                            }
                        };

                        el.innerHTML = "";
                        paramCharts[param] = new ApexCharts(el, options);
                        paramCharts[param].render();
                    });

                } catch (err) {
                    console.error("Error loading parameters:", err);
                    params.forEach(param => {
                        const el = document.querySelector(`#chart-${param}`);
                        if (el) {
                            el.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="ri-error-warning-line me-2"></i>
                            <div>Error loading ${param.toUpperCase()} data.</div>
                        </div>
                    `;
                        }
                    });
                }
            }

            // Load all charts
            function loadAll() {
                console.log('Loading all charts...');
                loadDisposition();
                loadParameters();
            }

            // Event Listeners
            $btnFilter.addEventListener("click", function() {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ri-loader-4-line spin me-1"></i>Loading...';
                btn.disabled = true;

                loadAll();

                // Re-enable button after loading
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            });

            $btnReset.addEventListener("click", function() {
                $start.value = "";
                $end.value = "";
                $variant.value = "";

                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ri-loader-4-line spin me-1"></i>Resetting...';
                btn.disabled = true;

                loadAll();

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            });

            // Initial load
            loadAll();
        });
    </script>
@endsection
