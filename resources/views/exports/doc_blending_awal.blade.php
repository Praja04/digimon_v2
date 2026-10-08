<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <!-- Row 1-4: Header Dokumen (Sama Persis Google Spreadsheet Form QLB) -->
        <tr>
            <td colspan="4" rowspan="4" style="border: 1px solid #000000; text-align: center; vertical-align: middle; padding: 6px;">
                <span style="font-weight: bold; font-size: 16pt; color: #cc0000; letter-spacing: 1px;">BAS</span><br>
                <span style="font-size: 8pt; color: #333333;">PT. Bumi Alam Segar</span>
            </td>
            <td colspan="10" rowspan="4" style="border: 1px solid #000000; font-weight: bold; font-size: 13pt; text-align: center; vertical-align: middle; color: #000000; letter-spacing: 0.5px;">
                HASIL ANALISIS PROSES BLENDING
            </td>
            <td colspan="2" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; font-size: 8.5pt; font-weight: bold; vertical-align: middle; padding-left: 4px;">
                Tanggal Record Doc
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size: 8.5pt; vertical-align: middle; padding-left: 4px;">
                : {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-left: 1px solid #000000; border-right: 1px solid #000000; font-size: 8.5pt; font-weight: bold; vertical-align: middle; padding-left: 4px;">
                Halaman
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size: 8.5pt; vertical-align: middle; padding-left: 4px;">
                : {{ $data['halaman'] ?? '1' }}
            </td>
        </tr>

        <!-- Row 5: Blank Row -->
        <tr>
            <td colspan="16" style="height: 12px;"></td>
        </tr>

        @php
            $blocks = !empty($data['batch_blocks']) ? $data['batch_blocks'] : [[]];
        @endphp

        @foreach($blocks as $bIdx => $block)
            @if($bIdx > 0)
                <tr>
                    <td colspan="16" style="height: 18px;"></td>
                </tr>
            @endif

            <!-- Row 6-9: Metadata Sesuai Google Spreadsheet Form QLB -->
            <tr>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Jenis Produk</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['jenis_produk'] ?? '-' }}</td>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Batch</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['batch'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Tanggal Produksi</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['tanggal_produksi'] ?? '-' }}</td>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">No. Blending</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['no_blending'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Jam Produksi</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['jam_produksi'] ?? '-' }}</td>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Volume awal</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['volume_awal'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 9pt; font-weight: bold; vertical-align: middle;">Kode Shift &amp; Grup</td>
                <td colspan="6" style="font-size: 9pt; vertical-align: middle;">: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                <td colspan="8"></td>
            </tr>

            <!-- Row 10: Blank Row -->
            <tr>
                <td colspan="16" style="height: 10px;"></td>
            </tr>

            <!-- Row 11-12: Header Tabel Grid (Sesuai Form Spreadsheet) -->
            <tr>
                <th rowspan="2" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Sampling ke-</th>
                <th rowspan="2" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Vol Tangki (L)</th>
                <th colspan="2" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Serah Terima</th>
                <th colspan="10" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Analisis</th>
                <th rowspan="2" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Waktu &amp; Adjustment</th>
                <th rowspan="2" style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Disposisi/<br>Keterangan</th>
            </tr>
            <tr>
                <!-- Serah Terima Subheaders -->
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Jam</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">PIC</th>
                <!-- Analisis 10 Subheaders -->
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">BJ (g/mL)</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Brix</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">pH</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">% NaCl</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Viskositas (ps)</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Organo</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Aroma</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Warna</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Buih</th>
                <th style="border: 1px solid #000000; font-size: 8.5pt; font-weight: bold; text-align: center; vertical-align: middle;">Aw</th>
            </tr>

            <!-- Rows 13+: Data Baris Tabel (Minimal 11 Baris Sesuai Spreadsheet) -->
            @php
                $rows = $block['rows'] ?? [];
                $rowCount = count($rows);
                $totalRows = max(11, $rowCount);
            @endphp

            @for($i = 0; $i < $totalRows; $i++)
                @php
                    $row = $rows[$i] ?? null;
                    $displaySamp = $row['sampling_ke'] ?? '';
                    $displayVol = $row['vol_tangki'] ?? '';
                    $displayAdj = $row['waktu_adjustment'] ?? '';
                    $displayDisp = $row['disposisi'] ?? '';
                @endphp
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $displaySamp }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle; font-weight: {{ strtolower(trim((string)$displayVol)) === 'awal' ? 'bold' : 'normal' }};">{{ $displayVol }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['serah_terima_jam'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['serah_terima_pic'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['bj'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['brix'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['ph'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['nacl'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['visco'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['organo'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['aroma'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['warna'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle; white-space: pre-wrap;">{{ (isset($row['buih']) && (strtolower(trim($row['buih'])) === 'tidak ada' || strtolower(trim($row['buih'])) === "tidak\nada")) ? "Tidak\nAda" : ($row['buih'] ?? '') }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle;">{{ $row['aw'] ?? '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle; white-space: pre-wrap;">{!! nl2br(e($displayAdj)) !!}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-size: 8.5pt; vertical-align: middle; white-space: pre-wrap;">{!! nl2br(e($displayDisp)) !!}</td>
                </tr>
            @endfor
        @endforeach

        <!-- Row Blank -->
        <tr>
            <td colspan="16" style="height: 12px;"></td>
        </tr>

        <!-- Footer: Catatan & Signatures (Sesuai Persis Baris 25-30 Form Spreadsheet) -->
        <tr>
            <td colspan="5" style="font-weight: bold; font-size: 8.5pt; vertical-align: top; border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; padding: 4px;">
                Catatan :
            </td>
            <td colspan="4" style="font-size: 8.5pt; text-align: left; vertical-align: top; border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; padding: 4px;">
                Disampling oleh,
            </td>
            <td colspan="3" style="font-size: 8.5pt; text-align: left; vertical-align: top; border-top: 1px solid #000000; border-right: 1px solid #000000; padding: 4px;">
                Dianalisis oleh,
            </td>
            <td colspan="4" style="font-size: 8.5pt; text-align: left; vertical-align: top; border-top: 1px solid #000000; border-right: 1px solid #000000; padding: 4px;">
                Dicek oleh,
            </td>
        </tr>
        <tr>
            <td colspan="5" rowspan="2" style="vertical-align: top; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size: 8.5pt; padding: 6px;">
                {{ $data['catatan'] ?? '' }}
            </td>
            <td colspan="4" style="border-left: 1px solid #000000; border-right: 1px solid #000000; height: 35px;"></td>
            <td colspan="3" style="border-right: 1px solid #000000; height: 35px;"></td>
            <td colspan="4" style="border-right: 1px solid #000000; height: 35px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 8.5pt; border-left: 1px solid #000000; border-right: 1px solid #000000;">
                ( {{ $data['pic_sampling'] ?: '                        ' }} )
            </td>
            <td colspan="3" style="text-align: center; font-size: 8.5pt; border-right: 1px solid #000000;">
                ( {{ $data['pic_analis'] ?: '     QC Analis     ' }} )
            </td>
            <td colspan="4" style="text-align: center; font-size: 8.5pt; border-right: 1px solid #000000;">
                ( {{ $data['pic_checker'] ?: '                        ' }} )
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="4" style="text-align: center; font-size: 8pt; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000; padding-bottom: 3px;">
                Produksi
            </td>
            <td colspan="3" style="text-align: center; font-size: 8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000; padding-bottom: 3px;">
                QC Analis
            </td>
            <td colspan="4" style="text-align: center; font-size: 8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000; padding-bottom: 3px;">
                Staff/SPV/MNG QC
            </td>
        </tr>
        <tr>
            <td colspan="12"></td>
            <td colspan="4" style="font-size: 8pt; text-align: right; color: #000000; padding-top: 4px;">
                FRM/QLB/04/104/005-01
            </td>
        </tr>
    </table>
</body>
</html>
