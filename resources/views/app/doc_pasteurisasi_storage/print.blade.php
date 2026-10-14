<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL ANALISIS PASTEURISASI DAN STORAGE TANK - {{ $data['po_number'] ?? '' }}</title>
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
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
            font-size: 8.2pt;
            margin-bottom: 8px;
        }

        .table-doc th, .table-doc td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }

        .table-doc th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        .section-header-cell {
            background-color: #e9ecef !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            font-size: 9pt;
            text-align: left;
            padding: 4px 8px !important;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: middle;
        }

        .sign-box {
            text-align: center;
            font-size: 8pt;
            border: 1px solid #000;
            padding: 4px;
            background: #fff;
        }

        .sign-space {
            height: 38px;
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
    <!-- Tombol Aksi Layar Non-Print (Persis seperti Blending) -->
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-1">
            <i class="ri-printer-line"></i> Cetak / Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    @php
        $blocks = !empty($data['batch_blocks']) ? $data['batch_blocks'] : [
            [
                'block_index' => 0,
                'halaman' => '1 / 1',
                'batch' => $data['batch'] ?? '1-2',
                'kode_st' => $data['kode_st'] ?? 'ST 01',
                'jenis_produk' => $data['jenis_produk'] ?? ($data['variant'] ?? ''),
                'tanggal_produksi' => $data['tanggal_record_doc'] ?? date('Y-m-d'),
                'jam_produksi_start' => '',
                'jam_produksi_end' => '',
                'kode_shift_grup' => $data['kode_shift_grup'] ?? '',
                'pasteurisasi_rows' => $data['pasteurisasi_rows'] ?? [],
                'storage_rows' => $data['storage_rows'] ?? [],
                'catatan' => $data['catatan'] ?? '',
                'pic_sampling_1' => $data['pic_sampling'] ?? '',
                'pic_sampling_2' => $data['pic_serah_terima'] ?? '',
                'pic_analis' => $data['pic_analis'] ?? '',
                'pic_checker' => $data['pic_checker'] ?? '',
            ]
        ];
        $totalBlocks = count($blocks);
    @endphp

    @foreach($blocks as $bIdx => $block)
        @php
            $pastRows = $block['pasteurisasi_rows'] ?? [];
            $totalPast = max(6, count($pastRows));
            $stRows = $block['storage_rows'] ?? [];
            $totalSt = max(5, count($stRows));
            $halamanStr = $block['halaman'] ?? (($bIdx + 1) . ' / ' . $totalBlocks);
        @endphp

        <div class="sheet-page">
            <!-- 1. Header Dokumen Resmi -->
            <table class="table-doc mb-0">
                <tr>
                    <td style="width: 18%; text-align: center; padding: 4px;">
                        <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                    </td>
                    <td style="width: 54%; text-align: center; vertical-align: middle;">
                        <div class="doc-title">HASIL ANALISIS PASTEURISASI DAN STORAGE TANK</div>
                    </td>
                    <td style="width: 28%; padding: 0;">
                        <table class="w-100 h-100" style="border-collapse: collapse; font-size: 8pt;">
                            <tr style="border-bottom: 1px solid #000;">
                                <td class="px-2 py-1 fw-bold text-start" style="width: 55%; border: none; border-right: 1px solid #000; background: #fafafa;">Tanggal Record Doc</td>
                                <td class="px-2 py-1 text-start" style="width: 45%; border: none;">: {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1 fw-bold text-start" style="border: none; border-right: 1px solid #000; background: #fafafa;">Halaman</td>
                                <td class="px-2 py-1 text-start fw-semibold" style="border: none;">: {{ $halamanStr }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Notes Kanan Atas Sesuai Google Sheets -->
            <div class="d-flex justify-content-end mb-1 mt-1">
                <span class="text-muted fst-italic" style="font-size: 7.5pt;">Notes: 1 lembar: 1 blending (1 pasangan batch)</span>
            </div>

            <!-- 2. Metadata Grid -->
            <table class="meta-table mb-2">
                <tr>
                    <td style="width: 14%; font-weight: bold; background: #fafafa;">Jenis Produk</td>
                    <td colspan="3" style="width: 86%;">: {{ $block['jenis_produk'] ?? ($data['jenis_produk'] ?? ($data['variant'] ?? '-')) }}</td>
                </tr>
                <tr>
                    <td style="width: 14%; font-weight: bold; background: #fafafa;">Tanggal Produksi</td>
                    <td style="width: 36%;">: {{ $block['tanggal_produksi'] ?? ($data['tanggal_record_doc'] ?? '-') }}</td>
                    <td style="width: 16%; font-weight: bold; background: #fafafa;">Kode Shift & Grup</td>
                    <td style="width: 34%;">: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #fafafa;">Jam Produksi</td>
                    <td>
                        : {{ !empty($block['jam_produksi']) && $block['jam_produksi'] !== '-' ? $block['jam_produksi'] : ((($block['jam_produksi_start'] ?? '') || ($block['jam_produksi_end'] ?? '')) ? ($block['jam_produksi_start'] ?? '') . ' s/d ' . ($block['jam_produksi_end'] ?? '') : '_____ s/d _____') }}
                    </td>
                    <td style="font-weight: bold; background: #fafafa;">Batch</td>
                    <td>: {{ $block['batch'] ?? '-' }}</td>
                </tr>
            </table>

            <!-- 3. Tabel 1: PASTEURISASI -->
            <table class="table-doc mb-2">
                <thead>
                    <tr>
                        <td colspan="14" class="section-header-cell">PASTEURISASI</td>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 12%;">Sampling</th>
                        <th colspan="2" style="width: 15%;">Serah Terima</th>
                        <th colspan="10" style="width: 55%;">Analisis</th>
                        <th rowspan="2" style="width: 18%;">Disposisi /<br>Keterangan<br><span style="font-size: 7pt; font-weight: normal;">(Disposisi After Cooling)</span></th>
                    </tr>
                    <tr>
                        <th style="width: 6%;">Jam</th>
                        <th style="width: 9%;">PIC</th>
                        <th style="width: 6%;">BJ<br>(g/mL)</th>
                        <th style="width: 5%;">Brix</th>
                        <th style="width: 4%;">pH</th>
                        <th style="width: 5%;">Aw</th>
                        <th style="width: 7%;">Viskositas<br>(ps)</th>
                        <th style="width: 5%;">Organo</th>
                        <th style="width: 5%;">Aroma</th>
                        <th style="width: 6%;">Warna</th>
                        <th style="width: 5%;">Buih</th>
                        <th style="width: 6%;">Endapan</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $totalPast; $i++)
                        @php $pRow = $pastRows[$i] ?? null; @endphp
                        <tr>
                            <td>{{ $pRow['sampling'] ?? '' }}</td>
                            <td>{{ $pRow['jam'] ?? '' }}</td>
                            <td>{{ $pRow['pic'] ?? '' }}</td>
                            <td>{{ $pRow['bj'] ?? '' }}</td>
                            <td>{{ $pRow['brix'] ?? '' }}</td>
                            <td>{{ $pRow['ph'] ?? '' }}</td>
                            <td>{{ $pRow['aw'] ?? '' }}</td>
                            <td>{{ $pRow['viskositas'] ?? '' }}</td>
                            <td>{{ $pRow['organo'] ?? '' }}</td>
                            <td>{{ $pRow['aroma'] ?? '' }}</td>
                            <td>{{ $pRow['warna'] ?? '' }}</td>
                            <td>{{ $pRow['buih'] ?? '' }}</td>
                            <td>{{ $pRow['endapan'] ?? '' }}</td>
                            <td class="text-start px-2">{{ $pRow['disposisi'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- 4. Tabel 2: STORAGE TANK -->
            <table class="table-doc mb-2">
                <thead>
                    <tr>
                        <td colspan="4" class="section-header-cell">STORAGE TANK</td>
                        <td colspan="12" class="section-header-cell text-start" style="border-left: none;">
                            KODE ST : <span class="fw-bold">{{ $block['kode_st'] ?? 'ST 01' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 10%;">Sampling</th>
                        <th colspan="2" style="width: 13%;">Serah Terima</th>
                        <th colspan="12" style="width: 61%;">Analisis</th>
                        <th rowspan="2" style="width: 16%;">Disposisi /<br>Keterangan</th>
                    </tr>
                    <tr>
                        <th style="width: 5%;">Jam</th>
                        <th style="width: 8%;">PIC</th>
                        <th style="width: 5%;">BJ</th>
                        <th style="width: 5%;">Brix</th>
                        <th style="width: 4%;">pH</th>
                        <th style="width: 5%;">% NaCl</th>
                        <th style="width: 7%;">Viskositas<br>(ps)</th>
                        <th style="width: 5%;">Organo</th>
                        <th style="width: 5%;">Aroma</th>
                        <th style="width: 6%;">Warna</th>
                        <th style="width: 5%;">Buih</th>
                        <th style="width: 5%;">Endapan</th>
                        <th style="width: 5%;">Kristal</th>
                        <th style="width: 4%;">Aw</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $totalSt; $i++)
                        @php $sRow = $stRows[$i] ?? null; @endphp
                        <tr>
                            <td>{{ $sRow['sampling'] ?? ($i === 0 ? 'Awal' : ($i === 1 ? 'Outlet' : '')) }}</td>
                            <td>{{ $sRow['jam'] ?? '' }}</td>
                            <td>{{ $sRow['pic'] ?? '' }}</td>
                            <td>{{ $sRow['bj'] ?? '' }}</td>
                            <td>{{ $sRow['brix'] ?? '' }}</td>
                            <td>{{ $sRow['ph'] ?? '' }}</td>
                            <td>{{ $sRow['nacl'] ?? '' }}</td>
                            <td>{{ $sRow['viskositas'] ?? '' }}</td>
                            <td>{{ $sRow['organo'] ?? '' }}</td>
                            <td>{{ $sRow['aroma'] ?? '' }}</td>
                            <td>{{ $sRow['warna'] ?? '' }}</td>
                            <td>{{ $sRow['buih'] ?? '' }}</td>
                            <td>{{ $sRow['endapan'] ?? '' }}</td>
                            <td>{{ $sRow['kristal'] ?? '' }}</td>
                            <td>{{ $sRow['aw'] ?? '' }}</td>
                            <td class="text-start px-2">{{ $sRow['disposisi'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- 5. Footer: Catatan & Tanda Tangan -->
            <div class="row g-2 pt-1">
                <div class="col-5">
                    <div class="p-2 border rounded bg-light h-100" style="border: 1px solid #000 !important; min-height: 85px;">
                        <span class="fw-bold" style="font-size: 8pt;">Catatan :</span>
                        <div class="mt-1" style="font-size: 7.8pt; white-space: pre-wrap;">{{ $block['catatan'] ?? ($data['catatan'] ?? '-') }}</div>
                    </div>
                </div>
                <div class="col-7">
                    <div class="row g-1 text-center">
                        <div class="col-3">
                            <div class="sign-box">
                                <div class="fw-bold" style="font-size: 7.5pt;">Disampling oleh,</div>
                                <div class="sign-space"></div>
                                <div class="border-top pt-1 fw-bold" style="font-size: 7.5pt;">
                                    ({{ $block['pic_sampling_1'] ?? ($data['pic_sampling'] ?? '________________') }})
                                    <div class="text-muted fw-normal" style="font-size: 7pt;">Produksi</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="sign-box">
                                <div class="fw-bold" style="font-size: 7.5pt;">Disampling oleh,</div>
                                <div class="sign-space"></div>
                                <div class="border-top pt-1 fw-bold" style="font-size: 7.5pt;">
                                    ({{ $block['pic_sampling_2'] ?? ($data['pic_serah_terima'] ?? '________________') }})
                                    <div class="text-muted fw-normal" style="font-size: 7pt;">Produksi</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="sign-box">
                                <div class="fw-bold" style="font-size: 7.5pt;">Dianalisis oleh,</div>
                                <div class="sign-space"></div>
                                <div class="border-top pt-1 fw-bold" style="font-size: 7.5pt;">
                                    ({{ $block['pic_analis'] ?? ($data['pic_analis'] ?? '________________') }})
                                    <div class="text-muted fw-normal" style="font-size: 7pt;">QC Analis</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="sign-box">
                                <div class="fw-bold" style="font-size: 7.5pt;">Dicek oleh,</div>
                                <div class="sign-space"></div>
                                <div class="border-top pt-1 fw-bold" style="font-size: 7.5pt;">
                                    ({{ $block['pic_checker'] ?? ($data['pic_checker'] ?? '________________') }})
                                    <div class="text-muted fw-normal" style="font-size: 7pt;">SPV/MNG QC</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-1">
                        <span class="fw-bold" style="font-size: 8pt;">{{ $data['doc_code'] ?? 'FRM/QLB/04/104/006-01' }}</span>
                    </div>
                </div>
            </div>

        </div>
    @endforeach
</body>
</html>
