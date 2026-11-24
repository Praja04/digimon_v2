@extends('layouts.component.main')
@section('title', 'Dokumen Hasil Analisis Raw Material')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #334155;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 24px;
        position: relative;
        margin-bottom: 35px;
    }

    .doc-title-main {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #0f172a;
        text-transform: uppercase;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Metadata Header Grid */
    .doc-meta-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .doc-meta-table td {
        padding: 4px 8px;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .doc-meta-table .meta-label {
        font-weight: 600;
        color: #334155;
        width: 130px;
    }

    .doc-meta-table .meta-val {
        font-weight: 500;
        color: #0f172a;
    }

    /* Official Metadata Grid Persis Format Dokumen Excel */
    .doc-meta-grid {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
        margin-bottom: 18px;
    }

    .doc-meta-grid th, .doc-meta-grid td {
        border: 1px solid #000000;
        padding: 4px 8px;
        vertical-align: middle;
    }

    .doc-meta-grid th {
        background-color: #f1f5f9;
        font-weight: 600;
        color: #1e293b;
        width: 18%;
    }

    .doc-meta-grid td {
        color: #0f172a;
        font-weight: 500;
        width: 32%;
    }

    /* Tables Side-by-Side */
    .table-kalkulasi {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
        background: #ffffff;
        margin-bottom: 0;
    }

    .table-kalkulasi th, .table-kalkulasi td {
        border: 1px solid #000000;
        padding: 5px 8px;
        vertical-align: middle;
    }

    .table-kalkulasi thead th {
        background-color: #d9e1f2;
        color: #000000;
        font-weight: 700;
        text-align: center;
    }

    .table-kalkulasi .section-header {
        background-color: #d9e1f2;
        font-weight: 700;
        color: #000000;
        text-align: left;
    }

    .table-kalkulasi .cell-center {
        text-align: center;
    }

    .table-kalkulasi .cell-right {
        text-align: right;
    }

    /* Table Official Form Excel */
    .table-doc-excel {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.80rem;
        background: #ffffff;
        margin-bottom: 10px;
    }

    .table-doc-excel th, .table-doc-excel td {
        border: 1px solid #000000;
        padding: 3px 6px;
        vertical-align: middle;
    }

    .table-doc-excel .th-header {
        background-color: #d9e1f2;
        color: #000000;
        font-weight: 700;
        text-align: center;
    }

    .table-doc-excel .th-sub {
        background-color: #e2e8f0;
        font-weight: 600;
        text-align: center;
    }

    .table-doc-excel .cell-center {
        text-align: center;
    }

    .table-doc-excel .cell-right {
        text-align: right;
    }

    /* Highlight Colors persis Excel */
    .bg-yellow-highlight {
        background-color: #ffff00 !important;
        font-weight: 700;
        color: #000000;
    }

    .bg-green-highlight {
        background-color: #c6efce !important;
        font-weight: 700;
        color: #000000;
    }

    .bps-plain-input {
        background-color: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 0.88rem;
        font-weight: 600;
        max-width: 220px;
        display: inline-block;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .bps-plain-input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
        outline: none;
    }

    .signature-block {
        margin-top: 25px;
        padding-top: 10px;
    }

    .doc-watermark-no {
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-align: right;
        margin-top: 15px;
    }

    /* Tab Custom Styling */
    .nav-tabs-custom .nav-link {
        font-weight: 600;
        color: #475569;
        border: 1px solid transparent;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        padding: 8px 18px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #1e40af;
        background-color: #ffffff;
        border-color: #cbd5e1 #cbd5e1 #ffffff;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #printableDocArea, #printableDocArea * {
            visibility: visible;
        }
        #printableDocArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .lembar-sheet-card {
            box-shadow: none;
            border: 1px solid #000;
            padding: 10px;
        }
        .bps-plain-input {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: transparent !important;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row no-print">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dokumen Hasil Analisis Raw Material</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.index') }}">RMPM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.rm', ['open' => 'data']) }}">Raw Material</a></li>
                            <li class="breadcrumb-item active">Dokumen Laporan Kalkulasi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Control Bar: Switch SPB & Export Actions --}}
        <div class="card mb-3 shadow-sm border-0 no-print">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5 col-md-6">
                        <label for="selectSpb" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-folder-open-line me-1 text-primary"></i> Pilih Nomor SPB Kedatangan:
                        </label>
                        <select id="selectSpb" class="form-select select2">
                            @foreach($identitasList as $item)
                                <option value="{{ $item->id }}" {{ $selectedId == $item->id ? 'selected' : '' }}>
                                    SPB: {{ $item->no_spb ?? '-' }} — {{ $item->jenis ?? 'RM' }} ({{ $item->supplier ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-7 col-md-6 text-md-end text-start d-flex flex-wrap gap-2 justify-content-md-end justify-content-start align-items-end">
                        @php
                            $from = request('from');
                            if ($from === 'show' && $selectedId) {
                                $backUrl = route('rmpm.show', $selectedId);
                            } else {
                                $backUrl = route('rmpm.rm', ['open' => 'data']);
                            }
                        @endphp
                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                        <button type="button" id="btnDownloadExcel" class="btn btn-success btn-sm">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm text-white" onclick="window.print()">
                            <i class="ri-printer-line me-1"></i> Cetak / Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Printable Document Sheet Area --}}
        @if($docData)
        @php
            $isGulaSpecial = ($docData['is_gula_kelapa'] || $docData['is_gula_tebu']);
        @endphp

        <div id="printableDocArea">

            {{-- Tab Switcher Khusus Gula Tebu & Gula Kelapa --}}
            @if($isGulaSpecial)
            <div class="no-print mb-2">
                <ul class="nav nav-tabs nav-tabs-custom" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-kalkulasi-btn" data-bs-toggle="tab" data-bs-target="#pane-kalkulasi" type="button" role="tab">
                            <i class="ri-calculator-line me-1"></i> Laporan Kalkulasi Statistik (Gula)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-form-btn" data-bs-toggle="tab" data-bs-target="#pane-form-dokumen" type="button" role="tab">
                            <i class="ri-file-list-3-line me-1"></i> Form Hasil Pengecekan & Analisis (Excel)
                        </button>
                    </li>
                </ul>
            </div>
            @endif

            <div class="tab-content" id="reportTabsContent">

                {{-- PANE 1: TABEL KALKULASI STATISTIK (Khusus Gula Tebu & Gula Kelapa) --}}
                @if($isGulaSpecial)
                <div class="tab-pane fade show active" id="pane-kalkulasi" role="tabpanel">
                    <div class="lembar-sheet-card">
                        
                        {{-- Title Center --}}
                        <div class="doc-title-main">
                            {{ $docData['is_gula_tebu'] ? 'GULA TEBU' : 'GULA KELAPA' }}
                        </div>

                        {{-- Header Metadata --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="doc-meta-table">
                                    <tr>
                                        <td class="meta-label">Tgl Incoming</td>
                                        <td class="meta-val">: {{ $docData['tgl_incoming'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-label">Vendor</td>
                                        <td class="meta-val">: {{ $docData['vendor'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-label">SPB</td>
                                        <td class="meta-val">: {{ $docData['spb'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-label">Standart BPS</td>
                                        <td class="meta-val">
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <span>:</span>
                                                <input 
                                                    type="text" 
                                                    id="inputStandartBps" 
                                                    name="standart_bps" 
                                                    class="bps-plain-input" 
                                                    value="{{ $docData['standart_bps'] ?? '' }}"
                                                    placeholder="Input Standart BPS..."
                                                    autocomplete="off"
                                                >
                                                <span id="bpsSaveStatus" class="badge bg-success-subtle text-success fs-12 no-print" style="display:none;">
                                                    <i class="ri-check-line"></i> Tersimpan
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="doc-meta-table ms-md-auto" style="max-width: 400px;">
                                    <tr>
                                        <td class="meta-label">Nopol</td>
                                        <td class="meta-val">: {{ $docData['nopol'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-label">Qty (kg)</td>
                                        <td class="meta-val">: {{ $docData['qty'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-label">Jumlah Sampel</td>
                                        <td class="meta-val">: <strong>{{ $docData['sample_count'] }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Two Tables Side-By-Side (Left & Right) Khusus Gula Tebu & Gula Kelapa --}}
                        <div class="row g-4">
                            
                            {{-- TABEL KIRI: pH, Brix, %Kotoran --}}
                            <div class="col-lg-6">
                                <table class="table-kalkulasi">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width: 50%;">Parameter</th>
                                            <th style="width: 25%;">Awal</th>
                                            <th style="width: 25%;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Section: pH --}}
                                        @php
                                            $phMinStd = $docData['ph_min_std'] ?? ($docData['is_gula_kelapa'] ? 5.30 : 5.50);
                                            $phMaxStd = $docData['ph_max_std'] ?? 6.50;
                                            $phStdLabel = $docData['ph_std_label'] ?? ($docData['is_gula_kelapa'] ? '5,30-6,50' : '5,50-6,50');
                                        @endphp
                                        <tr>
                                            <td colspan="4" class="section-header">pH (STD : {{ $phStdLabel }})</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Avg,</td>
                                            <td class="cell-center {{ ($docData['avg_ph'] !== null && ($docData['avg_ph'] < $phMinStd || $docData['avg_ph'] > $phMaxStd)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['avg_ph'] !== null ? number_format($docData['avg_ph'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Min,</td>
                                            <td class="cell-center {{ ($docData['min_ph'] !== null && $docData['min_ph'] < $phMinStd) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['min_ph'] !== null ? number_format($docData['min_ph'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Max,</td>
                                            <td class="cell-center {{ ($docData['max_ph'] !== null && $docData['max_ph'] > $phMaxStd) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['max_ph'] !== null ? number_format($docData['max_ph'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        
                                        @if($docData['is_gula_kelapa'])
                                        <tr>
                                            <td rowspan="3" class="align-middle fw-semibold text-center" style="width: 25%;">%Qty Sample</td>
                                            <td style="width: 25%;">pH Under {{ number_format($phMinStd, 2, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_under'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_under'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>pH Sesuai Standar ({{ number_format($phMinStd, 2, ',', '.') }} - {{ number_format($phMaxStd, 2, ',', '.') }}) (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_inspec'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_inspec'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>pH Over {{ number_format($phMaxStd, 2, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_over'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_over'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td rowspan="4" class="align-middle fw-semibold text-center" style="width: 25%;">%Qty Sample</td>
                                            <td style="width: 25%;">pH Under {{ number_format($phMinStd, 2, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_under'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_under'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>pH Sesuai Standar ({{ number_format($phMinStd, 2, ',', '.') }} - {{ number_format($phMaxStd, 2, ',', '.') }}) (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_inspec'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_inspec'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>pH Over {{ number_format($phMaxStd, 2, ',', '.') }}-{{ number_format($phMaxStd + 0.50, 2, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_over_65_70'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_over_65_70'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>pH Over {{ number_format($phMaxStd + 0.50, 2, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ph_over_70'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ph_over_70'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        @endif

                                        {{-- Section: Brix --}}
                                        <tr>
                                            <td colspan="4" class="section-header">Brix (STD : {{ $docData['brix_std_label'] ?? 'Min, 8,7' }})</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Avg,</td>
                                            <td class="cell-center {{ ($docData['avg_brix'] !== null && $docData['avg_brix'] < ($docData['brix_min_std'] ?? 8.7)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['avg_brix'] !== null ? number_format($docData['avg_brix'], 1, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Min,</td>
                                            <td class="cell-center {{ ($docData['min_brix'] !== null && $docData['min_brix'] < ($docData['brix_min_std'] ?? 8.7)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['min_brix'] !== null ? number_format($docData['min_brix'], 1, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Max,</td>
                                            <td class="cell-center">
                                                {{ $docData['max_brix'] !== null ? number_format($docData['max_brix'], 1, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2" class="align-middle fw-semibold text-center" style="width: 25%;">%Qty Sample</td>
                                            <td style="width: 25%;">Brix Sesuai Standar (&gt;= {{ number_format($docData['brix_min_std'] ?? 8.7, 1, ',', '.') }}) (#)</td>
                                            <td class="cell-center {{ ($docData['pct_brix_inspec'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ number_format($docData['pct_brix_inspec'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Brix Under {{ number_format($docData['brix_min_std'] ?? 8.7, 1, ',', '.') }} (#)</td>
                                            <td class="cell-center {{ ($docData['pct_brix_under'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_brix_under'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>

                                        {{-- Section: %Kotoran --}}
                                        <tr>
                                            <td colspan="4" class="section-header">%Kotoran (STD : {{ $docData['kot_std_label'] ?? 'Max, 10% w/w' }})</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Avg,</td>
                                            <td class="cell-center {{ ($docData['avg_kotoran'] !== null && $docData['avg_kotoran'] > ($docData['kot_max_std'] ?? 10.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['avg_kotoran'] !== null ? number_format($docData['avg_kotoran'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Min,</td>
                                            <td class="cell-center {{ ($docData['min_kotoran'] !== null && $docData['min_kotoran'] > ($docData['kot_max_std'] ?? 10.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['min_kotoran'] !== null ? number_format($docData['min_kotoran'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Max,</td>
                                            <td class="cell-center {{ ($docData['max_kotoran'] !== null && $docData['max_kotoran'] > ($docData['kot_max_std'] ?? 10.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['max_kotoran'] !== null ? number_format($docData['max_kotoran'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="3" class="align-middle fw-semibold text-center" style="width: 25%;">%Qty Sample</td>
                                            <td style="width: 25%;">Kotoran Sesuai Standar (&lt;= {{ number_format($docData['kot_max_std'] ?? 10, 0, ',', '.') }}%) (#)</td>
                                            <td class="cell-center {{ ($docData['pct_kot_inspec'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ number_format($docData['pct_kot_inspec'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>%Qty Sample Kotoran Over {{ number_format($docData['kot_max_std'] ?? 10, 0, ',', '.') }}-14%(#)</td>
                                            <td class="cell-center {{ ($docData['pct_kot_10_14'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_kot_10_14'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>%Qty Sample Kotoran Over 14%(#)</td>
                                            <td class="cell-center {{ ($docData['pct_kot_over_14'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_kot_over_14'] ?? 0, 0) }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- TABEL KANAN: Organo, %KA, Rasa Khas --}}
                            <div class="col-lg-6">
                                <table class="table-kalkulasi">
                                    <thead>
                                        <tr>
                                            <th style="width: 45%;">Parameter</th>
                                            <th style="width: 15%;">Awal</th>
                                            <th style="width: 40%;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Section: Organo --}}
                                        <tr>
                                            <td colspan="3" class="section-header">Organo</td>
                                        </tr>
                                        @foreach($docData['organo_items'] ?? [] as $org)
                                        <tr>
                                            <td>{{ $org['label'] }}</td>
                                            <td class="cell-center {{ ($org['key'] === 'ok' && $org['pct'] > 0) ? 'bg-green-highlight' : '' }}">
                                                {{ $org['pct'] }}%
                                            </td>
                                            <td class="text-muted" style="font-size: 0.80rem;">{{ $org['ket'] }}</td>
                                        </tr>
                                        @endforeach

                                        {{-- Section: %Kadar Air --}}
                                        <tr>
                                            <td colspan="3" class="section-header">%Kadar Air (STD : {{ $docData['ka_std_label'] ?? 'Max, 8%' }})</td>
                                        </tr>
                                        <tr>
                                            <td>Avg,</td>
                                            <td class="cell-center {{ ($docData['avg_ka'] !== null && $docData['avg_ka'] > ($docData['ka_max_std'] ?? 8.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['avg_ka'] !== null ? number_format($docData['avg_ka'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Min,</td>
                                            <td class="cell-center {{ ($docData['min_ka'] !== null && $docData['min_ka'] > ($docData['ka_max_std'] ?? 8.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['min_ka'] !== null ? number_format($docData['min_ka'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Max,</td>
                                            <td class="cell-center {{ ($docData['max_ka'] !== null && $docData['max_ka'] > ($docData['ka_max_std'] ?? 8.0)) ? 'bg-yellow-highlight' : '' }}">
                                                {{ $docData['max_ka'] !== null ? number_format($docData['max_ka'], 2, ',', '.') : '-' }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>%Qty Sample KA Sesuai Standar (&lt;= {{ number_format($docData['ka_max_std'] ?? 8, 0, ',', '.') }}%) (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ka_inspec'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ number_format($docData['pct_ka_inspec'] ?? 0, 2, ',', '.') }}%
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>%Qty Sample KA Over {{ number_format($docData['ka_max_std'] ?? 8, 0, ',', '.') }}% (#)</td>
                                            <td class="cell-center {{ ($docData['pct_ka_over'] ?? 0) > 0 ? 'bg-yellow-highlight' : '' }}">
                                                {{ number_format($docData['pct_ka_over'] ?? 0, 2, ',', '.') }}%
                                            </td>
                                            <td></td>
                                        </tr>

                                        {{-- Section: Rasa Khas --}}
                                        <tr>
                                            <td colspan="3" class="section-header">Rasa Khas {{ $docData['is_gula_tebu'] ? 'Gula Tebu' : 'Gula Kelapa' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Murni</td>
                                            <td class="cell-center {{ ($docData['murni_pct'] ?? 0) > 0 ? 'bg-green-highlight' : '' }}">
                                                {{ $docData['murni_pct'] ?? 0 }}%
                                            </td>
                                            <td style="font-size: 0.80rem;">{{ $docData['ket_murni'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Campuran</td>
                                            <td class="cell-center">
                                                {{ $docData['campuran_pct'] ?? 0 }}%
                                            </td>
                                            <td style="font-size: 0.80rem;">Rasa campuran dan pahit</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                @endif

                {{-- PANE 2: FORM HASIL PENGECEKAN & ANALISIS PERSIS EXCEL (Default untuk selain Gula Tebu & Kelapa) --}}
                <div class="tab-pane fade {{ !$isGulaSpecial ? 'show active' : '' }}" id="pane-form-dokumen" role="tabpanel">
                    <div class="lembar-sheet-card">

                        {{-- Document Title --}}
                        <div class="doc-title-main">
                            {{ $docData['title'] }}
                        </div>

                        {{-- Metadata Grid Persis Format Dokumen Excel --}}
                        <table class="doc-meta-grid">
                            <tr>
                                <th>Nama Bahan</th>
                                <td>: {{ $docData['nama_bahan'] }}</td>
                                <th>Jam Kedatangan</th>
                                <td>: {{ $docData['jam_kedatangan'] }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Kedatangan</th>
                                <td>: {{ $docData['tgl_incoming'] }}</td>
                                <th>No. SPB</th>
                                <td>: {{ $docData['spb'] }}</td>
                            </tr>
                            <tr>
                                <th>Supplier & Manufacturing</th>
                                <td>: {{ $docData['vendor'] }}</td>
                                <th>Lot / Batch</th>
                                <td>: {{ $docData['lot_batch'] }}</td>
                            </tr>
                            <tr>
                                <th>Negara / Kota Asal</th>
                                <td>: {{ $docData['asal_bahan'] }}</td>
                                <th>Jumlah Kedatangan</th>
                                <td>: {{ $docData['qty'] }}</td>
                            </tr>
                            <tr>
                                <th>No. Mobil</th>
                                <td>: {{ $docData['nopol'] }}</td>
                                <th>Exp. Date</th>
                                <td>: {{ $docData['exp_date'] }}</td>
                            </tr>
                        </table>

                        {{-- Body: Left (Pengecekan Kedatangan, Fisik, Dokumen, Serah Terima) & Right (Analisis) --}}
                        <div class="row g-3">
                            
                            {{-- KOLOM KIRI: Kedatangan, Fisik, Dokumen, Serah Terima --}}
                            <div class="col-lg-5">
                                
                                {{-- 1. Kondisi Mobil --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="th-header">Pengecekan Kedatangan</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="th-sub">Kondisi Mobil</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 70%;">Spesifikasi</th>
                                            <th style="width: 30%; text-align: center;">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Bersih</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['bersih'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kering</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['kering'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tidak ada benda asing</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['benda_asing'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tidak berbau</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['berbau'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tidak cacat/bolong</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['cacat'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Segel container</td>
                                            <td class="cell-center fw-bold">{{ $docData['kondisi_mobil']['segel'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="small text-muted">
                                                Keterangan: {{ $docData['kondisi_mobil']['keterangan'] ?? '-' }}<br>
                                                <span class="fst-italic">Note: √ = Ya &nbsp;&nbsp;&nbsp; × = Tidak</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- 2. Dokumen dan Identitas --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="th-header">Dokumen dan Identitas</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 70%;">Dokumen</th>
                                            <th style="width: 30%; text-align: center;">Ada / Tidak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>COA</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['coa'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Surat Jalan Vendor</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['surat_jalan'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Packing List</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['packing_list'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Identitas RM di kemasan</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['identitas_kemasan'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Logo Halal Kemasan</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['logo_halal'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Sesuai Matriks Bahan Baku</td>
                                            <td class="cell-center fw-bold">{{ $docData['dokumen']['matriks_bahan'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="small text-muted fst-italic">
                                                Note: √ : Ada &nbsp;&nbsp;&nbsp; x : Tidak ada
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- 3. Pengecekan Fisik Kemasan --}}
                                @if($docData['kemasan_type'] === 'cair')
                                    {{-- Khusus Gula Cair --}}
                                    <table class="table-doc-excel">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="th-header">Pengecekan Fisik Material</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 70%;">Spesifikasi</th>
                                                <th style="width: 30%; text-align: center;">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Bening, tidak berbau</td>
                                                <td class="cell-center fw-bold">√</td>
                                            </tr>
                                            <tr>
                                                <td>Tidak berbuih</td>
                                                <td class="cell-center fw-bold">√</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="small text-muted fst-italic">
                                                    Note: √ = Ya &nbsp;&nbsp;&nbsp; × = Tidak
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @elseif($docData['kemasan_type'] === 'drum')
                                    {{-- Khusus Drum (SKM, Karamel) --}}
                                    <table class="table-doc-excel">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="th-header">Pengecekan Fisik</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 50%;">Kriteria</th>
                                                <th style="width: 25%; text-align: center;">Qty Drum</th>
                                                <th style="width: 25%; text-align: center;">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Kotor</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['kotor'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Kembung</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['kembung'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Rusak</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['rusak'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr style="background-color: #f8fafc;">
                                                <th class="fw-bold">Total Bagus</th>
                                                <td colspan="2" class="cell-center fw-bold">{{ $docData['fisik_kemasan']['total_bagus'] ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    {{-- Khusus Zak (Garam, Minor Ingredient, Gula) --}}
                                    <table class="table-doc-excel">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="th-header">Pengecekan Fisik</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 50%;">Kriteria / Fisik Zak</th>
                                                <th style="width: 25%; text-align: center;">Qty Zak</th>
                                                <th style="width: 25%; text-align: center;">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($docData['template_type'] === 'gula')
                                            <tr>
                                                <td>Leleh</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['leleh'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>Kotor</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['kotor'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Berair / Basah</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['berair'] !== '-' ? $docData['fisik_kemasan']['berair'] : ($docData['fisik_kemasan']['basah'] ?? '-') }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Warna</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['warna'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Campuran</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['campuran'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Aroma</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['aroma'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Rusak / Robek</td>
                                                <td class="cell-center">{{ $docData['fisik_kemasan']['rusak'] ?? '-' }}</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr style="background-color: #f1f5f9;">
                                                <th colspan="3" class="fw-semibold">Reject: Zak Pupuk, Bahan Kimia, Pakan Ternak</th>
                                            </tr>
                                            <tr style="background-color: #f8fafc;">
                                                <th class="fw-bold">Total Bagus</th>
                                                <td colspan="2" class="cell-center fw-bold">{{ $docData['fisik_kemasan']['total_bagus'] ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif

                                {{-- 4. Serah Terima Sampel & Disposisi --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="th-header">Serah Terima Sampel</th>
                                        </tr>
                                        <tr>
                                            <th>Tahap</th>
                                            <th style="text-align: center;">Jam</th>
                                            <th style="text-align: center;">Ttd</th>
                                            <th style="text-align: center;">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($docData['template_type'] === 'gula_cair')
                                            <tr>
                                                <td>Sampling 1</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['jam_sampling'] ?? '-' }}</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['ttd_sampling'] ?? '-' }}</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['jumlah_sampling'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Sampling 2</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Sampling 3</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                            <tr>
                                                <td>Re-Sampling</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>Sampling</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['jam_sampling'] ?? '-' }}</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['ttd_sampling'] ?? '-' }}</td>
                                                <td class="cell-center">{{ $docData['serah_terima']['jumlah_sampling'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Re-Sampling</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                                <td class="cell-center">-</td>
                                            </tr>
                                        @endif
                                        <tr style="background-color: #f8fafc;">
                                            <th colspan="2">Disposisi</th>
                                            <td colspan="2" class="cell-center fw-bold">
                                                <span class="badge {{ str_contains(strtolower($docData['serah_terima']['disposisi']), 'terima') ? 'bg-success' : 'bg-primary' }} fs-11">
                                                    {{ $docData['serah_terima']['disposisi'] ?? 'Belum Disposisi' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if(!empty($docData['serah_terima']['group']) && $docData['serah_terima']['group'] !== '-')
                                        <tr>
                                            <th colspan="2">Group</th>
                                            <td colspan="2" class="cell-center fw-bold">
                                                <span class="badge bg-secondary fs-11">{{ $docData['serah_terima']['group'] }}</span>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>

                            </div>

                            {{-- KOLOM KANAN: Tabel Analisis Sampel --}}
                            <div class="col-lg-7">
                                <div class="table-responsive">
                                    <table class="table-doc-excel">
                                        <thead>
                                            <tr>
                                                <th colspan="{{ count($docData['template_meta']['param_labels']) + 1 }}" class="th-header">
                                                    Analisis
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="width: 8%; text-align: center;">No</th>
                                                @foreach($docData['template_meta']['param_labels'] as $label)
                                                    <th class="cell-center">{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rows = $docData['sample_rows'] ?? [];
                                                $totalRows = count($rows);
                                                $minDisplayRows = max(10, $totalRows);
                                            @endphp

                                            @for ($i = 0; $i < $minDisplayRows; $i++)
                                                @php
                                                    $s = $rows[$i] ?? null;
                                                @endphp
                                                <tr>
                                                    <td class="cell-center fw-semibold text-muted" style="height: 28px;">{{ $i + 1 }}</td>
                                                    @foreach($docData['template_meta']['param_keys'] as $key)
                                                        <td class="cell-center">
                                                            @if($s && isset($s[$key]) && $s[$key] !== '')
                                                                {{ $s[$key] }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endfor

                                            {{-- Baris Average --}}
                                            <tr style="background-color: #d9e1f2; font-weight: 700;">
                                                <td class="cell-center">Average</td>
                                                @foreach($docData['template_meta']['param_keys'] as $key)
                                                    <td class="cell-center">
                                                        {{ $docData['averages'][$key] ?? '-' }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Catatan / Note Khusus Template --}}
                                @if(!empty($docData['note']))
                                    <div class="mt-2 text-muted fst-italic small">
                                        Catatan: {{ $docData['note'] }}
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- Tanda Tangan Section --}}
                        <div class="signature-block">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="small text-muted mb-4">Disampling oleh,</div>
                                    <div class="fw-bold text-dark">({{ $docData['signatures']['qc_field'] ?: '....................................' }})</div>
                                    <div class="small text-muted">QC Field</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted mb-4">Dianalisis oleh,</div>
                                    <div class="fw-bold text-dark">({{ $docData['signatures']['qc_analis'] ?: '....................................' }})</div>
                                    <div class="small text-muted">QC Analis</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted mb-4">Dicek oleh,</div>
                                    <div class="fw-bold text-dark">({{ $docData['signatures']['spv'] ?: '....................................' }})</div>
                                    <div class="small text-muted">Staff / SPV / MNG QC</div>
                                </div>
                            </div>
                        </div>

                        {{-- Watermark Kode Dokumen di Kanan Bawah --}}
                        <div class="doc-watermark-no">
                            {{ $docData['doc_no'] }}
                        </div>

                    </div>
                </div>

            </div>

        </div>
        @else
        <div class="card border-0 shadow-sm p-5 text-center">
            <h5 class="text-muted">Silakan pilih data SPB Kedatangan untuk melihat dokumen.</h5>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selectSpb').select2({
            placeholder: '-- Pilih Nomor SPB Kedatangan --',
            width: '100%'
        });

        // Switch SPB
        $('#selectSpb').on('change', function() {
            const id = $(this).val();
            if (id) {
                const urlParams = new URLSearchParams(window.location.search);
                const from = urlParams.get('from');
                let targetUrl = "{{ route('doc-rm.index') }}?id=" + id;
                if (from) {
                    targetUrl += "&from=" + encodeURIComponent(from);
                }
                window.location.href = targetUrl;
            }
        });

        var bpsSaveTimeout = null;
        function autoSaveBps(val) {
            clearTimeout(bpsSaveTimeout);
            bpsSaveTimeout = setTimeout(function() {
                const id = $('#selectSpb').val();
                if (!id) return;
                $.ajax({
                    url: "{{ route('doc-rm.update-bps') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        standart_bps: val
                    },
                    success: function(res) {
                        $('#bpsSaveStatus').stop(true, true).fadeIn(150).delay(1500).fadeOut(300);
                    }
                });
            }, 400);
        }

        // Event saat input manual di lembar dokumen berubah
        $('#inputStandartBps').on('input change', function() {
            const val = $(this).val();
            autoSaveBps(val);
        });

        // Download Excel Button Action
        $('#btnDownloadExcel').on('click', function(e) {
            e.preventDefault();
            const id = $('#selectSpb').val();
            if (!id) {
                Swal.fire({ icon: 'warning', title: 'Pilih SPB', text: 'Silakan pilih SPB terlebih dahulu.' });
                return;
            }
            const standartBps = encodeURIComponent($('#inputStandartBps').val() || '');
            window.location.href = "{{ route('doc-rm.export') }}?id=" + id + "&standart_bps=" + standartBps;
        });
    });
</script>
@endsection
