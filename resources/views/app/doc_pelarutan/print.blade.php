<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PROSES PELARUTAN - {{ $data['po_number'] ?? '' }}</title>
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
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
            margin-bottom: 12px;
            font-size: 8pt;
        }

        .table-doc th, .table-doc td {
            border: 1px solid #000;
            padding: 3px 2px;
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

        .block-container {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 12px;
        }

        .sign-container {
            border: 1px solid #000;
            margin-top: 10px;
        }

        .sign-box {
            border-right: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 8.5pt;
        }

        .sign-box:last-child {
            border-right: none;
        }

        .sign-space {
            height: 45px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 6mm;
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
        <!-- Header Dokumen -->
        <table class="w-100 mb-2 border-bottom pb-2">
            <tr>
                <td width="20%" class="align-middle">
                    <div style="border: 2px solid #000; padding: 2px 4px; display: inline-block;">
                        <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 44px; object-fit: contain;">
                    </div>
                </td>
                <td width="60%" class="align-middle text-center">
                    <div class="doc-title">HASIL ANALISIS PROSES PELARUTAN</div>
                </td>
                <td width="20%" class="text-end align-middle">
                    <div class="small fw-bold">Tanggal Record Doc : {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</div>
                    <div class="small fw-bold">Halaman : {{ $data['halaman'] ?? '1' }}</div>
                </td>
            </tr>
        </table>

        <!-- Batch Blocks -->
        @if(!empty($data['batch_blocks']))
            @foreach($data['batch_blocks'] as $block)
            <div class="block-container">
                <!-- Meta Table -->
                <table class="w-100 meta-table mb-2">
                    <tr>
                        <td width="15%" class="fw-bold">Jenis Produk</td>
                        <td width="35%">: {{ $block['jenis_produk'] ?? '-' }}</td>
                        <td width="15%" class="fw-bold">Batch</td>
                        <td width="35%">: {{ $block['batch'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal Produksi</td>
                        <td>: {{ $block['tanggal_produksi'] ?? '-' }}</td>
                        <td class="fw-bold">No. Dissolver</td>
                        <td>: {{ $block['no_dissolver'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jam Produksi</td>
                        <td>: {{ $block['jam_produksi'] ?? '-' }}</td>
                        <td class="fw-bold">Volume</td>
                        <td>: {{ $block['volume'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kode Shift & Grup</td>
                        <td>: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                <!-- Table -->
                <table class="table-doc">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 45px;">Sampling ke-</th>
                            <th colspan="2">Serah Terima</th>
                            <th colspan="5">Pelarutan I (GGA)</th>
                            <th rowspan="2" style="width: 45px;">Sampling ke-</th>
                            <th colspan="2">Serah Terima</th>
                            <th colspan="5">Pelarutan II (GGAS)</th>
                            <th rowspan="2" style="min-width: 80px;">Disposisi</th>
                        </tr>
                        <tr>
                            <th style="width: 50px;">Jam</th>
                            <th style="width: 70px;">PIC</th>
                            <th style="width: 50px;">Brix</th>
                            <th style="width: 50px;">%NaCl</th>
                            <th style="width: 60px;">Warna</th>
                            <th style="width: 55px;">Organo</th>
                            <th style="min-width: 100px;">Waktu & Adjustment</th>
                            <th style="width: 50px;">Jam</th>
                            <th style="width: 70px;">PIC</th>
                            <th style="width: 50px;">Brix</th>
                            <th style="width: 50px;">%NaCl</th>
                            <th style="width: 60px;">Warna</th>
                            <th style="width: 55px;">Organo</th>
                            <th style="min-width: 100px;">Waktu & Adjustment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($block['rows']))
                            @foreach($block['rows'] as $r)
                            <tr>
                                <td>{{ $r['p1_sampling_ke'] ?? '1' }}</td>
                                <td>{{ $r['p1_jam'] ?? '-' }}</td>
                                <td>{{ $r['p1_pic'] ?? '-' }}</td>
                                <td>{{ $r['p1_brix'] ?? '-' }}</td>
                                <td>{{ $r['p1_nacl'] ?? '-' }}</td>
                                <td>{{ $r['p1_warna'] ?? '-' }}</td>
                                <td>{{ $r['p1_organo'] ?? '-' }}</td>
                                <td class="text-start">{{ $r['p1_waktu_adjustment'] ?? '-' }}</td>

                                <td>{{ $r['p2_sampling_ke'] ?? '1' }}</td>
                                <td>{{ $r['p2_jam'] ?? '-' }}</td>
                                <td>{{ $r['p2_pic'] ?? '-' }}</td>
                                <td>{{ $r['p2_brix'] ?? '-' }}</td>
                                <td>{{ $r['p2_nacl'] ?? '-' }}</td>
                                <td>{{ $r['p2_warna'] ?? '-' }}</td>
                                <td>{{ $r['p2_organo'] ?? '-' }}</td>
                                <td class="text-start">{{ $r['p2_waktu_adjustment'] ?? '-' }}</td>

                                <td>{{ $r['disposisi'] ?? 'Release' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="17">- Tidak Ada Data -</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endforeach
        @endif

        <!-- Catatan -->
        <div class="small mb-2">
            <span class="fw-bold">Catatan :</span> {{ $data['catatan'] ?? '-' }}
        </div>

        <!-- Signatures -->
        <div class="sign-container">
            <div class="row g-0">
                <div class="col-4 sign-box">
                    <div class="text-start fw-bold">Disampling oleh,</div>
                    <div class="sign-space"></div>
                    <div>( {{ $data['pic_sampling'] ?: '________________' }} )</div>
                    <div>&nbsp;</div>
                </div>
                <div class="col-4 sign-box">
                    <div class="text-start fw-bold">Dianalisis oleh,</div>
                    <div class="sign-space"></div>
                    <div>( {{ $data['pic_analis'] ?: '________________' }} )</div>
                    <div class="fw-bold">QC Analis</div>
                </div>
                <div class="col-4 sign-box">
                    <div class="text-start fw-bold">Dicek oleh,</div>
                    <div class="sign-space"></div>
                    <div>( {{ $data['pic_checker'] ?: '________________' }} )</div>
                    <div class="fw-bold">Staff/SPV/MNG QC</div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
