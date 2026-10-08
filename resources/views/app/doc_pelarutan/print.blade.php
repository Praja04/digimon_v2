<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PROSES PELARUTAN - {{ $data['po_number'] ?? '' }}</title>
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }

        .sheet-page {
            width: 100%;
            background: #fff;
            padding: 2px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .table-doc {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 7.5pt;
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

        .meta-table td {
            padding: 1.5px 3px;
            font-size: 8pt;
        }

        .block-card {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 6px;
        }

        .sign-container {
            border: 1px solid #000;
            margin-top: 4px;
        }

        .sign-box {
            border-right: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            font-size: 8pt;
            vertical-align: top;
        }

        .sign-box:last-child {
            border-right: none;
        }

        .sign-space {
            height: 36px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm 7mm;
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
        $blocks = !empty($data['batch_blocks']) ? $data['batch_blocks'] : [];
        if (empty($blocks)) {
            $blocks = [
                ['batch' => '1', 'no_dissolver' => '1', 'rows' => []],
                ['batch' => '2', 'no_dissolver' => '2', 'rows' => []]
            ];
        }
        $sheetPairs = array_chunk($blocks, 2);
        $totalSheets = count($sheetPairs);
    @endphp

    @foreach($sheetPairs as $pIdx => $pair)
    <div class="sheet-page">
        <!-- Header Dokumen (FRM/QLB/04/104/004-01) -->
        <table class="w-100 mb-1 border" style="border-collapse: collapse;">
            <tr>
                <td width="20%" class="align-middle p-2 border-end text-center">
                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                </td>
                <td width="55%" class="align-middle text-center border-end">
                    <div class="doc-title">HASIL ANALISIS PROSES PELARUTAN</div>
                </td>
                <td width="25%" class="align-middle p-0">
                    <table class="w-100" style="font-size: 7.5pt; border-collapse: collapse;">
                        <tr>
                            <td class="p-1 border-bottom text-start fw-bold bg-light" width="55%">Tanggal Record Doc</td>
                            <td class="p-1 border-bottom text-start" width="45%">: {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                            <td class="p-1 text-start">: {{ $pIdx + 1 }} / {{ $totalSheets }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Loop 2 Batch Blocks per Lembar (1 Lembar = 2 Batch) -->
        @foreach($pair as $bIdx => $block)
        <div class="block-card">
            <!-- Meta Table -->
            <table class="w-100 meta-table mb-1">
                <tr>
                    <td width="15%" class="fw-bold">Jenis Produk</td>
                    <td width="35%">: {{ $block['jenis_produk'] ?? ($data['variant'] ?? '-') }}</td>
                    <td width="15%" class="fw-bold">Batch</td>
                    <td width="35%">: {{ $block['batch'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Tanggal Produksi</td>
                    <td>: {{ $block['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
                    <td class="fw-bold">No. Dissolver</td>
                    <td>: {{ $block['no_dissolver'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Jam Produksi</td>
                    <td>: {{ $block['jam_produksi'] ?? '-' }}</td>
                    <td class="fw-bold">Volume</td>
                    <td>: {{ $block['volume'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Kode Shift & Grup</td>
                    <td>: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                    <td colspan="2"></td>
                </tr>
            </table>

            <!-- Table Analisis Pelarutan (5 Baris Standar) -->
            <table class="table-doc">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                        <th colspan="2" style="width: 10%;">Serah Terima</th>
                        <th colspan="5" style="width: 36%;">Pelarutan I (GGA)</th>
                        <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                        <th colspan="2" style="width: 10%;">Serah Terima</th>
                        <th colspan="5" style="width: 36%;">Pelarutan II (GGAS)</th>
                        <th rowspan="2" style="width: 8%;">Disposisi</th>
                    </tr>
                    <tr>
                        <th style="width: 4%;">Jam</th>
                        <th style="width: 6%;">PIC</th>
                        <th style="width: 5%;">Brix</th>
                        <th style="width: 5%;">%NaCl</th>
                        <th style="width: 6%;">Warna</th>
                        <th style="width: 5%;">Organo</th>
                        <th style="width: 15%;">Waktu & Adjustment</th>
                        <th style="width: 4%;">Jam</th>
                        <th style="width: 6%;">PIC</th>
                        <th style="width: 5%;">Brix</th>
                        <th style="width: 5%;">%NaCl</th>
                        <th style="width: 6%;">Warna</th>
                        <th style="width: 5%;">Organo</th>
                        <th style="width: 15%;">Waktu & Adjustment</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rows = $block['rows'] ?? [];
                        $totalRows = max(5, count($rows));
                    @endphp

                    @for($i = 0; $i < $totalRows; $i++)
                        @php
                            $r = $rows[$i] ?? null;
                        @endphp
                        <tr>
                            <td>{{ $r['p1_sampling_ke'] ?? ($i + 1) }}</td>
                            <td>{{ $r['p1_jam'] ?? '' }}</td>
                            <td>{{ $r['p1_pic'] ?? '' }}</td>
                            <td>{{ $r['p1_brix'] ?? '' }}</td>
                            <td>{{ $r['p1_nacl'] ?? '' }}</td>
                            <td>{{ $r['p1_warna'] ?? '' }}</td>
                            <td>{{ $r['p1_organo'] ?? '' }}</td>
                            <td class="text-start" style="padding-left: 4px;">{{ $r['p1_waktu_adjustment'] ?? '' }}</td>

                            <td>{{ $r['p2_sampling_ke'] ?? ($i + 1) }}</td>
                            <td>{{ $r['p2_jam'] ?? '' }}</td>
                            <td>{{ $r['p2_pic'] ?? '' }}</td>
                            <td>{{ $r['p2_brix'] ?? '' }}</td>
                            <td>{{ $r['p2_nacl'] ?? '' }}</td>
                            <td>{{ $r['p2_warna'] ?? '' }}</td>
                            <td>{{ $r['p2_organo'] ?? '' }}</td>
                            <td class="text-start" style="padding-left: 4px;">{{ $r['p2_waktu_adjustment'] ?? '' }}</td>

                            <td>{{ $r['disposisi'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        @endforeach

        <!-- Footer Catatan & Tanda Tangan Per Lembar -->
        <table class="w-100 sign-container" style="border-collapse: collapse;">
            <tr>
                <td width="35%" class="p-2 align-top border-end">
                    <strong>Catatan :</strong>
                    <div style="min-height: 42px; font-size: 7.5pt; white-space: pre-wrap;" class="mt-1">
                        {{ $data['catatan'] ?: '-' }}
                    </div>
                </td>
                <td width="21%" class="sign-box">
                    <div class="fw-semibold">Disampling oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_sampling'] ?: '________________' }} )</div>
                    <div class="text-muted fw-bold mt-1" style="font-size: 7pt;">Produksi</div>
                </td>
                <td width="22%" class="sign-box">
                    <div class="fw-semibold">Dianalisis oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_analis'] ?: '________________' }} )</div>
                    <div class="text-muted fw-bold mt-1" style="font-size: 7pt;">QC Analis</div>
                </td>
                <td width="22%" class="sign-box">
                    <div class="fw-semibold">Dicek oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_checker'] ?: '________________' }} )</div>
                    <div class="text-muted fw-bold mt-1" style="font-size: 7pt;">Staff/SPV/MNG QC</div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-end pe-2 py-1" style="font-size: 7pt; font-style: italic; border-top: 1px solid #000;">
                    FRM/QLB/04/104/004-01
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
