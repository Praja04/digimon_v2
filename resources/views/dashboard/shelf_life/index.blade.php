@extends('layouts.component.main')
@section('title', 'Dashboard - Shelf Life')

@section('styles')
    <style>
        .chart-container {
            position: relative;
            height: 320px;
            margin-bottom: 20px;
        }

        .card-chart {
            border-radius: 12px;
            border: 1px solid #e8e8e8;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card-chart:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .chart-title {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .filter-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid #e8e8e8;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .filter-header {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-header {
            background: #f8f9fa;
            color: #2c3e50;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            margin-top: 32px;
            border-left: 4px solid #5a67d8;
        }

        .section-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .section-header .section-count {
            font-size: 13px;
            color: #7f8c8d;
            font-weight: 400;
            margin-left: 8px;
        }

        .btn-reset {
            background: #ffffff;
            border: 1px solid #dee2e6;
            color: #495057;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #212529;
        }

        .btn-apply {
            background: #5a67d8;
            border: 1px solid #5a67d8;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .btn-apply:hover {
            background: #4c51bf;
            border-color: #4c51bf;
            color: #ffffff;
        }

        .data-info-panel {
            background: #5a67d8;
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .data-info-panel .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .data-info-panel .info-label {
            font-size: 13px;
            opacity: 0.9;
        }

        .data-info-panel .info-value {
            font-size: 16px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .page-title-box h4 {
            font-weight: 600;
            color: #2c3e50;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 24px;
        }

        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 280px;
            }
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
                                <li class="breadcrumb-item"><a href="{{ route('shelf-life.index') }}">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Filter Section -->
            <div class="row">
                <div class="col-12">
                    <div class="filter-section">
                        <div class="filter-header">Filter Data</div>
                        <div class="row g-3">
                            <!-- Variant (Multiple Select) -->
                            <div class="col-12 col-md-3">
                                <label for="variant_fg" class="form-label">Variant</label>
                                <select id="variant_fg" class="form-select select2" multiple="multiple">
                                    <option value="">-- Semua Variant --</option>
                                </select>
                            </div>

                            <!-- Bulan Ke -->
                            <div class="col-12 col-md-3">
                                <label for="bulan_ke" class="form-label">Bulan Ke-</label>
                                <select id="bulan_ke" class="form-select select2">
                                    <option value="">-- Semua Bulan --</option>
                                </select>
                            </div>

                            <!-- Tanggal (Tanggal Produksi) -->
                            <div class="col-12 col-md-3">
                                <label for="tanggal_produksi" class="form-label">Tanggal Produksi</label>
                                <input type="date" id="tanggal_produksi" class="form-control">
                            </div>

                            <!-- STK (Storage) -->
                            <div class="col-12 col-md-3">
                                <label for="stk" class="form-label">STK (Storage)</label>
                                <select id="stk" class="form-select select2">
                                    <option value="">-- Semua STK --</option>
                                </select>
                            </div>

                            <!-- Tanggal Filling -->
                            <div class="col-12 col-md-3">
                                <label for="tanggal_filling" class="form-label">Tanggal Filling</label>
                                <input type="date" id="tanggal_filling" class="form-control">
                            </div>

                            <!-- Tanggal Filling (Bulan) -->
                            <div class="col-12 col-md-3">
                                <label for="bulan_filling" class="form-label">Tanggal Filling (Bulan)</label>
                                <select id="bulan_filling" class="form-select select2">
                                    <option value="">-- Semua Bulan --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>

                            <!-- Tanggal Filling (Tahun) -->
                            <div class="col-12 col-md-3">
                                <label for="tahun_filling" class="form-label">Tanggal Filling (Tahun)</label>
                                <select id="tahun_filling" class="form-select select2">
                                    <option value="">-- Semua Tahun --</option>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                                <button type="button" id="btnApply" class="btn btn-apply flex-fill">
                                    <i class="mdi mdi-filter"></i> Apply Filter
                                </button>
                                <button type="button" id="btnReset" class="btn btn-reset flex-fill">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Filter Section -->

            <!-- Data Info Panel -->
            <div id="dataInfoPanel" class="data-info-panel" style="display: none;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Variant Terpilih</div>
                                <div class="info-value" id="infoVariant">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Bulan Ke</div>
                                <div class="info-value" id="infoBulanKe">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Storage</div>
                                <div class="info-value" id="infoSTK">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Periode Data</div>
                                <div class="info-value" id="infoPeriode">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div id="chartSection">

                <!-- KIMIA SECTION -->
                <div class="section-header">
                    <h5>Analisis Kimia <span class="section-count">(8 Parameter)</span></h5>
                </div>

                <div class="chart-grid">
                    <!-- NaCl Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">NaCl</h6>
                            <div class="chart-container">
                                <canvas id="naclChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Brix Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Brix (°Bx)</h6>
                            <div class="chart-container">
                                <canvas id="brixChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Aw Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Aw</h6>
                            <div class="chart-container">
                                <canvas id="awChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- pH Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">pH</h6>
                            <div class="chart-container">
                                <canvas id="phChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- BJ Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">BJ</h6>
                            <div class="chart-container">
                                <canvas id="bjChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Buih Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Buih</h6>
                            <div class="chart-container">
                                <canvas id="buihChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Visco Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Visco</h6>
                            <div class="chart-container">
                                <canvas id="viscoChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Total Nitrogen Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Total Nitrogen</h6>
                            <div class="chart-container">
                                <canvas id="totalNitrogenChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MIKRO SECTION -->
                <div class="section-header">
                    <h5>Analisis Mikro <span class="section-count">(4 Parameter)</span></h5>
                </div>

                <div class="chart-grid">
                    <!-- EB Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">EB</h6>
                            <div class="chart-container">
                                <canvas id="ebChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SA Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">SA</h6>
                            <div class="chart-container">
                                <canvas id="saChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- TPC Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">TPC</h6>
                            <div class="chart-container">
                                <canvas id="tpcChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- YM Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">YM</h6>
                            <div class="chart-container">
                                <canvas id="ymChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let charts = {};

            // Initialize Select2
            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                width: '100%'
            });

            // Load initial data
            loadFilterOptions();
            loadChartData();

            // Event: Apply Filter Button
            $('#btnApply').on('click', function() {
                loadChartData();
            });

            // Event: Reset Button
            $('#btnReset').on('click', function() {
                $('#variant_fg').val(null).trigger('change');
                $('#bulan_ke').val('').trigger('change');
                $('#tanggal_produksi').val('');
                $('#stk').val('').trigger('change');
                $('#tanggal_filling').val('');
                $('#bulan_filling').val('').trigger('change');
                $('#tahun_filling').val('').trigger('change');
                loadChartData();
            });

            // Function: Load Filter Options
            function loadFilterOptions() {
                // Load Variants
                $.ajax({
                    url: "{{ route('dashboard.shelf-life.filter-options') }}",
                    type: 'GET',
                    data: {
                        type: 'variant'
                    },
                    success: function(response) {
                        const $variantFg = $('#variant_fg');
                        $variantFg.empty();
                        if (response.length > 0) {
                            $.each(response, function(index, value) {
                                $variantFg.append(
                                    $('<option></option>').val(value).text(value)
                                );
                            });
                        }
                    }
                });

                // Load Bulan Ke
                $.ajax({
                    url: "{{ route('dashboard.shelf-life.filter-options') }}",
                    type: 'GET',
                    data: {
                        type: 'bulan_ke'
                    },
                    success: function(response) {
                        const $bulanKe = $('#bulan_ke');
                        $bulanKe.html('<option value="">-- Semua Bulan --</option>');
                        if (response.length > 0) {
                            $.each(response, function(index, value) {
                                $bulanKe.append(
                                    $('<option></option>').val(value).text('Bulan ' + value)
                                );
                            });
                        }
                    }
                });

                // Load STK (Storage)
                $.ajax({
                    url: "{{ route('dashboard.shelf-life.filter-options') }}",
                    type: 'GET',
                    data: {
                        type: 'stk'
                    },
                    success: function(response) {
                        const $stk = $('#stk');
                        $stk.html('<option value="">-- Semua STK --</option>');
                        if (response.length > 0) {
                            $.each(response, function(index, value) {
                                $stk.append(
                                    $('<option></option>').val(value).text(value)
                                );
                            });
                        }
                    }
                });

                // Load Tahun Filling (Last 5 years)
                const currentYear = new Date().getFullYear();
                const $tahunFilling = $('#tahun_filling');
                $tahunFilling.html('<option value="">-- Semua Tahun --</option>');
                for (let i = 0; i < 5; i++) {
                    const year = currentYear - i;
                    $tahunFilling.append(
                        $('<option></option>').val(year).text(year)
                    );
                }
            }

            // Function: Load Chart Data
            function loadChartData() {
                const filterData = {
                    variant_fg: $('#variant_fg').val(),
                    bulan_ke: $('#bulan_ke').val(),
                    tanggal_produksi: $('#tanggal_produksi').val(),
                    stk: $('#stk').val(),
                    tanggal_filling: $('#tanggal_filling').val(),
                    bulan_filling: $('#bulan_filling').val(),
                    tahun_filling: $('#tahun_filling').val()
                };

                $.ajax({
                    url: "{{ route('dashboard.shelf-life.chart-data') }}",
                    type: 'GET',
                    data: filterData,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Loading...',
                            text: 'Memuat data chart',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        Swal.close();

                        if (data.bulan_ke.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Kosong',
                                text: 'Tidak ada data untuk ditampilkan dengan filter yang dipilih',
                            });
                            $('#dataInfoPanel').hide();
                            destroyAllCharts();
                            return;
                        }

                        // Update info panel
                        const variantText = $('#variant_fg').val() ?
                            $('#variant_fg').val().length + ' Variant dipilih' : 'Semua Variant';
                        const bulanKeText = $('#bulan_ke').val() || 'Semua Bulan';
                        const stkText = $('#stk').val() || 'Semua STK';

                        $('#infoVariant').text(variantText);
                        $('#infoBulanKe').text(bulanKeText);
                        $('#infoSTK').text(stkText);
                        $('#infoPeriode').text(data.bulan_ke.length + ' Data Point');
                        $('#dataInfoPanel').show();

                        destroyAllCharts();
                        createCharts(data);
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data chart',
                        });
                    }
                });
            }

            // Function: Destroy All Charts
            function destroyAllCharts() {
                Object.keys(charts).forEach(key => {
                    if (charts[key]) {
                        charts[key].destroy();
                    }
                });
                charts = {};
            }

            // Function: Create Charts
            function createCharts(data) {
                // KIMIA CHARTS
                charts.nacl = createChart('naclChart', '%NaCl', data.bulan_ke, data.nacl, '#5a67d8', 'line');
                charts.brix = createChart('brixChart', 'Brix', data.bulan_ke, data.brix, '#48bb78', 'line');
                charts.aw = createChart('awChart', 'Aw', data.bulan_ke, data.aw, '#ed8936', 'line');
                charts.ph = createChart('phChart', 'pH', data.bulan_ke, data.ph, '#38b2ac', 'line');
                charts.bj = createChart('bjChart', 'BJ', data.bulan_ke, data.bj, '#9f7aea', 'line');
                charts.buih = createChart('buihChart', 'Buih', data.bulan_ke, data.buih, '#f56565', 'line');
                charts.visco = createChart('viscoChart', 'Visco', data.bulan_ke, data.visco, '#667eea', 'bar');
                charts.totalNitrogen = createChart('totalNitrogenChart', 'Total Nitrogen', data.bulan_ke, data
                    .total_nitrogen, '#4299e1', 'bar');

                // MIKRO CHARTS
                charts.eb = createChart('ebChart', 'EB', data.bulan_ke, data.eb, '#fc8181', 'line');
                charts.sa = createChart('saChart', 'SA', data.bulan_ke, data.sa, '#63b3ed', 'line');
                charts.tpc = createChart('tpcChart', 'TPC', data.bulan_ke, data.tpc, '#68d391', 'line');
                charts.ym = createChart('ymChart', 'YM', data.bulan_ke, data.ym, '#fbd38d', 'line');
            }

            // Function: Create Chart dengan Data Labels
            function createChart(canvasId, label, labels, data, color, type = 'line') {
                const ctx = document.getElementById(canvasId);

                const config = {
                    type: type,
                    data: {
                        labels: labels.map(l => 'Bulan ' + l),
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: type === 'bar' ? color + '80' : color + '20',
                            borderColor: color,
                            borderWidth: 2.5,
                            fill: type === 'line',
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: color,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: color,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    size: 13,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed.y;
                                        if (value === null || value === undefined) {
                                            return label + ': N/A';
                                        }
                                        return label + ': ' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    padding: 8
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    padding: 8
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'customDataLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;

                            chart.data.datasets.forEach(function(dataset, i) {
                                const meta = chart.getDatasetMeta(i);

                                if (!meta.hidden) {
                                    meta.data.forEach(function(element, index) {
                                        ctx.fillStyle = '#2c3e50';
                                        ctx.font = 'bold 10px Arial';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'bottom';

                                        const value = dataset.data[index];
                                        if (value !== null && value !== undefined) {
                                            const text = value.toFixed(2);
                                            const padding = 5;
                                            const position = element
                                                .tooltipPosition();

                                            ctx.fillText(text, position.x, position
                                                .y - padding);
                                        }
                                    });
                                }
                            });
                        }
                    }]
                };

                return new Chart(ctx, config);
            }
        });
    </script>
@endsection
