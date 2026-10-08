<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocPasteurisasiStorageExport implements WithEvents, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Form Pasteurisasi & ST';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Page setup: Landscape A4, Fit to 1x1 Page
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
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

                // 16 Kolom (A s/d P) dengan proporsi rapih & lapang
                $widths = [
                    'A' => 14, // Sampling
                    'B' => 9,  // Jam
                    'C' => 14, // PIC
                    'D' => 12, // BJ / BJ (g/mL)
                    'E' => 10, // Brix
                    'F' => 8,  // pH
                    'G' => 10, // Aw (Past) / % NaCl (Storage)
                    'H' => 15, // Viskositas (ps)
                    'I' => 10, // Organo
                    'J' => 10, // Aroma
                    'K' => 11, // Warna
                    'L' => 9,  // Buih
                    'M' => 10, // Endapan
                    'N' => 10, // Kristal (Storage) / part of Disposisi
                    'O' => 10, // Aw (Storage) / part of Disposisi
                    'P' => 24, // Disposisi / Keterangan
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

                // Judul Dokumen (E1:L4)
                $sheet->mergeCells('E1:L4');
                $sheet->setCellValue('E1', "HASIL ANALISIS PASTEURISASI DAN STORAGE TANK");
                $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:L4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Kotak Record Doc & Halaman (M1:P4)
                // Label M1:N2 (Width 20) & Value O1:P2 (Width 34) -> Lapang, tidak mepet/terpotong
                $sheet->mergeCells('M1:N2');
                $sheet->setCellValue('M1', "Tanggal Record Doc");
                $sheet->getStyle('M1')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('M1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('O1:P2');
                $sheet->setCellValueExplicit('O1', ": " . ($this->data['tanggal_record_doc'] ?? date('Y-m-d')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('O1')->getFont()->setSize(8.5);
                $sheet->getStyle('O1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('M3:N4');
                $sheet->setCellValue('M3', "Halaman");
                $sheet->getStyle('M3')->getFont()->setBold(true)->setSize(8.5);
                $sheet->getStyle('M3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('O3:P4');
                $sheet->setCellValueExplicit('O3', ": " . ($this->data['halaman'] ?? '1'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getStyle('O3')->getFont()->setSize(8.5);
                $sheet->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('M1:P4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M2:P2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M1:N4')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                for ($r = 1; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }
                $sheet->getRowDimension(5)->setRowHeight(10);

                // ==========================================
                // 2. METADATA INFORMASI (Rows 6-8)
                // ==========================================
                // Baris 6: Jenis Produk
                $sheet->mergeCells('A6:C6');
                $sheet->setCellValue('A6', "Jenis Produk");
                $sheet->getStyle('A6')->getFont()->setBold(true);
                $sheet->setCellValue('D6', ":");
                $sheet->mergeCells('E6:P6');
                $sheet->setCellValueExplicit('E6', (string)($this->data['jenis_produk'] ?? ($this->data['variant'] ?? '-')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                // Baris 7: Tanggal Produksi & Kode Shift & Grup
                $sheet->mergeCells('A7:C7');
                $sheet->setCellValue('A7', "Tanggal Produksi");
                $sheet->getStyle('A7')->getFont()->setBold(true);
                $sheet->setCellValue('D7', ":");
                $sheet->mergeCells('E7:G7');
                $sheet->setCellValueExplicit('E7', (string)($this->data['tanggal_produksi'] ?? ($this->data['tanggal_record_doc'] ?? '-')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $sheet->mergeCells('H7:J7');
                $sheet->setCellValue('H7', "Kode Shift & Grup");
                $sheet->getStyle('H7')->getFont()->setBold(true);
                $sheet->setCellValue('K7', ":");
                $sheet->mergeCells('L7:P7');
                $sheet->setCellValueExplicit('L7', (string)($this->data['kode_shift_grup'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                // Baris 8: Jam Produksi & Batch
                $jamStart = $this->data['jam_produksi_start'] ?? '';
                $jamEnd = $this->data['jam_produksi_end'] ?? '';
                $jamStr = ($jamStart || $jamEnd) ? "{$jamStart} s/d {$jamEnd}" : "_____ s/d _____";

                $sheet->mergeCells('A8:C8');
                $sheet->setCellValue('A8', "Jam Produksi");
                $sheet->getStyle('A8')->getFont()->setBold(true);
                $sheet->setCellValue('D8', ":");
                $sheet->mergeCells('E8:G8');
                $sheet->setCellValueExplicit('E8', (string)$jamStr, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $sheet->mergeCells('H8:J8');
                $sheet->setCellValue('H8', "Batch");
                $sheet->getStyle('H8')->getFont()->setBold(true);
                $sheet->setCellValue('K8', ":");
                $sheet->mergeCells('L8:P8');
                $sheet->setCellValueExplicit('L8', (string)($this->data['batch'] ?? ($this->data['batch_range'] ?? '-')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                for ($r = 6; $r <= 8; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                    $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("H{$r}:J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("L{$r}:P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                }
                $sheet->getRowDimension(9)->setRowHeight(10);

                // Garis Batas Kiri & Kanan Metadata yang Menghubungkan Header dan Tabel (No gaps/bolong)
                $sheet->getStyle("A5:A9")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P5:P9")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

                // ==========================================
                // 3. TABEL 1: PASTEURISASI (Rows 10-18)
                // ==========================================
                // Section Title Bar (Merged A10:P10 dengan latar rapih & border solid)
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
                $sheet->setCellValue('N11', "Disposisi /\nKeterangan");
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

                $pastRows = $this->data['pasteurisasi_rows'] ?? [];
                $pastCount = count($pastRows);
                $totalPastRows = max(6, $pastCount);

                for ($i = 0; $i < $totalPastRows; $i++) {
                    $r = 13 + $i;
                    $row = $pastRows[$i] ?? null;

                    $sheet->getRowDimension($r)->setRowHeight(20);
                    $sheet->mergeCells("N{$r}:P{$r}");

                    if ($row) {
                        $sheet->setCellValue("A{$r}", $row['sampling'] ?? ('Sampling ' . ($i + 1)));
                        $sheet->setCellValue("B{$r}", $row['jam'] ?? '');
                        $sheet->setCellValue("C{$r}", $row['pic'] ?? '');
                        $sheet->setCellValue("D{$r}", $row['bj'] ?? '');
                        $sheet->setCellValue("E{$r}", $row['brix'] ?? '');
                        $sheet->setCellValue("F{$r}", $row['ph'] ?? '');
                        $sheet->setCellValue("G{$r}", $row['aw'] ?? '');
                        $sheet->setCellValue("H{$r}", $row['viskositas'] ?? '');
                        $sheet->setCellValue("I{$r}", $row['organo'] ?? '');
                        $sheet->setCellValue("J{$r}", $row['aroma'] ?? '');
                        $sheet->setCellValue("K{$r}", $row['warna'] ?? '');
                        $sheet->setCellValue("L{$r}", $row['buih'] ?? '');
                        $sheet->setCellValue("M{$r}", $row['endapan'] ?? '');
                        $sheet->setCellValue("N{$r}", $row['disposisi'] ?? '');
                    } else {
                        $sheet->setCellValue("A{$r}", 'Sampling ' . ($i + 1));
                    }

                    $sheet->getStyle("A{$r}:M{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("N{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->getStyle("A{$r}:P{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$r}:P{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 4. TABEL 2: STORAGE TANK (Langsung Menyambung Tanpa Celah)
                // ==========================================
                $stHeaderRow = 13 + $totalPastRows;
                
                $sheet->mergeCells("A{$stHeaderRow}:D{$stHeaderRow}");
                $sheet->setCellValue("A{$stHeaderRow}", "  STORAGE TANK");
                $sheet->getStyle("A{$stHeaderRow}")->getFont()->setBold(true)->setSize(10);
                
                $sheet->mergeCells("E{$stHeaderRow}:P{$stHeaderRow}");
                $sheet->setCellValue("E{$stHeaderRow}", "KODE ST : " . ($this->data['kode_st'] ?? 'ST 01'));
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

                $stRows = $this->data['storage_rows'] ?? [];
                $stCount = count($stRows);
                $totalStRows = max(6, $stCount);
                $stDataStart = $stH2 + 1;

                for ($i = 0; $i < $totalStRows; $i++) {
                    $r = $stDataStart + $i;
                    $row = $stRows[$i] ?? null;

                    $sheet->getRowDimension($r)->setRowHeight(20);

                    if ($row) {
                        $sheet->setCellValue("A{$r}", $row['sampling'] ?? ('Sampling ' . ($i + 1)));
                        $sheet->setCellValue("B{$r}", $row['jam'] ?? '');
                        $sheet->setCellValue("C{$r}", $row['pic'] ?? '');
                        $sheet->setCellValue("D{$r}", $row['bj'] ?? '');
                        $sheet->setCellValue("E{$r}", $row['brix'] ?? '');
                        $sheet->setCellValue("F{$r}", $row['ph'] ?? '');
                        $sheet->setCellValue("G{$r}", $row['nacl'] ?? '');
                        $sheet->setCellValue("H{$r}", $row['viskositas'] ?? '');
                        $sheet->setCellValue("I{$r}", $row['organo'] ?? '');
                        $sheet->setCellValue("J{$r}", $row['aroma'] ?? '');
                        $sheet->setCellValue("K{$r}", $row['warna'] ?? '');
                        $sheet->setCellValue("L{$r}", $row['buih'] ?? '');
                        $sheet->setCellValue("M{$r}", $row['endapan'] ?? '');
                        $sheet->setCellValue("N{$r}", $row['kristal'] ?? '');
                        $sheet->setCellValue("O{$r}", $row['aw'] ?? '');
                        $sheet->setCellValue("P{$r}", $row['disposisi'] ?? '');
                    } else {
                        $sheet->setCellValue("A{$r}", 'Sampling ' . ($i + 1));
                    }

                    $sheet->getStyle("A{$r}:O{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->getStyle("A{$r}:P{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$r}:P{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // ==========================================
                // 5. CATATAN & TANDA TANGAN (Langsung Menempel di Bawah Tabel)
                // ==========================================
                $fStart = $stDataStart + $totalStRows;
                $s1 = $fStart + 1;
                $s2 = $fStart + 2;
                $sName = $fStart + 3;
                $sRole = $fStart + 4;

                // Catatan di kiri: Merged A sampai D (Kotak Utuh Tanpa Garis Potong)
                $sheet->mergeCells("A{$fStart}:D{$sRole}");
                $catatanText = "Catatan :\n" . ($this->data['catatan'] ?? '');
                $sheet->setCellValue("A{$fStart}", $catatanText);
                $sheet->getStyle("A{$fStart}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$fStart}:D{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Tanda Tangan: Sesuai Template Resmi FRM/QLB/04/104/006-01
                // 1. Baris Header Tanda Tangan
                $sheet->mergeCells("E{$fStart}:J{$fStart}");
                $sheet->setCellValue("E{$fStart}", 'Disampling oleh,');

                $sheet->mergeCells("K{$fStart}:M{$fStart}");
                $sheet->setCellValue("K{$fStart}", 'Dianalisis oleh,');

                $sheet->mergeCells("N{$fStart}:P{$fStart}");
                $sheet->setCellValue("N{$fStart}", 'Dicek oleh,');

                $sheet->getStyle("E{$fStart}:P{$fStart}")->getFont()->setSize(8.5);
                $sheet->getStyle("E{$fStart}:P{$fStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Space Tanda Tangan
                $sheet->mergeCells("E{$s1}:G{$s2}");
                $sheet->mergeCells("H{$s1}:J{$s2}");
                $sheet->mergeCells("K{$s1}:M{$s2}");
                $sheet->mergeCells("N{$s1}:P{$s2}");
                $sheet->getRowDimension($s1)->setRowHeight(18);
                $sheet->getRowDimension($s2)->setRowHeight(18);

                $picSampling = !empty($this->data['pic_sampling']) ? $this->data['pic_sampling'] : '________________';
                $picSerahTerima = !empty($this->data['pic_serah_terima']) ? $this->data['pic_serah_terima'] : '________________';
                $picAnalis = !empty($this->data['pic_analis']) ? $this->data['pic_analis'] : '________________';
                $picChecker = !empty($this->data['pic_checker']) ? $this->data['pic_checker'] : '________________';

                // 3. Nama Penandatangan
                $sheet->mergeCells("E{$sName}:G{$sName}");
                $sheet->setCellValue("E{$sName}", "( {$picSampling} )");

                $sheet->mergeCells("H{$sName}:J{$sName}");
                $sheet->setCellValue("H{$sName}", "( {$picSerahTerima} )");

                $sheet->mergeCells("K{$sName}:M{$sName}");
                $sheet->setCellValue("K{$sName}", "( {$picAnalis} )");

                $sheet->mergeCells("N{$sName}:P{$sName}");
                $sheet->setCellValue("N{$sName}", "( {$picChecker} )");

                $sheet->getStyle("E{$sName}:P{$sName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($sName)->setRowHeight(18);

                // 4. Role Penandatangan
                $sheet->mergeCells("E{$sRole}:G{$sRole}");
                $sheet->setCellValue("E{$sRole}", 'Produksi');

                $sheet->mergeCells("H{$sRole}:J{$sRole}");
                $sheet->setCellValue("H{$sRole}", 'Produksi');

                $sheet->mergeCells("K{$sRole}:M{$sRole}");
                $sheet->setCellValue("K{$sRole}", 'QC Analis');

                $sheet->mergeCells("N{$sRole}:P{$sRole}");
                $sheet->setCellValue("N{$sRole}", 'SPV/MNG QC');

                $sheet->getStyle("E{$sRole}:P{$sRole}")->getFont()->setSize(8)->setBold(true);
                $sheet->getStyle("E{$sRole}:P{$sRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($sRole)->setRowHeight(18);

                // Outline Borders Kotak Tanda Tangan
                $sheet->getStyle("E{$fStart}:J{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("E{$s1}:G{$sRole}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("K{$fStart}:M{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("N{$fStart}:P{$sRole}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Kode Dokumen Form Resmi
                $docCodeRow = $sRole + 1;
                $sheet->mergeCells("M{$docCodeRow}:P{$docCodeRow}");
                $sheet->setCellValue("M{$docCodeRow}", 'FRM/QLB/04/104/006-01');
                $sheet->getStyle("M{$docCodeRow}")->getFont()->setSize(8)->setItalic(true);
                $sheet->getStyle("M{$docCodeRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // ==========================================
                // 6. GARIS PEMBATAS KIRI & KANAN UTUH (NO GAPS / BOLONG)
                // ==========================================
                $sheet->getStyle("A1:A{$docCodeRow}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("P1:P{$docCodeRow}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
