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

class DocAdjustmentExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        $adjustmentSheets = !empty($this->data['adjustment_sheets']) ? $this->data['adjustment_sheets'] : [];

        if (empty($adjustmentSheets)) {
            // Fallback ke data tunggal jika belum dalam array multi-sheet
            $adjustmentSheets = [$this->data];
        }

        $totalPages = count($adjustmentSheets);
        $usedTitles = [];

        foreach ($adjustmentSheets as $idx => $sheetData) {
            $sheetNum = $idx + 1;
            $batchStr = !empty($sheetData['no_batch']) ? $sheetData['no_batch'] : (string)$sheetNum;
            $baseTitle = "Adj Batch {$batchStr}";

            // Sheet title limit is 31 chars
            $sheetTitle = substr($baseTitle, 0, 28);
            if (isset($usedTitles[$sheetTitle])) {
                $usedTitles[$sheetTitle]++;
                $sheetTitle = substr($sheetTitle, 0, 25) . '_' . $usedTitles[$sheetTitle];
            } else {
                $usedTitles[$sheetTitle] = 1;
            }

            $sheets[] = new SingleDocAdjustmentSheetExport(
                $this->data,
                $sheetData,
                $sheetNum,
                $totalPages,
                $sheetTitle
            );
        }

        return $sheets;
    }
}

class SingleDocAdjustmentSheetExport implements WithEvents, WithTitle
{
    protected $docData;
    protected $sheetData;
    protected $pageNumber;
    protected $totalPages;
    protected $sheetTitle;

    public function __construct(array $docData, array $sheetData, int $pageNumber, int $totalPages, string $sheetTitle)
    {
        $this->docData = $docData;
        $this->sheetData = $sheetData;
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

                // Page setup: Portrait A4, Fit to 1x1 Page
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(1);
                $sheet->getPageMargins()->setTop(0.4);
                $sheet->getPageMargins()->setBottom(0.4);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setRight(0.4);

                // Set default font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // Setup 16 Kolom (A s/d P)
                $widths = [
                    'A' => 12, // Bahan / Keterangan part 1
                    'B' => 10, // Bahan / Keterangan part 2
                    'C' => 10, // Bahan / Keterangan part 3
                    'D' => 12, // Bahan / Keterangan part 4 -> Total A..D = 44
                    'E' => 8,  // Adj 1 part 1
                    'F' => 8,  // Adj 1 part 2
                    'G' => 8,  // Adj 1 part 3 -> Total E..G = 24 (ADJUSMENT 1)
                    'H' => 8,  // Adj 2 part 1
                    'I' => 8,  // Adj 2 part 2
                    'J' => 8,  // Adj 2 part 3 -> Total H..J = 24 (ADJUSMENT 2)
                    'K' => 8,  // Adj 3 part 1
                    'L' => 8,  // Adj 3 part 2
                    'M' => 9,  // Adj 3 part 3 / Tanggal Record Doc label part 1
                    'N' => 9,  // Disposisi part 1 / Tanggal Record Doc label part 2
                    'O' => 10, // Disposisi part 2 / Tanggal Record Doc value part 1
                    'P' => 14, // Disposisi part 3 / Tanggal Record Doc value part 2
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
                    $drawing->setHeight(34);
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
                $sheet->setCellValue('E1', "FORM ADJUSTMENT");
                $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(12);
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

                $pageStr = $this->pageNumber . ' / ' . $this->totalPages;
                $sheet->mergeCells('M3:N4');
                $sheet->setCellValue('M3', "Halaman");
                $sheet->getStyle('M3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('M3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('O3:P4');
                $sheet->setCellValueExplicit('O3', ": " . $pageStr, DataType::TYPE_STRING);
                $sheet->getStyle('O3')->getFont()->setSize(8.5);
                $sheet->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('M1:P4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M2:P2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M1:N4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }
                
                // Notes kanan atas (Baris 5) Sesuai Google Spreadsheet Resmi
                $sheet->mergeCells('K5:P5');
                $sheet->setCellValue('K5', "Notes:\n1 lembar : 1 batch (Dissolver) atau 1 pasangan batch (Blending)\n1 lembar untuk 3x adjustment");
                $sheet->getStyle('K5')->getFont()->setSize(7.5)->setItalic(true);
                $sheet->getStyle('K5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $sheet->getRowDimension(5)->setRowHeight(24);

                // ==========================================
                // 2. METADATA INFORMASI (Rows 6-8)
                // ==========================================
                // Baris 6: Proses & Jenis Kecap
                $sheet->mergeCells('A6:B6');
                $sheet->setCellValue('A6', "Proses");
                $sheet->getStyle('A6')->getFont()->setBold(true);
                $sheet->setCellValue('C6', ":");
                $sheet->mergeCells('D6:G6');
                $sheet->setCellValueExplicit('D6', (string)($this->sheetData['proses'] ?? 'Blending'), DataType::TYPE_STRING);

                $sheet->mergeCells('H6:J6');
                $sheet->setCellValue('H6', "Jenis Kecap");
                $sheet->getStyle('H6')->getFont()->setBold(true);
                $sheet->setCellValue('K6', ":");
                $sheet->mergeCells('L6:P6');
                $sheet->setCellValueExplicit('L6', (string)($this->sheetData['jenis_kecap'] ?? ($this->docData['variant'] ?? '-')), DataType::TYPE_STRING);

                // Baris 7: No. Batch & Tanggal Produksi
                $sheet->mergeCells('A7:B7');
                $sheet->setCellValue('A7', "No. Batch");
                $sheet->getStyle('A7')->getFont()->setBold(true);
                $sheet->setCellValue('C7', ":");
                $sheet->mergeCells('D7:G7');
                $sheet->setCellValueExplicit('D7', (string)($this->sheetData['no_batch'] ?? '-'), DataType::TYPE_STRING);

                $sheet->mergeCells('H7:J7');
                $sheet->setCellValue('H7', "Tanggal Produksi");
                $sheet->getStyle('H7')->getFont()->setBold(true);
                $sheet->setCellValue('K7', ":");
                $sheet->mergeCells('L7:P7');
                $sheet->setCellValueExplicit('L7', (string)($this->sheetData['tanggal_produksi'] ?? ($this->docData['tanggal_record_doc'] ?? '-')), DataType::TYPE_STRING);

                // Baris 8: Volume Batch & Shift
                $sheet->mergeCells('A8:B8');
                $sheet->setCellValue('A8', "Volume Batch");
                $sheet->getStyle('A8')->getFont()->setBold(true);
                $sheet->setCellValue('C8', ":");
                $sheet->mergeCells('D8:G8');
                $sheet->setCellValueExplicit('D8', (string)($this->sheetData['volume_batch'] ?? '-'), DataType::TYPE_STRING);

                $sheet->mergeCells('H8:J8');
                $sheet->setCellValue('H8', "Shift");
                $sheet->getStyle('H8')->getFont()->setBold(true);
                $sheet->setCellValue('K8', ":");
                $sheet->mergeCells('L8:P8');
                $sheet->setCellValueExplicit('L8', (string)($this->sheetData['shift'] ?? '1'), DataType::TYPE_STRING);

                for ($r = 6; $r <= 8; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                    $sheet->getStyle("A{$r}:B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("H{$r}:J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("L{$r}:P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                }
                $sheet->getRowDimension(9)->setRowHeight(8);

                // Garis Batas Kiri & Kanan Metadata
                $sheet->getStyle("A5:A9")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P5:P9")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                // ==========================================
                // 3. TABEL UTAMA BAHAN & ADJUSTMENT (Rows 10-17)
                // ==========================================
                $sheet->mergeCells('A10:D10');
                $sheet->setCellValue('A10', 'BAHAN');

                $sheet->mergeCells('E10:G10');
                $sheet->setCellValue('E10', 'ADJUSMENT 1');

                $sheet->mergeCells('H10:J10');
                $sheet->setCellValue('H10', 'ADJUSMENT 2');

                $sheet->mergeCells('K10:M10');
                $sheet->setCellValue('K10', 'ADJUSMENT 3');

                $sheet->mergeCells('N10:P10');
                $sheet->setCellValue('N10', 'DISPOSISI');

                $sheet->getRowDimension(10)->setRowHeight(22);
                $sheet->getStyle('A10:P10')->getFont()->setBold(true)->setSize(9);
                $sheet->getStyle('A10:P10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A10:P10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $sheet->getStyle('A10:P10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // 9 Baris Bahan Resmi (7 Standar + 2 Tambahan/Kosong)
                $defaultBahans = [
                    'Gula Kelapa',
                    'Gula Tebu',
                    'Larutan Garam',
                    'Garam Kasar',
                    'Air',
                    'Garam Halus',
                    'Karamel (Jenis)',
                    '',
                    '',
                ];

                $bahanRows = !empty($this->sheetData['bahan_rows']) ? $this->sheetData['bahan_rows'] : [];
                $bahanMap = [];
                foreach ($bahanRows as $b) {
                    if (!empty($b['bahan'])) {
                        $bahanMap[$b['bahan']] = $b;
                    }
                }

                $startRow = 11;
                $endBahanRow = 19; // 9 baris bahan (11 - 19)

                for ($i = 0; $i < 9; $i++) {
                    $r = $startRow + $i;
                    $defaultName = $defaultBahans[$i] ?? '';
                    $bData = [];
                    if (!empty($bahanRows[$i])) {
                        $bData = $bahanRows[$i];
                    } elseif ($defaultName && isset($bahanMap[$defaultName])) {
                        $bData = $bahanMap[$defaultName];
                    }

                    $bName = !empty($bData['bahan']) ? $bData['bahan'] : $defaultName;
                    $adj1 = $this->formatVal($bData['adj1'] ?? '');
                    $adj2 = $this->formatVal($bData['adj2'] ?? '');
                    $adj3 = $this->formatVal($bData['adj3'] ?? '');

                    $sheet->getRowDimension($r)->setRowHeight(20);

                    // Bahan Name (A..D)
                    $sheet->mergeCells("A{$r}:D{$r}");
                    $sheet->setCellValue("A{$r}", $bName);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);

                    // Adj 1 (E..G)
                    $sheet->mergeCells("E{$r}:G{$r}");
                    $sheet->setCellValueExplicit("E{$r}", (string)$adj1, DataType::TYPE_STRING);
                    $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                    // Adj 2 (H..J)
                    $sheet->mergeCells("H{$r}:J{$r}");
                    $sheet->setCellValueExplicit("H{$r}", (string)$adj2, DataType::TYPE_STRING);
                    $sheet->getStyle("H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                    // Adj 3 (K..M)
                    $sheet->mergeCells("K{$r}:M{$r}");
                    $sheet->setCellValueExplicit("K{$r}", (string)$adj3, DataType::TYPE_STRING);
                    $sheet->getStyle("K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->getStyle("A{$r}:M{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 4. BOTTOM SECTION: KETERANGAN, JAM, STATUS, SIGNATURES
                // ==========================================
                // DISPOSISI mencakup baris 11 s/d 21 (9 bahan + Jam + Status)
                $disposisiEndRow = 21;
                $sheet->mergeCells("N{$startRow}:P{$disposisiEndRow}");
                $sheet->setCellValue("N{$startRow}", (string)($this->sheetData['disposisi'] ?? 'Release'));
                $sheet->getStyle("N{$startRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $sheet->getStyle("N{$startRow}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("N{$startRow}:P{$disposisiEndRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Kotak Keterangan (Baris 20-26 di kolom A..D)
                $sheet->mergeCells("A20:D26");
                $sheet->setCellValue("A20", "Keterangan :\n" . ($this->sheetData['keterangan'] ?? ''));
                $sheet->getStyle("A20")->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true)->setIndent(1);
                $sheet->getStyle("A20:D26")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 20: Baris Jam
                $sheet->getRowDimension(20)->setRowHeight(19);
                $sheet->mergeCells("E20:G20");
                $sheet->setCellValue("E20", "Jam : " . ($this->sheetData['adj1_jam'] ?? ''));
                $sheet->mergeCells("H20:J20");
                $sheet->setCellValue("H20", "Jam : " . ($this->sheetData['adj2_jam'] ?? ''));
                $sheet->mergeCells("K20:M20");
                $sheet->setCellValue("K20", "Jam : " . ($this->sheetData['adj3_jam'] ?? ''));
                $sheet->getStyle("E20:M20")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
                $sheet->getStyle("E20:M20")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 21: Baris Status
                $sheet->getRowDimension(21)->setRowHeight(19);
                $sheet->mergeCells("E21:G21");
                $sheet->setCellValue("E21", "Status : " . ($this->sheetData['adj1_status'] ?? 'Sudah dilakukan/belum'));
                $sheet->mergeCells("H21:J21");
                $sheet->setCellValue("H21", "Status : " . ($this->sheetData['adj2_status'] ?? 'Sudah dilakukan/belum'));
                $sheet->mergeCells("K21:M21");
                $sheet->setCellValue("K21", "Status : " . ($this->sheetData['adj3_status'] ?? 'Sudah dilakukan/belum'));
                $sheet->getStyle("E21:M21")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
                $sheet->getStyle("E21:M21")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 22: Baris Header Tanda Tangan (Adj 1, 2, 3, dan Disposisi)
                $sheet->getRowDimension(22)->setRowHeight(18);
                $sheet->mergeCells("E22:G22");
                $sheet->setCellValue("E22", "Tanda Tangan,");
                $sheet->mergeCells("H22:J22");
                $sheet->setCellValue("H22", "Tanda Tangan,");
                $sheet->mergeCells("K22:M22");
                $sheet->setCellValue("K22", "Tanda Tangan,");
                $sheet->mergeCells("N22:P22");
                $sheet->setCellValue("N22", "Tanda Tangan,");

                $sheet->getStyle("E22:P22")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("E22:P22")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 23-25: Ruang Tanda Tangan
                for ($sr = 23; $sr <= 25; $sr++) {
                    $sheet->getRowDimension($sr)->setRowHeight(16);
                }
                $sheet->mergeCells("E23:G25");
                $sheet->mergeCells("H23:J25");
                $sheet->mergeCells("K23:M25");
                $sheet->mergeCells("N23:P25");
                $sheet->getStyle("E23:P25")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 26: Baris Nama / Jabatan Penandatangan
                $sheet->getRowDimension(26)->setRowHeight(19);
                $petugas1 = !empty($this->sheetData['adj1_petugas']) ? $this->sheetData['adj1_petugas'] : 'Petugas Produksi';
                $petugas2 = !empty($this->sheetData['adj2_petugas']) ? $this->sheetData['adj2_petugas'] : 'Petugas Produksi';
                $petugas3 = !empty($this->sheetData['adj3_petugas']) ? $this->sheetData['adj3_petugas'] : 'Petugas Produksi';
                $analisQc = !empty($this->sheetData['qc_analis']) ? $this->sheetData['qc_analis'] : (!empty($this->docData['qc_analis']) ? $this->docData['qc_analis'] : 'Analis QC');

                $sheet->mergeCells("E26:G26");
                $sheet->setCellValue("E26", "( {$petugas1} )");
                $sheet->mergeCells("H26:J26");
                $sheet->setCellValue("H26", "( {$petugas2} )");
                $sheet->mergeCells("K26:M26");
                $sheet->setCellValue("K26", "( {$petugas3} )");
                $sheet->mergeCells("N26:P26");
                $sheet->setCellValue("N26", "( {$analisQc} )");

                $sheet->getStyle("E26:P26")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("E26:P26")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Row 27: Spacer Row
                $sheet->getRowDimension(27)->setRowHeight(8);

                // Row 28: Kode Dokumen Resmi FRM/QLB/04/104/011-00 di kanan bawah
                $sheet->mergeCells("N28:P28");
                $sheet->setCellValue("N28", 'FRM/QLB/04/104/011-00');
                $sheet->getStyle("N28")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("N28")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension(28)->setRowHeight(16);

                // Master Left & Right Border sepanjang tabel (Row 1-26)
                $sheet->getStyle("A1:A26")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P1:P26")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
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
