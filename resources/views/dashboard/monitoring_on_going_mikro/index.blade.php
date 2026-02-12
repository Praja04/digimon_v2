@extends('layouts.component.main')
@section('title', 'Dashboard - Monitoring On Going Mikro')

@section('styles')
    <!-- Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        /* Card animations */
        .card-animate {
            transition: all 0.3s ease;
        }

        .card-animate:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Counter animation styling */
        .counter-value {
            font-weight: 600;
            color: #495057;
        }

        /* Table improvements */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Status badges */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }

        /* ============================================ */
        /* SELECT2 STYLING - DISESUAIKAN DENGAN VELZON */
        /* ============================================ */

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px !important;
            height: auto !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple {
            padding: 0.25rem 0.5rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #405189 !important;
            border: 1px solid #364574 !important;
            color: white !important;
            padding: 0.15rem 0.5rem !important;
            margin: 0.15rem 0.25rem 0.15rem 0 !important;
            font-size: 0.8125rem !important;
            line-height: 1.5 !important;
            border-radius: 0.25rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 0.35rem !important;
            font-size: 0.875rem !important;
            font-weight: bold !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fef !important;
            background-color: transparent !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
            margin-top: 0.25rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            font-size: 0.875rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #405189 !important;
            color: white !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #e3f2fd !important;
            color: #1976d2 !important;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
            border-color: #405189 !important;
            box-shadow: 0 0 0 0.2rem rgba(64, 81, 137, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear {
            font-size: 1rem !important;
            margin-right: 0.5rem !important;
            color: #878a99 !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear:hover {
            color: #405189 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #405189 !important;
            box-shadow: 0 0 0 0.2rem rgba(64, 81, 137, 0.25) !important;
        }

        /* Chart container improvements */
        .card-body .apexcharts-canvas {
            margin: 0 auto;
        }

        /* Summary cards custom colors */
        .card-animate .text-success {
            color: #10b981 !important;
        }

        .card-animate .text-info {
            color: #3b82f6 !important;
        }

        .card-animate .text-warning {
            color: #f59e0b !important;
        }

        .card-animate .text-danger {
            color: #ef4444 !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
                font-size: 0.75rem !important;
                padding: 0.1rem 0.4rem !important;
            }
        }

        /* Filter form styling */
        #filterForm {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
        }

        #filterForm .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        /* Card header styling */
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #374151;
        }

        /* Button hover effects */
        .btn-primary {
            background-color: #405189;
            border-color: #405189;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #364574;
            border-color: #364574;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(64, 81, 137, 0.3);
        }

        /* Table striped custom */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
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

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="filterForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="weekSelect" class="form-label">Week</label>
                                        <select class="form-select" id="weekSelect" name="week">
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="variantSelect" class="form-label">Variant (Multiple Select)</label>
                                        <select class="form-select" id="variantSelect" name="variants[]" multiple>
                                            @foreach ($variants as $variant)
                                                <option value="{{ $variant }}">{{ $variant }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-filter-3-line"></i> Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row" id="summaryCards">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Weekly Summary Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Weekly Summary Result</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variant</th>
                                            <th class="text-center">Total Sample</th>
                                            <th class="text-center">TPC Max</th>
                                            <th class="text-center">YM Max</th>
                                            <th class="text-center">EB Max</th>
                                            <th class="text-center">NG Count</th>
                                            <th class="text-center">% NG</th>
                                        </tr>
                                    </thead>
                                    <tbody id="weeklySummaryTable">
                                        <tr>
                                            <td colspan="7" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Day-to-Day Review (by Filling Date)</h5>
                        </div>
                        <div class="card-body">
                            <div id="dayToDayFillingChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Day-to-Day Review (by Production Date)</h5>
                        </div>
                        <div class="card-body">
                            <div id="dayToDayProductionChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">% NG FG (Week-by-Week)</h5>
                        </div>
                        <div class="card-body">
                            <div id="ngWeekByWeekChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">% NG Per Variant FG (Week-by-Week)</h5>
                        </div>
                        <div class="card-body">
                            <div id="ngPerVariantChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 3 - Histograms -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Histogram TPC</h5>
                        </div>
                        <div class="card-body">
                            <div id="histogramTpcChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Histogram YM</h5>
                        </div>
                        <div class="card-body">
                            <div id="histogramYmChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 4 -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Frek Micro + & NG by Sample Type</h5>
                        </div>
                        <div class="card-body">
                            <div id="frekMicroSampleChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Filling Machine Review</h5>
                        </div>
                        <div class="card-body">
                            <div id="fillingMachineReviewChart"></div>
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
        // Global chart instances
        let charts = {
            dayToDayFilling: null,
            dayToDayProduction: null,
            ngWeekByWeek: null,
            ngPerVariant: null,
            histogramTpc: null,
            histogramYm: null,
            frekMicroSample: null,
            fillingMachineReview: null
        };

        // Initialize on document ready
        $(document).ready(function() {
            // Initialize Select2
            $('#variantSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select variants',
                allowClear: true,
                width: '100%'
            });

            // Load weeks first
            loadWeeks();

            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadDashboardData();
            });
        });

        // Load available weeks
        function loadWeeks() {
            $.ajax({
                url: '/api/dashboard/monitoring-on-going-mikro/weeks',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let options = '';
                        response.data.forEach((week, index) => {
                            const selected = index === 0 ? 'selected' : '';
                            options +=
                                `<option value="${week.value}" ${selected}>Week ${week.week_number} (${week.date_range})</option>`;
                        });
                        $('#weekSelect').html(options);

                        // Load initial data after weeks are loaded
                        loadDashboardData();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading weeks:', xhr);
                    alert('Error loading weeks. Please refresh the page.');
                }
            });
        }

        // Load dashboard data
        function loadDashboardData() {
            const week = $('#weekSelect').val();
            const variants = $('#variantSelect').val() || [];

            $.ajax({
                url: '/api/dashboard/monitoring-on-going-mikro/data',
                method: 'GET',
                data: {
                    week: week,
                    variants: variants
                },
                success: function(response) {
                    if (response.success) {
                        updateSummaryCards(response.data.summary);
                        updateWeeklySummaryTable(response.data.weeklySummary);
                        updateCharts(response.data.chartData);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading dashboard data:', xhr);
                }
            });
        }

        // Update summary cards
        function updateSummaryCards(summary) {
            const html = `
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Total Sample</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-flask-line align-middle"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                        <span class="counter-value" data-target="${summary.total_sample}">0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">TPC Max</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-info fs-14 mb-0">
                                        <i class="ri-microscope-line align-middle"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                        ${formatNumber(summary.tpc_max)}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">YM Max</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-warning fs-14 mb-0">
                                        <i class="ri-bacteria-line align-middle"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                        ${formatNumber(summary.ym_max)}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">% NG</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-danger fs-14 mb-0">
                                        <i class="ri-error-warning-line align-middle"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                        ${summary.ng_percentage.toFixed(2)}%
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#summaryCards').html(html);

            // Animate counter
            $('.counter-value').each(function() {
                $(this).prop('Counter', 0).animate({
                    Counter: $(this).data('target')
                }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function(now) {
                        $(this).text(Math.ceil(now).toLocaleString());
                    }
                });
            });
        }

        // Update weekly summary table
        function updateWeeklySummaryTable(weeklySummary) {
            let html = '';

            if (weeklySummary.length === 0) {
                html = '<tr><td colspan="7" class="text-center">No data available</td></tr>';
            } else {
                weeklySummary.forEach(item => {
                    const badgeClass = item.ng_percentage > 5 ? 'bg-danger' : 'bg-success';
                    html += `
                        <tr>
                            <td><strong>${item.variant}</strong></td>
                            <td class="text-center">${item.total_sample}</td>
                            <td class="text-center">${formatNumber(item.tpc_max)}</td>
                            <td class="text-center">${formatNumber(item.ym_max)}</td>
                            <td class="text-center">${formatNumber(item.eb_max)}</td>
                            <td class="text-center">
                                <span class="badge bg-danger">${item.ng_count}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${badgeClass}">
                                    ${item.ng_percentage.toFixed(2)}%
                                </span>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#weeklySummaryTable').html(html);
        }

        // Update all charts
        function updateCharts(chartData) {
            updateDayToDayFillingChart(chartData.dayToDayFilling);
            updateDayToDayProductionChart(chartData.dayToDayProduction);
            updateNgWeekByWeekChart(chartData.ngWeekByWeek);
            updateNgPerVariantChart(chartData.ngPerVariant);
            updateHistogramTpcChart(chartData.histogramTpc);
            updateHistogramYmChart(chartData.histogramYm);
            updateFrekMicroSampleChart(chartData.frekMicroSample);
            updateFillingMachineReviewChart(chartData.fillingMachineReview);
        }

        // Helper function to format number
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Chart update functions
        function updateDayToDayFillingChart(data) {
            const options = {
                series: [{
                    name: 'Frek Micro +',
                    data: data.frekMicro
                }, {
                    name: 'Frek NG',
                    data: data.frekNG
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#3b82f6', '#ef4444'],
                legend: {
                    position: 'top',
                }
            };

            if (charts.dayToDayFilling) {
                charts.dayToDayFilling.destroy();
            }
            charts.dayToDayFilling = new ApexCharts(document.querySelector("#dayToDayFillingChart"), options);
            charts.dayToDayFilling.render();
        }

        function updateDayToDayProductionChart(data) {
            const options = {
                series: [{
                    name: 'Frek Micro +',
                    data: data.frekMicro
                }, {
                    name: 'Frek NG',
                    data: data.frekNG
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#3b82f6', '#ef4444'],
                legend: {
                    position: 'top',
                }
            };

            if (charts.dayToDayProduction) {
                charts.dayToDayProduction.destroy();
            }
            charts.dayToDayProduction = new ApexCharts(document.querySelector("#dayToDayProductionChart"), options);
            charts.dayToDayProduction.render();
        }

        function updateNgWeekByWeekChart(data) {
            const options = {
                series: [{
                    name: '% NG',
                    data: data.data
                }],
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    },
                    labels: {
                        formatter: function(value) {
                            return value.toFixed(2) + '%';
                        }
                    }
                },
                colors: ['#3b82f6'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#3b82f6'],
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                }
            };

            if (charts.ngWeekByWeek) {
                charts.ngWeekByWeek.destroy();
            }
            charts.ngWeekByWeek = new ApexCharts(document.querySelector("#ngWeekByWeekChart"), options);
            charts.ngWeekByWeek.render();
        }

        function updateNgPerVariantChart(data) {
            const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
            const series = data.variants.map(variant => ({
                name: variant.name,
                data: variant.data
            }));

            const options = {
                series: series,
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    },
                    labels: {
                        formatter: function(value) {
                            return value.toFixed(2) + '%';
                        }
                    }
                },
                colors: colors,
                legend: {
                    position: 'top',
                },
                markers: {
                    size: 4,
                    hover: {
                        size: 6
                    }
                }
            };

            if (charts.ngPerVariant) {
                charts.ngPerVariant.destroy();
            }
            charts.ngPerVariant = new ApexCharts(document.querySelector("#ngPerVariantChart"), options);
            charts.ngPerVariant.render();
        }

        function updateHistogramTpcChart(data) {
            const options = {
                series: [{
                    name: 'Frequency',
                    data: data.data
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                    title: {
                        text: 'TPC Range'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#3b82f6']
            };

            if (charts.histogramTpc) {
                charts.histogramTpc.destroy();
            }
            charts.histogramTpc = new ApexCharts(document.querySelector("#histogramTpcChart"), options);
            charts.histogramTpc.render();
        }

        function updateHistogramYmChart(data) {
            const options = {
                series: [{
                    name: 'Frequency',
                    data: data.data
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                    title: {
                        text: 'YM Range'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#10b981']
            };

            if (charts.histogramYm) {
                charts.histogramYm.destroy();
            }
            charts.histogramYm = new ApexCharts(document.querySelector("#histogramYmChart"), options);
            charts.histogramYm.render();
        }

        function updateFrekMicroSampleChart(data) {
            const options = {
                series: [{
                    name: 'Frek Micro +',
                    data: data.frekMicro
                }, {
                    name: 'Frek NG',
                    data: data.frekNG
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#3b82f6', '#ef4444'],
                legend: {
                    position: 'top',
                }
            };

            if (charts.frekMicroSample) {
                charts.frekMicroSample.destroy();
            }
            charts.frekMicroSample = new ApexCharts(document.querySelector("#frekMicroSampleChart"), options);
            charts.frekMicroSample.render();
        }

        function updateFillingMachineReviewChart(data) {
            const options = {
                series: [{
                    name: 'Frek Micro +',
                    data: data.frekMicro
                }, {
                    name: 'Frek NG',
                    data: data.frekNG
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.labels,
                },
                yaxis: {
                    title: {
                        text: 'Frequency'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#3b82f6', '#ef4444'],
                legend: {
                    position: 'top',
                }
            };

            if (charts.fillingMachineReview) {
                charts.fillingMachineReview.destroy();
            }
            charts.fillingMachineReview = new ApexCharts(document.querySelector("#fillingMachineReviewChart"), options);
            charts.fillingMachineReview.render();
        }
    </script>
@endsection
