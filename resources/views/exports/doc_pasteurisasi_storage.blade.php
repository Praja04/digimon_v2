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
            <td colspan="8" rowspan="2" style="font-weight: bold; font-size: 13pt; text-align: center; vertical-align: middle; color: #1e293b; letter-spacing: 0.5px;">
                HASIL ANALISIS PASTEURISASI DAN STORAGE TANK
            </td>
            <td colspan="2" style="font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000; background-color: #f1f5f9;">
                Tanggal Record Doc
            </td>
            <td style="font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                : {{ $data['tanggal_record_doc'] ?? date('Y-m-d') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000; background-color: #f1f5f9;">
                Halaman
            </td>
            <td style="font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                : {{ $data['halaman'] ?? '1' }}
            </td>
        </tr>
        <tr>
            <td colspan="14" style="height: 10px;"></td>
        </tr>

        <!-- Metadata Dokumen -->
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 9pt;">No. PO</td>
            <td colspan="5" style="font-size: 9pt;">: {{ $data['po_number'] ?? '-' }}</td>
            <td colspan="2" style="font-weight: bold; font-size: 9pt;">Kode Shift &amp; Grup</td>
            <td colspan="5" style="font-size: 9pt;">: {{ $data['kode_shift_grup'] ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 9pt;">Produk / Varian</td>
            <td colspan="5" style="font-size: 9pt;">: {{ $data['variant'] ?? '-' }}</td>
            <td colspan="2" style="font-weight: bold; font-size: 9pt;">Batch Range</td>
            <td colspan="5" style="font-size: 9pt;">: {{ $data['batch_range'] ?? '_____ s/d _____' }}</td>
        </tr>
        <tr>
            <td colspan="14" style="height: 8px;"></td>
        </tr>

        <!-- TABEL 1: PASTEURISASI -->
        <tr>
            <td colspan="14" style="font-weight: bold; font-size: 10pt; background-color: #e2e8f0; color: #1e293b; border: 1px solid #000; padding: 4px 8px;">
                Proses Pasteurisasi
            </td>
        </tr>
        <tr>
            <th colspan="2" style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center; height: 22px;">Serah Terima</th>
            <th colspan="10" style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center;">Analisis Pasteurisasi</th>
            <th colspan="2" rowspan="2" style="border: 1px solid #000; background-color: #e2efda; font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle;">Disposisi / Keterangan</th>
        </tr>
        <tr>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Jam</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">PIC</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">BJ (g/mL)</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Brix</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">pH</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Aw</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Viskositas (ps)</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Organo</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Aroma</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Warna</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Buih</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Endapan</th>
        </tr>

        @php
            $pastRows = $data['pasteurisasi_rows'] ?? [];
            $pastCount = count($pastRows);
            $minPastRows = max(3, $pastCount);
        @endphp

        @for($i = 0; $i < $minPastRows; $i++)
            @php $row = $pastRows[$i] ?? null; @endphp
            <tr>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['jam'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['pic'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['bj'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['brix'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['ph'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['aw'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['viskositas'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['organo'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['aroma'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['warna'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['buih'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['endapan'] ?? '' }}</td>
                <td colspan="2" style="border: 1px solid #000; text-align: left; font-size: 9pt; vertical-align: middle;">{{ $row['disposisi'] ?? '' }}</td>
            </tr>
        @endfor

        <tr>
            <td colspan="14" style="height: 12px;"></td>
        </tr>

        <!-- TABEL 2: STORAGE TANK -->
        <tr>
            <td colspan="14" style="font-weight: bold; font-size: 10pt; background-color: #e2e8f0; color: #1e293b; border: 1px solid #000; padding: 4px 8px;">
                Storage Tank (KODE ST : {{ $data['kode_st'] ?? 'ST 01' }})
            </td>
        </tr>
        <tr>
            <th colspan="2" style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center; height: 22px;">Serah Terima</th>
            <th colspan="11" style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center;">Analisis Storage Tank</th>
            <th rowspan="2" style="border: 1px solid #000; background-color: #ddebf7; font-weight: bold; font-size: 9pt; text-align: center; vertical-align: middle;">Disposisi / Keterangan</th>
        </tr>
        <tr>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Jam</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">PIC</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">BJ</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Brix</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">pH</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">% NaCl</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Viskositas (ps)</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Organo</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Aroma</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Warna</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Buih</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Endapan</th>
            <th style="border: 1px solid #000; background-color: #f2f2f2; font-weight: bold; font-size: 9pt; text-align: center;">Kristal</th>
        </tr>

        @php
            $stRows = $data['storage_rows'] ?? [];
            $stCount = count($stRows);
            $minStRows = max(3, $stCount);
        @endphp

        @for($i = 0; $i < $minStRows; $i++)
            @php $row = $stRows[$i] ?? null; @endphp
            <tr>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['jam'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['pic'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['bj'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['brix'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['ph'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['nacl'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['viskositas'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['organo'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['aroma'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['warna'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['buih'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['endapan'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: center; font-size: 9pt; vertical-align: middle;">{{ $row['kristal'] ?? '' }}</td>
                <td style="border: 1px solid #000; text-align: left; font-size: 9pt; vertical-align: middle;">{{ $row['disposisi'] ?? '' }}</td>
            </tr>
        @endfor

        <tr>
            <td colspan="14" style="height: 12px;"></td>
        </tr>

        <!-- FOOTER TANDA TANGAN -->
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Disampling oleh,
            </td>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Diserahkan oleh,
            </td>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Dianalisis oleh,
            </td>
            <td colspan="3" style="text-align: center; font-weight: bold; font-size: 9pt; border-top: 1px solid #000; border-right: 1px solid #000; background-color: #f8fafc; padding: 4px;">
                Dicek oleh,
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-left: 1px solid #000; border-right: 1px solid #000; height: 45px;"></td>
            <td colspan="4" style="border-right: 1px solid #000; height: 45px;"></td>
            <td colspan="4" style="border-right: 1px solid #000; height: 45px;"></td>
            <td colspan="3" style="border-right: 1px solid #000; height: 45px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-size: 9pt; border-left: 1px solid #000; border-right: 1px solid #000;">
                ({{ $data['pic_sampling'] ? $data['pic_sampling'] : '________________' }})
            </td>
            <td colspan="4" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ({{ $data['pic_serah_terima'] ? $data['pic_serah_terima'] : '________________' }})
            </td>
            <td colspan="4" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ({{ $data['pic_analis'] ? $data['pic_analis'] : '________________' }})
            </td>
            <td colspan="3" style="text-align: center; font-size: 9pt; border-right: 1px solid #000;">
                ({{ $data['pic_checker'] ? $data['pic_checker'] : '________________' }})
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; font-size: 8pt; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                Produksi
            </td>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size: 8pt; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                Produksi
            </td>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size: 8pt; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                QC Analis
            </td>
            <td colspan="3" style="text-align: center; font-weight: bold; font-size: 8pt; border-right: 1px solid #000; border-bottom: 1px solid #000; padding-bottom: 3px;">
                SPV/MNG QC
            </td>
        </tr>
        <tr>
            <td colspan="11"></td>
            <td colspan="3" style="text-align: right; font-size: 8pt; font-style: italic; color: #475569; padding-top: 5px;">
                {{ $data['doc_code'] ?? 'FRM/QLB/04/104/006-01' }}
            </td>
        </tr>
    </table>
</body>
</html>
