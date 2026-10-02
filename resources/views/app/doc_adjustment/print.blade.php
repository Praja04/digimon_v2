<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM ADJUSTMENT - {{ $data['po_number'] ?? '' }}</title>
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .table-doc {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }

        .table-doc th, .table-doc td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }

        .table-doc th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .meta-table td {
            padding: 2px 4px;
            font-size: 8.5pt;
        }

        .sign-box {
            text-align: center;
            font-size: 8pt;
            border: 1px solid #000;
            padding: 6px;
        }

        .sign-space {
            height: 40px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid px-0">
        <!-- Header Table -->
        <table class="table-doc mb-2">
            <tr>
                <td width="20%" class="text-center p-2" style="border: 1px solid #000;">
                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 42px; object-fit: contain;">
                </td>
                <td width="55%" class="text-center align-middle" style="border: 1px solid #000;">
                    <div class="doc-title">FORM ADJUSTMENT</div>
                </td>
                <td width="25%" class="p-1" style="border: 1px solid #000; font-size: 8pt;">
                    <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                        <span class="fw-bold">Tanggal Record Doc:</span>
                        <span>{{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Halaman:</span>
                        <span>{{ $data['halaman'] ?? '1' }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Meta Table -->
        <table class="w-100 meta-table mb-2">
            <tr>
                <td width="15%" class="fw-bold">Proses</td>
                <td width="35%">: {{ $data['proses'] ?? 'Blending' }}</td>
                <td width="18%" class="fw-bold">Jenis Kecap</td>
                <td width="32%">: {{ $data['jenis_kecap'] ?? ($data['variant'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">No. Batch</td>
                <td>: {{ $data['no_batch'] ?? ($data['batch_range'] ?? '-') }}</td>
                <td class="fw-bold">Tanggal Produksi</td>
                <td>: {{ $data['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Volume Batch</td>
                <td>: {{ $data['volume_batch'] ?? '-' }}</td>
                <td class="fw-bold">Shift</td>
                <td>: {{ $data['shift'] ?? '1' }}</td>
            </tr>
        </table>

        <!-- TABEL UTAMA -->
        <table class="table-doc">
            <thead>
                <tr>
                    <th style="width: 25%;">BAHAN</th>
                    <th style="width: 18%;">ADJUSMENT 1</th>
                    <th style="width: 18%;">ADJUSMENT 2</th>
                    <th style="width: 18%;">ADJUSMENT 3</th>
                    <th style="width: 21%;">DISPOSISI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $bahanList = !empty($data['bahan_rows']) ? $data['bahan_rows'] : [
                        ['bahan' => 'Gula Kelapa', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Gula Tebu', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Larutan Garam', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Garam Kasar', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Air', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Garam Halus', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => 'Karamel (Jenis)', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                        ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                    ];
                    $totalRows = count($bahanList);
                @endphp

                @foreach($bahanList as $idx => $b)
                <tr>
                    <td class="text-start ps-2 fw-semibold">{{ $b['bahan'] ?? '' }}</td>
                    <td>{{ $b['adj1'] ?? '' }}</td>
                    <td>{{ $b['adj2'] ?? '' }}</td>
                    <td>{{ $b['adj3'] ?? '' }}</td>
                    
                    @if($idx === 0)
                    <td rowspan="{{ $totalRows }}" class="align-middle fw-bold text-success p-2" style="font-size: 10pt;">
                        {{ $data['disposisi'] ?? 'Release' }}
                    </td>
                    @endif
                </tr>
                @endforeach

                <!-- Keterangan & Signatures Section -->
                <tr>
                    <td rowspan="4" class="align-top text-start p-2" style="border: 1px solid #000;">
                        <div class="fw-bold small mb-1">Keterangan :</div>
                        <div class="small" style="white-space: pre-wrap;">{{ $data['keterangan'] ?? '' }}</div>
                    </td>
                    <td class="text-start ps-2" style="font-size: 8pt;">Jam : {{ $data['adj1_jam'] ?? '' }}</td>
                    <td class="text-start ps-2" style="font-size: 8pt;">Jam : {{ $data['adj2_jam'] ?? '' }}</td>
                    <td class="text-start ps-2" style="font-size: 8pt;">Jam : {{ $data['adj3_jam'] ?? '' }}</td>
                    <td rowspan="2" class="align-middle" style="font-size: 8pt;">Tanda Tangan,</td>
                </tr>
                <tr>
                    <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $data['adj1_status'] ?? 'Sudah dilakukan/belum' }}</td>
                    <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $data['adj2_status'] ?? 'Sudah dilakukan/belum' }}</td>
                    <td class="text-start ps-2" style="font-size: 7.5pt;">Status : {{ $data['adj3_status'] ?? 'Sudah dilakukan/belum' }}</td>
                </tr>
                <tr>
                    <td style="font-size: 8pt;">Tanda Tangan,</td>
                    <td style="font-size: 8pt;">Tanda Tangan,</td>
                    <td style="font-size: 8pt;">Tanda Tangan,</td>
                    <td rowspan="2" class="align-bottom pb-1" style="height: 60px;">
                        <div class="fw-bold" style="font-size: 7.5pt;">( {{ $data['qc_analis'] ?: 'Analis QC' }} )</div>
                    </td>
                </tr>
                <tr>
                    <td class="align-bottom pb-1" style="height: 50px;">
                        <div class="fw-bold" style="font-size: 7.5pt;">( {{ $data['adj1_petugas'] ?: 'Petugas Produksi' }} )</div>
                    </td>
                    <td class="align-bottom pb-1" style="height: 50px;">
                        <div class="fw-bold" style="font-size: 7.5pt;">( {{ $data['adj2_petugas'] ?: 'Petugas Produksi' }} )</div>
                    </td>
                    <td class="align-bottom pb-1" style="height: 50px;">
                        <div class="fw-bold" style="font-size: 7.5pt;">( {{ $data['adj3_petugas'] ?: 'Petugas Produksi' }} )</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Document Code Footer -->
        <div class="text-end pt-1" style="font-size: 7.5pt; font-style: italic;">
            {{ $data['doc_code'] ?? 'FRM/QLB/04/104/011-00' }}
        </div>
    </div>
</body>
</html>
