@extends('layouts.component.main')
@section('title', 'Dokumen Hasil Pemeriksaan Packaging Material')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #1e293b;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        padding: 20px 24px;
        position: relative;
        margin-bottom: 35px;
        width: 100%;
        box-sizing: border-box;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
        color: #000000;
    }

    /* Top Logo & Title Header */
    .doc-header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
        border: 1.5px solid #000000;
    }

    .doc-header-table td {
        padding: 6px 12px;
        vertical-align: middle;
    }

    .doc-logo-box {
        width: 190px;
        border-right: 1.5px solid #000000;
        text-align: center;
        padding: 4px 8px;
    }

    .doc-logo-box img {
        max-height: 40px;
        display: inline-block;
    }

    .doc-title-cell {
        text-align: center;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #000000;
        text-transform: uppercase;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
    }

    /* Metadata Header Grid */
    .doc-meta-table {
        width: 100%;
        margin-bottom: 14px;
        border-collapse: collapse;
        font-size: 0.85rem;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
    }

    .doc-meta-table td {
        padding: 3px 6px;
        vertical-align: middle;
    }

    .doc-meta-table .meta-label {
        font-weight: 700;
        width: 180px;
        color: #000000;
    }

    .doc-meta-table .meta-val {
        color: #000000;
    }

    /* Main Flex Grid: Left Block vs Right Block */
    .doc-main-grid {
        display: flex;
        gap: 12px;
        align-items: stretch;
        width: 100%;
    }

    .doc-left-block {
        width: 44%;
        flex-shrink: 0;
        display: flex;
        gap: 8px;
    }

    .doc-sub-col {
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .doc-right-block {
        width: 56%;
        flex-grow: 1;
        overflow-x: auto;
    }

    /* Table Styles matching exact Excel */
    .table-doc-excel {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.72rem;
        background: #ffffff;
        margin-bottom: 8px;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
        color: #000000;
    }

    .table-doc-excel th, .table-doc-excel td {
        border: 1px solid #000000;
        padding: 2px 4px;
        vertical-align: middle;
        line-height: 1.15;
    }

    .table-doc-excel .th-header {
        background-color: #d9e1f2;
        color: #000000;
        font-weight: 700;
        text-align: center;
    }

    .table-doc-excel .th-sub {
        background-color: #e2efda;
        font-weight: 700;
        text-align: center;
        color: #000000;
    }

    .table-doc-excel .th-col {
        background-color: #f2f2f2;
        font-weight: 700;
        text-align: center;
        color: #000000;
    }

    .table-doc-excel .cell-center {
        text-align: center;
    }

    .table-doc-excel .cell-right {
        text-align: right;
    }

    .table-doc-excel .row-avg {
        background-color: #f2f2f2;
        font-weight: 700;
    }

    .keterangan-box {
        font-size: 0.72rem;
        line-height: 1.3;
        margin-top: 4px;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
        color: #000000;
    }

    /* Signatures Table */
    .table-doc-sign {
        width: 100%;
        border-collapse: collapse;
        margin-top: 14px;
        font-size: 0.78rem;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
    }

    .table-doc-sign td {
        padding: 4px 10px;
        vertical-align: top;
    }

    .sign-space {
        height: 50px;
    }

    .doc-form-code {
        font-size: 0.75rem;
        font-style: italic;
        color: #333333;
        font-weight: 600;
        text-align: right;
        margin-top: 6px;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
    }

    @media print {
        @page {
            size: landscape;
            margin: 6mm;
        }
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
        .lembar-sheet-card {
            box-shadow: none;
            border: 1.5px solid #000;
            padding: 6px 10px;
        }
        .no-print {
            display: none !important;
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
                    <h4 class="mb-sm-0">Dokumen Hasil Pemeriksaan Packaging Material</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.index') }}">RMPM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.pm') }}">Packaging Material</a></li>
                            <li class="breadcrumb-item active">Dokumen Laporan Form</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Control Bar --}}
        <div class="card mb-3 shadow-sm border-0 no-print">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-lg-3 col-md-4">
                        <label for="filterJenisSelect" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-filter-line me-1 text-primary"></i> Filter Jenis Kemasan:
                        </label>
                        <select id="filterJenisSelect" class="form-select form-select-sm" onchange="applyFilters()">
                            <option value="" {{ empty($filterJenis) ? 'selected' : '' }}>-- Semua Kemasan --</option>
                            <option value="karton" {{ $filterJenis === 'karton' ? 'selected' : '' }}>Kardus / Karton</option>
                            <option value="pouch" {{ $filterJenis === 'pouch' ? 'selected' : '' }}>Pouch</option>
                            <option value="inner" {{ $filterJenis === 'inner' ? 'selected' : '' }}>Inner Sachet / Outer</option>
                        </select>
                    </div>

                    <div class="col-lg-5 col-md-5">
                        <label for="selectSpb" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-folder-open-line me-1 text-primary"></i> Pilih Nomor SPB Kedatangan:
                        </label>
                        <select id="selectSpb" class="form-select form-select-sm" onchange="applyFilters()">
                            @forelse($incomingList as $item)
                                @php
                                    $isItemFinal = $item->packagingKartonSampling || $item->packagingPouchSampling || $item->packagingInnerOuterSampling;
                                    $isItemDraft = $item->packagingKartonSamplingDraft || $item->packagingPouchSamplingDraft || $item->packagingInnerOuterSamplingDraft;
                                    $tag = $isItemFinal ? '[FINAL]' : ($isItemDraft ? '[DRAFT]' : '[INCOMING]');
                                @endphp
                                <option value="{{ $item->id }}" {{ $selectedId == $item->id ? 'selected' : '' }}>
                                    {{ $tag }} SPB: {{ $item->no_spb ?? '-' }} — {{ $item->jenisIncoming?->nama ?? 'PM' }} ({{ $item->supplier?->nama ?? '-' }})
                                </option>
                            @empty
                                <option value="">Tidak ada data kedatangan PM</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-3 text-md-end text-start d-flex flex-wrap gap-2 justify-content-md-end justify-content-start align-items-end">
                        @if($selectedId)
                            <a href="{{ route('doc-pm.export', ['id' => $selectedId]) }}" class="btn btn-success btn-sm">
                                <i class="ri-file-excel-2-line me-1"></i> Download Excel
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                                <i class="ri-printer-line me-1"></i> Print Form
                            </button>
                        @endif
                        @php
                            $previousUrl = url()->previous();
                            $backBtnUrl = route('rmpm.pm');
                            if ($previousUrl && (str_contains($previousUrl, '/pouch') || str_contains($previousUrl, '/inner') || str_contains($previousUrl, '/karton') || str_contains($previousUrl, '/incoming'))) {
                                $backBtnUrl = $previousUrl;
                            } elseif (isset($docData['category'])) {
                                if ($docData['category'] === 'pouch') $backBtnUrl = route('rmpm.pm.pouch');
                                elseif ($docData['category'] === 'karton') $backBtnUrl = route('rmpm.pm.karton');
                                elseif ($docData['category'] === 'inner') $backBtnUrl = route('rmpm.pm.inner-outer');
                            }
                        @endphp
                        <a href="{{ $backBtnUrl }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(!$docData)
            <div class="alert alert-warning text-center py-4">
                <i class="ri-information-line fs-20 d-block mb-2"></i>
                Belum ada data kedatangan Packaging Material yang dipilih atau belum ada pemeriksaan.
            </div>
        @else
            @php
                $inc = $docData['incoming'];
                $cat = $docData['category'];
                $formNo = $docData['formNo'];
                $title = $docData['title'];
                $sampling = $docData['sampling'];
                $bct = $docData['bct'];

                $tglDatang = $inc->tanggal_kedatangan ? \Carbon\Carbon::parse($inc->tanggal_kedatangan)->format('d/m/Y') : '-';
                $expDate = $inc->exp_date ? \Carbon\Carbon::parse($inc->exp_date)->format('d/m/Y') : ($sampling?->exp_date ? \Carbon\Carbon::parse($sampling->exp_date)->format('d/m/Y') : '-');
                $jamDatang = $inc->jam_kedatangan ? substr((string)$inc->jam_kedatangan, 0, 5) : '-';
                $namaBahan = $inc->jenisMaterial?->nama ?? $inc->jenisIncoming?->nama ?? '-';
                $supplier = $inc->supplier?->nama ?? '-';
                $noSpb = $inc->no_spb ?? '-';
                $lotBatch = $inc->no_batch ?? $sampling?->no_batch ?? '-';
                $noMobil = $inc->no_mobil ?? '-';
                $jumlahDatang = ($inc->jumlah !== null ? number_format((float)$inc->jumlah, 0, ',', '.') : '-') . ' ' . ($inc->uom?->nama ?? 'Pcs');
                $samples = is_array($sampling?->hasil_sampel) ? array_values($sampling->hasil_sampel) : [];

                // Pouch calculations
                $pouchTebalSum = 0; $pouchTebalCount = 0;
                $pouchBeratSum = 0; $pouchBeratCount = 0;
                $pouchSideSealSum = 0; $pouchSideSealCount = 0;
                $pouchBottomSealSum = 0; $pouchBottomSealCount = 0;
                $pouchBottomHighSum = 0; $pouchBottomHighCount = 0;

                foreach ($samples as $s) {
                    $tb = $s['thickness_1'] ?? $s['thickness'] ?? null;
                    if ($tb !== null && is_numeric($tb)) { $pouchTebalSum += (float)$tb; $pouchTebalCount++; }
                    if (isset($s['berat']) && is_numeric($s['berat'])) { $pouchBeratSum += (float)$s['berat']; $pouchBeratCount++; }
                    $ss = $s['side_seal_1'] ?? $s['side_seal'] ?? null;
                    if ($ss !== null && is_numeric($ss)) { $pouchSideSealSum += (float)$ss; $pouchSideSealCount++; }
                    if (isset($s['bottom_seal']) && is_numeric($s['bottom_seal'])) { $pouchBottomSealSum += (float)$s['bottom_seal']; $pouchBottomSealCount++; }
                    if (isset($s['bottom_high']) && is_numeric($s['bottom_high'])) { $pouchBottomHighSum += (float)$s['bottom_high']; $pouchBottomHighCount++; }
                }

                $avgTebal = $pouchTebalCount > 0 ? number_format($pouchTebalSum / $pouchTebalCount, 2) : '-';
                $avgBerat = $pouchBeratCount > 0 ? number_format($pouchBeratSum / $pouchBeratCount, 2) : '-';
                $avgSideSeal = $pouchSideSealCount > 0 ? number_format($pouchSideSealSum / $pouchSideSealCount, 2) : '-';
                $avgBottomSeal = $pouchBottomSealCount > 0 ? number_format($pouchBottomSealSum / $pouchBottomSealCount, 2) : '-';
                $avgBottomHigh = $pouchBottomHighCount > 0 ? number_format($pouchBottomHighSum / $pouchBottomHighCount, 2) : '-';

                // Thickness bottom for Pouch
                $thick = is_array($sampling?->hasil_thickness) ? $sampling->hasil_thickness : [];
                $t1 = isset($thick[0]) ? trim(($thick[0]['nilai_1'] ?? '') . ' / ' . ($thick[0]['nilai_2'] ?? ''), ' /') : '';
                $t2 = isset($thick[1]) ? trim(($thick[1]['nilai_1'] ?? '') . ' / ' . ($thick[1]['nilai_2'] ?? ''), ' /') : '';
                $t3 = isset($thick[2]) ? trim(($thick[2]['nilai_1'] ?? '') . ' / ' . ($thick[2]['nilai_2'] ?? ''), ' /') : '';

                // Karton calculations
                $kartonBeratSum = 0; $kartonBeratCount = 0;
                $kartonGramasiSum = 0; $kartonGramasiCount = 0;
                foreach ($samples as $s) {
                    $b = $s['berat'] ?? $s['hasil_berat'] ?? null;
                    $g = $s['gramasi'] ?? null;
                    if ($b !== null && is_numeric($b)) { $kartonBeratSum += (float)$b; $kartonBeratCount++; }
                    if ($g !== null && is_numeric($g)) { $kartonGramasiSum += (float)$g; $kartonGramasiCount++; }
                }
                $avgKartonBerat = $kartonBeratCount > 0 ? number_format($kartonBeratSum / $kartonBeratCount, 2) : '-';
                $avgKartonGramasi = $kartonGramasiCount > 0 ? number_format($kartonGramasiSum / $kartonGramasiCount, 2) : '-';
                $avgKartonBct = ($bct && $bct->average) ? number_format((float)$bct->average, 2) : '-';

                // Inner Sachet calculations
                $innerBeratGrossSum = 0; $innerBeratGrossCount = 0;
                $innerInsideCoreSum = 0; $innerInsideCoreCount = 0;
                $innerTebalSum = 0; $innerTebalCount = 0;
                $innerBondingSum = 0; $innerBondingCount = 0;
                $innerSealSum = 0; $innerSealCount = 0;
                foreach ($samples as $s) {
                    if (isset($s['berat_gross']) && is_numeric($s['berat_gross'])) { $innerBeratGrossSum += (float)$s['berat_gross']; $innerBeratGrossCount++; }
                    if (isset($s['inside_core']) && is_numeric($s['inside_core'])) { $innerInsideCoreSum += (float)$s['inside_core']; $innerInsideCoreCount++; }
                    if (isset($s['tebal']) && is_numeric($s['tebal'])) { $innerTebalSum += (float)$s['tebal']; $innerTebalCount++; }
                    if (isset($s['bonding_strength']) && is_numeric($s['bonding_strength'])) { $innerBondingSum += (float)$s['bonding_strength']; $innerBondingCount++; }
                    if (isset($s['seal_strength']) && is_numeric($s['seal_strength'])) { $innerSealSum += (float)$s['seal_strength']; $innerSealCount++; }
                }
                $avgInnerBeratGross = $innerBeratGrossCount > 0 ? number_format($innerBeratGrossSum / $innerBeratGrossCount, 2) : '-';
                $avgInnerInsideCore = $innerInsideCoreCount > 0 ? number_format($innerInsideCoreSum / $innerInsideCoreCount, 2) : '-';
                $avgInnerTebal = $innerTebalCount > 0 ? number_format($innerTebalSum / $innerTebalCount, 2) : '-';
                $avgInnerBonding = $innerBondingCount > 0 ? number_format($innerBondingSum / $innerBondingCount, 2) : '-';
                $avgInnerSeal = $innerSealCount > 0 ? number_format($innerSealSum / $innerSealCount, 2) : '-';

                // Maximum rows in official Excel sheet: 30 rows
                $maxSampleRows = 30;
            @endphp

            <div id="printableDocArea">
                <div class="lembar-sheet-card">

                    {{-- Top Header Box: BAS Logo & Title --}}
                    <table class="doc-header-table">
                        <tr>
                            <td class="doc-logo-box">
                                <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS">
                            </td>
                            <td class="doc-title-cell">
                                {{ $title }}
                            </td>
                        </tr>
                    </table>

                    {{-- Metadata Header Grid --}}
                    <table class="doc-meta-table">
                        <tr>
                            <td class="meta-label">Nama Bahan</td>
                            <td class="meta-val">: {{ $namaBahan }}</td>
                            <td class="meta-label">No. SPB</td>
                            <td class="meta-val">: {{ $noSpb }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tanggal Kedatangan</td>
                            <td class="meta-val">: {{ $tglDatang }} &nbsp;&nbsp;&nbsp;&nbsp; <strong>Jam Kedatangan :</strong> {{ $jamDatang }}</td>
                            <td class="meta-label">Lot/Batch</td>
                            <td class="meta-val">: {{ $lotBatch }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Supplier & Manufacturing</td>
                            <td class="meta-val">: {{ $supplier }}</td>
                            <td class="meta-label">Jumlah Kedatangan</td>
                            <td class="meta-val">: {{ $jumlahDatang }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">No. Mobil</td>
                            <td class="meta-val">: {{ $noMobil }}</td>
                            <td class="meta-label">Exp Date</td>
                            <td class="meta-val">: {{ $expDate }}</td>
                        </tr>
                    </table>

                    {{-- Main 2-Block Layout: Left (Kondisi Mobil, Fisik, Dokumen, Serah Terima) vs Right (Analisis Table) --}}
                    <div class="doc-main-grid">

                        {{-- ==================== LEFT SIDE (44%): 2 SUB-COLUMNS ==================== --}}
                        <div class="doc-left-block">

                            {{-- Sub-Column 1: Kondisi Mobil, Dokumen & Identitas, Keterangan --}}
                            <div class="doc-sub-col">
                                {{-- 1. Kondisi Mobil --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="th-header">Pengecekan Kedatangan</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="th-sub">Kondisi Mobil</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th style="width: 70%;">Spesifikasi</th>
                                            <th class="cell-center" style="width: 30%;">Kondisi*</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Bersih</td><td class="cell-center">√</td></tr>
                                        <tr><td>Kering</td><td class="cell-center">√</td></tr>
                                        <tr><td>Tidak ada benda asing</td><td class="cell-center">√</td></tr>
                                        <tr><td>Tidak berbau</td><td class="cell-center">√</td></tr>
                                        <tr><td>Tidak cacat/bolong</td><td class="cell-center">√</td></tr>
                                        <tr><td>Segel container</td><td class="cell-center">√</td></tr>
                                        <tr><td colspan="2"><strong>Kesimpulan :</strong> Sesuai</td></tr>
                                        <tr><td colspan="2"><strong>Catatan :</strong> -</td></tr>
                                    </tbody>
                                </table>

                                {{-- 2. Dokumen dan Identitas --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="th-sub">Dokumen dan Identitas</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th style="width: 70%;">Dokumen</th>
                                            <th class="cell-center" style="width: 30%;">Ada/Tidak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>COA</td><td class="cell-center">{{ ($sampling && $sampling->coa === 'Ada') ? 'Ada' : 'Tidak' }}</td></tr>
                                        <tr><td>Surat Jalan Vendor</td><td class="cell-center">Ada</td></tr>
                                        <tr><td>Packing List</td><td class="cell-center">Ada</td></tr>
                                        <tr><td>Identitas PM di kemasan</td><td class="cell-center">Ada</td></tr>
                                    </tbody>
                                </table>

                                {{-- 3. Keterangan SOP --}}
                                <div class="keterangan-box">
                                    <strong>Keterangan :</strong><br>
                                    @if($cat === 'karton')
                                        * Ukuran (pxlxt) : panjang x lebar x tinggi<br>
                                    @else
                                        * Ukuran (pxl) : panjang x lebar<br>
                                    @endif
                                    * Kondisi diisi dengan :<br>
                                    &nbsp;&nbsp;<strong>√</strong> : Ya<br>
                                    &nbsp;&nbsp;<strong>x</strong> : Tidak<br>
                                    &nbsp;&nbsp;<strong>-</strong> : Tidak Ada<br>

                                    @if($cat === 'pouch')
                                        <div class="mt-2">
                                            <strong>Thickness bottom :</strong><br>
                                            1. {{ $t1 ?: '..........................' }}<br>
                                            2. {{ $t2 ?: '..........................' }}<br>
                                            3. {{ $t3 ?: '..........................' }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Sub-Column 2: Pengecekan Fisik, Serah Terima Sampel, Disposisi --}}
                            <div class="doc-sub-col">
                                {{-- 1. Pengecekan Fisik --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="th-header">Pengecekan Fisik</th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="th-sub">Fisik Dus/Roll</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th>Kriteria</th>
                                            <th class="cell-center" style="width: 35%;">Qty Dus/Roll</th>
                                            <th class="cell-center" style="width: 25%;">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Rusak</td><td class="cell-center">0</td><td class="cell-center">0%</td></tr>
                                        <tr><td>Kotor</td><td class="cell-center">0</td><td class="cell-center">0%</td></tr>
                                        <tr><td>Aroma</td><td class="cell-center">0</td><td class="cell-center">0%</td></tr>
                                        <tr><td>Basah</td><td class="cell-center">0</td><td class="cell-center">0%</td></tr>
                                        <tr><td>Campuran</td><td class="cell-center">0</td><td class="cell-center">0%</td></tr>
                                        <tr class="row-avg">
                                            <td>Total Bagus</td>
                                            <td class="cell-center">{{ $inc->jumlah !== null ? number_format((float)$inc->jumlah, 2, '.', '') : '100.00' }}</td>
                                            <td class="cell-center">100%</td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- 2. Serah Terima Sampel --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="th-sub">Serah Terima Sampel</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th>Keterangan</th>
                                            <th class="cell-center" style="width: 30%;">Jam</th>
                                            <th class="cell-center" style="width: 25%;">Ttd</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Sampling<br>
                                                <small class="text-muted">Jumlah : {{ $inc->jumlah_sampel ?? 1 }}</small>
                                            </td>
                                            <td class="cell-center">{{ $jamDatang }}</td>
                                            <td class="cell-center">√</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Re-Sampling<br>
                                                <small class="text-muted">Jumlah : -</small>
                                            </td>
                                            <td class="cell-center">-</td>
                                            <td class="cell-center">-</td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- 3. Disposisi --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th class="th-sub">Disposisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 6px 8px;">
                                                <strong>Disposisi :</strong>
                                                <span class="badge {{ ($sampling && $sampling->rekomendasi === 'Ditolak') ? 'bg-danger' : 'bg-success' }} fs-11 ms-1">
                                                    {{ $sampling?->rekomendasi ?? 'Diterima' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 4px 8px;">
                                                <strong>Catatan :</strong><br>
                                                <small class="text-muted">{{ $sampling?->keterangan ?? '* monitoring seminggu sekali' }}</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        {{-- ==================== RIGHT SIDE (56%): ANALISIS SAMPEL (FULL 30 ROWS) ==================== --}}
                        <div class="doc-right-block">
                            @if($cat === 'pouch')
                                {{-- POUCH ANALISIS TABLE --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="12" class="th-header">Analisis</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th class="cell-center" style="width: 32px;">No Box</th>
                                            <th class="cell-center" style="width: 32px;">No Sample</th>
                                            <th class="cell-center">Desain</th>
                                            <th class="cell-center">Barcode& QR code</th>
                                            <th class="cell-center">Warna</th>
                                            <th class="cell-center">Tulisan</th>
                                            <th class="cell-center">Ukuran (pxl)</th>
                                            <th class="cell-center">Tebal</th>
                                            <th class="cell-center">Berat</th>
                                            <th class="cell-center">Side Seal</th>
                                            <th class="cell-center">Bottom Seal</th>
                                            <th class="cell-center">Bottom High</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 1; $i <= $maxSampleRows; $i++)
                                            @php
                                                $s = $samples[$i - 1] ?? null;
                                                $p = $s['panjang'] ?? '';
                                                $l = $s['lebar'] ?? '';
                                                $ukuran = ($p && $l) ? "{$p}x{$l}" : ($s ? '-' : '');
                                                $tebal = $s ? ($s['thickness_1'] ?? $s['thickness'] ?? '-') : '';
                                                $berat = $s ? ($s['berat'] ?? '-') : '';
                                                $sideSeal = $s ? ($s['side_seal_1'] ?? $s['side_seal'] ?? '-') : '';
                                                $bottomSeal = $s ? ($s['bottom_seal'] ?? '-') : '';
                                                $bottomHigh = $s ? ($s['bottom_high'] ?? '-') : '';
                                                $design = $s ? ($s['design'] ?? $s['desain'] ?? 'OK') : '';
                                                $barcode = $s ? ($s['barcode'] ?? $s['qr_code'] ?? 'OK') : '';
                                                $warna = $s ? ($s['warna'] ?? 'OK') : '';
                                                $tulisan = $s ? ($s['tulisan'] ?? 'OK') : '';
                                            @endphp
                                            <tr>
                                                <td class="cell-center">{{ $s ? ($s['no_box'] ?? $i) : '' }}</td>
                                                <td class="cell-center">{{ $s ? $i : '' }}</td>
                                                <td class="cell-center">{{ $design }}</td>
                                                <td class="cell-center">{{ $barcode }}</td>
                                                <td class="cell-center">{{ $warna }}</td>
                                                <td class="cell-center">{{ $tulisan }}</td>
                                                <td class="cell-center">{{ $ukuran }}</td>
                                                <td class="cell-center">{{ $tebal }}</td>
                                                <td class="cell-center">{{ $berat }}</td>
                                                <td class="cell-center">{{ $sideSeal }}</td>
                                                <td class="cell-center">{{ $bottomSeal }}</td>
                                                <td class="cell-center">{{ $bottomHigh }}</td>
                                            </tr>
                                        @endfor
                                        <tr class="row-avg">
                                            <td colspan="7" class="cell-center fw-bold">Average</td>
                                            <td class="cell-center fw-bold">{{ $avgTebal }}</td>
                                            <td class="cell-center fw-bold">{{ $avgBerat }}</td>
                                            <td class="cell-center fw-bold">{{ $avgSideSeal }}</td>
                                            <td class="cell-center fw-bold">{{ $avgBottomSeal }}</td>
                                            <td class="cell-center fw-bold">{{ $avgBottomHigh }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            @elseif($cat === 'karton')
                                {{-- KARTON ANALISIS TABLE --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="9" class="th-header">Pengecekan Karton</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th class="cell-center" style="width: 38px;">No Sampel</th>
                                            <th class="cell-center">Desain</th>
                                            <th class="cell-center">Warna</th>
                                            <th class="cell-center">Tulisan</th>
                                            <th class="cell-center">Barcode</th>
                                            <th class="cell-center">Ukuran (pxlxt)</th>
                                            <th class="cell-center">Berat (g)</th>
                                            <th class="cell-center">Gramasi</th>
                                            <th class="cell-center">BCT*</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 1; $i <= $maxSampleRows; $i++)
                                            @php
                                                $s = $samples[$i - 1] ?? null;
                                                $p = $s['panjang'] ?? '';
                                                $l = $s['lebar'] ?? '';
                                                $t = $s['tinggi'] ?? '';
                                                $dimensi = ($p && $l && $t) ? "{$p}x{$l}x{$t}" : ($s ? '-' : '');
                                                $berat = $s ? ($s['berat'] ?? $s['hasil_berat'] ?? '-') : '';
                                                $gramasi = $s ? ($s['gramasi'] ?? '-') : '';
                                                $bctVal = $s ? ($bct?->average ?? '-') : '';
                                                $design = $s ? ($s['desain'] ?? $s['design'] ?? 'OK') : '';
                                                $warna = $s ? ($s['warna'] ?? 'OK') : '';
                                                $tulisan = $s ? ($s['tulisan'] ?? 'OK') : '';
                                                $barcode = $s ? ($s['barcode'] ?? 'OK') : '';
                                            @endphp
                                            <tr>
                                                <td class="cell-center">{{ $s ? $i : '' }}</td>
                                                <td class="cell-center">{{ $design }}</td>
                                                <td class="cell-center">{{ $warna }}</td>
                                                <td class="cell-center">{{ $tulisan }}</td>
                                                <td class="cell-center">{{ $barcode }}</td>
                                                <td class="cell-center">{{ $dimensi }}</td>
                                                <td class="cell-center">{{ $berat }}</td>
                                                <td class="cell-center">{{ $gramasi }}</td>
                                                <td class="cell-center">{{ $bctVal }}</td>
                                            </tr>
                                        @endfor
                                        <tr class="row-avg">
                                            <td colspan="6" class="cell-center fw-bold">Average</td>
                                            <td class="cell-center fw-bold">{{ $avgKartonBerat }}</td>
                                            <td class="cell-center fw-bold">{{ $avgKartonGramasi }}</td>
                                            <td class="cell-center fw-bold">{{ $avgKartonBct }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            @else
                                {{-- INNER SACHET ANALISIS TABLE --}}
                                <table class="table-doc-excel">
                                    <thead>
                                        <tr>
                                            <th colspan="12" class="th-header">Analisis</th>
                                        </tr>
                                        <tr class="th-col">
                                            <th class="cell-center" style="width: 32px;">No Sampel</th>
                                            <th class="cell-center">Desain</th>
                                            <th class="cell-center">Warna</th>
                                            <th class="cell-center">Tulisan</th>
                                            <th class="cell-center">Berat Gross (kg)</th>
                                            <th class="cell-center">Inside Core (cm)</th>
                                            <th class="cell-center">Ukuran(pxl)</th>
                                            <th class="cell-center">Tebal (mm)</th>
                                            <th class="cell-center">Bonding Strength</th>
                                            <th class="cell-center">Seal Strength</th>
                                            <th class="cell-center">Laminasi</th>
                                            <th class="cell-center">Stability Test</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 1; $i <= $maxSampleRows; $i++)
                                            @php
                                                $s = $samples[$i - 1] ?? null;
                                                $p = $s['panjang'] ?? '';
                                                $l = $s['lebar'] ?? '';
                                                $dimensi = ($p && $l) ? "{$p}x{$l}" : ($s ? '-' : '');
                                                $beratGross = $s ? ($s['berat_gross'] ?? '-') : '';
                                                $insideCore = $s ? ($s['inside_core'] ?? '-') : '';
                                                $tebal = $s ? ($s['tebal'] ?? '-') : '';
                                                $bonding = $s ? ($s['bonding_strength'] ?? '-') : '';
                                                $seal = $s ? ($s['seal_strength'] ?? '-') : '';
                                                $laminasi = $s ? ($s['laminasi'] ?? 'OK') : '';
                                                $stability = $s ? ($s['stability_test'] ?? 'OK') : '';
                                                $design = $s ? ($s['desain'] ?? $s['design'] ?? 'OK') : '';
                                                $warna = $s ? ($s['warna'] ?? 'OK') : '';
                                                $tulisan = $s ? ($s['tulisan'] ?? 'OK') : '';
                                            @endphp
                                            <tr>
                                                <td class="cell-center">{{ $s ? $i : '' }}</td>
                                                <td class="cell-center">{{ $design }}</td>
                                                <td class="cell-center">{{ $warna }}</td>
                                                <td class="cell-center">{{ $tulisan }}</td>
                                                <td class="cell-center">{{ $beratGross }}</td>
                                                <td class="cell-center">{{ $insideCore }}</td>
                                                <td class="cell-center">{{ $dimensi }}</td>
                                                <td class="cell-center">{{ $tebal }}</td>
                                                <td class="cell-center">{{ $bonding }}</td>
                                                <td class="cell-center">{{ $seal }}</td>
                                                <td class="cell-center">{{ $laminasi }}</td>
                                                <td class="cell-center">{{ $stability }}</td>
                                            </tr>
                                        @endfor
                                        <tr class="row-avg">
                                            <td colspan="4" class="cell-center fw-bold">Average</td>
                                            <td class="cell-center fw-bold">{{ $avgInnerBeratGross }}</td>
                                            <td class="cell-center fw-bold">{{ $avgInnerInsideCore }}</td>
                                            <td class="cell-center">-</td>
                                            <td class="cell-center fw-bold">{{ $avgInnerTebal }}</td>
                                            <td class="cell-center fw-bold">{{ $avgInnerBonding }}</td>
                                            <td class="cell-center fw-bold">{{ $avgInnerSeal }}</td>
                                            <td class="cell-center">-</td>
                                            <td class="cell-center">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>

                    </div>

                    {{-- Footer Tanda Tangan (Matching Excel) --}}
                    @php
                        $creator = $sampling?->creator?->name ?? '..........................';
                        $updater = $sampling?->updater?->name ?? '..........................';
                    @endphp
                    <table class="table-doc-sign">
                        <tr>
                            @if($cat === 'karton')
                                <td style="width: 25%;">
                                    <div>Disampling oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QRP</div>
                                </td>
                                <td style="width: 25%;">
                                    <div>Dianalisis oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QRP</div>
                                </td>
                                <td style="width: 25%;">
                                    <div>Dianalisis oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $updater }} )</div>
                                    <div class="text-muted fs-11">QC PM</div>
                                </td>
                                <td style="width: 25%;">
                                    <div>Dicek oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( .......................... )</div>
                                    <div class="text-muted fs-11">SPV/MNG QC</div>
                                </td>
                            @elseif($cat === 'pouch')
                                <td style="width: 28%;">
                                    <div>Disampling oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QC PM</div>
                                </td>
                                <td style="width: 28%;">
                                    <div>Dianalisis oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QC PM</div>
                                </td>
                                <td style="width: 28%;">
                                    <div>Dicek oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( .......................... )</div>
                                    <div class="text-muted fs-11">Staff/SPV/MGR QC</div>
                                </td>
                                <td style="width: 16%; vertical-align: bottom; text-align: right;">
                                    <div class="doc-form-code">{{ $formNo }}</div>
                                </td>
                            @else
                                <td style="width: 28%;">
                                    <div>Disampling oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QRP</div>
                                </td>
                                <td style="width: 28%;">
                                    <div>Dianalisis oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( {{ $creator }} )</div>
                                    <div class="text-muted fs-11">QRP</div>
                                </td>
                                <td style="width: 28%;">
                                    <div>Dicek oleh,</div>
                                    <div class="sign-space"></div>
                                    <div>( .......................... )</div>
                                    <div class="text-muted fs-11">Staff/SPV/MGR QC</div>
                                </td>
                                <td style="width: 16%; vertical-align: bottom; text-align: right;">
                                    <div class="doc-form-code">{{ $formNo }}</div>
                                </td>
                            @endif
                        </tr>
                        @if($cat === 'karton')
                            <tr>
                                <td colspan="4" style="text-align: right; padding-top: 0;">
                                    <div class="doc-form-code">{{ $formNo }}</div>
                                </td>
                            </tr>
                        @endif
                    </table>

                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    function applyFilters() {
        const jenis = document.getElementById('filterJenisSelect').value;
        const id = document.getElementById('selectSpb').value;
        let url = '{{ route("doc-pm.index") }}?';
        const params = [];
        if (jenis) params.push('jenis=' + encodeURIComponent(jenis));
        if (id) params.push('id=' + encodeURIComponent(id));
        window.location.href = url + params.join('&');
    }
</script>
@endsection
