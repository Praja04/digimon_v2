<?php

namespace App\Exports;

use App\Models\BlendingAwal;
use App\Models\BlendingAfterAdjustMikro;
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

class BlendingExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $blendingType;
    protected $status;
    protected $batchId;
    protected $rowCount = 0;

    public function __construct($startDate = null, $endDate = null, $blendingType = 'all', $status = null, $batchId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->blendingType = $blendingType ?: 'all';
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

        // 1. Ambil Blending Awal (Kimia) jika diminta
        if ($this->blendingType === 'all' || $this->blendingType === 'kimia') {
            $qKimia = BlendingAwal::with(['productionBatch', 'user', 'color']);

            if ($this->batchId) {
                $qKimia->where('production_batch_id', $this->batchId);
            }

            if ($this->startDate || $this->endDate) {
                $qKimia->whereHas('productionBatch', function ($b) {
                    if ($this->startDate) {
                        $b->whereDate('date', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $b->whereDate('date', '<=', $this->endDate);
                    }
                });
            }

            if ($this->status === 'complete') {
                $qKimia->whereNotNull('disposition');
            } elseif ($this->status === 'progress') {
                $qKimia->whereNull('disposition');
            }

            $kimiaData = $qKimia->get()->map(function ($item) {
                return [
                    'po' => $item->productionBatch->po_number ?? '-',
                    'date' => $item->productionBatch ? Carbon::parse($item->productionBatch->date)->format('d-m-Y') : '-',
                    'type' => 'Blending Awal (Kimia)',
                    'nomor_blending' => $item->nomor_blending ?? '-',
                    'batch_range' => $item->batch_range ?? '-',
                    'volume' => $item->volume !== null ? $item->volume : '-',
                    'brix' => $item->brix !== null ? number_format((float)$item->brix, 2, '.', '') : '-',
                    'nacl' => $item->nacl !== null ? number_format((float)$item->nacl, 2, '.', '') : '-',
                    'bj' => $item->bj !== null ? number_format((float)$item->bj, 4, '.', '') : '-',
                    'visco' => $item->visco !== null ? number_format((float)$item->visco, 2, '.', '') : '-',
                    'aw' => $item->aw !== null ? number_format((float)$item->aw, 3, '.', '') : '-',
                    'ph' => $item->ph !== null ? number_format((float)$item->ph, 2, '.', '') : '-',
                    'organo' => $item->organo ?? '-',
                    'aroma' => $item->aroma ?? '-',
                    'color' => $item->color->name ?? '-',
                    'eb' => '-',
                    'tpc' => '-',
                    'ym' => '-',
                    'disposition' => $item->disposition ?? ($item->status ?: 'Pending'),
                    'adj_air' => $item->adjustment_qty_air !== null ? $item->adjustment_qty_air : '-',
                    'adj_garam' => $item->adjustment_qty_garam !== null ? $item->adjustment_qty_garam : '-',
                    'adj_caramel' => $item->adjustment_qty_caramel !== null ? $item->adjustment_qty_caramel : '-',
                    'analis' => $item->user->name ?? '-',
                    'created_at' => $item->created_at ? Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-',
                ];
            });

            $rows = $rows->merge($kimiaData);
        }

        // 2. Ambil Blending After Adjust (Mikro) jika diminta
        if ($this->blendingType === 'all' || $this->blendingType === 'mikro') {
            $qMikro = BlendingAfterAdjustMikro::with(['productionBatch']);

            if ($this->batchId) {
                $qMikro->where('production_batch_id', $this->batchId);
            }

            if ($this->startDate || $this->endDate) {
                $qMikro->whereHas('productionBatch', function ($b) {
                    if ($this->startDate) {
                        $b->whereDate('date', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $b->whereDate('date', '<=', $this->endDate);
                    }
                });
            }

            if ($this->status === 'complete') {
                $qMikro->whereNotNull('hasil');
            } elseif ($this->status === 'progress') {
                $qMikro->whereNull('hasil');
            }

            $mikroData = $qMikro->get()->map(function ($item) {
                return [
                    'po' => $item->productionBatch->po_number ?? '-',
                    'date' => $item->productionBatch ? Carbon::parse($item->productionBatch->date)->format('d-m-Y') : '-',
                    'type' => 'Blending Adjust (Mikro)',
                    'nomor_blending' => $item->nomor_blending ?? '-',
                    'batch_range' => $item->batch_range ?? '-',
                    'volume' => $item->volume !== null ? $item->volume : '-',
                    'brix' => '-',
                    'nacl' => '-',
                    'bj' => '-',
                    'visco' => '-',
                    'aw' => '-',
                    'ph' => '-',
                    'organo' => '-',
                    'aroma' => '-',
                    'color' => '-',
                    'eb' => $item->eb ?? '-',
                    'tpc' => $item->tpc ?? '-',
                    'ym' => $item->ym ?? '-',
                    'disposition' => $item->hasil ?? 'Pending',
                    'adj_air' => '-',
                    'adj_garam' => '-',
                    'adj_caramel' => '-',
                    'analis' => $item->nama_analis ?? '-',
                    'created_at' => $item->created_at ? Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-',
                ];
            });

            $rows = $rows->merge($mikroData);
        }

        $no = 1;
        $formattedData = $rows->map(function ($item) use (&$no) {
            return [
                $no++,
                $item['po'],
                $item['date'],
                $item['type'],
                $item['nomor_blending'],
                $item['batch_range'],
                $item['volume'],
                $item['brix'],
                $item['nacl'],
                $item['bj'],
                $item['visco'],
                $item['aw'],
                $item['ph'],
                $item['organo'],
                $item['aroma'],
                $item['color'],
                $item['eb'],
                $item['tpc'],
                $item['ym'],
                $item['disposition'],
                $item['adj_air'],
                $item['adj_garam'],
                $item['adj_caramel'],
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
            'Kategori Analisa',
            'No. Blending / Tangki',
            'Batch Range',
            'Volume (L)',
            'Brix (°Bx)',
            'NaCl (%)',
            'BJ',
            'Visco (cP)',
            'Aw',
            'pH',
            'Organoleptik',
            'Aroma',
            'Warna',
            'EB',
            'TPC',
            'YM',
            'Disposisi / Hasil',
            'Adj. Air (L)',
            'Adj. Garam (kg)',
            'Adj. Caramel (kg)',
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
            'D' => 24,  // Kategori Analisa
            'E' => 18,  // No. Blending
            'F' => 14,  // Batch Range
            'G' => 12,  // Volume
            'H' => 12,  // Brix
            'I' => 12,  // NaCl
            'J' => 12,  // BJ
            'K' => 12,  // Visco
            'L' => 12,  // Aw
            'M' => 10,  // pH
            'N' => 14,  // Organoleptik
            'O' => 14,  // Aroma
            'P' => 14,  // Warna
            'Q' => 12,  // EB
            'R' => 12,  // TPC
            'S' => 12,  // YM
            'T' => 18,  // Disposisi / Hasil
            'U' => 14,  // Adj. Air
            'V' => 16,  // Adj. Garam
            'W' => 18,  // Adj. Caramel
            'X' => 20,  // Analis
            'Y' => 18,  // Waktu Input
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title Banner (Row 1-3)
        $sheet->mergeCells('A1:Y1');
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI EXPORT DATA BLENDING (KIMIA & MIKRO)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $filterInfo = 'Periode: ' . ($this->startDate ? Carbon::parse($this->startDate)->format('d/m/Y') : 'Semua') .
            ' s/d ' . ($this->endDate ? Carbon::parse($this->endDate)->format('d/m/Y') : 'Semua') .
            ' | Kategori: ' . ($this->blendingType === 'all' ? 'Semua (Kimia & Mikro)' : ($this->blendingType === 'kimia' ? 'Kimia' : 'Mikro')) .
            ' | Status: ' . ($this->status ? ucfirst($this->status) : 'Semua') .
            ' | Di-export pada: ' . Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        $sheet->mergeCells('A2:Y2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // Table Header (Row 5)
        $headerRange = 'A5:Y5';
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
            $dataRange = 'A6:Y' . (5 + $totalRows);
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

            // Center align columns: No, Tanggal, Type, No Blending, Batch Range, numeric parameters, Disposisi, Analis, Waktu
            $centerCols = ['A', 'C', 'D', 'E', 'F', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'X', 'Y'];
            foreach ($centerCols as $col) {
                $sheet->getStyle($col . '6:' . $col . (5 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Right align volume & adjustment columns
            $sheet->getStyle('G6:G' . (5 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('U6:W' . (5 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Zebra striping
            for ($row = 6; $row <= 5 + $totalRows; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':Y' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
            }
        }

        return [];
    }
}
