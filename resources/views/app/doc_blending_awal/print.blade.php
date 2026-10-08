<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PROSES BLENDING - {{ $data['po_number'] ?? '' }}</title>
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }

        .sheet-page {
            width: 100%;
            background: #fff;
            padding: 4px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .table-doc {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .table-doc th, .table-doc td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }

        .table-doc th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: middle;
        }

        .sign-container {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .sign-container td {
            border: 1px solid #000;
        }

        .sign-box {
            padding: 4px 6px;
            text-align: center;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .sign-space {
            height: 40px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 6mm 8mm;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .sheet-page {
                padding: 0;
                margin-bottom: 0;
                page-break-after: always;
                break-after: page;
            }
            .sheet-page:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Tombol Aksi Layar Non-Print -->
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-1">
            <i class="ri-printer-line"></i> Cetak / Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    @php
        $blocks = !empty($data['batch_blocks']) ? $data['batch_blocks'] : [[]];
        $totalBlocks = count($blocks);
    @endphp

    @foreach($blocks as $bIdx => $block)
    <div class="sheet-page">
        <!-- 1. Header Dokumen Resmi FRM/QLB/04/104/005-01 -->
        <table class="w-100 mb-2" style="border-collapse: collapse; border: 1px solid #000;">
            <tr>
                <td width="20%" class="align-middle p-2" style="text-align: center; border-right: 1px solid #000;">
                    <div style="display: inline-block;">
                        <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                    </div>
                </td>
                <td width="55%" class="align-middle text-center" style="border-right: 1px solid #000;">
                    <div class="doc-title">HASIL ANALISIS PROSES BLENDING</div>
                </td>
                <td width="25%" class="align-middle p-0">
                    <table class="w-100" style="font-size: 8pt; border-collapse: collapse;">
                        <tr>
                            <td class="p-1 fw-bold bg-light" width="55%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">Tanggal Record Doc</td>
                            <td class="p-1 text-start" width="45%" style="border-bottom: 1px solid #000;">: {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="p-1 fw-bold bg-light" style="border-right: 1px solid #000;">Halaman</td>
                            <td class="p-1 text-start fw-bold">: {{ $bIdx + 1 }} / {{ $totalBlocks }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 2. Metadata Siklus Blending (Tangki) -->
        <table class="meta-table mb-2">
            <tr>
                <td width="14%" class="fw-bold bg-light">Jenis Produk</td>
                <td width="36%">: {{ $block['jenis_produk'] ?? ($data['variant'] ?? '-') }}</td>
                <td width="14%" class="fw-bold bg-light">Batch</td>
                <td width="36%">: {{ $block['batch'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold bg-light">Tanggal Produksi</td>
                <td>: {{ $block['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
                <td class="fw-bold bg-light">No. Blending</td>
                <td>: {{ $block['no_blending'] ?? ($bIdx + 1) }}</td>
            </tr>
            <tr>
                <td class="fw-bold bg-light">Jam Produksi</td>
                <td>: {{ $block['jam_produksi'] ?? '-' }}</td>
                <td class="fw-bold bg-light">Volume awal</td>
                <td>: {{ $block['volume_awal'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold bg-light">Kode Shift & Grup</td>
                <td colspan="3">: {{ $block['kode_shift_grup'] ?? '-' }}</td>
            </tr>
        </table>

        <!-- 3. Tabel Analisis Proses Blending (11 Baris Standar) -->
        <table class="table-doc">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 4.5%;">Sampling ke-</th>
                    <th rowspan="2" style="width: 6%;">Vol Tangki (L)</th>
                    <th colspan="2" style="width: 10%;">Serah Terima</th>
                    <th colspan="10" style="width: 46.5%;">Analisis</th>
                    <th rowspan="2" style="width: 24%;">Waktu & Adjustment</th>
                    <th rowspan="2" style="width: 9%;">Disposisi/ Keterangan</th>
                </tr>
                <tr>
                    <!-- Serah Terima -->
                    <th style="width: 4%;">Jam</th>
                    <th style="width: 6%;">PIC</th>
                    <!-- Analisis 10 Parameter -->
                    <th style="width: 5%;">BJ (g/mL)</th>
                    <th style="width: 4.5%;">Brix</th>
                    <th style="width: 4%;">pH</th>
                    <th style="width: 4.5%;">% NaCl</th>
                    <th style="width: 5.5%;">Viskositas (ps)</th>
                    <th style="width: 4.5%;">Organo</th>
                    <th style="width: 4.5%;">Aroma</th>
                    <th style="width: 5%;">Warna</th>
                    <th style="width: 4.5%;">Buih</th>
                    <th style="width: 4.5%;">Aw</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rows = $block['rows'] ?? [];
                    $totalRows = max(11, count($rows));
                @endphp

                @for($i = 0; $i < $totalRows; $i++)
                    @php
                        $row = $rows[$i] ?? null;
                        $rawSamp = $row['sampling_ke'] ?? '';
                        $rawVol = $row['vol_tangki'] ?? '';
                        $rawAdj = $row['waktu_adjustment'] ?? '';

                        $isAwal = (strtolower(trim((string)$rawSamp)) === 'awal' || strtolower(trim((string)$rawVol)) === 'awal');
                        $displaySamp = $isAwal ? 'Awal' : ($rawSamp ?: ($row ? ($i + 1) : ''));
                        $displayVol = $isAwal ? '' : $rawVol;
                        $displayAdj = ($isAwal && ($rawAdj === ($row['serah_terima_jam'] ?? '') || $rawAdj === '')) ? '-' : $rawAdj;
                    @endphp
                    <tr>
                        <td class="{{ $isAwal ? 'fw-bold' : '' }}">{{ $displaySamp }}</td>
                        <td>{{ $displayVol }}</td>
                        <td>{{ $row['serah_terima_jam'] ?? '' }}</td>
                        <td>{{ $row['serah_terima_pic'] ?? '' }}</td>
                        <td>{{ $row['bj'] ?? '' }}</td>
                        <td>{{ $row['brix'] ?? '' }}</td>
                        <td>{{ $row['ph'] ?? '' }}</td>
                        <td>{{ $row['nacl'] ?? '' }}</td>
                        <td>{{ $row['visco'] ?? '' }}</td>
                        <td>{{ $row['organo'] ?? '' }}</td>
                        <td>{{ $row['aroma'] ?? '' }}</td>
                        <td>{{ $row['warna'] ?? '' }}</td>
                        <td style="line-height: 1.15; white-space: pre-line; font-size: 7.5pt;">{{ (isset($row['buih']) && (strtolower(trim($row['buih'])) === 'tidak ada' || strtolower(trim($row['buih'])) === "tidak\nada")) ? "Tidak\nAda" : ($row['buih'] ?? '') }}</td>
                        <td>{{ $row['aw'] ?? '' }}</td>
                        <td class="text-start" style="padding: 2px 4px; word-break: break-word; white-space: pre-wrap; font-size: 7.5pt; line-height: 1.2;">{{ $displayAdj }}</td>
                        <td class="fw-semibold">{{ $row['disposisi'] ?? '' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- 4. Footer Catatan & Tanda Tangan Per Lembar -->
        <table class="sign-container">
            <tr>
                <td width="40%" class="p-2 align-top" rowspan="4">
                    <strong>Catatan :</strong>
                    <div style="min-height: 48px; font-size: 8pt; white-space: pre-wrap;" class="mt-1">
                        {{ $block['catatan'] ?: ($data['catatan'] ?: '-') }}
                    </div>
                </td>
                <td width="20%" class="text-center fw-semibold bg-light" style="padding: 3px 0;">Disampling oleh,</td>
                <td width="20%" class="text-center fw-semibold bg-light" style="padding: 3px 0;">Dianalisis oleh,</td>
                <td width="20%" class="text-center fw-semibold bg-light" style="padding: 3px 0;">Dicek oleh,</td>
            </tr>
            <tr>
                <td class="sign-space"></td>
                <td class="sign-space"></td>
                <td class="sign-space"></td>
            </tr>
            <tr>
                <td class="text-center fw-bold" style="padding: 2px 4px;">( {{ $block['pic_sampling'] ?: ($data['pic_sampling'] ?: '________________') }} )</td>
                <td class="text-center fw-bold" style="padding: 2px 4px;">( {{ $block['pic_analis'] ?: ($data['pic_analis'] ?: '________________') }} )</td>
                <td class="text-center fw-bold" style="padding: 2px 4px;">( {{ $block['pic_checker'] ?: ($data['pic_checker'] ?: '________________') }} )</td>
            </tr>
            <tr>
                <td class="text-center fw-bold bg-light py-1" style="font-size: 7.5pt;">Produksi</td>
                <td class="text-center fw-bold bg-light py-1" style="font-size: 7.5pt;">QC Analis</td>
                <td class="text-center fw-bold bg-light py-1" style="font-size: 7.5pt;">Staff/SPV/MNG QC</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end pe-2 py-1 bg-light" style="font-size: 7.5pt; font-style: italic;">
                    FRM/QLB/04/104/005-01
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
