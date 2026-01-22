<?php

namespace App\Exports;

use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PressTestMesin1Export implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    protected $tanggal;
    protected $variant;
    protected $status;
    protected $limit;
    protected $rowNumber = 1;

    public function __construct($tanggal, $variant, $status, $limit)
    {
        $this->tanggal = $tanggal;
        $this->variant = $variant;
        $this->status  = $status;
        $this->limit   = $limit;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $response = Http::get('http://10.11.10.130:8081/api/press-test-mesin-1/all');
        $data = collect($response->json()['data'] ?? []);

        // Filter tanggal
        if ($this->tanggal) {
            $data = $data->filter(function ($item) {
                return substr($item['created_at'], 0, 10) === $this->tanggal;
            });
        }

        // Filter variant
        if ($this->variant) {
            $data = $data->where('variant', $this->variant);
        }

        // Filter status
        if ($this->status) {
            $data = $data->where('status', $this->status);
        }

        // Urutkan terbaru
        $data = $data->sortByDesc('created_at');

        // Limit
        if ($this->limit && $this->limit !== 'all') {
            $data = $data->take((int) $this->limit);
        }

        return $data->values()->map(function ($item) {
            return [
                $this->rowNumber++,
                $item['variant'] ?? '',
                isset($item['jarak']) ? number_format((float) $item['jarak'], 3, '.', '') : '0.000',
                isset($item['batas']) ? number_format((float) $item['batas'], 3, '.', '') : '0.000',
                $item['status'] ?? '',
                isset($item['created_at'])
                    ? \Carbon\Carbon::parse($item['created_at'])->setTimezone('Asia/Jakarta')->format('d-m-Y')
                    : '',
                isset($item['created_at'])
                    ? \Carbon\Carbon::parse($item['created_at'])->setTimezone('Asia/Jakarta')->format('H:i:s')
                    : '',
            ];
        });
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Variant',
            'Jarak (cm)',
            'Batas (cm)',
            'Status',
            'Tanggal',
            'Waktu',
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $styles = [
            // Header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '4F81BD',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],

            // Kolom No (center)
            'A2:A' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Semua data border
            'A1:G' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];

        for ($row = 2; $row <= $lastRow; $row++) {
            $status = strtolower(trim($sheet->getCell('E' . $row)->getValue()));

            if ($status === 'bocor') {
                $styles['A' . $row . ':G' . $row] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F8D7DA',
                        ],
                    ],
                ];
            }
        }

        return $styles;
    }


    /**
     * Lebar Kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Variant
            'C' => 15,  // Jarak
            'D' => 15,  // Batas
            'E' => 12,  // Status
            'F' => 15,  // Tanggal
            'G' => 12,  // Waktu
        ];
    }
}
