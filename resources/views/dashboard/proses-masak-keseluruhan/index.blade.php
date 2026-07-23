@extends('layouts.component.main')
@section('title', 'Dashboard Masak (Keseluruhan)')

@section('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --warning-gradient: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            --danger-gradient: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            --info-gradient: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
        }

        .page-content {
            background-color: #f8fafc;
            padding-top: calc(70px + 1.5rem);
            padding-bottom: 2rem;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Hero Banner */
        .modern-hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 16px;
            padding: 20px 24px;
            color: #ffffff;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
            position: relative;
            overflow: hidden;
        }

        .modern-hero-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
            pointer-events: none;
        }

        /* Filter Card */
        .filter-glass-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .filter-glass-card label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .form-select-modern, .form-control-modern {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .form-select-modern:focus, .form-control-modern:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Stat Cards */
        .modern-stat-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .modern-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
        }

        .modern-stat-card .card-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .icon-purple { background: rgba(124, 58, 237, 0.1); color: #7c3aed; }
        .icon-emerald { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .icon-blue { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
        .icon-amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .icon-rose { background: rgba(244, 63, 94, 0.1); color: #f43f5e; }

        .modern-stat-card .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-stat-card .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }

        /* Section Container Cards */
        .modern-section-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .modern-section-header {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .modern-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Histogram Cards */
        .histogram-modern-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            height: 100%;
            transition: border-color 0.2s ease;
        }

        .histogram-modern-box:hover {
            border-color: #cbd5e1;
        }

        .histogram-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .histogram-title-clean {
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pill-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            background: #dcfce7;
            color: #15803d;
        }

        .chart-canvas-modern {
            height: 110px;
            position: relative;
        }

        .stats-summary-pills {
            display: flex;
            justify-content: space-between;
            background: #ffffff;
            border-radius: 8px;
            padding: 4px 8px;
            margin-top: 8px;
            border: 1px solid #edf2f7;
            font-size: 9px;
        }

        .stats-summary-pills .pill-item {
            text-align: center;
            flex: 1;
        }

        .stats-summary-pills .pill-lbl {
            color: #94a3b8;
            font-weight: 600;
            display: block;
        }

        .stats-summary-pills .pill-val {
            color: #0f172a;
            font-weight: 700;
            display: block;
        }

        /* Table Styling */
        .table-modern {
            width: 100%;
            margin-bottom: 0;
            font-size: 11px;
        }

        .table-modern th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-modern td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .table-modern tr:hover td {
            background-color: #f8fafc;
        }

        .table-modern tr.highlight-total td {
            background-color: #fef3c7;
            color: #78350f;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Hero Title Banner -->
            <div class="modern-hero-banner d-flex align-items-center justify-content-between">
                <div>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-1 mb-2 fs-11 rounded-pill">
                        WINGS - PT. Bumi Alam Segar
                    </span>
                    <h3 class="fw-extrabold text-white mb-1" id="modernMainTitle">Dashboard Monitoring Proses Masak</h3>
                    <p class="text-slate-400 mb-0 fs-12">Rangkuman analisis real-time seluruh varian dan formulasi</p>
                </div>
                <div class="d-none d-md-block text-end">
                    <span class="badge bg-emerald-500 text-white px-3 py-2 rounded-pill fs-11 fw-semibold">
                        <i class="ri-checkbox-circle-line me-1"></i> Live System
                    </span>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-glass-card">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-lg-2 col-md-4">
                        <label><i class="ri-price-tag-3-line me-1"></i> Varian Kecap</label>
                        <select name="variant" id="variantSelect" class="form-select form-select-modern">
                            <option value="">Semua Varian</option>
                            @foreach ($variants as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label><i class="ri-flask-line me-1"></i> Formulasi</label>
                        <select name="formulasi" id="formulasiSelect" class="form-select form-select-modern">
                            <option value="">Semua Formulasi</option>
                            @foreach ($formulations as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label><i class="ri-calendar-event-line me-1"></i> Bulan</label>
                        <select name="month" id="monthSelect" class="form-select form-select-modern">
                            <option value="">Semua Bulan</option>
                            @foreach ($availableMonths as $mKey => $mLabel)
                                <option value="{{ $mKey }}">{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label><i class="ri-calendar-2-line me-1"></i> Tanggal Mulai</label>
                        <input type="date" name="start_date" id="startDateInput" class="form-control form-control-modern">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label><i class="ri-calendar-2-line me-1"></i> Tanggal Selesai</label>
                        <input type="date" name="end_date" id="endDateInput" class="form-control form-control-modern">
                    </div>
                    <div class="col-lg-2 col-md-4 d-flex gap-2">
                        <button type="button" id="filterBtn" class="btn btn-primary w-100 fw-semibold" style="border-radius: 8px;">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                        <button type="button" id="resetBtn" class="btn btn-light w-100 fw-semibold" style="border-radius: 8px;">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Top Stat Cards (5 Grid Columns) -->
            <div class="row g-3 mb-4">
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="modern-stat-card">
                        <div class="card-icon-wrapper icon-purple">
                            <i class="ri-stack-line"></i>
                        </div>
                        <div class="stat-label">Qty Batch</div>
                        <div class="stat-value" id="kpi-qty-batch">0</div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="modern-stat-card">
                        <div class="card-icon-wrapper icon-amber">
                            <i class="ri-settings-4-line"></i>
                        </div>
                        <div class="stat-label">Adjust > 1x</div>
                        <div class="stat-value text-amber-600" id="kpi-adjust-gt">0</div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="modern-stat-card">
                        <div class="card-icon-wrapper icon-blue">
                            <i class="ri-blender-line"></i>
                        </div>
                        <div class="stat-label">V. BL Awal</div>
                        <div class="stat-value" id="kpi-vol-awal">0</div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="modern-stat-card">
                        <div class="card-icon-wrapper icon-emerald">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div class="stat-label">V. BL Oke</div>
                        <div class="stat-value text-emerald-600" id="kpi-vol-oke">0</div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="modern-stat-card">
                        <div class="card-icon-wrapper icon-rose">
                            <i class="ri-pie-chart-line"></i>
                        </div>
                        <div class="stat-label">CTS Overall</div>
                        <div class="stat-value text-rose-600" id="kpi-cts-overall">0%</div>
                    </div>
                </div>
            </div>

            <!-- Main Content Layout (Left Analytics + Right Sections) -->
            <div class="row g-4 mb-4">

                <!-- Left Column: Trend Analytics & Disposisi SFG -->
                <div class="col-xl-4 col-lg-5">

                    <!-- Trend adjustment GH -->
                    <div class="modern-section-card mb-3">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-line-chart-line text-primary"></i> Rata-rata Adjustment GH (kg)
                            </div>
                        </div>
                        <div class="p-3">
                            <div style="height: 160px;"><canvas id="chartTrendGh"></canvas></div>
                        </div>
                    </div>

                    <!-- Trend adjustment Air -->
                    <div class="modern-section-card mb-3">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-drop-line text-info"></i> Rata-rata Adjustment Air (kg)
                            </div>
                        </div>
                        <div class="p-3">
                            <div style="height: 160px;"><canvas id="chartTrendH2o"></canvas></div>
                        </div>
                    </div>

                    <!-- Disposisi SFG Pie Chart -->
                    <div class="modern-section-card">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-donut-chart-line text-warning"></i> Disposisi SFG
                            </div>
                        </div>
                        <div class="p-3 d-flex align-items-center justify-content-center">
                            <div id="chartSfgDispositionPie" style="width: 100%; min-height: 220px;"></div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Histograms for Dissolver & Blending Awal -->
                <div class="col-xl-8 col-lg-7">

                    <!-- DISSOLVER SECTION -->
                    <div class="modern-section-card mb-3">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-flask-line text-emerald-600"></i> Dissolver Analysis (GFMix & Glukosa)
                            </div>
                            <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-1 fs-11">Pelarutan 1 & 2</span>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-header-flex">
                                            <span class="histogram-title-clean">BRIX (Fase 4)</span>
                                        </div>
                                        <div class="chart-canvas-modern"><canvas id="chartDissolverP1Brix"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-dissolver-p1-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-dissolver-p1-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-dissolver-p1-brix">-</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-header-flex">
                                            <span class="histogram-title-clean">BRIX (Fase 7)</span>
                                        </div>
                                        <div class="chart-canvas-modern"><canvas id="chartDissolverP2Brix"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-dissolver-p2-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-dissolver-p2-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-dissolver-p2-brix">-</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-header-flex">
                                            <span class="histogram-title-clean">VISCO (Fase 7)</span>
                                        </div>
                                        <div class="chart-canvas-modern"><canvas id="chartDissolverP2Visco"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-dissolver-p2-visco">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-dissolver-p2-visco">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-dissolver-p2-visco">-</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLENDING AWAL SECTION -->
                    <div class="modern-section-card">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-blender-line text-indigo-600"></i> Blending Awal Analysis
                            </div>
                            <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-1 fs-11">Data Sample Pertama (Tanpa After Adjustment)</span>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md col-sm-6">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-title-clean mb-2">Brix</div>
                                        <div class="chart-canvas-modern"><canvas id="chartBlAwalBrix"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-awal-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-awal-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-awal-brix">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-awal-brix">0</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-awal-brix">0</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md col-sm-6">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-title-clean mb-2">Visco</div>
                                        <div class="chart-canvas-modern"><canvas id="chartBlAwalVisco"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-awal-visco">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-awal-visco">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-awal-visco">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-awal-visco">0</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-awal-visco">0</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md col-sm-6">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-title-clean mb-2">NaCl</div>
                                        <div class="chart-canvas-modern"><canvas id="chartBlAwalNacl"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-awal-nacl">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-awal-nacl">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-awal-nacl">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-awal-nacl">0</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-awal-nacl">0</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md col-sm-6">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-title-clean mb-2">Aw</div>
                                        <div class="chart-canvas-modern"><canvas id="chartBlAwalAw"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-awal-aw">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-awal-aw">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-awal-aw">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-awal-aw">0</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-awal-aw">0</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md col-sm-6">
                                    <div class="histogram-modern-box">
                                        <div class="histogram-title-clean mb-2">Warna</div>
                                        <div class="chart-canvas-modern"><canvas id="chartBlAwalWarna"></canvas></div>
                                        <div class="stats-summary-pills">
                                            <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-awal-warna">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-awal-warna">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-awal-warna">-</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-awal-warna">0</span></div>
                                            <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-awal-warna">0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- BLENDING RELEASE SECTION (Full Width Grid) -->
            <div class="modern-section-card mb-4">
                <div class="modern-section-header">
                    <div class="modern-section-title">
                        <i class="ri-checkbox-circle-line text-emerald-600"></i> Blending Release Analysis
                    </div>
                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-pill px-3 py-1 fs-11 fw-semibold">
                        Release & Release Bersyarat Disposition Only
                    </span>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">Brix</span>
                                    <span class="status-pill-badge" id="badge-release-brix">CTS 100%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseBrix"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-brix">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-brix">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-brix">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-brix">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-brix">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">Visco</span>
                                    <span class="status-pill-badge" id="badge-release-visco">CTS 96.38%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseVisco"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-visco">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-visco">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-visco">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-visco">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-visco">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">NaCl</span>
                                    <span class="status-pill-badge" id="badge-release-nacl">CTS 98.70%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseNacl"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-nacl">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-nacl">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-nacl">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-nacl">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-nacl">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">Aw</span>
                                    <span class="status-pill-badge" id="badge-release-aw">CTS 100.00%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseAw"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-aw">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-aw">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-aw">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-aw">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-aw">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">pH</span>
                                    <span class="status-pill-badge" id="badge-release-ph">CTS 79.90%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleasePh"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-ph">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-ph">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-ph">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-ph">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-ph">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">BJ</span>
                                    <span class="status-pill-badge" id="badge-release-bj">CTS 99.95%</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseBj"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-bj">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-bj">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-bj">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-bj">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-bj">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">Warna</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseWarnaBloke"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-warna_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-warna_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-warna_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-warna_bloke">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-warna_bloke">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="histogram-modern-box">
                                <div class="histogram-header-flex">
                                    <span class="histogram-title-clean">Organo</span>
                                </div>
                                <div class="chart-canvas-modern"><canvas id="chartBlReleaseOrganoBloke"></canvas></div>
                                <div class="stats-summary-pills">
                                    <div class="pill-item"><span class="pill-lbl">Min</span><span class="pill-val" id="min-bl-release-organo_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Avg</span><span class="pill-val" id="avg-bl-release-organo_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Max</span><span class="pill-val" id="max-bl-release-organo_bloke">-</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Under</span><span class="pill-val text-amber-600" id="under-bl-release-organo_bloke">0</span></div>
                                    <div class="pill-item"><span class="pill-lbl">Over</span><span class="pill-val text-rose-600" id="over-bl-release-organo_bloke">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Catatan Proses Masak & CTS Per Parameter Table -->
            <div class="row g-4">
                <!-- Catatan Proses Masak Table -->
                <div class="col-xl-5 col-lg-6">
                    <div class="modern-section-card h-100">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-file-list-3-line text-indigo-600"></i> Catatan Proses Masak
                            </div>
                            <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-1 fs-11">QC Pasteurisasi Log</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Batch</th>
                                            <th>Stk</th>
                                            <th>Catatan QC</th>
                                        </tr>
                                    </thead>
                                    <tbody id="catatanProsesTbody">
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Memuat data catatan...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTS Per Parameter Table -->
                <div class="col-xl-7 col-lg-6">
                    <div class="modern-section-card h-100">
                        <div class="modern-section-header">
                            <div class="modern-section-title">
                                <i class="ri-table-line text-emerald-600"></i> CTS Per Parameter Matrix
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700 rounded-pill px-3 py-1 fs-11">Release, Aftercooling & Storage</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Tgl</th>
                                            <th>Stk</th>
                                            <th>CTS BJ</th>
                                            <th>CTS Brix</th>
                                            <th>CTS NaCl</th>
                                            <th>CTS Visco</th>
                                            <th>CTS AW</th>
                                            <th>CTS pH</th>
                                            <th>CTS Organo</th>
                                            <th>CTS Endapan</th>
                                            <th>CTS Buih</th>
                                            <th>CTS Overall</th>
                                            <th>% Adjustment GH</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ctsPerParamTbody">
                                        <tr><td colspan="13" class="text-center py-4 text-muted">Memuat data CTS...</td></tr>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let apexCharts = {};
            let chartJsInstances = {};

            function loadDashboardData() {
                const formData = $('#filterForm').serialize();
                $.getJSON("{{ route('dashboard.proses-masak-keseluruhan.get-data') }}", formData)
                    .done(function(res) {
                        if (res.status === 'success') {
                            updateTopHeaderTitle();
                            updateKpiCards(res.summary);
                            renderTrendLineCharts(res.trend_gh, res.trend_h2o);
                            renderSfgDispositionPie(res.sfg_dispositions);
                            renderHistogramSection('dissolver', res.dissolver);
                            renderHistogramSection('blending_awal', res.blending_awal);
                            renderHistogramSection('blending_release', res.blending_release);
                            renderCatatanProsesTable(res.catatan_proses);
                            renderCtsPerParamTable(res.cts_table_rows, res.grand_total_row);
                        }
                    })
                    .fail(function(err) {
                        console.error('Error fetching dashboard data:', err);
                    });
            }

            function updateTopHeaderTitle() {
                const selectedVariant = $('#variantSelect').val();
                const selectedFormulasi = $('#formulasiSelect').val();

                let title = 'Dashboard Monitoring Proses Masak';
                if (selectedVariant || selectedFormulasi) {
                    title += ' - ' + (selectedVariant || '') + ' ' + (selectedFormulasi || '');
                }

                $('#modernMainTitle').text(title);
            }

            function updateKpiCards(summary) {
                $('#kpi-qty-batch').text(summary.total_batch || '0');
                $('#kpi-adjust-gt').text(summary.adjust_gt_1x_count || '0');
                $('#kpi-vol-awal').text(summary.avg_vol_awal || '0');
                $('#kpi-vol-oke').text(summary.avg_vol_oke || '0');
                $('#kpi-cts-overall').text(summary.cts_overall || '0%');
            }

            function renderTrendLineCharts(trendGh, trendH2o) {
                renderSingleLineChart('#chartTrendGh', trendGh, '#4f46e5');
                renderSingleLineChart('#chartTrendH2o', trendH2o, '#0284c7');
            }

            function renderSingleLineChart(canvasId, trendData, color) {
                const canvas = document.querySelector(canvasId);
                if (!canvas) return;

                if (chartJsInstances[canvasId]) {
                    chartJsInstances[canvasId].destroy();
                }

                const labels = (trendData || []).map(item => item.date);
                const values = (trendData || []).map(item => item.value);

                chartJsInstances[canvasId] = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values.length > 0 ? values : [0.2, 0.2, 0.2, 0.2, 0.2],
                            borderColor: color,
                            backgroundColor: color + '15',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: color,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { display: false },
                            y: { display: true, ticks: { font: { size: 9 } } }
                        }
                    }
                });
            }

            function renderSfgDispositionPie(dispositions) {
                const elementId = '#chartSfgDispositionPie';
                if (apexCharts[elementId]) {
                    apexCharts[elementId].destroy();
                }

                const labels = Object.keys(dispositions || {});
                const series = Object.values(dispositions || {});

                const options = {
                    chart: { type: 'donut', height: 210 },
                    series: series.length > 0 ? series : [99.3, 0.7],
                    labels: labels.length > 0 ? labels : ['Release', 'Hold'],
                    legend: { position: 'bottom', fontSize: '11px' },
                    colors: ['#10b981', '#f59e0b', '#7c3aed', '#ef4444'],
                    plotOptions: {
                        pie: {
                            donut: { size: '65%' }
                        }
                    }
                };

                apexCharts[elementId] = new ApexCharts(document.querySelector(elementId), options);
                apexCharts[elementId].render();
            }

            function renderHistogramSection(sectionPrefix, dataMap) {
                $.each(dataMap, function(paramKey, stats) {
                    let canvasId = '';
                    let minId = '';
                    let avgId = '';
                    let maxId = '';
                    let underId = '';
                    let overId = '';
                    let badgeId = '';

                    if (sectionPrefix === 'dissolver') {
                        const parts = paramKey.split('_');
                        const pPrefix = (parts[0] || '').toUpperCase();
                        const pName = parts[1] ? (parts[1].charAt(0).toUpperCase() + parts[1].slice(1).toLowerCase()) : '';
                        canvasId = `chartDissolver${pPrefix}${pName}`;
                        minId = `min-dissolver-${paramKey.replace('_', '-')}`;
                        avgId = `avg-dissolver-${paramKey.replace('_', '-')}`;
                        maxId = `max-dissolver-${paramKey.replace('_', '-')}`;
                        underId = `under-dissolver-${paramKey.replace('_', '-')}`;
                        overId = `over-dissolver-${paramKey.replace('_', '-')}`;
                    } else if (sectionPrefix === 'blending_awal') {
                        const capitalized = paramKey.charAt(0).toUpperCase() + paramKey.slice(1);
                        canvasId = `chartBlAwal${capitalized}`;
                        minId = `min-bl-awal-${paramKey}`;
                        avgId = `avg-bl-awal-${paramKey}`;
                        maxId = `max-bl-awal-${paramKey}`;
                        underId = `under-bl-awal-${paramKey}`;
                        overId = `over-bl-awal-${paramKey}`;
                    } else if (sectionPrefix === 'blending_release') {
                        let formattedKey = paramKey;
                        if (paramKey === 'warna_bloke') formattedKey = 'WarnaBloke';
                        if (paramKey === 'organo_bloke') formattedKey = 'OrganoBloke';

                        const capitalized = formattedKey.charAt(0).toUpperCase() + formattedKey.slice(1);
                        canvasId = `chartBlRelease${capitalized}`;
                        minId = `min-bl-release-${paramKey}`;
                        avgId = `avg-bl-release-${paramKey}`;
                        maxId = `max-bl-release-${paramKey}`;
                        underId = `under-bl-release-${paramKey}`;
                        overId = `over-bl-release-${paramKey}`;
                        badgeId = `badge-release-${paramKey}`;
                    }

                    if (minId) $(`#${minId}`).text(stats.min !== undefined ? stats.min : '-');
                    if (avgId) $(`#${avgId}`).text(stats.avg !== undefined ? stats.avg : '-');
                    if (maxId) $(`#${maxId}`).text(stats.max !== undefined ? stats.max : '-');
                    if (underId) $(`#${underId}`).text(stats.under !== undefined ? stats.under : '0');
                    if (overId) $(`#${overId}`).text(stats.over !== undefined ? stats.over : '0');
                    if (badgeId) $(`#${badgeId}`).text(`CTS ${stats.cts || 100}%`);

                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    if (chartJsInstances[canvasId]) {
                        chartJsInstances[canvasId].destroy();
                    }

                    const labels = (stats.bins && stats.bins.labels && stats.bins.labels.length > 0) ? stats.bins.labels : [];
                    const counts = (stats.bins && stats.bins.counts && stats.bins.counts.length > 0) ? stats.bins.counts : [];

                    let barColor = '#4f46e5';
                    if (paramKey.includes('visco')) barColor = '#f59e0b';
                    if (paramKey.includes('nacl')) barColor = '#10b981';
                    if (paramKey.includes('aw')) barColor = '#06b6d4';
                    if (paramKey.includes('ph')) barColor = '#8b5cf6';
                    if (paramKey.includes('bj')) barColor = '#ec4899';

                    chartJsInstances[canvasId] = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: counts,
                                backgroundColor: barColor,
                                borderRadius: 4,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { display: true, ticks: { font: { size: 7 } } },
                                y: { display: false }
                            }
                        }
                    });
                });
            }

            function renderCatatanProsesTable(catatanList) {
                const tbody = $('#catatanProsesTbody');
                tbody.empty();

                if (!catatanList || catatanList.length === 0) {
                    tbody.append('<tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada catatan proses</td></tr>');
                    return;
                }

                catatanList.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td><span class="badge bg-slate-100 text-slate-700">${item.date}</span></td>
                            <td class="fw-semibold text-indigo-600">${item.batch}</td>
                            <td>${item.stk}</td>
                            <td>${item.catatan}</td>
                        </tr>
                    `);
                });
            }

            function renderCtsPerParamTable(rows, grandTotalRow) {
                const tbody = $('#ctsPerParamTbody');
                tbody.empty();

                if (rows && rows.length > 0) {
                    rows.forEach(r => {
                        tbody.append(`
                            <tr>
                                <td>${r.tgl}</td>
                                <td><span class="badge bg-slate-100 text-slate-700">${r.stk}</span></td>
                                <td>${r.cts_bj}</td>
                                <td>${r.cts_brix}</td>
                                <td>${r.cts_nacl}</td>
                                <td>${r.cts_visco}</td>
                                <td>${r.cts_aw}</td>
                                <td>${r.cts_ph}</td>
                                <td>${r.cts_organo}</td>
                                <td>${r.cts_endapan}</td>
                                <td>${r.cts_buih}</td>
                                <td><span class="badge bg-emerald-100 text-emerald-700 fw-bold">${r.cts_overall}</span></td>
                                <td>${r.adjust_gh_percent}</td>
                            </tr>
                        `);
                    });
                }

                if (grandTotalRow) {
                    tbody.append(`
                        <tr class="highlight-total">
                            <td>${grandTotalRow.tgl}</td>
                            <td>${grandTotalRow.stk}</td>
                            <td>${grandTotalRow.cts_bj}</td>
                            <td>${grandTotalRow.cts_brix}</td>
                            <td>${grandTotalRow.cts_nacl}</td>
                            <td>${grandTotalRow.cts_visco}</td>
                            <td>${grandTotalRow.cts_aw}</td>
                            <td>${grandTotalRow.cts_ph}</td>
                            <td>${grandTotalRow.cts_organo}</td>
                            <td>${grandTotalRow.cts_endapan}</td>
                            <td>${grandTotalRow.cts_buih}</td>
                            <td>${grandTotalRow.cts_overall}</td>
                            <td>${grandTotalRow.adjust_gh_percent}</td>
                        </tr>
                    `);
                }
            }

            // Event Listeners
            $('#filterBtn').on('click', function() {
                loadDashboardData();
            });

            $('#resetBtn').on('click', function() {
                $('#filterForm')[0].reset();
                loadDashboardData();
            });

            // Initial load
            loadDashboardData();
        });
    </script>
@endsection
