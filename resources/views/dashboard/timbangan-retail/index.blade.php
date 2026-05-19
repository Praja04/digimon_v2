@extends('layouts.component.main')
@section('title', 'Timbangan Retail')

@section('styles')
<style>
    /* ── VARIABLES ───────────────────────── */
    :root {
        --tr-primary: #1a56db;
        --tr-success: #0e9f6e;
        --tr-warning: #ff5a1f;
        --tr-danger: #e02424;
        --tr-info: #0694a2;
        --tr-purple: #7e3af2;
        --tr-muted: #6b7280;
        --tr-border: #e5e7eb;
        --tr-bg: #f9fafb;
        --tr-card: #ffffff;
        --tr-shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .04);
        --tr-shadow-md: 0 4px 6px rgba(0, 0, 0, .07), 0 2px 4px rgba(0, 0, 0, .06);
    }

    /* ── PAGE WRAPPER ───────────────────── */
    #tr-dashboard {
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* ── TAB NAV ───────────────────────── */
    .tr-tab-nav {
        display: flex;
        gap: 4px;
        background: var(--tr-border);
        border-radius: 10px;
        padding: 4px;
        width: fit-content;
    }

    .tr-tab-btn {
        padding: 8px 22px;
        border: none;
        background: transparent;
        border-radius: 7px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        color: var(--tr-muted);
        transition: all .2s;
    }

    .tr-tab-btn.active {
        background: #fff;
        color: var(--tr-primary);
        box-shadow: var(--tr-shadow);
    }

    /* ── FILTER BAR ─────────────────────── */
    .tr-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        background: #fff;
        border: 1px solid var(--tr-border);
        border-radius: 10px;
        padding: 14px 16px;
    }

    .tr-filters .f-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .tr-filters label {
        font-size: 11px;
        font-weight: 600;
        color: var(--tr-muted);
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .tr-filters select,
    .tr-filters input[type=date] {
        border: 1px solid var(--tr-border);
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 13px;
        color: #111;
        background: var(--tr-bg);
        outline: none;
        min-width: 130px;
        transition: border-color .2s;
    }

    .tr-filters select:focus,
    .tr-filters input[type=date]:focus {
        border-color: var(--tr-primary);
    }

    .tr-btn-apply {
        padding: 8px 20px;
        border: none;
        border-radius: 7px;
        background: var(--tr-primary);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: background .2s;
        align-self: flex-end;
    }

    .tr-btn-apply:hover {
        background: #1648c0;
    }

    /* ── SUMMARY TABLE ──────────────────── */
    .tr-summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .tr-summary-table thead th {
        background: var(--tr-bg);
        border-bottom: 2px solid var(--tr-border);
        padding: 9px 12px;
        text-align: left;
        font-weight: 600;
        color: var(--tr-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    .tr-summary-table tbody td {
        padding: 9px 12px;
        border-bottom: 1px solid var(--tr-border);
        vertical-align: middle;
    }

    .tr-summary-table tbody tr:last-child td {
        border-bottom: none;
    }

    .tr-summary-table .shift-label {
        font-weight: 700;
        color: #111;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .shift-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    /* ── CARD ───────────────────────────── */
    .tr-card {
        background: var(--tr-card);
        border: 1px solid var(--tr-border);
        border-radius: 12px;
        box-shadow: var(--tr-shadow);
        overflow: hidden;
    }

    .tr-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--tr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .tr-card-title {
        font-size: 13px;
        font-weight: 700;
        color: #111;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .tr-card-body {
        padding: 14px 16px;
    }

    /* ── MACHINE BADGE ──────────────────── */
    .mesin-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: -.3px;
        border: 1.5px solid transparent;
    }

    /* ── STATUS BADGES ──────────────────── */
    .badge-ok {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warn {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-err {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-over {
        background: #ede9fe;
        color: #5b21b6;
    }

    /* ── STACKED BAR WRAP ───────────────── */
    .stacked-wrap {
        width: 100%;
    }

    .stacked-bar-row {
        display: flex;
        height: 14px;
        border-radius: 4px;
        overflow: hidden;
        gap: 1px;
        background: transparent;
    }

    .stacked-bar-row>span {
        transition: flex .4s;
        min-width: 0;
    }

    .stacked-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 14px;
        margin-top: 8px;
        font-size: 11px;
    }

    .stacked-legend span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--tr-muted);
    }

    .stacked-legend .dot {
        width: 8px;
        height: 8px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* ── LINE CHART WRAP ────────────────── */
    .chart-canvas-wrap {
        position: relative;
    }

    /* ── OVERVIEW TABLE ─────────────────── */
    .tr-ov-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .tr-ov-table thead th {
        background: var(--tr-bg);
        padding: 7px 10px;
        text-align: left;
        font-weight: 700;
        color: var(--tr-muted);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 2px solid var(--tr-border);
        white-space: nowrap;
    }

    .tr-ov-table tbody td {
        padding: 7px 10px;
        border-bottom: 1px solid var(--tr-border);
        vertical-align: middle;
    }

    .tr-ov-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ── LC GROUP HEADER ────────────────── */
    .lc-group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0 6px;
        margin-bottom: 10px;
        border-bottom: 2px solid var(--tr-border);
    }

    .lc-group-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--tr-muted);
    }

    /* ── MACHINE MINI CARD ──────────────── */
    .mesin-mini-card {
        border: 1px solid var(--tr-border);
        border-radius: 10px;
        padding: 12px;
        background: var(--tr-bg);
    }

    .mesin-mini-card .mini-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        margin-top: 8px;
        font-size: 11px;
    }

    .mesin-mini-card .mini-stats div {
        color: var(--tr-muted);
    }

    .mesin-mini-card .mini-stats strong {
        color: #111;
    }

    /* ── LOADING SPINNER ────────────────── */
    .tr-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
        flex-direction: column;
        gap: 10px;
        color: var(--tr-muted);
        font-size: 13px;
    }

    .tr-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid var(--tr-border);
        border-top-color: var(--tr-primary);
        border-radius: 50%;
        animation: tr-spin .7s linear infinite;
    }

    @keyframes tr-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ── EMPTY STATE ────────────────────── */
    .tr-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 0;
        flex-direction: column;
        gap: 6px;
        color: var(--tr-muted);
        font-size: 13px;
    }

    .tr-empty i {
        font-size: 28px;
        opacity: .4;
    }

    /* ── UTILITY ────────────────────────── */
    .text-primary {
        color: var(--tr-primary) !important;
    }

    .fs-11 {
        font-size: 11px !important;
    }

    .fw-700 {
        font-weight: 700 !important;
    }

    /* ── RESPONSIVE ─────────────────────── */
    @media (max-width: 768px) {
        .tr-filters {
            gap: 8px;
        }

        .tr-filters select,
        .tr-filters input[type=date] {
            min-width: 100px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content" id="tr-dashboard">
    <div class="container-fluid">

        {{-- ── PAGE HEADER ──────────────────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <h4 class="mb-1 fw-700" style="font-size:18px;">
                            <i class="ri-scales-2-line text-primary me-2"></i>Timbangan Retail
                        </h4>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            Monitoring gramasi produk retail — data real-time dari semua mesin
                        </p>
                    </div>
                    <div class="ms-auto">
                        <div class="tr-tab-nav">
                            <button class="tr-tab-btn active" data-tab="slide1" onclick="switchTab('slide1')">
                                <i class="ri-bar-chart-grouped-line me-1"></i>Perbandingan Shift
                            </button>
                            <button class="tr-tab-btn" data-tab="slide2" onclick="switchTab('slide2')">
                                <i class="ri-dashboard-line me-1"></i>Report Mesin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
             SLIDE 1 — Perbandingan Antar Shift
        ════════════════════════════════════════════════════════════ --}}
        <div id="tab-slide1">

            {{-- FILTER ──────────────────────────────────────────────── --}}
            <div class="tr-filters mb-3">
                <div class="f-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="s1-date-start">
                </div>
                <div class="f-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" id="s1-date-end">
                </div>
                <div class="f-group">
                    <label>Varian</label>
                    <select id="s1-varian">
                        <option value="">Semua Varian</option>
                    </select>
                </div>
                <div class="f-group">
                    <label>Mesin</label>
                    <select id="s1-mesin">
                        <option value="">Semua Mesin</option>
                    </select>
                </div>
                <button class="tr-btn-apply" onclick="loadSlide1()">
                    <i class="ri-search-line me-1"></i>Tampilkan
                </button>
            </div>

            {{-- SHIFT CHARTS GRID ────────────────────────────────────── --}}
            <div class="row g-3 mb-3" id="s1-shift-grid">
                @foreach (['1','2','3'] as $shift)
                <div class="col-12 col-xl-4">
                    <div class="tr-card h-100">
                        <div class="tr-card-header">
                            <h6 class="tr-card-title">
                                <span class="shift-dot" style="background:{{ $shift=='1'?'#1a56db':($shift=='2'?'#0e9f6e':'#ff5a1f') }};"></span>
                                Shift {{ $shift }}
                            </h6>
                        </div>
                        <div class="tr-card-body">
                            {{-- Stacked bar --}}
                            <p class="mb-1" style="font-size:11px;font-weight:700;color:var(--tr-muted);text-transform:uppercase;letter-spacing:.4px;">
                                Distribusi Klasifikasi
                            </p>
                            <div class="stacked-wrap mb-3" id="s1-stacked-shift{{ $shift }}">
                                <div class="tr-loading">
                                    <div class="tr-spinner"></div>
                                </div>
                            </div>
                            {{-- Line chart --}}
                            <p class="mb-1" style="font-size:11px;font-weight:700;color:var(--tr-muted);text-transform:uppercase;letter-spacing:.4px;">
                                Tren Gramasi (No Sampel vs Berat)
                            </p>
                            <div class="chart-canvas-wrap">
                                <canvas id="s1-line-shift{{ $shift }}" height="140"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- SUMMARY TABLE ─────────────────────────────────────────── --}}
            <div class="tr-card">
                <div class="tr-card-header">
                    <h6 class="tr-card-title"><i class="ri-table-line me-1 text-primary"></i>Olah Data — Ringkasan Per Shift</h6>
                </div>
                <div class="tr-card-body p-0">
                    <div class="table-responsive">
                        <table class="tr-summary-table">
                            <thead>
                                <tr>
                                    <th>Shift</th>
                                    <th>Min (gr)</th>
                                    <th>Avg (gr)</th>
                                    <th>Max (gr)</th>
                                    <th>&lt;TU2 (pcs)</th>
                                    <th>TU2→TU1 (pcs)</th>
                                    <th>TU1→STD (pcs)</th>
                                    <th>STD→Max (pcs)</th>
                                    <th>&gt;Max (pcs)</th>
                                </tr>
                            </thead>
                            <tbody id="s1-summary-body">
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="tr-loading">
                                            <div class="tr-spinner"></div><span>Memuat data...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
             SLIDE 2 — Report Antar Mesin (Per Shift)
        ════════════════════════════════════════════════════════════ --}}
        <div id="tab-slide2" style="display:none;">

            {{-- FILTER ──────────────────────────────────────────────── --}}
            <div class="tr-filters mb-3">
                <div class="f-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="s2-date-start">
                </div>
                <div class="f-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" id="s2-date-end">
                </div>
                <div class="f-group">
                    <label>Shift</label>
                    <select id="s2-shift">
                        <option value="">Semua Shift</option>
                        <option value="1">Shift 1</option>
                        <option value="2">Shift 2</option>
                        <option value="3">Shift 3</option>
                    </select>
                </div>
                <button class="tr-btn-apply" onclick="loadSlide2()">
                    <i class="ri-search-line me-1"></i>Tampilkan
                </button>
            </div>

            {{-- OVERVIEW TABLE ───────────────────────────────────────── --}}
            <div class="tr-card mb-3">
                <div class="tr-card-header">
                    <h6 class="tr-card-title"><i class="ri-list-check-2 me-1 text-primary"></i>Overview — Semua Varian</h6>
                </div>
                <div class="tr-card-body p-0">
                    <div class="table-responsive">
                        <table class="tr-ov-table">
                            <thead>
                                <tr>
                                    <th>Varian</th>
                                    <th>Under (pcs)</th>
                                    <th>Min (gr)</th>
                                    <th>Avg (gr)</th>
                                    <th>Max (gr)</th>
                                    <th>Over (pcs)</th>
                                </tr>
                            </thead>
                            <tbody id="s2-overview-body">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="tr-loading">
                                            <div class="tr-spinner"></div><span>Memuat data...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MACHINE GROUPS ────────────────────────────────────────── --}}
            <div id="s2-machine-groups">
                @php
                $lc_groups = [
                'LC1' => ['F','G','H','I'],
                'LC2' => ['D','E','J','K'],
                'LC3' => ['C','L','AE','AG'],
                'LC5' => ['B','AF','AI','AJ'],
                'Pouch Besar & Medium' => ['AH','V','U','A'],
                'Sachet 20G' => ['O','P','W','X'],
                'Sachet 40G & 12.5G' => ['R','Q','Y','Z'],
                ];
                $lc_colors = [
                'LC1' => '#1a56db','LC2' => '#0e9f6e','LC3' => '#7e3af2',
                'LC5' => '#0694a2','Pouch Besar & Medium' => '#ff5a1f',
                'Sachet 20G' => '#e02424','Sachet 40G & 12.5G' => '#c27803',
                ];
                @endphp

                @foreach($lc_groups as $lcName => $machines)
                <div class="mb-4">
                    <div class="lc-group-header">
                        <div style="width:3px;height:20px;border-radius:2px;background:{{ $lc_colors[$lcName] ?? '#888' }}"></div>
                        <span class="lc-group-label">{{ $lcName }}</span>
                        <div style="flex:1;height:1px;background:var(--tr-border);"></div>
                    </div>
                    <div class="row g-3">
                        @foreach($machines as $mesin)
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="mesin-mini-card" id="s2-mesin-{{ $mesin }}">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="mesin-badge" style="background:{{ $lc_colors[$lcName] ?? '#888' }}20;color:{{ $lc_colors[$lcName] ?? '#888' }};border-color:{{ $lc_colors[$lcName] ?? '#888' }}40;">
                                        {{ $mesin }}
                                    </span>
                                    <div>
                                        <div style="font-size:12px;font-weight:700;color:#111;">Mesin {{ $mesin }}</div>
                                        <div style="font-size:10px;color:var(--tr-muted);" class="s2-mesin-varian-{{ $mesin }}">—</div>
                                    </div>
                                </div>

                                {{-- Stacked bar --}}
                                <div class="stacked-wrap mb-2" id="s2-stacked-{{ $mesin }}">
                                    <div class="tr-loading" style="padding:16px 0;">
                                        <div class="tr-spinner" style="width:20px;height:20px;border-width:2px;"></div>
                                    </div>
                                </div>

                                {{-- ▼ GANTI canvas tunggal dengan div container — canvas di-inject JS per variant ▼ --}}
                                <div id="s2-charts-{{ $mesin }}" class="mb-1"></div>

                                <div class="mini-stats" id="s2-stats-{{ $mesin }}">
                                    <div>Min: <strong>—</strong></div>
                                    <div>Max: <strong>—</strong></div>
                                    <div>Avg: <strong>—</strong></div>
                                    <div>Total: <strong>—</strong></div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    /* ══════════════════════════════════════════════════════════════════
   CONSTANTS
══════════════════════════════════════════════════════════════════ */
    const VARIANT_STANDARDS = {
        "Sachet YB 12,5gr PCS": {
            min: 12.05,
            std: 13.05,
            max: 14.05,
            tu1: 11.93,
            tu2: 10.80,
            code: "S12.5G-P"
        },
        "Sachet YB 12,5gr RENCENG": {
            min: 154.60,
            std: 156.60,
            max: 168.60,
            tu1: 143.10,
            tu2: 129.60,
            code: "S12.5G-R"
        },
        "Sachet YB 20gr PCS": {
            min: 19.14,
            std: 20.64,
            max: 21.64,
            tu1: 18.84,
            tu2: 17.04,
            code: "S20G-P"
        },
        "Sachet YB 20gr RENCENG": {
            min: 244.68,
            std: 247.68,
            max: 259.68,
            tu1: 226.08,
            tu2: 204.48,
            code: "S20G-R"
        },
        "Sachet BB 40gr PCS": {
            min: 39.10,
            std: 41.10,
            max: 42.10,
            tu1: 37.50,
            tu2: 33.90,
            code: "S40G-P"
        },
        "Sachet BB 40gr RENCENG": {
            min: 489.20,
            std: 493.20,
            max: 505.20,
            tu1: 450.00,
            tu2: 406.80,
            code: "S40G-R"
        },
        "Pouch YB 77gr": {
            min: 78.70,
            std: 79.20,
            max: 82.70,
            tu1: 74.70,
            tu2: 70.20,
            code: "P77G-YB"
        },
        "Pouch BB 77gr": {
            min: 78.70,
            std: 79.20,
            max: 82.70,
            tu1: 74.70,
            tu2: 70.20,
            code: "P77G-BB"
        },
        "Pouch YB 250gr": {
            min: 253.00,
            std: 255.00,
            max: 257.00,
            tu1: 246.00,
            tu2: 237.00,
            code: "P250G"
        },
        "Pouch BB 270gr": {
            min: 273.00,
            std: 275.00,
            max: 277.00,
            tu1: 266.00,
            tu2: 257.00,
            code: "P270G"
        },
        "Pouch YB 550gr": {
            min: 556.00,
            std: 561.00,
            max: 566.00,
            tu1: 545.80,
            tu2: 530.80,
            code: "P550G"
        },
        "Pouch YB 700gr": {
            min: 706.00,
            std: 711.00,
            max: 716.00,
            tu1: 696.00,
            tu2: 681.00,
            code: "P700G"
        },
        "Pouch BB 725gr": {
            min: 730.00,
            std: 735.00,
            max: 740.00,
            tu1: 720.00,
            tu2: 705.00,
            code: "P725G"
        },
        "Pouch YB 1000gr": {
            min: 1007.50,
            std: 1012.50,
            max: 1017.50,
            tu1: 997.50,
            tu2: 982.50,
            code: "P1000G"
        },
    };

    const VARIANT_MESIN = {
        "Sachet YB 12,5gr PCS": ["Y", "Z"],
        "Sachet YB 12,5gr RENCENG": ["Y", "Z"],
        "Sachet YB 20gr PCS": ["O", "P", "W", "X"],
        "Sachet YB 20gr RENCENG": ["O", "P", "W", "X"],
        "Sachet BB 40gr PCS": ["Q", "R"],
        "Sachet BB 40gr RENCENG": ["Q", "R"],
        "Pouch YB 77gr": ["F", "G", "H", "I", "D", "E", "J", "K", "C", "L", "AE", "AG"],
        "Pouch BB 77gr": ["C", "L", "AE", "AG", "B", "AF", "AI", "AJ"],
        "Pouch YB 250gr": ["AH"],
        "Pouch BB 270gr": ["AH"],
        "Pouch YB 550gr": ["A", "U", "V"],
        "Pouch YB 700gr": ["A", "U", "V"],
        "Pouch BB 725gr": ["A", "U", "V"],
        "Pouch YB 1000gr": ["A", "U", "V"],
    };

    // Reverse map: mesin → variants
    const MESIN_VARIAN = {};
    Object.entries(VARIANT_MESIN).forEach(([varian, mesins]) => {
        mesins.forEach(m => {
            if (!MESIN_VARIAN[m]) MESIN_VARIAN[m] = [];
            if (!MESIN_VARIAN[m].includes(varian)) MESIN_VARIAN[m].push(varian);
        });
    });

    const CLASS_COLORS = {
        overMax: '#7e3af2', // >Max
        stdToMax: '#0e9f6e', // STD→Max (OK)
        tu1ToStd: '#1a56db', // TU1→STD (OK)
        tu2ToTu1: '#fbbf24', // TU2→TU1 (warning)
        underTu2: '#e02424', // <TU2 (danger)
    };

    const SHIFT_COLORS = ['#1a56db', '#0e9f6e', '#ff5a1f'];
    const ALL_MESINS = [
        'F', 'G', 'H', 'I', // LC1
        'D', 'E', 'J', 'K', // LC2
        'C', 'L', 'AE', 'AG', // LC3
        'B', 'AF', 'AI', 'AJ', // LC5
        'AH', 'V', 'U', 'A', // Pouch Besar & Medium
        'O', 'P', 'W', 'X', // Sachet 20G
        'R', 'Q', 'Y', 'Z', // Sachet 40G & 12.5G
    ];
    /* ══════════════════════════════════════════════════════════════════
       CHART REGISTRY — avoid double-init
    ══════════════════════════════════════════════════════════════════ */
    const _charts = {};

    function destroyChart(id) {
        if (_charts[id]) {
            _charts[id].destroy();
            delete _charts[id];
        }
    }

    function registerChart(id, instance) {
        _charts[id] = instance;
    }

    /* ══════════════════════════════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════════════════════════════ */
    function classifyWeight(w, std) {
        if (!std) return 'tu1ToStd';
        if (w > std.max) return 'overMax';
        if (w >= std.min) return (w >= std.std) ? 'stdToMax' : 'tu1ToStd';
        if (w >= std.tu1) return 'tu2ToTu1';
        return 'underTu2';
    }

    function buildStackedBar(counts, total) {
        if (!total) return '<div class="tr-empty"><i class="ri-bar-chart-line"></i><span>Tidak ada data</span></div>';
        const pct = k => ((counts[k] || 0) / total * 100).toFixed(1);
        return `
    <div class="stacked-bar-row mb-1">
        <span style="flex:${pct('underTu2')};background:${CLASS_COLORS.underTu2};" title="<TU2: ${counts.underTu2||0}"></span>
        <span style="flex:${pct('tu2ToTu1')};background:${CLASS_COLORS.tu2ToTu1};" title="TU2→TU1: ${counts.tu2ToTu1||0}"></span>
        <span style="flex:${pct('tu1ToStd')};background:${CLASS_COLORS.tu1ToStd};" title="TU1→STD: ${counts.tu1ToStd||0}"></span>
        <span style="flex:${pct('stdToMax')};background:${CLASS_COLORS.stdToMax};" title="STD→Max: ${counts.stdToMax||0}"></span>
        <span style="flex:${pct('overMax')};background:${CLASS_COLORS.overMax};" title=">Max: ${counts.overMax||0}"></span>
    </div>
    <div class="stacked-legend">
        <span><i class="dot" style="background:${CLASS_COLORS.underTu2};"></i>&lt;TU2 (${counts.underTu2||0})</span>
        <span><i class="dot" style="background:${CLASS_COLORS.tu2ToTu1};"></i>TU2→TU1 (${counts.tu2ToTu1||0})</span>
        <span><i class="dot" style="background:${CLASS_COLORS.tu1ToStd};"></i>TU1→STD (${counts.tu1ToStd||0})</span>
        <span><i class="dot" style="background:${CLASS_COLORS.stdToMax};"></i>STD→Max (${counts.stdToMax||0})</span>
        <span><i class="dot" style="background:${CLASS_COLORS.overMax};"></i>&gt;Max (${counts.overMax||0})</span>
    </div>`;
    }

    function buildLineChart(canvasId, labels, datasets, yLines = []) {
        destroyChart(canvasId);
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        // Fix: set wrapper height agar canvas punya dimensi tetap
        const wrapper = ctx.parentElement;
        if (wrapper) {
            const fixedHeight = parseInt(ctx.getAttribute('height')) || 140;
            wrapper.style.position = 'relative';
            wrapper.style.height = fixedHeight + 'px';
        }

        const annotations = {};
        yLines.forEach((l, i) => {
            annotations[`line${i}`] = {
                type: 'line',
                yMin: l.v,
                yMax: l.v,
                borderColor: l.color,
                borderWidth: 1.5,
                borderDash: [4, 3],
                label: {
                    content: l.label,
                    display: true,
                    font: {
                        size: 9
                    },
                    position: 'end'
                }
            };
        });

        const instance = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // ← fix utama
                resizeDelay: 200, // ← cegah resize loop saat hover
                animation: {
                    duration: 300
                },
                plugins: {
                    legend: {
                        display: datasets.length > 1,
                        labels: {
                            font: {
                                size: 10
                            },
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    annotation: yLines.length ? {
                        annotations
                    } : {}
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 9
                            },
                            maxTicksLimit: 10,
                            maxRotation: 0
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 9
                            }
                        },
                        grid: {
                            color: '#f3f4f6'
                        }
                    }
                }
            }
        });
        registerChart(canvasId, instance);
    }

    function fmt(v, d = 2) {
        return v != null ? (+v).toFixed(d) : '—';
    }

    /* ══════════════════════════════════════════════════════════════════
       API CALLS
    ══════════════════════════════════════════════════════════════════ */
    async function apiFetch(url, params = {}) {
        const q = new URLSearchParams(Object.fromEntries(Object.entries(params).filter(([, v]) => v !== '' && v != null)));
        const res = await fetch(`${url}?${q}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    }

    /* ══════════════════════════════════════════════════════════════════
       FILTER OPTIONS — populate selects
    ══════════════════════════════════════════════════════════════════ */
    async function loadFilterOptions() {
        try {
            const data = await apiFetch('/api/timbangan-retail/filter-options');
            const variantSel = document.getElementById('s1-varian');
            const mesinSel = document.getElementById('s1-mesin');
            (data.variants || Object.keys(VARIANT_STANDARDS)).forEach(v => {
                [variantSel].forEach(s => {
                    const o = document.createElement('option');
                    o.value = v;
                    o.textContent = v;
                    s.appendChild(o);
                });
            });
            (data.mesins || Object.keys(MESIN_VARIAN).sort()).forEach(m => {
                const o = document.createElement('option');
                o.value = m;
                o.textContent = `Mesin ${m}`;
                mesinSel.appendChild(o);
            });
        } catch (e) {
            // Fallback: populate from constants
            const variantSel = document.getElementById('s1-varian');
            const mesinSel = document.getElementById('s1-mesin');
            Object.keys(VARIANT_STANDARDS).forEach(v => {
                const o = document.createElement('option');
                o.value = v;
                o.textContent = v;
                variantSel.appendChild(o);
            });
            Object.keys(MESIN_VARIAN).sort().forEach(m => {
                const o = document.createElement('option');
                o.value = m;
                o.textContent = `Mesin ${m}`;
                mesinSel.appendChild(o);
            });
        }
    }

    /* ══════════════════════════════════════════════════════════════════
       SLIDE 1 — Perbandingan Shift
    ══════════════════════════════════════════════════════════════════ */
    async function loadSlide1() {
        const params = {
            start_date: document.getElementById('s1-date-start').value,
            end_date: document.getElementById('s1-date-end').value,
            varian: document.getElementById('s1-varian').value,
            mesin: document.getElementById('s1-mesin').value,
        };

        // Show loaders
        [1, 2, 3].forEach(s => {
            document.getElementById(`s1-stacked-shift${s}`).innerHTML = '<div class="tr-loading"><div class="tr-spinner"></div></div>';
            destroyChart(`s1-line-shift${s}`);
        });
        document.getElementById('s1-summary-body').innerHTML =
            '<tr><td colspan="9" class="text-center py-3"><div class="tr-loading"><div class="tr-spinner"></div><span>Memuat...</span></div></td></tr>';

        try {
            const [avgData, chartData] = await Promise.all([
                apiFetch('/api/timbangan-retail/average-minmax', params),
                apiFetch('/api/timbangan-retail/chart', params),
            ]);

            const summaryRows = [];

            [1, 2, 3].forEach((shift, si) => {
                const shiftKey = `shift${shift}`;
                const shiftData = avgData[shiftKey] || {};
                const shiftChart = chartData[shiftKey] || {};

                // Build counts from chart data or API
                const counts = shiftData.counts || {
                    underTu2: 0,
                    tu2ToTu1: 0,
                    tu1ToStd: 0,
                    stdToMax: 0,
                    overMax: 0
                };
                const total = Object.values(counts).reduce((a, b) => a + b, 0);

                // Stacked bar
                document.getElementById(`s1-stacked-shift${shift}`).innerHTML = buildStackedBar(counts, total);

                // Line chart
                const samples = shiftChart.samples || [];
                const std = params.varian && VARIANT_STANDARDS[params.varian] ? VARIANT_STANDARDS[params.varian] : null;
                const yLines = std ? [{
                        v: std.max,
                        color: '#7e3af2',
                        label: 'Max'
                    },
                    {
                        v: std.std,
                        color: '#0e9f6e',
                        label: 'STD'
                    },
                    {
                        v: std.min,
                        color: '#1a56db',
                        label: 'Min'
                    },
                    {
                        v: std.tu1,
                        color: '#fbbf24',
                        label: 'TU1'
                    },
                    {
                        v: std.tu2,
                        color: '#e02424',
                        label: 'TU2'
                    },
                ] : [];

                buildLineChart(
                    `s1-line-shift${shift}`,
                    samples.map((_, i) => `#${i+1}`),
                    [{
                        label: `Shift ${shift}`,
                        data: samples.map(s => s.berat || s.weight || s),
                        borderColor: SHIFT_COLORS[si],
                        backgroundColor: SHIFT_COLORS[si] + '22',
                        pointRadius: 2,
                        borderWidth: 2,
                        tension: .35,
                        fill: true,
                    }],
                    yLines
                );

                // Summary row
                summaryRows.push({
                    shift,
                    color: SHIFT_COLORS[si],
                    min: shiftData.min,
                    avg: shiftData.avg || shiftData.average,
                    max: shiftData.max,
                    ...counts,
                });
            });

            // Render summary table
            document.getElementById('s1-summary-body').innerHTML = summaryRows.map(r => `
            <tr>
                <td><div class="shift-label"><span class="shift-dot" style="background:${r.color};"></span>Shift ${r.shift}</div></td>
                <td>${fmt(r.min)}</td>
                <td>${fmt(r.avg)}</td>
                <td>${fmt(r.max)}</td>
                <td><span class="badge ${r.underTu2>0?'badge-err':'badge-ok'}">${r.underTu2||0}</span></td>
                <td><span class="badge ${r.tu2ToTu1>0?'badge-warn':'badge-ok'}">${r.tu2ToTu1||0}</span></td>
                <td>${r.tu1ToStd||0}</td>
                <td>${r.stdToMax||0}</td>
                <td><span class="badge ${r.overMax>0?'badge-over':'badge-ok'}">${r.overMax||0}</span></td>
            </tr>
        `).join('');

        } catch (e) {
            console.error('Slide 1 error:', e);
            [1, 2, 3].forEach(s => {
                document.getElementById(`s1-stacked-shift${s}`).innerHTML =
                    '<div class="tr-empty"><i class="ri-error-warning-line"></i><span>Gagal memuat data</span></div>';
            });
            document.getElementById('s1-summary-body').innerHTML =
                '<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data — periksa koneksi API</td></tr>';
        }
    }

    /* ══════════════════════════════════════════════════════════════════
       SLIDE 2 — Report Mesin Per Shift  (FIXED: multi-variant per mesin)
    ══════════════════════════════════════════════════════════════════ */

    // Warna per variant — supaya mudah dibedakan di satu card
    const VARIANT_LINE_COLORS = [
        '#1a56db', '#0e9f6e', '#ff5a1f', '#7e3af2',
        '#0694a2', '#c27803', '#e02424', '#6b7280',
    ];

    async function loadSlide2() {
        const params = {
            start_date: document.getElementById('s2-date-start').value,
            end_date: document.getElementById('s2-date-end').value,
            shift: document.getElementById('s2-shift').value,
        };

        // ── Reset semua card ke loading state ─────────────────────────
        ALL_MESINS.forEach(m => {
            const stEl = document.getElementById(`s2-stacked-${m}`);
            if (stEl) stEl.innerHTML = '<div class="tr-loading" style="padding:12px 0;"><div class="tr-spinner" style="width:18px;height:18px;border-width:2px;"></div></div>';

            // Hancurkan semua chart variant lama untuk mesin ini
            const varianList = MESIN_VARIAN[m] || [];
            varianList.forEach(v => destroyChart(`s2-line-${m}-${slugify(v)}`));
            // Hapus juga canvas lama yang sudah di-inject
            const chartWrap = document.getElementById(`s2-charts-${m}`);
            if (chartWrap) chartWrap.innerHTML = '';

            const statsEl = document.getElementById(`s2-stats-${m}`);
            if (statsEl) statsEl.innerHTML = '<div>Min: <strong>—</strong></div><div>Max: <strong>—</strong></div><div>Avg: <strong>—</strong></div><div>Total: <strong>—</strong></div>';

            // Update label varian
            const varianEls = document.querySelectorAll(`.s2-mesin-varian-${m}`);
            varianEls.forEach(el => {
                el.textContent = (MESIN_VARIAN[m] || [])
                    .map(v => VARIANT_STANDARDS[v]?.code || v).join(', ') || '—';
            });
        });

        document.getElementById('s2-overview-body').innerHTML =
            '<tr><td colspan="6" class="text-center py-3"><div class="tr-loading"><div class="tr-spinner"></div><span>Memuat...</span></div></td></tr>';

        try {
            const [ovData, chartData] = await Promise.all([
                apiFetch('/api/timbangan-retail/average-minmax', params),
                apiFetch('/api/timbangan-retail/chart', params),
            ]);

            // ── OVERVIEW TABLE ──────────────────────────────────────────
            const variants = Object.keys(VARIANT_STANDARDS);
            const overviewRows = variants.map(v => {
                const vd = ovData.variants?.[v] || {};
                return `
                <tr>
                    <td>
                        <span class="fw-600" style="font-size:12px;">${v}</span><br>
                        <span style="font-size:10px;color:var(--tr-muted);">${VARIANT_STANDARDS[v].code}</span>
                    </td>
                    <td><span class="badge ${(vd.under||0)>0?'badge-err':'badge-ok'}">${vd.under||0}</span></td>
                    <td>${fmt(vd.min)}</td>
                    <td>${fmt(vd.avg||vd.average)}</td>
                    <td>${fmt(vd.max)}</td>
                    <td><span class="badge ${(vd.over||0)>0?'badge-over':'badge-ok'}">${vd.over||0}</span></td>
                </tr>`;
            });
            document.getElementById('s2-overview-body').innerHTML =
                overviewRows.join('') ||
                '<tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data</td></tr>';

            // ── PER MACHINE ───────────────────────────────────────────────
            ALL_MESINS.forEach(m => {
                // API response shape (baru):
                //   ovData.mesins[m]   = { variants: { "Pouch YB 250gr": {...stats}, ... }, combined: {...stats} }
                //   chartData.mesins[m] = { variants: { "Pouch YB 250gr": { samples:[...] }, ... } }
                const mStatsCombined = ovData.mesins?.[m]?.combined || {};
                const mStatsPerVar = ovData.mesins?.[m]?.variants || {};
                const mChartPerVar = chartData.mesins?.[m]?.variants || {};

                const total = mStatsCombined.total || 0;
                const counts = mStatsCombined.counts || {
                    underTu2: 0,
                    tu2ToTu1: 0,
                    tu1ToStd: 0,
                    stdToMax: 0,
                    overMax: 0
                };

                // 1) Stacked bar pakai combined counts
                const stEl = document.getElementById(`s2-stacked-${m}`);
                if (stEl) {
                    stEl.innerHTML = total > 0 ?
                        buildStackedBar(counts, total) :
                        '<div class="tr-empty" style="padding:10px 0;font-size:11px;"><i class="ri-bar-chart-line" style="font-size:16px;"></i><span>Tidak ada data</span></div>';
                }

                // 2) Mini stats pakai combined
                const statsEl = document.getElementById(`s2-stats-${m}`);
                if (statsEl) {
                    const varianList = MESIN_VARIAN[m] || [];
                    const hasMultiple = varianList.filter(v => mStatsPerVar[v]?.total > 0).length > 1;

                    if (!hasMultiple) {
                        // Mesin 1 variant — tampilan lama sudah cukup
                        const vd = mStatsPerVar[varianList[0]] || mStatsCombined;
                        statsEl.innerHTML = `
            <div>Min: <strong>${fmt(vd.min)}</strong></div>
            <div>Max: <strong>${fmt(vd.max)}</strong></div>
            <div>Avg: <strong>${fmt(vd.avg)}</strong></div>
            <div>Total: <strong>${vd.total ?? 0}</strong></div>
        `;
                    } else {
                        // Mesin multi-variant — tampilkan baris per variant
                        const rows = varianList
                            .filter(v => (mStatsPerVar[v]?.total || 0) > 0)
                            .map(v => {
                                const vd = mStatsPerVar[v];
                                const code = VARIANT_STANDARDS[v]?.code || v;
                                return `
                <tr>
                    <td style="padding:2px 6px 2px 0;font-weight:700;color:#111;white-space:nowrap;">${code}</td>
                    <td style="padding:2px 4px;color:var(--tr-muted);">Min <strong style="color:#111;">${fmt(vd.min)}</strong></td>
                    <td style="padding:2px 4px;color:var(--tr-muted);">Avg <strong style="color:#111;">${fmt(vd.avg)}</strong></td>
                    <td style="padding:2px 4px;color:var(--tr-muted);">Max <strong style="color:#111;">${fmt(vd.max)}</strong></td>
                    <td style="padding:2px 0 2px 4px;color:var(--tr-muted);">n=<strong style="color:#111;">${vd.total}</strong></td>
                </tr>`;
                            }).join('');

                        statsEl.style.display = 'block'; // override grid layout
                        statsEl.innerHTML = `
            <table style="width:100%;border-collapse:collapse;font-size:10px;">
                ${rows}
            </table>
            <div style="font-size:10px;color:var(--tr-muted);margin-top:3px;border-top:1px solid var(--tr-border);padding-top:3px;">
                Total gabungan: <strong style="color:#111;">${mStatsCombined.total ?? 0}</strong>
            </div>
        `;
                    }
                }

                // 3) Line chart — satu per variant yang punya data
                const chartWrap = document.getElementById(`s2-charts-${m}`);
                if (!chartWrap) return;

                chartWrap.innerHTML = ''; // bersihkan canvas lama

                const varianList = MESIN_VARIAN[m] || [];
                let colorIdx = 0;

                varianList.forEach(v => {
                    const samples = mChartPerVar[v]?.samples || [];
                    if (!samples.length) return; // skip variant tanpa data

                    const std = VARIANT_STANDARDS[v] || null;
                    const color = VARIANT_LINE_COLORS[colorIdx % VARIANT_LINE_COLORS.length];
                    const canvasId = `s2-line-${m}-${slugify(v)}`;
                    const code = std?.code || v;
                    colorIdx++;

                    // Buat wrapper + label + canvas
                    const wrap = document.createElement('div');
                    wrap.style.cssText = 'margin-bottom:10px;';
                    wrap.innerHTML = `
                        <div style="font-size:10px;font-weight:700;color:${color};margin-bottom:3px;text-transform:uppercase;letter-spacing:.4px;">
                            ${code}
                            <span style="font-weight:400;color:var(--tr-muted);font-size:9px;margin-left:6px;">
                                n=${samples.length} · avg=${fmt(mStatsPerVar[v]?.avg)}
                            </span>
                        </div>
                        <div style="position:relative;height:70px;">
                            <canvas id="${canvasId}" height="70"></canvas>
                        </div>
                    `;
                    chartWrap.appendChild(wrap);

                    // yLines sesuai standar variant ini
                    const yLines = std ? [{
                            v: std.max,
                            color: '#7e3af2',
                            label: 'Max'
                        },
                        {
                            v: std.min,
                            color: '#1a56db',
                            label: 'Min'
                        },
                    ] : [];

                    buildLineChart(
                        canvasId,
                        samples.map((_, i) => `#${i + 1}`),
                        [{
                            label: code,
                            data: samples.map(s => s.berat),
                            borderColor: color,
                            backgroundColor: color + '18',
                            pointRadius: 1,
                            borderWidth: 1.5,
                            tension: .35,
                            fill: true,
                        }],
                        yLines
                    );
                });

                // Jika tidak ada variant yang punya data
                if (chartWrap.innerHTML === '') {
                    chartWrap.innerHTML = '<div style="font-size:11px;color:var(--tr-muted);padding:4px 0;">Tidak ada data chart</div>';
                }
            });

        } catch (e) {
            console.error('Slide 2 error:', e);
            document.getElementById('s2-overview-body').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger py-3">Gagal memuat data — periksa koneksi API</td></tr>';
            ALL_MESINS.forEach(m => {
                const stEl = document.getElementById(`s2-stacked-${m}`);
                if (stEl) stEl.innerHTML = '<div class="tr-empty" style="padding:8px 0;font-size:11px;"><i class="ri-error-warning-line" style="font-size:14px;"></i><span>Error</span></div>';
            });
        }
    }

    // Helper: buat slug dari nama variant untuk dipakai sebagai canvas id
    function slugify(str) {
        return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }

    /* ══════════════════════════════════════════════════════════════════
       TAB SWITCHING
    ══════════════════════════════════════════════════════════════════ */
    function switchTab(tab) {
        document.getElementById('tab-slide1').style.display = tab === 'slide1' ? '' : 'none';
        document.getElementById('tab-slide2').style.display = tab === 'slide2' ? '' : 'none';
        document.querySelectorAll('.tr-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });
        if (tab === 'slide2') loadSlide2();
    }

    /* ══════════════════════════════════════════════════════════════════
       SET DEFAULT DATES
    ══════════════════════════════════════════════════════════════════ */
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        const weekAgo = new Date(Date.now() - 7 * 86400000).toISOString().split('T')[0];
        ['s1-date-start', 's2-date-start'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = weekAgo;
        });
        ['s1-date-end', 's2-date-end'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = today;
        });
    }

    /* ══════════════════════════════════════════════════════════════════
       INIT
    ══════════════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', async () => {
        setDefaultDates();
        await loadFilterOptions();
        loadSlide1();
    });
</script>
@endsection