@extends('layouts.component.main')
@section('title', 'Dashboard - Press Test Mesin')

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

            <!-- Filter Card -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card filter-card">
                        <div class="card-body">

                            <!-- Filter Fields -->
                            <div class="row g-3 align-items-end">
                                <!-- Tanggal -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="filterTanggal" class="form-label">
                                        <i class="ri-calendar-line me-1"></i>Tanggal
                                    </label>
                                    <input type="date" class="form-control" id="filterTanggal">
                                </div>

                                <!-- Shift -->
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="filterShift" class="form-label">
                                        <i class="ri-time-line me-1"></i>Shift
                                    </label>
                                    <select class="form-select" id="filterShift">
                                        <option value="">Semua</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>

                                <!-- Variant -->
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="filterVariant" class="form-label">
                                        <i class="ri-list-check me-1"></i>Variant
                                    </label>
                                    <select class="form-select" id="filterVariant">
                                        <option value="">Semua</option>
                                        <option value="P 77">P 77</option>
                                        <option value="P 250">P 250</option>
                                        <option value="P 270">P 270</option>
                                        <option value="P 550">P 550</option>
                                        <option value="P 700">P 700</option>
                                        <option value="P 725">P 725</option>
                                        <option value="P 1000">P 1000</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="filterStatus" class="form-label">
                                        <i class="ri-shield-check-line me-1"></i>Status
                                    </label>
                                    <select class="form-select" id="filterStatus">
                                        <option value="">Semua</option>
                                        <option value="OK">OK</option>
                                        <option value="Bocor">Bocor</option>
                                    </select>
                                </div>

                                <!-- Jumlah Data -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="filterLimit" class="form-label">
                                        <i class="ri-database-2-line me-1"></i>Jumlah Data
                                    </label>
                                    <select class="form-select" id="filterLimit">
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="250">250</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                                <button class="btn btn-primary" id="btnApplyFilter">
                                    <i class="ri-filter-3-line me-1"></i>Terapkan
                                </button>
                                <button class="btn btn-outline-secondary" id="btnResetFilter">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </button>
                                <button class="btn btn-success" id="btnExport">
                                    <i class="ri-file-excel-2-line me-1"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Total Data</p>
                                    <h4 class="mb-0" id="totalData">0</h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary rounded-circle">
                                            <i class="ri-file-list-3-line fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Status OK</p>
                                    <h4 class="mb-0 text-success" id="statusOK">0</h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-success rounded-circle">
                                            <i class="ri-checkbox-circle-line fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Bocor</p>
                                    <h4 class="mb-0 text-danger" id="statusBocor">0</h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-danger rounded-circle">
                                            <i class="ri-close-circle-line fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Rata-rata Jarak</p>
                                    <h4 class="mb-0" id="avgJarak">0</h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-info rounded-circle">
                                            <i class="ri-ruler-line fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <!-- Line Chart - Jarak vs Batas -->
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Grafik Jarak vs Batas - Mesin 1</h5>
                            <button class="btn btn-sm btn-primary" id="btnRefresh">
                                <i class="ri-refresh-line"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div id="jarakChart"></div>
                                <div id="emptyStateJarak" class="text-center py-5" style="display: none;">
                                    <i class="ri-inbox-line" style="font-size: 64px; color: #ddd;"></i>
                                    <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                    <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Donut Chart - Status Distribution -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Distribusi Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div id="statusChart"></div>
                                <div id="emptyStateStatus" class="text-center py-5" style="display: none;">
                                    <i class="ri-pie-chart-line" style="font-size: 64px; color: #ddd;"></i>
                                    <h5 class="mt-3 text-muted">Data Tidak Tersedia</h5>
                                    <p class="text-muted">Tidak ada data untuk ditampilkan</p>
                                </div>
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
        let jarakChart, statusChart;
        let allData = [];
        let filteredData = [];
        let isAutoRefresh = false;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Set tanggal hari ini sebagai default
            const today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            // Event handlers
            $('#btnApplyFilter').on('click', function() {
                fetchData();
            });
            $('#btnResetFilter').on('click', resetFilter);
            $('#btnRefresh').on('click', refreshData);

            $('#btnExport').on('click', function() {
                const params = new URLSearchParams({
                    tanggal: $('#filterTanggal').val(),
                    variant: $('#filterVariant').val(),
                    status: $('#filterStatus').val(),
                    limit: $('#filterLimit').val(),
                });

                window.open(
                    "{{ route('dashboard.press-test-mesin.export') }}?" + params.toString(),
                    "_blank"
                );
            });

            // Initial fetch
            fetchData();

            // Auto refresh setiap 10 detik
            setInterval(function() {
                isAutoRefresh = true;
                fetchData();
            }, 10000);
        });

        // Fetch data dari API
        function fetchData() {
            $.ajax({
                url: 'http://10.11.10.130:8081/api/press-test-mesin-1/all',
                type: 'GET',
                data: {
                    tanggal: $('#filterTanggal').val(),
                    shift: $('#filterShift').val(),
                    variant: $('#filterVariant').val(),
                    status: $('#filterStatus').val(),
                    limit: $('#filterLimit').val(),
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        allData = result.data;
                        updateDashboard(allData);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Data',
                            text: 'Terjadi kesalahan saat mengambil data dari server.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: 'Tidak dapat terhubung ke server. Silakan cek koneksi Anda.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        }

        // Reset filter
        function resetFilter() {
            const today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);
            $('#filterShift').val('');
            $('#filterVariant').val('');
            $('#filterStatus').val('');
            $('#filterLimit').val('25');
            fetchData();
        }

        // Refresh data
        function refreshData() {
            isAutoRefresh = false;
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang mengambil data terbaru',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'http://10.11.10.130:8081/api/press-test-mesin-1/all',
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        allData = result.data;
                        fetchData();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Refresh',
                        text: 'Tidak dapat memuat data terbaru',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        }

        // Update dashboard dengan data
        function updateDashboard(data) {
            // Cek jika data kosong
            if (data.length === 0) {
                showEmptyState();
                return;
            }

            hideEmptyState();
            updateStatistics(data);
            updateCharts(data);
        }

        // Show empty state
        function showEmptyState() {
            // Hide charts
            $('#jarakChart').hide();
            $('#statusChart').hide();

            // Show empty state
            $('#emptyStateJarak').show();
            $('#emptyStateStatus').show();

            // Reset statistics to 0
            $('#totalData').text(0);
            $('#statusOK').text(0);
            $('#statusBocor').text(0);
            $('#avgJarak').text(0);
        }

        // Hide empty state
        function hideEmptyState() {
            // Show charts
            $('#jarakChart').show();
            $('#statusChart').show();

            // Hide empty state
            $('#emptyStateJarak').hide();
            $('#emptyStateStatus').hide();
        }

        // Update statistics cards
        function updateStatistics(data) {
            const totalData = data.length;
            const statusOK = data.filter(function(item) {
                return item.status === 'OK';
            }).length;
            const statusBocor = data.filter(function(item) {
                return item.status === 'Bocor';
            }).length;
            const avgJarak = totalData > 0 ?
                (data.reduce(function(sum, item) {
                    return sum + parseFloat(item.jarak);
                }, 0) / totalData).toFixed(3) : 0;

            $('#totalData').text(totalData);
            $('#statusOK').text(statusOK);
            $('#statusBocor').text(statusBocor);
            $('#avgJarak').text(avgJarak);
        }

        // Update charts
        function updateCharts(data) {
            // Prepare data for line chart - group by variant
            const variantGroups = {};
            $.each(data, function(index, item) {
                if (!variantGroups[item.variant]) {
                    variantGroups[item.variant] = [];
                }
                variantGroups[item.variant].push(item);
            });

            // Sort variants
            const sortedVariants = Object.keys(variantGroups).sort();

            const labels = [];
            const jarakData = [];
            const batasData = [];
            const colors = [];
            const createdAtData = [];

            $.each(sortedVariants, function(index, variant) {
                const items = variantGroups[variant];
                $.each(items, function(idx, item) {
                    labels.push(variant + ' #' + (idx + 1));
                    jarakData.push(parseFloat(item.jarak));
                    batasData.push(parseFloat(item.batas));
                    createdAtData.push(item.created_at);
                    // Color based on status
                    colors.push(item.status === 'OK' ? '#22c55e' : '#ef4444');
                });
            });

            // Line Chart - Jarak vs Batas dengan area fill
            const jarakOptions = {
                series: [{
                    name: 'Jarak',
                    data: jarakData,
                    type: 'line'
                }, {
                    name: 'Batas',
                    data: batasData,
                    type: 'line'
                }],
                chart: {
                    height: 450,
                    type: 'line',
                    zoom: {
                        enabled: true,
                        type: 'x',
                        autoScaleYaxis: true
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: !isAutoRefresh,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [4, 3],
                    curve: 'smooth',
                    dashArray: [0, 8]
                },
                colors: ['#4bc0c0', '#ff6384'],
                fill: {
                    type: ['solid', 'solid'],
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                markers: {
                    size: 5,
                    colors: colors,
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        style: {
                            fontSize: '11px'
                        }
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Nilai (cm)',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(2);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontSize: '14px',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 2
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const createdAt = createdAtData[dataPointIndex];
                        const date = new Date(createdAt);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        const formattedTime = date.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });

                        return '<div class="apexcharts-tooltip-custom" style="padding: 12px; background: white; border: 1px solid #e3e3e3; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
                            '<div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #e3e3e3;">' +
                            '<strong style="font-size: 13px; color: #333;">' + labels[dataPointIndex] +
                            '</strong>' +
                            '</div>' +
                            '<div style="margin-bottom: 6px;">' +
                            '<span style="display: inline-block; width: 10px; height: 10px; background: #4bc0c0; border-radius: 50%; margin-right: 6px;"></span>' +
                            '<span style="color: #666; font-size: 12px;">Jarak: </span>' +
                            '<strong style="color: #333; font-size: 13px;">' + series[0][dataPointIndex].toFixed(
                                3) + ' cm</strong>' +
                            '</div>' +
                            '<div style="margin-bottom: 8px;">' +
                            '<span style="display: inline-block; width: 10px; height: 10px; background: #ff6384; border-radius: 50%; margin-right: 6px;"></span>' +
                            '<span style="color: #666; font-size: 12px;">Batas: </span>' +
                            '<strong style="color: #333; font-size: 13px;">' + series[1][dataPointIndex].toFixed(
                                3) + ' cm</strong>' +
                            '</div>' +
                            '<div style="padding-top: 6px; border-top: 1px solid #e3e3e3; font-size: 11px; color: #999;">' +
                            formattedDate + ' ' +
                            formattedTime +
                            '</div>' +
                            '</div>';
                    }
                }
            };

            if (jarakChart) {
                jarakChart.destroy();
            }
            jarakChart = new ApexCharts(document.querySelector("#jarakChart"), jarakOptions);
            jarakChart.render();

            if (isAutoRefresh) {
                setTimeout(function() {
                    isAutoRefresh = false;
                }, 100);
            }

            // Donut Chart - Status Distribution
            const statusOK = data.filter(function(item) {
                return item.status === 'OK';
            }).length;
            const statusBocor = data.filter(function(item) {
                return item.status === 'Bocor';
            }).length;

            const statusOptions = {
                series: [statusOK, statusBocor],
                chart: {
                    type: 'donut',
                    height: 350,
                    animations: {
                        enabled: !isAutoRefresh,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                labels: ['OK', 'Bocor'],
                colors: ['#22c55e', '#ef4444'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 600
                                },
                                value: {
                                    show: true,
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    formatter: function(val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Data',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#6c757d',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce(function(a, b) {
                                            return a + b;
                                        }, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 2
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    },
                    style: {
                        fontSize: '16px',
                        fontWeight: 600
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            if (statusChart) {
                statusChart.destroy();
            }
            statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
        }
    </script>
@endsection
