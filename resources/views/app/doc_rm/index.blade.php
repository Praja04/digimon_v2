@extends('layouts.component.main')
@section('title', 'Dokumen Hasil Analisis Raw Material')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #334155;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 28px;
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
        margin-bottom: 24px;
    }

    /* Metadata Header Grid */
    .doc-meta-table {
        width: 100%;
        margin-bottom: 24px;
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
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.rm') }}">Raw Material</a></li>
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
                        <a href="{{ route('rmpm.rm') }}" class="btn btn-outline-secondary btn-sm">
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
        <div id="printableDocArea">
            <div class="lembar-sheet-card">
                
                {{-- Title Center --}}
                <div class="doc-title-main">
                    {{ $docData['title'] ?? $docData['jenis'] }}
                </div>

                @if(!empty($docData['is_supported']) && !$docData['is_supported'])
                <div class="alert alert-warning d-flex align-items-center mb-4 no-print" role="alert">
                    <i class="ri-alert-line fs-20 me-2"></i>
                    <div>
                        <strong>Perhatian:</strong> Dokumen kalkulasi ini dikhususkan untuk Raw Material <strong>GULA</strong> (Gula Tebu / Gula Kelapa). Data yang dipilih saat ini adalah <strong>{{ $docData['jenis'] }}</strong>.
                    </div>
                </div>
                @endif

                {{-- Header Metadata persis Gambar 2 & 3 --}}
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

                {{-- Two Tables Side-By-Side (Left & Right) --}}
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
                                    <td class="cell-center {{ $docData['pct_ph_under'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_under'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>pH Sesuai Standar ({{ number_format($phMinStd, 2, ',', '.') }} - {{ number_format($phMaxStd, 2, ',', '.') }}) (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_inspec'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_inspec'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>pH Over {{ number_format($phMaxStd, 2, ',', '.') }} (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_over'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_over'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                @else
                                <tr>
                                    <td rowspan="4" class="align-middle fw-semibold text-center" style="width: 25%;">%Qty Sample</td>
                                    <td style="width: 25%;">pH Under {{ number_format($phMinStd, 2, ',', '.') }} (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_under'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_under'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>pH Sesuai Standar ({{ number_format($phMinStd, 2, ',', '.') }} - {{ number_format($phMaxStd, 2, ',', '.') }}) (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_inspec'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_inspec'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>pH Over {{ number_format($phMaxStd, 2, ',', '.') }}-{{ number_format($phMaxStd + 0.50, 2, ',', '.') }} (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_over_65_70'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_over_65_70'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>pH Over {{ number_format($phMaxStd + 0.50, 2, ',', '.') }} (#)</td>
                                    <td class="cell-center {{ $docData['pct_ph_over_70'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ph_over_70'], 0) }}%
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
                                    <td class="cell-center {{ $docData['pct_brix_inspec'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ number_format($docData['pct_brix_inspec'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Brix Under {{ number_format($docData['brix_min_std'] ?? 8.7, 1, ',', '.') }} (#)</td>
                                    <td class="cell-center {{ $docData['pct_brix_under'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_brix_under'], 0) }}%
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
                                    <td class="cell-center {{ $docData['pct_kot_inspec'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ number_format($docData['pct_kot_inspec'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>%Qty Sample Kotoran Over {{ number_format($docData['kot_max_std'] ?? 10, 0, ',', '.') }}-14%(#)</td>
                                    <td class="cell-center {{ $docData['pct_kot_10_14'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_kot_10_14'], 0) }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>%Qty Sample Kotoran Over 14%(#)</td>
                                    <td class="cell-center {{ $docData['pct_kot_over_14'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_kot_over_14'], 0) }}%
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
                                @foreach($docData['organo_items'] as $org)
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
                                    <td class="cell-center {{ $docData['pct_ka_inspec'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ number_format($docData['pct_ka_inspec'], 2, ',', '.') }}%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>%Qty Sample KA Over {{ number_format($docData['ka_max_std'] ?? 8, 0, ',', '.') }}% (#)</td>
                                    <td class="cell-center {{ $docData['pct_ka_over'] > 0 ? 'bg-yellow-highlight' : '' }}">
                                        {{ number_format($docData['pct_ka_over'], 2, ',', '.') }}%
                                    </td>
                                    <td></td>
                                </tr>

                                {{-- Section: Rasa Khas --}}
                                <tr>
                                    <td colspan="3" class="section-header">Rasa Khas {{ $docData['display_jenis'] ?? ucwords(strtolower($docData['jenis'])) }}</td>
                                </tr>
                                <tr>
                                    <td>Murni</td>
                                    <td class="cell-center {{ $docData['murni_pct'] > 0 ? 'bg-green-highlight' : '' }}">
                                        {{ $docData['murni_pct'] }}%
                                    </td>
                                    <td style="font-size: 0.80rem;">{{ $docData['ket_murni'] }}</td>
                                </tr>
                                <tr>
                                    <td>Campuran</td>
                                    <td class="cell-center">
                                        {{ $docData['campuran_pct'] }}%
                                    </td>
                                    <td style="font-size: 0.80rem;">Rasa campuran dan pahit</td>
                                </tr>
                            </tbody>
                        </table>
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
                window.location.href = "{{ route('doc-rm.index') }}?id=" + id;
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
