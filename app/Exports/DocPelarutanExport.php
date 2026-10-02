<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DocPelarutanExport implements WithEvents, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Form Pelarutan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);

                // Column Widths (17 columns A to Q)
                $widths = [
                    'A' => 12, // Sampling ke- P1
                    'B' => 8,  // Jam P1
                    'C' => 14, // PIC P1
                    'D' => 9,  // Brix P1
                    'E' => 9,  // %NaCl P1
                    'F' => 11, // Warna P1
                    'G' => 9,  // Organo P1
                    'H' => 28, // Waktu & Adj P1
                    'I' => 12, // Sampling ke- P2
                    'J' => 8,  // Jam P2
                    'K' => 14, // PIC P2
                    'L' => 9,  // Brix P2
                    'M' => 9,  // %NaCl P2
                    'N' => 11, // Warna P2
                    'O' => 9,  // Organo P2
                    'P' => 28, // Waktu & Adj P2
                    'Q' => 14, // Disposisi
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // 1. Header (Rows 1-4)
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
                }

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
                $sheet->setCellValueExplicit('Q1', ": " . ($this->data['tanggal_record_doc'] ?? date('Y-m-d')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('Q1')->getFont()->setSize(8.5);
                $sheet->getStyle('Q1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('P3:P4');
                $sheet->setCellValue('P3', "Halaman");
                $sheet->getStyle('P3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->mergeCells('Q3:Q4');
                $sheet->setCellValueExplicit('Q3', ": " . ($this->data['halaman'] ?? '1'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('Q3')->getFont()->setSize(8.5);
                $sheet->getStyle('Q3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('P1:Q4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('P2:Q2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('P1:P4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

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

                    // Metadata (4 Baris) dengan Penggabungan Kolom agar Tidak Terpotong
                    // Baris 1: Jenis Produk & Batch
                    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", "Jenis Produk");
                    $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$currentRow}", ":");
                    $sheet->mergeCells("E{$currentRow}:H{$currentRow}");
                    $sheet->setCellValueExplicit("E{$currentRow}", (string)($block['jenis_produk'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("I{$currentRow}:K{$currentRow}");
                    $sheet->setCellValue("I{$currentRow}", "Batch");
                    $sheet->getStyle("I{$currentRow}")->getFont()->setBold(true);
                    $sheet->setCellValue("L{$currentRow}", ":");
                    $sheet->mergeCells("M{$currentRow}:Q{$currentRow}");
                    $sheet->setCellValueExplicit("M{$currentRow}", (string)($block['batch'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 2: Tanggal Produksi & No. Dissolver
                    $r2 = $currentRow + 1;
                    $sheet->mergeCells("A{$r2}:C{$r2}");
                    $sheet->setCellValue("A{$r2}", "Tanggal Produksi");
                    $sheet->getStyle("A{$r2}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r2}", ":");
                    $sheet->mergeCells("E{$r2}:H{$r2}");
                    $sheet->setCellValueExplicit("E{$r2}", (string)($block['tanggal_produksi'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("I{$r2}:K{$r2}");
                    $sheet->setCellValue("I{$r2}", "No. Dissolver");
                    $sheet->getStyle("I{$r2}")->getFont()->setBold(true);
                    $sheet->setCellValue("L{$r2}", ":");
                    $sheet->mergeCells("M{$r2}:Q{$r2}");
                    $sheet->setCellValueExplicit("M{$r2}", (string)($block['no_dissolver'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 3: Jam Produksi & Volume
                    $r3 = $currentRow + 2;
                    $sheet->mergeCells("A{$r3}:C{$r3}");
                    $sheet->setCellValue("A{$r3}", "Jam Produksi");
                    $sheet->getStyle("A{$r3}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r3}", ":");
                    $sheet->mergeCells("E{$r3}:H{$r3}");
                    $sheet->setCellValueExplicit("E{$r3}", (string)($block['jam_produksi'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    $sheet->mergeCells("I{$r3}:K{$r3}");
                    $sheet->setCellValue("I{$r3}", "Volume");
                    $sheet->getStyle("I{$r3}")->getFont()->setBold(true);
                    $sheet->setCellValue("L{$r3}", ":");
                    $sheet->mergeCells("M{$r3}:Q{$r3}");
                    $sheet->setCellValueExplicit("M{$r3}", (string)($block['volume'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // Baris 4: Kode Shift & Grup
                    $r4 = $currentRow + 3;
                    $sheet->mergeCells("A{$r4}:C{$r4}");
                    $sheet->setCellValue("A{$r4}", "Kode Shift & Grup");
                    $sheet->getStyle("A{$r4}")->getFont()->setBold(true);
                    $sheet->setCellValue("D{$r4}", ":");
                    $sheet->mergeCells("E{$r4}:Q{$r4}");
                    $sheet->setCellValueExplicit("E{$r4}", (string)($block['kode_shift_grup'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    for ($r = $currentRow; $r <= $r4; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(18);
                        $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("E{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("I{$r}:K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("M{$r}:Q{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Baris Spasi sebelum tabel
                    $spRow = $currentRow + 4;
                    $sheet->getRowDimension($spRow)->setRowHeight(10);

                    // Garis Batas Kiri (Kolom A) dan Kanan (Kolom Q) yang Menghubungkan Header dan Tabel
                    $metaStartRow = $currentRow - 1;
                    $sheet->getStyle("A{$metaStartRow}:A{$spRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("Q{$metaStartRow}:Q{$spRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                    // Table Header (2 baris)
                    $th1 = $currentRow + 5;
                    $th2 = $currentRow + 6;

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

                    $sheet->getStyle("A{$th1}:Q{$th2}")->getFont()->setBold(true)->setSize(8.5);
                    $sheet->getStyle("A{$th1}:Q{$th2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$th1}:Q{$th2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    // Data Rows (Minimal 11 baris)
                    $rows = $block['rows'] ?? [];
                    $rowCount = count($rows);
                    $totalRows = max(11, $rowCount);
                    $dataStart = $th2 + 1;

                    for ($i = 0; $i < $totalRows; $i++) {
                        $dr = $dataStart + $i;
                        $row = $rows[$i] ?? null;

                        if ($row) {
                            $sheet->setCellValue("A{$dr}", $row['p1_sampling_ke'] ?? ($i + 1));
                            $sheet->setCellValue("B{$dr}", $row['p1_jam'] ?? '');
                            $sheet->setCellValue("C{$dr}", $row['p1_pic'] ?? '');
                            $sheet->setCellValue("D{$dr}", $row['p1_brix'] ?? '');
                            $sheet->setCellValue("E{$dr}", $row['p1_nacl'] ?? '');
                            $sheet->setCellValue("F{$dr}", $row['p1_warna'] ?? '');
                            $sheet->setCellValue("G{$dr}", $row['p1_organo'] ?? '');
                            $sheet->setCellValue("H{$dr}", $row['p1_waktu_adjustment'] ?? '');

                            $sheet->setCellValue("I{$dr}", $row['p2_sampling_ke'] ?? ($i + 1));
                            $sheet->setCellValue("J{$dr}", $row['p2_jam'] ?? '');
                            $sheet->setCellValue("K{$dr}", $row['p2_pic'] ?? '');
                            $sheet->setCellValue("L{$dr}", $row['p2_brix'] ?? '');
                            $sheet->setCellValue("M{$dr}", $row['p2_nacl'] ?? '');
                            $sheet->setCellValue("N{$dr}", $row['p2_warna'] ?? '');
                            $sheet->setCellValue("O{$dr}", $row['p2_organo'] ?? '');
                            $sheet->setCellValue("P{$dr}", $row['p2_waktu_adjustment'] ?? '');

                            $sheet->setCellValue("Q{$dr}", $row['disposisi'] ?? 'Release');
                        }

                        $sheet->getStyle("A{$dr}:G{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                        $sheet->getStyle("I{$dr}:O{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("P{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                        $sheet->getStyle("Q{$dr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$dr}:Q{$dr}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("A{$dr}:Q{$dr}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }

                    $currentRow = $dataStart + $totalRows;
                }

                // Footer - Langsung menempel di bawah tabel
                $fStart = $currentRow;
                $catatanEnd = $fStart + 4;

                $sheet->mergeCells("A{$fStart}:E{$catatanEnd}");
                $sheet->setCellValue("A{$fStart}", "Catatan :\n" . ($this->data['catatan'] ?? ''));
                $sheet->getStyle("A{$fStart}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$fStart}:E{$catatanEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$fStart}:E{$catatanEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Signatures
                $sheet->mergeCells("F{$fStart}:I{$fStart}");
                $sheet->setCellValue("F{$fStart}", 'Disampling oleh,');
                $sheet->mergeCells("J{$fStart}:M{$fStart}");
                $sheet->setCellValue("J{$fStart}", 'Dianalisis oleh,');
                $sheet->mergeCells("N{$fStart}:Q{$fStart}");
                $sheet->setCellValue("N{$fStart}", 'Dicek oleh,');
                $sheet->getStyle("F{$fStart}:Q{$fStart}")->getFont()->setSize(8.5);
                $sheet->getStyle("F{$fStart}:Q{$fStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $s1 = $fStart + 1;
                $s2 = $fStart + 2;
                $sheet->mergeCells("F{$s1}:I{$s2}");
                $sheet->mergeCells("J{$s1}:M{$s2}");
                $sheet->mergeCells("N{$s1}:Q{$s2}");

                $sName = $fStart + 3;
                $picSampling = !empty($this->data['pic_sampling']) ? $this->data['pic_sampling'] : '________________';
                $picAnalis = !empty($this->data['pic_analis']) ? $this->data['pic_analis'] : '________________';
                $picChecker = !empty($this->data['pic_checker']) ? $this->data['pic_checker'] : '________________';

                $sheet->mergeCells("F{$sName}:I{$sName}");
                $sheet->setCellValue("F{$sName}", "( {$picSampling} )");
                $sheet->mergeCells("J{$sName}:M{$sName}");
                $sheet->setCellValue("J{$sName}", "( {$picAnalis} )");
                $sheet->mergeCells("N{$sName}:Q{$sName}");
                $sheet->setCellValue("N{$sName}", "( {$picChecker} )");
                $sheet->getStyle("F{$sName}:Q{$sName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sRole = $fStart + 4;
                $sheet->mergeCells("F{$sRole}:I{$sRole}");
                $sheet->setCellValue("F{$sRole}", 'Produksi');
                $sheet->mergeCells("J{$sRole}:M{$sRole}");
                $sheet->setCellValue("J{$sRole}", 'QC Analis');
                $sheet->mergeCells("N{$sRole}:Q{$sRole}");
                $sheet->setCellValue("N{$sRole}", 'Staff/SPV/MNG QC');
                $sheet->getStyle("F{$sRole}:Q{$sRole}")->getFont()->setSize(8)->setBold(true);
                $sheet->getStyle("F{$sRole}:Q{$sRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("F{$fStart}:I{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("J{$fStart}:M{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("N{$fStart}:Q{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                $docCodeRow = $sRole + 1;
                $sheet->mergeCells("N{$docCodeRow}:Q{$docCodeRow}");
                $sheet->setCellValue("N{$docCodeRow}", 'FRM/QLB/04/104/004-01');
                $sheet->getStyle("N{$docCodeRow}")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("N{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Pastikan Garis Batas Kiri (Kolom A) dan Kanan (Kolom Q) 100% Utuh dan Bersambung Tanpa Ada yang Bolong
                $sheet->getStyle("A1:A{$docCodeRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("Q1:Q{$docCodeRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
