@extends('layouts.component.main')
@section('title', 'Dashboard - Packaging Material (PM)')
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

        .bg-teal-light {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        .bg-purple-light {
            background-color: #f3e8ff;
            color: #7e22ce;
        }

        .text-purple {
            color: #7e22ce !important;
        }

        #categoryChartFilterGroup .btn,
        #dispositionChartFilterGroup .btn {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
        }

        #categoryChartFilterGroup .btn.active {
            background-color: #059669;
            color: #ffffff;
            border-color: #059669;
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

        .section-banner {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        .section-pill {
            background-color: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .pillar-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }

        .pillar-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
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
                            <h4 class="mb-1 text-success fw-bold">
                                <i class="mdi mdi-package-variant-closed me-1"></i> Dashboard - Packaging Material (PM)
                            </h4>
                            <p class="text-muted mb-0 small">
                                Monitoring analisa mutu kemasan, pemeriksaan sampling (Karton, Pouch, Inner/Outer), analisis cacat mutu (defects), dan kedatangan packaging
                            </p>
                        </div>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">RMPM</a></li>
                                <li class="breadcrumb-item active">Packaging Material</li>
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
                        <label class="form-label"><i class="ri-archive-line me-1"></i> Kategori Kemasan (PM)</label>
                        <select class="form-select" id="pmCategoryFilter">
                            <option value="all" selected>Semua Kategori (Pouch, Karton, Inner/Outer)</option>
                            <option value="pouch">Pouch (Refill, Sachet, Stand Pouch)</option>
                            <option value="karton">Karton Box (Master Box / Corrugated)</option>
                            <option value="inner_outer">Inner / Outer (Roll, Film, Label, dll)</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-md-6 ms-auto d-flex gap-2">
                        <button class="btn btn-success flex-grow-1" id="applyFilter">
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
                <!-- Card 1: Total Kedatangan PM -->
                <div class="col-xxl col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary-light">
                                    <i class="ri-archive-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Total Kedatangan PM</p>
                                    <h3 class="stats-value" id="totalArrivals">-</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Total Kuantitas Masuk</span>
                                <span class="badge bg-primary-light" id="totalVolumeFormatted">- Unit</span>
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
                                <span class="stats-subtitle">Status Disetujui (Release)</span>
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
                                <span class="stats-subtitle">Status Ditolak (Reject / Hold)</span>
                                <span class="badge bg-danger-light" id="rejectCountBadge">- Batch</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Kepatuhan Dokumen COA -->
                <div class="col-xxl col-lg-6 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info-light">
                                    <i class="ri-file-shield-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Kelengkapan COA</p>
                                    <h3 class="stats-value text-info" id="documentCompleteness">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Sertifikat Mutu Pemasok</span>
                                <span class="badge bg-info-light">COA &amp; Surat Jalan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Tingkat Bebas Cacat Kemasan -->
                <div class="col-xxl col-lg-6 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-teal-light">
                                    <i class="ri-shield-check-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="stats-label mb-0">Tingkat Bebas Cacat</p>
                                    <h3 class="stats-value" style="color: #0f766e;" id="defectFreeRate">-%</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="stats-subtitle">Standar Mutu Fisik &amp; Cetak</span>
                                <span class="badge bg-teal-light">Defect-Free Rate</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 1: Frekuensi Kedatangan Packaging Material -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Frekuensi Kedatangan Packaging Material</h5>
                                <p class="text-muted mb-0 small">Frekuensi pengiriman bahan kemas masuk per tanggal kedatangan</p>
                            </div>
                            <span class="badge bg-light text-dark border px-2 py-1" id="totalPmBatchesBadge">10 Kedatangan</span>
                        </div>
                        <div class="card-body position-relative">
                            <div id="trendChart"></div>
                            <div id="emptyStateTrend" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Tidak Tersedia</h6>
                                <p class="text-muted small">Tidak ada transaksi kedatangan kemasan pada rentang waktu ini</p>
                            </div>
                            <div class="loading-overlay d-none" id="trendLoading">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Komposisi Kategori & Distribusi Disposisi PM -->
            <div class="row g-3 mb-4">
                <!-- Komposisi Kedatangan Kategori Kemasan -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="chart-title mb-1">Komposisi Kedatangan Kategori Kemasan</h5>
                                <p class="text-muted mb-0 small">Perbandingan volume kedatangan Pouch, Karton Box, dan Inner/Outer</p>
                            </div>
                            <!-- Filter Pill Buttons (Pouch, Karton, Inner/Outer, Semua) -->
                            <div class="btn-group btn-group-sm" role="group" id="categoryChartFilterGroup">
                                <button type="button" class="btn btn-sm btn-outline-success active" data-cat="all">Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-cat="pouch">Pouch</button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-cat="karton">Karton</button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-cat="inner_outer">Inner/Outer</button>
                            </div>
                        </div>
                        <div class="card-body position-relative">
                            <div id="pmCategoryChart"></div>
                            <div id="emptyStatePmCategory" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Kategori Tidak Ditemukan</h6>
                                <p class="text-muted small">Tidak ada kedatangan kemasan pada periode ini</p>
                            </div>

                            <!-- Legend Pills Container (Dynamically rendered based on active filter) -->
                            <div class="row g-2 mt-2 pt-2 border-top text-center" id="categoryLegendPillsContainer" style="font-size: 11px;">
                                <div class="col-4">
                                    <div class="p-2 rounded bg-purple-light">
                                        <div class="fw-bold text-purple" id="pmPouchBadge">- SPB</div>
                                        <div class="text-muted small">Pouch (Refill/Sachet)</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-warning-light">
                                        <div class="fw-bold text-warning" id="pmKartonBadge">- SPB</div>
                                        <div class="text-muted small">Karton (Master Box)</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-info-light">
                                        <div class="fw-bold text-info" id="pmInnerOuterBadge">- SPB</div>
                                        <div class="text-muted small">Inner / Outer (Film/Roll)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Disposisi Packaging Material -->
                <div class="col-xl-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="chart-title mb-1">Distribusi Rekomendasi / Disposisi Mutu Kemasan</h5>
                                <p class="text-muted mb-0 small">Keputusan kelulusan QC terhadap kedatangan kemasan</p>
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
                                <p class="text-muted small">Tidak ada data rekomendasi kemasan yang tercatat</p>
                            </div>
                            <div class="loading-overlay d-none" id="dispositionLoading">
                                <div class="spinner-border text-success" role="status">
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

            <!-- Section 3: Analisa Cacat Kemasan, Top Bahan Kemas & Kinerja Pemasok -->
            <div class="row g-3 mb-4">
                <!-- Temuan Ketidaksesuaian Kemasan (Defects) -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Analisa Ketidaksesuaian Kemasan (Defects)</h5>
                                <p class="text-muted mb-0 small">Frekuensi jenis cacat fisik &amp; cetak kemasan</p>
                            </div>
                            <i class="ri-alert-line text-danger fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="packagingFindingsChart"></div>
                            <div id="emptyStatePackagingFindings" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Bebas Temuan Cacat</h6>
                                <p class="text-muted small">Tidak ditemukan ketidaksesuaian pada kemasan teruji</p>
                            </div>
                            <div class="loading-overlay d-none" id="packagingLoading">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MID Kemas Paling Banyak -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">MID Paling Banyak Masuk</h5>
                                <p class="text-muted mb-0 small">Volume kedatangan bahan kemas berdasarkan nomor MID</p>
                            </div>
                            <i class="ri-bar-chart-horizontal-line text-muted fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="topMaterialsChart"></div>
                            <div id="emptyStateTopMaterials" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Tidak Tersedia</h6>
                                <p class="text-muted small">Tidak ada data MID kemas masuk</p>
                            </div>
                            <div class="loading-overlay d-none" id="topMaterialsLoading">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frekuensi Supplier Packaging Material -->
                <div class="col-xl-4 col-lg-12">
                    <div class="card chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">Frekuensi Supplier</h5>
                                <p class="text-muted mb-0 small">Frekuensi pengiriman bahan kemas per pemasok (SPB)</p>
                            </div>
                            <i class="ri-building-line text-muted fs-5"></i>
                        </div>
                        <div class="card-body position-relative">
                            <div id="supplierChart"></div>
                            <div id="emptyStateSupplier" class="text-center py-5 d-none">
                                <i class="ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                <h6 class="mt-2 text-muted">Data Supplier Tidak Ada</h6>
                                <p class="text-muted small">Belum ada evaluasi supplier kemasan</p>
                            </div>
                            <div class="loading-overlay d-none" id="supplierLoading">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Tabel Kedatangan & Status Sampling PM Terkini -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1 fw-bold text-dark">Data Kedatangan &amp; Status Sampling Packaging Material</h5>
                                <p class="text-muted mb-0 small">Rincian kedatangan armada, kategori kemasan, kelengkapan COA, disposisi mutu, dan status temuan cacat</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('rmpm.pm.incoming.create') }}" class="btn btn-sm btn-success">
                                    <i class="ri-add-line me-1"></i> Input Incoming PM
                                </a>
                                <a href="{{ route('rmpm.pm') }}" class="btn btn-sm btn-outline-success">
                                    <i class="ri-external-link-line me-1"></i> Buka Menu PM
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. SPB / Batch</th>
                                            <th>Armada Mobil</th>
                                            <th>Pemasok</th>
                                            <th>Kategori Kemasan</th>
                                            <th>Jenis Material</th>
                                            <th>Jumlah</th>
                                            <th>Status COA</th>
                                            <th>Disposisi Mutu</th>
                                            <th>Status Cacat</th>
                                            <th class="text-center">Aksi Sampling</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentDataTable">
                                        <tr>
                                            <td colspan="11" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                                Memuat data kedatangan bahan kemas...
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
        let trendChart, dispositionChart, topMaterialsChart, packagingFindingsChart, supplierChart, pmCategoryChart;
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
                $('#pmCategoryFilter').val('all');
                $('#datePickerCol').hide();
                $('#categoryChartFilterGroup button').removeClass('active');
                $('#categoryChartFilterGroup button[data-cat="all"]').addClass('active');
                currentCategoryFilter = 'all';
                $('#dispositionChartFilterGroup button').removeClass('active');
                $('#dispositionChartFilterGroup button[data-disp="all"]').addClass('active');
                currentDispositionFilter = 'all';
                loadAllData();
            });

            // Filter pill buttons for Komposisi Kategori Kemasan
            $('#categoryChartFilterGroup button').on('click', function() {
                $('#categoryChartFilterGroup button').removeClass('active');
                $(this).addClass('active');
                const cat = $(this).data('cat');
                renderCategoryChartWithFilter(cat);
            });

            // Filter pill buttons for Distribusi Disposisi Mutu Kemasan
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
            const pmCategory = $('#pmCategoryFilter').val();

            const params = {
                category: 'pm',
                period: period,
                pm_category: pmCategory
            };

            if (period === 'custom' && date) {
                params.date = date;
            }

            return params;
        }

        function loadAllData() {
            const params = getFilterParams();

            loadStatistics(params);
            loadTrendChart(params);
            loadDispositionChart(params);
            loadTopMaterialsChart(params);
            loadPackagingFindingsChart(params);
            loadSupplierChart(params);
            loadRecentData(params);
        }

        // 1. Load Statistics & 3 Pillar Distribution
        function loadStatistics(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/statistics',
                method: 'GET',
                data: params,
                success: function(response) {
                    if (response.success && response.data) {
                        const d = response.data;
                        const total = d.total_arrivals ?? d.total_kedatangan ?? 0;
                        $('#totalArrivals').text(total);
                        $('#totalVolumeFormatted').text(d.total_volume_formatted ?? '0 Unit');
                        $('#acceptanceRate').text((d.acceptance_rate ?? 0) + '%');
                        $('#acceptCountBadge').text((d.accept_count ?? 0) + ' Batch');
                        $('#rejectionRate').text((d.rejection_rate ?? 0) + '%');
                        $('#rejectCountBadge').text((d.reject_count ?? 0) + ' Batch');
                        $('#documentCompleteness').text((d.document_completeness ?? 0) + '%');
                        $('#defectFreeRate').text((d.defect_free_rate ?? 100) + '%');

                        $('#totalPmBatchesBadge').text(total + ' Kedatangan Terdata');

                        // Save category breakdowns & render active filter
                        cachedCategoryBreakdowns = d.category_breakdowns || null;
                        renderCategoryChartWithFilter(currentCategoryFilter);
                    }
                },
                error: function() {
                    $('#totalArrivals').text('0');
                    $('#totalVolumeFormatted').text('0 Unit');
                    $('#acceptanceRate').text('0%');
                    $('#rejectionRate').text('0%');
                    $('#documentCompleteness').text('0%');
                    $('#defectFreeRate').text('100%');
                }
            });
        }

        let currentCategoryFilter = 'all';
        let cachedCategoryBreakdowns = null;

        function renderCategoryChartWithFilter(catType) {
            currentCategoryFilter = catType || 'all';

            if (!cachedCategoryBreakdowns || !cachedCategoryBreakdowns[currentCategoryFilter]) {
                if (pmCategoryChart) pmCategoryChart.destroy();
                $('#pmCategoryChart').hide();
                $('#emptyStatePmCategory').removeClass('d-none');
                $('#categoryLegendPillsContainer').empty();
                return;
            }

            const data = cachedCategoryBreakdowns[currentCategoryFilter];
            const total = data.total || 0;
            const values = data.values || [];
            const labels = data.labels || [];
            const colors = data.colors || ['#8b5cf6', '#f59e0b', '#06b6d4'];
            const centerTitle = data.title || 'Total';

            if (total === 0 || values.length === 0 || values.every(v => v === 0)) {
                if (pmCategoryChart) pmCategoryChart.destroy();
                $('#pmCategoryChart').hide();
                $('#emptyStatePmCategory').removeClass('d-none');
                $('#categoryLegendPillsContainer').empty();
                return;
            }

            $('#emptyStatePmCategory').addClass('d-none');
            $('#pmCategoryChart').show();

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

            if (pmCategoryChart) pmCategoryChart.destroy();
            pmCategoryChart = new ApexCharts(document.querySelector("#pmCategoryChart"), options);
            pmCategoryChart.render();

            // Render Dynamic Legend Pills
            let pillsHtml = '';
            const pills = data.pills || [];
            const colSize = pills.length === 1 ? 'col-12' : (pills.length === 2 ? 'col-6' : (pills.length === 3 ? 'col-4' : 'col-md-3 col-6'));
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
            $('#categoryLegendPillsContainer').html(pillsHtml);
        }

        // 2. Load Trend Chart
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
                            name: 'Kedatangan Packaging (SPB)',
                            data: response.data.packaging
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
                        colors: ['#059669'],
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
                                formatter: function(val) {
                                    return val + ' Pengiriman Masuk';
                                }
                            }
                        }
                    };

                    if (trendChart) trendChart.destroy();
                    trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
                    trendChart.render();
                },
                error: function() {
                    $('#trendLoading').addClass('d-none');
                    if (trendChart) trendChart.destroy();
                    showEmptyState('#trendChart', '#emptyStateTrend');
                }
            });
        }

        // 3. Load Disposition Chart
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
            const centerTitle = data.title || 'Total Sampel';

            if (total === 0 || values.length === 0 || values.every(v => v === 0)) {
                if (dispositionChart) dispositionChart.destroy();
                $('#dispositionChart').hide();
                $('#emptyStateDisposition').removeClass('d-none');
                $('#dispositionLegendPillsContainer').html(`
                    <div class="col-12 py-2 text-muted fst-italic">
                        Tidak ada data kemasan dengan disposisi ini
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
            const colSize = pills.length === 1 ? 'col-12' : (pills.length === 2 ? 'col-6' : (pills.length === 3 ? 'col-4' : 'col-md-3 col-6'));
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

        // 4. Load Packaging Findings Chart (Defects)
        function loadPackagingFindingsChart(params) {
            $('#packagingLoading').removeClass('d-none');

            $.ajax({
                url: '/api/dashboard/rmpm/packaging-findings',
                method: 'GET',
                data: params,
                success: function(response) {
                    $('#packagingLoading').addClass('d-none');

                    if (!response.success || response.data.values.length === 0) {
                        if (packagingFindingsChart) packagingFindingsChart.destroy();
                        showEmptyState('#packagingFindingsChart', '#emptyStatePackagingFindings');
                        return;
                    }
                    showChart('#packagingFindingsChart', '#emptyStatePackagingFindings');

                    const options = {
                        series: [{
                            name: 'Frekuensi Temuan Cacat',
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
                                distributed: true
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                return val > 0 ? val + ' Kasus' : '';
                            },
                            style: { fontSize: '11px', fontWeight: 600 }
                        },
                        xaxis: { categories: response.data.labels },
                        colors: ['#ef4444', '#f97316', '#f59e0b', '#8b5cf6', '#06b6d4', '#64748b'],
                        legend: { show: false },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' Temuan Cacat';
                                }
                            }
                        }
                    };

                    if (packagingFindingsChart) packagingFindingsChart.destroy();
                    packagingFindingsChart = new ApexCharts(document.querySelector("#packagingFindingsChart"), options);
                    packagingFindingsChart.render();
                },
                error: function() {
                    $('#packagingLoading').addClass('d-none');
                    if (packagingFindingsChart) packagingFindingsChart.destroy();
                    showEmptyState('#packagingFindingsChart', '#emptyStatePackagingFindings');
                }
            });
        }

        // 5. Load Top Materials Chart
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
                            name: 'Jumlah Pengiriman (SPB)',
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
                            formatter: function (val) {
                                return val + ' SPB';
                            },
                            offsetX: 0
                        },
                        xaxis: {
                            categories: response.data.labels.map(function(l) { return String(l); }),
                            labels: {
                                style: { fontSize: '11px', fontWeight: 600 }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: { fontSize: '12px', fontWeight: 700 }
                            }
                        },
                        colors: ['#059669', '#10b981', '#34d399', '#0d9488', '#0284c7'],
                        legend: { show: false },
                        tooltip: {
                            y: {
                                formatter: function(val, opt) {
                                    let str = val + ' Pengiriman (SPB)';
                                    const idx = opt.dataPointIndex;
                                    if (response.data.materials && response.data.materials[idx]) {
                                        str += ' • Jenis: ' + response.data.materials[idx];
                                    }
                                    if (response.data.volumes && response.data.volumes[idx]) {
                                        str += ' (' + response.data.volumes[idx] + ')';
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (topMaterialsChart) topMaterialsChart.destroy();
                    topMaterialsChart = new ApexCharts(document.querySelector("#topMaterialsChart"), options);
                    topMaterialsChart.render();
                },
                error: function() {
                    $('#topMaterialsLoading').addClass('d-none');
                    if (topMaterialsChart) topMaterialsChart.destroy();
                    showEmptyState('#topMaterialsChart', '#emptyStateTopMaterials');
                }
            });
        }

        // 6. Load Supplier Performance Chart
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

        // 7. Load Recent Data Table
        function loadRecentData(params) {
            $.ajax({
                url: '/api/dashboard/rmpm/recent-data',
                method: 'GET',
                data: params,
                success: function(response) {
                    let rows = '';

                    if (!response.data || response.data.length === 0) {
                        rows = '<tr><td colspan="11" class="text-center py-4 text-muted">Tidak ada catatan kedatangan bahan kemas yang sesuai</td></tr>';
                    } else {
                        response.data.forEach(function(item) {
                            // Disposisi badge
                            let disposisiBadge = '';
                            const dispLower = (item.disposisi || '').toLowerCase();
                            if (dispLower.includes('release') || dispLower.includes('diterima')) {
                                if (dispLower.includes('bersyarat')) {
                                    disposisiBadge = '<span class="status-badge status-pending"><i class="ri-alert-line me-1"></i>Release Bersyarat</span>';
                                } else {
                                    disposisiBadge = '<span class="status-badge status-accept"><i class="ri-check-line me-1"></i>Release</span>';
                                }
                            } else if (dispLower.includes('reject') || dispLower.includes('tolak')) {
                                disposisiBadge = '<span class="status-badge status-reject"><i class="ri-close-line me-1"></i>Reject</span>';
                            } else {
                                disposisiBadge = '<span class="status-badge status-info"><i class="ri-time-line me-1"></i>' + (item.disposisi || 'Belum Sampling') + '</span>';
                            }

                            // Dokumen badge
                            let dokumenBadge = '';
                            if (item.dokumen_status === 'COA Lengkap' || item.dokumen_status === 'Lengkap') {
                                dokumenBadge = '<span class="badge bg-success-subtle text-success">COA Ada</span>';
                            } else if (item.dokumen_status === 'Belum Sampling') {
                                dokumenBadge = '<span class="badge bg-warning-subtle text-warning">Belum Sampling</span>';
                            } else {
                                dokumenBadge = '<span class="badge bg-danger-subtle text-danger">' + item.dokumen_status + '</span>';
                            }

                            // Category badge
                            let categoryBadge = '<span class="badge bg-light text-muted border">' + (item.category_name || 'Packaging') + '</span>';
                            if (item.category_name === 'Pouch') {
                                categoryBadge = '<span class="badge bg-purple-light">Pouch</span>';
                            } else if (item.category_name === 'Karton Box' || item.category_name === 'Karton') {
                                categoryBadge = '<span class="badge bg-warning-light">Karton Box</span>';
                            } else if (item.category_name === 'Inner/Outer') {
                                categoryBadge = '<span class="badge bg-info-light">Inner/Outer</span>';
                            }

                            // Cacat badge
                            let cacatBadge = item.has_defect ?
                                `<span class="badge bg-danger-subtle text-danger" title="${item.defect_notes}"><i class="ri-error-warning-line me-1"></i>${item.defect_notes}</span>` :
                                `<span class="badge bg-success-subtle text-success"><i class="ri-checkbox-circle-line me-1"></i>Bebas Cacat</span>`;

                            // Action buttons
                            const samplingBtn = item.sampling_url ?
                                `<a href="${item.sampling_url}" class="btn btn-sm btn-light border py-1 px-2" title="Form Sampling Mutu"><i class="ri-file-edit-line text-success"></i></a>` : '';
                            const editBtn = item.edit_url ?
                                `<a href="${item.edit_url}" class="btn btn-sm btn-light border py-1 px-2 ms-1" title="Edit Incoming"><i class="ri-pencil-line text-primary"></i></a>` : '';

                            rows += `
                            <tr>
                                <td class="fw-semibold text-dark">${item.tanggal}</td>
                                <td>
                                    <span class="fw-bold">${item.no_spb}</span>
                                    ${item.no_batch && item.no_batch !== '-' ? '<br><small class="text-muted">Batch: ' + item.no_batch + '</small>' : ''}
                                </td>
                                <td><span class="badge bg-light text-dark border">${item.no_mobil || '-'}</span></td>
                                <td>${item.supplier}</td>
                                <td>${categoryBadge}</td>
                                <td><span class="fw-semibold text-dark">${item.jenis}</span></td>
                                <td class="fw-semibold">${item.jumlah}</td>
                                <td>${dokumenBadge}</td>
                                <td>${disposisiBadge}</td>
                                <td>${cacatBadge}</td>
                                <td class="text-center text-nowrap">${samplingBtn}${editBtn}</td>
                            </tr>
                            `;
                        });
                    }

                    $('#recentDataTable').html(rows);
                },
                error: function() {
                    $('#recentDataTable').html('<tr><td colspan="11" class="text-center py-4 text-muted">Gagal memuat data kedatangan bahan kemas</td></tr>');
                }
            });
        }
    </script>
@endsection
