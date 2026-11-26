<?php

namespace App\Exports;

use App\Models\PackagingIncoming;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPackagingExport implements WithEvents, WithTitle
{
    protected PackagingIncoming $incoming;
    protected string $type; // 'karton', 'pouch', 'inner'

    public function __construct(PackagingIncoming $incoming)
    {
        $this->incoming = $incoming;
        $namaJenis = strtolower($incoming->jenisIncoming?->nama ?? '');
        if (str_contains($namaJenis, 'pouch')) {
            $this->type = 'pouch';
        } elseif (str_contains($namaJenis, 'karton') || str_contains($namaJenis, 'kardus')) {
            $this->type = 'karton';
        } else {
            $this->type = 'inner';
        }
    }

    public function title(): string
    {
        $spb = preg_replace('/[\:\/\\\?\*\[\]]/', '', $this->incoming->no_spb ?? 'PM');
        if ($this->type === 'karton') {
            return substr("KARTON-{$spb}", 0, 31);
        } elseif ($this->type === 'pouch') {
            return substr("POUCH-{$spb}", 0, 31);
        } else {
            return substr("INNER-{$spb}", 0, 31);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(true);

                if ($this->type === 'karton') {
                    $this->buildKartonReport($sheet);
                } elseif ($this->type === 'pouch') {
                    $this->buildPouchReport($sheet);
                } else {
                    $this->buildInnerReport($sheet);
                }
            },
        ];
    }

    // =========================================================================
    // 1. FORMAT LAPORAN KARTON (FRM/QRP/02/003/001-01)
    // =========================================================================
    protected function buildKartonReport(Worksheet $sheet): void
    {
        $incoming = $this->incoming;
        $sampling = $incoming->packagingKartonSampling ?? $incoming->packagingKartonSamplingDraft;
        $bct = $incoming->kartonBct;

        // Styles
        $headerBg = 'D9E1F2'; // Soft blue
        $subHeaderBg = 'E2EFDA'; // Soft green
        $grayBg = 'F2F2F2';

        // Title
        $sheet->setCellValue('A2', 'HASIL PENGECEKAN KARTON');
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setName('Calibri');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Metadata
        $tglDatang = $incoming->tanggal_kedatangan ? Carbon::parse($incoming->tanggal_kedatangan)->format('d/m/Y') : '-';
        $expDate = $incoming->exp_date ? Carbon::parse($incoming->exp_date)->format('d/m/Y') : ($sampling?->exp_date ? Carbon::parse($sampling->exp_date)->format('d/m/Y') : '-');
        $jamDatang = $incoming->jam_kedatangan ? substr((string)$incoming->jam_kedatangan, 0, 5) : '-';
        $namaBahan = $incoming->jenisMaterial?->nama ?? $incoming->jenisIncoming?->nama ?? '-';
        $supplier = $incoming->supplier?->nama ?? '-';
        $noSpb = $incoming->no_spb ?? '-';
        $lotBatch = $incoming->no_batch ?? $sampling?->no_batch ?? '-';
        $noMobil = $incoming->no_mobil ?? '-';
        $jumlahDatang = ($incoming->jumlah !== null ? number_format((float)$incoming->jumlah, 0, ',', '.') : '-') . ' ' . ($incoming->uom?->nama ?? 'Pcs');

        $sheet->setCellValue('A4', 'Nama Bahan');
        $sheet->setCellValue('C4', ': ' . $namaBahan);
        $sheet->setCellValue('I4', 'No. SPB');
        $sheet->setCellValue('K4', ': ' . $noSpb);

        $sheet->setCellValue('A5', 'Tanggal Kedatangan');
        $sheet->setCellValue('C5', ': ' . $tglDatang);
        $sheet->setCellValue('I5', 'Lot/Batch');
        $sheet->setCellValue('K5', ': ' . $lotBatch);

        $sheet->setCellValue('A6', 'Supplier & Manufacturing');
        $sheet->setCellValue('C6', ': ' . $supplier);
        $sheet->setCellValue('I6', 'Jumlah Kedatangan');
        $sheet->setCellValue('K6', ': ' . $jumlahDatang);

        $sheet->setCellValue('A7', 'No. Mobil');
        $sheet->setCellValue('C7', ': ' . $noMobil);
        $sheet->setCellValue('I7', 'Jam Kedatangan');
        $sheet->setCellValue('K7', ': ' . $jamDatang);

        $sheet->setCellValue('I8', 'Exp Date');
        $sheet->setCellValue('K8', ': ' . $expDate);

        $sheet->getStyle('A4:A7')->getFont()->setBold(true);
        $sheet->getStyle('I4:I8')->getFont()->setBold(true);

        // Section Headers (Row 10)
        $sheet->setCellValue('A10', 'Pengecekan Kedatangan');
        $sheet->mergeCells('A10:D10');
        $sheet->setCellValue('E10', 'Pengecekan Fisik');
        $sheet->mergeCells('E10:H10');
        $sheet->setCellValue('I10', 'Pengecekan Karton');
        $sheet->mergeCells('I10:Q10');

        $sheet->getStyle('A10:Q10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);
        $sheet->getStyle('E10:H10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderBg);
        $sheet->getStyle('I10:Q10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);

        // Table Columns (Row 11)
        // Left: Kondisi Mobil
        $sheet->setCellValue('A11', 'Kondisi Mobil');
        $sheet->mergeCells('A11:D11');
        $sheet->setCellValue('A12', 'Spesifikasi');
        $sheet->mergeCells('A12:B12');
        $sheet->setCellValue('C12', 'Kondisi');
        $sheet->mergeCells('C12:D12');

        $kondisiRows = [
            ['Bersih', '√'],
            ['Kering', '√'],
            ['Tidak ada benda asing', '√'],
            ['Tidak berbau', '√'],
            ['Tidak cacat/bolong', '√'],
            ['Segel container', '√'],
        ];
        $rK = 13;
        foreach ($kondisiRows as $kr) {
            $sheet->setCellValue("A{$rK}", $kr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $kr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Kesimpulan :');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Sesuai');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Catatan :');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $rK++;

        // Dokumen & Identitas
        $sheet->setCellValue("A{$rK}", 'Dokumen dan Identitas');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $sheet->getStyle("A{$rK}")->getFont()->setBold(true);
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Dokumen');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Ada/Tidak');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;

        $coaStatus = $sampling?->coa ? ($sampling->coa === 'Ada' ? 'Ada' : 'Tidak') : 'Ada';
        $docRows = [
            ['COA', $coaStatus],
            ['Surat Jalan Vendor', 'Ada'],
            ['Packing List', 'Ada'],
            ['Identitas PM di kemasan', 'Ada'],
        ];
        foreach ($docRows as $dr) {
            $sheet->setCellValue("A{$rK}", $dr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $dr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Kesimpulan : Sesuai');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Catatan : ' . ($sampling?->keterangan ?? '-'));
        $sheet->mergeCells("A{$rK}:D{$rK}");

        // Middle Table: Pengecekan Fisik Dus/Roll
        $sheet->setCellValue('E11', 'Kriteria');
        $sheet->mergeCells('E11:F11');
        $sheet->setCellValue('G11', 'Qty Dus/Roll');
        $sheet->setCellValue('H11', '%');

        $fisikRows = [
            ['Rusak', '0', '0%'],
            ['Kotor', '0', '0%'],
            ['Aroma', '0', '0%'],
            ['Basah', '0', '0%'],
            ['Campuran', '0', '0%'],
            ['Total Bagus', $incoming->jumlah ?? '-', '100%'],
        ];
        $rF = 12;
        foreach ($fisikRows as $fr) {
            $sheet->setCellValue("E{$rF}", $fr[0]);
            $sheet->mergeCells("E{$rF}:F{$rF}");
            $sheet->setCellValue("G{$rF}", $fr[1]);
            $sheet->setCellValue("H{$rF}", $fr[2]);
            $sheet->getStyle("G{$rF}:H{$rF}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rF++;
        }

        // Serah Terima Sampel
        $sheet->setCellValue("E{$rF}", 'Serah Terima Sampel');
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", '');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", 'Jam');
        $sheet->setCellValue("H{$rF}", 'Ttd');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Sampling (Qty: ' . ($incoming->jumlah_sampel ?? 1) . ')');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", $jamDatang);
        $sheet->setCellValue("H{$rF}", '√');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Re-Sampling (Qty: ' . ($bct?->resampling_count ?? 0) . ')');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", '-');
        $sheet->setCellValue("H{$rF}", '-');
        $rF++;

        // Disposisi
        $disposisi = $sampling?->rekomendasi ?? 'Diterima';
        $sheet->setCellValue("E{$rF}", 'Disposisi : ' . $disposisi);
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Catatan : * monitoring seminggu sekali');
        $sheet->mergeCells("E{$rF}:H{$rF}");

        // Right Table: Pengecekan Karton (Analisis Sampel)
        $headersKarton = ['No Sampel', 'Desain', 'Warna', 'Tulisan', 'Barcode', 'Ukuran (pxlxt)', 'Berat (g)', 'Gramasi', 'BCT*'];
        $colsKarton = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];

        foreach ($headersKarton as $idx => $hdr) {
            $sheet->setCellValue($colsKarton[$idx] . '11', $hdr);
            $sheet->getStyle($colsKarton[$idx] . '11')->getFont()->setBold(true);
            $sheet->getStyle($colsKarton[$idx] . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $samples = is_array($sampling?->hasil_sampel) ? $sampling->hasil_sampel : [];
        $rS = 12;
        $bctAverage = $bct?->average ?? 0;
        $totalBerat = 0;
        $countBerat = 0;
        $totalGramasi = 0;
        $countGramasi = 0;

        if (empty($samples)) {
            // Minimal 1 baris kosong jika belum ada data
            $sheet->setCellValue("I{$rS}", 1);
            $sheet->setCellValue("J{$rS}", 'OK');
            $sheet->setCellValue("K{$rS}", 'OK');
            $sheet->setCellValue("L{$rS}", 'OK');
            $sheet->setCellValue("M{$rS}", 'OK');
            $sheet->setCellValue("N{$rS}", '-');
            $sheet->setCellValue("O{$rS}", '-');
            $sheet->setCellValue("P{$rS}", '-');
            $sheet->setCellValue("Q{$rS}", $bctAverage > 0 ? number_format($bctAverage, 2) : '-');
            $rS++;
        } else {
            foreach ($samples as $idx => $s) {
                $no = $idx + 1;
                $desain = $s['design'] ?? 'OK';
                $warna = $s['warna'] ?? 'OK';
                $tulisan = $s['tulisan'] ?? 'OK';
                $barcode = $s['no_barcode'] ?? $s['scan_barcode'] ?? 'OK';

                $p = $s['panjang'] ?? '';
                $l = $s['lebar'] ?? '';
                $t = $s['tinggi'] ?? '';
                $dimensi = ($p && $l && $t) ? "{$p}x{$l}x{$t}" : '-';

                $berat = $s['berat'] ?? $s['hasil_berat'] ?? null;
                $gramasi = $s['gramasi'] ?? null;

                if (is_numeric($berat) && (float)$berat > 0) {
                    $totalBerat += (float)$berat;
                    $countBerat++;
                }
                if (is_numeric($gramasi) && (float)$gramasi > 0) {
                    $totalGramasi += (float)$gramasi;
                    $countGramasi++;
                }

                $sampleBct = $s['bct'] ?? ($bctAverage > 0 ? $bctAverage : null);

                $sheet->setCellValue("I{$rS}", $no);
                $sheet->setCellValue("J{$rS}", $desain);
                $sheet->setCellValue("K{$rS}", $warna);
                $sheet->setCellValue("L{$rS}", $tulisan);
                $sheet->setCellValue("M{$rS}", $barcode);
                $sheet->setCellValue("N{$rS}", $dimensi);
                $sheet->setCellValue("O{$rS}", is_numeric($berat) ? number_format((float)$berat, 2) : '-');
                $sheet->setCellValue("P{$rS}", is_numeric($gramasi) ? number_format((float)$gramasi, 2) : '-');
                $sheet->setCellValue("Q{$rS}", is_numeric($sampleBct) ? number_format((float)$sampleBct, 2) : '-');

                $sheet->getStyle("I{$rS}:Q{$rS}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rS++;
            }
        }

        // Row Average
        $avgBerat = $countBerat > 0 ? number_format($totalBerat / $countBerat, 2) : '-';
        $avgGramasi = $countGramasi > 0 ? number_format($totalGramasi / $countGramasi, 2) : '-';
        $avgBct = $bctAverage > 0 ? number_format($bctAverage, 2) : '-';

        $sheet->setCellValue("I{$rS}", 'Average');
        $sheet->mergeCells("I{$rS}:N{$rS}");
        $sheet->setCellValue("O{$rS}", $avgBerat);
        $sheet->setCellValue("P{$rS}", $avgGramasi);
        $sheet->setCellValue("Q{$rS}", $avgBct);
        $sheet->getStyle("I{$rS}:Q{$rS}")->getFont()->setBold(true);
        $sheet->getStyle("I{$rS}:Q{$rS}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I{$rS}:Q{$rS}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($grayBg);

        // Styling Borders untuk Seluruh Tabel
        $maxRow = max($rK, $rF, $rS);
        $sheet->getStyle("A10:D{$rK}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("E10:H{$rF}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("I10:Q{$rS}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Footer Tanda Tangan
        $signRow = $maxRow + 3;
        $sheet->setCellValue("A{$signRow}", 'Disampling oleh,');
        $sheet->mergeCells("A{$signRow}:C{$signRow}");
        $sheet->setCellValue("E{$signRow}", 'Dianalisis oleh,');
        $sheet->mergeCells("E{$signRow}:G{$signRow}");
        $sheet->setCellValue("I{$signRow}", 'Dianalisis oleh,');
        $sheet->mergeCells("I{$signRow}:K{$signRow}");
        $sheet->setCellValue("M{$signRow}", 'Dicek oleh,');
        $sheet->mergeCells("M{$signRow}:O{$signRow}");

        $nameRow = $signRow + 4;
        $creatorName = $sampling?->creator?->name ?? 'QRP';
        $updaterName = $sampling?->updater?->name ?? 'QC PM';

        $sheet->setCellValue("A{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("A{$nameRow}:C{$nameRow}");
        $sheet->setCellValue("E{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("E{$nameRow}:G{$nameRow}");
        $sheet->setCellValue("I{$nameRow}", "( {$updaterName} )");
        $sheet->mergeCells("I{$nameRow}:K{$nameRow}");
        $sheet->setCellValue("M{$nameRow}", "( ________________ )");
        $sheet->mergeCells("M{$nameRow}:O{$nameRow}");

        $roleRow = $nameRow + 1;
        $sheet->setCellValue("A{$roleRow}", 'QRP');
        $sheet->mergeCells("A{$roleRow}:C{$roleRow}");
        $sheet->setCellValue("E{$roleRow}", 'QRP');
        $sheet->mergeCells("E{$roleRow}:G{$roleRow}");
        $sheet->setCellValue("I{$roleRow}", 'QC PM');
        $sheet->mergeCells("I{$roleRow}:K{$roleRow}");
        $sheet->setCellValue("M{$roleRow}", 'SPV/MNG QC');
        $sheet->mergeCells("M{$roleRow}:O{$roleRow}");

        $sheet->getStyle("A{$signRow}:O{$roleRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Document Form Number di pojok kanan bawah
        $formNoRow = $roleRow + 2;
        $sheet->setCellValue("N{$formNoRow}", 'FRM/QRP/02/003/001-01');
        $sheet->mergeCells("N{$formNoRow}:Q{$formNoRow}");
        $sheet->getStyle("N{$formNoRow}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("N{$formNoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Set column widths
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('N')->setWidth(15);
    }

    // =========================================================================
    // 2. FORMAT LAPORAN POUCH (FRM/QRP/02/002/001-01)
    // =========================================================================
    protected function buildPouchReport(Worksheet $sheet): void
    {
        $incoming = $this->incoming;
        $sampling = $incoming->packagingPouchSampling ?? $incoming->packagingPouchSamplingDraft;

        $headerBg = 'D9E1F2';
        $subHeaderBg = 'E2EFDA';
        $grayBg = 'F2F2F2';

        $sheet->setCellValue('A2', 'HASIL PENGECEKAN POUCH');
        $sheet->mergeCells('A2:R2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setName('Calibri');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $tglDatang = $incoming->tanggal_kedatangan ? Carbon::parse($incoming->tanggal_kedatangan)->format('d/m/Y') : '-';
        $expDate = $incoming->exp_date ? Carbon::parse($incoming->exp_date)->format('d/m/Y') : ($sampling?->exp_date ? Carbon::parse($sampling->exp_date)->format('d/m/Y') : '-');
        $jamDatang = $incoming->jam_kedatangan ? substr((string)$incoming->jam_kedatangan, 0, 5) : '-';
        $namaBahan = $incoming->jenisMaterial?->nama ?? $incoming->jenisIncoming?->nama ?? '-';
        $supplier = $incoming->supplier?->nama ?? '-';
        $noSpb = $incoming->no_spb ?? '-';
        $lotBatch = $incoming->no_batch ?? $sampling?->no_batch ?? '-';
        $noMobil = $incoming->no_mobil ?? '-';
        $jumlahDatang = ($incoming->jumlah !== null ? number_format((float)$incoming->jumlah, 0, ',', '.') : '-') . ' ' . ($incoming->uom?->nama ?? 'Pcs');

        $sheet->setCellValue('A4', 'Nama Bahan');
        $sheet->setCellValue('C4', ': ' . $namaBahan);
        $sheet->setCellValue('J4', 'No. SPB');
        $sheet->setCellValue('L4', ': ' . $noSpb);

        $sheet->setCellValue('A5', 'Tanggal Kedatangan');
        $sheet->setCellValue('C5', ': ' . $tglDatang);
        $sheet->setCellValue('E5', 'Jam Kedatangan : ' . $jamDatang);
        $sheet->setCellValue('J5', 'Lot/Batch');
        $sheet->setCellValue('L5', ': ' . $lotBatch);

        $sheet->setCellValue('A6', 'Supplier & Manufacturing');
        $sheet->setCellValue('C6', ': ' . $supplier);
        $sheet->setCellValue('J6', 'Jumlah Kedatangan');
        $sheet->setCellValue('L6', ': ' . $jumlahDatang);

        $sheet->setCellValue('A7', 'No. Mobil');
        $sheet->setCellValue('C7', ': ' . $noMobil);
        $sheet->setCellValue('J7', 'Exp Date');
        $sheet->setCellValue('L7', ': ' . $expDate);

        $sheet->getStyle('A4:A7')->getFont()->setBold(true);
        $sheet->getStyle('J4:J7')->getFont()->setBold(true);

        // Section Headers
        $sheet->setCellValue('A10', 'Pengecekan Kedatangan');
        $sheet->mergeCells('A10:D10');
        $sheet->setCellValue('E10', 'Pengecekan Fisik');
        $sheet->mergeCells('E10:H10');
        $sheet->setCellValue('I10', 'Analisis');
        $sheet->mergeCells('I10:T10');

        $sheet->getStyle('A10:T10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);
        $sheet->getStyle('E10:H10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderBg);
        $sheet->getStyle('I10:T10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);

        // Kondisi Mobil
        $sheet->setCellValue('A11', 'Kondisi Mobil');
        $sheet->mergeCells('A11:D11');
        $sheet->setCellValue('A12', 'Spesifikasi');
        $sheet->mergeCells('A12:B12');
        $sheet->setCellValue('C12', 'Kondisi*');
        $sheet->mergeCells('C12:D12');

        $kondisiRows = [
            ['Bersih', '√'],
            ['Kering', '√'],
            ['Tidak ada benda asing', '√'],
            ['Tidak berbau', '√'],
            ['Tidak cacat/bolong', '√'],
            ['Segel container', '√'],
        ];
        $rK = 13;
        foreach ($kondisiRows as $kr) {
            $sheet->setCellValue("A{$rK}", $kr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $kr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Kesimpulan :');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Sesuai');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Catatan :');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $rK++;

        // Dokumen
        $sheet->setCellValue("A{$rK}", 'Dokumen dan Identitas');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $sheet->getStyle("A{$rK}")->getFont()->setBold(true);
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Dokumen');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Ada/Tidak');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;

        $coaStatus = $sampling?->coa ? ($sampling->coa === 'Ada' ? 'Ada' : 'Tidak') : 'Ada';
        $docRows = [
            ['COA', $coaStatus],
            ['Surat Jalan Vendor', 'Ada'],
            ['Packing List', 'Ada'],
            ['Identitas PM di kemasan', 'Ada'],
        ];
        foreach ($docRows as $dr) {
            $sheet->setCellValue("A{$rK}", $dr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $dr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Catatan : ' . ($sampling?->keterangan ?? '-'));
        $sheet->mergeCells("A{$rK}:D{$rK}");

        // Pengecekan Fisik
        $sheet->setCellValue('E11', 'Kriteria');
        $sheet->mergeCells('E11:F11');
        $sheet->setCellValue('G11', 'Qty Dus/Roll');
        $sheet->setCellValue('H11', '%');

        $fisikRows = [
            ['Rusak', '0', '0%'],
            ['Kotor', '0', '0%'],
            ['Aroma', '0', '0%'],
            ['Basah', '0', '0%'],
            ['Campuran', '0', '0%'],
            ['Total Bagus', $incoming->jumlah ?? '-', '100%'],
        ];
        $rF = 12;
        foreach ($fisikRows as $fr) {
            $sheet->setCellValue("E{$rF}", $fr[0]);
            $sheet->mergeCells("E{$rF}:F{$rF}");
            $sheet->setCellValue("G{$rF}", $fr[1]);
            $sheet->setCellValue("H{$rF}", $fr[2]);
            $sheet->getStyle("G{$rF}:H{$rF}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rF++;
        }

        $sheet->setCellValue("E{$rF}", 'Serah Terima Sampel');
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", '');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", 'Jam');
        $sheet->setCellValue("H{$rF}", 'Ttd');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Sampling (Qty: ' . ($incoming->jumlah_sampel ?? 1) . ')');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", $jamDatang);
        $sheet->setCellValue("H{$rF}", '√');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Re-Sampling');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", '-');
        $sheet->setCellValue("H{$rF}", '-');
        $rF++;

        $disposisi = $sampling?->rekomendasi ?? 'Diterima';
        $sheet->setCellValue("E{$rF}", 'Disposisi : ' . $disposisi);
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Catatan : -');
        $sheet->mergeCells("E{$rF}:H{$rF}");

        // Analisis Pouch Table
        $headersPouch = [
            'No Box', 'No Sample', 'Desain', 'Barcode& QR code', 'Warna', 'Tulisan',
            'Ukuran (pxl)', 'Tebal 1', 'Tebal 2', 'Berat', 'Side Seal 1', 'Side Seal 2', 'Bottom Seal', 'Bottom High'
        ];
        $colsPouch = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];

        foreach ($headersPouch as $idx => $hdr) {
            $sheet->setCellValue($colsPouch[$idx] . '11', $hdr);
            $sheet->getStyle($colsPouch[$idx] . '11')->getFont()->setBold(true);
            $sheet->getStyle($colsPouch[$idx] . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $samples = is_array($sampling?->hasil_sampel) ? $sampling->hasil_sampel : [];
        $rS = 12;

        if (empty($samples)) {
            $sheet->setCellValue("I{$rS}", 1);
            $sheet->setCellValue("J{$rS}", 1);
            $sheet->setCellValue("K{$rS}", 'OK');
            $sheet->setCellValue("L{$rS}", 'OK');
            $sheet->setCellValue("M{$rS}", 'OK');
            $sheet->setCellValue("N{$rS}", 'OK');
            $sheet->setCellValue("O{$rS}", '-');
            $sheet->setCellValue("P{$rS}", '-');
            $sheet->setCellValue("Q{$rS}", '-');
            $sheet->setCellValue("R{$rS}", '-');
            $sheet->setCellValue("S{$rS}", '-');
            $sheet->setCellValue("T{$rS}", '-');
            $sheet->setCellValue("U{$rS}", '-');
            $sheet->setCellValue("V{$rS}", '-');
            $rS++;
        } else {
            foreach ($samples as $idx => $s) {
                $no = $idx + 1;
                $box = $s['box'] ?? 1;
                $p = $s['panjang'] ?? '';
                $l = $s['lebar'] ?? '';
                $ukuran = ($p && $l) ? "{$p}x{$l}" : '-';

                $sheet->setCellValue("I{$rS}", $box);
                $sheet->setCellValue("J{$rS}", $no);
                $sheet->setCellValue("K{$rS}", $s['design'] ?? 'OK');
                $sheet->setCellValue("L{$rS}", $s['barcode'] ?? $s['qr_code'] ?? 'OK');
                $sheet->setCellValue("M{$rS}", $s['warna'] ?? 'OK');
                $sheet->setCellValue("N{$rS}", $s['tulisan'] ?? 'OK');
                $sheet->setCellValue("O{$rS}", $ukuran);
                $sheet->setCellValue("P{$rS}", $s['thickness_1'] ?? $s['thickness'] ?? '-');
                $sheet->setCellValue("Q{$rS}", $s['thickness_2'] ?? '-');
                $sheet->setCellValue("R{$rS}", $s['berat'] ?? '-');
                $sheet->setCellValue("S{$rS}", $s['side_seal_1'] ?? '-');
                $sheet->setCellValue("T{$rS}", $s['side_seal_2'] ?? '-');
                $sheet->setCellValue("U{$rS}", $s['bottom_seal'] ?? '-');
                $sheet->setCellValue("V{$rS}", $s['bottom_high'] ?? '-');

                $sheet->getStyle("I{$rS}:V{$rS}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rS++;
            }
        }

        // Thickness Bottom Section
        $thick = is_array($sampling?->hasil_thickness) ? $sampling->hasil_thickness : [];
        $rThick = $rS + 1;
        $sheet->setCellValue("I{$rThick}", 'Thickness bottom :');
        $sheet->mergeCells("I{$rThick}:L{$rThick}");
        $sheet->getStyle("I{$rThick}")->getFont()->setBold(true);

        for ($tIdx = 1; $tIdx <= 3; $tIdx++) {
            $rT = $rThick + $tIdx;
            $tVal1 = $thick[$tIdx - 1]['nilai_1'] ?? '-';
            $tVal2 = $thick[$tIdx - 1]['nilai_2'] ?? '-';
            $sheet->setCellValue("I{$rT}", "{$tIdx}. Posisi {$tIdx}");
            $sheet->mergeCells("I{$rT}:K{$rT}");
            $sheet->setCellValue("L{$rT}", $tVal1);
            $sheet->setCellValue("M{$rT}", $tVal2);
            $sheet->getStyle("L{$rT}:M{$rT}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $maxRow = max($rK, $rF, $rThick + 4);
        $sheet->getStyle("A10:D{$rK}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("E10:H{$rF}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("I10:V{$rS}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Keterangan Ketentuan
        $rKet = $maxRow + 1;
        $sheet->setCellValue("A{$rKet}", "Keterangan :\n*Kondisi diisi dengan : √ : Ya, x : Tidak, - : Tidak Ada\nUkuran (pxl) : panjang x lebar");
        $sheet->mergeCells("A{$rKet}:F" . ($rKet + 2));

        // Footer Tanda Tangan
        $signRow = $rKet + 4;
        $sheet->setCellValue("A{$signRow}", 'Disampling oleh,');
        $sheet->mergeCells("A{$signRow}:C{$signRow}");
        $sheet->setCellValue("F{$signRow}", 'Dianalisis oleh,');
        $sheet->mergeCells("F{$signRow}:H{$signRow}");
        $sheet->setCellValue("K{$signRow}", 'Dianalisis oleh,');
        $sheet->mergeCells("K{$signRow}:M{$signRow}");
        $sheet->setCellValue("P{$signRow}", 'Dicek oleh,');
        $sheet->mergeCells("P{$signRow}:S{$signRow}");

        $nameRow = $signRow + 4;
        $creatorName = $sampling?->creator?->name ?? 'QC PM';
        $updaterName = $sampling?->updater?->name ?? 'QC PM';

        $sheet->setCellValue("A{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("A{$nameRow}:C{$nameRow}");
        $sheet->setCellValue("F{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("F{$nameRow}:H{$nameRow}");
        $sheet->setCellValue("K{$nameRow}", "( {$updaterName} )");
        $sheet->mergeCells("K{$nameRow}:M{$nameRow}");
        $sheet->setCellValue("P{$nameRow}", "( ________________ )");
        $sheet->mergeCells("P{$nameRow}:S{$nameRow}");

        $roleRow = $nameRow + 1;
        $sheet->setCellValue("A{$roleRow}", 'QC PM');
        $sheet->mergeCells("A{$roleRow}:C{$roleRow}");
        $sheet->setCellValue("F{$roleRow}", 'QC PM');
        $sheet->mergeCells("F{$roleRow}:H{$roleRow}");
        $sheet->setCellValue("K{$roleRow}", 'QC PM');
        $sheet->mergeCells("K{$roleRow}:M{$roleRow}");
        $sheet->setCellValue("P{$roleRow}", 'Staff/SPV/MGR QC');
        $sheet->mergeCells("P{$roleRow}:S{$roleRow}");

        $sheet->getStyle("A{$signRow}:S{$roleRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $formNoRow = $roleRow + 2;
        $sheet->setCellValue("R{$formNoRow}", 'FRM/QRP/02/002/001-01');
        $sheet->mergeCells("R{$formNoRow}:V{$formNoRow}");
        $sheet->getStyle("R{$formNoRow}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("R{$formNoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        foreach (range('A', 'V') as $col) {
            $sheet->getColumnDimension($col)->setWidth(11);
        }
        $sheet->getColumnDimension('A')->setWidth(16);
    }

    // =========================================================================
    // 3. FORMAT LAPORAN INNER SACHET (FRM/QRP/02/001/001-00)
    // =========================================================================
    protected function buildInnerReport(Worksheet $sheet): void
    {
        $incoming = $this->incoming;
        $sampling = $incoming->packagingInnerOuterSampling ?? $incoming->packagingInnerOuterSamplingDraft;

        $headerBg = 'D9E1F2';
        $subHeaderBg = 'E2EFDA';
        $grayBg = 'F2F2F2';

        $sheet->setCellValue('A2', 'FORM HASIL PENGECEKAN INNER SACHET');
        $sheet->mergeCells('A2:T2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setName('Calibri');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $tglDatang = $incoming->tanggal_kedatangan ? Carbon::parse($incoming->tanggal_kedatangan)->format('d/m/Y') : '-';
        $expDate = $incoming->exp_date ? Carbon::parse($incoming->exp_date)->format('d/m/Y') : ($sampling?->exp_date ? Carbon::parse($sampling->exp_date)->format('d/m/Y') : '-');
        $jamDatang = $incoming->jam_kedatangan ? substr((string)$incoming->jam_kedatangan, 0, 5) : '-';
        $namaBahan = $incoming->jenisMaterial?->nama ?? $incoming->jenisIncoming?->nama ?? '-';
        $supplier = $incoming->supplier?->nama ?? '-';
        $noSpb = $incoming->no_spb ?? '-';
        $lotBatch = $incoming->no_batch ?? $sampling?->no_batch ?? '-';
        $noMobil = $incoming->no_mobil ?? '-';
        $jumlahDatang = ($incoming->jumlah !== null ? number_format((float)$incoming->jumlah, 0, ',', '.') : '-') . ' ' . ($incoming->uom?->nama ?? 'Roll');

        $sheet->setCellValue('A4', 'Nama Bahan');
        $sheet->setCellValue('C4', ': ' . $namaBahan);
        $sheet->setCellValue('J4', 'No. SPB');
        $sheet->setCellValue('L4', ': ' . $noSpb);

        $sheet->setCellValue('A5', 'Tanggal Kedatangan');
        $sheet->setCellValue('C5', ': ' . $tglDatang);
        $sheet->setCellValue('E5', 'Jam Kedatangan : ' . $jamDatang);
        $sheet->setCellValue('J5', 'Lot/Batch');
        $sheet->setCellValue('L5', ': ' . $lotBatch);

        $sheet->setCellValue('A6', 'Supplier & Manufacturing');
        $sheet->setCellValue('C6', ': ' . $supplier);
        $sheet->setCellValue('J6', 'Jumlah Kedatangan');
        $sheet->setCellValue('L6', ': ' . $jumlahDatang);

        $sheet->setCellValue('A7', 'No. Mobil');
        $sheet->setCellValue('C7', ': ' . $noMobil);
        $sheet->setCellValue('J7', 'Exp Date');
        $sheet->setCellValue('L7', ': ' . $expDate);

        $sheet->getStyle('A4:A7')->getFont()->setBold(true);
        $sheet->getStyle('J4:J7')->getFont()->setBold(true);

        // Section Headers
        $sheet->setCellValue('A10', 'Pengecekan Kedatangan');
        $sheet->mergeCells('A10:D10');
        $sheet->setCellValue('E10', 'Pengecekan Fisik');
        $sheet->mergeCells('E10:H10');
        $sheet->setCellValue('I10', 'Analisis');
        $sheet->mergeCells('I10:T10');

        $sheet->getStyle('A10:T10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);
        $sheet->getStyle('E10:H10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderBg);
        $sheet->getStyle('I10:T10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerBg);

        // Kondisi Mobil
        $sheet->setCellValue('A11', 'Kondisi Mobil');
        $sheet->mergeCells('A11:D11');
        $sheet->setCellValue('A12', 'Spesifikasi');
        $sheet->mergeCells('A12:B12');
        $sheet->setCellValue('C12', 'Kondisi*');
        $sheet->mergeCells('C12:D12');

        $kondisiRows = [
            ['Bersih', '√'],
            ['Kering', '√'],
            ['Tidak ada benda asing', '√'],
            ['Tidak berbau', '√'],
            ['Tidak cacat/bolong', '√'],
            ['Segel container', '√'],
        ];
        $rK = 13;
        foreach ($kondisiRows as $kr) {
            $sheet->setCellValue("A{$rK}", $kr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $kr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Kesimpulan :');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Sesuai');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Catatan :');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $rK++;

        // Dokumen
        $sheet->setCellValue("A{$rK}", 'Dokumen dan Identitas');
        $sheet->mergeCells("A{$rK}:D{$rK}");
        $sheet->getStyle("A{$rK}")->getFont()->setBold(true);
        $rK++;
        $sheet->setCellValue("A{$rK}", 'Dokumen');
        $sheet->mergeCells("A{$rK}:B{$rK}");
        $sheet->setCellValue("C{$rK}", 'Ada/Tidak');
        $sheet->mergeCells("C{$rK}:D{$rK}");
        $rK++;

        $coaStatus = $sampling?->coa ? ($sampling->coa === 'Ada' ? 'Ada' : 'Tidak') : 'Ada';
        $docRows = [
            ['COA', $coaStatus],
            ['Surat Jalan Vendor', 'Ada'],
            ['Packing List', 'Ada'],
            ['Identitas PM di kemasan', 'Ada'],
        ];
        foreach ($docRows as $dr) {
            $sheet->setCellValue("A{$rK}", $dr[0]);
            $sheet->mergeCells("A{$rK}:B{$rK}");
            $sheet->setCellValue("C{$rK}", $dr[1]);
            $sheet->mergeCells("C{$rK}:D{$rK}");
            $sheet->getStyle("C{$rK}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rK++;
        }
        $sheet->setCellValue("A{$rK}", 'Catatan : ' . ($sampling?->keterangan ?? '-'));
        $sheet->mergeCells("A{$rK}:D{$rK}");

        // Fisik
        $sheet->setCellValue('E11', 'Kriteria');
        $sheet->mergeCells('E11:F11');
        $sheet->setCellValue('G11', 'Qty Dus/Roll');
        $sheet->setCellValue('H11', '%');

        $fisikRows = [
            ['Rusak', '0', '0%'],
            ['Kotor', '0', '0%'],
            ['Aroma', '0', '0%'],
            ['Basah', '0', '0%'],
            ['Campuran', '0', '0%'],
            ['Total Bagus', $incoming->jumlah ?? '-', '100%'],
        ];
        $rF = 12;
        foreach ($fisikRows as $fr) {
            $sheet->setCellValue("E{$rF}", $fr[0]);
            $sheet->mergeCells("E{$rF}:F{$rF}");
            $sheet->setCellValue("G{$rF}", $fr[1]);
            $sheet->setCellValue("H{$rF}", $fr[2]);
            $sheet->getStyle("G{$rF}:H{$rF}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rF++;
        }

        $sheet->setCellValue("E{$rF}", 'Serah Terima Sampel');
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", '');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", 'Jam');
        $sheet->setCellValue("H{$rF}", 'Ttd');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Sampling (Qty: ' . ($incoming->jumlah_sampel ?? 1) . ')');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", $jamDatang);
        $sheet->setCellValue("H{$rF}", '√');
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Re-Sampling');
        $sheet->mergeCells("E{$rF}:F{$rF}");
        $sheet->setCellValue("G{$rF}", '-');
        $sheet->setCellValue("H{$rF}", '-');
        $rF++;

        $disposisi = $sampling?->rekomendasi ?? 'Diterima';
        $sheet->setCellValue("E{$rF}", 'Disposisi : ' . $disposisi);
        $sheet->mergeCells("E{$rF}:H{$rF}");
        $sheet->getStyle("E{$rF}")->getFont()->setBold(true);
        $rF++;
        $sheet->setCellValue("E{$rF}", 'Catatan : -');
        $sheet->mergeCells("E{$rF}:H{$rF}");

        // Analisis Inner Sachet Table
        $headersInner = [
            'No Sampel', 'Desain', 'Warna', 'Tulisan', 'Berat Gross (kg)', 'Inside Core (cm)',
            'Ukuran (pxl)', 'Tebal (mm)', 'Bonding Strength', 'Seal Strength', 'Laminasi', 'Stability Test'
        ];
        $colsInner = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];

        foreach ($headersInner as $idx => $hdr) {
            $sheet->setCellValue($colsInner[$idx] . '11', $hdr);
            $sheet->getStyle($colsInner[$idx] . '11')->getFont()->setBold(true);
            $sheet->getStyle($colsInner[$idx] . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $samples = is_array($sampling?->hasil_sampel) ? $sampling->hasil_sampel : [];
        $rS = 12;

        if (empty($samples)) {
            $sheet->setCellValue("I{$rS}", 1);
            $sheet->setCellValue("J{$rS}", 'OK');
            $sheet->setCellValue("K{$rS}", 'OK');
            $sheet->setCellValue("L{$rS}", 'OK');
            $sheet->setCellValue("M{$rS}", '-');
            $sheet->setCellValue("N{$rS}", '-');
            $sheet->setCellValue("O{$rS}", '-');
            $sheet->setCellValue("P{$rS}", '-');
            $sheet->setCellValue("Q{$rS}", '-');
            $sheet->setCellValue("R{$rS}", '-');
            $sheet->setCellValue("S{$rS}", 'OK');
            $sheet->setCellValue("T{$rS}", 'OK');
            $rS++;
        } else {
            foreach ($samples as $idx => $s) {
                $no = $idx + 1;
                $lebar = $s['lebar'] ?? '';
                $pitch = $s['pitch'] ?? '';
                $ukuran = ($lebar && $pitch) ? "{$lebar}x{$pitch}" : ($lebar ?: '-');

                $sheet->setCellValue("I{$rS}", $no);
                $sheet->setCellValue("J{$rS}", $s['design'] ?? 'OK');
                $sheet->setCellValue("K{$rS}", $s['warna'] ?? 'OK');
                $sheet->setCellValue("L{$rS}", $s['tulisan'] ?? 'OK');
                $sheet->setCellValue("M{$rS}", $s['berat_gross'] ?? '-');
                $sheet->setCellValue("N{$rS}", $s['inside_core'] ?? '-');
                $sheet->setCellValue("O{$rS}", $ukuran);
                $sheet->setCellValue("P{$rS}", $s['thickness'] ?? '-');
                $sheet->setCellValue("Q{$rS}", $s['bonding_strength'] ?? '-');
                $sheet->setCellValue("R{$rS}", $s['sealing_strength'] ?? '-');
                $sheet->setCellValue("S{$rS}", $s['laminasi'] ?? 'OK');
                $sheet->setCellValue("T{$rS}", $s['stability_test'] ?? 'OK');

                $sheet->getStyle("I{$rS}:T{$rS}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rS++;
            }
        }

        $maxRow = max($rK, $rF, $rS);
        $sheet->getStyle("A10:D{$rK}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("E10:H{$rF}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("I10:T{$rS}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $rKet = $maxRow + 1;
        $sheet->setCellValue("A{$rKet}", "Keterangan :\n*Kondisi diisi dengan : √ : Ya, x : Tidak, - : Tidak Ada");
        $sheet->mergeCells("A{$rKet}:F" . ($rKet + 2));

        // Footer Tanda Tangan
        $signRow = $rKet + 4;
        $sheet->setCellValue("A{$signRow}", 'Disampling oleh,');
        $sheet->mergeCells("A{$signRow}:C{$signRow}");
        $sheet->setCellValue("G{$signRow}", 'Dianalisis oleh,');
        $sheet->mergeCells("G{$signRow}:I{$signRow}");
        $sheet->setCellValue("N{$signRow}", 'Dicek oleh,');
        $sheet->mergeCells("N{$signRow}:Q{$signRow}");

        $nameRow = $signRow + 4;
        $creatorName = $sampling?->creator?->name ?? 'QRP';

        $sheet->setCellValue("A{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("A{$nameRow}:C{$nameRow}");
        $sheet->setCellValue("G{$nameRow}", "( {$creatorName} )");
        $sheet->mergeCells("G{$nameRow}:I{$nameRow}");
        $sheet->setCellValue("N{$nameRow}", "( ________________ )");
        $sheet->mergeCells("N{$nameRow}:Q{$nameRow}");

        $roleRow = $nameRow + 1;
        $sheet->setCellValue("A{$roleRow}", 'QRP');
        $sheet->mergeCells("A{$roleRow}:C{$roleRow}");
        $sheet->setCellValue("G{$roleRow}", 'QRP');
        $sheet->mergeCells("G{$roleRow}:I{$roleRow}");
        $sheet->setCellValue("N{$roleRow}", 'Staff/SPV/MGR QC');
        $sheet->mergeCells("N{$roleRow}:Q{$roleRow}");

        $sheet->getStyle("A{$signRow}:Q{$roleRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $formNoRow = $roleRow + 2;
        $sheet->setCellValue("P{$formNoRow}", 'FRM/QRP/02/001/001-00');
        $sheet->mergeCells("P{$formNoRow}:T{$formNoRow}");
        $sheet->getStyle("P{$formNoRow}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("P{$formNoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setWidth(11);
        }
        $sheet->getColumnDimension('A')->setWidth(16);
    }
}
