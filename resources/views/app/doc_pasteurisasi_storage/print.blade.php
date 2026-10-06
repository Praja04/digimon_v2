<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PASTEURISASI DAN STORAGE TANK - {{ $data['po_number'] ?? '' }}</title>
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
            font-size: 11pt;
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

        .sign-box {
            text-align: center;
            font-size: 8.5pt;
            border: 1px solid #000;
            padding: 6px;
        }

        .sign-space {
            height: 40px;
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
        <!-- Header Table -->
        <table class="table-doc mb-2">
            <tr>
                <td width="20%" class="text-center p-2" style="border: 1px solid #000;">
                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 40px; object-fit: contain;">
                </td>
                <td width="55%" class="text-center align-middle" style="border: 1px solid #000;">
                    <div class="doc-title">HASIL ANALISIS PASTEURISASI DAN STORAGE TANK</div>
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
                <td width="15%" class="fw-bold">Jenis Produk</td>
                <td width="35%">: {{ $data['jenis_produk'] ?? ($data['variant'] ?? '-') }}</td>
                <td width="18%"></td>
                <td width="32%"></td>
            </tr>
            <tr>
                <td class="fw-bold">Tanggal Produksi</td>
                <td>: {{ $data['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
                <td class="fw-bold">Kode Shift & Grup</td>
                <td>: {{ $data['kode_shift_grup'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Jam Produksi</td>
                <td>: {{ ($data['jam_produksi_start'] || $data['jam_produksi_end']) ? ($data['jam_produksi_start'] . ' s/d ' . $data['jam_produksi_end']) : '_____ s/d _____' }}</td>
                <td class="fw-bold">Batch</td>
                <td>: {{ $data['batch'] ?? ($data['batch_range'] ?? '-') }}</td>
            </tr>
        </table>

        <!-- TABEL 1: PASTEURISASI -->
        <div class="fw-bold small mb-1">PASTEURISASI</div>
        <table class="table-doc">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 70px;">Sampling</th>
                    <th colspan="2">Serah Terima</th>
                    <th colspan="10">Analisis</th>
                    <th rowspan="2" style="min-width: 120px;">Disposisi / Keterangan</th>
                </tr>
                <tr>
                    <th style="width: 50px;">Jam</th>
                    <th style="width: 75px;">PIC</th>
                    <th style="width: 65px;">BJ (g/mL)</th>
                    <th style="width: 50px;">Brix</th>
                    <th style="width: 45px;">pH</th>
                    <th style="width: 45px;">Aw</th>
                    <th style="width: 75px;">Viskositas (ps)</th>
                    <th style="width: 60px;">Organo</th>
                    <th style="width: 60px;">Aroma</th>
                    <th style="width: 65px;">Warna</th>
                    <th style="width: 50px;">Buih</th>
                    <th style="width: 65px;">Endapan</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data['pasteurisasi_rows']))
                    @foreach($data['pasteurisasi_rows'] as $idx => $row)
                    <tr>
                        <td>{{ $row['sampling'] ?? ('Sampling ' . ($idx + 1)) }}</td>
                        <td>{{ $row['jam'] ?? '' }}</td>
                        <td>{{ $row['pic'] ?? '' }}</td>
                        <td>{{ $row['bj'] ?? '' }}</td>
                        <td>{{ $row['brix'] ?? '' }}</td>
                        <td>{{ $row['ph'] ?? '' }}</td>
                        <td>{{ $row['aw'] ?? '' }}</td>
                        <td>{{ $row['viskositas'] ?? '' }}</td>
                        <td>{{ $row['organo'] ?? '' }}</td>
                        <td>{{ $row['aroma'] ?? '' }}</td>
                        <td>{{ $row['warna'] ?? '' }}</td>
                        <td>{{ $row['buih'] ?? '' }}</td>
                        <td>{{ $row['endapan'] ?? '' }}</td>
                        <td class="text-start">{{ $row['disposisi'] ?? '' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="14">- Tidak Ada Data -</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- TABEL 2: STORAGE TANK -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-bold small">STORAGE TANK</span>
            <span class="fw-bold small">KODE ST : {{ $data['kode_st'] ?? 'ST 01' }}</span>
        </div>
        <table class="table-doc">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 70px;">Sampling</th>
                    <th colspan="2">Serah Terima</th>
                    <th colspan="12">Analisis</th>
                    <th rowspan="2" style="min-width: 120px;">Disposisi / Keterangan</th>
                </tr>
                <tr>
                    <th style="width: 50px;">Jam</th>
                    <th style="width: 75px;">PIC</th>
                    <th style="width: 55px;">BJ</th>
                    <th style="width: 50px;">Brix</th>
                    <th style="width: 45px;">pH</th>
                    <th style="width: 55px;">% NaCl</th>
                    <th style="width: 75px;">Viskositas (ps)</th>
                    <th style="width: 60px;">Organo</th>
                    <th style="width: 60px;">Aroma</th>
                    <th style="width: 65px;">Warna</th>
                    <th style="width: 50px;">Buih</th>
                    <th style="width: 65px;">Endapan</th>
                    <th style="width: 60px;">Kristal</th>
                    <th style="width: 45px;">Aw</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data['storage_rows']))
                    @foreach($data['storage_rows'] as $idx => $row)
                    <tr>
                        <td>{{ $row['sampling'] ?? ('Sampling ' . ($idx + 1)) }}</td>
                        <td>{{ $row['jam'] ?? '' }}</td>
                        <td>{{ $row['pic'] ?? '' }}</td>
                        <td>{{ $row['bj'] ?? '' }}</td>
                        <td>{{ $row['brix'] ?? '' }}</td>
                        <td>{{ $row['ph'] ?? '' }}</td>
                        <td>{{ $row['nacl'] ?? '' }}</td>
                        <td>{{ $row['viskositas'] ?? '' }}</td>
                        <td>{{ $row['organo'] ?? '' }}</td>
                        <td>{{ $row['aroma'] ?? '' }}</td>
                        <td>{{ $row['warna'] ?? '' }}</td>
                        <td>{{ $row['buih'] ?? '' }}</td>
                        <td>{{ $row['endapan'] ?? '' }}</td>
                        <td>{{ $row['kristal'] ?? '' }}</td>
                        <td>{{ $row['aw'] ?? '' }}</td>
                        <td class="text-start">{{ $row['disposisi'] ?? '' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="16">- Tidak Ada Data -</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Catatan & Signatures -->
        <table class="w-100 mt-2">
            <tr>
                <td width="30%" class="align-top pe-2">
                    <div class="border p-2" style="border: 1px solid #000 !important; min-height: 95px;">
                        <div class="fw-bold small mb-1">Catatan :</div>
                        <div class="small" style="white-space: pre-wrap;">{{ $data['catatan'] ?? '-' }}</div>
                    </div>
                </td>
                <td width="70%" class="align-top">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="text-center fw-bold small p-1" style="border: 1px solid #000; border-bottom: none; background: #f2f2f2;">Disampling oleh,</td>
                            <td class="text-center fw-bold small p-1" style="border: 1px solid #000; border-bottom: none; background: #f2f2f2;">Dianalisis oleh,</td>
                            <td class="text-center fw-bold small p-1" style="border: 1px solid #000; border-bottom: none; background: #f2f2f2;">Dicek oleh,</td>
                        </tr>
                        <tr>
                            <td width="25%" class="sign-box" style="border-top: none;">
                                <div class="sign-space"></div>
                                <div>( {{ $data['pic_sampling'] ?: '________________' }} )</div>
                                <div class="fw-bold">Produksi</div>
                            </td>
                            <td width="25%" class="sign-box" style="border-top: none;">
                                <div class="sign-space"></div>
                                <div>( {{ $data['pic_serah_terima'] ?: '________________' }} )</div>
                                <div class="fw-bold">Produksi</div>
                            </td>
                            <td width="25%" class="sign-box" style="border-top: none;">
                                <div class="sign-space"></div>
                                <div>( {{ $data['pic_analis'] ?: '________________' }} )</div>
                                <div class="fw-bold">QC Analis</div>
                            </td>
                            <td width="25%" class="sign-box" style="border-top: none;">
                                <div class="sign-space"></div>
                                <div>( {{ $data['pic_checker'] ?: '________________' }} )</div>
                                <div class="fw-bold">SPV/MNG QC</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end pt-1" style="font-size: 7.5pt; font-style: italic;">
                                {{ $data['doc_code'] ?? 'FRM/QLB/04/104/006-01' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
