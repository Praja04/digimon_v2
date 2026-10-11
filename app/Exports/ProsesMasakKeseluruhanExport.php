<?php

namespace App\Exports;

use App\Models\ProductionBatch;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProsesMasakKeseluruhanExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $month;
    protected $variant;
    protected $formulasi;
    protected $rowCount = 0;
    protected $revisionRowIndexes = [];

    public function __construct($startDate = null, $endDate = null, $month = null, $variant = null, $formulasi = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->month = $month;
        $this->variant = $variant;
        $this->formulasi = $formulasi;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $this->revisionRowIndexes = [];

        $query = ProductionBatch::with([
            'BlendingAwal',
            'monitoringTurunBlending',
            'monitoringPasteurisasi'
        ]);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        } elseif ($this->month) {
            $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$this->month]);
        }

        if ($this->variant) {
            $query->where('variant', $this->variant);
        }

        if ($this->formulasi) {
            $query->where('formulasi', $this->formulasi);
        }

        $batches = $query->orderBy('date', 'asc')->get();

        $no = 1;
        $rows = collect();

        $formatTime = function ($dt) {
            if (!$dt) return '-';
            try {
                return Carbon::parse($dt)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i');
            } catch (\Exception $e) {
                return '-';
            }
        };

        foreach ($batches as $batch) {
            $rangeList = collect();

            foreach ($batch->BlendingAwal as $ba) {
                if (!empty($ba->batch_range)) {
                    $rangeList->push((string)$ba->batch_range);
                }
            }
            foreach ($batch->monitoringTurunBlending as $mtb) {
                if (!empty($mtb->batch_range)) {
                    $rangeList->push((string)$mtb->batch_range);
                }
            }
            foreach ($batch->monitoringPasteurisasi as $mp) {
                if (!empty($mp->batch_range)) {
                    $rangeList->push((string)$mp->batch_range);
                }
            }

            $uniqueRanges = $rangeList->unique()->values();

            if ($uniqueRanges->isEmpty()) {
                if (!empty($batch->batch_range)) {
                    $uniqueRanges->push((string)$batch->batch_range);
                }
            }

            $sortedRanges = $uniqueRanges->sort(function ($a, $b) {
                preg_match('/^(\d+)/', $a, $mA);
                preg_match('/^(\d+)/', $b, $mB);
                $startA = isset($mA[1]) ? (int)$mA[1] : 0;
                $startB = isset($mB[1]) ? (int)$mB[1] : 0;
                return $startA <=> $startB;
            })->values();

            foreach ($sortedRanges as $rangeStr) {
                $baList = $batch->BlendingAwal->filter(fn($i) => (string)$i->batch_range === (string)$rangeStr)->values();
                $mtbList = $batch->monitoringTurunBlending->filter(fn($i) => (string)$i->batch_range === (string)$rangeStr)->values();
                $mpList = $batch->monitoringPasteurisasi->filter(fn($i) => (string)$i->batch_range === (string)$rangeStr)->values();

                $maxSubCount = max(
                    $baList->count(),
                    $mtbList->count(),
                    $mpList->count(),
                    1
                );

                for ($subIdx = 0; $subIdx < $maxSubCount; $subIdx++) {
                    $baItem = $baList->get($subIdx);
                    $mtbItem = $mtbList->get($subIdx);
                    $mpItem = $mpList->get($subIdx);

                    if ($subIdx === 0) {
                        $baItem = $baItem ?? $baList->first();
                        $mtbItem = $mtbItem ?? $mtbList->first();
                        $mpItem = $mpItem ?? $mpList->first();
                    }

                    if ($subIdx > 0 && !$baItem && !$mtbItem && !$mpItem) {
                        continue;
                    }

                    $revisiTag = null;
                    if ($baItem && !empty($baItem->revisi)) {
                        $revisiTag = 'Rev ' . $baItem->revisi;
                    } elseif ($mtbItem && !empty($mtbItem->revisi)) {
                        $revisiTag = 'Rev ' . $mtbItem->revisi;
                    } elseif ($mpItem && !empty($mpItem->revisi)) {
                        $revisiTag = 'Rev ' . $mpItem->revisi;
                    } elseif ($subIdx > 0) {
                        $revisiTag = 'Rev ' . $subIdx;
                    }

                    $batchLabel = $revisiTag ? ($rangeStr . ' (' . $revisiTag . ')') : $rangeStr;

                    $isRevisionRow = ($subIdx > 0)
                        || ($baItem && (!empty($baItem->revisi) || ($baItem->is_adjustment ?? 0) > 0 || in_array($baItem->disposition ?? '', ['Adjustment', 'Resampling', 'Repro'])))
                        || ($mtbItem && (!empty($mtbItem->revisi) || ($mtbItem->is_adjustment ?? 0) > 0 || in_array($mtbItem->disposition ?? '', ['Adjustment', 'Resampling', 'Repro'])))
                        || ($mpItem && (!empty($mpItem->revisi) || ($mpItem->is_adjustment ?? 0) > 0 || in_array($mpItem->disposition ?? '', ['Adjustment', 'Resampling', 'Repro'])));

                    if ($isRevisionRow) {
                        $this->revisionRowIndexes[] = 7 + $rows->count();
                    }

                    $rows->push([
                        $no++,
                        $batch->po_number ?? '-',
                        $batch->date ? Carbon::parse($batch->date)->format('d-m-Y') : '-',
                        $batch->variant ?? '-',
                        $batch->formulasi ?? '-',
                        $batch->batch_range ?? ($batch->batch_number ?? '-'),
                        $batchLabel,

                        // Blending Awal
                        $formatTime($baItem->scanned_at ?? null),
                        $formatTime($baItem->updated_at ?? null),
                        $baItem ? ($baItem->volume_blending ?? ($baItem->volume ?? '-')) : '-',
                        $baItem && is_numeric($baItem->brix) ? number_format((float)$baItem->brix, 2, '.', '') : '-',
                        $baItem && is_numeric($baItem->visco) ? number_format((float)$baItem->visco, 2, '.', '') : '-',
                        $baItem && is_numeric($baItem->nacl) ? number_format((float)$baItem->nacl, 2, '.', '') : '-',
                        $baItem && is_numeric($baItem->aw) ? number_format((float)$baItem->aw, 3, '.', '') : '-',
                        $baItem && is_numeric($baItem->ph) ? number_format((float)$baItem->ph, 2, '.', '') : '-',
                        $baItem && is_numeric($baItem->bj) ? number_format((float)$baItem->bj, 4, '.', '') : '-',
                        $baItem ? ($baItem->color->name ?? ($baItem->warna ?? '-')) : '-',
                        $baItem->organo ?? '-',
                        $baItem->disposition ?? '-',

                        // Blending Release / Turun Blending
                        $formatTime($mtbItem->scanned_at ?? null),
                        $formatTime($mtbItem->updated_at ?? null),
                        $mtbItem->tangki_storage ?? ($mtbItem->storage ?? '-'),
                        $mtbItem ? ($mtbItem->volume_release ?? ($mtbItem->volume ?? '-')) : '-',
                        $mtbItem && is_numeric($mtbItem->brix) ? number_format((float)$mtbItem->brix, 2, '.', '') : '-',
                        $mtbItem && is_numeric($mtbItem->visco) ? number_format((float)$mtbItem->visco, 2, '.', '') : '-',
                        $mtbItem && is_numeric($mtbItem->nacl) ? number_format((float)$mtbItem->nacl, 2, '.', '') : '-',
                        $mtbItem && is_numeric($mtbItem->aw) ? number_format((float)$mtbItem->aw, 3, '.', '') : '-',
                        $mtbItem && is_numeric($mtbItem->ph) ? number_format((float)$mtbItem->ph, 2, '.', '') : '-',
                        $mtbItem && is_numeric($mtbItem->bj) ? number_format((float)$mtbItem->bj, 4, '.', '') : '-',
                        $mtbItem->warna ?? '-',
                        $mtbItem->organo ?? '-',
                        $mtbItem->disposition ?? '-',
                        $mtbItem && ($mtbItem->adjust_gh || $mtbItem->adjustment_qty_gula) ? ($mtbItem->adjust_gh ?? $mtbItem->adjustment_qty_gula) : '-',
                        $mtbItem && is_numeric($mtbItem->adjust_gh ?? $mtbItem->adjustment_qty_gula) ? number_format(((float)($mtbItem->adjust_gh ?? $mtbItem->adjustment_qty_gula) / 500) * 100, 2) . '%' : '-',
                        $mtbItem && ($mtbItem->adjust_air || $mtbItem->adjustment_qty_air) ? ($mtbItem->adjust_air ?? $mtbItem->adjustment_qty_air) : '-',

                        // Monitoring Pasteurisasi
                        $formatTime($mpItem->scanned_at ?? null),
                        $formatTime($mpItem->updated_at ?? null),
                        $mpItem->storage ?? '-',
                        $mpItem->volume ?? '-',
                        $mpItem && is_numeric($mpItem->brix) ? number_format((float)$mpItem->brix, 2, '.', '') : '-',
                        $mpItem && is_numeric($mpItem->visco) ? number_format((float)$mpItem->visco, 2, '.', '') : '-',
                        $mpItem && is_numeric($mpItem->nacl) ? number_format((float)$mpItem->nacl, 2, '.', '') : '-',
                        $mpItem && is_numeric($mpItem->aw) ? number_format((float)$mpItem->aw, 3, '.', '') : '-',
                        $mpItem && is_numeric($mpItem->ph) ? number_format((float)$mpItem->ph, 2, '.', '') : '-',
                        $mpItem && is_numeric($mpItem->bj) ? number_format((float)$mpItem->bj, 4, '.', '') : '-',
                        $mpItem->warna ?? '-',
                        $mpItem->organo ?? '-',
                        $mpItem->disposition ?? '-',
                    ]);
                }
            }
        }

        $this->rowCount = $rows->count();

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. PO',
            'Tanggal PO',
            'Varian',
            'Formulasi',
            'Batch Range PO',
            'Batch Ke-',

            // Blending Awal
            'Jam Scan (BL Awal)',
            'Jam Selesai (BL Awal)',
            'Vol. BL Awal (L)',
            'Brix BL Awal (°Bx)',
            'Visco BL Awal (cP)',
            'NaCl BL Awal (%)',
            'Aw BL Awal',
            'pH BL Awal',
            'BJ BL Awal',
            'Warna BL Awal',
            'Organo BL Awal',
            'Disposisi BL Awal',

            // Blending Release
            'Jam Scan (Release)',
            'Jam Selesai (Release)',
            'Tangki Storage',
            'Vol. Release (L)',
            'Brix Release (°Bx)',
            'Visco Release (cP)',
            'NaCl Release (%)',
            'Aw Release',
            'pH Release',
            'BJ Release',
            'Warna Release',
            'Organo Release',
            'Disposisi Release',
            'Adj. GH (kg)',
            '% Adj. GH',
            'Adj. Air (kg)',

            // Pasteurisasi
            'Jam Scan (Pasteur)',
            'Jam Selesai (Pasteur)',
            'Tangki Pasteur',
            'Vol. Pasteur (L)',
            'Brix Pasteur (°Bx)',
            'Visco Pasteur (cP)',
            'NaCl Pasteur (%)',
            'Aw Pasteur',
            'pH Pasteur',
            'BJ Pasteur',
            'Warna Pasteur',
            'Organo Pasteur',
            'Disposisi Pasteur',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 16,  // No. PO
            'C' => 14,  // Tanggal PO
            'D' => 14,  // Varian
            'E' => 14,  // Formulasi
            'F' => 16,  // Batch Range PO
            'G' => 16,  // Batch Ke-

            // Blending Awal
            'H' => 16,  // Jam Scan BL Awal
            'I' => 16,  // Jam Selesai BL Awal
            'J' => 16,  // Vol Awal
            'K' => 14,  // Brix Awal
            'L' => 14,  // Visco Awal
            'M' => 14,  // NaCl Awal
            'N' => 12,  // Aw Awal
            'O' => 10,  // pH Awal
            'P' => 12,  // BJ Awal
            'Q' => 14,  // Warna Awal
            'R' => 14,  // Organo Awal
            'S' => 14,  // Disposisi Awal

            // Blending Release
            'T' => 16,  // Jam Scan Release
            'U' => 16,  // Jam Selesai Release
            'V' => 15,  // Tangki Storage
            'W' => 16,  // Vol Release
            'X' => 14,  // Brix Release
            'Y' => 14,  // Visco Release
            'Z' => 14,  // NaCl Release
            'AA' => 12, // Aw Release
            'AB' => 10, // pH Release
            'AC' => 12, // BJ Release
            'AD' => 14, // Warna Release
            'AE' => 14, // Organo Release
            'AF' => 14, // Disposisi Release
            'AG' => 14, // Adj GH
            'AH' => 13, // % Adj GH
            'AI' => 14, // Adj Air

            // Pasteurisasi
            'AJ' => 16, // Jam Scan Pasteur
            'AK' => 16, // Jam Selesai Pasteur
            'AL' => 15, // Tangki Pasteur
            'AM' => 16, // Vol Pasteur
            'AN' => 14, // Brix Pasteur
            'AO' => 14, // Visco Pasteur
            'AP' => 14, // NaCl Pasteur
            'AQ' => 12, // Aw Pasteur
            'AR' => 10, // pH Pasteur
            'AS' => 12, // BJ Pasteur
            'AT' => 14, // Warna Pasteur
            'AU' => 14, // Organo Pasteur
            'AV' => 14, // Disposisi Pasteur
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Title Banner (Row 1-3)
        $sheet->mergeCells('A1:AV1');
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PROSES MASAK (BLENDING & PASTEURISASI)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $filterInfo = 'Periode: ' . ($this->startDate ? Carbon::parse($this->startDate)->format('d/m/Y') : 'Semua') .
            ' s/d ' . ($this->endDate ? Carbon::parse($this->endDate)->format('d/m/Y') : 'Semua') .
            ' | Bulan: ' . ($this->month ?: 'Semua') .
            ' | Varian: ' . ($this->variant ?: 'Semua') .
            ' | Formulasi: ' . ($this->formulasi ?: 'Semua') .
            ' | Di-export pada: ' . Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        $sheet->mergeCells('A2:AV2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // 2. Multi-level Group Headers (Row 5)
        $sheet->mergeCells('A5:G5');
        $sheet->setCellValue('A5', 'INFORMASI PO & BATCH');
        $sheet->getStyle('A5:G5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('H5:S5');
        $sheet->setCellValue('H5', 'BLENDING AWAL (KIMIA)');
        $sheet->getStyle('H5:S5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('T5:AI5');
        $sheet->setCellValue('T5', 'BLENDING RELEASE / TURUN BLENDING');
        $sheet->getStyle('T5:AI5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('AJ5:AV5');
        $sheet->setCellValue('AJ5', 'MONITORING PASTEURISASI');
        $sheet->getStyle('AJ5:AV5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF7C3AED']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getRowDimension(5)->setRowHeight(22);

        // 3. Table Column Headers (Row 6)
        $headerRange = 'A6:AV6';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF334155'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(28);

        // 4. Data Rows Styling
        $totalRows = $this->rowCount;
        if ($totalRows > 0) {
            $dataRange = 'A7:AV' . (6 + $totalRows);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align standard columns
            $centerCols = [
                'A', 'B', 'C', 'D', 'E', 'F', 'G',
                'H', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                'T', 'U', 'V', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AH',
                'AJ', 'AK', 'AL', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV'
            ];
            foreach ($centerCols as $col) {
                $sheet->getStyle($col . '7:' . $col . (6 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Right align volume & adjustment columns
            $rightCols = ['J', 'W', 'AG', 'AI', 'AM'];
            foreach ($rightCols as $col) {
                $sheet->getStyle($col . '7:' . $col . (6 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Zebra striping
            for ($row = 7; $row <= 6 + $totalRows; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':AV' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
            }

            // Highlight revision & adjustment rows
            foreach ($this->revisionRowIndexes as $revRow) {
                $sheet->getStyle('A' . $revRow . ':AV' . $revRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF3C7'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF92400E'],
                    ],
                ]);
            }
        }

        return [];
    }
}
