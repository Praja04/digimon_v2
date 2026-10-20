<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class DocPasteurisasiStorageExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        $blocks = !empty($this->data['batch_blocks']) ? $this->data['batch_blocks'] : [];

        if (empty($blocks)) {
            $blocks = [
                [
                    'block_index' => 0,
                    'halaman' => '1 / 1',
                    'batch' => $this->data['batch'] ?? ($this->data['batch_range'] ?? '1-2'),
                    'kode_st' => $this->data['kode_st'] ?? 'ST 01',
                    'jenis_produk' => $this->data['jenis_produk'] ?? ($this->data['variant'] ?? ''),
                    'tanggal_produksi' => $this->data['tanggal_produksi'] ?? ($this->data['tanggal_record_doc'] ?? date('Y-m-d')),
                    'jam_produksi_start' => $this->data['jam_produksi_start'] ?? '',
                    'jam_produksi_end' => $this->data['jam_produksi_end'] ?? '',
                    'kode_shift_grup' => $this->data['kode_shift_grup'] ?? '',
                    'pasteurisasi_rows' => $this->data['pasteurisasi_rows'] ?? [],
                    'storage_rows' => $this->data['storage_rows'] ?? [],
                    'catatan' => $this->data['catatan'] ?? '',
                    'pic_sampling_1' => $this->data['pic_sampling'] ?? '',
                    'pic_sampling_2' => $this->data['pic_serah_terima'] ?? '',
                    'pic_analis' => $this->data['pic_analis'] ?? '',
                    'pic_checker' => $this->data['pic_checker'] ?? '',
                ]
            ];
        }

        $totalBlocks = count($blocks);
        foreach ($blocks as $idx => $block) {
            $batchTitle = !empty($block['batch']) ? $block['batch'] : ('Siklus ' . ($idx + 1));
            $sheetTitle = 'Siklus ' . ($idx + 1) . ' (' . $batchTitle . ')';
            if (strlen($sheetTitle) > 31) {
                $sheetTitle = substr($sheetTitle, 0, 31);
            }
            $sheets[] = new DocPasteurisasiStorageBlockSheetExport($this->data, $block, $idx, $totalBlocks, $sheetTitle);
        }

        return $sheets;
    }
}

class DocPasteurisasiStorageBlockSheetExport implements WithEvents, WithTitle
{
    protected $docData;
    protected $block;
    protected $blockIndex;
    protected $totalBlocks;
    protected $sheetTitle;

    public function __construct(array $docData, array $block, int $blockIndex, int $totalBlocks, string $sheetTitle)
    {
        $this->docData = $docData;
        $this->block = $block;
        $this->blockIndex = $blockIndex;
        $this->totalBlocks = $totalBlocks;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Page setup: Landscape A4, Fit to 1x1 Page
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(1);
                $sheet->getPageMargins()->setTop(0.4);
                $sheet->getPageMargins()->setBottom(0.4);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setRight(0.4);

                // Default Font Arial 9
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // 16 Kolom Presisi (A s/d P)
                $widths = [
                    'A' => 14, // Sampling
                    'B' => 9,  // Jam
                    'C' => 12, // PIC
                    'D' => 11, // BJ (g/mL) / BJ
                    'E' => 8,  // Brix
                    'F' => 7,  // pH
                    'G' => 9,  // Aw (Past) / % NaCl (Storage)
                    'H' => 13, // Viskositas (ps)
                    'I' => 9,  // Organo
                    'J' => 9,  // Aroma
                    'K' => 10, // Warna
                    'L' => 8,  // Buih
                    'M' => 9,  // Endapan
                    'N' => 9,  // Part of Disposisi (Past) / Kristal (Storage)
                    'O' => 8,  // Part of Disposisi (Past) / Aw (Storage)
                    'P' => 22, // Disposisi / Keterangan
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // ==========================================
                // 1. HEADER DOKUMEN RESMI (Rows 1-4)
                // ==========================================
                // Logo BAS (A1:D4)
                $sheet->mergeCells('A1:D4');
                $sheet->getStyle('A1:D4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                $logoPath = public_path('assets/images/logo-bas.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo BAS');
                    $drawing->setDescription('Logo BAS PT. Bumi Alam Segar');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setHeight(36);
                    $drawing->setOffsetX(15);
                    $drawing->setOffsetY(10);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue('A1', "BAS\nPT. Bumi Alam Segar");
                    $sheet->getStyle('A1')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A1')->getFont()->setSize(10)->setBold(true);
                }

                // Judul Dokumen (E1:L4)
                $sheet->mergeCells('E1:L4');
                $sheet->setCellValue('E1', "HASIL ANALISIS PASTEURISASI DAN STORAGE TANK");
                $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:L4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Tanggal Record Doc & Halaman (M1:P4)
                $sheet->mergeCells('M1:N2');
                $sheet->setCellValue('M1', "Tanggal Record Doc");
                $sheet->getStyle('M1')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('M1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('O1:P2');
                $sheet->setCellValueExplicit('O1', ": " . ($this->docData['tanggal_record_doc'] ?? date('Y-m-d')), DataType::TYPE_STRING);
                $sheet->getStyle('O1')->getFont()->setSize(8.5);
                $sheet->getStyle('O1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('M3:N4');
                $sheet->setCellValue('M3', "Halaman");
                $sheet->getStyle('M3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('M3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $halamanVal = $this->block['halaman'] ?? (($this->blockIndex + 1) . ' / ' . $this->totalBlocks);
                $sheet->mergeCells('O3:P4');
                $sheet->setCellValueExplicit('O3', ": " . $halamanVal, DataType::TYPE_STRING);
                $sheet->getStyle('O3')->getFont()->setSize(8.5);
                $sheet->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('M1:P4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M2:P2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M1:N4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }

                // Row 5: Catatan Notes Sesuai Google Sheets
                $sheet->getRowDimension(5)->setRowHeight(14);
                $sheet->mergeCells('M5:P5');
                $sheet->setCellValue('M5', "Notes: 1 lembar: 1 blending (1 pasangan batch)");
                $sheet->getStyle('M5')->getFont()->setSize(7.5)->setItalic(true)->getColor()->setARGB('FF64748B');
                $sheet->getStyle('M5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                // ==========================================
                // 2. METADATA INFORMASI (Rows 6-8)
                // ==========================================
                $jenisProduk = $this->block['jenis_produk'] ?? ($this->docData['jenis_produk'] ?? ($this->docData['variant'] ?? '-'));
                $tglProduksi = $this->block['tanggal_produksi'] ?? ($this->docData['tanggal_record_doc'] ?? '-');
                $kodeShift = $this->block['kode_shift_grup'] ?? ($this->docData['kode_shift_grup'] ?? '-');
                $jamStart = $this->block['jam_produksi_start'] ?? '';
                $jamEnd = $this->block['jam_produksi_end'] ?? '';
                $jamStr = !empty($this->block['jam_produksi']) && $this->block['jam_produksi'] !== '-'
                    ? $this->block['jam_produksi']
                    : (($jamStart || $jamEnd) ? "{$jamStart} s/d {$jamEnd}" : "_____ s/d _____");
                $batchStr = $this->block['batch'] ?? '-';

                // Baris 6: Jenis Produk
                $sheet->mergeCells('A6:C6');
                $sheet->setCellValue('A6', "Jenis Produk");
                $sheet->getStyle('A6')->getFont()->setBold(true);
                $sheet->setCellValue('D6', ":");
                $sheet->mergeCells('E6:P6');
                $sheet->setCellValueExplicit('E6', (string)$jenisProduk, DataType::TYPE_STRING);

                // Baris 7: Tanggal Produksi & Kode Shift & Grup
                $sheet->mergeCells('A7:C7');
                $sheet->setCellValue('A7', "Tanggal Produksi");
                $sheet->getStyle('A7')->getFont()->setBold(true);
                $sheet->setCellValue('D7', ":");
                $sheet->mergeCells('E7:G7');
                $sheet->setCellValueExplicit('E7', (string)$tglProduksi, DataType::TYPE_STRING);

                $sheet->mergeCells('H7:J7');
                $sheet->setCellValue('H7', "Kode Shift & Grup");
                $sheet->getStyle('H7')->getFont()->setBold(true);
                $sheet->setCellValue('K7', ":");
                $sheet->mergeCells('L7:P7');
                $sheet->setCellValueExplicit('L7', (string)$kodeShift, DataType::TYPE_STRING);

                // Baris 8: Jam Produksi & Batch (Pasangan Batch Dissolver)
                $sheet->mergeCells('A8:C8');
                $sheet->setCellValue('A8', "Jam Produksi");
                $sheet->getStyle('A8')->getFont()->setBold(true);
                $sheet->setCellValue('D8', ":");
                $sheet->mergeCells('E8:G8');
                $sheet->setCellValueExplicit('E8', (string)$jamStr, DataType::TYPE_STRING);

                $sheet->mergeCells('H8:J8');
                $sheet->setCellValue('H8', "Batch");
                $sheet->getStyle('H8')->getFont()->setBold(true);
                $sheet->setCellValue('K8', ":");
                $sheet->mergeCells('L8:P8');
                $sheet->setCellValueExplicit('L8', (string)$batchStr, DataType::TYPE_STRING);

                for ($r = 6; $r <= 8; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                    $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("H{$r}:J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("L{$r}:P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                }
                $sheet->getRowDimension(9)->setRowHeight(8);

                // Garis Batas Kiri & Kanan Metadata
                $sheet->getStyle("A5:A9")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P5:P9")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                // ==========================================
                // 3. TABEL 1: PASTEURISASI (Rows 10-18)
                // ==========================================
                // Section Title Bar
                $sheet->mergeCells('A10:P10');
                $sheet->setCellValue('A10', '  PASTEURISASI');
                $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A10:P10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9ECEF');
                $sheet->getStyle('A10:P10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension(10)->setRowHeight(22);

                // Header Baris 1 (Row 11)
                $sheet->mergeCells('A11:A12');
                $sheet->setCellValue('A11', 'Sampling');
                $sheet->mergeCells('B11:C11');
                $sheet->setCellValue('B11', 'Serah Terima');
                $sheet->mergeCells('D11:M11');
                $sheet->setCellValue('D11', 'Analisis');
                $sheet->mergeCells('N11:P12');
                $sheet->setCellValue('N11', "Disposisi /\nKeterangan\n(Disposisi After Cooling)");
                $sheet->getStyle('N11')->getAlignment()->setWrapText(true);

                // Header Baris 2 (Row 12)
                $sheet->setCellValue('B12', 'Jam');
                $sheet->setCellValue('C12', 'PIC');
                $sheet->setCellValue('D12', 'BJ (g/mL)');
                $sheet->setCellValue('E12', 'Brix');
                $sheet->setCellValue('F12', 'pH');
                $sheet->setCellValue('G12', 'Aw');
                $sheet->setCellValue('H12', 'Viskositas (ps)');
                $sheet->setCellValue('I12', 'Organo');
                $sheet->setCellValue('J12', 'Aroma');
                $sheet->setCellValue('K12', 'Warna');
                $sheet->setCellValue('L12', 'Buih');
                $sheet->setCellValue('M12', 'Endapan');

                $sheet->getRowDimension(11)->setRowHeight(20);
                $sheet->getRowDimension(12)->setRowHeight(20);
                $sheet->getStyle('A11:P12')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('A11:P12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A11:P12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $sheet->getStyle('A11:P12')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $pastRows = $this->block['pasteurisasi_rows'] ?? [];
                $totalPastRows = max(6, count($pastRows));

                for ($i = 0; $i < $totalPastRows; $i++) {
                    $r = 13 + $i;
                    $row = $pastRows[$i] ?? null;

                    $sheet->getRowDimension($r)->setRowHeight(20);
                    $sheet->mergeCells("N{$r}:P{$r}");

                    if ($row) {
                        $sheet->setCellValue("A{$r}", $row['sampling'] ?? '');
                        $sheet->setCellValue("B{$r}", $row['jam'] ?? '');
                        $sheet->setCellValue("C{$r}", $row['pic'] ?? '');
                        $sheet->setCellValue("D{$r}", $this->formatVal($row['bj'] ?? ''));
                        $sheet->setCellValue("E{$r}", $this->formatVal($row['brix'] ?? ''));
                        $sheet->setCellValue("F{$r}", $this->formatVal($row['ph'] ?? ''));
                        $sheet->setCellValue("G{$r}", $this->formatVal($row['aw'] ?? ''));
                        $sheet->setCellValue("H{$r}", $this->formatVal($row['viskositas'] ?? ''));
                        $sheet->setCellValue("I{$r}", $row['organo'] ?? '');
                        $sheet->setCellValue("J{$r}", $row['aroma'] ?? '');
                        $sheet->setCellValue("K{$r}", $row['warna'] ?? '');
                        $sheet->setCellValue("L{$r}", $this->formatVal($row['buih'] ?? ''));
                        $sheet->setCellValue("M{$r}", $row['endapan'] ?? '');
                        $sheet->setCellValue("N{$r}", $row['disposisi'] ?? '');
                    } else {
                        $sheet->setCellValue("A{$r}", '');
                    }

                    $sheet->getStyle("A{$r}:M{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("N{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->getStyle("A{$r}:P{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$r}:P{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 4. TABEL 2: STORAGE TANK (Rows 19-27)
                // ==========================================
                $stHeaderRow = 13 + $totalPastRows;
                $kodeSt = $this->block['kode_st'] ?? 'ST 01';

                $sheet->mergeCells("A{$stHeaderRow}:D{$stHeaderRow}");
                $sheet->setCellValue("A{$stHeaderRow}", "  STORAGE TANK");
                $sheet->getStyle("A{$stHeaderRow}")->getFont()->setBold(true)->setSize(10);

                $sheet->mergeCells("E{$stHeaderRow}:P{$stHeaderRow}");
                $sheet->setCellValue("E{$stHeaderRow}", "KODE ST : " . $kodeSt);
                $sheet->getStyle("E{$stHeaderRow}")->getFont()->setBold(true)->setSize(10);

                $sheet->getStyle("A{$stHeaderRow}:P{$stHeaderRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9ECEF');
                $sheet->getStyle("A{$stHeaderRow}:P{$stHeaderRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$stHeaderRow}:P{$stHeaderRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($stHeaderRow)->setRowHeight(22);

                $stH1 = $stHeaderRow + 1;
                $stH2 = $stHeaderRow + 2;

                // Header Baris 1
                $sheet->mergeCells("A{$stH1}:A{$stH2}");
                $sheet->setCellValue("A{$stH1}", 'Sampling');
                $sheet->mergeCells("B{$stH1}:C{$stH1}");
                $sheet->setCellValue("B{$stH1}", 'Serah Terima');
                $sheet->mergeCells("D{$stH1}:O{$stH1}");
                $sheet->setCellValue("D{$stH1}", 'Analisis');
                $sheet->mergeCells("P{$stH1}:P{$stH2}");
                $sheet->setCellValue("P{$stH1}", "Disposisi /\nKeterangan");
                $sheet->getStyle("P{$stH1}")->getAlignment()->setWrapText(true);

                // Header Baris 2
                $sheet->setCellValue("B{$stH2}", 'Jam');
                $sheet->setCellValue("C{$stH2}", 'PIC');
                $sheet->setCellValue("D{$stH2}", 'BJ');
                $sheet->setCellValue("E{$stH2}", 'Brix');
                $sheet->setCellValue("F{$stH2}", 'pH');
                $sheet->setCellValue("G{$stH2}", '% NaCl');
                $sheet->setCellValue("H{$stH2}", 'Viskositas (ps)');
                $sheet->setCellValue("I{$stH2}", 'Organo');
                $sheet->setCellValue("J{$stH2}", 'Aroma');
                $sheet->setCellValue("K{$stH2}", 'Warna');
                $sheet->setCellValue("L{$stH2}", 'Buih');
                $sheet->setCellValue("M{$stH2}", 'Endapan');
                $sheet->setCellValue("N{$stH2}", 'Kristal');
                $sheet->setCellValue("O{$stH2}", 'Aw');

                $sheet->getRowDimension($stH1)->setRowHeight(20);
                $sheet->getRowDimension($stH2)->setRowHeight(20);
                $sheet->getStyle("A{$stH1}:P{$stH2}")->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle("A{$stH1}:P{$stH2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$stH1}:P{$stH2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $sheet->getStyle("A{$stH1}:P{$stH2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $stRows = $this->block['storage_rows'] ?? [];
                $totalStRows = max(5, count($stRows));
                $stDataStart = $stH2 + 1;

                for ($i = 0; $i < $totalStRows; $i++) {
                    $r = $stDataStart + $i;
                    $row = $stRows[$i] ?? null;

                    $sheet->getRowDimension($r)->setRowHeight(20);

                    if ($row) {
                        $sheet->setCellValue("A{$r}", $row['sampling'] ?? ($i === 0 ? 'Awal' : ($i === 1 ? 'Outlet' : '')));
                        $sheet->setCellValue("B{$r}", $row['jam'] ?? '');
                        $sheet->setCellValue("C{$r}", $row['pic'] ?? '');
                        $sheet->setCellValue("D{$r}", $this->formatVal($row['bj'] ?? ''));
                        $sheet->setCellValue("E{$r}", $this->formatVal($row['brix'] ?? ''));
                        $sheet->setCellValue("F{$r}", $this->formatVal($row['ph'] ?? ''));
                        $sheet->setCellValue("G{$r}", $this->formatVal($row['nacl'] ?? ''));
                        $sheet->setCellValue("H{$r}", $this->formatVal($row['viskositas'] ?? ''));
                        $sheet->setCellValue("I{$r}", $row['organo'] ?? '');
                        $sheet->setCellValue("J{$r}", $row['aroma'] ?? '');
                        $sheet->setCellValue("K{$r}", $row['warna'] ?? '');
                        $sheet->setCellValue("L{$r}", $this->formatVal($row['buih'] ?? ''));
                        $sheet->setCellValue("M{$r}", $row['endapan'] ?? '');
                        $sheet->setCellValue("N{$r}", $row['kristal'] ?? '');
                        $sheet->setCellValue("O{$r}", $this->formatVal($row['aw'] ?? ''));
                        $sheet->setCellValue("P{$r}", $row['disposisi'] ?? '');
                    } else {
                        $sheet->setCellValue("A{$r}", ($i === 0 ? 'Awal' : ($i === 1 ? 'Outlet' : '')));
                    }

                    $sheet->getStyle("A{$r}:O{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->getStyle("A{$r}:P{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$r}:P{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 5. FOOTER: CATATAN & TANDA TANGAN (Rows 29-34)
                // ==========================================
                $footerStart = $stDataStart + $totalStRows + 1; // Row 29
                
                // Box Catatan (A s/d F)
                $catatanVal = $this->block['catatan'] ?? ($this->docData['catatan'] ?? '');
                $sheet->mergeCells("A{$footerStart}:F" . ($footerStart + 4));
                $sheet->setCellValue("A{$footerStart}", "Catatan :\n" . $catatanVal);
                $sheet->getStyle("A{$footerStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                $sheet->getStyle("A{$footerStart}:F" . ($footerStart + 4))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$footerStart}:F" . ($footerStart + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

                // Tanda Tangan:
                // G-H: Disampling oleh, (Produksi 1)
                $sheet->mergeCells("G{$footerStart}:H{$footerStart}");
                $sheet->setCellValue("G{$footerStart}", "Disampling oleh,");
                $sheet->mergeCells("G" . ($footerStart + 1) . ":H" . ($footerStart + 3));
                $sheet->mergeCells("G" . ($footerStart + 4) . ":H" . ($footerStart + 4));
                $pSampling1 = $this->block['pic_sampling_1'] ?? ($this->docData['pic_sampling'] ?? '');
                $sheet->setCellValue("G" . ($footerStart + 4), "(" . ($pSampling1 ? ' ' . $pSampling1 . ' ' : '________________') . ")\nProduksi");

                // I-J: Disampling oleh, (Produksi 2)
                $sheet->mergeCells("I{$footerStart}:J{$footerStart}");
                $sheet->setCellValue("I{$footerStart}", "Disampling oleh,");
                $sheet->mergeCells("I" . ($footerStart + 1) . ":J" . ($footerStart + 3));
                $sheet->mergeCells("I" . ($footerStart + 4) . ":J" . ($footerStart + 4));
                $pSampling2 = $this->block['pic_sampling_2'] ?? ($this->docData['pic_serah_terima'] ?? '');
                $sheet->setCellValue("I" . ($footerStart + 4), "(" . ($pSampling2 ? ' ' . $pSampling2 . ' ' : '________________') . ")\nProduksi");

                // K-L: Dianalisis oleh, (QC Analis)
                $sheet->mergeCells("K{$footerStart}:L{$footerStart}");
                $sheet->setCellValue("K{$footerStart}", "Dianalisis oleh,");
                $sheet->mergeCells("K" . ($footerStart + 1) . ":L" . ($footerStart + 3));
                $sheet->mergeCells("K" . ($footerStart + 4) . ":L" . ($footerStart + 4));
                $pAnalis = $this->block['pic_analis'] ?? ($this->docData['pic_analis'] ?? '');
                $sheet->setCellValue("K" . ($footerStart + 4), "(" . ($pAnalis ? ' ' . $pAnalis . ' ' : '________________') . ")\nQC Analis");

                // M-P: Dicek oleh, (SPV/MNG QC)
                $sheet->mergeCells("M{$footerStart}:P{$footerStart}");
                $sheet->setCellValue("M{$footerStart}", "Dicek oleh,");
                $sheet->mergeCells("M" . ($footerStart + 1) . ":P" . ($footerStart + 3));
                $sheet->mergeCells("M" . ($footerStart + 4) . ":P" . ($footerStart + 4));
                $pChecker = $this->block['pic_checker'] ?? ($this->docData['pic_checker'] ?? '');
                $sheet->setCellValue("M" . ($footerStart + 4), "(" . ($pChecker ? ' ' . $pChecker . ' ' : '________________') . ")\nSPV/MNG QC");

                // Format Kotak Tanda Tangan
                $sheet->getStyle("G{$footerStart}:P" . ($footerStart + 4))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("G{$footerStart}:P{$footerStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("G{$footerStart}:P{$footerStart}")->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle("G" . ($footerStart + 4) . ":P" . ($footerStart + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $sheet->getStyle("G" . ($footerStart + 4) . ":P" . ($footerStart + 4))->getFont()->setSize(8);

                // Row Heights Footer
                $sheet->getRowDimension($footerStart)->setRowHeight(18);
                for ($fr = $footerStart + 1; $fr <= $footerStart + 3; $fr++) {
                    $sheet->getRowDimension($fr)->setRowHeight(14);
                }
                $sheet->getRowDimension($footerStart + 4)->setRowHeight(26);

                // Row Kode Dokumen Bawah Kanan (Row 34)
                $docCodeRow = $footerStart + 5;
                $sheet->getRowDimension($docCodeRow)->setRowHeight(16);
                $sheet->mergeCells("M{$docCodeRow}:P{$docCodeRow}");
                $sheet->setCellValue("M{$docCodeRow}", $this->docData['doc_code'] ?? 'FRM/QLB/04/104/006-01');
                $sheet->getStyle("M{$docCodeRow}")->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle("M{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            }
        ];
    }

    /**
     * Format nilai analisis: jika 0, 0.0, null, '-', atau kosong jadikan blank
     */
    private function formatVal($val): string
    {
        if ($val === null || $val === '' || $val === 'null' || $val === '-' || $val === '0' || $val === 0 || $val === 0.0 || $val === '0.0' || $val === '0.00') {
            return '';
        }
        if (is_numeric($val) && floatval($val) == 0) {
            return '';
        }
        return (string)$val;
    }
}
