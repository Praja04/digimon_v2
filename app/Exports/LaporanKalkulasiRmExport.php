<?php

namespace App\Exports;

use App\Http\Controllers\Analisa\DocRmController;
use App\Http\Controllers\RMPMController;
use App\Models\IdentitasRM;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKalkulasiRmExport implements WithMultipleSheets
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Lembar Laporan / Form Dokumen Analisis
        $sheets[] = new LaporanKalkulasiSheetExport($this->identitas);

        // Sheet 2: Data Mentah Sampel
        $sheets[] = new DataSampelRmSheetExport($this->identitas);

        return $sheets;
    }
}

/**
 * Sheet 1: Hasil Kalkulasi / Laporan Analisa RM
 */
class LaporanKalkulasiSheetExport implements WithEvents, WithTitle
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function title(): string
    {
        $jenisUpper = strtoupper(trim($this->identitas->jenis ?? 'GULA TEBU'));
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA') || $jenisUpper === 'GK';
        $isGulaTebu = (str_contains($jenisUpper, 'TEBU') || $jenisUpper === 'GT') && !str_contains($jenisUpper, 'RAFINASI');
        $cleanTitle = $isGulaTebu ? 'GULA TEBU' : ($isGulaKelapa ? 'GULA KELAPA' : $jenisUpper);
        $titleStr = 'LAPORAN ' . $cleanTitle;
        return substr(preg_replace('/[\:\/\\\?\*\[\]]/', '', $titleStr), 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildReport($sheet);
            },
        ];
    }

    protected function buildReport(Worksheet $sheet): void
    {
        $sheet->setShowGridlines(true);

        $identitas = $this->identitas;
        $jenisUpper = strtoupper(trim($identitas->jenis ?? 'RM'));
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA') || $jenisUpper === 'GK';
        $isGulaTebu = (str_contains($jenisUpper, 'TEBU') || $jenisUpper === 'GT') && !str_contains($jenisUpper, 'RAFINASI');
        $isGulaSpecial = ($isGulaKelapa || $isGulaTebu);

        // Khusus Gula Tebu & Gula Kelapa: Buat Laporan Kalkulasi Statistik
        if ($isGulaSpecial) {
            $this->buildGulaKalkulasiReport($sheet);
        } else {
            // Semua bahan lainnya (Garam, SKM, Karamel, Minor Ingredient, Glukosa Cair/HFCS, Gula Rafinasi/lainnya):
            // Buat Form Laporan Dokumen Analisis persis template Excel resmi
            $this->buildOfficialTemplateReport($sheet);
        }
    }

    /**
     * Format Laporan Kalkulasi Statistik khusus Gula Tebu & Gula Kelapa
     */
    protected function buildGulaKalkulasiReport(Worksheet $sheet): void
    {
        $identitas = $this->identitas;
        $jenisUpper = strtoupper(trim($identitas->jenis ?? 'GULA TEBU'));
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA') || $jenisUpper === 'GK';
        $isGulaTebu = (str_contains($jenisUpper, 'TEBU') || $jenisUpper === 'GT') && !str_contains($jenisUpper, 'RAFINASI');
        $titleMain = $isGulaTebu ? 'GULA TEBU' : 'GULA KELAPA';

        $docCtrl = new DocRmController();
        $docData = $docCtrl->formatDocumentData($identitas);

        $sampleCount = $docData['sample_count'];
        $sampleRows = $docData['sample_rows'];

        // Data numerik
        $phVals = collect($sampleRows)->pluck('ph')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $brixVals = collect($sampleRows)->pluck('brix')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $kotoranVals = collect($sampleRows)->pluck('kotoran')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $kaVals = collect($sampleRows)->pluck('ka')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $organoVals = collect($sampleRows)->pluck('organo')->filter(fn($v) => !empty($v) && $v !== '-')->map(fn($v) => trim((string)$v))->values();

        // Header Atas
        $tglIncoming = $docData['tgl_incoming'];
        $vendor = $docData['vendor'];
        $spb = $docData['spb'];
        $standartBps = $docData['standart_bps'];
        $nopol = $docData['nopol'];
        $qty = $docData['qty'];

        $sheet->setCellValue('D1', $titleMain);
        $sheet->mergeCells('D1:G1');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B3', 'Tgl Incoming');
        $sheet->setCellValue('C3', $tglIncoming);

        $sheet->setCellValue('B4', 'Vendor');
        $sheet->setCellValue('C4', $vendor);

        $sheet->setCellValue('B5', 'SPB');
        $sheet->setCellValue('C5', $spb);

        $sheet->setCellValue('B6', 'Standart BPS');
        $sheet->setCellValue('C6', $standartBps);

        $cleanBps = strtolower(trim((string)$standartBps));
        if ($cleanBps === 'kritis') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF990000');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        } elseif ($cleanBps === 'standar') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF15803D');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        }

        $sheet->setCellValue('G3', 'Nopol');
        $sheet->setCellValue('H3', $nopol);

        $sheet->setCellValue('G4', 'Qty (kg)');
        $sheet->setCellValue('H4', $qty);

        $sheet->setCellValue('G5', 'Jumlah Sampel');
        $sheet->setCellValue('H5', $sampleCount);
        $sheet->getStyle('H5')->getFont()->setBold(true);

        // Styling Color Constants
        $headerFill = 'FFD9E1F2';
        $subHeaderFill = 'FFB4C6E7';
        $yellowFill = 'FFFFFF00';
        $greenFill = 'FFC6EFCE';

        $sheet->setCellValue('B8', 'Parameter');
        $sheet->mergeCells('B8:C8');
        $sheet->setCellValue('D8', 'Awal');
        $sheet->setCellValue('E8', 'Keterangan');
        $sheet->getStyle('B8:E8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('B8:E8')->getFont()->setBold(true);
        $sheet->getStyle('B8:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('G8', 'Parameter');
        $sheet->setCellValue('H8', 'Awal');
        $sheet->setCellValue('I8', 'Keterangan');
        $sheet->getStyle('G8:I8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('G8:I8')->getFont()->setBold(true);
        $sheet->getStyle('G8:I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Standar Mutu
        $phMinSpec = $docData['ph_min_std'] ?? ($isGulaKelapa ? 5.30 : 5.50);
        $phMaxSpec = $docData['ph_max_std'] ?? 6.50;
        $phStdText = 'pH (STD : ' . ($docData['ph_std_label'] ?? (number_format($phMinSpec, 2, ',', '.') . ' - ' . number_format($phMaxSpec, 2, ',', '.'))) . ')';
        $brixStdText = 'Brix (STD : ' . ($docData['brix_std_label'] ?? 'Min, 8,7') . ')';
        $kotStdText = '%Kotoran (STD : ' . ($docData['kot_std_label'] ?? 'Max, 10% w/w') . ')';
        $kaMaxSpec = $docData['ka_max_std'] ?? 8.0;
        $kaStdText = '%Kadar Air (STD : ' . ($docData['ka_std_label'] ?? ('Max, ' . number_format($kaMaxSpec, 0, ',', '.') . '%')) . ')';

        // --- pH ---
        $sheet->setCellValue('B9', $phStdText);
        $sheet->mergeCells('B9:E9');
        $sheet->getStyle('B9:E9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('B9:E9')->getFont()->setBold(true);

        $avgPh = $phVals->isNotEmpty() ? $phVals->avg() : null;
        $minPh = $phVals->isNotEmpty() ? $phVals->min() : null;
        $maxPh = $phVals->isNotEmpty() ? $phVals->max() : null;

        $sheet->setCellValue('B10', 'Avg,'); $sheet->mergeCells('B10:C10');
        $sheet->setCellValue('D10', $avgPh !== null ? number_format($avgPh, 2, ',', '.') : '-');

        $sheet->setCellValue('B11', 'Min,'); $sheet->mergeCells('B11:C11');
        $sheet->setCellValue('D11', $minPh !== null ? number_format($minPh, 2, ',', '.') : '-');

        $sheet->setCellValue('B12', 'Max,'); $sheet->mergeCells('B12:C12');
        $sheet->setCellValue('D12', $maxPh !== null ? number_format($maxPh, 2, ',', '.') : '-');

        if ($avgPh !== null && ($avgPh < $phMinSpec || $avgPh > $phMaxSpec)) {
            $sheet->getStyle('D10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $totalPhSampel = $phVals->count() ?: 1;
        $cntPhUnder = $phVals->filter(fn($v) => $v < $phMinSpec)->count();
        $pctPhUnder = ($cntPhUnder / $totalPhSampel) * 100;
        $cntPhInSpec = $phVals->filter(fn($v) => $v >= $phMinSpec && $v <= $phMaxSpec)->count();
        $pctPhInSpec = ($cntPhInSpec / $totalPhSampel) * 100;

        if ($isGulaKelapa) {
            $cntPhOver = $phVals->filter(fn($v) => $v > $phMaxSpec)->count();
            $pctPhOver = ($cntPhOver / $totalPhSampel) * 100;

            $sheet->setCellValue('B13', '%Qty Sample');
            $sheet->mergeCells('B13:B15');
            $sheet->getStyle('B13:B15')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('C13', 'pH Under ' . number_format($phMinSpec, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D13', number_format($pctPhUnder, 0) . '%');
            if ($pctPhUnder > 0) $sheet->getStyle('D13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

            $sheet->setCellValue('C14', 'pH Sesuai Standar (' . number_format($phMinSpec, 2, ',', '.') . ' - ' . number_format($phMaxSpec, 2, ',', '.') . ') (#)');
            $sheet->setCellValue('D14', number_format($pctPhInSpec, 0) . '%');
            if ($pctPhInSpec > 0) $sheet->getStyle('D14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

            $sheet->setCellValue('C15', 'pH Over ' . number_format($phMaxSpec, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D15', number_format($pctPhOver, 0) . '%');
            if ($pctPhOver > 0) $sheet->getStyle('D15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

            $rBrix = 16;
        } else {
            $cntPhOver65_70 = $phVals->filter(fn($v) => $v > 6.50 && $v <= 7.00)->count();
            $pctPhOver65_70 = ($cntPhOver65_70 / $totalPhSampel) * 100;
            $cntPhOver70 = $phVals->filter(fn($v) => $v > 7.00)->count();
            $pctPhOver70 = ($cntPhOver70 / $totalPhSampel) * 100;

            $sheet->setCellValue('B13', '%Qty Sample');
            $sheet->mergeCells('B13:B16');
            $sheet->getStyle('B13:B16')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('C13', 'pH Under 5,50 (#)');
            $sheet->setCellValue('D13', number_format($pctPhUnder, 0) . '%');
            if ($pctPhUnder > 0) $sheet->getStyle('D13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

            $sheet->setCellValue('C14', 'pH Sesuai Standar (5,50 - 6,50) (#)');
            $sheet->setCellValue('D14', number_format($pctPhInSpec, 0) . '%');
            if ($pctPhInSpec > 0) $sheet->getStyle('D14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

            $sheet->setCellValue('C15', 'pH Over 6,50-7,00 (#)');
            $sheet->setCellValue('D15', number_format($pctPhOver65_70, 0) . '%');
            if ($pctPhOver65_70 > 0) $sheet->getStyle('D15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

            $sheet->setCellValue('C16', 'pH Over 7,00 (#)');
            $sheet->setCellValue('D16', number_format($pctPhOver70, 0) . '%');
            if ($pctPhOver70 > 0) $sheet->getStyle('D16')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

            $rBrix = 17;
        }

        // --- BRIX ---
        $sheet->setCellValue("B{$rBrix}", $brixStdText);
        $sheet->mergeCells("B{$rBrix}:E{$rBrix}");
        $sheet->getStyle("B{$rBrix}:E{$rBrix}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("B{$rBrix}:E{$rBrix}")->getFont()->setBold(true);

        $avgBrix = $brixVals->isNotEmpty() ? $brixVals->avg() : null;
        $minBrix = $brixVals->isNotEmpty() ? $brixVals->min() : null;
        $maxBrix = $brixVals->isNotEmpty() ? $brixVals->max() : null;

        $sheet->setCellValue('B' . ($rBrix + 1), 'Avg,'); $sheet->mergeCells('B' . ($rBrix + 1) . ':C' . ($rBrix + 1));
        $sheet->setCellValue('D' . ($rBrix + 1), $avgBrix !== null ? number_format($avgBrix, 1, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rBrix + 2), 'Min,'); $sheet->mergeCells('B' . ($rBrix + 2) . ':C' . ($rBrix + 2));
        $sheet->setCellValue('D' . ($rBrix + 2), $minBrix !== null ? number_format($minBrix, 1, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rBrix + 3), 'Max,'); $sheet->mergeCells('B' . ($rBrix + 3) . ':C' . ($rBrix + 3));
        $sheet->setCellValue('D' . ($rBrix + 3), $maxBrix !== null ? number_format($maxBrix, 1, ',', '.') : '-');

        if ($avgBrix !== null && $avgBrix < 8.7) {
            $sheet->getStyle('D' . ($rBrix + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $totalBrixSampel = $brixVals->count() ?: 1;
        $cntBrixInSpec = $brixVals->filter(fn($v) => $v >= 8.7)->count();
        $pctBrixInSpec = ($cntBrixInSpec / $totalBrixSampel) * 100;
        $cntBrixUnder = $brixVals->filter(fn($v) => $v < 8.7)->count();
        $pctBrixUnder = ($cntBrixUnder / $totalBrixSampel) * 100;

        $sheet->setCellValue('B' . ($rBrix + 4), '%Qty Sample');
        $sheet->mergeCells('B' . ($rBrix + 4) . ':B' . ($rBrix + 5));
        $sheet->getStyle('B' . ($rBrix + 4) . ':B' . ($rBrix + 5))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C' . ($rBrix + 4), 'Brix Sesuai Standar (>= 8,7) (#)');
        $sheet->setCellValue('D' . ($rBrix + 4), number_format($pctBrixInSpec, 0) . '%');
        if ($pctBrixInSpec > 0) $sheet->getStyle('D' . ($rBrix + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

        $sheet->setCellValue('C' . ($rBrix + 5), 'Brix Under 8,7 (#)');
        $sheet->setCellValue('D' . ($rBrix + 5), number_format($pctBrixUnder, 0) . '%');
        if ($pctBrixUnder > 0) $sheet->getStyle('D' . ($rBrix + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

        // --- %KOTORAN ---
        $rKot = $rBrix + 6;
        $sheet->setCellValue("B{$rKot}", $kotStdText);
        $sheet->mergeCells("B{$rKot}:E{$rKot}");
        $sheet->getStyle("B{$rKot}:E{$rKot}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("B{$rKot}:E{$rKot}")->getFont()->setBold(true);

        $avgKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->avg() : null;
        $minKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->min() : null;
        $maxKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->max() : null;

        $sheet->setCellValue('B' . ($rKot + 1), 'Avg,'); $sheet->mergeCells('B' . ($rKot + 1) . ':C' . ($rKot + 1));
        $sheet->setCellValue('D' . ($rKot + 1), $avgKotoran !== null ? number_format($avgKotoran, 2, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rKot + 2), 'Min,'); $sheet->mergeCells('B' . ($rKot + 2) . ':C' . ($rKot + 2));
        $sheet->setCellValue('D' . ($rKot + 2), $minKotoran !== null ? number_format($minKotoran, 2, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rKot + 3), 'Max,'); $sheet->mergeCells('B' . ($rKot + 3) . ':C' . ($rKot + 3));
        $sheet->setCellValue('D' . ($rKot + 3), $maxKotoran !== null ? number_format($maxKotoran, 2, ',', '.') : '-');

        $totalKotSampel = $kotoranVals->count() ?: 1;
        $cntKotInSpec = $kotoranVals->filter(fn($v) => $v <= 10.0)->count();
        $pctKotInSpec = ($cntKotInSpec / $totalKotSampel) * 100;
        $cntKot10_14 = $kotoranVals->filter(fn($v) => $v > 10.0 && $v <= 14.0)->count();
        $pctKot10_14 = ($cntKot10_14 / $totalKotSampel) * 100;
        $cntKotOver14 = $kotoranVals->filter(fn($v) => $v > 14.0)->count();
        $pctKotOver14 = ($cntKotOver14 / $totalKotSampel) * 100;

        $sheet->setCellValue('B' . ($rKot + 4), '%Qty Sample');
        $sheet->mergeCells('B' . ($rKot + 4) . ':B' . ($rKot + 6));
        $sheet->getStyle('B' . ($rKot + 4) . ':B' . ($rKot + 6))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C' . ($rKot + 4), 'Kotoran Sesuai Standar (<= 10%) (#)');
        $sheet->setCellValue('D' . ($rKot + 4), number_format($pctKotInSpec, 0) . '%');
        if ($pctKotInSpec > 0) $sheet->getStyle('D' . ($rKot + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

        $sheet->setCellValue('C' . ($rKot + 5), '%Qty Sample Kotoran Over 10-14%(#)');
        $sheet->setCellValue('D' . ($rKot + 5), number_format($pctKot10_14, 0) . '%');
        if ($pctKot10_14 > 0) $sheet->getStyle('D' . ($rKot + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

        $sheet->setCellValue('C' . ($rKot + 6), '%Qty Sample Kotoran Over 14%(#)');
        $sheet->setCellValue('D' . ($rKot + 6), number_format($pctKotOver14, 0) . '%');
        if ($pctKotOver14 > 0) $sheet->getStyle('D' . ($rKot + 6))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

        $leftTableEndRow = $rKot + 6;

        // --- TABEL KANAN: ORGANO ---
        $sheet->setCellValue('G9', 'Organo');
        $sheet->mergeCells('G9:I9');
        $sheet->getStyle('G9:I9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('G9:I9')->getFont()->setBold(true);

        $rOrg = 10;
        foreach ($docData['organo_items'] ?? [] as $org) {
            $sheet->setCellValue("G{$rOrg}", $org['label']);
            $sheet->setCellValue("H{$rOrg}", $org['pct'] . '%');
            $sheet->setCellValue("I{$rOrg}", $org['ket']);

            if ($org['key'] === 'ok' && $org['pct'] > 0) {
                $sheet->getStyle("H{$rOrg}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
            }
            $rOrg++;
        }

        // --- %KADAR AIR ---
        $rKa = $rOrg;
        $sheet->setCellValue("G{$rKa}", $kaStdText);
        $sheet->mergeCells("G{$rKa}:I{$rKa}");
        $sheet->getStyle("G{$rKa}:I{$rKa}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("G{$rKa}:I{$rKa}")->getFont()->setBold(true);

        $avgKa = $kaVals->isNotEmpty() ? $kaVals->avg() : null;
        $minKa = $kaVals->isNotEmpty() ? $kaVals->min() : null;
        $maxKa = $kaVals->isNotEmpty() ? $kaVals->max() : null;

        $sheet->setCellValue('G' . ($rKa + 1), 'Avg,');
        $sheet->setCellValue('H' . ($rKa + 1), $avgKa !== null ? number_format($avgKa, 2, ',', '.') : '-');

        $sheet->setCellValue('G' . ($rKa + 2), 'Min,');
        $sheet->setCellValue('H' . ($rKa + 2), $minKa !== null ? number_format($minKa, 2, ',', '.') : '-');

        $sheet->setCellValue('G' . ($rKa + 3), 'Max,');
        $sheet->setCellValue('H' . ($rKa + 3), $maxKa !== null ? number_format($maxKa, 2, ',', '.') : '-');

        $totalKaSampel = $kaVals->count() ?: 1;
        $cntKaInSpec = $kaVals->filter(fn($v) => $v <= $kaMaxSpec)->count();
        $pctKaInSpec = ($cntKaInSpec / $totalKaSampel) * 100;
        $cntKaOver = $kaVals->filter(fn($v) => $v > $kaMaxSpec)->count();
        $pctKaOver = ($cntKaOver / $totalKaSampel) * 100;

        $sheet->setCellValue('G' . ($rKa + 4), '%Qty Sample KA Sesuai Standar (<= ' . number_format($kaMaxSpec, 0, ',', '.') . '%) (#)');
        $sheet->setCellValue('H' . ($rKa + 4), number_format($pctKaInSpec, 2, ',', '.') . '%');
        if ($pctKaInSpec > 0) $sheet->getStyle('H' . ($rKa + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

        $sheet->setCellValue('G' . ($rKa + 5), '%Qty Sample KA Over ' . number_format($kaMaxSpec, 0, ',', '.') . '% (#)');
        $sheet->setCellValue('H' . ($rKa + 5), number_format($pctKaOver, 2, ',', '.') . '%');
        if ($pctKaOver > 0) $sheet->getStyle('H' . ($rKa + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

        // --- RASA KHAS ---
        $rRasa = $rKa + 6;
        $displayJenisRasa = $isGulaTebu ? 'Gula Tebu' : 'Gula Kelapa';
        $sheet->setCellValue("G{$rRasa}", 'Rasa Khas ' . $displayJenisRasa);
        $sheet->mergeCells("G{$rRasa}:I{$rRasa}");
        $sheet->getStyle("G{$rRasa}:I{$rRasa}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("G{$rRasa}:I{$rRasa}")->getFont()->setBold(true);

        $murniPct = $docData['murni_pct'] ?? 0;
        $campuranPct = $docData['campuran_pct'] ?? 0;

        $sheet->setCellValue('G' . ($rRasa + 1), 'Murni');
        $sheet->setCellValue('H' . ($rRasa + 1), $murniPct . '%');
        $sheet->setCellValue('I' . ($rRasa + 1), $docData['ket_murni'] ?? 'Total poin organo dari no 1 & 3-6');
        if ($murniPct > 0) $sheet->getStyle('H' . ($rRasa + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);

        $sheet->setCellValue('G' . ($rRasa + 2), 'Campuran');
        $sheet->setCellValue('H' . ($rRasa + 2), $campuranPct . '%');
        $sheet->setCellValue('I' . ($rRasa + 2), $docData['ket_campuran'] ?? 'Rasa campuran dan pahit');
        if ($campuranPct > 0) $sheet->getStyle('H' . ($rRasa + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);

        $rightTableEndRow = $rRasa + 2;

        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle("B8:E{$leftTableEndRow}")->applyFromArray($thinBorder);
        $sheet->getStyle("D8:E{$leftTableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("G8:I{$rightTableEndRow}")->applyFromArray($thinBorder);
        $sheet->getStyle("H8:H{$rightTableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(4);
        $sheet->getColumnDimension('G')->setWidth(38);
        $sheet->getColumnDimension('H')->setWidth(14);
        $sheet->getColumnDimension('I')->setWidth(36);

        $maxRow = max($leftTableEndRow, $rightTableEndRow);
        $sheet->getStyle("A1:I{$maxRow}")->getFont()->setName('Calibri')->setSize(10);
        $sheet->getStyle('D1')->getFont()->setSize(14)->setBold(true);
    }

    /**
     * Format Laporan Dokumen Analisis persis Template Excel Resmi
     * untuk Garam, SKM, Karamel, Minor Ingredient, Gula Cair/HFCS, Gula Rafinasi/Lainnya
     */
    protected function buildOfficialTemplateReport(Worksheet $sheet): void
    {
        $docCtrl = new DocRmController();
        $docData = $docCtrl->formatDocumentData($this->identitas);

        if (!$docData) {
            return;
        }

        $headerFill = 'FFD9E1F2';
        $subHeaderFill = 'FFE2E8F0';
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // 1. Title
        $sheet->setCellValue('D1', $docData['title']);
        $sheet->mergeCells('D1:N1');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Metadata Grid (Rows 5-9)
        $sheet->setCellValue('B5', 'Nama Bahan');
        $sheet->setCellValue('D5', ': ' . $docData['nama_bahan']);
        $sheet->setCellValue('K5', 'Jam Kedatangan');
        $sheet->setCellValue('M5', ': ' . $docData['jam_kedatangan']);

        $sheet->setCellValue('B6', 'Tanggal Kedatangan');
        $sheet->setCellValue('D6', ': ' . $docData['tgl_incoming']);
        $sheet->setCellValue('K6', 'No. SPB');
        $sheet->setCellValue('M6', ': ' . $docData['spb']);

        $sheet->setCellValue('B7', 'Supplier & Manufacturing');
        $sheet->setCellValue('D7', ': ' . $docData['vendor']);
        $sheet->setCellValue('K7', 'Lot / Batch');
        $sheet->setCellValue('M7', ': ' . $docData['lot_batch']);

        $sheet->setCellValue('B8', 'Negara / Kota Asal');
        $sheet->setCellValue('D8', ': ' . $docData['asal_bahan']);
        $sheet->setCellValue('K8', 'Jumlah Kedatangan');
        $sheet->setCellValue('M8', ': ' . $docData['qty']);

        $sheet->setCellValue('B9', 'No. Mobil');
        $sheet->setCellValue('D9', ': ' . $docData['nopol']);
        $sheet->setCellValue('K9', 'Exp. Date');
        $sheet->setCellValue('M9', ': ' . $docData['exp_date']);

        $sheet->getStyle('B5:B9')->getFont()->setBold(true);
        $sheet->getStyle('K5:K9')->getFont()->setBold(true);

        // 3. Left Block: Pengecekan Kedatangan & Pengecekan Fisik (Row 11)
        $sheet->setCellValue('B11', 'Pengecekan Kedatangan');
        $sheet->mergeCells('B11:D11');
        $sheet->getStyle('B11:D11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('B11:D11')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B11:D11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fisikHeaderTitle = ($docData['kemasan_type'] === 'cair') ? 'Pengecekan Fisik Material' : 'Pengecekan Fisik';
        $sheet->setCellValue('E11', $fisikHeaderTitle);
        $sheet->mergeCells('E11:H11');
        $sheet->getStyle('E11:H11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('E11:H11')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('E11:H11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subheaders Row 12
        $sheet->setCellValue('B12', 'Kondisi Mobil');
        $sheet->mergeCells('B12:D12');
        $sheet->getStyle('B12:D12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('B12:D12')->getFont()->setBold(true);
        $sheet->getStyle('B12:D12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($docData['kemasan_type'] === 'cair') {
            $sheet->setCellValue('E12', 'Spesifikasi'); $sheet->mergeCells('E12:F12');
            $sheet->setCellValue('G12', 'Kondisi'); $sheet->mergeCells('G12:H12');
            $sheet->getStyle('E12:H12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
            $sheet->getStyle('E12:H12')->getFont()->setBold(true);
            $sheet->getStyle('E12:H12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } elseif ($docData['kemasan_type'] === 'drum') {
            $sheet->setCellValue('E12', 'Kriteria');
            $sheet->setCellValue('F12', 'Drum');
            $sheet->setCellValue('G12', '%'); $sheet->mergeCells('G12:H12');
            $sheet->getStyle('E12:H12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
            $sheet->getStyle('E12:H12')->getFont()->setBold(true);
            $sheet->getStyle('E12:H12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('E12', 'Kriteria / Fisik Zak');
            $sheet->setCellValue('F12', 'Qty Zak');
            $sheet->setCellValue('G12', '%'); $sheet->mergeCells('G12:H12');
            $sheet->getStyle('E12:H12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
            $sheet->getStyle('E12:H12')->getFont()->setBold(true);
            $sheet->getStyle('E12:H12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Subheader Row 13 for Kondisi Mobil
        $sheet->setCellValue('B13', 'Spesifikasi');
        $sheet->setCellValue('C13', 'Kondisi'); $sheet->mergeCells('C13:D13');
        $sheet->getStyle('B13:D13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('B13:D13')->getFont()->setBold(true);
        $sheet->getStyle('C13:D13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Rows 14-19: Kondisi Mobil
        $mobilSpecs = [
            ['Bersih', $docData['kondisi_mobil']['bersih'] ?? '-'],
            ['Kering', $docData['kondisi_mobil']['kering'] ?? '-'],
            ['Tidak ada benda asing', $docData['kondisi_mobil']['benda_asing'] ?? '-'],
            ['Tidak berbau', $docData['kondisi_mobil']['berbau'] ?? '-'],
            ['Tidak cacat/bolong', $docData['kondisi_mobil']['cacat'] ?? '-'],
            ['Segel container', $docData['kondisi_mobil']['segel'] ?? '-'],
        ];

        $rMobil = 14;
        foreach ($mobilSpecs as $spec) {
            $sheet->setCellValue("B{$rMobil}", $spec[0]);
            $sheet->setCellValue("C{$rMobil}", $spec[1]);
            $sheet->mergeCells("C{$rMobil}:D{$rMobil}");
            $sheet->getStyle("C{$rMobil}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$rMobil}")->getFont()->setBold(true);
            $rMobil++;
        }

        $sheet->setCellValue("B{$rMobil}", 'Keterangan: ' . ($docData['kondisi_mobil']['keterangan'] ?? '-'));
        $sheet->mergeCells("B{$rMobil}:D{$rMobil}");
        $rMobil++;
        $sheet->setCellValue("B{$rMobil}", 'Note: √= Ya       ×= Tidak');
        $sheet->mergeCells("B{$rMobil}:D{$rMobil}");
        $sheet->getStyle("B{$rMobil}")->getFont()->setItalic(true);

        // Rows Fisik Kemasan (Rows 13-20 on Cols E-H)
        $rFisik = 13;
        if ($docData['kemasan_type'] === 'cair') {
            $sheet->setCellValue("E{$rFisik}", 'Bening, tidak berbau'); $sheet->mergeCells("E{$rFisik}:F{$rFisik}");
            $sheet->setCellValue("G{$rFisik}", '√'); $sheet->mergeCells("G{$rFisik}:H{$rFisik}");
            $sheet->getStyle("G{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rFisik++;
            $sheet->setCellValue("E{$rFisik}", 'Tidak berbuih'); $sheet->mergeCells("E{$rFisik}:F{$rFisik}");
            $sheet->setCellValue("G{$rFisik}", '√'); $sheet->mergeCells("G{$rFisik}:H{$rFisik}");
            $sheet->getStyle("G{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rFisik++;
            $sheet->setCellValue("E{$rFisik}", 'Note: √= Ya       ×= Tidak'); $sheet->mergeCells("E{$rFisik}:H{$rFisik}");
            $sheet->getStyle("E{$rFisik}")->getFont()->setItalic(true);
            $rFisik++;
        } elseif ($docData['kemasan_type'] === 'drum') {
            $drumItems = [
                ['Kotor', $docData['fisik_kemasan']['kotor'] ?? '-'],
                ['Kembung', $docData['fisik_kemasan']['kembung'] ?? '-'],
                ['Rusak', $docData['fisik_kemasan']['rusak'] ?? '-'],
            ];
            foreach ($drumItems as $it) {
                $sheet->setCellValue("E{$rFisik}", $it[0]);
                $sheet->setCellValue("F{$rFisik}", $it[1]);
                $sheet->setCellValue("G{$rFisik}", '-'); $sheet->mergeCells("G{$rFisik}:H{$rFisik}");
                $sheet->getStyle("F{$rFisik}:G{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rFisik++;
            }
            $sheet->setCellValue("E{$rFisik}", 'Total Bagus');
            $sheet->setCellValue("F{$rFisik}", $docData['fisik_kemasan']['total_bagus'] ?? '-'); $sheet->mergeCells("F{$rFisik}:H{$rFisik}");
            $sheet->getStyle("E{$rFisik}:F{$rFisik}")->getFont()->setBold(true);
            $sheet->getStyle("F{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rFisik++;
        } else {
            $zakItems = [
                ['Kotor', $docData['fisik_kemasan']['kotor'] ?? '-'],
                ['Berair / Basah', $docData['fisik_kemasan']['berair'] !== '-' ? $docData['fisik_kemasan']['berair'] : ($docData['fisik_kemasan']['basah'] ?? '-')],
                ['Warna', $docData['fisik_kemasan']['warna'] ?? '-'],
                ['Campuran', $docData['fisik_kemasan']['campuran'] ?? '-'],
                ['Aroma', $docData['fisik_kemasan']['aroma'] ?? '-'],
                ['Rusak / Robek', $docData['fisik_kemasan']['rusak'] ?? '-'],
            ];
            foreach ($zakItems as $it) {
                $sheet->setCellValue("E{$rFisik}", $it[0]);
                $sheet->setCellValue("F{$rFisik}", $it[1]);
                $sheet->setCellValue("G{$rFisik}", '-'); $sheet->mergeCells("G{$rFisik}:H{$rFisik}");
                $sheet->getStyle("F{$rFisik}:G{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rFisik++;
            }
            $sheet->setCellValue("E{$rFisik}", 'Total Bagus');
            $sheet->setCellValue("F{$rFisik}", $docData['fisik_kemasan']['total_bagus'] ?? '-'); $sheet->mergeCells("F{$rFisik}:H{$rFisik}");
            $sheet->getStyle("E{$rFisik}:F{$rFisik}")->getFont()->setBold(true);
            $sheet->getStyle("F{$rFisik}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rFisik++;
        }

        // Dokumen & Identitas (Rows 22-30 on Cols B-D)
        $rDok = 22;
        $sheet->setCellValue("B{$rDok}", 'Dokumen dan Identitas');
        $sheet->mergeCells("B{$rDok}:D{$rDok}");
        $sheet->getStyle("B{$rDok}:D{$rDok}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle("B{$rDok}:D{$rDok}")->getFont()->setBold(true);
        $sheet->getStyle("B{$rDok}:D{$rDok}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rDok++;

        $sheet->setCellValue("B{$rDok}", 'Dokumen');
        $sheet->setCellValue("C{$rDok}", 'Ada / Tidak'); $sheet->mergeCells("C{$rDok}:D{$rDok}");
        $sheet->getStyle("B{$rDok}:D{$rDok}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("B{$rDok}:D{$rDok}")->getFont()->setBold(true);
        $sheet->getStyle("C{$rDok}:D{$rDok}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rDok++;

        $dokItems = [
            ['COA', $docData['dokumen']['coa'] ?? '-'],
            ['Surat Jalan Vendor', $docData['dokumen']['surat_jalan'] ?? '-'],
            ['Packing List', $docData['dokumen']['packing_list'] ?? '-'],
            ['Identitas RM di kemasan', $docData['dokumen']['identitas_kemasan'] ?? '-'],
            ['Logo Halal Kemasan', $docData['dokumen']['logo_halal'] ?? '-'],
            ['Sesuai Matriks Bahan Baku', $docData['dokumen']['matriks_bahan'] ?? '-'],
        ];

        foreach ($dokItems as $it) {
            $sheet->setCellValue("B{$rDok}", $it[0]);
            $sheet->setCellValue("C{$rDok}", $it[1]);
            $sheet->mergeCells("C{$rDok}:D{$rDok}");
            $sheet->getStyle("C{$rDok}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$rDok}")->getFont()->setBold(true);
            $rDok++;
        }

        $sheet->setCellValue("B{$rDok}", 'Note: √ : Ada       x : Tidak ada');
        $sheet->mergeCells("B{$rDok}:D{$rDok}");
        $sheet->getStyle("B{$rDok}")->getFont()->setItalic(true);

        // Serah Terima Sampel & Disposisi (Rows 22-28 on Cols E-H)
        $rSt = 22;
        $sheet->setCellValue("E{$rSt}", 'Serah Terima Sampel');
        $sheet->mergeCells("E{$rSt}:H{$rSt}");
        $sheet->getStyle("E{$rSt}:H{$rSt}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle("E{$rSt}:H{$rSt}")->getFont()->setBold(true);
        $sheet->getStyle("E{$rSt}:H{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rSt++;

        $sheet->setCellValue("E{$rSt}", 'Tahap');
        $sheet->setCellValue("F{$rSt}", 'Jam');
        $sheet->setCellValue("G{$rSt}", 'Ttd');
        $sheet->setCellValue("H{$rSt}", 'Jumlah');
        $sheet->getStyle("E{$rSt}:H{$rSt}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("E{$rSt}:H{$rSt}")->getFont()->setBold(true);
        $sheet->getStyle("F{$rSt}:H{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rSt++;

        $sheet->setCellValue("E{$rSt}", 'Sampling');
        $sheet->setCellValue("F{$rSt}", $docData['serah_terima']['jam_sampling'] ?? '-');
        $sheet->setCellValue("G{$rSt}", $docData['serah_terima']['ttd_sampling'] ?? '-');
        $sheet->setCellValue("H{$rSt}", $docData['serah_terima']['jumlah_sampling'] ?? '-');
        $sheet->getStyle("F{$rSt}:H{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rSt++;

        $sheet->setCellValue("E{$rSt}", 'Re-Sampling');
        $sheet->setCellValue("F{$rSt}", '-');
        $sheet->setCellValue("G{$rSt}", '-');
        $sheet->setCellValue("H{$rSt}", '-');
        $sheet->getStyle("F{$rSt}:H{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rSt++;

        $sheet->setCellValue("E{$rSt}", 'Disposisi'); $sheet->mergeCells("E{$rSt}:F{$rSt}");
        $sheet->setCellValue("G{$rSt}", $docData['serah_terima']['disposisi'] ?? 'Belum Disposisi'); $sheet->mergeCells("G{$rSt}:H{$rSt}");
        $sheet->getStyle("E{$rSt}")->getFont()->setBold(true);
        $sheet->getStyle("G{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$rSt}")->getFont()->setBold(true);
        $rSt++;

        if (!empty($docData['serah_terima']['group']) && $docData['serah_terima']['group'] !== '-') {
            $sheet->setCellValue("E{$rSt}", 'Group'); $sheet->mergeCells("E{$rSt}:F{$rSt}");
            $sheet->setCellValue("G{$rSt}", $docData['serah_terima']['group']); $sheet->mergeCells("G{$rSt}:H{$rSt}");
            $sheet->getStyle("E{$rSt}")->getFont()->setBold(true);
            $sheet->getStyle("G{$rSt}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$rSt}")->getFont()->setBold(true);
            $rSt++;
        }

        // Apply borders on left block
        $leftMaxRow = max($rDok, $rSt);
        $sheet->getStyle("B11:D{$rDok}")->applyFromArray($thinBorder);
        $sheet->getStyle("E11:H{$rSt}")->applyFromArray($thinBorder);

        // 4. Right Block: Analisis Table (Columns J onwards)
        $startColIdx = 10; // Column J
        $paramLabels = $docData['template_meta']['param_labels'];
        $paramKeys = $docData['template_meta']['param_keys'];
        $numParams = count($paramLabels);
        $endColIdx = $startColIdx + $numParams;

        $startColLetter = Coordinate::stringFromColumnIndex($startColIdx);
        $endColLetter = Coordinate::stringFromColumnIndex($endColIdx);

        // Header Row 11: Analisis
        $sheet->setCellValue("{$startColLetter}11", 'Analisis');
        $sheet->mergeCells("{$startColLetter}11:{$endColLetter}11");
        $sheet->getStyle("{$startColLetter}11:{$endColLetter}11")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle("{$startColLetter}11:{$endColLetter}11")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("{$startColLetter}11:{$endColLetter}11")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subheader Row 12: No Sampel + Param Labels
        $sheet->setCellValue("{$startColLetter}12", 'No Sampel');
        $sheet->getStyle("{$startColLetter}12")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("{$startColLetter}12")->getFont()->setBold(true);
        $sheet->getStyle("{$startColLetter}12")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($paramLabels as $idx => $label) {
            $colLetter = Coordinate::stringFromColumnIndex($startColIdx + 1 + $idx);
            $sheet->setCellValue("{$colLetter}12", $label);
            $sheet->getStyle("{$colLetter}12")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
            $sheet->getStyle("{$colLetter}12")->getFont()->setBold(true);
            $sheet->getStyle("{$colLetter}12")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Sample Rows (Rows 13 to max(27, 13 + count($docData['sample_rows'])))
        $sampleRows = $docData['sample_rows'] ?? [];
        $totalSampleCount = count($sampleRows);
        $minDisplayRows = max(15, $totalSampleCount);

        $currR = 13;
        for ($i = 0; $i < $minDisplayRows; $i++) {
            $s = $sampleRows[$i] ?? null;
            $sheet->setCellValue("{$startColLetter}{$currR}", $i + 1);
            $sheet->getStyle("{$startColLetter}{$currR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($paramKeys as $idx => $key) {
                $colLetter = Coordinate::stringFromColumnIndex($startColIdx + 1 + $idx);
                $val = ($s && isset($s[$key]) && $s[$key] !== '') ? $s[$key] : '-';
                $sheet->setCellValue("{$colLetter}{$currR}", $val);
                $sheet->getStyle("{$colLetter}{$currR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $currR++;
        }

        // Row Average
        $sheet->setCellValue("{$startColLetter}{$currR}", 'Average');
        $sheet->getStyle("{$startColLetter}{$currR}")->getFont()->setBold(true);
        $sheet->getStyle("{$startColLetter}{$currR}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle("{$startColLetter}{$currR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($paramKeys as $idx => $key) {
            $colLetter = Coordinate::stringFromColumnIndex($startColIdx + 1 + $idx);
            $avgVal = $docData['averages'][$key] ?? '-';
            $sheet->setCellValue("{$colLetter}{$currR}", $avgVal);
            $sheet->getStyle("{$colLetter}{$currR}")->getFont()->setBold(true);
            $sheet->getStyle("{$colLetter}{$currR}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
            $sheet->getStyle("{$colLetter}{$currR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle("{$startColLetter}11:{$endColLetter}{$currR}")->applyFromArray($thinBorder);

        // Note below table
        if (!empty($docData['note'])) {
            $currR++;
            $sheet->setCellValue("{$startColLetter}{$currR}", 'Catatan: ' . $docData['note']);
            $sheet->mergeCells("{$startColLetter}{$currR}:{$endColLetter}{$currR}");
            $sheet->getStyle("{$startColLetter}{$currR}")->getFont()->setItalic(true);
        }

        // 5. Signatures Block (Below tables)
        $rSig = max($leftMaxRow, $currR) + 2;

        $sheet->setCellValue("B{$rSig}", 'Disampling oleh,'); $sheet->mergeCells("B{$rSig}:D{$rSig}");
        $sheet->setCellValue("G{$rSig}", 'Dianalisis oleh,'); $sheet->mergeCells("G{$rSig}:I{$rSig}");
        $sheet->setCellValue("L{$rSig}", 'Dicek oleh,'); $sheet->mergeCells("L{$rSig}:N{$rSig}");
        $sheet->getStyle("B{$rSig}:N{$rSig}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$rSig}:N{$rSig}")->getFont()->setBold(true);

        $rSigName = $rSig + 3;
        $sheet->setCellValue("B{$rSigName}", '( ' . ($docData['signatures']['qc_field'] ?? '....................................') . ' )');
        $sheet->mergeCells("B{$rSigName}:D{$rSigName}");
        $sheet->setCellValue("G{$rSigName}", '( ' . ($docData['signatures']['qc_analis'] ?? '....................................') . ' )');
        $sheet->mergeCells("G{$rSigName}:I{$rSigName}");
        $sheet->setCellValue("L{$rSigName}", '( ' . ($docData['signatures']['spv'] ?? '....................................') . ' )');
        $sheet->mergeCells("L{$rSigName}:N{$rSigName}");
        $sheet->getStyle("B{$rSigName}:N{$rSigName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$rSigName}:N{$rSigName}")->getFont()->setBold(true);

        $rSigRole = $rSigName + 1;
        $sheet->setCellValue("B{$rSigRole}", 'QC Field'); $sheet->mergeCells("B{$rSigRole}:D{$rSigRole}");
        $sheet->setCellValue("G{$rSigRole}", 'QC Analis'); $sheet->mergeCells("G{$rSigRole}:I{$rSigRole}");
        $sheet->setCellValue("L{$rSigRole}", 'Staff / SPV / MNG QC'); $sheet->mergeCells("L{$rSigRole}:N{$rSigRole}");
        $sheet->getStyle("B{$rSigRole}:N{$rSigRole}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Document Code Watermark
        $rWatermark = $rSigRole + 2;
        $sheet->setCellValue("{$endColLetter}{$rWatermark}", $docData['doc_no']);
        $sheet->getStyle("{$endColLetter}{$rWatermark}")->getFont()->setSize(9)->setBold(true);
        $sheet->getStyle("{$endColLetter}{$rWatermark}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 6. Formatting Column Widths
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(3);
        $sheet->getColumnDimension('J')->setWidth(10);

        for ($c = $startColIdx + 1; $c <= $endColIdx; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(13);
        }

        $sheet->getStyle("A1:{$endColLetter}{$rWatermark}")->getFont()->setName('Calibri')->setSize(9.5);
        $sheet->getStyle('D1')->getFont()->setSize(13)->setBold(true);
    }
}

/**
 * Sheet 2: Data Mentah Hasil Uji Per Sampel (1 s/d N)
 */
class DataSampelRmSheetExport implements WithEvents, WithTitle
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function title(): string
    {
        return 'DATA SAMPEL';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildSampleSheet($sheet);
            },
        ];
    }

    protected function buildSampleSheet(Worksheet $sheet): void
    {
        $sheet->setShowGridlines(true);

        $identitas = $this->identitas;
        try {
            $rmCtrl = new RMPMController();
            $accumulatedSamples = $rmCtrl->getFlattenedSamplesForIdentitas($identitas->id, ['incoming', 'sta']);
            if ($accumulatedSamples->isEmpty()) {
                $accumulatedSamples = $rmCtrl->getFlattenedSamplesForIdentitas($identitas->id);
            }
        } catch (\Throwable $e) {
            $accumulatedSamples = $identitas->analisaSampel ?? collect();
        }

        // Header sheet 2
        $sheet->setCellValue('A1', 'DATA HASIL UJI SAMPEL (INCOMING & STA)');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A2', 'No SPB: ' . ($identitas->no_spb ?? '-') . ' | Bahan: ' . ($identitas->nama_bahan ?: $identitas->jenis) . ' | Supplier: ' . ($identitas->supplier ?? '-'));
        $sheet->mergeCells('A2:J2');

        // Dynamic Columns based on template
        $templateType = DocRmController::resolveTemplateType($identitas->jenis ?? '');
        $templateMeta = DocRmController::getTemplateMeta($templateType);

        $headers = array_merge(['Sampel Ke'], $templateMeta['param_labels']);
        $keys = $templateMeta['param_keys'];

        $headerRow = 4;
        foreach ($headers as $idx => $h) {
            $col = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue("{$col}{$headerRow}", $h);
        }

        $endCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A{$headerRow}:{$endCol}{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$endCol}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getStyle("A{$headerRow}:{$endCol}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currRow = 5;
        foreach ($accumulatedSamples as $idx => $s) {
            $sheet->setCellValue("A{$currRow}", $idx + 1);
            $sheet->getStyle("A{$currRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($keys as $kIdx => $k) {
                $col = Coordinate::stringFromColumnIndex($kIdx + 2);
                $val = $s->{$k} ?? ($s->{strtolower($k)} ?? '-');
                $sheet->setCellValue("{$col}{$currRow}", $val !== null && $val !== '' ? $val : '-');
                $sheet->getStyle("{$col}{$currRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $currRow++;
        }

        $lastRow = max(5, $currRow - 1);
        $sheet->getStyle("A4:{$endCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        for ($c = 1; $c <= count($headers); $c++) {
            $col = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
