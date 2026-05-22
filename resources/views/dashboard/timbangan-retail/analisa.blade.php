@extends('layouts.component.main')
@section('title', 'Analisa Abnormal & Traceability')

@section('styles')
<style>
    /* ── Velzon-compatible overrides & additions ── */
    .kpi-card {
        transition: box-shadow .18s, transform .18s;
    }

    .kpi-card:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, .10);
        transform: translateY(-2px);
    }

    .kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .kpi-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        margin: 4px 0 2px;
    }

    /* severity badges */
    .badge-kritis {
        background: #fde8e8;
        color: #c81e1e;
        font-weight: 700;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
        font-weight: 700;
    }

    .badge-over {
        background: #ede9fe;
        color: #5b21b6;
        font-weight: 700;
    }

    .badge-normal {
        background: #d1fae5;
        color: #065f46;
        font-weight: 700;
    }

    .badge-s1 {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-s2 {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-s3 {
        background: #ede9fe;
        color: #5b21b6;
    }

    /* row highlights */
    .row-kritis {
        background-color: #fde8e8 !important;
    }

    .row-warning {
        background-color: #fef9c3 !important;
    }

    .row-op-high {
        background-color: #fde8e8 !important;
    }

    .row-op-medium {
        background-color: #fef9c3 !important;
    }

    /* selisih */
    .sel-neg {
        color: #dc2626;
        font-weight: 600;
    }

    .sel-pos {
        color: #7c3aed;
        font-weight: 600;
    }

    /* chart wrapper */
    .chart-container {
        position: relative;
        min-height: 280px;
    }

    .chart-container canvas {
        width: 100% !important;
    }

    /* spinner overlay */
    .section-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 48px;
        color: var(--vz-secondary);
        font-size: .85rem;
    }

    /* table scroll */
    .tbl-scroll {
        max-height: 420px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .tbl-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--vz-table-bg, #fff);
    }

    /* heatmap legend */
    .heatmap-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 8px;
        font-size: .78rem;
        color: var(--vz-secondary);
    }

    .heatmap-legend span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .heatmap-legend i {
        width: 11px;
        height: 11px;
        border-radius: 2px;
        display: inline-block;
    }

    /* pagination */
    .aa-pager-info {
        font-size: .82rem;
        color: var(--vz-secondary);
    }

    /* section separator */
    .section-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--vz-secondary);
        margin-bottom: 12px;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--vz-border-color);
    }

    /* filter card */
    .filter-card .form-label {
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    /* stagger animation */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anim-in {
        animation: fadeUp .3s ease forwards;
    }

    .anim-in:nth-child(1) {
        animation-delay: .05s;
    }

    .anim-in:nth-child(2) {
        animation-delay: .10s;
    }

    .anim-in:nth-child(3) {
        animation-delay: .15s;
    }

    .anim-in:nth-child(4) {
        animation-delay: .20s;
    }

    .anim-in:nth-child(5) {
        animation-delay: .25s;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- ── PAGE TITLE ──────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Analisa Abnormal &amp; Traceability</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Timbangan Retail</a></li>
                            <li class="breadcrumb-item active">Analisa Abnormal</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 1 — FILTER BAR
    ════════════════════════════════════════════════════════ --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-sm-4 col-lg-2">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" id="f_start" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('-6 days')) }}">
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" id="f_end" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <label class="form-label">Shift</label>
                        <select id="f_shift" class="form-select form-select-sm">
                            <option value="">Semua Shift</option>
                            <option value="1">Shift 1 (06:00–13:59)</option>
                            <option value="2">Shift 2 (14:00–21:59)</option>
                            <option value="3">Shift 3 (22:00–05:59)</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-3">
                        <label class="form-label">Varian</label>
                        <select id="f_varian" class="form-select form-select-sm">
                            <option value="">Semua Varian</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <label class="form-label">Mesin</label>
                        <select id="f_mesin" class="form-select form-select-sm">
                            <option value="">Semua Mesin</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-1">
                        <button class="btn btn-primary btn-sm w-100" id="btnTampilkan" style="padding-top:7px;padding-bottom:7px;">
                            <i class="ri-search-2-line me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 2 — KPI CARDS
    ════════════════════════════════════════════════════════ --}}
        <p class="section-label"><i class="ri-dashboard-3-line"></i> Ringkasan Periode</p>
        <div class="row g-3 mb-3" id="kpiGrid">
            {{-- KPI: Total Sampel --}}
            <div class="col-6 col-sm-4 col-xl anim-in">
                <div class="card mb-0 kpi-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon-box bg-soft-primary text-primary">
                            <i class="ri-archive-line"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Total Sampel</p>
                            <div class="kpi-value text-body" id="kpi-total">—</div>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Seluruh timbangan</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- KPI: Total Abnormal --}}
            <div class="col-6 col-sm-4 col-xl anim-in">
                <div class="card mb-0 kpi-card border-warning border-opacity-50">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon-box bg-soft-warning text-warning">
                            <i class="ri-alert-line"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Total Abnormal</p>
                            <div class="kpi-value text-warning" id="kpi-abnormal">—</div>
                            <p class="text-muted mb-0" style="font-size:.75rem;" id="kpi-pct">— % dari total</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- KPI: Kritis --}}
            <div class="col-6 col-sm-4 col-xl anim-in">
                <div class="card mb-0 kpi-card border-danger border-opacity-50">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon-box bg-soft-danger text-danger">
                            <i class="ri-error-warning-line"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Kritis (&lt;TU2)</p>
                            <div class="kpi-value text-danger" id="kpi-kritis">—</div>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Berat sangat kurang</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- KPI: Warning --}}
            <div class="col-6 col-sm-4 col-xl anim-in">
                <div class="card mb-0 kpi-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon-box" style="background:#fef3c7;color:#92400e;">
                            <i class="ri-alert-fill"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Warning (TU2→TU1)</p>
                            <div class="kpi-value" style="color:#d97706;" id="kpi-warning">—</div>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Di bawah TU1</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- KPI: Over --}}
            <div class="col-6 col-sm-4 col-xl anim-in">
                <div class="card mb-0 kpi-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon-box" style="background:#ede9fe;color:#7c3aed;">
                            <i class="ri-arrow-up-line"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Over (&gt;Max)</p>
                            <div class="kpi-value" style="color:#9333ea;" id="kpi-over">—</div>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Melebihi batas max</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 3 — PARETO CHARTS
    ════════════════════════════════════════════════════════ --}}
        <p class="section-label"><i class="ri-bar-chart-grouped-line"></i> Pareto Abnormal</p>
        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0">
                            <i class="ri-settings-5-line text-primary me-1"></i> Pareto per Mesin
                        </h6>
                        <span class="badge bg-light text-muted fw-normal" style="font-size:.72rem;">Top 10 kontributor</span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" id="paretoMesinWrap">
                            <div class="section-loading">
                                <div class="spinner-border spinner-border-sm text-primary"></div> Memuat data…
                            </div>
                            <canvas id="chartParetoMesin" style="display:none;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0">
                            <i class="ri-price-tag-3-line text-primary me-1"></i> Pareto per Varian
                        </h6>
                        <span class="badge bg-light text-muted fw-normal" style="font-size:.72rem;">Top 10 kontributor</span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" id="paretoVariantWrap">
                            <div class="section-loading">
                                <div class="spinner-border spinner-border-sm text-primary"></div> Memuat data…
                            </div>
                            <canvas id="chartParetoVariant" style="display:none;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 4 — HEATMAP JAM
    ════════════════════════════════════════════════════════ --}}
        <p class="section-label"><i class="ri-time-line"></i> Distribusi Abnormal per Jam (24h)</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="card-title mb-0">
                    <i class="ri-bar-chart-2-line text-primary me-1"></i> Heatmap % Abnormal per Jam
                </h6>
                <div class="heatmap-legend">
                    <span><i style="background:#3b82f6;"></i> Shift 1 (06–13)</span>
                    <span><i style="background:#f59e0b;"></i> Shift 2 (14–21)</span>
                    <span><i style="background:#8b5cf6;"></i> Shift 3 (22–05)</span>
                    <span class="ms-3"><i style="background:#16a34a;"></i> ≤5% OK</span>
                    <span><i style="background:#d97706;"></i> 5–15% Warning</span>
                    <span><i style="background:#dc2626;"></i> &gt;15% Kritis</span>
                </div>
            </div>
            <div class="card-body">
                <div id="heatmapWrap" style="position:relative;min-height:240px;">
                    <div class="section-loading">
                        <div class="spinner-border spinner-border-sm text-primary"></div> Memuat heatmap…
                    </div>
                    <canvas id="chartHeatmap" style="display:none;height:240px;"></canvas>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 5 — RANKING OPERATOR
    ════════════════════════════════════════════════════════ --}}
        <p class="section-label"><i class="ri-user-3-line"></i> Ranking Operator / NIK</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="card-title mb-0">
                    <i class="ri-group-line text-primary me-1"></i> Performa Operator
                </h6>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <span class="badge badge-kritis py-1 px-2">&gt;10% = Highlight Merah</span>
                    <span class="badge badge-warning py-1 px-2">&gt;5% = Highlight Kuning</span>
                    <span class="badge bg-soft-primary text-primary fw-semibold" id="opTotal"></span>
                </div>
            </div>
            <div id="opTableWrap">
                <div class="section-loading">
                    <div class="spinner-border spinner-border-sm text-primary"></div> Memuat data operator…
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         BAGIAN 6 — AUDIT TRAIL LOG
    ════════════════════════════════════════════════════════ --}}
        <p class="section-label"><i class="ri-file-list-3-line"></i> Audit Trail Log Abnormal</p>
        <div class="card mb-4">
            {{-- Sub-filter --}}
            <div class="card-header bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">NIK Operator</label>
                        <input type="text" id="lf_nik" class="form-control form-control-sm" placeholder="Cari NIK…">
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Severity</label>
                        <select id="lf_severity" class="form-select form-select-sm">
                            <option value="">Semua Severity</option>
                            <option value="kritis">Kritis</option>
                            <option value="warning">Warning</option>
                            <option value="over">Over</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Per Halaman</label>
                        <select id="lf_perpage" class="form-select form-select-sm">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary btn-sm" id="btnLogSearch">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                        <button class="btn btn-light btn-sm ms-1" id="btnLogReset">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div id="logTableWrap">
                <div class="section-loading">
                    <div class="spinner-border spinner-border-sm text-primary"></div> Memuat log…
                </div>
            </div>

            {{-- Pagination --}}
            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2" id="logPagination" style="display:none!important;">
                <span class="aa-pager-info" id="logPaginationInfo"></span>
                <ul class="pagination pagination-sm mb-0" id="logPageBtns"></ul>
            </div>
        </div>

    </div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
<script>
    'use strict';

    /* ── Chart registry ─────────────────────────────────────── */
    const _charts = {};

    /* ── State ───────────────────────────────────────────────── */
    let _logPage = 1;

    /* ── Helpers ─────────────────────────────────────────────── */
    const fmt2 = n => (n == null ? '—' : parseFloat(n).toFixed(2));
    const fmt1 = n => (n == null ? '—' : parseFloat(n).toFixed(1));
    const fmtInt = n => (n == null ? '—' : parseInt(n).toLocaleString('id-ID'));

    function destroyChart(key) {
        if (_charts[key]) {
            _charts[key].destroy();
            delete _charts[key];
        }
    }

    async function apiFetch(url) {
        const r = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
    }

    function baseParams() {
        const p = new URLSearchParams();
        const sd = document.getElementById('f_start').value;
        const ed = document.getElementById('f_end').value;
        if (!sd || !ed) return null;
        p.append('start_date', sd);
        p.append('end_date', ed);
        const sh = document.getElementById('f_shift').value;
        const va = document.getElementById('f_varian').value;
        const me = document.getElementById('f_mesin').value;
        if (sh) p.append('shift', sh);
        if (va) p.append('varian', va);
        if (me) p.append('mesin', me);
        return p;
    }

    function spinnerHTML(msg = 'Memuat data…') {
        return `<div class="section-loading"><div class="spinner-border spinner-border-sm text-primary"></div> ${msg}</div>`;
    }

    function emptyHTML(msg = 'Tidak ada data untuk filter ini') {
        return `<div class="py-5 text-center text-muted"><i class="ri-inbox-line fs-2 d-block mb-2 opacity-50"></i>${msg}</div>`;
    }

    function errHTML(msg = 'Gagal memuat data. Periksa koneksi.') {
        return `<div class="py-4 text-center text-danger"><i class="ri-wifi-off-line fs-2 d-block mb-2"></i>${msg}</div>`;
    }

    /* ══════════════════════════════════════════════════════════
       FILTER OPTIONS
    ══════════════════════════════════════════════════════════ */
    async function loadFilterOptions() {
        try {
            const data = await apiFetch('/api/timbangan-retail/filter-options');
            const selV = document.getElementById('f_varian');
            const selM = document.getElementById('f_mesin');
            (data.variants || []).forEach(v => {
                const o = new Option(v, v);
                selV.appendChild(o);
            });
            (data.mesins || []).forEach(m => {
                const o = new Option(`Mesin ${m}`, m);
                selM.appendChild(o);
            });
        } catch (e) {
            console.warn('Filter options gagal:', e);
        }
    }

    /* ══════════════════════════════════════════════════════════
       BAGIAN 2 — KPI
    ══════════════════════════════════════════════════════════ */
    async function loadSummary(params) {
        try {
            const d = await apiFetch(`/api/timbangan-retail/abnormal-summary?${params}`);
            document.getElementById('kpi-total').textContent = fmtInt(d.total_sampel);
            document.getElementById('kpi-abnormal').textContent = fmtInt(d.total_abnormal);
            document.getElementById('kpi-pct').textContent = `${fmt1(d.pct_abnormal)}% dari total`;
            document.getElementById('kpi-kritis').textContent = fmtInt(d.kritis);
            document.getElementById('kpi-warning').textContent = fmtInt(d.warning);
            document.getElementById('kpi-over').textContent = fmtInt(d.over);
        } catch (e) {
            ['kpi-total', 'kpi-abnormal', 'kpi-kritis', 'kpi-warning', 'kpi-over']
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = 'Err';
            });
        }
    }

    /* ══════════════════════════════════════════════════════════
       BAGIAN 3 — PARETO
    ══════════════════════════════════════════════════════════ */
    function buildParetoChart(canvasId, wrapId, labels, counts, cumulatives) {
        const wrap = document.getElementById(wrapId);
        const canvas = document.getElementById(canvasId);
        destroyChart(canvasId);
        wrap.querySelector('.section-loading') && (wrap.querySelector('.section-loading').style.display = 'none');

        if (!labels.length) {
            canvas.style.display = 'none';
            wrap.insertAdjacentHTML('beforeend', emptyHTML('Tidak ada data abnormal'));
            return;
        }
        canvas.style.display = '';
        wrap.querySelectorAll('.py-5').forEach(e => e.remove());

        _charts[canvasId] = new Chart(canvas.getContext('2d'), {
            data: {
                labels,
                datasets: [{
                        type: 'bar',
                        label: 'Jml Abnormal',
                        data: counts,
                        backgroundColor: 'rgba(59,130,246,.75)',
                        borderRadius: 4,
                        yAxisID: 'yCount',
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: '% Kumulatif',
                        data: cumulatives,
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: .3,
                        pointRadius: 4,
                        pointBackgroundColor: '#ef4444',
                        yAxisID: 'yPct',
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 11
                            },
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    annotation: {
                        annotations: {
                            line80: {
                                type: 'line',
                                yScaleID: 'yPct',
                                yMin: 80,
                                yMax: 80,
                                borderColor: '#f59e0b',
                                borderWidth: 1.5,
                                borderDash: [5, 4],
                                label: {
                                    content: '80%',
                                    display: true,
                                    position: 'end',
                                    font: {
                                        size: 10
                                    },
                                    color: '#f59e0b',
                                    backgroundColor: 'transparent'
                                }
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.type === 'line' ?
                                `  Kumulatif: ${ctx.parsed.y}%` :
                                `  Abnormal: ${ctx.parsed.y}`
                        }
                    }
                },
                scales: {
                    yCount: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah',
                            font: {
                                size: 11
                            }
                        }
                    },
                    yPct: {
                        type: 'linear',
                        position: 'right',
                        min: 0,
                        max: 100,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: v => v + '%'
                        },
                        title: {
                            display: true,
                            text: '%',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            maxRotation: 40
                        }
                    }
                }
            }
        });
    }

    async function loadPareto(params) {
        ['paretoMesinWrap', 'paretoVariantWrap'].forEach(id => {
            const w = document.getElementById(id);
            w.querySelector('canvas').style.display = 'none';
            w.querySelectorAll('.py-5,.py-4').forEach(e => e.remove());
            let sp = w.querySelector('.section-loading');
            if (!sp) {
                w.insertAdjacentHTML('afterbegin', spinnerHTML());
            } else sp.style.display = 'flex';
        });
        try {
            const d = await apiFetch(`/api/timbangan-retail/abnormal-summary?${params}`);
            const pm = d.pareto_mesin || [];
            const pv = d.pareto_variant || [];
            buildParetoChart('chartParetoMesin', 'paretoMesinWrap',
                pm.map(r => `M-${r.mesin}`), pm.map(r => r.count), pm.map(r => r.cumulative));
            buildParetoChart('chartParetoVariant', 'paretoVariantWrap',
                pv.map(r => r.code || r.variant), pv.map(r => r.count), pv.map(r => r.cumulative));
        } catch (e) {
            document.getElementById('paretoMesinWrap').innerHTML = errHTML('Gagal memuat pareto mesin');
            document.getElementById('paretoVariantWrap').innerHTML = errHTML('Gagal memuat pareto varian');
        }
    }

    /* ══════════════════════════════════════════════════════════
       BAGIAN 4 — HEATMAP
    ══════════════════════════════════════════════════════════ */
    function heatColor(pct) {
        if (pct > 15) return '#dc2626';
        if (pct > 5) return '#d97706';
        return '#16a34a';
    }

    async function loadHeatmap() {
        const wrap = document.getElementById('heatmapWrap');
        const canvas = document.getElementById('chartHeatmap');
        canvas.style.display = 'none';
        wrap.querySelectorAll('.py-5,.py-4').forEach(e => e.remove());
        let sp = wrap.querySelector('.section-loading');
        if (!sp) {
            wrap.insertAdjacentHTML('afterbegin', spinnerHTML('Memuat heatmap…'));
        } else sp.style.display = 'flex';

        const hp = new URLSearchParams();
        hp.append('start_date', document.getElementById('f_start').value);
        hp.append('end_date', document.getElementById('f_end').value);
        const va = document.getElementById('f_varian').value;
        const me = document.getElementById('f_mesin').value;
        if (va) hp.append('varian', va);
        if (me) hp.append('mesin', me);

        try {
            const d = await apiFetch(`/api/timbangan-retail/hourly-heatmap?${hp}`);
            const rows = d.data || [];
            destroyChart('chartHeatmap');
            const s = wrap.querySelector('.section-loading');
            if (s) s.style.display = 'none';

            if (!rows.length) {
                canvas.style.display = 'none';
                wrap.insertAdjacentHTML('beforeend', emptyHTML('Tidak ada data heatmap'));
                return;
            }
            canvas.style.display = '';

            _charts['chartHeatmap'] = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: rows.map(r => r.jam),
                    datasets: [{
                        label: '% Abnormal',
                        data: rows.map(r => r.pct_abnormal),
                        backgroundColor: rows.map(r => heatColor(r.pct_abnormal)),
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                w5: {
                                    type: 'line',
                                    yMin: 5,
                                    yMax: 5,
                                    borderColor: '#d97706',
                                    borderWidth: 1,
                                    borderDash: [4, 3]
                                },
                                w15: {
                                    type: 'line',
                                    yMin: 15,
                                    yMax: 15,
                                    borderColor: '#dc2626',
                                    borderWidth: 1,
                                    borderDash: [4, 3]
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `  ${fmt1(ctx.parsed.y)}% abnormal`,
                                afterLabel: ctx => {
                                    const r = rows[ctx.dataIndex];
                                    return [`  Total: ${r.total}`, `  Abnormal: ${r.abnormal}`, `  ${r.shift}`];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                callback: v => v + '%',
                                font: {
                                    size: 11
                                }
                            },
                            title: {
                                display: true,
                                text: '% Abnormal',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                maxRotation: 45
                            }
                        }
                    }
                }
            });
        } catch (e) {
            document.getElementById('heatmapWrap').innerHTML = errHTML('Gagal memuat heatmap jam');
        }
    }

    /* ══════════════════════════════════════════════════════════
       BAGIAN 5 — OPERATOR
    ══════════════════════════════════════════════════════════ */
    async function loadOperator(params) {
        const wrap = document.getElementById('opTableWrap');
        wrap.innerHTML = spinnerHTML('Memuat data operator…');
        try {
            const d = await apiFetch(`/api/timbangan-retail/operator-stats?${params}`);
            const rows = d.data || [];
            document.getElementById('opTotal').textContent = `${d.total_operator} Operator`;

            if (!rows.length) {
                wrap.innerHTML = emptyHTML('Tidak ada data operator pada periode ini');
                return;
            }

            let html = `<div class="tbl-scroll">
        <table class="table table-hover table-sm table-nowrap align-middle mb-0">
        <thead class="table-light">
        <tr>
            <th style="width:36px;">#</th>
            <th>NIK</th>
            <th class="text-end">Total</th>
            <th class="text-end">Abnormal</th>
            <th class="text-end">%</th>
            <th class="text-center">Kritis</th>
            <th class="text-center">Warning</th>
            <th class="text-center">Over</th>
            <th>Mesin</th>
            <th>Terakhir Input</th>
        </tr></thead><tbody>`;

            rows.forEach((r, i) => {
                const pct = parseFloat(r.pct_abnormal);
                const rowCls = pct > 10 ? 'row-op-high' : pct > 5 ? 'row-op-medium' : '';
                const pctHtml = pct > 10 ?
                    `<span class="fw-bold text-danger">${fmt1(pct)}%</span>` :
                    pct > 5 ?
                    `<span class="fw-bold" style="color:#d97706;">${fmt1(pct)}%</span>` :
                    `<span class="text-success fw-semibold">${fmt1(pct)}%</span>`;

                const lastSeen = r.last_seen ?
                    new Date(r.last_seen).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    '—';

                html += `<tr class="${rowCls}">
                <td class="text-muted" style="font-size:.78rem;">${i + 1}</td>
                <td><span class="fw-semibold font-monospace">${r.nik ?? '—'}</span></td>
                <td class="text-end">${fmtInt(r.total)}</td>
                <td class="text-end fw-semibold">${fmtInt(r.abnormal)}</td>
                <td class="text-end">${pctHtml}</td>
                <td class="text-center"><span class="badge badge-kritis">${r.kritis ?? 0}</span></td>
                <td class="text-center"><span class="badge badge-warning">${r.warning ?? 0}</span></td>
                <td class="text-center"><span class="badge badge-over">${r.over ?? 0}</span></td>
                <td class="text-muted" style="font-size:.8rem;">${r.mesins ?? '—'}</td>
                <td class="text-muted" style="font-size:.78rem;">${lastSeen}</td>
            </tr>`;
            });

            html += '</tbody></table></div>';
            wrap.innerHTML = html;
        } catch (e) {
            wrap.innerHTML = errHTML('Gagal memuat data operator');
        }
    }

    /* ══════════════════════════════════════════════════════════
       BAGIAN 6 — LOG ABNORMAL
    ══════════════════════════════════════════════════════════ */
    function shiftBadge(shift) {
        const s = String(shift ?? '');
        if (s.includes('1')) return `<span class="badge badge-s1">Shift 1</span>`;
        if (s.includes('2')) return `<span class="badge badge-s2">Shift 2</span>`;
        return `<span class="badge badge-s3">Shift 3</span>`;
    }

    function sevBadge(sev) {
        const map = {
            kritis: 'badge-kritis',
            warning: 'badge-warning',
            over: 'badge-over'
        };
        return `<span class="badge ${map[sev] || 'badge-normal'}">${sev ?? '—'}</span>`;
    }

    function selisihCell(val) {
        if (val == null) return '<td class="text-muted">—</td>';
        const n = parseFloat(val);
        const sign = n >= 0 ? '+' : '';
        const cls = n >= 0 ? 'sel-pos' : 'sel-neg';
        return `<td class="${cls}">${sign}${fmt2(n)}</td>`;
    }

    async function loadLog(page = 1) {
        _logPage = page;
        const wrap = document.getElementById('logTableWrap');
        const pgEl = document.getElementById('logPagination');
        wrap.innerHTML = spinnerHTML('Memuat log…');
        pgEl.style.setProperty('display', 'none', 'important');

        const p = baseParams();
        if (!p) {
            wrap.innerHTML = emptyHTML('Pilih rentang tanggal terlebih dahulu');
            return;
        }
        const nik = document.getElementById('lf_nik').value.trim();
        const sev = document.getElementById('lf_severity').value;
        const pp = document.getElementById('lf_perpage').value;
        if (nik) p.append('nik', nik);
        if (sev) p.append('severity', sev);
        p.append('per_page', pp);
        p.append('page', page);

        try {
            const d = await apiFetch(`/api/timbangan-retail/abnormal-log?${p}`);
            const rows = d.data || [];

            if (!rows.length) {
                wrap.innerHTML = emptyHTML('Tidak ada log abnormal untuk filter ini');
                return;
            }

            let html = `<div class="tbl-scroll">
        <table class="table table-hover table-sm table-nowrap align-middle mb-0">
        <thead class="table-light"><tr>
            <th>Waktu</th>
            <th>NIK</th>
            <th>Mesin</th>
            <th>Varian</th>
            <th>Shift</th>
            <th class="text-end">Berat (g)</th>
            <th class="text-end">STD (g)</th>
            <th class="text-end">Selisih</th>
            <th class="text-end">Min</th>
            <th class="text-end">Max</th>
            <th class="text-center">Severity</th>
            <th class="text-center">Status</th>
        </tr></thead><tbody>`;

            rows.forEach(r => {
                const waktu = r.waktu ?
                    new Date(r.waktu).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }) :
                    '—';

                const statusBadge = r.status === 'OK' ?
                    `<span class="badge bg-success-subtle text-success fw-semibold">OK</span>` :
                    `<span class="badge bg-danger-subtle text-danger fw-semibold">NOT OK</span>`;

                const rowCls = r.severity === 'kritis' ? 'row-kritis' : r.severity === 'warning' ? 'row-warning' : '';

                html += `<tr class="${rowCls}">
                <td style="font-size:.78rem;">${waktu}</td>
                <td><span class="fw-semibold font-monospace" style="font-size:.82rem;">${r.nik ?? '—'}</span></td>
                <td><span class="fw-bold">${r.mesin ?? '—'}</span></td>
                <td style="max-width:140px;white-space:normal;line-height:1.3;font-size:.78rem;">
                    <span class="text-muted" style="font-size:.7rem;">[${r.variant_code ?? '?'}]</span><br>
                    ${r.variant ?? '—'}
                </td>
                <td>${shiftBadge(r.shift)}</td>
                <td class="text-end fw-bold">${fmt2(r.berat)}</td>
                <td class="text-end text-muted">${r.std_value != null ? fmt2(r.std_value) : '—'}</td>
                ${selisihCell(r.selisih)}
                <td class="text-end text-muted" style="font-size:.8rem;">${r.batas_min != null ? fmt2(r.batas_min) : '—'}</td>
                <td class="text-end text-muted" style="font-size:.8rem;">${r.batas_max != null ? fmt2(r.batas_max) : '—'}</td>
                <td class="text-center">${sevBadge(r.severity)}</td>
                <td class="text-center">${statusBadge}</td>
            </tr>`;
            });

            html += '</tbody></table></div>';
            wrap.innerHTML = html;
            renderPagination(d);
        } catch (e) {
            wrap.innerHTML = errHTML('Gagal memuat log abnormal. Periksa koneksi.');
        }
    }

    function renderPagination(d) {
        const {
            total,
            page,
            per_page,
            total_pages
        } = d;
        const pgEl = document.getElementById('logPagination');
        if (!total) {
            pgEl.style.setProperty('display', 'none', 'important');
            return;
        }

        pgEl.style.removeProperty('display');
        pgEl.style.display = 'flex';

        document.getElementById('logPaginationInfo').textContent =
            `Menampilkan ${((page-1)*per_page)+1}–${Math.min(page*per_page, total)} dari ${total.toLocaleString('id-ID')} log`;

        const btns = document.getElementById('logPageBtns');
        let pages = [1];
        if (page > 3) pages.push('…');
        for (let i = Math.max(2, page - 1); i <= Math.min(total_pages - 1, page + 1); i++) pages.push(i);
        if (page < total_pages - 2) pages.push('…');
        if (total_pages > 1) pages.push(total_pages);

        let html = `<li class="page-item ${page<=1?'disabled':''}">
        <button class="page-link" data-page="${page-1}"><i class="ri-arrow-left-s-line"></i></button></li>`;

        pages.forEach(p => {
            if (p === '…') {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            } else {
                html += `<li class="page-item ${p===page?'active':''}">
                <button class="page-link" data-page="${p}">${p}</button></li>`;
            }
        });

        html += `<li class="page-item ${page>=total_pages?'disabled':''}">
        <button class="page-link" data-page="${page+1}"><i class="ri-arrow-right-s-line"></i></button></li>`;

        btns.innerHTML = html;
        btns.querySelectorAll('.page-link[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                const p = parseInt(this.dataset.page);
                if (!isNaN(p) && p !== _logPage) loadLog(p);
            });
        });
    }

    /* ══════════════════════════════════════════════════════════
       LOAD ALL
    ══════════════════════════════════════════════════════════ */
    async function loadAll() {
        const p = baseParams();
        if (!p) {
            alert('Silakan pilih rentang tanggal terlebih dahulu.');
            return;
        }
        _logPage = 1;
        await Promise.all([loadSummary(p), loadPareto(p), loadHeatmap(), loadOperator(p), loadLog(1)]);
    }

    /* ══════════════════════════════════════════════════════════
       EVENTS & INIT
    ══════════════════════════════════════════════════════════ */
    document.getElementById('btnTampilkan').addEventListener('click', loadAll);
    document.getElementById('btnLogSearch').addEventListener('click', () => loadLog(1));
    document.getElementById('btnLogReset').addEventListener('click', () => {
        document.getElementById('lf_nik').value = '';
        document.getElementById('lf_severity').value = '';
        document.getElementById('lf_perpage').value = '50';
        loadLog(1);
    });
    document.querySelectorAll('#f_start, #f_end').forEach(el => {
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter') loadAll();
        });
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadFilterOptions();
        loadAll();
    });
</script>
@endsection