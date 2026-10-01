<?php

namespace App\Exports;

use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
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

class PelarutanExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $pelarutanType;
    protected $status;
    protected $batchId;
    protected $rowCount = 0;

    public function __construct($startDate = null, $endDate = null, $pelarutanType = 'all', $status = null, $batchId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->pelarutanType = $pelarutanType ?: 'all';
        $this->status = $status;
        $this->batchId = $batchId;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function collection()
    {
        $rows = collect();

        // 1. Ambil Pelarutan 1 jika diminta
        if ($this->pelarutanType === 'all' || $this->pelarutanType === 'pelarutan_1') {
            $q1 = Pelarutan1::with(['productionBatch', 'user']);

            if ($this->batchId) {
                $q1->where('production_batch_id', $this->batchId);
            }

            if ($this->startDate || $this->endDate) {
                $q1->whereHas('productionBatch', function ($b) {
                    if ($this->startDate) {
                        $b->whereDate('date', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $b->whereDate('date', '<=', $this->endDate);
                    }
                });
            }

            if ($this->status === 'complete') {
                $q1->whereNotNull('disposition');
            } elseif ($this->status === 'progress') {
                $q1->whereNull('disposition');
            }

            $pelarutan1Data = $q1->get()->map(function ($item) {
                return [
                    'type' => 'Pelarutan 1',
                    'po' => $item->productionBatch->po_number ?? '-',
                    'date' => $item->productionBatch ? Carbon::parse($item->productionBatch->date)->format('d-m-Y') : '-',
                    'batch' => $item->batch_number ?? '-',
                    'dissolver' => $item->dissolver_number ?? '-',
                    'brix' => $item->brix !== null ? number_format((float)$item->brix, 2, '.', '') : '-',
                    'nacl' => $item->nacl !== null ? number_format((float)$item->nacl, 2, '.', '') : '-',
                    'organo' => $item->organo ?? '-',
                    'disposition' => $item->disposition ?? 'Pending',
                    'adj_tebu' => $item->adjustment_qty_gula_tebu !== null ? $item->adjustment_qty_gula_tebu : '-',
                    'adj_kelapa' => $item->adjustment_qty_gula_kelapa !== null ? $item->adjustment_qty_gula_kelapa : '-',
                    'status' => $item->status ?? ($item->disposition ? 'Complete' : 'Progress'),
                    'remark' => $item->disposition_remark ?? '-',
                    'analis' => $item->user->name ?? '-',
                    'created_at' => $item->created_at ? Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-',
                ];
            });

            $rows = $rows->merge($pelarutan1Data);
        }

        // 2. Ambil Pelarutan 2 jika diminta
        if ($this->pelarutanType === 'all' || $this->pelarutanType === 'pelarutan_2') {
            $q2 = Pelarutan2::with(['productionBatch', 'user']);

            if ($this->batchId) {
                $q2->where('production_batch_id', $this->batchId);
            }

            if ($this->startDate || $this->endDate) {
                $q2->whereHas('productionBatch', function ($b) {
                    if ($this->startDate) {
                        $b->whereDate('date', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $b->whereDate('date', '<=', $this->endDate);
                    }
                });
            }

            if ($this->status === 'complete') {
                $q2->whereNotNull('disposition');
            } elseif ($this->status === 'progress') {
                $q2->whereNull('disposition');
            }

            $pelarutan2Data = $q2->get()->map(function ($item) {
                return [
                    'type' => 'Pelarutan 2',
                    'po' => $item->productionBatch->po_number ?? '-',
                    'date' => $item->productionBatch ? Carbon::parse($item->productionBatch->date)->format('d-m-Y') : '-',
                    'batch' => $item->batch_number ?? '-',
                    'dissolver' => $item->dissolver_number ?? '-',
                    'brix' => $item->brix !== null ? number_format((float)$item->brix, 2, '.', '') : '-',
                    'nacl' => $item->nacl !== null ? number_format((float)$item->nacl, 2, '.', '') : '-',
                    'organo' => $item->organo ?? '-',
                    'disposition' => $item->disposition ?? 'Pending',
                    'adj_tebu' => $item->adjustment_qty_gula_tebu !== null ? $item->adjustment_qty_gula_tebu : '-',
                    'adj_kelapa' => $item->adjustment_qty_gula_kelapa !== null ? $item->adjustment_qty_gula_kelapa : '-',
                    'status' => $item->status ?? ($item->disposition ? 'Complete' : 'Progress'),
                    'remark' => $item->disposition_remark ?? '-',
                    'analis' => $item->user->name ?? '-',
                    'created_at' => $item->created_at ? Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-',
                ];
            });

            $rows = $rows->merge($pelarutan2Data);
        }

        $no = 1;
        $formattedData = $rows->map(function ($item) use (&$no) {
            return [
                $no++,
                $item['po'],
                $item['date'],
                $item['type'],
                $item['batch'],
                $item['dissolver'],
                $item['brix'],
                $item['nacl'],
                $item['organo'],
                $item['disposition'],
                $item['adj_tebu'],
                $item['adj_kelapa'],
                $item['status'],
                $item['remark'],
                $item['analis'],
                $item['created_at'],
            ];
        });

        $this->rowCount = $formattedData->count();

        return $formattedData;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. PO',
            'Tanggal PO',
            'Kategori Pelarutan',
            'Batch #',
            'No. Dissolver',
            'Brix (°Bx)',
            'NaCl (%)',
            'Organoleptik',
            'Disposisi',
            'Adj. Gula Tebu (kg)',
            'Adj. Gula Kelapa (kg)',
            'Status',
            'Remark / Catatan',
            'Analis / QC',
            'Waktu Input',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 16,  // No. PO
            'C' => 14,  // Tanggal PO
            'D' => 18,  // Kategori Pelarutan
            'E' => 12,  // Batch #
            'F' => 15,  // No. Dissolver
            'G' => 12,  // Brix
            'H' => 12,  // NaCl
            'I' => 15,  // Organoleptik
            'J' => 18,  // Disposisi
            'K' => 20,  // Adj. Gula Tebu
            'L' => 22,  // Adj. Gula Kelapa
            'M' => 14,  // Status
            'N' => 25,  // Remark
            'O' => 20,  // Analis
            'P' => 18,  // Waktu Input
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title Banner (Row 1-3)
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI EXPORT DATA PELARUTAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $filterInfo = 'Periode: ' . ($this->startDate ? Carbon::parse($this->startDate)->format('d/m/Y') : 'Semua') .
            ' s/d ' . ($this->endDate ? Carbon::parse($this->endDate)->format('d/m/Y') : 'Semua') .
            ' | Kategori: ' . ($this->pelarutanType === 'all' ? 'Semua (Pelarutan 1 & 2)' : ($this->pelarutanType === 'pelarutan_1' ? 'Pelarutan 1' : 'Pelarutan 2')) .
            ' | Status: ' . ($this->status ? ucfirst($this->status) : 'Semua') .
            ' | Di-export pada: ' . Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // Table Header (Row 5)
        $headerRange = 'A5:P5';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF405189'],
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
        $sheet->getRowDimension(5)->setRowHeight(28);

        // Data Rows Styling
        $totalRows = $this->rowCount;
        if ($totalRows > 0) {
            $dataRange = 'A6:P' . (5 + $totalRows);
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

            // Center align columns: No, Tanggal, Type, Batch, Dissolver, Brix, NaCl, Status, Waktu
            $centerCols = ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'M', 'P'];
            foreach ($centerCols as $col) {
                $sheet->getStyle($col . '6:' . $col . (5 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Right align numeric adjustment columns
            $sheet->getStyle('K6:L' . (5 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Zebra striping
            for ($row = 6; $row <= 5 + $totalRows; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':P' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
            }
        }

        return [];
    }
}
