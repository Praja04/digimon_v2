<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM ADJUSTMENT - {{ $data['po_number'] ?? '' }}</title>
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
            font-size: 8.5pt;
        }

        .table-doc th, .table-doc td {
            border: 1px solid #000;
            padding: 4px 6px;
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
            padding: 2.5px 4px;
            font-size: 8.5pt;
        }

        .sign-space {
            height: 38px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 7mm 8mm;
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
        $sheets = !empty($data['adjustment_sheets']) ? $data['adjustment_sheets'] : [$data];
        $totalSheets = count($sheets);
        $defaultBahans = [
            'Gula Kelapa',
            'Gula Tebu',
            'Larutan Garam',
            'Garam Kasar',
            'Air',
            'Garam Halus',
            'Karamel (Jenis)',
        ];
    @endphp

    @foreach($sheets as $sIdx => $sheet)
    <div class="sheet-page">
        <!-- Header Dokumen (FRM/QLB/04/104/011-00) -->
        <table class="w-100 mb-2 border" style="border-collapse: collapse;">
            <tr>
                <td width="20%" class="align-middle p-2 border-end text-center">
                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 42px; object-fit: contain;">
                </td>
                <td width="55%" class="align-middle text-center border-end">
                    <div class="doc-title">FORM ADJUSTMENT</div>
                </td>
                <td width="25%" class="align-middle p-0">
                    <table class="w-100" style="font-size: 8pt; border-collapse: collapse;">
                        <tr>
                            <td class="p-1 border-bottom text-start fw-bold bg-light" width="55%">Tanggal Record Doc</td>
                            <td class="p-1 border-bottom text-start" width="45%">: {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                            <td class="p-1 text-start">: {{ $sIdx + 1 }} / {{ $totalSheets }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Metadata Informasi (3 Baris) -->
        <table class="w-100 meta-table mb-2">
            <tr>
                <td width="15%" class="fw-bold">Proses</td>
                <td width="35%">: {{ $sheet['proses'] ?? 'Blending' }}</td>
                <td width="15%" class="fw-bold">Jenis Kecap</td>
                <td width="35%">: {{ $sheet['jenis_kecap'] ?? ($data['variant'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">No. Batch</td>
                <td>: {{ $sheet['no_batch'] ?? '-' }}</td>
                <td class="fw-bold">Tanggal Produksi</td>
                <td>: {{ $sheet['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Volume Batch</td>
                <td>: {{ $sheet['volume_batch'] ?? '-' }}</td>
                <td class="fw-bold">Shift</td>
                <td>: {{ $sheet['shift'] ?? '1' }}</td>
            </tr>
        </table>

        <!-- Tabel Utama Bahan & Adjustment -->
        @php
            $rawRows = $sheet['bahan_rows'] ?? [];
            $bMap = [];
            foreach($rawRows as $r) {
                if(!empty($r['bahan'])) {
                    $bMap[$r['bahan']] = $r;
                }
            }
        @endphp
        <table class="table-doc">
            <thead>
                <tr>
                    <th style="width: 28%; text-align: center;">BAHAN</th>
                    <th style="width: 18%;">ADJUSMENT 1</th>
                    <th style="width: 18%;">ADJUSMENT 2</th>
                    <th style="width: 18%;">ADJUSMENT 3</th>
                    <th style="width: 18%;">DISPOSISI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($defaultBahans as $idxB => $bName)
                    @php
                        $rData = $bMap[$bName] ?? ($rawRows[$idxB] ?? []);
                    @endphp
                    <tr>
                        <td class="text-start ps-2">{{ $bName }}</td>
                        <td>{{ $rData['adj1'] ?? '' }}</td>
                        <td>{{ $rData['adj2'] ?? '' }}</td>
                        <td>{{ $rData['adj3'] ?? '' }}</td>
                        @if($loop->first)
                            <td rowspan="7" class="fw-bold fs-12 align-middle text-center bg-white" style="border: 1px solid #000;">
                                {{ $sheet['disposisi'] ?? 'Release' }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Keterangan & Tanda Tangan Sesuai Format -->
        <table class="table-doc" style="margin-top: -9px;">
            <tr>
                <!-- Kotak Keterangan di Kiri -->
                <td rowspan="5" class="align-top text-start p-2" style="width: 28%; border: 1px solid #000;">
                    <span class="fw-bold">Keterangan :</span>
                    <div style="min-height: 80px; font-size: 8pt; white-space: pre-wrap;" class="mt-1">
                        {{ $sheet['keterangan'] ?: '-' }}
                    </div>
                </td>
                <!-- Baris Jam -->
                <td style="width: 18%;" class="text-start ps-2">Jam : {{ $sheet['adj1_jam'] ?? '' }}</td>
                <td style="width: 18%;" class="text-start ps-2">Jam : {{ $sheet['adj2_jam'] ?? '' }}</td>
                <td style="width: 18%;" class="text-start ps-2">Jam : {{ $sheet['adj3_jam'] ?? '' }}</td>
                <!-- Header Tanda Tangan Disposisi -->
                <td rowspan="2" style="width: 18%; vertical-align: middle;">Tanda Tangan,</td>
            </tr>
            <tr>
                <!-- Baris Status -->
                <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $sheet['adj1_status'] ?? 'Sudah dilakukan/belum' }}</td>
                <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $sheet['adj2_status'] ?? 'Sudah dilakukan/belum' }}</td>
                <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $sheet['adj3_status'] ?? 'Sudah dilakukan/belum' }}</td>
            </tr>
            <tr>
                <!-- Baris Header Tanda Tangan -->
                <td>Tanda Tangan,</td>
                <td>Tanda Tangan,</td>
                <td>Tanda Tangan,</td>
                <td rowspan="2" class="align-middle">
                    <div class="sign-space"></div>
                </td>
            </tr>
            <tr>
                <!-- Space Tanda Tangan -->
                <td><div class="sign-space"></div></td>
                <td><div class="sign-space"></div></td>
                <td><div class="sign-space"></div></td>
            </tr>
            <tr>
                <!-- Nama Tanda Tangan -->
                <td class="fw-semibold">( {{ $sheet['adj1_petugas'] ?: 'Petugas Produksi' }} )</td>
                <td class="fw-semibold">( {{ $sheet['adj2_petugas'] ?: 'Petugas Produksi' }} )</td>
                <td class="fw-semibold">( {{ $sheet['adj3_petugas'] ?: 'Petugas Produksi' }} )</td>
                <td class="fw-semibold">( {{ $sheet['qc_analis'] ?: ($data['qc_analis'] ?: 'Analis QC') }} )</td>
            </tr>
        </table>

        <!-- Kode Dokumen -->
        <div class="text-end pe-1" style="font-size: 7.5pt; font-style: italic;">
            FRM/QLB/04/104/011-00
        </div>
    </div>
    @endforeach
</body>
</html>
