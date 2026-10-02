<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PROSES BLENDING - {{ $data['po_number'] ?? '' }}</title>
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
                    <div class="doc-title">HASIL ANALISIS PROSES BLENDING</div>
                </td>
                <td width="20%" class="align-middle text-end">
                    <table class="float-end border" style="font-size: 8pt;">
                        <tr>
                            <td class="p-1 border-bottom text-start fw-bold">Tanggal Record Doc</td>
                            <td class="p-1 border-bottom">: {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="p-1 text-start fw-bold">Halaman</td>
                            <td class="p-1">: {{ $data['halaman'] ?? '1' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tombol Aksi Layar Non-Print -->
        <div class="no-print mb-3 text-end">
            <button onclick="window.print()" class="btn btn-primary btn-sm me-1">Cetak / Print</button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
        </div>

        @if(!empty($data['batch_blocks']))
            @foreach($data['batch_blocks'] as $bIdx => $block)
            <div class="block-container">
                <!-- Meta Info Blok Batch -->
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
                        <td class="fw-bold">No. Blending</td>
                        <td>: {{ $block['no_blending'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jam Produksi</td>
                        <td>: {{ $block['jam_produksi'] ?? '-' }}</td>
                        <td class="fw-bold">Volume awal</td>
                        <td>: {{ $block['volume_awal'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kode Shift & Grup</td>
                        <td>: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                <!-- Tabel Analisis Proses Blending -->
                <table class="table-doc">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                            <th rowspan="2" style="width: 6%;">Vol Tangki (L)</th>
                            <th colspan="2" style="width: 10%;">Serah Terima</th>
                            <th colspan="10" style="width: 56%;">Analisis</th>
                            <th rowspan="2" style="width: 14%;">Waktu & Adjustment</th>
                            <th rowspan="2" style="width: 10%;">Disposisi/ Keterangan</th>
                        </tr>
                        <tr>
                            <!-- Serah Terima -->
                            <th style="width: 4%;">Jam</th>
                            <th style="width: 6%;">PIC</th>
                            <!-- Analisis 10 Parameter -->
                            <th style="width: 6%;">BJ (g/mL)</th>
                            <th style="width: 5%;">Brix</th>
                            <th style="width: 5%;">pH</th>
                            <th style="width: 5%;">% NaCl</th>
                            <th style="width: 6%;">Viskositas (ps)</th>
                            <th style="width: 6%;">Organo</th>
                            <th style="width: 6%;">Aroma</th>
                            <th style="width: 6%;">Warna</th>
                            <th style="width: 5%;">Buih</th>
                            <th style="width: 6%;">Aw</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($block['rows']))
                            @foreach($block['rows'] as $row)
                            <tr>
                                <td>{{ $row['sampling_ke'] ?? '' }}</td>
                                <td>{{ $row['vol_tangki'] ?? '' }}</td>
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
                                <td>{{ $row['buih'] ?? '' }}</td>
                                <td>{{ $row['aw'] ?? '' }}</td>
                                <td class="text-start">{{ $row['waktu_adjustment'] ?? '' }}</td>
                                <td>{{ $row['disposisi'] ?? '' }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @endforeach
        @endif

        <!-- Footer Catatan & Tanda Tangan -->
        <table class="w-100 sign-container">
            <tr>
                <td width="40%" class="p-2 align-top border-end">
                    <strong>Catatan :</strong>
                    <div style="min-height: 50px; font-size: 8pt;" class="mt-1">
                        {{ $data['catatan'] ?? '-' }}
                    </div>
                </td>
                <td width="20%" class="sign-box">
                    <div>Disampling oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_sampling'] ?: '________________' }} )</div>
                    <div class="text-muted" style="font-size: 7.5pt;">Produksi</div>
                </td>
                <td width="20%" class="sign-box">
                    <div>Dianalisis oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_analis'] ?: '________________' }} )</div>
                    <div class="text-muted" style="font-size: 7.5pt;">QC Analis</div>
                </td>
                <td width="20%" class="sign-box">
                    <div>Dicek oleh,</div>
                    <div class="sign-space"></div>
                    <div class="fw-bold">( {{ $data['pic_checker'] ?: '________________' }} )</div>
                    <div class="text-muted" style="font-size: 7.5pt;">Staff/SPV/MNG QC</div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-end pe-2 py-1" style="font-size: 7.5pt; font-style: italic; border-top: 1px solid #000;">
                    FRM/QLB/04/104/005-01
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
