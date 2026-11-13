@extends('layouts.component.main')
@section('title', 'Dashboard - Raw Material (RM)')
@section('styles')
    <style>
        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease;
            height: 100%;
            background: #ffffff;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.09);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stats-value {
            font-size: 26px;
            font-weight: 700;
            margin: 10px 0 4px 0;
            line-height: 1.1;
            color: #1e293b;
        }

        .stats-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stats-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .chart-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
            background: #ffffff;
        }

        .chart-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .chart-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .filter-card {
            background: #ffffff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .filter-card .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .status-accept {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-reject {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
        }

        .status-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .bg-primary-light {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .bg-success-light {
            background-color: #dcfce7;
            color: #15803d;
        }

        .bg-warning-light {
            background-color: #fef3c7;
            color: #b45309;
        }

        .bg-danger-light {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .bg-info-light {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .bg-primary-light {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .bg-teal-light {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        #dispositionChartFilterGroup .btn {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
        }

        #dispositionChartFilterGroup .btn.active {
            color: #ffffff !important;
        }

        #dispositionChartFilterGroup .btn[data-disp="all"].active,
        #dispositionChartFilterGroup .btn[data-disp="release"].active {
            background-color: #10b981;
            border-color: #10b981;
        }

        #dispositionChartFilterGroup .btn[data-disp="bersyarat"].active {
            background-color: #f59e0b;
            border-color: #f59e0b;
        }

        #dispositionChartFilterGroup .btn[data-disp="reject"].active {
            background-color: #ef4444;
            border-color: #ef4444;
        }

        #dispositionChartFilterGroup .btn[data-disp="pending"].active {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 10px;
        }

        /* Sugar Priority Cards */
        .sugar-batch-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .sugar-batch-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
            border-color: #e2e8f0;
            border-left-color: #dc2626;
        }

        .section-banner {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        .section-pill {
            background-color: #3b82f6;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* Detail Standar Mutu per Transaksi Styles */
        .table-row-clickable {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .table-row-clickable:hover {
            background-color: #f8fafc !important;
        }

        .expanded-detail-row {
            background-color: #f8fafc;
        }

        .param-detail-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            margin: 10px 14px 18px 14px;
            overflow: hidden;
            border-left: 5px solid #4338ca;
        }

        .param-detail-card .sub-header {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 20px;
        }

        .sub-param-table {
            font-size: 12.5px;
            margin-bottom: 0;
        }

        .sub-param-table th {
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            padding: 10px 14px;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .sub-param-table td {
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .sub-param-table tr:hover {
            background-color: #fcfcfd;
        }

        .filter-tab-pill {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tab-pill:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .filter-tab-pill.active {
            background: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(67, 56, 202, 0.25);
        }

        .disposisi-filter-btn {
            font-size: 11.5px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .disposisi-filter-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .disposisi-filter-btn.active {
            background: #1e293b;
            border-color: #1e293b;
            color: #ffffff;
        }

        .compliance-bar-container {
            width: 75px;
            height: 6px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }

        .compliance-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .transition-transform {
            transition: transform 0.2s ease;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header Page -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-1 text-primary fw-bold">
                                <i class="ri-dashboard-3-line me-1"></i> Dashboard - Raw Material (RM)
                            </h4>
                            <p class="text-muted mb-0 small">
                                Monitoring analisa mutu, ketahanan simpan gula (uji kristal), kepatuhan armada truk, dan kedatangan bahan baku
                            </p>
                        </div>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">RMPM</a></li>
                                <li class="breadcrumb-item active">Raw Material</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="filter-card">
                <div class="row g-3 align-items-end">
                    <div class="col-xl-3 col-md-6">
                        <label class="form-label"><i class="ri-calendar-line me-1"></i> Periode Waktu</label>
                        <select class="form-select" id="periodFilter">
                            <option value="all" selected>Semua Data (Total Historis)</option>
                            <option value="today">Hari Ini</option>
                            <option value="7days">7 Hari Terakhir</option>
                            <option value="30days">30 Hari Terakhir</option>
                            <option value="custom">Pilih Tanggal Tertentu</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-md-6" id="datePickerCol" style="display: none;">
                        <label class="form-label"><i class="ri-calendar-event-line me-1"></i> Tanggal Kedatangan</label>
                        <input type="date" class="form-control" id="dateFilter" value="{{ $defaultDate ?? date('Y-m-d') }}">
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <label class="form-label"><i class="ri-archive-line me-1"></i> Kategori Bahan Baku</label>
                        <select class="form-select" id="materialTypeFilter">
                            <option value="all" selected>Semua Kategori RM</option>
                            <option value="gula">Gula (Kelapa & Tebu)</option>
                            <option value="garam">Garam</option>
                            <option value="cair">Bahan Cair (Glukosa/Fructose/dll)</option>
                            <option value="kering">Bahan Kering & BTP</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-md-6 ms-auto d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" id="applyFilter">
                            <i class="ri-filter-3-line me-1"></i> Terapkan
                        </button>
                        <button class="btn btn-light" id="resetFilter" title="Atur Ulang ke Semua Data">
                            <i class="ri-restart-line"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Cards Row (5 Key Metrics) -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Total Kedatangan -->
                <div class="col-xxl col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary-light">
                                    <i class="ri-truck-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Total Kedatangan</p>
                                    <h3 class="stats-value" id="totalArrivals">-</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Total Tonase Masuk</span>
                                <span class="badge bg-primary-light" id="totalVolumeFormatted">- Ton</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tingkat Penerimaan -->
                <div class="col-xxl col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success-light">
                                    <i class="ri-checkbox-circle-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Tingkat Penerimaan</p>
                                    <h3 class="stats-value text-success" id="acceptanceRate">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Status Disetujui Lab</span>
                                <span class="badge bg-success-light" id="acceptCountBadge">- Batch</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tingkat Penolakan -->
                <div class="col-xxl col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-danger-light">
                                    <i class="ri-close-circle-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Tingkat Penolakan</p>
                                    <h3 class="stats-value text-danger" id="rejectionRate">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Status Ditolak (Reject)</span>
                                <span class="badge bg-danger-light" id="rejectCountBadge">- Batch</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Kepatuhan Dokumen -->
                <div class="col-xxl col-lg-6 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info-light">
                                    <i class="ri-file-shield-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Kepatuhan Dokumen</p>
                                    <h3 class="stats-value text-info" id="documentCompleteness">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Kelengkapan Berkas</span>
                                <span class="badge bg-info-light">COA, SJ &amp; PL</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Kepatuhan Armada Truk -->
                <div class="col-xxl col-lg-6 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-teal-light">
                                    <i class="ri-shield-check-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Kepatuhan Armada Truk</p>
                                    <h3 class="stats-value" style="color: #0f766e;" id="vehicleCleanlinessRate">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Pemeriksaan Gerbang</span>
                                <span class="badge bg-teal-light">Bersih &amp; Tersegel</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 1: Analisa Ketahanan Gula (Raw Material Spesifik Gula Kelapa & Gula Tebu) -->
            <div class="row g-3 mb-4" id="sugarStabilitySection">
                <div class="col-12">
                    <div class="section-banner d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="section-pill">Analisa Khusus</span>
                            <h5 class="mb-0 fw-bold text-dark">Ketahanan Mutu &amp; Masa Simpan Gula (Uji Kristalisasi)</h5>
                        </div>
                        <span class="badge bg-light text-dark border px-2 py-1" id="totalSugarBatchesBadge">0 Batch Teruji</span>
                    </div>
                </div>

                <!-- Card Kiri: Diagram Donut Ketahanan Gula -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title mb-1">Distribusi Kelompok Ketahanan Gula</h5>
                                <p class="text-muted mb-0 small">Klasifikasi masa simpan gudang berdasarkan hasil uji kristal laboratorium</p>
                            </div>
                        </div>
                        <div class="card-body position-relative">
                            <div id="sugarDonutChart"></div>
                            <div id="emptyStateSugarDonut" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Gula Tidak Ditemukan</h6>
                                <p class="text-muted small">Tidak ada data kedatangan gula pada periode ini</p>
                            </div>

                            <!-- Legend Pills -->
                            <div class="row g-2 mt-2 pt-2 border-top text-center" style="font-size: 11px;">
                                <div class="col-4">
                                    <div class="p-2 rounded bg-success-light">
                                        <div class="fw-bold text-success" id="sugarGroupACount">- Batch</div>
                                        <div class="text-muted small">Grup A (~30 Hari)</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-warning-light">
                                        <div class="fw-bold text-warning" id="sugarGroupBCount">- Batch</div>
                                        <div class="text-muted small">Grup B (~14 Hari)</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-danger-light">
                                        <div class="fw-bold text-danger" id="sugarGroupCCount">- Batch</div>
                                        <div class="text-muted small">Grup C (3–7 Hari)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Kanan: Rekomendasi Prioritas Pelarutan Batch Gula -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title mb-1">Antrean Prioritas Pelarutan (Batch Kritis)</h5>
                                <p class="text-muted mb-0 small">Batch rentan mengkristal (Grup C) yang wajib segera dilarutkan ke produksi</p>
                            </div>
                            <span class="badge bg-danger" id="criticalAlertCountBadge">0 Kritis</span>
                        </div>
                        <div class="card-body position-relative d-flex flex-column justify-content-between" style="min-height: 340px;">
                            <!-- Alert Banner -->
                            <div id="sugarAlertBanner" class="alert alert-success py-2 px-3 d-flex align-items-center mb-3" style="font-size: 13px;">
                                <i class="ri-checkbox-circle-line fs-5 me-2 text-success" id="sugarAlertBannerIcon"></i>
                                <div id="sugarAlertBannerText">Seluruh persediaan gula dalam kondisi aman &amp; stabil.</div>
                            </div>

                            <!-- List Batch Mendesak -->
                            <div id="criticalAlertContainer" style="max-height: 220px; overflow-y: auto;" class="flex-grow-1 pe-1">
                                <!-- Dynamic Batch Cards injected via JS -->
                            </div>

                            <!-- Catatan SOP Standar QC -->
                            <div class="mt-3 pt-2 border-top text-muted d-flex justify-content-between align-items-center" style="font-size: 11px;">
                                <span><i class="ri-information-line me-1"></i> Standar QC: Grup A (Buffer Stock) | Grup B (Pantau) | Grup C (Prioritas Larutkan)</span>
                                <span class="fw-semibold text-dark">QC Raw Material</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Diagram Monitoring Parameter Mutu RM vs Standar Mutu -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <!-- Header & Material Switcher Bar -->
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary px-2 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">QC Control Charts</span>
                                        <h5 class="card-title mb-0 fw-bold text-dark fs-5">
                                            <i class="ri-line-chart-line text-primary me-1"></i> Diagram Monitoring
                                        </h5>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        Tren pengujian laboratorium per transaksi kedatangan (SPB) terhadap batas standar mutu (Min &amp; Maks spesifikasi QC)
                                    </p>
                                </div>

                                <!-- Quick Material Dropdown -->
                                <div class="d-flex align-items-center gap-2">
                                    <label class="form-label mb-0 small text-muted fw-semibold">Pilih Bahan:</label>
                                    <select class="form-select form-select-sm" id="materialSelectDropdown" style="min-width: 220px;">
                                        <option value="Gula Tebu" selected>Gula Tebu</option>
                                        <option value="GULA KELAPA">Gula Kelapa</option>
                                        <option value="GARAM">Garam Halus</option>
                                        <option value="SARI KECAP MURNI HAITIAN (YELLOW BEAN)">Sari Kecap Murni</option>
                                        <option value="HIGH FRUCTOSE CORN SYRUP">High Fructose Corn Syrup (HFCS)</option>
                                        <option value="ND 17103">ND 17103</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Material Selector Quick Pills -->
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top" id="materialQuickPills">
                                <span class="text-muted small fw-bold me-1"><i class="ri-filter-line me-1"></i>Bahan Cepat:</span>
                                <button type="button" class="filter-tab-pill active" data-mat="Gula Tebu">
                                    <i class="ri-contrast-drop-line text-warning"></i> Gula Tebu
                                </button>
                                <button type="button" class="filter-tab-pill" data-mat="GULA KELAPA">
                                    <i class="ri-contrast-drop-line text-warning"></i> Gula Kelapa
                                </button>
                                <button type="button" class="filter-tab-pill" data-mat="GARAM">
                                    <i class="ri-sparkling-line text-info"></i> Garam Halus
                                </button>
                                <button type="button" class="filter-tab-pill" data-mat="SARI KECAP MURNI HAITIAN (YELLOW BEAN)">
                                    <i class="ri-drop-line text-primary"></i> Sari Kecap Murni
                                </button>
                                <button type="button" class="filter-tab-pill" data-mat="HIGH FRUCTOSE CORN SYRUP">
                                    <i class="ri-drop-line text-primary"></i> HFCS / Fruktosa
                                </button>
                                <button type="button" class="filter-tab-pill" data-mat="ND 17103">
                                    <i class="ri-box-3-line text-secondary"></i> ND / Bahan Kering
                                </button>
                            </div>
                        </div>

                        <!-- Active Material Info & Overall Stats Banner -->
                        <div class="card-body p-3 px-4 bg-light-subtle border-bottom">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stats-icon bg-primary-light" style="width: 44px; height: 44px;">
                                        <i class="ri-flask-fill fs-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark fs-5" id="activeMaterialTitle">GULA TEBU</h6>
                                        <div class="text-muted small">
                                            Monitoring kepatuhan parameter uji lab: <strong class="text-dark" id="activeMaterialParamsList">Brix, pH, % Kadar Air, % Kotoran</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-white text-dark border px-3 py-2 shadow-xs" id="activeMatBatchCountBadge">
                                        <i class="ri-truck-line text-primary me-1"></i> <strong id="activeMatBatchCount">0</strong> Batch / SPB Terpantau
                                    </span>
                                    <span class="badge bg-white text-success border px-3 py-2 shadow-xs" id="activeMatInSpecBadge">
                                        <i class="ri-checkbox-circle-line me-1"></i> <strong id="activeMatInSpecRate">0%</strong> Lolos Standar
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Grid Container for Dynamic Quality Charts -->
                        <div class="card-body p-4">
                            <div class="row g-4" id="qualityChartsGrid">
                                <!-- Dynamic Parameter Charts injected via JS -->
                                <div class="col-12 text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Memuat diagram mutu...</span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0 small">Menyiapkan diagram kontrol mutu...</p>
                                </div>
                            </div>

                            <!-- Empty state if no data for material -->
                            <div id="materialChartsEmpty" class="text-center py-5 d-none">
                                <i class="ri-line-chart-line" style="font-size: 54px; color: #cbd5e1;"></i>
                                <h6 class="mt-3 text-muted fw-bold">Belum Ada Data Pengujian untuk Bahan Ini</h6>
                                <p class="text-muted small mb-0">Silakan pilih bahan lain atau sesuaikan filter periode tanggal</p>
                            </div>
                        </div>

                        <!-- Collapsible Recent Transactions Log Table for Selected Material -->
                        <div class="card-footer bg-white border-top p-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <a class="text-decoration-none fw-semibold text-primary small d-flex align-items-center gap-1" data-bs-toggle="collapse" href="#collapseMatTxTable" role="button" aria-expanded="false" aria-controls="collapseMatTxTable">
                                    <i class="ri-table-line"></i> <span id="collapseLogTitle">Lihat Log Data Transaksi Teruji (Gula Tebu)</span>
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <span class="badge bg-light text-muted border small">Log Data Lab</span>
                            </div>

                            <div class="collapse mt-3" id="collapseMatTxTable">
                                <div class="table-responsive rounded border">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12.5px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>No. SPB</th>
                                                <th>Lot/Batch</th>
                                                <th>Pemasok</th>
                                                <th>Jumlah</th>
                                                <th>Disposisi Mutu</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="materialTxTableBody">
                                            <!-- Injected via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Analitik Distribusi Disposisi & Frekuensi Kedatangan RM -->
            <div class="row g-3 mb-4">
                <!-- Frekuensi Kedatangan RM -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Frekuensi Kedatangan Raw Material</h5>
                                <p class="text-muted mb-0 small">Volume tonase dan frekuensi kedatangan armada per tanggal</p>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size: 11px;">Jumlah Armada SPB</span>
                        </div>
                        <div class="card-body position-relative">
                            <div id="trendChart"></div>
                            <div id="emptyStateTrend" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Tidak Tersedia</h6>
                                <p class="text-muted small">Tidak ada transaksi kedatangan pada rentang waktu ini</p>
                            </div>
                            <div class="loading-overlay d-none" id="trendLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Disposisi Laboratorium -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="chart-title mb-1">Distribusi Disposisi Mutu Laboratorium</h5>
                                <p class="text-muted mb-0 small">Perbandingan status persetujuan release lab QC</p>
                            </div>
                            <!-- Filter Pill Buttons (Semua, Release, Bersyarat, Reject, Pending) -->
                            <div class="btn-group btn-group-sm" role="group" id="dispositionChartFilterGroup">
                                <button type="button" class="btn btn-sm btn-outline-success active" data-disp="all">Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-disp="release">Release</button>
                                <button type="button" class="btn btn-sm btn-outline-warning" data-disp="bersyarat">Bersyarat</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-disp="reject">Reject</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-disp="pending">Pending</button>
                            </div>
                        </div>
                        <div class="card-body position-relative">
                            <div id="dispositionChart"></div>
                            <div id="emptyStateDisposition" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Tidak Tersedia</h6>
                                <p class="text-muted small">Tidak ada data disposisi yang tercatat</p>
                            </div>
                            <div class="loading-overlay d-none" id="dispositionLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>

                            <!-- Legend Pills Container (Dynamically rendered based on active filter) -->
                            <div class="row g-2 mt-2 pt-2 border-top text-center" id="dispositionLegendPillsContainer" style="font-size: 11px;">
                                <!-- Injected dynamically via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Kepatuhan Armada Truk, Top Bahan & Kinerja Pemasok -->
            <div class="row g-3 mb-4">
                <!-- Radar: Kepatuhan Armada Mobil -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Kepatuhan Armada Truk Masuk</h5>
                                <p class="text-muted mb-0 small">Pemeriksaan sampling fisik gerbang pabrik</p>
                            </div>
                            <i class="ri-radar-line text-muted fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="vehicleRadarChart"></div>
                            <div id="emptyStateVehicleRadar" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Armada Tidak Ada</h6>
                                <p class="text-muted small">Belum ada catatan sampling kendaraan masuk</p>
                            </div>
                            <div class="loading-overlay d-none" id="vehicleLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bahan Baku Paling Banyak -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Bahan Baku Paling Banyak Datang</h5>
                                <p class="text-muted mb-0 small">Volume kedatangan paling sering di gerbang RM</p>
                            </div>
                            <i class="ri-bar-chart-horizontal-line text-muted fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="topMaterialsChart"></div>
                            <div id="emptyStateTopMaterials" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Tidak Tersedia</h6>
                                <p class="text-muted small">Tidak ada data bahan masuk</p>
                            </div>
                            <div class="loading-overlay d-none" id="topMaterialsLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frekuensi Supplier Raw Material -->
                <div class="col-xl-4 col-lg-12">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Frekuensi Supplier</h5>
                                <p class="text-muted mb-0 small">Frekuensi pengiriman bahan baku per pemasok (SPB)</p>
                            </div>
                            <i class="ri-building-line text-muted fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="supplierChart"></div>
                            <div id="emptyStateSupplier" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Supplier Tidak Ada</h6>
                                <p class="text-muted small">Belum ada evaluasi supplier</p>
                            </div>
                            <div class="loading-overlay d-none" id="supplierLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
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
        let trendChart, dispositionChart, topMaterialsChart, supplierChart, sugarDonutChart, vehicleRadarChart;
        const defaultFilterDate = "{{ $defaultDate ?? date('Y-m-d') }}";

        $(document).ready(function() {
            // Handle period change to show/hide date picker
            $('#periodFilter').on('change', function() {
                const val = $(this).val();
                if (val === 'custom') {
                    $('#datePickerCol').fadeIn(200);
                } else {
                    $('#datePickerCol').fadeOut(150);
                }
            });

            // Trigger load data immediately on page load
            loadAllData();

            $('#applyFilter').on('click', function() {
                loadAllData();
            });

            $('#resetFilter').on('click', function() {
                $('#periodFilter').val('all');
                $('#dateFilter').val(defaultFilterDate);
                $('#materialTypeFilter').val('all');
                $('#datePickerCol').hide();
                $('#dispositionChartFilterGroup button').removeClass('active');
                $('#dispositionChartFilterGroup button[data-disp="all"]').addClass('active');
                currentDispositionFilter = 'all';
                loadAllData();
            });

            // Filter pill buttons for Distribusi Disposisi Mutu Laboratorium
            $('#dispositionChartFilterGroup button').on('click', function() {
                $('#dispositionChartFilterGroup button').removeClass('active');
                $(this).addClass('active');
                const disp = $(this).data('disp');
                renderDispositionChartWithFilter(disp);
            });
        });

        function showEmptyState(chartId, emptyId) {
            $(chartId).hide();
            $(emptyId).removeClass('d-none');
        }

        function showChart(chartId, emptyId) {
            $(emptyId).addClass('d-none');
            $(chartId).show();
        }

        function getFilterParams() {
            const period = $('#periodFilter').val();
            const date = $('#dateFilter').val();
            const materialType = $('#materialTypeFilter').val();

            const params = {
                category: 'rm',
                period: period,
                material_type: materialType
            };

            if (period === 'custom' && date) {
                params.date = date;
            }

            return params;
        }

        function loadAllData() {
            const params = getFilterParams();

            loadStatistics(params);
            loadSugarStability(params);
            loadMaterialQualityTrends(currentSelectedMaterial);
            loadTrendChart(params);
            loadDispositionChart(params);
            loadVehicleRadarChart(params);
            loadTopMaterialsChart(params);
            loadSupplierChart(params);
        }

        // 1. Load Statistics
        function loadStatistics(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/statistics',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (response.success && response.data) {
                        const d = response.data;
                        $('#totalArrivals').text(d.total_arrivals ?? d.total_kedatangan ?? 0);
                        $('#totalVolumeFormatted').text(d.total_volume_formatted ?? '0 Kg');
                        $('#acceptanceRate').text((d.acceptance_rate ?? 0) + '%');
                        $('#acceptCountBadge').text((d.accept_count ?? 0) + ' Batch');
                        $('#rejectionRate').text((d.rejection_rate ?? 0) + '%');
                        $('#rejectCountBadge').text((d.reject_count ?? 0) + ' Batch');
                        $('#documentCompleteness').text((d.document_completeness ?? 0) + '%');
                        $('#vehicleCleanlinessRate').text((d.vehicle_cleanliness_rate ?? 0) + '%');
                    }
                }
            });
        }

        // 2. Load Sugar Stability (Donut Chart & Priority Queue)
        function loadSugarStability(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/sugar-stability',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (!response.success || !response.is_available) {
                        $('#sugarStabilitySection').hide();
                        return;
                    }

                    $('#sugarStabilitySection').show();
                    const data = response.data;

                    $('#totalSugarBatchesBadge').text(data.total_batches + ' Batch Teruji');
                    $('#criticalAlertCountBadge').text(data.group_c + ' Kritis');
                    $('#sugarGroupACount').text(data.group_a + ' Batch (' + data.group_a_pct + '%)');
                    $('#sugarGroupBCount').text(data.group_b + ' Batch (' + data.group_b_pct + '%)');
                    $('#sugarGroupCCount').text(data.group_c + ' Batch (' + data.group_c_pct + '%)');

                    // 1. Donut Chart
                    if (data.total_batches === 0) {
                        if (sugarDonutChart) sugarDonutChart.destroy();
                        $('#sugarDonutChart').hide();
                        $('#emptyStateSugarDonut').removeClass('d-none');
                    } else {
                        $('#emptyStateSugarDonut').addClass('d-none');
                        $('#sugarDonutChart').show();

                        const donutOptions = {
                            series: data.values,
                            chart: {
                                type: 'donut',
                                height: 260
                            },
                            labels: [
                                'Grup A (~30 Hari)',
                                'Grup B (~14 Hari)',
                                'Grup C (3–7 Hari)'
                            ],
                            colors: ['#10b981', '#f59e0b', '#ef4444'],
                            legend: {
                                position: 'bottom',
                                fontSize: '12px',
                                markers: { radius: 12 },
                                formatter: function(seriesName, opts) {
                                    const val = opts.w.globals.series[opts.seriesIndex];
                                    return seriesName + ': ' + val + ' Batch';
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return Math.round(val) + "%";
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '68%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total Teruji',
                                                fontSize: '13px',
                                                fontWeight: 600,
                                                color: '#64748b',
                                                formatter: function() {
                                                    return data.total_batches + ' Batch';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        const pct = data.total_batches > 0 ? Math.round((val / data.total_batches) * 100) : 0;
                                        return val + ' Batch (' + pct + '%)';
                                    }
                                }
                            }
                        };

                        if (sugarDonutChart) sugarDonutChart.destroy();
                        sugarDonutChart = new ApexCharts(document.querySelector("#sugarDonutChart"), donutOptions);
                        sugarDonutChart.render();
                    }

                    // 2. Alert Banner & Priority Queue
                    if (data.group_c > 0) {
                        $('#sugarAlertBanner')
                            .removeClass('alert-success')
                            .addClass('alert-warning')
                            .html(`
                                <i class="ri-alert-line fs-5 me-2 text-danger"></i>
                                <div>
                                    <strong>Perhatian Operasional:</strong> Terdeteksi <strong>${data.group_c} batch Grup C</strong> rentan mengkristal. Prioritaskan ke tangki pelarutan sebelum memadat.
                                </div>
                            `);
                    } else {
                        $('#sugarAlertBanner')
                            .removeClass('alert-warning')
                            .addClass('alert-success')
                            .html(`
                                <i class="ri-checkbox-circle-line fs-5 me-2 text-success"></i>
                                <div>
                                    <strong>Kondisi Baik:</strong> Seluruh persediaan gula saat ini dalam kondisi stabil (Grup A &amp; B).
                                </div>
                            `);
                    }

                    let listHtml = '';
                    if (!data.critical_alerts || data.critical_alerts.length === 0) {
                        listHtml = `
                            <div class="text-center py-4 my-auto">
                                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                                    <i class="ri-check-double-line fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-success mb-1">Tidak Ada Antrean Mendesak</h6>
                                <p class="text-muted small mb-0">Masa simpan persediaan gula masih aman sesuai standar laboratorium.</p>
                            </div>
                        `;
                    } else {
                        data.critical_alerts.forEach(function(item, idx) {
                            listHtml += `
                                <div class="sugar-batch-card d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-danger" style="font-size: 10px;">Prioritas #${idx + 1}</span>
                                            <span class="fw-bold text-dark" style="font-size: 13px;">${item.spb}</span>
                                            <span class="badge bg-light text-muted border">${item.jenis}</span>
                                        </div>
                                        <div class="small text-muted">
                                            Pemasok: <strong>${item.supplier}</strong> &bull; Masuk: ${item.tanggal}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-danger-subtle text-danger mb-1" style="font-size: 11px;">
                                            <i class="ri-time-line me-1"></i> Aging: ${item.aging_hari} Hari
                                        </div>
                                        <div>
                                            <span class="badge bg-danger" style="font-size: 10px;">Segera Larutkan</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#criticalAlertContainer').html(listHtml);
                }
            });
        }

        // 3. Load Trend Chart
        function loadTrendChart(params) {
            $('#trendLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/trend-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#trendLoading').addClass('d-none');

                    if (!response.success || response.data.labels.length === 0) {
                        if (trendChart) trendChart.destroy();
                        showEmptyState('#trendChart', '#emptyStateTrend');
                        return;
                    }

                    showChart('#trendChart', '#emptyStateTrend');

                    const options = {
                        series: [{
                            name: 'Kedatangan (Armada/SPB)',
                            data: response.data.raw_material
                        }],
                        chart: {
                            type: 'area',
                            height: 290,
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2.5 },
                        xaxis: { categories: response.data.labels },
                        yaxis: {
                            labels: {
                                formatter: function(val) { return Math.round(val); }
                            }
                        },
                        colors: ['#4338ca'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.45,
                                opacityTo: 0.05,
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val, opt) {
                                    let str = val + ' SPB';
                                    if (response.data.raw_material_volume && response.data.raw_material_volume[opt.dataPointIndex] !== undefined) {
                                        const vol = response.data.raw_material_volume[opt.dataPointIndex];
                                        const volFormatted = vol >= 1000 ? (vol / 1000).toFixed(1) + ' Ton' : vol + ' Kg';
                                        str += ' (' + volFormatted + ')';
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (trendChart) trendChart.destroy();
                    trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
                    trendChart.render();
                }
            });
        }

        // 4. Load Disposition Chart
        let currentDispositionFilter = 'all';
        let cachedDispositionBreakdowns = null;

        function renderDispositionChartWithFilter(dispType) {
            currentDispositionFilter = dispType || 'all';

            if (!cachedDispositionBreakdowns || !cachedDispositionBreakdowns[currentDispositionFilter]) {
                if (dispositionChart) dispositionChart.destroy();
                $('#dispositionChart').hide();
                $('#emptyStateDisposition').removeClass('d-none');
                $('#dispositionLegendPillsContainer').empty();
                return;
            }

            const data = cachedDispositionBreakdowns[currentDispositionFilter];
            const total = data.total || 0;
            const values = data.values || [];
            const labels = data.labels || [];
            const colors = data.colors || ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'];
            const centerTitle = data.title || 'Total Analisa';

            if (total === 0 || values.length === 0 || values.every(v => v === 0)) {
                if (dispositionChart) dispositionChart.destroy();
                $('#dispositionChart').hide();
                $('#emptyStateDisposition').removeClass('d-none');
                $('#dispositionLegendPillsContainer').html(`
                    <div class="col-12 py-2 text-muted fst-italic">
                        Tidak ada data analisa dengan disposisi ini
                    </div>
                `);
                return;
            }

            $('#emptyStateDisposition').addClass('d-none');
            $('#dispositionChart').show();

            const options = {
                series: values,
                chart: {
                    type: 'donut',
                    height: 260
                },
                labels: labels,
                colors: colors,
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: { radius: 12 }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: centerTitle,
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function() {
                                        return total + ' SPB';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            if (dispositionChart) dispositionChart.destroy();
            dispositionChart = new ApexCharts(document.querySelector("#dispositionChart"), options);
            dispositionChart.render();

            // Render Dynamic Legend Pills
            let pillsHtml = '';
            const pills = data.pills || [];
            const colSize = pills.length === 1 ? 'col-12' : (pills.length === 2 ? 'col-6' : (pills.length === 3 ? 'col-4' : (pills.length === 4 ? 'col-md-3 col-6' : 'col-md-4 col-6')));
            pills.forEach(p => {
                const pct = total > 0 ? Math.round((p.val / total) * 100) : 0;
                pillsHtml += `
                    <div class="${colSize}">
                        <div class="p-2 rounded bg-${p.color}-light">
                            <div class="fw-bold text-${p.color}">${p.val} SPB (${pct}%)</div>
                            <div class="text-muted small text-truncate" title="${p.label}">${p.label}</div>
                        </div>
                    </div>
                `;
            });
            $('#dispositionLegendPillsContainer').html(pillsHtml);
        }

        function loadDispositionChart(params) {
            $('#dispositionLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/disposition-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#dispositionLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (dispositionChart) dispositionChart.destroy();
                        showEmptyState('#dispositionChart', '#emptyStateDisposition');
                        $('#dispositionLegendPillsContainer').empty();
                        return;
                    }

                    cachedDispositionBreakdowns = response.data.disposition_breakdowns || null;
                    renderDispositionChartWithFilter(currentDispositionFilter);
                },
                error: function() {
                    $('#dispositionLoading').addClass('d-none');
                    if (dispositionChart) dispositionChart.destroy();
                    showEmptyState('#dispositionChart', '#emptyStateDisposition');
                    $('#dispositionLegendPillsContainer').empty();
                }
            });
        }

        // 5. Load Vehicle Radar Chart (Armada Mobil Pengangkut)
        function loadVehicleRadarChart(params) {
            $('#vehicleLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/vehicle-condition',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#vehicleLoading').addClass('d-none');

                    if (!response.success || !response.data || !response.data.values) {
                        if (vehicleRadarChart) vehicleRadarChart.destroy();
                        showEmptyState('#vehicleRadarChart', '#emptyStateVehicleRadar');
                        return;
                    }

                    showChart('#vehicleRadarChart', '#emptyStateVehicleRadar');

                    const options = {
                        series: [{
                            name: 'Tingkat Kepatuhan (%)',
                            data: response.data.values
                        }],
                        chart: {
                            height: 290,
                            type: 'radar',
                            toolbar: { show: false }
                        },
                        colors: ['#0f766e'],
                        markers: { size: 4 },
                        yaxis: {
                            min: 0,
                            max: 100,
                            labels: {
                                formatter: function(val) { return val + '%'; }
                            }
                        },
                        xaxis: {
                            categories: response.data.labels
                        }
                    };

                    if (vehicleRadarChart) vehicleRadarChart.destroy();
                    vehicleRadarChart = new ApexCharts(document.querySelector("#vehicleRadarChart"), options);
                    vehicleRadarChart.render();
                }
            });
        }

        // 6. Load Top Materials Chart
        function loadTopMaterialsChart(params) {
            $('#topMaterialsLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/top-materials',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#topMaterialsLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (topMaterialsChart) topMaterialsChart.destroy();
                        showEmptyState('#topMaterialsChart', '#emptyStateTopMaterials');
                        return;
                    }
                    showChart('#topMaterialsChart', '#emptyStateTopMaterials');

                    const options = {
                        series: [{
                            name: 'Kedatangan (Batch)',
                            data: response.data.values
                        }],
                        chart: {
                            type: 'bar',
                            height: 290,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                horizontal: true,
                                distributed: true,
                                dataLabels: { position: 'bottom' }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            textAnchor: 'start',
                            style: { colors: ['#fff'], fontSize: '11px', fontWeight: 600 },
                            formatter: function (val, opt) {
                                return val + ' SPB';
                            },
                            offsetX: 0
                        },
                        xaxis: { categories: response.data.labels },
                        colors: ['#4338ca', '#2563eb', '#0284c7', '#0d9488', '#059669'],
                        legend: { show: false },
                        tooltip: {
                            y: {
                                formatter: function(val, opt) {
                                    let str = val + ' Kedatangan';
                                    if (response.data.volumes && response.data.volumes[opt.dataPointIndex]) {
                                        str += ' (Tonase: ' + response.data.volumes[opt.dataPointIndex] + ')';
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (topMaterialsChart) topMaterialsChart.destroy();
                    topMaterialsChart = new ApexCharts(document.querySelector("#topMaterialsChart"), options);
                    topMaterialsChart.render();
                }
            });
        }

        // 7. Load Supplier Frequency Chart
        function loadSupplierChart(params) {
            $('#supplierLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/supplier-performance',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#supplierLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (supplierChart) supplierChart.destroy();
                        showEmptyState('#supplierChart', '#emptyStateSupplier');
                        return;
                    }
                    showChart('#supplierChart', '#emptyStateSupplier');

                    const chartData = (response.data.totals && response.data.totals.length > 0)
                        ? response.data.totals
                        : response.data.values;

                    const options = {
                        series: [{
                            name: 'Frekuensi Pengiriman (SPB)',
                            data: chartData
                        }],
                        chart: {
                            type: 'bar',
                            height: 290,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                dataLabels: { position: 'top' },
                                columnWidth: '45%'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) { return val + " SPB"; },
                            offsetY: -20,
                            style: { fontSize: '11px', colors: ["#334155"], fontWeight: 600 }
                        },
                        xaxis: {
                            categories: response.data.labels,
                            labels: {
                                rotate: -30,
                                trim: true,
                                maxHeight: 60,
                                style: { fontSize: '11px' }
                            }
                        },
                        yaxis: {
                            forceNiceScale: true,
                            min: 0,
                            labels: {
                                formatter: function(val) { return Math.round(val) + ' SPB'; }
                            }
                        },
                        colors: ['#059669'],
                        tooltip: {
                            y: {
                                formatter: function(val, opt) {
                                    let str = val + ' Pengiriman (SPB)';
                                    if (response.data.values && response.data.values[opt.dataPointIndex] !== undefined) {
                                        str += ' • Mutu: ' + response.data.values[opt.dataPointIndex] + '% Lulus';
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (supplierChart) supplierChart.destroy();
                    supplierChart = new ApexCharts(document.querySelector("#supplierChart"), options);
                    supplierChart.render();
                },
                error: function() {
                    $('#supplierLoading').addClass('d-none');
                    if (supplierChart) supplierChart.destroy();
                    showEmptyState('#supplierChart', '#emptyStateSupplier');
                }
            });
        }

        // 8. Quality Control Charts (Diagram Monitoring Bahan Baku vs Standar Mutu)
        let qualityChartsMap = {};
        let currentSelectedMaterial = 'Gula Tebu';

        $(document).ready(function() {
            // Material Quick Pills handler
            $(document).on('click', '#materialQuickPills .filter-tab-pill', function() {
                $('#materialQuickPills .filter-tab-pill').removeClass('active');
                $(this).addClass('active');
                currentSelectedMaterial = $(this).data('mat') || 'Gula Tebu';
                $('#materialSelectDropdown').val(currentSelectedMaterial);
                loadMaterialQualityTrends(currentSelectedMaterial);
            });

            // Material Dropdown handler
            $('#materialSelectDropdown').on('change', function() {
                currentSelectedMaterial = $(this).val();
                $('#materialQuickPills .filter-tab-pill').removeClass('active');
                $(`#materialQuickPills .filter-tab-pill[data-mat="${currentSelectedMaterial}"]`).addClass('active');
                loadMaterialQualityTrends(currentSelectedMaterial);
            });
        });

        function destroyAllQualityCharts() {
            for (const key in qualityChartsMap) {
                if (qualityChartsMap[key] && typeof qualityChartsMap[key].destroy === 'function') {
                    try { qualityChartsMap[key].destroy(); } catch (e) {}
                }
            }
            qualityChartsMap = {};
        }

        function loadMaterialQualityTrends(material) {
            $('#qualityChartsGrid').html(`
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat diagram mutu...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0 small">Menyiapkan diagram kontrol mutu ${material}...</p>
                </div>
            `);
            $('#materialChartsEmpty').addClass('d-none');

            const params = getFilterParams();
            params.material = material;

            $.ajax({
                url: '/api/dashboard/rmpm/material-quality-trends',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (response.success && response.parameters && response.parameters.length > 0) {
                        renderQualityCharts(response);
                    } else {
                        destroyAllQualityCharts();
                        $('#qualityChartsGrid').html('');
                        $('#materialChartsEmpty').removeClass('d-none');
                    }
                },
                error: function() {
                    destroyAllQualityCharts();
                    $('#qualityChartsGrid').html('<div class="col-12 text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2 mb-0">Gagal memuat diagram kontrol mutu.</p></div>');
                }
            });
        }

        function renderQualityCharts(data) {
            destroyAllQualityCharts();

            // Update banner titles & stats
            $('#activeMaterialTitle').text(data.material_name.toUpperCase());
            const paramNames = data.parameters.map(p => p.parameter).join(', ');
            $('#activeMaterialParamsList').text(paramNames);
            $('#activeMatBatchCount').text(data.total_transactions);
            $('#collapseLogTitle').text(`Lihat Log Data Transaksi Teruji (${data.material_name})`);

            // Calculate overall in-spec rate across all parameters
            let totalTestedPoints = 0;
            let totalInSpecPoints = 0;
            data.parameters.forEach(p => {
                totalTestedPoints += p.stats.total;
                totalInSpecPoints += p.stats.in_spec;
            });
            const overallRate = totalTestedPoints > 0 ? Math.round((totalInSpecPoints / totalTestedPoints) * 100) : 100;
            $('#activeMatInSpecRate').text(overallRate + '%');

            // Populate dropdown options if available
            if (data.available_materials && data.available_materials.length > 0) {
                let opts = '';
                data.available_materials.forEach(m => {
                    const isSelected = (m.name === data.material_name || m.name === currentSelectedMaterial);
                    opts += `<option value="${m.name}" ${isSelected ? 'selected' : ''}>${m.name} (${m.count} SPB)</option>`;
                });
                $('#materialSelectDropdown').html(opts);
            }

            // Render Cards in Grid
            let cardsHtml = '';
            data.parameters.forEach((param, idx) => {
                const chartId = `paramChart_${idx}`;
                const inSpecPct = param.stats.in_spec_pct;
                let badgeClass = 'bg-success-subtle text-success border-success';
                if (inSpecPct < 60) badgeClass = 'bg-danger-subtle text-danger border-danger';
                else if (inSpecPct < 90) badgeClass = 'bg-warning-subtle text-warning border-warning';

                cardsHtml += `
                    <div class="col-xl-6 col-lg-6">
                        <div class="card h-100 border shadow-xs" style="border-radius: 10px; background: #ffffff;">
                            <div class="card-header bg-white border-bottom py-3 px-3.5 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 fw-bold text-dark fs-6">
                                            <i class="ri-pulse-line text-primary me-1"></i> ${param.parameter}
                                        </h6>
                                        ${param.uom ? `<span class="badge bg-light text-dark border font-monospace">${param.uom}</span>` : ''}
                                    </div>
                                    <p class="text-muted mb-0 small" style="font-size: 11.5px;">
                                        Batas Standar QC: <strong class="text-dark">${param.standard_label}</strong> &bull; Rata-rata: <strong>${param.stats.avg} ${param.uom}</strong>
                                    </p>
                                </div>
                                <div>
                                    <span class="badge ${badgeClass} px-2.5 py-1.5" style="font-size: 11.5px;">
                                        <i class="ri-shield-check-line me-1"></i> ${inSpecPct}% Lolos
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div id="${chartId}" style="min-height: 270px;"></div>
                            </div>
                            <div class="card-footer bg-light-subtle border-top py-2 px-3.5 d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                                <span><i class="ri-truck-line me-1"></i> ${param.stats.total} SPB Teruji</span>
                                <span><span class="text-success fw-bold">${param.stats.in_spec} Sesuai</span> &bull; <span class="text-danger fw-bold">${param.stats.out_spec} Menyimpang</span></span>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#qualityChartsGrid').html(cardsHtml);

            // Initialize each ApexChart
            data.parameters.forEach((param, idx) => {
                const chartId = `paramChart_${idx}`;
                const points = param.point_details || [];

                // Y-axis min and max bounds for sensible chart limits
                let yMinVal = param.stats.min_actual;
                let yMaxVal = param.stats.max_actual;
                if (param.min !== null) yMinVal = (yMinVal !== null) ? Math.min(yMinVal, param.min) : param.min;
                if (param.max !== null) yMaxVal = (yMaxVal !== null) ? Math.max(yMaxVal, param.max) : param.max;

                let padding = 1;
                if (yMinVal !== null && yMaxVal !== null) {
                    const diff = yMaxVal - yMinVal;
                    padding = diff > 0 ? diff * 0.2 : (yMinVal > 0 ? yMinVal * 0.1 : 1);
                }
                const yMinChart = (yMinVal !== null) ? Math.max(0, Math.floor(yMinVal - padding)) : undefined;
                const yMaxChart = (yMaxVal !== null) ? Math.ceil(yMaxVal + padding) : undefined;

                // Annotations for standard limits
                const yAnnotations = [];
                if (param.min !== null) {
                    yAnnotations.push({
                        y: param.min,
                        borderColor: '#f59e0b',
                        strokeDashArray: 4,
                        borderWidth: 2,
                        label: {
                            borderColor: '#f59e0b',
                            style: { color: '#ffffff', background: '#f59e0b', fontSize: '11px', fontWeight: 600 },
                            text: `Min Std: ${param.min} ${param.uom}`,
                            position: 'left'
                        }
                    });
                }
                if (param.max !== null) {
                    yAnnotations.push({
                        y: param.max,
                        borderColor: '#ef4444',
                        strokeDashArray: 4,
                        borderWidth: 2,
                        label: {
                            borderColor: '#ef4444',
                            style: { color: '#ffffff', background: '#ef4444', fontSize: '11px', fontWeight: 600 },
                            text: `Maks Std: ${param.max} ${param.uom}`,
                            position: 'right'
                        }
                    });
                }

                const options = {
                    series: [{
                        name: `Hasil Lab ${param.parameter}`,
                        data: param.values
                    }],
                    chart: {
                        type: 'line',
                        height: 270,
                        toolbar: { show: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 600 }
                    },
                    stroke: {
                        curve: 'straight',
                        width: 3
                    },
                    colors: ['#4338ca'],
                    markers: {
                        size: 6,
                        strokeWidth: 2,
                        strokeColors: '#ffffff',
                        hover: { size: 8 },
                        discrete: points.map((pt, pIdx) => ({
                            seriesIndex: 0,
                            dataPointIndex: pIdx,
                            fillColor: pt.is_compliant ? '#10b981' : '#ef4444',
                            strokeColor: '#ffffff',
                            size: 6
                        }))
                    },
                    xaxis: {
                        categories: param.categories,
                        labels: {
                            rotate: -30,
                            trim: true,
                            maxHeight: 50,
                            style: { fontSize: '11px', fontWeight: 600 }
                        },
                        title: { text: 'No. SPB / Batch Kedatangan', style: { fontSize: '11px', color: '#64748b' } }
                    },
                    yaxis: {
                        min: yMinChart,
                        max: yMaxChart,
                        title: {
                            text: param.uom ? `${param.parameter} (${param.uom})` : param.parameter,
                            style: { fontSize: '11.5px', fontWeight: 600 }
                        },
                        labels: {
                            formatter: function(val) {
                                return typeof val === 'number' ? (Number.isInteger(val) ? val : val.toFixed(1)) : val;
                            }
                        }
                    },
                    annotations: {
                        yaxis: yAnnotations
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 3
                    },
                    tooltip: {
                        custom: function({ series, seriesIndex, dataPointIndex }) {
                            const pt = points[dataPointIndex];
                            if (!pt) return '';
                            const isGood = pt.is_compliant;
                            const statusBadge = isGood
                                ? '<span class="badge bg-success-subtle text-success py-1 px-2"><i class="ri-checkbox-circle-fill me-1"></i>Sesuai Standar</span>'
                                : '<span class="badge bg-danger-subtle text-danger py-1 px-2"><i class="ri-alert-fill me-1"></i>Menyimpang (Out of Spec)</span>';

                            return `
                                <div class="p-2.5 bg-white shadow border rounded" style="font-size: 12px; min-width: 200px;">
                                    <div class="fw-bold text-dark border-bottom pb-1 mb-1.5">
                                        SPB: <span class="text-primary font-monospace">${pt.spb}</span>
                                    </div>
                                    <div class="text-muted small">Pemasok: <strong>${pt.supplier}</strong></div>
                                    <div class="text-muted small">Tgl Masuk: ${pt.full_date || pt.date}</div>
                                    <div class="mt-2 pt-1.5 border-top d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Hasil Uji Lab:</span>
                                        <strong class="fs-6 ${isGood ? 'text-success' : 'text-danger'}">${pt.formatted}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted mt-1">
                                        <span>Batas Standar:</span>
                                        <span class="badge bg-light text-dark border">${param.standard_label}</span>
                                    </div>
                                    <div class="mt-2 pt-1.5 border-top text-center">
                                        ${statusBadge}
                                    </div>
                                </div>
                            `;
                        }
                    }
                };

                const chartElem = document.querySelector(`#${chartId}`);
                if (chartElem) {
                    const chart = new ApexCharts(chartElem, options);
                    chart.render();
                    qualityChartsMap[chartId] = chart;
                }
            });

            // Populate Collapsible Log Table
            let logRows = '';
            if (data.recent_transactions && data.recent_transactions.length > 0) {
                data.recent_transactions.forEach(t => {
                    let dispBadge = '<span class="badge bg-light text-muted">Pending</span>';
                    const dLower = t.disposisi.toLowerCase();
                    if (dLower.includes('release') && !dLower.includes('bersyarat')) dispBadge = '<span class="badge bg-success-subtle text-success">Release</span>';
                    else if (dLower.includes('bersyarat')) dispBadge = '<span class="badge bg-warning-subtle text-warning">Release Bersyarat</span>';
                    else if (dLower.includes('reject') || dLower.includes('tolak')) dispBadge = '<span class="badge bg-danger-subtle text-danger">Reject</span>';

                    logRows += `
                        <tr>
                            <td class="fw-semibold text-dark">${t.date}</td>
                            <td class="font-monospace fw-bold text-primary">${t.no_spb}</td>
                            <td>${t.lot_batch}</td>
                            <td>${t.supplier}</td>
                            <td class="fw-semibold">${t.jumlah}</td>
                            <td>${dispBadge}</td>
                            <td class="text-center text-nowrap">
                                <a href="${t.analisa_url}" class="btn btn-sm btn-light border py-0 px-2" title="Form Analisa"><i class="ri-flask-line text-success"></i></a>
                                <a href="${t.detail_url}" class="btn btn-sm btn-light border py-0 px-2 ms-1" title="Detail"><i class="ri-eye-line text-primary"></i></a>
                            </td>
                        </tr>
                    `;
                });
            } else {
                logRows = '<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada catatan transaksi untuk bahan ini</td></tr>';
            }
            $('#materialTxTableBody').html(logRows);
        }
    </script>
@endsection
