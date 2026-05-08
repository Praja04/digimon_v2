@extends('layouts.component.main')
@section('title', 'Dashboard - Timbangan Retail')
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.timbangan-retail.index') }}">Menu</a>
                                </li>
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
                    <div class="card">
                        <div class="card-body">
                            <form id="filterForm" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Shift</label>
                                    <select class="form-select" id="shift" name="shift">
                                        <option value="">Semua Shift</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="status_hasil" name="status_hasil">
                                        <option value="">Semua Status</option>
                                        <option value="OK">OK</option>
                                        <option value="NOT OK">NOT OK</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="mdi mdi-filter me-1"></i>Apply Filter
                                        </button>
                                        <button type="button" id="btnReset" class="btn btn-light flex-fill">
                                            <i class="mdi mdi-refresh me-1"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-2">Total Berat Hari Ini</p>
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        <span id="beratHariIni">0.00</span> g
                                    </h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-primary rounded fs-3">
                                            <i class="bx bx-package text-primary"></i>
                                        </span>
                                    </div>
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
                                    <p class="text-uppercase fw-medium text-muted mb-2">Transaksi Hari Ini</p>
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        <span id="transaksiHariIni">0</span>
                                    </h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-success rounded fs-3">
                                            <i class="bx bx-transfer text-success"></i>
                                        </span>
                                    </div>
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
                                    <p class="text-uppercase fw-medium text-muted mb-2">Rata-rata Berat</p>
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        <span id="rataRataBerat">0.00</span> g
                                    </h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-info rounded fs-3">
                                            <i class="bx bx-bar-chart-alt-2 text-info"></i>
                                        </span>
                                    </div>
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
                                    <p class="text-uppercase fw-medium text-muted mb-2">Mesin Paling Aktif</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="mesinAktifNama">-</h4>
                                    <p class="text-muted mb-0"><small id="mesinAktifTotal">0 transaksi</small></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-warning rounded fs-3">
                                            <i class="bx bx-devices text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-xl-7">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Transaksi Per Jam Hari Ini</h4>
                        </div>
                        <div class="card-body">
                            <div id="transaksiChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Top 5 Variant (Berat)</h4>
                        </div>
                        <div class="card-body">
                            <div id="variantChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Row -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Data Per Mesin</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Mesin</th>
                                            <th scope="col" class="text-end">Transaksi</th>
                                            <th scope="col" class="text-end">Total Berat (g)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataMesinTable">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                Memuat data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Transaksi Terbaru</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Waktu</th>
                                            <th scope="col">Mesin</th>
                                            <th scope="col">Variant</th>
                                            <th scope="col" class="text-end">Berat</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transaksiTerbaruTable">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Memuat data...
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
        let transaksiChart = null;
        let variantChart = null;

        $(document).ready(function() {
            loadDashboardData();

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadDashboardData();
            });

            $('#btnReset').on('click', function() {
                $('#tanggal').val('{{ date('Y-m-d') }}');
                $('#shift').val('');
                $('#status_hasil').val('');
                loadDashboardData();
            });
        });

        function loadDashboardData() {
            const tanggal = $('#tanggal').val();
            const shift = $('#shift').val();
            const statusHasil = $('#status_hasil').val();

            $.ajax({
                url: 'http://10.11.10.130:8081/api/mesin/dashboard',
                method: 'GET',
                data: {
                    tanggal: tanggal,
                    shift: shift,
                    status: statusHasil
                },
                success: function(response) {
                    if (response.success) {
                        updateStatistics(response.data.statistics);
                        updateDataMesinTable(response.data.data_mesin);
                        updateTransaksiTerbaruTable(response.data.transaksi_terbaru);
                        updateTransaksiChart(response.data.transaksi_per_jam);
                        updateVariantChart(response.data.berat_per_variant);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading dashboard data:', error);
                    showErrorState();
                }
            });
        }

        function formatNumber(number, decimals = 2) {
            const num = parseFloat(number);
            if (isNaN(num)) return '0.00';

            return num.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        function updateStatistics(stats) {
            $('#beratHariIni').text(formatNumber(stats.berat_hari_ini));
            $('#transaksiHariIni').text(stats.transaksi_hari_ini);
            $('#rataRataBerat').text(formatNumber(stats.rata_rata_berat));

            if (stats.mesin_aktif && stats.mesin_aktif.mesin) {
                $('#mesinAktifNama').text(stats.mesin_aktif.mesin);
                $('#mesinAktifTotal').text(formatNumber(stats.mesin_aktif.total) + ' transaksi');
            } else {
                $('#mesinAktifNama').text('-');
                $('#mesinAktifTotal').text('0 transaksi');
            }
        }

        function updateDataMesinTable(dataMesin) {
            const tbody = $('#dataMesinTable');
            tbody.empty();

            if (dataMesin.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Tidak ada data hari ini
                        </td>
                    </tr>
                `);
                return;
            }

            dataMesin.forEach(function(mesin) {
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <div class="avatar-title bg-soft-secondary rounded-circle">
                                        <i class="bx bx-devices"></i>
                                    </div>
                                </div>
                                <h6 class="mb-0">${mesin.mesin || '-'}</h6>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="badge badge-soft-primary">${formatNumber(mesin.total_transaksi)}</span>
                        </td>
                        <td class="text-end">
                            <strong>${formatNumber(mesin.total_berat)}</strong>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function updateTransaksiTerbaruTable(transaksiTerbaru) {
            const tbody = $('#transaksiTerbaruTable');
            tbody.empty();

            if (transaksiTerbaru.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Tidak ada transaksi
                        </td>
                    </tr>
                `);
                return;
            }

            transaksiTerbaru.forEach(function(transaksi) {
                const waktu = transaksi.waktu ? new Date(transaksi.waktu).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '-';

                const statusBadge = transaksi.status === 'OK' ?
                    `<span class="badge badge-soft-success">${transaksi.status}</span>` :
                    `<span class="badge badge-soft-danger">${transaksi.status || 'NOT OK'}</span>`;

                const row = `
                    <tr>
                        <td>
                            <small class="text-muted">${waktu}</small>
                        </td>
                        <td>${transaksi.mesin || '-'}</td>
                        <td>${transaksi.variant || '-'}</td>
                        <td class="text-end">
                            <strong>${formatNumber(transaksi.berat)}</strong>
                            <small class="text-muted">${transaksi.unit || 'g'}</small>
                        </td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function updateTransaksiChart(transaksiPerJam) {
            const hours = Array.from({
                length: 24
            }, (_, i) => i);
            const transaksiCounts = hours.map(function(hour) {
                const found = transaksiPerJam.find(d => d.jam == hour);
                return found ? found.total : 0;
            });

            const options = {
                series: [{
                    name: 'Jumlah Transaksi',
                    data: transaksiCounts
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
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                colors: ['#4285F4'],
                xaxis: {
                    categories: hours.map(h => h.toString().padStart(2, '0') + ':00'),
                    labels: {
                        rotate: -45,
                        rotateAlways: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Transaksi'
                    },
                    labels: {
                        formatter: val => Math.floor(val)
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: val => val + " transaksi"
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            if (transaksiChart) {
                transaksiChart.destroy();
            }
            transaksiChart = new ApexCharts(document.querySelector("#transaksiChart"), options);
            transaksiChart.render();
        }

        function updateVariantChart(beratPerVariant) {
            const options = {
                series: beratPerVariant.map(v => parseFloat(v.total_berat)),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: beratPerVariant.map(v => v.variant || 'Unknown'),
                colors: ['#4285F4', '#34A853', '#FBBC04', '#EA4335', '#9E9E9E'],
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '13px'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    offsetY: -5
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 600,
                                    offsetY: 5,
                                    formatter: val => formatNumber(val) + ' g'
                                },
                                total: {
                                    show: true,
                                    label: 'Total Berat',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return formatNumber(total) + ' g';
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: val => formatNumber(val) + " g"
                    }
                }
            };

            if (variantChart) {
                variantChart.destroy();
            }
            variantChart = new ApexCharts(document.querySelector("#variantChart"), options);
            variantChart.render();
        }

        function showErrorState() {
            $('#beratHariIni').text('0.00');
            $('#transaksiHariIni').text('0');
            $('#rataRataBerat').text('0.00');
            $('#mesinAktifNama').text('-');
            $('#mesinAktifTotal').text('0 transaksi');

            $('#dataMesinTable').html(`
                <tr>
                    <td colspan="3" class="text-center text-danger py-4">
                        Gagal memuat data
                    </td>
                </tr>
            `);

            $('#transaksiTerbaruTable').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        Gagal memuat data
                    </td>
                </tr>
            `);
        }
    </script>
@endsection
