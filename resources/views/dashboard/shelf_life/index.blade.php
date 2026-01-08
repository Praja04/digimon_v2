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

        .chart-subtitle {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 4px;
            font-weight: 400;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.3;
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
                            <div class="col-12 col-md-5">
                                <label for="kelompok_sample" class="form-label">Kelompok Sample</label>
                                <select id="kelompok_sample" class="form-select select2">
                                    <option value="">Pilih Kelompok Sample</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Non Retail">Non Retail</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label for="kelompok_tanggal" class="form-label">Kelompok Tanggal</label>
                                <select id="kelompok_tanggal" class="form-select select2">
                                    <option value="">Pilih Kelompok Tanggal</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button type="button" id="btnReset" class="btn btn-reset w-100">
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Filter Section -->

            <!-- Data Info Panel -->
            <div id="dataInfoPanel" class="data-info-panel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Kelompok Sample</div>
                                <div class="info-value" id="infoKelompokSample">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div>
                                <div class="info-label">Kelompok Tanggal</div>
                                <div class="info-value" id="infoKelompokTanggal">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                            <h6 class="chart-title">
                                NaCl
                            </h6>
                            <div class="chart-container">
                                <canvas id="naclChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Brix Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                Brix (°Bx)
                            </h6>
                            <div class="chart-container">
                                <canvas id="brixChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Aw Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                Aw
                            </h6>
                            <div class="chart-container">
                                <canvas id="awChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- pH Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                pH
                            </h6>
                            <div class="chart-container">
                                <canvas id="phChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- BJ Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                BJ
                            </h6>
                            <div class="chart-container">
                                <canvas id="bjChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Buih Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                Buih
                            </h6>
                            <div class="chart-container">
                                <canvas id="buihChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Visco Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                Visco
                            </h6>
                            <div class="chart-container">
                                <canvas id="viscoChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Total Nitrogen Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                Total Nitrogen
                            </h6>
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
                            <h6 class="chart-title">
                                EB
                            </h6>
                            <div class="chart-container">
                                <canvas id="ebChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SA Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                SA
                            </h6>
                            <div class="chart-container">
                                <canvas id="saChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- TPC Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                TPC
                            </h6>
                            <div class="chart-container">
                                <canvas id="tpcChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- YM Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">
                                YM
                            </h6>
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

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                width: '100%'
            });

            // Load all data on page load
            loadKelompokTanggal();
            loadChartData();

            // Event: Kelompok Sample Change
            $('#kelompok_sample').on('change', function() {
                loadKelompokTanggal();
                loadChartData();
            });

            // Event: Kelompok Tanggal Change
            $('#kelompok_tanggal').on('change', function() {
                loadChartData();
            });

            // Event: Reset
            $('#btnReset').on('click', function() {
                $('#kelompok_sample').val('').trigger('change');
                $('#kelompok_tanggal').val('').trigger('change');
                loadKelompokTanggal();
                loadChartData();
            });

            // Function: Load Kelompok Tanggal
            function loadKelompokTanggal() {
                const kelompokSample = $('#kelompok_sample').val();
                const $kelompokTanggal = $('#kelompok_tanggal');

                $.ajax({
                    url: "{{ route('dashboard.shelf-life.kelompok-tanggal') }}",
                    type: 'GET',
                    data: {
                        kelompok_sample: kelompokSample
                    },
                    success: function(response) {
                        $kelompokTanggal.html('<option value="">Pilih Kelompok Tanggal</option>');

                        if (response.length > 0) {
                            $.each(response, function(index, value) {
                                $kelompokTanggal.append(
                                    $('<option></option>').val(value).text(value)
                                );
                            });
                        }
                    },
                    error: function() {
                        console.error('Gagal memuat kelompok tanggal');
                    }
                });
            }

            // Function: Load Chart Data
            function loadChartData() {
                const kelompokSample = $('#kelompok_sample').val();
                const kelompokTanggal = $('#kelompok_tanggal').val();

                $.ajax({
                    url: "{{ route('dashboard.shelf-life.chart-data') }}",
                    type: 'GET',
                    data: {
                        kelompok_sample: kelompokSample,
                        kelompok_tanggal: kelompokTanggal
                    },
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
                                text: 'Tidak ada data untuk ditampilkan',
                            });
                            $('#dataInfoPanel').hide();
                            destroyAllCharts();
                            return;
                        }

                        // Update info panel
                        $('#infoKelompokSample').text(kelompokSample || 'Semua Kelompok Sample');
                        $('#infoKelompokTanggal').text(kelompokTanggal || 'Semua Periode');
                        $('#infoPeriode').text(data.bulan_ke.length + ' Bulan');
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
                charts.visco = createChart('viscoChart', 'Visco', data.bulan_ke, data.visco, '#667eea',
                    'bar');
                charts.totalNitrogen = createChart('totalNitrogenChart', 'Total Nitrogen', data.bulan_ke, data
                    .total_nitrogen, '#4299e1', 'bar');

                // MIKRO CHARTS
                charts.eb = createChart('ebChart', 'EB', data.bulan_ke, data.eb, '#fc8181', 'line');
                charts.sa = createChart('saChart', 'SA', data.bulan_ke, data.sa, '#63b3ed', 'line');
                charts.tpc = createChart('tpcChart', 'TPC', data.bulan_ke, data.tpc, '#68d391', 'line');
                charts.ym = createChart('ymChart', 'YM', data.bulan_ke, data.ym, '#fbd38d', 'line');
            }

            // Function: Create Chart
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
                    }
                };

                return new Chart(ctx, config);
            }
        });
    </script>
@endsection
