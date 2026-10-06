<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class DocPelarutanExport implements WithMultipleSheets
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
                $this->createDefaultBlock('1', '1'),
                $this->createDefaultBlock('2', '2'),
            ];
        }

        // 1 Lembar = 2 Batch
        $pairedBlocks = array_chunk($blocks, 2);
        $totalPages = count($pairedBlocks);
        $usedTitles = [];

        foreach ($pairedBlocks as $idx => $pair) {
            $sheetNum = $idx + 1;
            $b1 = $pair[0];
            $b2 = $pair[1] ?? $this->createDefaultBlock((string)($sheetNum * 2), (string)($sheetNum * 2));

            $b1Num = $b1['batch'] ?? (string)($sheetNum * 2 - 1);
            $b2Num = $b2['batch'] ?? (string)($sheetNum * 2);
            $baseTitle = "Batch {$b1Num}-{$b2Num}";

            // Sheet title limit is 31 chars
            $sheetTitle = substr($baseTitle, 0, 28);
            if (isset($usedTitles[$sheetTitle])) {
                $usedTitles[$sheetTitle]++;
                $sheetTitle = substr($sheetTitle, 0, 25) . '_' . $usedTitles[$sheetTitle];
            } else {
                $usedTitles[$sheetTitle] = 1;
            }

            $sheets[] = new SingleDocPelarutanSheetExport(
                $this->data,
                $b1,
                $b2,
                $sheetNum,
                $totalPages,
                $sheetTitle
            );
        }

        return $sheets;
    }

    private function createDefaultBlock($batchNum, $dissolverNum): array
    {
        $defaultRows = [];
        for ($r = 1; $r <= 5; $r++) {
            $defaultRows[] = [
                'p1_sampling_ke' => (string)$r,
                'p1_jam' => '',
                'p1_pic' => '',
                'p1_brix' => '',
                'p1_nacl' => '',
                'p1_warna' => '',
                'p1_organo' => '',
                'p1_waktu_adjustment' => '',
                'p2_sampling_ke' => (string)$r,
                'p2_jam' => '',
                'p2_pic' => '',
                'p2_brix' => '',
                'p2_nacl' => '',
                'p2_warna' => '',
                'p2_organo' => '',
                'p2_waktu_adjustment' => '',
                'disposisi' => '',
            ];
        }

        return [
            'jenis_produk' => $this->data['variant'] ?? 'Kecap Manis',
            'tanggal_produksi' => $this->data['tanggal_record_doc'] ?? date('Y-m-d'),
            'jam_produksi' => '08:00',
            'kode_shift_grup' => 'Shift 1 / Grup A',
            'batch' => $batchNum,
            'no_dissolver' => $dissolverNum,
            'volume' => '',
            'rows' => $defaultRows,
        ];
    }
}

class SingleDocPelarutanSheetExport implements WithEvents, WithTitle
{
    protected $docData;
    protected $block1;
    protected $block2;
    protected $pageNumber;
    protected $totalPages;
    protected $sheetTitle;

    public function __construct(array $docData, array $block1, array $block2, int $pageNumber, int $totalPages, string $sheetTitle)
    {
        $this->docData = $docData;
        $this->block1 = $block1;
        $this->block2 = $block2;
        $this->pageNumber = $pageNumber;
        $this->totalPages = $totalPages;
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
                $sheet->getPageMargins()->setTop(0.35);
                $sheet->getPageMargins()->setBottom(0.35);
                $sheet->getPageMargins()->setLeft(0.35);
                $sheet->getPageMargins()->setRight(0.35);

                // Set Default Font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // Setup 17 Column Widths (A to Q)
                $widths = [
                    'A' => 11, // Sampling ke- P1
                    'B' => 8,  // Jam P1
                    'C' => 13, // PIC P1
                    'D' => 9,  // Brix P1
                    'E' => 9,  // %NaCl P1
                    'F' => 10, // Warna P1
                    'G' => 9,  // Organo P1
                    'H' => 25, // Waktu & Adj P1
                    'I' => 11, // Sampling ke- P2
                    'J' => 8,  // Jam P2
                    'K' => 13, // PIC P2
                    'L' => 9,  // Brix P2
                    'M' => 9,  // %NaCl P2
                    'N' => 10, // Warna P2
                    'O' => 9,  // Organo P2
                    'P' => 25, // Waktu & Adj P2
                    'Q' => 13, // Disposisi
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // ==========================================
                // 1. HEADER DOKUMEN (Baris 1 - 4)
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
                    $drawing->setHeight(34);
                    $drawing->setOffsetX(12);
                    $drawing->setOffsetY(8);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue('A1', "BAS\nPT. Bumi Alam Segar");
                    $sheet->getStyle('A1')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A1')->getFont()->setSize(10)->setBold(true);
                }

                // Judul Dokumen (E1:O4)
                $sheet->mergeCells('E1:O4');
                $sheet->setCellValue('E1', "HASIL ANALISIS PROSES PELARUTAN");
                $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:O4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Tanggal Record Doc & Halaman (P1:Q4)
                $sheet->mergeCells('P1:P2');
                $sheet->setCellValue('P1', "Tanggal Record Doc");
                $sheet->getStyle('P1')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('P1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('Q1:Q2');
                $sheet->setCellValueExplicit('Q1', ": " . ($this->docData['tanggal_record_doc'] ?? date('Y-m-d')), DataType::TYPE_STRING);
                $sheet->getStyle('Q1')->getFont()->setSize(8.5);
                $sheet->getStyle('Q1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $pageStr = $this->pageNumber . ' / ' . $this->totalPages;
                $sheet->mergeCells('P3:P4');
                $sheet->setCellValue('P3', "Halaman");
                $sheet->getStyle('P3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('Q3:Q4');
                $sheet->setCellValueExplicit('Q3', ": " . $pageStr, DataType::TYPE_STRING);
                $sheet->getStyle('Q3')->getFont()->setSize(8.5);
                $sheet->getStyle('Q3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('P1:Q4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('P2:Q2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('P1:P4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(15);
                }
                $sheet->getRowDimension(5)->setRowHeight(6);

                // ==========================================
                // 2. BLOK BATCH 1 (Baris 6 - 17)
                // ==========================================
                $this->renderBatchBlock($sheet, $this->block1, 6);

                // Spasi antara Blok 1 dan Blok 2
                $sheet->getRowDimension(18)->setRowHeight(6);

                // ==========================================
                // 3. BLOK BATCH 2 (Baris 19 - 30)
                // ==========================================
                $this->renderBatchBlock($sheet, $this->block2, 19);

                // ==========================================
                // 4. FOOTER (Catatan & Tanda Tangan) (Baris 31 - 35)
                // ==========================================
                $fStart = 31;
                $catatanEnd = 35;

                // Catatan di A31:E35
                $sheet->mergeCells("A{$fStart}:E{$catatanEnd}");
                $catatanVal = $this->docData['catatan'] ?? '';
                $sheet->setCellValue("A{$fStart}", "Catatan :\n" . $catatanVal);
                $sheet->getStyle("A{$fStart}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$fStart}:E{$catatanEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Signatures
                $sheet->mergeCells("F{$fStart}:I{$fStart}");
                $sheet->setCellValue("F{$fStart}", 'Disampling oleh,');
                $sheet->mergeCells("J{$fStart}:M{$fStart}");
                $sheet->setCellValue("J{$fStart}", 'Dianalisis oleh,');
                $sheet->mergeCells("N{$fStart}:Q{$fStart}");
                $sheet->setCellValue("N{$fStart}", 'Dicek oleh,');
                $sheet->getStyle("F{$fStart}:Q{$fStart}")->getFont()->setSize(8.5);
                $sheet->getStyle("F{$fStart}:Q{$fStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // Space Tanda Tangan (2 baris)
                $s1 = $fStart + 1; // 32
                $s2 = $fStart + 2; // 33
                $sheet->mergeCells("F{$s1}:I{$s2}");
                $sheet->mergeCells("J{$s1}:M{$s2}");
                $sheet->mergeCells("N{$s1}:Q{$s2}");
                $sheet->getRowDimension($s1)->setRowHeight(15);
                $sheet->getRowDimension($s2)->setRowHeight(15);

                // Nama Tanda Tangan
                $sName = $fStart + 3; // 34
                $picSampling = !empty($this->docData['pic_sampling']) ? $this->docData['pic_sampling'] : '________________';
                $picAnalis = !empty($this->docData['pic_analis']) ? $this->docData['pic_analis'] : '________________';
                $picChecker = !empty($this->docData['pic_checker']) ? $this->docData['pic_checker'] : '________________';

                $sheet->mergeCells("F{$sName}:I{$sName}");
                $sheet->setCellValue("F{$sName}", "( {$picSampling} )");
                $sheet->mergeCells("J{$sName}:M{$sName}");
                $sheet->setCellValue("J{$sName}", "( {$picAnalis} )");
                $sheet->mergeCells("N{$sName}:Q{$sName}");
                $sheet->setCellValue("N{$sName}", "( {$picChecker} )");
                $sheet->getStyle("F{$sName}:Q{$sName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($sName)->setRowHeight(15);

                // Role Tanda Tangan
                $sRole = $fStart + 4; // 35
                $sheet->mergeCells("F{$sRole}:I{$sRole}");
                $sheet->setCellValue("F{$sRole}", 'Produksi');
                $sheet->mergeCells("J{$sRole}:M{$sRole}");
                $sheet->setCellValue("J{$sRole}", 'QC Analis');
                $sheet->mergeCells("N{$sRole}:Q{$sRole}");
                $sheet->setCellValue("N{$sRole}", 'Staff/SPV/MNG QC');
                $sheet->getStyle("F{$sRole}:Q{$sRole}")->getFont()->setSize(8)->setBold(true);
                $sheet->getStyle("F{$sRole}:Q{$sRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($sRole)->setRowHeight(15);

                // Outline Borders Signatures
                $sheet->getStyle("F{$fStart}:I{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("J{$fStart}:M{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("N{$fStart}:Q{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Kode Dokumen
                $docCodeRow = $sRole + 1; // 36
                $sheet->mergeCells("N{$docCodeRow}:Q{$docCodeRow}");
                $sheet->setCellValue("N{$docCodeRow}", 'FRM/QLB/04/104/004-01');
                $sheet->getStyle("N{$docCodeRow}")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("N{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension($docCodeRow)->setRowHeight(14);

                // Pastikan Garis Batas Kiri dan Kanan Terhubung Utuh
                $sheet->getStyle("A1:A{$docCodeRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("Q1:Q{$docCodeRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }

    private function renderBatchBlock($sheet, array $block, int $startRow): void
    {
        // Metadata (4 Baris)
        $r1 = $startRow;
        $sheet->mergeCells("A{$r1}:C{$r1}");
        $sheet->setCellValue("A{$r1}", "Jenis Produk");
        $sheet->getStyle("A{$r1}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$r1}", ":");
        $sheet->mergeCells("E{$r1}:H{$r1}");
        $sheet->setCellValueExplicit("E{$r1}", (string)($block['jenis_produk'] ?? '-'), DataType::TYPE_STRING);

        $sheet->mergeCells("I{$r1}:K{$r1}");
        $sheet->setCellValue("I{$r1}", "Batch");
        $sheet->getStyle("I{$r1}")->getFont()->setBold(true);
        $sheet->setCellValue("L{$r1}", ":");
        $sheet->mergeCells("M{$r1}:Q{$r1}");
        $sheet->setCellValueExplicit("M{$r1}", (string)($block['batch'] ?? '-'), DataType::TYPE_STRING);

        $r2 = $startRow + 1;
        $sheet->mergeCells("A{$r2}:C{$r2}");
        $sheet->setCellValue("A{$r2}", "Tanggal Produksi");
        $sheet->getStyle("A{$r2}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$r2}", ":");
        $sheet->mergeCells("E{$r2}:H{$r2}");
        $sheet->setCellValueExplicit("E{$r2}", (string)($block['tanggal_produksi'] ?? '-'), DataType::TYPE_STRING);

        $sheet->mergeCells("I{$r2}:K{$r2}");
        $sheet->setCellValue("I{$r2}", "No. Dissolver");
        $sheet->getStyle("I{$r2}")->getFont()->setBold(true);
        $sheet->setCellValue("L{$r2}", ":");
        $sheet->mergeCells("M{$r2}:Q{$r2}");
        $sheet->setCellValueExplicit("M{$r2}", (string)($block['no_dissolver'] ?? '-'), DataType::TYPE_STRING);

        $r3 = $startRow + 2;
        $sheet->mergeCells("A{$r3}:C{$r3}");
        $sheet->setCellValue("A{$r3}", "Jam Produksi");
        $sheet->getStyle("A{$r3}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$r3}", ":");
        $sheet->mergeCells("E{$r3}:H{$r3}");
        $sheet->setCellValueExplicit("E{$r3}", (string)($block['jam_produksi'] ?? '-'), DataType::TYPE_STRING);

        $sheet->mergeCells("I{$r3}:K{$r3}");
        $sheet->setCellValue("I{$r3}", "Volume");
        $sheet->getStyle("I{$r3}")->getFont()->setBold(true);
        $sheet->setCellValue("L{$r3}", ":");
        $sheet->mergeCells("M{$r3}:Q{$r3}");
        $sheet->setCellValueExplicit("M{$r3}", (string)($block['volume'] ?? ''), DataType::TYPE_STRING);

        $r4 = $startRow + 3;
        $sheet->mergeCells("A{$r4}:C{$r4}");
        $sheet->setCellValue("A{$r4}", "Kode Shift & Grup");
        $sheet->getStyle("A{$r4}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$r4}", ":");
        $sheet->mergeCells("E{$r4}:Q{$r4}");
        $sheet->setCellValueExplicit("E{$r4}", (string)($block['kode_shift_grup'] ?? '-'), DataType::TYPE_STRING);

        for ($r = $r1; $r <= $r4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(16);
            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("E{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("I{$r}:K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("M{$r}:Q{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Spasi sebelum tabel
        $spRow = $startRow + 4;
        $sheet->getRowDimension($spRow)->setRowHeight(6);

        // Garis batas samping
        $metaStartRow = $startRow - 1;
        $sheet->getStyle("A{$metaStartRow}:A{$spRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("Q{$metaStartRow}:Q{$spRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

        // Table Header (2 baris)
        $th1 = $startRow + 5;
        $th2 = $startRow + 6;

        $sheet->mergeCells("A{$th1}:A{$th2}");
        $sheet->setCellValue("A{$th1}", "Sampling ke-");

        $sheet->mergeCells("B{$th1}:C{$th1}");
        $sheet->setCellValue("B{$th1}", "Serah Terima");
        $sheet->setCellValue("B{$th2}", "Jam");
        $sheet->setCellValue("C{$th2}", "PIC");

        $sheet->mergeCells("D{$th1}:H{$th1}");
        $sheet->setCellValue("D{$th1}", "Pelarutan I (GGA)");
        $sheet->setCellValue("D{$th2}", "Brix");
        $sheet->setCellValue("E{$th2}", "%NaCl");
        $sheet->setCellValue("F{$th2}", "Warna");
        $sheet->setCellValue("G{$th2}", "Organo");
        $sheet->setCellValue("H{$th2}", "Waktu & Adjustment");

        $sheet->mergeCells("I{$th1}:I{$th2}");
        $sheet->setCellValue("I{$th1}", "Sampling ke-");

        $sheet->mergeCells("J{$th1}:K{$th1}");
        $sheet->setCellValue("J{$th1}", "Serah Terima");
        $sheet->setCellValue("J{$th2}", "Jam");
        $sheet->setCellValue("K{$th2}", "PIC");

        $sheet->mergeCells("L{$th1}:P{$th1}");
        $sheet->setCellValue("L{$th1}", "Pelarutan II (GGAS)");
        $sheet->setCellValue("L{$th2}", "Brix");
        $sheet->setCellValue("M{$th2}", "%NaCl");
        $sheet->setCellValue("N{$th2}", "Warna");
        $sheet->setCellValue("O{$th2}", "Organo");
        $sheet->setCellValue("P{$th2}", "Waktu & Adjustment");

        $sheet->mergeCells("Q{$th1}:Q{$th2}");
        $sheet->setCellValue("Q{$th1}", "Disposisi");

        $sheet->getRowDimension($th1)->setRowHeight(18);
        $sheet->getRowDimension($th2)->setRowHeight(18);
        $sheet->getStyle("A{$th1}:Q{$th2}")->getFont()->setBold(true)->setSize(8.5);
        $sheet->getStyle("A{$th1}:Q{$th2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$th1}:Q{$th2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data Rows (Tepat 5 baris standar per blok batch)
        $rows = $block['rows'] ?? [];
        $dataStart = $th2 + 1;
        $totalRows = max(5, count($rows));

        for ($i = 0; $i < $totalRows; $i++) {
            $dr = $dataStart + $i;
            $row = $rows[$i] ?? null;

            if ($row) {
                $sheet->setCellValueExplicit("A{$dr}", (string)($row['p1_sampling_ke'] ?? ($i + 1)), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("B{$dr}", (string)($row['p1_jam'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("C{$dr}", (string)($row['p1_pic'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("D{$dr}", (string)($row['p1_brix'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("E{$dr}", (string)($row['p1_nacl'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("F{$dr}", (string)($row['p1_warna'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("G{$dr}", (string)($row['p1_organo'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("H{$dr}", (string)($row['p1_waktu_adjustment'] ?? ''), DataType::TYPE_STRING);

                $sheet->setCellValueExplicit("I{$dr}", (string)($row['p2_sampling_ke'] ?? ($i + 1)), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("J{$dr}", (string)($row['p2_jam'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("K{$dr}", (string)($row['p2_pic'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("L{$dr}", (string)($row['p2_brix'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("M{$dr}", (string)($row['p2_nacl'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("N{$dr}", (string)($row['p2_warna'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("O{$dr}", (string)($row['p2_organo'] ?? ''), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("P{$dr}", (string)($row['p2_waktu_adjustment'] ?? ''), DataType::TYPE_STRING);

                $sheet->setCellValueExplicit("Q{$dr}", (string)($row['disposisi'] ?? ''), DataType::TYPE_STRING);
            }

            $sheet->getRowDimension($dr)->setRowHeight(18);
            $sheet->getStyle("A{$dr}:G{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
            $sheet->getStyle("I{$dr}:O{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("P{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
            $sheet->getStyle("Q{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$dr}:Q{$dr}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$dr}:Q{$dr}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }
}
