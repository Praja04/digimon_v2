@extends('layouts.component.main')
@section('title', 'Dashboard - Proses Masak')

@section('styles')
    <style>
        body {
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .page-content {
            background-color: #f5f5f5;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Stat Box */
        .stat-box {
            background: white;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .stat-box .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .stat-box.qty-pesan {
            border-left: 4px solid #FF6B6B;
        }

        .stat-box.aktual {
            border-left: 4px solid #4ECDC4;
        }

        .stat-box.bl-akhir {
            border-left: 4px solid #45B7D1;
        }

        .stat-box.b-on-p {
            border-left: 4px solid #FFA07A;
        }

        .stat-box.cts-overall {
            border-left: 4px solid #98D8C8;
        }

        /* Chart Card */
        .chart-card {
            background: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-card h6 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Trend Charts */
        .trend-single-chart {
            height: 180px;
            margin-bottom: 20px;
        }

        .trend-single-chart:last-child {
            margin-bottom: 0;
        }

        .trend-chart-title {
            font-size: 10px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            text-align: center;
        }

        /* Disposition Chart */
        .disposition-chart-container {
            height: 580px;
        }

        /* Section Header */
        .section-header {
            background: #d4edda;
            color: #155724;
            padding: 8px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
        }

        /* Histogram Card */
        .histogram-card {
            background: white;
            border-radius: 5px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .histogram-header {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cts-badge {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: normal;
        }

        .chart-container {
            height: 150px;
            margin-bottom: 8px;
        }

        .stats-row {
            display: flex;
            justify-content: space-around;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .stats-row .stat-item {
            text-align: center;
            flex: 1;
        }

        .stats-row .stat-label {
            font-size: 9px;
            color: #999;
            display: block;
        }

        .stats-row .stat-value {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            display: block;
        }

        /* Filter */
        .filter-section {
            background: white;
            padding: 12px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Table */
        .table-section {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table {
            font-size: 11px;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            padding: 8px;
        }

        .table td {
            padding: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Proses Masak</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="filter-section">
                        <form id="filterForm" class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label" style="font-size: 11px;">Variant</label>
                                <select name="variant" id="variantSelect" class="form-select form-select-sm">
                                    <option value="">Semua Variant</option>
                                    @foreach ($variants as $variantOption)
                                        <option value="{{ $variantOption }}"
                                            {{ $variant == $variantOption ? 'selected' : '' }}>
                                            {{ $variantOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-size: 11px;">Formulasi</label>
                                <select name="formulasi" id="formulasiSelect" class="form-select form-select-sm" disabled>
                                    <option value="">Semua Formulasi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-size: 11px;">Bulan</label>
                                <select name="month" id="monthSelect" class="form-select form-select-sm">
                                    <option value="">Pilih Bulan</option>
                                    @foreach ($availableMonths as $monthKey => $monthLabel)
                                        <option value="{{ $monthKey }}" {{ $month == $monthKey ? 'selected' : '' }}>
                                            {{ $monthLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-size: 11px;">Week (per Bulan)</label>
                                <select name="week" id="weekSelect" class="form-select form-select-sm"
                                    {{ empty($weeks) ? 'disabled' : '' }}>
                                    <option value="">Pilih Week</option>
                                    @foreach ($weeks as $weekKey => $weekLabel)
                                        <option value="{{ $weekKey }}" {{ $week == $weekKey ? 'selected' : '' }}>
                                            {{ $weekLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-size: 11px;">Tanggal Produksi</label>
                                <input type="date" name="date" id="dateInput" class="form-control form-control-sm"
                                    value="{{ $date }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="filterBtn" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="resetBtn" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Header Stats -->
            <div class="row g-2 mb-3">
                <div class="col">
                    <div class="stat-box qty-pesan">
                        <div class="label">Qty Pesan</div>
                        <div class="value">450</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box aktual">
                        <div class="label">Aktual 1T</div>
                        <div class="value">360</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box bl-akhir">
                        <div class="label">V. BL Akhir</div>
                        <div class="value">9,792.58</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box b-on-p">
                        <div class="label">V. B-On-P</div>
                        <div class="value">9,808.85</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box cts-overall">
                        <div class="label">CTS Overall</div>
                        <div class="value">90.91%</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <!-- Left: Trend Produksi -->
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="chart-card">
                        <h6>Trend Produksi</h6>

                        <!-- Chart 1: Total Adjustment NaCL(Kg/L) -->
                        <div class="trend-single-chart">
                            <div class="trend-chart-title">Total Adjustment NaCL(Kg/L)</div>
                            <div style="height: calc(100% - 25px);">
                                <canvas id="trendAdjustmentNaClChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 2: Total Adjustment Air(Kg/L) -->
                        <div class="trend-single-chart">
                            <div class="trend-chart-title">Total Adjustment Air(Kg/L)</div>
                            <div style="height: calc(100% - 25px);">
                                <canvas id="trendAdjustmentAirChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 3: Kebutuhan Air Per Penurunan -->
                        <div class="trend-single-chart">
                            <div class="trend-chart-title">Kebutuhan Air Per Penurunan</div>
                            <div style="height: calc(100% - 25px);">
                                <canvas id="trendAirChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Disposisi SFG -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <h6>Disposisi SFG</h6>
                        <div class="disposition-chart-container">
                            <canvas id="dispositionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DISSOLVER SECTION -->
            <div class="row mb-2">
                <div class="col-12">
                    <div class="section-header">DISSOLVER</div>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <!-- HISTOGRAM BRIX (Pelarutan 1) -->
                <div class="col-lg-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM BRIX (Pelarutan 1)</span>
                            <span class="cts-badge" id="cts-dissolver-pelarutan-1">CTS 0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="dissolverBrixPelarutan1"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-dissolver-pelarutan-1">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-dissolver-pelarutan-1">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-dissolver-pelarutan-1">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM BRIX (Pelarutan 2) -->
                <div class="col-lg-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM BRIX (Pelarutan 2)</span>
                            <span class="cts-badge" id="cts-dissolver-pelarutan-2">CTS 0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="dissolverBrixPelarutan2"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-dissolver-pelarutan-2">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-dissolver-pelarutan-2">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-dissolver-pelarutan-2">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLENDING AWAL SECTION -->
            <div class="row mb-2">
                <div class="col-12">
                    <div class="section-header">BLENDING AWAL</div>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <!-- HISTOGRAM BRIX -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM BRIX</span>
                            <span class="cts-badge" id="cts-blending-brix">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingBrix"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-blending-brix">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-blending-brix">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-blending-brix">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM VISCO -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM VISCO</span>
                            <span class="cts-badge" id="cts-blending-visco">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingVisco"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-blending-visco">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-blending-visco">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-blending-visco">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM NACL -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM NACL</span>
                            <span class="cts-badge" id="cts-blending-nacl">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingNacl"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-blending-nacl">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-blending-nacl">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-blending-nacl">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM AW -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM AW</span>
                            <span class="cts-badge" id="cts-blending-aw">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingAw"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-blending-aw">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-blending-aw">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-blending-aw">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM PH -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM PH</span>
                            <span class="cts-badge" id="cts-blending-ph">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingPh"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-blending-ph">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-blending-ph">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-blending-ph">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM WARNA -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM WARNA</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="blendingWarna"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Total</span>
                                <span class="stat-value" id="total-blending-warna">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLENDING RELEASE SECTION -->
            <div class="row mb-2">
                <div class="col-12">
                    <div class="section-header">BLENDING RELEASE</div>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <!-- HISTOGRAM BRIX -->
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM BRIX</span>
                            <span class="cts-badge" id="cts-release-brix">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="releaseBrix"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-release-brix">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-release-brix">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-release-brix">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM NACL -->
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM NACL</span>
                            <span class="cts-badge" id="cts-release-nacl">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="releaseNacl"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-release-nacl">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-release-nacl">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-release-nacl">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM BJ -->
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM BJ</span>
                            <span class="cts-badge" id="cts-release-bj">0%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="releaseBj"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Min</span>
                                <span class="stat-value" id="min-release-bj">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Avg</span>
                                <span class="stat-value" id="avg-release-bj">-</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Max</span>
                                <span class="stat-value" id="max-release-bj">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM WARNA BLOKE -->
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM WARNA BLOKE</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="releaseWarnaBloke"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Total</span>
                                <span class="stat-value" id="total-release-warna">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTOGRAM ORGANO BLOKE -->
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="histogram-card">
                        <div class="histogram-header">
                            <span>HISTOGRAM ORGANO BLOKE</span>
                            <span class="cts-badge" id="cts-release-organo">100%</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="releaseOrganoBloke"></canvas>
                        </div>
                        <div class="stats-row">
                            <div class="stat-item">
                                <span class="stat-label">Total</span>
                                <span class="stat-value" id="total-release-organo">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Proses Masak Table -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="table-section">
                        <h6 style="font-size: 13px; font-weight: bold; margin-bottom: 15px;">Catatan Proses Masak</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Tanggal</th>
                                        <th style="width: 150px;">Batch</th>
                                        <th style="width: 120px;">Type</th>
                                        <th>Catatan Proses</th>
                                    </tr>
                                </thead>
                                <tbody id="catatanProsesTable">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada catatan proses</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Global variables untuk menyimpan chart instances
        let chartInstances = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Filter handling
            const filterBtn = document.getElementById('filterBtn');
            const resetBtn = document.getElementById('resetBtn');
            const monthSelect = document.getElementById('monthSelect');
            const weekSelect = document.getElementById('weekSelect');
            const dateInput = document.getElementById('dateInput');

            // Load initial data
            loadData();

            // Initialize Trend Charts (static data)
            initializeTrendCharts();

            // Filter button click
            filterBtn.addEventListener('click', function() {
                loadData();
            });

            // Reset button click
            resetBtn.addEventListener('click', function() {
                document.getElementById('filterForm').reset();
                weekSelect.disabled = true;
                loadData();
            });

            // Month change - load weeks
            monthSelect.addEventListener('change', function() {
                if (this.value) {
                    weekSelect.value = '';
                    dateInput.value = '';
                    loadWeeks(this.value);
                } else {
                    weekSelect.disabled = true;
                    weekSelect.innerHTML = '<option value="">Pilih Week</option>';
                }
            });

            // Week change - clear date
            weekSelect.addEventListener('change', function() {
                if (this.value) {
                    dateInput.value = '';
                }
            });

            // Date change - clear month and week
            dateInput.addEventListener('change', function() {
                if (this.value) {
                    monthSelect.value = '';
                    weekSelect.value = '';
                    weekSelect.disabled = true;
                }
            });

            // Load weeks via AJAX
            function loadWeeks(month) {
                fetch(`/api/dashboard/proses-masak/weeks?month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            weekSelect.innerHTML = '<option value="">Pilih Week</option>';
                            Object.entries(data.weeks).forEach(([key, value]) => {
                                const option = document.createElement('option');
                                option.value = key;
                                option.textContent = value;
                                weekSelect.appendChild(option);
                            });
                            weekSelect.disabled = false;
                        }
                    })
                    .catch(error => console.error('Error loading weeks:', error));
            }

            // Load data via AJAX
            function loadData() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                fetch(`/api/dashboard/proses-masak/data?${params.toString()}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            updateCharts(result.data);
                            updateTable(result.data.catatanProses);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
                    })
            }

            // Initialize Trend Charts (static for now)
            function initializeTrendCharts() {
                const trendDates = ['Feb 4', 'Feb 5', 'Feb 6', 'Feb 7', 'Feb 8', 'Jan 6', 'Jan 8', 'Jan 10',
                    'Jan 14', 'Jan 16', 'Jan 18', 'Jan 22', 'Jan 24', 'Jan 26', 'Jan 30', 'Feb 1'
                ];

                // Trend Adjustment NaCL
                const trendNaClCtx = document.getElementById('trendAdjustmentNaClChart');
                if (trendNaClCtx) {
                    new Chart(trendNaClCtx, {
                        type: 'line',
                        data: {
                            labels: trendDates,
                            datasets: [{
                                label: 'Kg/L',
                                data: [2.75, 8.43, 24.58, 4.3, 4.12, 14.38, 6.7, 32.38, 7.77,
                                    14.57, 7.77, 6.16, 14.38, 0.8, 9.5, 5.74
                                ],
                                borderColor: '#4169E1',
                                backgroundColor: 'rgba(65, 105, 225, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 2,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 8
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 7
                                        },
                                        maxRotation: 45
                                    }
                                }
                            }
                        }
                    });
                }

                // Trend Adjustment Air
                const trendAirCtx = document.getElementById('trendAdjustmentAirChart');
                if (trendAirCtx) {
                    new Chart(trendAirCtx, {
                        type: 'line',
                        data: {
                            labels: trendDates,
                            datasets: [{
                                label: 'Kg/L',
                                data: [1.276, 1.270, 1.744, 0.965, 0.623, 3.050, 1.747, 1.909,
                                    0.970, 1.178, 1.271, 0.960, 0.957, 0.962, 1.178, 1.271
                                ],
                                borderColor: '#32CD32',
                                backgroundColor: 'rgba(50, 205, 50, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 2,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 8
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 7
                                        },
                                        maxRotation: 45
                                    }
                                }
                            }
                        }
                    });
                }

                // Trend Air Per Penurunan
                const trendChart = document.getElementById('trendAirChart');
                if (trendChart) {
                    new Chart(trendChart, {
                        type: 'line',
                        data: {
                            labels: trendDates,
                            datasets: [{
                                label: '%',
                                data: [150, 180, 120, 200, 250, 180, 160, 220, 190, 170, 210,
                                    180, 160, 140, 120, 100
                                ],
                                borderColor: '#FFD700',
                                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 2,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 8
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 7
                                        },
                                        maxRotation: 45
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Update all charts
            function updateCharts(data) {
                // Destroy existing dynamic charts (kecuali trend charts)
                Object.entries(chartInstances).forEach(([key, chart]) => {
                    if (chart && !key.startsWith('trend')) {
                        chart.destroy();
                        delete chartInstances[key];
                    }
                });

                // Update dissolver charts
                updateDissolverStats(data.dissolverStats);

                // Update blending awal charts
                updateBlendingAwalCharts(data.blendingAwalStats, data.colorCount);

                // Update blending release charts
                updateBlendingReleaseCharts(data.blendingReleaseStats, data.warnaBloke, data.organoBloke);

                // Update disposition chart
                updateDispositionChart(data.dispositionData, data.sourceBreakdown);
            }

            // Update dissolver statistics
            function updateDissolverStats(stats) {
                // Pelarutan 1
                document.getElementById('min-dissolver-pelarutan-1').textContent = stats.pelarutan1.min;
                document.getElementById('avg-dissolver-pelarutan-1').textContent = stats.pelarutan1.avg;
                document.getElementById('max-dissolver-pelarutan-1').textContent = stats.pelarutan1.max;
                document.getElementById('cts-dissolver-pelarutan-1').textContent = `CTS ${stats.pelarutan1.cts}%`;

                chartInstances.dissolverBrixPelarutan1 = createHistogram('dissolverBrixPelarutan1', stats.pelarutan1
                    .data,
                    'BRIX (Pelarutan 1)', '#4169E1');

                // Pelarutan 2
                document.getElementById('min-dissolver-pelarutan-2').textContent = stats.pelarutan2.min;
                document.getElementById('avg-dissolver-pelarutan-2').textContent = stats.pelarutan2.avg;
                document.getElementById('max-dissolver-pelarutan-2').textContent = stats.pelarutan2.max;
                document.getElementById('cts-dissolver-pelarutan-2').textContent = `CTS ${stats.pelarutan2.cts}%`;

                chartInstances.dissolverBrixPelarutan2 = createHistogram('dissolverBrixPelarutan2', stats.pelarutan2
                    .data,
                    'BRIX (Pelarutan 2)', '#FFD700');
            }

            // Update blending awal charts
            function updateBlendingAwalCharts(stats, colorCount) {
                // BRIX
                document.getElementById('min-blending-brix').textContent = stats.brix.min;
                document.getElementById('avg-blending-brix').textContent = stats.brix.avg;
                document.getElementById('max-blending-brix').textContent = stats.brix.max;
                document.getElementById('cts-blending-brix').textContent = `${stats.brix.cts}%`;
                chartInstances.blendingBrix = createHistogram('blendingBrix', stats.brix.data, 'BRIX',
                    '#4169E1');

                // VISCO
                document.getElementById('min-blending-visco').textContent = stats.visco.min;
                document.getElementById('avg-blending-visco').textContent = stats.visco.avg;
                document.getElementById('max-blending-visco').textContent = stats.visco.max;
                document.getElementById('cts-blending-visco').textContent = `${stats.visco.cts}%`;
                chartInstances.blendingVisco = createHistogram('blendingVisco', stats.visco.data, 'VISCO',
                    '#FFD700');

                // NACL
                document.getElementById('min-blending-nacl').textContent = stats.nacl.min;
                document.getElementById('avg-blending-nacl').textContent = stats.nacl.avg;
                document.getElementById('max-blending-nacl').textContent = stats.nacl.max;
                document.getElementById('cts-blending-nacl').textContent = `${stats.nacl.cts}%`;
                chartInstances.blendingNacl = createHistogram('blendingNacl', stats.nacl.data, 'NACL',
                    '#32CD32');

                // AW
                document.getElementById('min-blending-aw').textContent = stats.aw.min;
                document.getElementById('avg-blending-aw').textContent = stats.aw.avg;
                document.getElementById('max-blending-aw').textContent = stats.aw.max;
                document.getElementById('cts-blending-aw').textContent = `${stats.aw.cts}%`;
                chartInstances.blendingAw = createHistogram('blendingAw', stats.aw.data, 'AW', '#00CED1');

                // PH
                document.getElementById('min-blending-ph').textContent = stats.ph.min;
                document.getElementById('avg-blending-ph').textContent = stats.ph.avg;
                document.getElementById('max-blending-ph').textContent = stats.ph.max;
                document.getElementById('cts-blending-ph').textContent = `${stats.ph.cts}%`;
                chartInstances.blendingPh = createHistogram('blendingPh', stats.ph.data, 'PH', '#9370DB');

                // WARNA
                const warnaLabels = Object.values(colorCount).map(item => item.color_name);
                const warnaData = Object.values(colorCount).map(item => item.count);
                const totalWarna = warnaData.reduce((a, b) => a + b, 0);
                document.getElementById('total-blending-warna').textContent = totalWarna;
                chartInstances.blendingWarna = createBarChart('blendingWarna', warnaLabels, warnaData,
                    '#FF6384');
            }

            // Update blending release charts
            function updateBlendingReleaseCharts(stats, warnaBloke, organoBloke) {
                // BRIX
                document.getElementById('min-release-brix').textContent = stats.brix.min;
                document.getElementById('avg-release-brix').textContent = stats.brix.avg;
                document.getElementById('max-release-brix').textContent = stats.brix.max;
                document.getElementById('cts-release-brix').textContent = `${stats.brix.cts}%`;
                chartInstances.releaseBrix = createHistogram('releaseBrix', stats.brix.data, 'BRIX',
                    '#4169E1');

                // NACL
                document.getElementById('min-release-nacl').textContent = stats.nacl.min;
                document.getElementById('avg-release-nacl').textContent = stats.nacl.avg;
                document.getElementById('max-release-nacl').textContent = stats.nacl.max;
                document.getElementById('cts-release-nacl').textContent = `${stats.nacl.cts}%`;
                chartInstances.releaseNacl = createHistogram('releaseNacl', stats.nacl.data, 'NACL',
                    '#32CD32');

                // BJ
                document.getElementById('min-release-bj').textContent = stats.bj.min;
                document.getElementById('avg-release-bj').textContent = stats.bj.avg;
                document.getElementById('max-release-bj').textContent = stats.bj.max;
                document.getElementById('cts-release-bj').textContent = `${stats.bj.cts}%`;
                chartInstances.releaseBj = createHistogram('releaseBj', stats.bj.data, 'BJ', '#FFD700');

                // WARNA BLOKE
                const warnaLabels = Object.values(warnaBloke).map(item => item.color_name);
                const warnaData = Object.values(warnaBloke).map(item => item.count);
                const totalWarna = warnaData.reduce((a, b) => a + b, 0);
                document.getElementById('total-release-warna').textContent = totalWarna;
                chartInstances.releaseWarnaBloke = createBarChart('releaseWarnaBloke', warnaLabels, warnaData,
                    '#E91E63');

                // ORGANO BLOKE
                const organoLabels = Object.keys(organoBloke);
                const organoData = Object.values(organoBloke);
                const totalOrgano = organoData.reduce((a, b) => a + b, 0);
                document.getElementById('total-release-organo').textContent = totalOrgano;
                document.getElementById('cts-release-organo').textContent = `${stats.organo.cts}%`;
                chartInstances.releaseOrganoBloke = createBarChart('releaseOrganoBloke', organoLabels,
                    organoData, '#9C27B0');
            }

            // Update disposition chart
            function updateDispositionChart(dispositionData, sourceBreakdown) {
                const ctx = document.getElementById('dispositionChart');
                if (!ctx) return;

                if (chartInstances.dispositionChart) {
                    chartInstances.dispositionChart.destroy();
                }

                const dispositionColorMap = {
                    'Release': '#28a745',
                    'Release Bersyarat': '#5cb85c',
                    'Resampling': '#17a2b8',
                    'Reject': '#dc3545',
                    'Repro': '#fd7e14',
                    'Jalan Bareng': '#6f42c1',
                    'Leveling': '#ffc107'
                };

                const sourceColorMap = {
                    'Pelarutan 1': '#4169E1',
                    'Pelarutan 2': '#FFD700',
                    'Blending': '#32CD32'
                };

                // Outer ring — disposisi (filter yg count > 0)
                const filteredDisp = dispositionData.filter(item => item.count > 0);
                const outerLabels = filteredDisp.map(item => `${item.label} (${item.percentage}%)`);
                const outerData = filteredDisp.map(item => item.count);
                const outerColors = filteredDisp.map(item => dispositionColorMap[item.label] ?? '#999999');

                // Inner ring — sumber Pelarutan 1 / Pelarutan 2 / Blending (filter yg count > 0)
                const filteredSrc = (sourceBreakdown ?? []).filter(item => item.count > 0);
                const innerLabels = filteredSrc.map(item => `${item.label} (${item.percentage}%)`);
                const innerData = filteredSrc.map(item => item.count);
                const innerColors = filteredSrc.map(item => sourceColorMap[item.label] ?? '#aaaaaa');

                const hasData = outerData.length > 0;

                chartInstances.dispositionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: outerLabels, // legend utama = disposisi
                        datasets: [{
                                // Outer ring — disposisi
                                label: 'Disposisi',
                                data: hasData ? outerData : [1],
                                backgroundColor: hasData ? outerColors : ['#e0e0e0'],
                                borderWidth: 2,
                                borderColor: '#fff',
                                weight: 2 // ring luar lebih tebal
                            },
                            {
                                // Inner ring — sumber
                                label: 'Label',
                                data: hasData ? innerData : [1],
                                backgroundColor: hasData ? innerColors : ['#e0e0e0'],
                                borderWidth: 2,
                                borderColor: '#fff',
                                weight: 1 // ring dalam lebih tipis
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '35%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 9
                                    },
                                    boxWidth: 10,
                                    padding: 6,
                                    // Tampilkan legend gabungan: disposisi + sumber
                                    generateLabels: function(chart) {
                                        const dispLabels = outerLabels.map((lbl, i) => ({
                                            text: lbl,
                                            fillStyle: outerColors[i] ?? '#999',
                                            strokeStyle: '#fff',
                                            lineWidth: 1,
                                            hidden: false,
                                            datasetIndex: 0,
                                            index: i
                                        }));

                                        // Separator
                                        const separator = [{
                                            text: '— Proses —',
                                            fillStyle: 'transparent',
                                            strokeStyle: 'transparent',
                                            lineWidth: 0,
                                            hidden: false,
                                            datasetIndex: -1,
                                            index: -1
                                        }];

                                        const srcLabels = innerLabels.map((lbl, i) => ({
                                            text: lbl,
                                            fillStyle: innerColors[i] ?? '#aaa',
                                            strokeStyle: '#fff',
                                            lineWidth: 1,
                                            hidden: false,
                                            datasetIndex: 1,
                                            index: i
                                        }));

                                        return hasData ? [...dispLabels, ...separator, ...srcLabels] :
                                    [{
                                            text: 'No Data',
                                            fillStyle: '#e0e0e0'
                                        }];
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.datasetIndex === 0) {
                                            // Outer = disposisi
                                            const item = filteredDisp[context.dataIndex];
                                            return item ?
                                                ` ${item.label}: ${item.count} batch (${item.percentage}%)` :
                                                '';
                                        } else {
                                            // Inner = sumber
                                            const item = filteredSrc[context.dataIndex];
                                            return item ?
                                                ` ${item.label}: ${item.count} batch (${item.percentage}%)` :
                                                '';
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Update catatan proses table
            function updateTable(catatanProses) {
                const tbody = document.getElementById('catatanProsesTable');
                tbody.innerHTML = '';

                if (catatanProses.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center text-muted">Tidak ada catatan proses</td></tr>';
                    return;
                }

                catatanProses.forEach(catatan => {
                    const row = `
                        <tr>
                            <td>${catatan.tgl}</td>
                            <td>${catatan.batch}</td>
                            <td><span class="badge bg-info">${catatan.type}</span></td>
                            <td>${catatan.catatan}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            }

            // Helper function to create histogram
            function createHistogram(canvasId, data, label, color) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return null;

                if (!data || data.length === 0) {
                    return new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['No Data'],
                            datasets: [{
                                label: label,
                                data: [0],
                                backgroundColor: '#e0e0e0',
                                borderColor: '#e0e0e0',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 1,
                                    ticks: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 8
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Create bins for histogram
                const bins = 8;
                const min = Math.min(...data);
                const max = Math.max(...data);
                const binSize = (max - min) / bins || 1;

                const histogram = new Array(bins).fill(0);
                const labels = [];

                for (let i = 0; i < bins; i++) {
                    const binStart = min + (i * binSize);
                    const binEnd = binStart + binSize;
                    labels.push(binStart.toFixed(1));
                    histogram[i] = data.filter(val => val >= binStart && val < binEnd).length;
                }

                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: histogram,
                            backgroundColor: color,
                            borderColor: color,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 9
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 8
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }

            // Helper function to create bar chart
            function createBarChart(canvasId, labels, data, color) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return null;

                if (!labels || labels.length === 0) {
                    labels = ['No Data'];
                    data = [0];
                    color = '#e0e0e0';
                }

                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: color,
                            borderColor: color,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: labels[0] !== 'No Data'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 9
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 8
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
