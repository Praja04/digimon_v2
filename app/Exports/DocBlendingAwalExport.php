<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DocBlendingAwalExport implements WithEvents, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Form Blending';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set Default Font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // Setup 16 Columns Widths (A to P)
                $widths = [
                    'A' => 14, // Sampling ke-
                    'B' => 15, // Vol Tangki (L)
                    'C' => 9,  // Jam
                    'D' => 15, // PIC
                    'E' => 12, // BJ (g/mL)
                    'F' => 10, // Brix
                    'G' => 8,  // pH
                    'H' => 10, // % NaCl
                    'I' => 15, // Viskositas (ps)
                    'J' => 10, // Organo
                    'K' => 10, // Aroma
                    'L' => 12, // Warna
                    'M' => 10, // Buih
                    'N' => 8,  // Aw
                    'O' => 32, // Waktu & Adjustment
                    'P' => 20, // Disposisi/ Keterangan
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // 1. Header Dokumen (Baris 1 - 4)
                $sheet->mergeCells('A1:D4');
                $sheet->getStyle('A1:D4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                $logoPath = public_path('assets/images/logo-bas.png');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
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
                $sheet->setCellValueExplicit('P1', ": " . ($this->data['tanggal_record_doc'] ?? date('Y-m-d')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('P1')->getFont()->setSize(8.5);
                $sheet->getStyle('P1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('O3:O4');
                $sheet->setCellValue('O3', "Halaman");
                $sheet->getStyle('O3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('P3:P4');
                $sheet->setCellValueExplicit('P3', ": " . ($this->data['halaman'] ?? '1'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('P3')->getFont()->setSize(8.5);
                $sheet->getStyle('P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('O1:P4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('O2:P2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('O1:O4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }
                $sheet->getRowDimension(5)->setRowHeight(10);

                $currentRow = 6;
                $blocks = !empty($this->data['batch_blocks']) ? $this->data['batch_blocks'] : [[]];

                foreach ($blocks as $bIdx => $block) {
                    if ($bIdx > 0) {
                        $currentRow += 2;
                    }

                    // 2. Metadata (4 Baris) - Left Aligned Explicitly
                    // Baris 1: Jenis Produk & Batch
                    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", "Jenis Produk");
                    $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$currentRow}", ":");
                    $sheet->mergeCells("E{$currentRow}:G{$currentRow}");
                    $sheet->setCellValueExplicit("E{$currentRow}", (string)($block['jenis_produk'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("H{$currentRow}:I{$currentRow}");
                    $sheet->setCellValue("H{$currentRow}", "Batch");
                    $sheet->getStyle("H{$currentRow}")->getFont()->setBold(true);
                    $sheet->setCellValue("J{$currentRow}", ":");
                    $sheet->mergeCells("K{$currentRow}:P{$currentRow}");
                    $sheet->setCellValueExplicit("K{$currentRow}", (string)($block['batch'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 2: Tanggal Produksi & No. Blending
                    $r2 = $currentRow + 1;
                    $sheet->mergeCells("A{$r2}:C{$r2}");
                    $sheet->setCellValue("A{$r2}", "Tanggal Produksi");
                    $sheet->getStyle("A{$r2}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r2}", ":");
                    $sheet->mergeCells("E{$r2}:G{$r2}");
                    $sheet->setCellValueExplicit("E{$r2}", (string)($block['tanggal_produksi'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("H{$r2}:I{$r2}");
                    $sheet->setCellValue("H{$r2}", "No. Blending");
                    $sheet->getStyle("H{$r2}")->getFont()->setBold(true);
                    $sheet->setCellValue("J{$r2}", ":");
                    $sheet->mergeCells("K{$r2}:P{$r2}");
                    $sheet->setCellValueExplicit("K{$r2}", (string)($block['no_blending'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 3: Jam Produksi & Volume awal
                    $r3 = $currentRow + 2;
                    $sheet->mergeCells("A{$r3}:C{$r3}");
                    $sheet->setCellValue("A{$r3}", "Jam Produksi");
                    $sheet->getStyle("A{$r3}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r3}", ":");
                    $sheet->mergeCells("E{$r3}:G{$r3}");
                    $sheet->setCellValueExplicit("E{$r3}", (string)($block['jam_produksi'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("H{$r3}:I{$r3}");
                    $sheet->setCellValue("H{$r3}", "Volume awal");
                    $sheet->getStyle("H{$r3}")->getFont()->setBold(true);
                    $sheet->setCellValue("J{$r3}", ":");
                    $sheet->mergeCells("K{$r3}:P{$r3}");
                    $sheet->setCellValueExplicit("K{$r3}", (string)($block['volume_awal'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 4: Kode Shift & Grup
                    $r4 = $currentRow + 3;
                    $sheet->mergeCells("A{$r4}:C{$r4}");
                    $sheet->setCellValue("A{$r4}", "Kode Shift & Grup");
                    $sheet->getStyle("A{$r4}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r4}", ":");
                    $sheet->mergeCells("E{$r4}:P{$r4}");
                    $sheet->setCellValueExplicit("E{$r4}", (string)($block['kode_shift_grup'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    for ($r = $currentRow; $r <= $r4; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(18);
                        $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("E{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("H{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("K{$r}:P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Baris Spasi sebelum tabel
                    $spRow = $currentRow + 4;
                    $sheet->getRowDimension($spRow)->setRowHeight(10);

                    // Garis Batas Kiri (Kolom A) dan Kanan (Kolom P) yang Menghubungkan Header dan Tabel
                    $metaStartRow = $currentRow - 1;
                    $sheet->getStyle("A{$metaStartRow}:A{$spRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("P{$metaStartRow}:P{$spRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                    // 3. Table Header (2 Baris)
                    $th1 = $currentRow + 5;
                    $th2 = $currentRow + 6;

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

                    // 4. Data Rows (Minimal 11 baris)
                    $rows = $block['rows'] ?? [];
                    $rowCount = count($rows);
                    $totalRows = max(11, $rowCount);
                    $dataStart = $th2 + 1;

                    for ($i = 0; $i < $totalRows; $i++) {
                        $dr = $dataStart + $i;
                        $row = $rows[$i] ?? null;

                        if ($row) {
                            $sheet->setCellValue("A{$dr}", $row['sampling_ke'] ?? ($i + 1));
                            $sheet->setCellValue("B{$dr}", $row['vol_tangki'] ?? '');
                            $sheet->setCellValue("C{$dr}", $row['serah_terima_jam'] ?? '');
                            $sheet->setCellValue("D{$dr}", $row['serah_terima_pic'] ?? '');
                            $sheet->setCellValue("E{$dr}", $row['bj'] ?? '');
                            $sheet->setCellValue("F{$dr}", $row['brix'] ?? '');
                            $sheet->setCellValue("G{$dr}", $row['ph'] ?? '');
                            $sheet->setCellValue("H{$dr}", $row['nacl'] ?? '');
                            $sheet->setCellValue("I{$dr}", $row['visco'] ?? '');
                            $sheet->setCellValue("J{$dr}", $row['organo'] ?? '');
                            $sheet->setCellValue("K{$dr}", $row['aroma'] ?? '');
                            $sheet->setCellValue("L{$dr}", $row['warna'] ?? '');
                            $sheet->setCellValue("M{$dr}", $row['buih'] ?? '');
                            $sheet->setCellValue("N{$dr}", $row['aw'] ?? '');
                            $sheet->setCellValue("O{$dr}", $row['waktu_adjustment'] ?? '');
                            $sheet->setCellValue("P{$dr}", $row['disposisi'] ?? '');
                        }

                        $sheet->getRowDimension($dr)->setRowHeight(20);
                        $sheet->getStyle("A{$dr}:N{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$dr}:P{$dr}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("O{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                        $sheet->getStyle("P{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$dr}:P{$dr}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }

                    $currentRow = $dataStart + $totalRows;
                }

                // 5. Footer (Catatan & Tanda Tangan) - Langsung menempel di bawah tabel
                $fStart = $currentRow;

                // Catatan di A..E (Kotak Utuh Tanpa Garis Potong)
                $catatanEnd = $fStart + 4;
                $sheet->mergeCells("A{$fStart}:E{$catatanEnd}");
                $catatanText = "Catatan :\n" . ($this->data['catatan'] ?? '');
                $sheet->setCellValue("A{$fStart}", $catatanText);
                $sheet->getStyle("A{$fStart}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$fStart}:E{$catatanEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Signatures: Disampling (F..H), Dianalisis (I..L), Dicek (M..P)
                // Baris Header Tanda Tangan
                $sheet->mergeCells("F{$fStart}:H{$fStart}");
                $sheet->setCellValue("F{$fStart}", 'Disampling oleh,');
                $sheet->mergeCells("I{$fStart}:L{$fStart}");
                $sheet->setCellValue("I{$fStart}", 'Dianalisis oleh,');
                $sheet->mergeCells("M{$fStart}:P{$fStart}");
                $sheet->setCellValue("M{$fStart}", 'Dicek oleh,');
                $sheet->getStyle("F{$fStart}:P{$fStart}")->getFont()->setSize(8.5);
                $sheet->getStyle("F{$fStart}:P{$fStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $picSampling = !empty($this->data['pic_sampling']) ? $this->data['pic_sampling'] : '________________';
                $picAnalis = !empty($this->data['pic_analis']) ? $this->data['pic_analis'] : '________________';
                $picChecker = !empty($this->data['pic_checker']) ? $this->data['pic_checker'] : '________________';

                $sheet->mergeCells("F{$sName}:H{$sName}");
                $sheet->setCellValue("F{$sName}", "( {$picSampling} )");
                $sheet->mergeCells("I{$sName}:L{$sName}");
                $sheet->setCellValue("I{$sName}", "( {$picAnalis} )");
                $sheet->mergeCells("M{$sName}:P{$sName}");
                $sheet->setCellValue("M{$sName}", "( {$picChecker} )");
                $sheet->getStyle("F{$sName}:P{$sName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
                $sheet->getStyle("F{$sRole}:P{$sRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($sRole)->setRowHeight(18);

                // Border Outline untuk masing-masing kotak tanda tangan
                $sheet->getStyle("F{$fStart}:H{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("I{$fStart}:L{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("M{$fStart}:P{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Kode Dokumen (Baris 30)
                $docCodeRow = $sRole + 1;
                $sheet->mergeCells("M{$docCodeRow}:P{$docCodeRow}");
                $sheet->setCellValue("M{$docCodeRow}", 'FRM/QLB/04/104/005-01');
                $sheet->getStyle("M{$docCodeRow}")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("M{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Pastikan Garis Batas Kiri (Kolom A) dan Kanan (Kolom P) 100% Utuh dan Bersambung Tanpa Ada yang Bolong
                $sheet->getStyle("A1:A{$docCodeRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P1:P{$docCodeRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
