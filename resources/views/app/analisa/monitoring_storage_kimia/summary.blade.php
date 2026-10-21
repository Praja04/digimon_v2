@extends('layouts.component.main')
@section('title', 'Summary Laporan Monitoring Storage Kimia - ' . ($productionBatch->po_number ?? ''))

@section('styles')
<style>
    .report-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 24px;
        margin-bottom: 25px;
    }

    .report-header-banner {
        border-bottom: 2px solid #0f172a;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .doc-logo-img {
        max-height: 48px;
        object-fit: contain;
    }

    .doc-main-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .doc-sub-title {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .meta-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 12px 16px;
    }

    .meta-box dt {
        font-size: 0.78rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .meta-box dd {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0;
    }

    /* Comparison Table Styling */
    .table-comparison {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        background: #ffffff;
    }

    .table-comparison th, .table-comparison td {
        border: 1px solid #cbd5e1;
        padding: 7px 10px;
        vertical-align: middle;
    }

    .table-comparison thead th {
        background-color: #0f172a;
        color: #ffffff;
        text-align: center;
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.3px;
    }

    .table-comparison thead th.th-param {
        background-color: #1e293b;
        text-align: left;
        width: 25%;
    }

    .table-comparison thead th.th-sample {
        width: 25%;
    }

    .table-comparison tbody tr.section-title-row td {
        background-color: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-top: 2px solid #94a3b8;
    }

    .table-comparison tbody td.param-name {
        font-weight: 600;
        color: #334155;
    }

    .table-comparison tbody td.sample-val {
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .blending-card-badge {
        background: #0f172a;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Sign-off box */
    .sign-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 0.82rem;
    }

    .sign-table td {
        border: 1px solid #64748b;
        text-align: center;
        padding: 8px;
        width: 33.33%;
    }

    .sign-space {
        height: 50px;
    }

    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 8.5pt !important;
        }

        .d-print-none, .page-title-box, .app-menu, .navbar-header, footer {
            display: none !important;
        }

        .page-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .report-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .table-comparison thead th {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-comparison tbody tr.section-title-row td {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .blending-card-badge {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
            border: 1px solid #000000;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb & Top Bar -->
        <div class="row d-print-none">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Summary Laporan Monitoring Storage Kimia</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-storage-kimia.index') }}">Monitoring Storage Kimia</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-storage-kimia.show', $productionBatch->id) }}">{{ $productionBatch->po_number }}</a></li>
                            <li class="breadcrumb-item active">Summary Laporan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
            <a href="{{ route('analisa.monitoring-storage-kimia.show', $productionBatch->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Detail PO
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="ri-printer-line me-1"></i> Cetak / Print Laporan
                </button>
            </div>
        </div>

        <!-- Report Main Card -->
        <div class="report-card">
            <!-- Header Banner -->
            <div class="report-header-banner">
                <div class="row align-items-center">
                    <div class="col-md-3 col-4">
                        <img src="{{ asset('assets/images/logo-miesedaap.png') }}" alt="Logo Mie Sedaap" class="doc-logo-img">
                    </div>
                    <div class="col-md-6 col-8 text-center">
                        <div class="doc-main-title">SUMMARY LAPORAN MONITORING STORAGE KIMIA</div>
                        <div class="doc-sub-title">Perbandingan Parameter Analisa Sampel (Awal, Tengah, Akhir) per PO</div>
                    </div>
                    <div class="col-md-3 col-12 text-md-end text-start mt-2 mt-md-0">
                        <span class="badge {{ $summaryData['is_complete'] ? 'bg-success' : 'bg-warning' }} fs-12 px-3 py-2">
                            <i class="{{ $summaryData['is_complete'] ? 'ri-check-double-line' : 'ri-time-line' }} me-1"></i>
                            {{ $summaryData['is_complete'] ? 'SELESAI ANALISA' : 'DALAM PROSES' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Metadata PO -->
            <div class="row g-2 mb-4">
                <div class="col-md-3 col-6">
                    <div class="meta-box">
                        <dt>Nomor PO</dt>
                        <dd>{{ $productionBatch->po_number }}</dd>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-box">
                        <dt>Tanggal Produksi</dt>
                        <dd>{{ $productionBatch->date ? \Carbon\Carbon::parse($productionBatch->date)->locale('id')->translatedFormat('d F Y') : '-' }}</dd>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-box">
                        <dt>Variant Produk</dt>
                        <dd>{{ $productionBatch->variant ?? '-' }}</dd>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="meta-box">
                        <dt>Batch Range</dt>
                        <dd>{{ $productionBatch->batch_range ?? '-' }}</dd>
                    </div>
                </div>
            </div>

            <!-- KPI Badges -->
            <div class="row g-2 mb-4 d-print-none">
                <div class="col">
                    <div class="card border border-primary-subtle shadow-none mb-0">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-muted fs-11 text-uppercase mb-1">Total Sampel</h6>
                            <h4 class="mb-0 text-primary">{{ $summaryData['total_samples'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-info-subtle shadow-none mb-0">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-muted fs-11 text-uppercase mb-1">Sampel Awal</h6>
                            <h4 class="mb-0 text-info">{{ $summaryData['count_awal'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning-subtle shadow-none mb-0">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-muted fs-11 text-uppercase mb-1">Sampel Tengah</h6>
                            <h4 class="mb-0 text-warning">{{ $summaryData['count_tengah'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-success-subtle shadow-none mb-0">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-muted fs-11 text-uppercase mb-1">Sampel Akhir</h6>
                            <h4 class="mb-0 text-success">{{ $summaryData['count_akhir'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-secondary-subtle shadow-none mb-0">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-muted fs-11 text-uppercase mb-1">Status Analisa</h6>
                            <h5 class="mb-0 fs-13">
                                <span class="badge bg-success">{{ $summaryData['count_ok'] }} OK</span>
                                @if($summaryData['count_not_ok'] > 0)
                                    <span class="badge bg-danger">{{ $summaryData['count_not_ok'] }} NOT OK</span>
                                @endif
                                @if($summaryData['count_pending'] > 0)
                                    <span class="badge bg-secondary">{{ $summaryData['count_pending'] }} Pending</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perbandingan Parameter per Blending (Awal, Tengah, Akhir) -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="ri-git-commit-line text-primary me-1"></i> Perbandingan Parameter Analisa (Awal - Tengah - Akhir)
                </h5>

                @forelse ($blendings as $blending)
                    <div class="card border border-secondary-subtle shadow-none mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="blending-card-badge">
                                    <i class="ri-flask-line"></i> Blending #{{ $blending['nomor_blending'] }}
                                </span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    Batch {{ $blending['batch_range'] }}
                                </span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    Tangki: {{ $blending['storage'] }}
                                </span>
                                @if($blending['volume'] && $blending['volume'] != '-')
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Volume: {{ number_format((float)$blending['volume'], 0, ',', '.') }} L
                                    </span>
                                @endif
                            </div>
                            <span class="fs-12 text-muted">
                                Total: {{ $blending['total_samples'] }} Sampel
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table-comparison">
                                    <thead>
                                        <tr>
                                            <th class="th-param">Parameter Uji</th>
                                            <th class="th-sample bg-info-subtle text-dark">
                                                <i class="ri-play-circle-line me-1 text-info"></i> Sampel Awal
                                            </th>
                                            <th class="th-sample bg-warning-subtle text-dark">
                                                <i class="ri-record-circle-line me-1 text-warning"></i> Sampel Tengah
                                            </th>
                                            <th class="th-sample bg-success-subtle text-dark">
                                                <i class="ri-checkbox-circle-line me-1 text-success"></i> Sampel Akhir
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Header Section: Parameter Kimia -->
                                        <tr class="section-title-row">
                                            <td colspan="4"><i class="ri-test-tube-line me-1"></i> I. Parameter Kimia / Analisa</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Brix (%)</td>
                                            <td class="sample-val fw-bold">{{ $blending['awal']?->brix !== null ? number_format((float)$blending['awal']->brix, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val fw-bold">{{ $blending['tengah']?->brix !== null ? number_format((float)$blending['tengah']->brix, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val fw-bold">{{ $blending['akhir']?->brix !== null ? number_format((float)$blending['akhir']->brix, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Viskositas (cps)</td>
                                            <td class="sample-val">{{ $blending['awal']?->visco !== null ? number_format((float)$blending['awal']->visco, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->visco !== null ? number_format((float)$blending['tengah']->visco, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->visco !== null ? number_format((float)$blending['akhir']->visco, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">NaCl (%)</td>
                                            <td class="sample-val">{{ $blending['awal']?->nacl !== null ? number_format((float)$blending['awal']->nacl, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->nacl !== null ? number_format((float)$blending['tengah']->nacl, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->nacl !== null ? number_format((float)$blending['akhir']->nacl, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Berat Jenis (BJ)</td>
                                            <td class="sample-val">{{ $blending['awal']?->bj !== null ? number_format((float)$blending['awal']->bj, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->bj !== null ? number_format((float)$blending['tengah']->bj, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->bj !== null ? number_format((float)$blending['akhir']->bj, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">pH</td>
                                            <td class="sample-val">{{ $blending['awal']?->ph !== null ? number_format((float)$blending['awal']->ph, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->ph !== null ? number_format((float)$blending['tengah']->ph, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->ph !== null ? number_format((float)$blending['akhir']->ph, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Aw</td>
                                            <td class="sample-val">{{ $blending['awal']?->aw !== null ? number_format((float)$blending['awal']->aw, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->aw !== null ? number_format((float)$blending['tengah']->aw, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->aw !== null ? number_format((float)$blending['akhir']->aw, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">%TN</td>
                                            <td class="sample-val">{{ $blending['awal']?->tn !== null ? number_format((float)$blending['awal']->tn, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->tn !== null ? number_format((float)$blending['tengah']->tn, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->tn !== null ? number_format((float)$blending['akhir']->tn, 2, ',', '.') : '-' }}</td>
                                        </tr>

                                        <!-- Header Section: Parameter Fisik -->
                                        <tr class="section-title-row">
                                            <td colspan="4"><i class="ri-eye-line me-1"></i> II. Parameter Fisik & Organoleptik</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Organoleptik</td>
                                            <td class="sample-val">{{ $blending['awal']?->organo ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->organo ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->organo ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Aroma</td>
                                            <td class="sample-val">{{ $blending['awal']?->aroma ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->aroma ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->aroma ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Buih</td>
                                            <td class="sample-val">{{ $blending['awal']?->buih !== null ? number_format((float)$blending['awal']->buih, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->buih !== null ? number_format((float)$blending['tengah']->buih, 2, ',', '.') : '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->buih !== null ? number_format((float)$blending['akhir']->buih, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Endapan</td>
                                            <td class="sample-val">{{ $blending['awal']?->endapan ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->endapan ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->endapan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Kristal</td>
                                            <td class="sample-val">{{ $blending['awal']?->kristal ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['tengah']?->kristal ?? '-' }}</td>
                                            <td class="sample-val">{{ $blending['akhir']?->kristal ?? '-' }}</td>
                                        </tr>

                                        <!-- Header Section: Status & Disposisi -->
                                        <tr class="section-title-row">
                                            <td colspan="4"><i class="ri-shield-check-line me-1"></i> III. Status, Disposisi & Keterangan</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Status Analisa</td>
                                            <td class="sample-val">
                                                @if($blending['awal']?->status)
                                                    <span class="badge {{ $blending['awal']->status == 'OK' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $blending['awal']->status }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="sample-val">
                                                @if($blending['tengah']?->status)
                                                    <span class="badge {{ $blending['tengah']->status == 'OK' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $blending['tengah']->status }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="sample-val">
                                                @if($blending['akhir']?->status)
                                                    <span class="badge {{ $blending['akhir']->status == 'OK' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $blending['akhir']->status }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Disposisi QC</td>
                                            <td class="sample-val fw-bold {{ in_array($blending['awal']?->disposition, ['Release', 'Release Bersyarat']) ? 'text-success' : 'text-danger' }}">
                                                {{ $blending['awal']?->disposition ?? '-' }}
                                            </td>
                                            <td class="sample-val fw-bold {{ in_array($blending['tengah']?->disposition, ['Release', 'Release Bersyarat']) ? 'text-success' : 'text-danger' }}">
                                                {{ $blending['tengah']?->disposition ?? '-' }}
                                            </td>
                                            <td class="sample-val fw-bold {{ in_array($blending['akhir']?->disposition, ['Release', 'Release Bersyarat']) ? 'text-success' : 'text-danger' }}">
                                                {{ $blending['akhir']?->disposition ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Keterangan / Remark</td>
                                            <td class="sample-val text-start px-2 small">{{ $blending['awal']?->disposition_remark ?? '-' }}</td>
                                            <td class="sample-val text-start px-2 small">{{ $blending['tengah']?->disposition_remark ?? '-' }}</td>
                                            <td class="sample-val text-start px-2 small">{{ $blending['akhir']?->disposition_remark ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Analis (PIC)</td>
                                            <td class="sample-val small">{{ $blending['awal']?->user?->name ?? '-' }}</td>
                                            <td class="sample-val small">{{ $blending['tengah']?->user?->name ?? '-' }}</td>
                                            <td class="sample-val small">{{ $blending['akhir']?->user?->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="param-name">Waktu Analisa</td>
                                            <td class="sample-val small text-muted">{{ $blending['awal']?->updated_at ? \Carbon\Carbon::parse($blending['awal']->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                                            <td class="sample-val small text-muted">{{ $blending['tengah']?->updated_at ? \Carbon\Carbon::parse($blending['tengah']->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                                            <td class="sample-val small text-muted">{{ $blending['akhir']?->updated_at ? \Carbon\Carbon::parse($blending['akhir']->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if($blending['other_samples']->isNotEmpty())
                                <div class="p-3 bg-light border-top">
                                    <h6 class="fw-bold fs-12 mb-2 text-muted">Sampel Tambahan Pada Blending Ini (Outlet / Inlet / After Drain):</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered bg-white mb-0 fs-12">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Jenis Sampel</th>
                                                    <th>Brix</th>
                                                    <th>Visco</th>
                                                    <th>NaCl</th>
                                                    <th>pH</th>
                                                    <th>Aw</th>
                                                    <th>Status</th>
                                                    <th>Disposisi</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($blending['other_samples'] as $other)
                                                    <tr>
                                                        <td><span class="badge bg-secondary">{{ $other->jenis_sampel }}</span></td>
                                                        <td>{{ $other->brix !== null ? number_format((float)$other->brix, 2, ',', '.') : '-' }}</td>
                                                        <td>{{ $other->visco !== null ? number_format((float)$other->visco, 2, ',', '.') : '-' }}</td>
                                                        <td>{{ $other->nacl !== null ? number_format((float)$other->nacl, 2, ',', '.') : '-' }}</td>
                                                        <td>{{ $other->ph !== null ? number_format((float)$other->ph, 2, ',', '.') : '-' }}</td>
                                                        <td>{{ $other->aw !== null ? number_format((float)$other->aw, 2, ',', '.') : '-' }}</td>
                                                        <td>
                                                            @if($other->status)
                                                                <span class="badge {{ $other->status == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $other->status }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $other->disposition ?? '-' }}</td>
                                                        <td>{{ $other->disposition_remark ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">Belum ada data monitoring storage kimia untuk Production Batch ini.</div>
                @endforelse
            </div>

            <!-- Consolidated Table of All Samples -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="ri-table-line text-primary me-1"></i> Data Seluruh Sampel Monitoring Storage Kimia
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0 fs-12">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Batch Range</th>
                                <th>No Blending</th>
                                <th>Jenis Sampel</th>
                                <th>Storage</th>
                                <th>Brix</th>
                                <th>Visco</th>
                                <th>NaCl</th>
                                <th>BJ</th>
                                <th>pH</th>
                                <th>Aw</th>
                                <th>%TN</th>
                                <th>Status</th>
                                <th>Disposisi</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allSamples as $idx => $sample)
                                <tr class="text-center">
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $sample->batch_range }}</td>
                                    <td>{{ $sample->nomor_blending }}</td>
                                    <td>
                                        <span class="badge {{ match($sample->jenis_sampel) {
                                            'Awal' => 'bg-info',
                                            'Tengah' => 'bg-warning text-dark',
                                            'Akhir' => 'bg-success',
                                            default => 'bg-secondary'
                                        } }}">
                                            {{ $sample->jenis_sampel ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $sample->storage ?? '-' }}</td>
                                    <td>{{ $sample->brix !== null ? number_format((float)$sample->brix, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->visco !== null ? number_format((float)$sample->visco, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->nacl !== null ? number_format((float)$sample->nacl, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->bj !== null ? number_format((float)$sample->bj, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->ph !== null ? number_format((float)$sample->ph, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->aw !== null ? number_format((float)$sample->aw, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $sample->tn !== null ? number_format((float)$sample->tn, 2, ',', '.') : '-' }}</td>
                                    <td>
                                        @if($sample->status)
                                            <span class="badge {{ $sample->status == 'OK' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $sample->status }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ in_array($sample->disposition, ['Release', 'Release Bersyarat']) ? 'text-success' : ($sample->disposition ? 'text-danger' : 'text-muted') }}">
                                        {{ $sample->disposition ?? '-' }}
                                    </td>
                                    <td class="text-start">{{ $sample->disposition_remark ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center text-muted">Tidak ada data sampel tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Signature Verification Section -->
            <div class="mt-4 pt-3 border-top">
                <table class="sign-table">
                    <thead>
                        <tr class="bg-light">
                            <th>Dibuat Oleh (QC Analis)</th>
                            <th>Diperiksa Oleh (QC Foreman)</th>
                            <th>Disetujui Oleh (QC Supervisor / Dept Head)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="sign-space"></div>
                                <div class="fw-bold">( ............................................ )</div>
                                <div class="text-muted fs-11">QC Analis Kimia</div>
                            </td>
                            <td>
                                <div class="sign-space"></div>
                                <div class="fw-bold">( ............................................ )</div>
                                <div class="text-muted fs-11">Foreman QC</div>
                            </td>
                            <td>
                                <div class="sign-space"></div>
                                <div class="fw-bold">( ............................................ )</div>
                                <div class="text-muted fs-11">SPV / Dept Head QC</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
