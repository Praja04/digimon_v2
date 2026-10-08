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

class DocBlendingAwalExport implements WithMultipleSheets
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
                    'jenis_produk' => $this->data['variant'] ?? 'Kecap Sedap',
                    'tanggal_produksi' => $this->data['tanggal_record_doc'] ?? date('Y-m-d'),
                    'jam_produksi' => '08:00',
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => '1',
                    'no_blending' => '1',
                    'volume_awal' => '10000 L',
                    'catatan' => $this->data['catatan'] ?? '',
                    'pic_sampling' => $this->data['pic_sampling'] ?? '',
                    'pic_analis' => $this->data['pic_analis'] ?? '',
                    'pic_checker' => $this->data['pic_checker'] ?? '',
                    'rows' => [],
                ]
            ];
        }

        $totalPages = count($blocks);
        $usedTitles = [];

        foreach ($blocks as $idx => $block) {
            $sheetNum = $idx + 1;
            $noBlending = !empty($block['no_blending']) ? $block['no_blending'] : $sheetNum;
            $baseTitle = 'Blending ' . $noBlending;

            // Sheet title in Excel must be <= 31 chars and unique
            $sheetTitle = substr($baseTitle, 0, 28);
            if (isset($usedTitles[$sheetTitle])) {
                $usedTitles[$sheetTitle]++;
                $sheetTitle = substr($sheetTitle, 0, 25) . '_' . $usedTitles[$sheetTitle];
            } else {
                $usedTitles[$sheetTitle] = 1;
            }

            $sheets[] = new SingleDocBlendingSheetExport(
                $this->data,
                $block,
                $sheetNum,
                $totalPages,
                $sheetTitle
            );
        }

        return $sheets;
    }
}

class SingleDocBlendingSheetExport implements WithEvents, WithTitle
{
    protected $docData;
    protected $block;
    protected $pageNumber;
    protected $totalPages;
    protected $sheetTitle;

    public function __construct(array $docData, array $block, int $pageNumber, int $totalPages, string $sheetTitle)
    {
        $this->docData = $docData;
        $this->block = $block;
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
                $sheet->getPageMargins()->setTop(0.4);
                $sheet->getPageMargins()->setBottom(0.4);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setRight(0.4);

                // Set Default Font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // Setup 16 Columns Widths (A to P)
                $widths = [
                    'A' => 12, // Sampling ke-
                    'B' => 14, // Vol Tangki (L)
                    'C' => 9,  // Jam
                    'D' => 14, // PIC
                    'E' => 11, // BJ (g/mL)
                    'F' => 9,  // Brix
                    'G' => 8,  // pH
                    'H' => 9,  // % NaCl
                    'I' => 13, // Viskositas (ps)
                    'J' => 9,  // Organo
                    'K' => 9,  // Aroma
                    'L' => 10, // Warna
                    'M' => 9,  // Buih
                    'N' => 8,  // Aw
                    'O' => 26, // Waktu & Adjustment
                    'P' => 24, // Disposisi/ Keterangan
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
                    $drawing->setOffsetX(15);
                    $drawing->setOffsetY(10);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue('A1', "BAS\nPT. Bumi Alam Segar");
                    $sheet->getStyle('A1')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A1')->getFont()->setSize(10)->setBold(true);
                }

                // Judul Dokumen (E1:N4)
                $sheet->mergeCells('E1:N4');
                $sheet->setCellValue('E1', "HASIL ANALISIS PROSES BLENDING");
                $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:N4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Tanggal Record Doc & Halaman (O1:P4)
                $sheet->mergeCells('O1:O2');
                $sheet->setCellValue('O1', "Tanggal Record Doc");
                $sheet->getStyle('O1')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('O1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('P1:P2');
                $sheet->setCellValueExplicit('P1', ": " . ($this->docData['tanggal_record_doc'] ?? date('Y-m-d')), DataType::TYPE_STRING);
                $sheet->getStyle('P1')->getFont()->setSize(8.5);
                $sheet->getStyle('P1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $pageStr = $this->pageNumber . ' / ' . $this->totalPages;
                $sheet->mergeCells('O3:O4');
                $sheet->setCellValue('O3', "Halaman");
                $sheet->getStyle('O3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('P3:P4');
                $sheet->setCellValueExplicit('P3', ": " . $pageStr, DataType::TYPE_STRING);
                $sheet->getStyle('P3')->getFont()->setSize(8.5);
                $sheet->getStyle('P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('O1:P4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('O2:P2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('O1:O4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }
                $sheet->getRowDimension(5)->setRowHeight(10);

                // ==========================================
                // 2. METADATA SIKLUS BLENDING (Baris 6 - 9)
                // ==========================================
                // Baris 6: Jenis Produk & Batch
                $sheet->mergeCells("A6:C6");
                $sheet->setCellValue("A6", "Jenis Produk");
                $sheet->getStyle("A6")->getFont()->setBold(true);
                $sheet->setCellValue("D6", ":");
                $sheet->mergeCells("E6:G6");
                $sheet->setCellValueExplicit("E6", (string)($this->block['jenis_produk'] ?? '-'), DataType::TYPE_STRING);

                $sheet->mergeCells("H6:I6");
                $sheet->setCellValue("H6", "Batch");
                $sheet->getStyle("H6")->getFont()->setBold(true);
                $sheet->setCellValue("J6", ":");
                $sheet->mergeCells("K6:P6");
                $sheet->setCellValueExplicit("K6", (string)($this->block['batch'] ?? '-'), DataType::TYPE_STRING);

                // Baris 7: Tanggal Produksi & No. Blending
                $sheet->mergeCells("A7:C7");
                $sheet->setCellValue("A7", "Tanggal Produksi");
                $sheet->getStyle("A7")->getFont()->setBold(true);
                $sheet->setCellValue("D7", ":");
                $sheet->mergeCells("E7:G7");
                $sheet->setCellValueExplicit("E7", (string)($this->block['tanggal_produksi'] ?? '-'), DataType::TYPE_STRING);

                $sheet->mergeCells("H7:I7");
                $sheet->setCellValue("H7", "No. Blending");
                $sheet->getStyle("H7")->getFont()->setBold(true);
                $sheet->setCellValue("J7", ":");
                $sheet->mergeCells("K7:P7");
                $sheet->setCellValueExplicit("K7", (string)($this->block['no_blending'] ?? '-'), DataType::TYPE_STRING);

                // Baris 8: Jam Produksi & Volume awal
                $sheet->mergeCells("A8:C8");
                $sheet->setCellValue("A8", "Jam Produksi");
                $sheet->getStyle("A8")->getFont()->setBold(true);
                $sheet->setCellValue("D8", ":");
                $sheet->mergeCells("E8:G8");
                $sheet->setCellValueExplicit("E8", (string)($this->block['jam_produksi'] ?? '-'), DataType::TYPE_STRING);

                $sheet->mergeCells("H8:I8");
                $sheet->setCellValue("H8", "Volume awal");
                $sheet->getStyle("H8")->getFont()->setBold(true);
                $sheet->setCellValue("J8", ":");
                $sheet->mergeCells("K8:P8");
                $sheet->setCellValueExplicit("K8", (string)($this->block['volume_awal'] ?? '-'), DataType::TYPE_STRING);

                // Baris 9: Kode Shift & Grup
                $sheet->mergeCells("A9:C9");
                $sheet->setCellValue("A9", "Kode Shift & Grup");
                $sheet->getStyle("A9")->getFont()->setBold(true);
                $sheet->setCellValue("D9", ":");
                $sheet->mergeCells("E9:P9");
                $sheet->setCellValueExplicit("E9", (string)($this->block['kode_shift_grup'] ?? '-'), DataType::TYPE_STRING);

                for ($r = 6; $r <= 9; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                    $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("H{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("K{$r}:P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Spasi Baris 10
                $sheet->getRowDimension(10)->setRowHeight(10);
                $sheet->getStyle("A5:A10")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P5:P10")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                // ==========================================
                // 3. TABLE HEADER (Baris 11 - 12)
                // ==========================================
                $th1 = 11;
                $th2 = 12;

                $sheet->mergeCells("A{$th1}:A{$th2}");
                $sheet->setCellValue("A{$th1}", "Sampling ke-");

                $sheet->mergeCells("B{$th1}:B{$th2}");
                $sheet->setCellValue("B{$th1}", "Vol Tangki (L)");

                $sheet->mergeCells("C{$th1}:D{$th1}");
                $sheet->setCellValue("C{$th1}", "Serah Terima");
                $sheet->setCellValue("C{$th2}", "Jam");
                $sheet->setCellValue("D{$th2}", "PIC");

                $sheet->mergeCells("E{$th1}:N{$th1}");
                $sheet->setCellValue("E{$th1}", "Analisis");
                $sheet->setCellValue("E{$th2}", "BJ (g/mL)");
                $sheet->setCellValue("F{$th2}", "Brix");
                $sheet->setCellValue("G{$th2}", "pH");
                $sheet->setCellValue("H{$th2}", "% NaCl");
                $sheet->setCellValue("I{$th2}", "Viskositas (ps)");
                $sheet->setCellValue("J{$th2}", "Organo");
                $sheet->setCellValue("K{$th2}", "Aroma");
                $sheet->setCellValue("L{$th2}", "Warna");
                $sheet->setCellValue("M{$th2}", "Buih");
                $sheet->setCellValue("N{$th2}", "Aw");

                $sheet->mergeCells("O{$th1}:O{$th2}");
                $sheet->setCellValue("O{$th1}", "Waktu & Adjustment");

                $sheet->mergeCells("P{$th1}:P{$th2}");
                $sheet->setCellValue("P{$th1}", "Disposisi/\nKeterangan");
                $sheet->getStyle("P{$th1}")->getAlignment()->setWrapText(true);

                $sheet->getRowDimension($th1)->setRowHeight(20);
                $sheet->getRowDimension($th2)->setRowHeight(20);
                $sheet->getStyle("A{$th1}:P{$th2}")->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle("A{$th1}:P{$th2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$th1}:P{$th2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // ==========================================
                // 4. DATA ROWS (Baris 13 - 23, Standar 11 Baris)
                // ==========================================
                $rows = $this->block['rows'] ?? [];
                $rowCount = count($rows);
                $totalRows = max(11, $rowCount);
                $dataStart = 13;

                for ($i = 0; $i < $totalRows; $i++) {
                    $dr = $dataStart + $i;
                    $row = $rows[$i] ?? null;

                    if ($row) {
                        $displaySamp = (string)($row['sampling_ke'] ?? '');
                        $displayVol = (string)($row['vol_tangki'] ?? '');
                        $displayAdj = (string)($row['waktu_adjustment'] ?? '');
                        $displayDisp = (string)($row['disposisi'] ?? '');

                        $rawBuih = (string)($row['buih'] ?? '');
                        if (strtolower(trim($rawBuih)) === 'tidak ada' || strtolower(trim($rawBuih)) === "tidak\nada") {
                            $rawBuih = "Tidak\nAda";
                        }

                        $sheet->setCellValueExplicit("A{$dr}", $displaySamp, DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("B{$dr}", $displayVol, DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("C{$dr}", (string)($row['serah_terima_jam'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("D{$dr}", (string)($row['serah_terima_pic'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("E{$dr}", (string)($row['bj'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("F{$dr}", (string)($row['brix'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("G{$dr}", (string)($row['ph'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("H{$dr}", (string)($row['nacl'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("I{$dr}", (string)($row['visco'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("J{$dr}", (string)($row['organo'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("K{$dr}", (string)($row['aroma'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("L{$dr}", (string)($row['warna'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("M{$dr}", $rawBuih, DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("N{$dr}", (string)($row['aw'] ?? ''), DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("O{$dr}", $displayAdj, DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit("P{$dr}", $displayDisp, DataType::TYPE_STRING);
                    }

                    $linesO = !empty($displayAdj) ? substr_count($displayAdj, "\n") + 1 : 1;
                    $linesP = !empty($displayDisp) ? substr_count($displayDisp, "\n") + 1 : 1;
                    $maxLines = max($linesO, $linesP, 1);
                    $rowH = $maxLines > 1 ? max(28, $maxLines * 16) : 22;

                    $sheet->getRowDimension($dr)->setRowHeight($rowH);
                    $sheet->getStyle("A{$dr}:N{$dr}")->getFont()->setSize(8.5);
                    $sheet->getStyle("O{$dr}:P{$dr}")->getFont()->setSize(8);
                    $sheet->getStyle("A{$dr}:P{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("M{$dr}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("O{$dr}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("P{$dr}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("A{$dr}:P{$dr}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 5. FOOTER (Catatan & Tanda Tangan)
                // ==========================================
                $fStart = $dataStart + $totalRows; // default: 24

                // Catatan di A..E
                $catatanEnd = $fStart + 4; // 28
                $sheet->mergeCells("A{$fStart}:E{$catatanEnd}");
                $catatanVal = $this->block['catatan'] ?: ($this->docData['catatan'] ?? '');
                $sheet->setCellValue("A{$fStart}", "Catatan :\n" . $catatanVal);
                $sheet->getStyle("A{$fStart}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$fStart}:E{$catatanEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Signatures: Disampling (F..H), Dianalisis (I..L), Dicek (M..P)
                $sheet->mergeCells("F{$fStart}:H{$fStart}");
                $sheet->setCellValue("F{$fStart}", 'Disampling oleh,');
                $sheet->mergeCells("I{$fStart}:L{$fStart}");
                $sheet->setCellValue("I{$fStart}", 'Dianalisis oleh,');
                $sheet->mergeCells("M{$fStart}:P{$fStart}");
                $sheet->setCellValue("M{$fStart}", 'Dicek oleh,');
                $sheet->getStyle("F{$fStart}:P{$fStart}")->getFont()->setSize(8.5);
                $sheet->getStyle("F{$fStart}:P{$fStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // Space Tanda Tangan (2 baris)
                $s1 = $fStart + 1;
                $s2 = $fStart + 2;
                $sheet->mergeCells("F{$s1}:H{$s2}");
                $sheet->mergeCells("I{$s1}:L{$s2}");
                $sheet->mergeCells("M{$s1}:P{$s2}");
                $sheet->getRowDimension($s1)->setRowHeight(18);
                $sheet->getRowDimension($s2)->setRowHeight(18);

                // Nama Tanda Tangan
                $sName = $fStart + 3;
                $picSampling = !empty($this->block['pic_sampling']) ? $this->block['pic_sampling'] : (!empty($this->docData['pic_sampling']) ? $this->docData['pic_sampling'] : '________________');
                $picAnalis = !empty($this->block['pic_analis']) ? $this->block['pic_analis'] : (!empty($this->docData['pic_analis']) ? $this->docData['pic_analis'] : '________________');
                $picChecker = !empty($this->block['pic_checker']) ? $this->block['pic_checker'] : (!empty($this->docData['pic_checker']) ? $this->docData['pic_checker'] : '________________');

                $sheet->mergeCells("F{$sName}:H{$sName}");
                $sheet->setCellValue("F{$sName}", "( {$picSampling} )");
                $sheet->mergeCells("I{$sName}:L{$sName}");
                $sheet->setCellValue("I{$sName}", "( {$picAnalis} )");
                $sheet->mergeCells("M{$sName}:P{$sName}");
                $sheet->setCellValue("M{$sName}", "( {$picChecker} )");
                $sheet->getStyle("F{$sName}:P{$sName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($sName)->setRowHeight(18);

                // Role Tanda Tangan
                $sRole = $fStart + 4;
                $sheet->mergeCells("F{$sRole}:H{$sRole}");
                $sheet->setCellValue("F{$sRole}", 'Produksi');
                $sheet->mergeCells("I{$sRole}:L{$sRole}");
                $sheet->setCellValue("I{$sRole}", 'QC Analis');
                $sheet->mergeCells("M{$sRole}:P{$sRole}");
                $sheet->setCellValue("M{$sRole}", 'Staff/SPV/MNG QC');
                $sheet->getStyle("F{$sRole}:P{$sRole}")->getFont()->setSize(8)->setBold(true);
                $sheet->getStyle("F{$sRole}:P{$sRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($sRole)->setRowHeight(18);

                // Border Outline Kotak Tanda Tangan
                $sheet->getStyle("F{$fStart}:H{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("I{$fStart}:L{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("M{$fStart}:P{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Kode Dokumen
                $docCodeRow = $sRole + 1;
                $sheet->mergeCells("M{$docCodeRow}:P{$docCodeRow}");
                $sheet->setCellValue("M{$docCodeRow}", 'FRM/QLB/04/104/005-01');
                $sheet->getStyle("M{$docCodeRow}")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("M{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension($docCodeRow)->setRowHeight(16);

                // Pastikan Garis Batas Kiri dan Kanan Terhubung Utuh
                $sheet->getStyle("A1:A{$docCodeRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P1:P{$docCodeRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
