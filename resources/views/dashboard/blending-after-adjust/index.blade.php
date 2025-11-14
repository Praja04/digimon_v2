@extends('layouts.component.main')
@section('title', 'Dashboard - Analisis Blending After Adjust')
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

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .apex-charts {
            min-height: 320px;
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
                                        specific periods</p>
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
                            style="height: 4px;"></div>
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="mb-1 text-dark fw-bold">
                                        <i class="ri-bar-chart-box-line text-warning me-2"></i>Hasil Analysis After
                                        Adjustment
                                    </h5>
                                    <p class="text-muted mb-0 small">Distribution breakdown by hasil types after adjustment
                                    </p>
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
                                <i class="ri-line-chart-line text-success me-2"></i>Parameter Trend Analysis After
                                Adjustment
                            </h5>
                            <p class="text-muted mb-0">Detailed analysis of blending parameters after adjustment over time
                            </p>
                        </div>
                        <div class="badge bg-soft-success text-success px-3 py-2">
                            <i class="ri-trending-up-line me-1"></i>Live Monitoring
                        </div>
                    </div>
                    <hr class="my-3">
                </div>

                @php
                    $parameters = [
                        'volume' => ['name' => 'Volume', 'icon' => 'ri-water-flash-line', 'color' => 'primary'],
                        'eb' => ['name' => 'EB', 'icon' => 'ri-microscope-line', 'color' => 'info'],
                        'tpc' => ['name' => 'TPC', 'icon' => 'ri-test-tube-line', 'color' => 'success'],
                        'ym' => ['name' => 'YM', 'icon' => 'ri-virus-line', 'color' => 'danger'],
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
                                            Analysis (After Adjust)
                                        </h6>
                                        <p class="text-muted mb-0 small">Monitoring {{ strtolower($config['name']) }}
                                            parameter trends after adjustment</p>
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

            function getParams() {
                const params = {};
                if ($start.value) params.start_date = $start.value;
                if ($end.value) params.end_date = $end.value;
                if ($variant.value) params.variant = $variant.value;
                return new URLSearchParams(params).toString();
            }

            async function loadDisposition() {
                try {
                    const res = await fetch(
                        "{{ route('api.dashboard.blending-after-adjust.analisa.disposisi') }}?" +
                        getParams());
                    const data = await res.json();
                    const labels = Object.keys(data.disposition_summary || {});
                    const series = Object.values(data.disposition_summary || {});
                    const container = document.querySelector("#chart-disposition-blending");

                    if (series.length === 0) {
                        if (chartDisposition) {
                            chartDisposition.destroy();
                            chartDisposition = null;
                        }
                        container.innerHTML =
                            `<div class="text-center text-muted py-5">Tidak ada data untuk ditampilkan</div>`;
                        return;
                    }

                    const options = {
                        series: series,
                        chart: {
                            type: "donut",
                            height: 320,
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
                            }
                        },
                        labels: labels,
                        legend: {
                            position: "bottom"
                        },
                        colors: ["#28a745", "#dc3545", "#ffc107", "#17a2b8", "#6f42c1"],
                        dataLabels: {
                            enabled: true
                        }
                    };

                    container.innerHTML = "";
                    chartDisposition = new ApexCharts(container, options);
                    chartDisposition.render();
                } catch (err) {
                    console.error("Error loading disposition:", err);
                }
            }

            async function loadParameters() {
                try {
                    const res = await fetch(
                        "{{ route('api.dashboard.blending-after-adjust.analisa') }}?" +
                        getParams());
                    const data = await res.json();
                    const blendingData = data.blending_after_adjust || [];

                    blendingData.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                    const params = ["volume", "eb", "tpc", "ym"];

                    params.forEach(param => {
                        const el = document.querySelector(`#chart-${param}`);
                        if (!el) return;

                        const validData = blendingData.filter(item => {
                            const val = item[param];
                            return val !== null && val !== undefined && val !== "";
                        });

                        const seriesData = validData.map(item => [
                            new Date(item.created_at).getTime(),
                            parseFloat(item[param])
                        ]);

                        if (seriesData.length === 0) {
                            if (paramCharts[param]) {
                                paramCharts[param].destroy();
                                paramCharts[param] = null;
                            }
                            el.innerHTML =
                                `<div class="text-center text-muted py-5">Tidak ada data untuk parameter ${param.toUpperCase()}</div>`;
                            return;
                        }

                        const options = {
                            series: [{
                                name: param.toUpperCase(),
                                data: seriesData
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
                            stroke: {
                                curve: "smooth",
                                width: 3
                            },
                            xaxis: {
                                type: "datetime"
                            },
                            yaxis: {
                                labels: {
                                    formatter: val => val != null ? val.toFixed(2) : "-"
                                }
                            },
                            markers: {
                                size: 4
                            },
                            tooltip: {
                                x: {
                                    format: "dd MMM yyyy HH:mm"
                                },
                                y: {
                                    formatter: val => val != null ? val.toFixed(2) : "-"
                                },
                                custom: function({
                                    series,
                                    seriesIndex,
                                    dataPointIndex
                                }) {
                                    const item = validData[dataPointIndex];
                                    return item ? `
                            <div class="p-2">
                                <strong>${param.toUpperCase()}: ${series[seriesIndex][dataPointIndex]}</strong><br/>
                                Variant: ${item.variant || "-"}<br/>
                                PO: ${item.po_number || "-"}<br/>
                                Hasil: ${item.hasil || "-"}<br/>
                                Date: ${new Date(item.created_at).toLocaleString("id-ID")}
                            </div>
                        ` : "";
                                }
                            }
                        };

                        el.innerHTML = "";
                        paramCharts[param] = new ApexCharts(el, options);
                        paramCharts[param].render();
                    });
                } catch (err) {
                    console.error("Error loading parameters:", err);
                }
            }

            function loadAll() {
                loadDisposition();
                loadParameters();
            }

            $btnFilter.addEventListener("click", loadAll);
            $btnReset.addEventListener("click", () => {
                $start.value = "";
                $end.value = "";
                $variant.value = "";
                loadAll();
            });

            loadAll();
        });
    </script>
@endsection
