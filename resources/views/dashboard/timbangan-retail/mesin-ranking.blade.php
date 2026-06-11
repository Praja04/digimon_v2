<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Mesin — Timbangan Retail</title>
    <meta name="description" content="Dashboard ranking mesin berdasarkan output di luar standar timbangan retail">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-main: #f0f2f7;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1, #7c3aed);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100vh;
            overflow: hidden;
            font-family: var(--font-family);
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ── LAYOUT ───────────────────────────────────── */
        .tv-layout {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 12px 16px 10px;
            gap: 10px;
        }

        /* ── TOP BAR ──────────────────────────────────── */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
            background: var(--card-bg);
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.03);
        }
        .top-bar h1 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .top-bar h1 .accent {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Live Badge & Indicators */
        .live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: var(--success);
            font-size: .68rem;
            font-weight: 700;
        }
        .live-badge .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--success);
            animation: blink 2s ease infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: .3; }
        }

        /* Mode Toggle Container */
        .mode-toggle-container {
            display: flex;
            background: #e2e8f0;
            padding: 3px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }
        .toggle-btn {
            border: none;
            background: transparent;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            cursor: pointer;
            color: #475569;
            transition: all 0.2s ease;
            font-family: var(--font-family);
        }
        .toggle-btn.active {
            background: #fff;
            color: var(--text-main);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* ── VIEW CONTAINERS ──────────────────────────── */
        .view-container {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── MANUAL MODE: FILTER & KPI ───────────────── */
        .filter-kpi-row {
            display: flex;
            gap: 10px;
            align-items: stretch;
            flex-shrink: 0;
        }
        .filter-card {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            flex-shrink: 0;
        }
        .fg {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .fg label {
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
        }
        .fg input, .fg select {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: #334155;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: .72rem;
            font-family: var(--font-family);
            outline: none;
            width: 110px;
        }
        .fg select { width: 130px; }
        .fg input:focus, .fg select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 2px rgba(99,102,241,.1);
        }
        .btn-go {
            padding: 6px 16px;
            border: none;
            border-radius: 8px;
            background: var(--primary-gradient);
            color: #fff;
            font-weight: 700;
            font-size: .72rem;
            font-family: var(--font-family);
            cursor: pointer;
            transition: transform .1s, box-shadow .1s;
            white-space: nowrap;
            height: 32px;
            align-self: flex-end;
        }
        .btn-go:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(99,102,241,.25); }
        .btn-go:active { transform: scale(.97); }

        .kpi-strip {
            display: flex;
            gap: 8px;
            flex: 1;
        }
        .kpi {
            flex: 1;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 10px 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .kpi::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .kpi.c1::before { background: var(--primary); }
        .kpi.c2::before { background: #3b82f6; }
        .kpi.c3::before { background: var(--danger); }
        .kpi.c4::before { background: var(--warning); }
        .kpi.c5::before { background: var(--success); }
        .kpi-lbl {
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--text-muted);
        }
        .kpi-val {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.1;
        }
        .kpi.c1 .kpi-val { color: var(--primary); }
        .kpi.c2 .kpi-val { color: #3b82f6; }
        .kpi.c3 .kpi-val { color: var(--danger); }
        .kpi.c4 .kpi-val { color: var(--warning); }
        .kpi.c5 .kpi-val { color: var(--success); }
        .kpi-sub {
            font-size: .6rem;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ── MANUAL MODE: MAIN GRID ──────────────────── */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 10px;
            flex: 1;
            min-height: 0;
        }
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }
        .card-hdr {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .card-hdr h3 {
            font-size: .78rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .card-body {
            flex: 1;
            min-height: 0;
            position: relative;
            padding: 8px 12px;
        }
        .card-body canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* ── TV MODE: SIDE-BY-SIDE GRID ──────────────── */
        .tv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            flex: 1;
            min-height: 0;
        }
        .tv-col {
            display: flex;
            flex-direction: column;
            background: var(--card-bg);
            border-radius: 14px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0,0,0,.03);
            overflow: hidden;
            min-height: 0;
        }
        .tv-col-hdr {
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .bg-prev { background: #f8fafc; }
        .bg-curr { background: #f0fdf4; border-bottom: 1px solid #dcfce7; }
        .shift-title { display: flex; align-items: center; gap: 8px; }
        .shift-title h2 { font-size: 0.95rem; font-weight: 850; color: var(--text-main); }
        .shift-meta { display: flex; align-items: center; gap: 6px; font-size: 0.68rem; font-weight: 600; color: var(--text-muted); }
        .shift-meta .separator { color: #cbd5e1; }
        
        .badge { font-size: 0.58rem; font-weight: 800; text-transform: uppercase; padding: 3px 8px; border-radius: 20px; letter-spacing: 0.05em; }
        .badge-prev { background: #e2e8f0; color: #475569; }
        .badge-curr { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        .tv-kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            padding: 8px 12px 4px;
            background: #fafbfc;
            flex-shrink: 0;
        }
        .tv-kpi-row-secondary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            padding: 0 12px 8px;
            background: #fafbfc;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .tv-kpi {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 6px 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .tv-kpi-val { font-size: 1.15rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
        .tv-kpi-lbl { font-size: 0.52rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted); margin-top: 2px; }
        
        .warning-kpi .tv-kpi-val { color: var(--warning); }
        .alert-kpi .tv-kpi-val { color: var(--danger); }
        .info-kpi .tv-kpi-val { color: var(--primary); }

        .tv-col-body {
            flex: 1;
            min-height: 0;
            display: flex;
            padding: 10px;
            gap: 10px;
        }
        .tv-table-wrap {
            flex: 1.3;
            display: flex;
            flex-direction: column;
            min-height: 0;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            background: #fff;
        }
        .tv-chart-wrap {
            flex: 0.7;
            display: flex;
            flex-direction: column;
            min-height: 0;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            background: #fff;
        }
        .tv-table-hdr, .tv-chart-hdr {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.65rem;
            font-weight: 800;
            color: #475569;
            background: #f8fafc;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            flex-shrink: 0;
        }
        .tv-table-scroll {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            padding: 4px;
        }
        .tv-chart-body {
            flex: 1;
            min-height: 0;
            padding: 8px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tv-chart-body canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* ── TABLES ───────────────────────────────────── */
        .tbl-wrap {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
        }
        table { width: 100%; border-collapse: collapse; font-size: .68rem; }
        thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #f8fafc;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            font-size: .55rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-muted);
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        thead th.ctr { text-align: center; }
        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            white-space: nowrap;
        }
        tbody tr { transition: background .1s; }
        tbody tr:hover { background: #f8fafc; }

        /* Rank Badge */
        .rk {
            width: 20px; height: 20px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .65rem;
        }
        .rk-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #451a03; }
        .rk-2 { background: linear-gradient(135deg, #94a3b8, #cbd5e1); color: #1e293b; }
        .rk-3 { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; }
        .rk-n { background: #f1f5f9; color: var(--text-muted); }

        .mn { font-weight: 700; font-size: .72rem; color: var(--text-main); }

        /* Progress Bar */
        .pb-wrap { display: flex; align-items: center; gap: 6px; }
        .pb { flex: 1; height: 5px; border-radius: 3px; background: #f1f5f9; overflow: hidden; min-width: 35px; }
        .pb-fill { height: 100%; border-radius: 3px; transition: width .5s ease; }
        .pb-fill.g { background: var(--success); }
        .pb-fill.a { background: var(--warning); }
        .pb-fill.r { background: var(--danger); }
        .pv { font-weight: 700; font-size: .68rem; min-width: 35px; text-align: right; }
        .pv.g { color: var(--success); }
        .pv.a { color: var(--warning); }
        .pv.r { color: var(--danger); }

        /* Severity Badges */
        .sv { display: inline-flex; padding: 2px 6px; border-radius: 4px; font-size: .58rem; font-weight: 700; }
        .sv-k { background: #fef2f2; color: #dc2626; }
        .sv-w { background: #fffbeb; color: #d97706; }
        .sv-o { background: #f5f3ff; color: #7c3aed; }

        tr.tr-d { background: #fef2f2; }
        tr.tr-d:hover { background: #fee2e2; }
        tr.tr-w { background: #fffbeb; }
        tr.tr-w:hover { background: #fef3c7; }

        /* Legend */
        .lgd {
            display: flex;
            gap: 12px;
            font-size: .6rem;
            color: var(--text-muted);
        }
        .lgd span { display: flex; align-items: center; gap: 3px; }
        .lgd i { width: 8px; height: 8px; border-radius: 2px; display: inline-block; }

        /* Loaders */
        .ld {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 40px;
            color: var(--text-muted);
            font-size: .75rem;
        }
        .sp {
            width: 16px; height: 16px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .empty {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
            font-size: .75rem;
        }

        /* Responsive for dev dashboard view */
        @media (max-width: 1200px) {
            .main-grid { grid-template-columns: 1fr; }
            .tv-grid { grid-template-columns: 1fr; overflow-y: auto; }
            .tv-layout { height: auto; overflow: auto; }
            html, body { height: auto; overflow: auto; }
        }
    </style>
</head>
<body>
    <div class="tv-layout">
        <!-- ═══ TOP BAR ═══ -->
        <div class="top-bar">
            <h1>🏭 <span class="accent">Ranking Mesin</span> — Timbangan Retail</h1>
            
            <div style="display: flex; align-items: center; gap: 12px;">
                <!-- Real-time TV mode indicator -->
                <div id="autoRefreshIndicator" class="live-badge" style="display: none;">
                    <span class="dot"></span>
                    <span id="refreshTimerLabel">Memuat...</span>
                </div>
                
                <!-- Manual mode updated time -->
                <div id="manualUpdateIndicator" class="live-badge" style="display: none; background: #f8fafc; border: 1px solid var(--border-color); color: var(--text-muted);">
                    <span>Terakhir dimuat: </span>
                    <span id="lastUpdate">Belum dimuat</span>
                </div>

                <!-- Mode Switcher -->
                <div class="mode-toggle-container">
                    <button class="toggle-btn" id="btnModeTv" onclick="switchMode('tv')">📺 Mode TV (Real-time)</button>
                    <button class="toggle-btn" id="btnModeManual" onclick="switchMode('manual')">🔍 Mode Filter</button>
                </div>
            </div>
        </div>

        <!-- ═══ TV VIEW (Side-by-Side Dual Shift) ═══ -->
        <div id="tvView" class="view-container tv-grid">
            <!-- LEFT COLUMN: SHIFT SEBELUMNYA -->
            <div class="tv-col prev-shift-col">
                <div class="tv-col-hdr bg-prev">
                    <div class="shift-title">
                        <span class="badge badge-prev">Shift Sebelumnya</span>
                        <h2 id="tvPrevLabel">Shift —</h2>
                    </div>
                    <div class="shift-meta">
                        <span id="tvPrevDate">—</span>
                        <span class="separator">|</span>
                        <span id="tvPrevTime">—</span>
                    </div>
                </div>
                <div class="tv-kpi-row">
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiSample">—</div>
                        <div class="tv-kpi-lbl">Total Sampel</div>
                    </div>
                    <div class="tv-kpi warning-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiAbnormal">—</div>
                        <div class="tv-kpi-lbl">Abnormal</div>
                    </div>
                    <div class="tv-kpi alert-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiPct">—%</div>
                        <div class="tv-kpi-lbl">% Abnormal</div>
                    </div>
                    <div class="tv-kpi info-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiWorst">—</div>
                        <div class="tv-kpi-lbl">Terburuk</div>
                    </div>
                </div>
                <div class="tv-kpi-row-secondary">
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiUnderTu2" style="color: #e02424; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">&lt;TU2</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiTu2ToTu1" style="color: #fbbf24; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">TU2→TU1</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiTu1ToStd" style="color: #1a56db; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">TU1→STD</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiStdToMax" style="color: #0e9f6e; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">STD→MAX</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvPrevKpiOverMax" style="color: #7e3af2; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">&gt;MAX</div>
                    </div>
                </div>
                <div class="tv-col-body">
                    <div class="tv-table-wrap">
                        <div class="tv-table-hdr">🏆 Ranking Abnormalitas</div>
                        <div class="tv-table-scroll" id="tvPrevTableWrap">
                            <div class="ld"><div class="sp"></div> Memuat...</div>
                        </div>
                    </div>
                    <div class="tv-chart-wrap">
                        <div class="tv-chart-hdr">📊 Grafik % Abnormal</div>
                        <div class="tv-chart-body">
                            <canvas id="chartPrev"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: SHIFT BERJALAN -->
            <div class="tv-col curr-shift-col">
                <div class="tv-col-hdr bg-curr">
                    <div class="shift-title">
                        <span class="badge badge-curr">Shift Berjalan</span>
                        <h2 id="tvCurrLabel">Shift —</h2>
                    </div>
                    <div class="shift-meta">
                        <span id="tvCurrDate">—</span>
                        <span class="separator">|</span>
                        <span id="tvCurrTime">—</span>
                    </div>
                </div>
                <div class="tv-kpi-row">
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiSample">—</div>
                        <div class="tv-kpi-lbl">Total Sampel</div>
                    </div>
                    <div class="tv-kpi warning-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiAbnormal">—</div>
                        <div class="tv-kpi-lbl">Abnormal</div>
                    </div>
                    <div class="tv-kpi alert-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiPct">—%</div>
                        <div class="tv-kpi-lbl">% Abnormal</div>
                    </div>
                    <div class="tv-kpi info-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiWorst">—</div>
                        <div class="tv-kpi-lbl">Terburuk</div>
                    </div>
                </div>
                <div class="tv-kpi-row-secondary">
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiUnderTu2" style="color: #e02424; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">&lt;TU2</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiTu2ToTu1" style="color: #fbbf24; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">TU2→TU1</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiTu1ToStd" style="color: #1a56db; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">TU1→STD</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiStdToMax" style="color: #0e9f6e; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">STD→MAX</div>
                    </div>
                    <div class="tv-kpi">
                        <div class="tv-kpi-val" id="tvCurrKpiOverMax" style="color: #7e3af2; font-size: 0.95rem;">—</div>
                        <div class="tv-kpi-lbl" style="font-size: 0.48rem;">&gt;MAX</div>
                    </div>
                </div>
                <div class="tv-col-body">
                    <div class="tv-table-wrap">
                        <div class="tv-table-hdr">🏆 Ranking Abnormalitas</div>
                        <div class="tv-table-scroll" id="tvCurrTableWrap">
                            <div class="ld"><div class="sp"></div> Memuat...</div>
                        </div>
                    </div>
                    <div class="tv-chart-wrap">
                        <div class="tv-chart-hdr">📊 Grafik % Abnormal</div>
                        <div class="tv-chart-body">
                            <canvas id="chartCurr"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ MANUAL FILTER VIEW ═══ -->
        <div id="manualView" class="view-container" style="display: none;">
            <!-- FILTER + KPI ROW -->
            <div class="filter-kpi-row">
                <div class="filter-card">
                    <div class="fg">
                        <label>Tgl Awal</label>
                        <input type="date" id="fStart">
                    </div>
                    <div class="fg">
                        <label>Tgl Akhir</label>
                        <input type="date" id="fEnd">
                    </div>
                    <div class="fg">
                        <label>Shift</label>
                        <select id="fShift">
                            <option value="">Semua</option>
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                            <option value="3">Shift 3</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Varian</label>
                        <select id="fVarian"><option value="">Semua</option></select>
                    </div>
                    <div class="fg">
                        <label>Mesin</label>
                        <select id="fMesin"><option value="">Semua</option></select>
                    </div>
                    <button class="btn-go" id="btnGo" onclick="loadManualData()">🔍 Tampilkan</button>
                </div>
                <div class="kpi-strip">
                    <div class="kpi c1">
                        <div class="kpi-lbl">Mesin</div>
                        <div class="kpi-val" id="kM">—</div>
                        <div class="kpi-sub">Mesin aktif</div>
                    </div>
                    <div class="kpi c2">
                        <div class="kpi-lbl">Total Sampel</div>
                        <div class="kpi-val" id="kS">—</div>
                        <div class="kpi-sub">Data timbangan</div>
                    </div>
                    <div class="kpi c3">
                        <div class="kpi-lbl">Abnormal</div>
                        <div class="kpi-val" id="kA">—</div>
                        <div class="kpi-sub" id="kAp">—%</div>
                    </div>
                    <div class="kpi c4">
                        <div class="kpi-lbl">Terburuk</div>
                        <div class="kpi-val" id="kW">—</div>
                        <div class="kpi-sub" id="kWp">—</div>
                    </div>
                    <div class="kpi c5">
                        <div class="kpi-lbl">Terbaik</div>
                        <div class="kpi-val" id="kB">—</div>
                        <div class="kpi-sub" id="kBp">—</div>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="main-grid">
                <!-- LEFT: Charts stacked -->
                <div style="display:grid;grid-template-rows:1fr 1fr;gap:10px;min-height:0;">
                    <div class="card">
                        <div class="card-hdr">
                            <h3>📊 % Abnormal per Mesin</h3>
                            <div class="lgd">
                                <span><i style="background:var(--success);"></i> ≤5%</span>
                                <span><i style="background:var(--warning);"></i> 5–10%</span>
                                <span><i style="background:var(--danger);"></i> &gt;10%</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chart1"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-hdr">
                            <h3>📉 Breakdown Severity per Mesin</h3>
                            <div class="lgd">
                                <span><i style="background:var(--danger);"></i> Kritis</span>
                                <span><i style="background:var(--warning);"></i> Warning</span>
                                <span><i style="background:#8b5cf6;"></i> Over</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chart2"></canvas>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Ranking Table -->
                <div class="card">
                    <div class="card-hdr">
                        <h3>🏆 Ranking Mesin (% Abnormal Tertinggi → Terendah)</h3>
                    </div>
                    <div class="tbl-wrap" id="tableWrap">
                        <div class="ld"><div class="sp"></div> Klik Tampilkan untuk memuat data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    'use strict';

    // Global state for charts
    const _c = {};
    function dc(k) { if (_c[k]) { _c[k].destroy(); delete _c[k]; } }

    const fi = n => n == null ? '—' : parseInt(n).toLocaleString('id-ID');
    const f1 = n => n == null ? '—' : parseFloat(n).toFixed(1);
    const f2 = n => n == null ? '—' : parseFloat(n).toFixed(2);
    function td() { const d=new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
    function da(n) { const d=new Date(); d.setDate(d.getDate()-n); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
    function pc(p) { return p > 10 ? 'r' : p > 5 ? 'a' : 'g'; }

    // Init date filters
    document.getElementById('fStart').value = da(6);
    document.getElementById('fEnd').value = td();

    // Load filter options
    fetch('/api/timbangan-retail/filter-options', { headers: { 'Accept':'application/json' } })
        .then(r => r.json()).then(d => {
            const sv = document.getElementById('fVarian');
            const sm = document.getElementById('fMesin');
            (d.variants||[]).forEach(v => sv.appendChild(new Option(v, v)));
            (d.mesins||[]).forEach(m => sm.appendChild(new Option('Mesin '+m, m)));
        }).catch(()=>{});

    // ── MODE DUAL-VIEW LOGIC ─────────────────────────
    let currentMode = 'tv';
    let autoRefreshTimer = null;
    let countdownSecs = 15;
    const REFRESH_DURATION = 15;

    function switchMode(mode) {
        currentMode = mode;
        
        document.getElementById('btnModeTv').classList.toggle('active', mode === 'tv');
        document.getElementById('btnModeManual').classList.toggle('active', mode === 'manual');
        
        document.getElementById('tvView').style.display = mode === 'tv' ? 'grid' : 'none';
        document.getElementById('manualView').style.display = mode === 'manual' ? 'flex' : 'none';
        
        document.getElementById('autoRefreshIndicator').style.display = mode === 'tv' ? 'flex' : 'none';
        document.getElementById('manualUpdateIndicator').style.display = mode === 'manual' ? 'flex' : 'none';
        
        if (mode === 'tv') {
            startAutoRefresh();
            loadTvData();
        } else {
            stopAutoRefresh();
            loadManualData();
        }
    }

    // Auto Refresh
    function startAutoRefresh() {
        stopAutoRefresh();
        countdownSecs = REFRESH_DURATION;
        updateTimerLabel();
        
        autoRefreshTimer = setInterval(() => {
            countdownSecs--;
            if (countdownSecs <= 0) {
                countdownSecs = REFRESH_DURATION;
                loadTvData();
            } else {
                updateTimerLabel();
            }
        }, 1000);
    }

    function stopAutoRefresh() {
        if (autoRefreshTimer) {
            clearInterval(autoRefreshTimer);
            autoRefreshTimer = null;
        }
    }

    function updateTimerLabel() {
        const lbl = document.getElementById('refreshTimerLabel');
        if (lbl) lbl.textContent = `Update otomatis: ${countdownSecs}s`;
    }

    // ── TV REAL-TIME DATA LOAD ──────────────────────
    async function loadTvData() {
        const lbl = document.getElementById('refreshTimerLabel');
        if (lbl) lbl.textContent = 'Memuat data...';
        
        try {
            // Apply current variant / machine filters from drop downs if selected
            const va = document.getElementById('fVarian').value;
            const me = document.getElementById('fMesin').value;
            
            const params = new URLSearchParams();
            if (va) params.append('varian', va);
            if (me) params.append('mesin', me);

            const r = await fetch(`/api/timbangan-retail/realtime-ranking?${params.toString()}`, { 
                headers: { 'Accept':'application/json' } 
            });
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            const d = await r.json();
            
            if (d.success) {
                renderTvShift('prev', d.previous);
                renderTvShift('curr', d.current);
            }
        } catch(e) {
            console.error('TV Load error:', e);
            document.getElementById('tvPrevTableWrap').innerHTML = '<div class="empty">⚠️ Gagal memuat data</div>';
            document.getElementById('tvCurrTableWrap').innerHTML = '<div class="empty">⚠️ Gagal memuat data</div>';
        }
        updateTimerLabel();
    }

    function renderTvShift(prefix, shift) {
        // Headers
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}Label`).textContent = shift.label;
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}Date`).textContent = shift.date_label;
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}Time`).textContent = shift.time_label;

        // KPIs
        const stats = shift.stats || {};
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiSample`).textContent = fi(stats.total_sampel);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiAbnormal`).textContent = fi(stats.total_abnormal);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiPct`).textContent = f2(stats.pct_abnormal) + '%';
        
        const counts = stats.counts || { underTu2: 0, tu2ToTu1: 0, tu1ToStd: 0, stdToMax: 0, overMax: 0 };
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiUnderTu2`).textContent = fi(counts.underTu2);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiTu2ToTu1`).textContent = fi(counts.tu2ToTu1);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiTu1ToStd`).textContent = fi(counts.tu1ToStd);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiStdToMax`).textContent = fi(counts.stdToMax);
        document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiOverMax`).textContent = fi(counts.overMax);
        
        const rows = stats.data || [];
        const worstKpi = document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}KpiWorst`);
        if (stats.total_abnormal > 0 && rows.length && rows[0].abnormal > 0) {
            worstKpi.textContent = 'Msn ' + rows[0].mesin;
        } else {
            worstKpi.textContent = '—';
        }

        // Table
        const wrap = document.getElementById(`tv${prefix === 'prev'?'Prev':'Curr'}TableWrap`);
        if (!rows.length) {
            wrap.innerHTML = '<div class="empty">📭 Tidak ada data shift ini</div>';
            dc(`chart_${prefix}`);
            return;
        }

        let h = `<table><thead><tr>
            <th class="ctr" style="width:25px;">#</th>
            <th>Mesin</th>
            <th>Varian</th>
            <th class="ctr" style="width:45px;">Sampel</th>
            <th class="ctr" style="width:35px;">Abn</th>
            <th>% Abnormal</th>
        </tr></thead><tbody>`;

        rows.forEach((m, i) => {
            const r = i + 1;
            const rc = r===1?'rk-1':r===2?'rk-2':r===3?'rk-3':'rk-n';
            const tr = m.pct_abnormal > 10 ? 'tr-d' : m.pct_abnormal > 5 ? 'tr-w' : '';
            const c = pc(m.pct_abnormal);
            const bw = Math.min(m.pct_abnormal * 2, 100);

            h += `<tr class="${tr}">
                <td class="ctr"><span class="rk ${rc}">${r}</span></td>
                <td><span class="mn">Mesin ${m.mesin}</span></td>
                <td><span style="font-weight:600;color:#64748b;" title="${m.variant}">${m.variant_code}</span></td>
                <td class="ctr">${fi(m.total)}</td>
                <td class="ctr"><strong>${fi(m.abnormal)}</strong></td>
                <td><div class="pb-wrap"><div class="pb"><div class="pb-fill ${c}" style="width:${bw}%"></div></div><span class="pv ${c}">${f2(m.pct_abnormal)}%</span></div></td>
            </tr>`;
        });
        h += '</tbody></table>';
        wrap.innerHTML = h;

        // Render mini chart
        renderTvChart(prefix, rows);
    }

    function renderTvChart(prefix, rows) {
        const canvasId = prefix === 'prev' ? 'chartPrev' : 'chartCurr';
        const ctx = document.getElementById(canvasId).getContext('2d');
        const chartKey = `chart_${prefix}`;
        
        dc(chartKey);
        
        // Take top 6 rows to fit nicely
        const topRows = rows.slice(0, 6);
        const labels = topRows.map(r => `${r.mesin} (${r.variant_code})`);
        const data = topRows.map(r => r.pct_abnormal);
        
        _c[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: data.map(p => p > 10 ? 'rgba(239,68,68,.8)' : p > 5 ? 'rgba(245,158,11,.8)' : 'rgba(16,185,129,.8)'),
                    borderColor: data.map(p => p > 10 ? '#ef4444' : p > 5 ? '#f59e0b' : '#10b981'),
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1,
                        titleFont: { family: 'Inter', weight: '700', size: 9 },
                        bodyFont: { family: 'Inter', size: 9 },
                        callbacks: { label: ctx => ` ${ctx.parsed.y}% abnormal` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 8, family: 'Inter' } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 8, family: 'Inter' }, callback: v => v+'%' } }
                }
            }
        });
    }

    // ── MANUAL QUERY DATA LOAD ──────────────────────
    function bp() {
        const p = new URLSearchParams();
        const s = document.getElementById('fStart').value, e = document.getElementById('fEnd').value;
        if (!s||!e) { alert('Pilih tanggal!'); return null; }
        p.append('start_date', s); p.append('end_date', e);
        const sh = document.getElementById('fShift').value;
        const va = document.getElementById('fVarian').value;
        const me = document.getElementById('fMesin').value;
        if (sh) p.append('shift', sh);
        if (va) p.append('varian', va);
        if (me) p.append('mesin', me);
        return p;
    }

    async function loadManualData() {
        const params = bp();
        if (!params) return;
        const btn = document.getElementById('btnGo');
        btn.disabled = true; btn.textContent = '⏳ Memuat...';
        document.getElementById('tableWrap').innerHTML = '<div class="ld"><div class="sp"></div> Memuat ranking...</div>';

        try {
            const r = await fetch(`/api/timbangan-retail/mesin-ranking?${params}`, { headers: { 'Accept':'application/json' } });
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            const d = await r.json();
            renderKPI(d); renderCharts(d); renderTable(d);
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
        } catch(e) {
            console.error(e);
            document.getElementById('tableWrap').innerHTML = '<div class="empty">⚠️ Gagal memuat data</div>';
        }
        btn.disabled = false; btn.textContent = '🔍 Tampilkan';
    }

    function renderKPI(d) {
        document.getElementById('kM').textContent = fi(d.total_mesin);
        document.getElementById('kS').textContent = fi(d.total_sampel);
        document.getElementById('kA').textContent = fi(d.total_abnormal);
        document.getElementById('kAp').textContent = f2(d.pct_abnormal) + '% dari total';
        const data = d.data || [];
        if (d.total_abnormal > 0 && data.length && data[0].abnormal > 0) {
            const w = data[0], b = data[data.length-1];
            document.getElementById('kW').textContent = 'Msn ' + w.mesin;
            document.getElementById('kWp').textContent = f2(w.pct_abnormal) + '% abn';
            document.getElementById('kB').textContent = 'Msn ' + b.mesin;
            document.getElementById('kBp').textContent = f2(b.pct_abnormal) + '% abn';
        } else {
            document.getElementById('kW').textContent = '—';
            document.getElementById('kWp').textContent = '—';
            document.getElementById('kB').textContent = '—';
            document.getElementById('kBp').textContent = '—';
        }
    }

    // Chart logic for manual mode
    function renderCharts(d) {
        const data = d.data || [];
        const labels = data.map(m => `${m.mesin} (${m.variant_code})`);
        const pcts = data.map(m => m.pct_abnormal);

        // Chart 1 — % Abnormal
        dc('chart1');
        _c['chart1'] = new Chart(document.getElementById('chart1').getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: '% Abnormal',
                    data: pcts,
                    backgroundColor: pcts.map(p => p > 10 ? 'rgba(239,68,68,.75)' : p > 5 ? 'rgba(245,158,11,.75)' : 'rgba(16,185,129,.75)'),
                    borderColor: pcts.map(p => p > 10 ? '#ef4444' : p > 5 ? '#f59e0b' : '#10b981'),
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1,
                        titleFont: { family: 'Inter', weight: '700', size: 11 },
                        bodyFont: { family: 'Inter', size: 11 },
                        callbacks: { label: ctx => ` ${ctx.parsed.y}% abnormal` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 8, family: 'Inter' }, maxRotation: 30 } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' }, callback: v => v+'%' } }
                }
            }
        });

        // Chart 2 — Stacked severity
        dc('chart2');
        _c['chart2'] = new Chart(document.getElementById('chart2').getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Kritis', data: data.map(m=>m.kritis), backgroundColor: 'rgba(239,68,68,.7)', borderRadius: 2 },
                    { label: 'Warning', data: data.map(m=>m.warning), backgroundColor: 'rgba(245,158,11,.7)', borderRadius: 2 },
                    { label: 'Over', data: data.map(m=>m.over), backgroundColor: 'rgba(139,92,246,.7)', borderRadius: 2 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1,
                        titleFont: { family: 'Inter', weight: '700', size: 11 },
                        bodyFont: { family: 'Inter', size: 11 },
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 8, family: 'Inter' }, maxRotation: 30 } },
                    y: { stacked: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' } } }
                }
            }
        });
    }

    function renderTable(d) {
        const data = d.data || [];
        if (!data.length) {
            document.getElementById('tableWrap').innerHTML = '<div class="empty">📭 Tidak ada data</div>';
            return;
        }

        let h = `<table><thead><tr>
            <th class="ctr">#</th>
            <th>Mesin</th>
            <th>Varian</th>
            <th class="ctr">Sampel</th>
            <th class="ctr">Abn</th>
            <th>% Abnormal</th>
            <th class="ctr">Kritis</th>
            <th class="ctr">Warn</th>
            <th class="ctr">Over</th>
            <th class="ctr">Kontrib</th>
        </tr></thead><tbody>`;

        data.forEach((m, i) => {
            const r = i + 1;
            const rc = r===1?'rk-1':r===2?'rk-2':r===3?'rk-3':'rk-n';
            const tr = m.pct_abnormal > 10 ? 'tr-d' : m.pct_abnormal > 5 ? 'tr-w' : '';
            const c = pc(m.pct_abnormal);
            const bw = Math.min(m.pct_abnormal * 2, 100);

            h += `<tr class="${tr}">
                <td class="ctr"><span class="rk ${rc}">${r}</span></td>
                <td><span class="mn">Mesin ${m.mesin}</span></td>
                <td><span style="font-weight:600;color:#64748b;" title="${m.variant}">${m.variant_code}</span></td>
                <td class="ctr">${fi(m.total)}</td>
                <td class="ctr"><strong>${fi(m.abnormal)}</strong></td>
                <td><div class="pb-wrap"><div class="pb"><div class="pb-fill ${c}" style="width:${bw}%"></div></div><span class="pv ${c}">${f2(m.pct_abnormal)}%</span></div></td>
                <td class="ctr">${m.kritis>0?'<span class="sv sv-k">'+m.kritis+'</span>':'<span style="color:#cbd5e1;">0</span>'}</td>
                <td class="ctr">${m.warning>0?'<span class="sv sv-w">'+m.warning+'</span>':'<span style="color:#cbd5e1;">0</span>'}</td>
                <td class="ctr">${m.over>0?'<span class="sv sv-o">'+m.over+'</span>':'<span style="color:#cbd5e1;">0</span>'}</td>
                <td class="ctr" style="font-weight:600;">${f1(m.kontribusi_abnormal)}%</td>
            </tr>`;
        });

        h += '</tbody></table>';
        document.getElementById('tableWrap').innerHTML = h;
    }

    // Auto reload when filters change during TV mode
    document.getElementById('fVarian').addEventListener('change', () => {
        if (currentMode === 'tv') loadTvData();
    });
    document.getElementById('fMesin').addEventListener('change', () => {
        if (currentMode === 'tv') loadTvData();
    });

    // Start in TV mode on load
    switchMode('tv');
    </script>
</body>
</html>
