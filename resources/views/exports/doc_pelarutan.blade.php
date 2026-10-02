<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <!-- Header Dokumen Sesuai Format Form Web -->
        <tr>
            <td colspan="3" rowspan="2" style="border: 2px solid #2d3748; background-color: #f8fafc; text-align: center; vertical-align: middle;">
                <span style="font-weight: bold; font-size: 14pt; color: #dc2626;">BAS</span><br>
                <span style="font-size: 8pt; color: #475569;">PT. Bumi Alam Segar</span>
            </td>
            <td colspan="10" rowspan="2" style="font-weight: bold; font-size: 13pt; text-align: center; vertical-align: middle; color: #1e293b; letter-spacing: 0.5px;">
                HASIL ANALISIS PROSES PELARUTAN
            </td>
            <td colspan="2" style="font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000; background-color: #f1f5f9;">
                Tanggal Record Doc
            </td>
            <td colspan="2" style="font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                : {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000; background-color: #f1f5f9;">
                Halaman
            </td>
            <td colspan="2" style="font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                : {{ $data['halaman'] ?? '1' }}
            </td>
        </tr>
        <tr>
            <td colspan="17" style="height: 10px;"></td>
        </tr>

        @if(!empty($data['batch_blocks']))
            @foreach($data['batch_blocks'] as $bIdx => $block)
            <!-- Header Kartu Blok Dissolver / Batch -->
            <tr>
                <td colspan="17" style="font-weight: bold; font-size: 10pt; background-color: #e2e8f0; color: #1e293b; border: 1px solid #000; padding: 5px;">
                    Tangki / Dissolver #{{ $bIdx + 1 }}
                </td>
            </tr>

            <!-- Metadata Blok Batch -->
            <tr>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Jenis Produk</td>
                <td colspan="5" style="font-size: 9pt;">: {{ $block['jenis_produk'] ?? '-' }}</td>
                <td colspan="2"></td>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Batch</td>
                <td colspan="4" style="font-size: 9pt;">: {{ $block['batch'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Tanggal Produksi</td>
                <td colspan="5" style="font-size: 9pt;">: {{ $block['tanggal_produksi'] ?? '-' }}</td>
                <td colspan="2"></td>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">No. Dissolver</td>
                <td colspan="4" style="font-size: 9pt;">: {{ $block['no_dissolver'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Jam Produksi</td>
                <td colspan="5" style="font-size: 9pt;">: {{ $block['jam_produksi'] ?? '-' }}</td>
                <td colspan="2"></td>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Volume</td>
                <td colspan="4" style="font-size: 9pt;">: {{ $block['volume'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; font-size: 9pt;">Kode Shift &amp; Grup</td>
                <td colspan="5" style="font-size: 9pt;">: {{ $block['kode_shift_grup'] ?? '-' }}</td>
                <td colspan="9"></td>
            </tr>
            <tr>
                <td colspan="17" style="height: 5px;"></td>
            </tr>

            <!-- Tabel Header Blok (2 Tingkat Sesuai Web Form) -->
            <tr>
                <th rowspan="2" style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle;">Sampling ke-</th>
                <th colspan="2" style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Serah Terima</th>
                <th colspan="5" style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Pelarutan I (GGA)</th>
                <th rowspan="2" style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle;">Sampling ke-</th>
                <th colspan="2" style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Serah Terima</th>
                <th colspan="5" style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Pelarutan II (GGAS)</th>
                <th rowspan="2" style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle;">Disposisi</th>
            </tr>
            <tr>
                <!-- Serah Terima P1 -->
                <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Jam</th>
                <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">PIC</th>
                <!-- P1 Analisis -->
                <th style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Brix</th>
                <th style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">%NaCl</th>
                <th style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Warna</th>
                <th style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Organo</th>
                <th style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Waktu &amp; Adjustment</th>
                <!-- Serah Terima P2 -->
                <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Jam</th>
                <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">PIC</th>
                <!-- P2 Analisis -->
                <th style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Brix</th>
                <th style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">%NaCl</th>
                <th style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Warna</th>
                <th style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Organo</th>
                <th style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Waktu &amp; Adjustment</th>
            </tr>

            @php
                $rows = $block['rows'] ?? [];
                $rowCount = count($rows);
                $minRows = max(4, $rowCount);
            @endphp

            @for($i = 0; $i < $minRows; $i++)
                @php
                    $row = $rows[$i] ?? null;
                @endphp
                <tr>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_sampling_ke'] ?? ($i + 1) }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_jam'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_pic'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_brix'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_nacl'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_warna'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p1_organo'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: left; font-size: 9pt; vertical-align: middle;">{{ $row['p1_waktu_adjustment'] ?? '' }}</td>

                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_sampling_ke'] ?? ($i + 1) }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_jam'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_pic'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_brix'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_nacl'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_warna'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['p2_organo'] ?? '' }}</td>
                    <td style="border: 1px solid #000; text-align: left; font-size: 9pt; vertical-align: middle;">{{ $row['p2_waktu_adjustment'] ?? '' }}</td>

                    <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['disposisi'] ?? 'Release' }}</td>
                </tr>
            @endfor

            <tr>
                <td colspan="17" style="height: 12px;"></td>
            </tr>
            @endforeach
        @endif

        <!-- Catatan & Signatures Sesuai Standar Form -->
        <tr>
            <td colspan="5" style="font-weight: bold; font-size: 9pt; vertical-align: top; border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Catatan :
            </td>
            <td colspan="4" style="font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Disampling oleh,
            </td>
            <td colspan="4" style="font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Dianalisis oleh,
            </td>
            <td colspan="4" style="font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Dicek oleh,
            </td>
        </tr>
        <tr>
            <td colspan="5" rowspan="2" style="vertical-align: top; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-size: 9pt; padding: 6px;">
                {{ $data['catatan'] ?? '' }}
            </td>
            <td colspan="4" style="border-right: 1px solid #000; height: 45px;"></td>
            <td colspan="4" style="border-right: 1px solid #000; height: 45px;"></td>
            <td colspan="4" style="border-right: 1px solid #000; height: 45px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ( {{ $data['pic_sampling'] ?: '________________' }} )
            </td>
            <td colspan="4" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ( {{ $data['pic_analis'] ?: '________________' }} )
            </td>
            <td colspan="4" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ( {{ $data['pic_checker'] ?: '________________' }} )
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="4" style="text-align: center; font-size: 8pt; font-weight: bold; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                Produksi
            </td>
            <td colspan="4" style="text-align: center; font-size: 8pt; font-weight: bold; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                QC Analis
            </td>
            <td colspan="4" style="text-align: center; font-size: 8pt; font-weight: bold; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                Staff/SPV/MNG QC
            </td>
        </tr>
        <tr>
            <td colspan="13"></td>
            <td colspan="4" style="font-weight: bold; font-size: 8pt; text-align: right; font-style: italic; color: #475569; padding-top: 5px;">
                FRM/QLB/04/104/004-01
            </td>
        </tr>
    </table>
</body>
</html>
