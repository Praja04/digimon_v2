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

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-2">Total Berat Hari Ini</p>
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        <span class="counter-value"
                                            data-target="{{ $beratHariIni }}">{{ number_format($beratHariIni, 2) }}</span>
                                        kg
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
                                        <span class="counter-value"
                                            data-target="{{ $transaksiHariIni }}">{{ $transaksiHariIni }}</span>
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
                                        <span class="counter-value"
                                            data-target="{{ $rataRataBerat }}">{{ number_format($rataRataBerat ?? 0, 2) }}</span>
                                        kg
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
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        {{ $mesinAktif->mesin ?? '-' }}
                                    </h4>
                                    <p class="text-muted mb-0"><small>{{ $mesinAktif->total ?? 0 }} transaksi</small></p>
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
                                            <th scope="col" class="text-end">Total Berat (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dataMesin as $mesin)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title bg-soft-secondary rounded-circle">
                                                                <i class="bx bx-devices"></i>
                                                            </div>
                                                        </div>
                                                        <h6 class="mb-0">{{ $mesin->mesin ?? '-' }}</h6>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span
                                                        class="badge badge-soft-primary">{{ $mesin->total_transaksi }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($mesin->total_berat, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    Tidak ada data hari ini
                                                </td>
                                            </tr>
                                        @endforelse
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
                                    <tbody>
                                        @forelse($transaksiTerbaru as $transaksi)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $transaksi->waktu ? \Carbon\Carbon::parse($transaksi->waktu)->format('H:i') : '-' }}
                                                    </small>
                                                </td>
                                                <td>{{ $transaksi->mesin ?? '-' }}</td>
                                                <td>{{ $transaksi->variant ?? '-' }}</td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($transaksi->berat, 2) }}</strong>
                                                    <small class="text-muted">{{ $transaksi->unit ?? 'kg' }}</small>
                                                </td>
                                                <td>
                                                    @if ($transaksi->status == 'success' || $transaksi->status == 'selesai')
                                                        <span
                                                            class="badge badge-soft-success">{{ $transaksi->status }}</span>
                                                    @elseif($transaksi->status == 'pending' || $transaksi->status == 'proses')
                                                        <span
                                                            class="badge badge-soft-warning">{{ $transaksi->status }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-soft-secondary">{{ $transaksi->status ?? '-' }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    Tidak ada transaksi
                                                </td>
                                            </tr>
                                        @endforelse
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
        $(document).ready(function() {
            var transaksiData = @json($transaksiPerJam);

            var hours = Array.from({
                length: 24
            }, function(_, i) {
                return i;
            });
            var transaksiCounts = hours.map(function(hour) {
                var found = transaksiData.find(function(d) {
                    return d.jam == hour;
                });
                return found ? found.total : 0;
            });

            var transaksiOptions = {
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
                    categories: hours.map(function(h) {
                        return h.toString().padStart(2, '0') + ':00';
                    }),
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
                        formatter: function(val) {
                            return Math.floor(val);
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " transaksi";
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var transaksiChart = new ApexCharts($("#transaksiChart")[0], transaksiOptions);
            transaksiChart.render();

            var variantData = @json($beratPerVariant);

            var variantOptions = {
                series: variantData.map(function(v) {
                    return parseFloat(v.total_berat);
                }),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: variantData.map(function(v) {
                    return v.variant || 'Unknown';
                }),
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
                                    formatter: function(val) {
                                        return parseFloat(val).toFixed(2) + ' kg';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Berat',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        var total = w.globals.seriesTotals.reduce(function(a, b) {
                                            return a + b;
                                        }, 0);
                                        return total.toFixed(2) + ' kg';
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
                        formatter: function(val) {
                            return val.toFixed(2) + " kg";
                        }
                    }
                }
            };

            var variantChart = new ApexCharts($("#variantChart")[0], variantOptions);
            variantChart.render();
        });
    </script>
@endsection
