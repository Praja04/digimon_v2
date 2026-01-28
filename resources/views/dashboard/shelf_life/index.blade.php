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
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .filter-info {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #5a67d8;
        }

        .filter-info i {
            color: #5a67d8;
            margin-right: 6px;
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

        .empty-state {
            min-height: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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

                        <div class="row mt-2">
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
                                <button type="button" id="btnApply" class="btn btn-primary flex-fill">
                                    <i class="mdi mdi-filter me-1"></i>Apply Filter
                                </button>
                                <button type="button" id="btnReset" class="btn btn-light flex-fill">
                                    <i class="mdi mdi-refresh me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Filter Section -->

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
                            <div id="emptyStateNaCl" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerNaCl" class="chart-container">
                                <canvas id="naclChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Brix Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Brix (°Bx)</h6>
                            <div id="emptyStateBrix" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerBrix" class="chart-container">
                                <canvas id="brixChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Aw Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Aw</h6>
                            <div id="emptyStateAw" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerAw" class="chart-container">
                                <canvas id="awChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- pH Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">pH</h6>
                            <div id="emptyStatePh" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerPh" class="chart-container">
                                <canvas id="phChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- BJ Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">BJ</h6>
                            <div id="emptyStateBj" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerBj" class="chart-container">
                                <canvas id="bjChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Buih Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Buih</h6>
                            <div id="emptyStateBuih" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerBuih" class="chart-container">
                                <canvas id="buihChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Visco Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Visco</h6>
                            <div id="emptyStateVisco" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerVisco" class="chart-container">
                                <canvas id="viscoChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Total Nitrogen Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">Total Nitrogen</h6>
                            <div id="emptyStateTotalNitrogen" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerTotalNitrogen" class="chart-container">
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
                            <div id="emptyStateEb" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerEb" class="chart-container">
                                <canvas id="ebChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SA Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">SA</h6>
                            <div id="emptyStateSa" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerSa" class="chart-container">
                                <canvas id="saChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- TPC Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">TPC</h6>
                            <div id="emptyStateTpc" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerTpc" class="chart-container">
                                <canvas id="tpcChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- YM Chart -->
                    <div class="card card-chart">
                        <div class="card-body">
                            <h6 class="chart-title">YM</h6>
                            <div id="emptyStateYm" class="text-center py-5 d-none empty-state">
                                <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                            <div id="chartContainerYm" class="chart-container">
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
                    success: function(data) {
                        console.log('Chart data received:', data);
                        console.log('bulan_ke length:', data.bulan_ke.length);

                        destroyAllCharts();

                        if (data.bulan_ke.length === 0) {
                            console.log('No data found, showing empty states');
                            showAllEmptyStates();

                            Swal.fire({
                                icon: 'info',
                                title: 'Data Tidak Ditemukan',
                                text: 'Tidak ada data yang sesuai dengan filter yang dipilih. Silakan coba kombinasi filter lain.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#5a67d8'
                            });
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

                        createCharts(data);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data chart',
                        });
                    }
                });
            }

            // Function: Show All Empty States
            function showAllEmptyStates() {
                console.log('Showing all empty states');
                const chartNames = ['NaCl', 'Brix', 'Aw', 'Ph', 'Bj', 'Buih', 'Visco', 'TotalNitrogen',
                    'Eb', 'Sa', 'Tpc', 'Ym'
                ];

                chartNames.forEach(name => {
                    console.log('Showing empty state for: emptyState' + name);
                    const emptyStateEl = $(`#emptyState${name}`);
                    const chartContainerEl = $(`#chartContainer${name}`);

                    console.log('Empty state element found:', emptyStateEl.length);
                    console.log('Chart container element found:', chartContainerEl.length);

                    emptyStateEl.removeClass('d-none').show();
                    chartContainerEl.hide();
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

            // Function: Check if data is empty or all null
            function isDataEmpty(data) {
                return !data || data.length === 0 || data.every(val => val === null || val === undefined);
            }

            // Function: Toggle Chart or Empty State
            function toggleChartDisplay(chartName, isEmpty) {
                console.log(`Toggle ${chartName}: isEmpty=${isEmpty}`);
                const emptyStateEl = $(`#emptyState${chartName}`);
                const chartContainerEl = $(`#chartContainer${chartName}`);

                if (isEmpty) {
                    emptyStateEl.removeClass('d-none').show();
                    chartContainerEl.hide();
                } else {
                    emptyStateEl.addClass('d-none').hide();
                    chartContainerEl.show();
                }
            }

            function createCharts(data) {
                // KIMIA CHARTS
                const naclEmpty = isDataEmpty(data.nacl);
                toggleChartDisplay('NaCl', naclEmpty);
                if (!naclEmpty) {
                    charts.nacl = createChart('naclChart', '%NaCl', data.bulan_ke, data.nacl, '#5a67d8', 'line');
                }

                const brixEmpty = isDataEmpty(data.brix);
                toggleChartDisplay('Brix', brixEmpty);
                if (!brixEmpty) {
                    charts.brix = createChart('brixChart', 'Brix', data.bulan_ke, data.brix, '#48bb78', 'line');
                }

                const awEmpty = isDataEmpty(data.aw);
                toggleChartDisplay('Aw', awEmpty);
                if (!awEmpty) {
                    charts.aw = createChart('awChart', 'Aw', data.bulan_ke, data.aw, '#ed8936', 'line');
                }

                const phEmpty = isDataEmpty(data.ph);
                toggleChartDisplay('Ph', phEmpty);
                if (!phEmpty) {
                    charts.ph = createChart('phChart', 'pH', data.bulan_ke, data.ph, '#38b2ac', 'line');
                }

                const bjEmpty = isDataEmpty(data.bj);
                toggleChartDisplay('Bj', bjEmpty);
                if (!bjEmpty) {
                    charts.bj = createChart('bjChart', 'BJ', data.bulan_ke, data.bj, '#9f7aea', 'line');
                }

                const buihEmpty = isDataEmpty(data.buih);
                toggleChartDisplay('Buih', buihEmpty);
                if (!buihEmpty) {
                    charts.buih = createChart('buihChart', 'Buih', data.bulan_ke, data.buih, '#f56565', 'line');
                }

                const viscoEmpty = isDataEmpty(data.visco);
                toggleChartDisplay('Visco', viscoEmpty);
                if (!viscoEmpty) {
                    charts.visco = createChart('viscoChart', 'Visco', data.bulan_ke, data.visco, '#667eea', 'bar');
                }

                const totalNitrogenEmpty = isDataEmpty(data.total_nitrogen);
                toggleChartDisplay('TotalNitrogen', totalNitrogenEmpty);
                if (!totalNitrogenEmpty) {
                    charts.totalNitrogen = createChart('totalNitrogenChart', 'Total Nitrogen', data.bulan_ke, data
                        .total_nitrogen, '#4299e1', 'bar');
                }

                // MIKRO CHARTS
                const ebEmpty = isDataEmpty(data.eb);
                toggleChartDisplay('Eb', ebEmpty);
                if (!ebEmpty) {
                    charts.eb = createChart('ebChart', 'EB', data.bulan_ke, data.eb, '#fc8181', 'line');
                }

                const saEmpty = isDataEmpty(data.sa);
                toggleChartDisplay('Sa', saEmpty);
                if (!saEmpty) {
                    charts.sa = createChart('saChart', 'SA', data.bulan_ke, data.sa, '#63b3ed', 'line');
                }

                const tpcEmpty = isDataEmpty(data.tpc);
                toggleChartDisplay('Tpc', tpcEmpty);
                if (!tpcEmpty) {
                    charts.tpc = createChart('tpcChart', 'TPC', data.bulan_ke, data.tpc, '#68d391', 'line');
                }

                const ymEmpty = isDataEmpty(data.ym);
                toggleChartDisplay('Ym', ymEmpty);
                if (!ymEmpty) {
                    charts.ym = createChart('ymChart', 'YM', data.bulan_ke, data.ym, '#fbd38d', 'line');
                }
            }

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
