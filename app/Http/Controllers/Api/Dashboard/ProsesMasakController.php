<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlendingAwal;
use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
use App\Models\ProductionBatch;
use Carbon\Carbon;

class ProsesMasakController extends Controller
{
    public function getData(Request $request)
    {
        // Get filter parameters
        $variant = $request->input('variant');
        $formulasi = $request->input('formulasi');
        $month = $request->input('month');
        $week = $request->input('week');
        $date = $request->input('date');

        // Default: Set tanggal hari ini jika tidak ada filter
        $today = Carbon::now()->format('Y-m-d');
        if (!$variant && !$formulasi && !$month && !$week && !$date) {
            $date = $today;
        }

        // Build query for production batches based on filters
        $batchQuery = ProductionBatch::query();

        if ($variant) {
            $batchQuery->where('variant', $variant);
        }

        if ($formulasi) {
            // Will be implemented later when formulasi field is added
            // $batchQuery->where('formulasi', $formulasi);
        }

        if ($month && !$week) {
            // Filter by month only
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $batchQuery->whereYear('date', $monthDate->year)
                ->whereMonth('date', $monthDate->month);
        }

        if ($week) {
            // Parse week format: YYYY-MM-WN (e.g., 2026-02-W1)
            $weekParts = explode('-W', $week);
            if (count($weekParts) == 2) {
                $monthPart = $weekParts[0]; // YYYY-MM
                $weekNum = $weekParts[1];   // 1, 2, 3, 4, or 5

                $monthDate = Carbon::createFromFormat('Y-m', $monthPart);

                // Filter by month and week of month
                $batchQuery->whereYear('date', $monthDate->year)
                    ->whereMonth('date', $monthDate->month)
                    ->whereRaw('WEEK(date, 1) - WEEK(DATE_SUB(date, INTERVAL DAYOFMONTH(date) - 1 DAY), 1) + 1 = ?', [$weekNum]);
            }
        }

        if ($date) {
            // Filter by specific date
            $batchQuery->whereDate('date', $date);
        }

        // Get filtered production batch IDs
        $productionBatchIds = $batchQuery->pluck('id');

        // Build query for Pelarutan1 with production batch filter
        $pelarutan1Query = Pelarutan1::query();
        if ($productionBatchIds->isNotEmpty()) {
            $pelarutan1Query->whereIn('production_batch_id', $productionBatchIds);
        } else {
            // If no batches match, return empty collection
            $pelarutan1Query->whereRaw('1 = 0');
        }
        $pelarutan1Data = $pelarutan1Query->get();

        // Build query for Pelarutan2 with production batch filter
        $pelarutan2Query = Pelarutan2::query();
        if ($productionBatchIds->isNotEmpty()) {
            $pelarutan2Query->whereIn('production_batch_id', $productionBatchIds);
        } else {
            $pelarutan2Query->whereRaw('1 = 0');
        }
        $pelarutan2Data = $pelarutan2Query->get();

        // Build query for Blending Awal with production batch filter
        $blendingQuery = BlendingAwal::with('color');
        if ($productionBatchIds->isNotEmpty()) {
            $blendingQuery->whereIn('production_batch_id', $productionBatchIds);
        } else {
            $blendingQuery->whereRaw('1 = 0');
        }
        $blendingData = $blendingQuery->get();

        // === DISSOLVER DATA (Pelarutan 1 & Pelarutan 2 separated) ===
        $pelarutan1Brix = $pelarutan1Data->pluck('brix')->filter()->values();
        $pelarutan2Brix = $pelarutan2Data->pluck('brix')->filter()->values();

        $dissolverStats = [
            'pelarutan1' => [
                'min' => $pelarutan1Brix->count() > 0 ? $pelarutan1Brix->min() : '-',
                'avg' => $pelarutan1Brix->count() > 0 ? round($pelarutan1Brix->avg(), 2) : '-',
                'max' => $pelarutan1Brix->count() > 0 ? $pelarutan1Brix->max() : '-',
                'data' => $pelarutan1Brix->toArray(),
                'cts' => $this->calculateCTS($pelarutan1Brix, 68, 72)
            ],
            'pelarutan2' => [
                'min' => $pelarutan2Brix->count() > 0 ? $pelarutan2Brix->min() : '-',
                'avg' => $pelarutan2Brix->count() > 0 ? round($pelarutan2Brix->avg(), 2) : '-',
                'max' => $pelarutan2Brix->count() > 0 ? $pelarutan2Brix->max() : '-',
                'data' => $pelarutan2Brix->toArray(),
                'cts' => $this->calculateCTS($pelarutan2Brix, 68, 72)
            ]
        ];

        // === BLENDING AWAL DATA (All disposition) ===
        $blendingBrix = $blendingData->pluck('brix')->filter();
        $blendingNacl = $blendingData->pluck('nacl')->filter();
        $blendingVisco = $blendingData->pluck('visco')->filter();
        $blendingAw = $blendingData->pluck('aw')->filter();
        $blendingPh = $blendingData->pluck('ph')->filter();

        $blendingAwalStats = [
            'brix' => [
                'min' => $blendingBrix->count() > 0 ? $blendingBrix->min() : '-',
                'avg' => $blendingBrix->count() > 0 ? round($blendingBrix->avg(), 2) : '-',
                'max' => $blendingBrix->count() > 0 ? $blendingBrix->max() : '-',
                'data' => $blendingBrix->toArray(),
                'cts' => $this->calculateCTS($blendingBrix, 67, 73)
            ],
            'nacl' => [
                'min' => $blendingNacl->count() > 0 ? $blendingNacl->min() : '-',
                'avg' => $blendingNacl->count() > 0 ? round($blendingNacl->avg(), 2) : '-',
                'max' => $blendingNacl->count() > 0 ? $blendingNacl->max() : '-',
                'data' => $blendingNacl->toArray(),
                'cts' => $this->calculateCTS($blendingNacl, 15, 17)
            ],
            'visco' => [
                'min' => $blendingVisco->count() > 0 ? $blendingVisco->min() : '-',
                'avg' => $blendingVisco->count() > 0 ? round($blendingVisco->avg(), 2) : '-',
                'max' => $blendingVisco->count() > 0 ? $blendingVisco->max() : '-',
                'data' => $blendingVisco->toArray(),
                'cts' => $this->calculateCTS($blendingVisco, 100, 300)
            ],
            'aw' => [
                'min' => $blendingAw->count() > 0 ? $blendingAw->min() : '-',
                'avg' => $blendingAw->count() > 0 ? round($blendingAw->avg(), 4) : '-',
                'max' => $blendingAw->count() > 0 ? $blendingAw->max() : '-',
                'data' => $blendingAw->toArray(),
                'cts' => $this->calculateCTS($blendingAw, 0.7, 0.8)
            ],
            'ph' => [
                'min' => $blendingPh->count() > 0 ? $blendingPh->min() : '-',
                'avg' => $blendingPh->count() > 0 ? round($blendingPh->avg(), 2) : '-',
                'max' => $blendingPh->count() > 0 ? $blendingPh->max() : '-',
                'data' => $blendingPh->toArray(),
                'cts' => $this->calculateCTS($blendingPh, 4.0, 6.0)
            ],
        ];

        // Organo count
        $organoCount = $blendingData->groupBy('organo')->map(function ($items) {
            return $items->count();
        });

        // Color count
        $colorCount = $blendingData->groupBy('color_id')->map(function ($items) {
            return [
                'count' => $items->count(),
                'color_name' => $items->first()->color->name ?? 'Unknown'
            ];
        });

        // === BLENDING RELEASE DATA ===
        $releaseDispositions = ['Release', 'Release Bersyarat', 'Resampling', 'Adjustment', 'Reject', 'Repro', 'Jalan Bareng', 'Leveling'];
        $blendingRelease = $blendingData->whereIn('disposition', $releaseDispositions);

        $releaseBrix = $blendingRelease->pluck('brix')->filter();
        $releaseNacl = $blendingRelease->pluck('nacl')->filter();
        $releaseBj = $blendingRelease->pluck('bj')->filter();

        $blendingReleaseStats = [
            'brix' => [
                'min' => $releaseBrix->count() > 0 ? $releaseBrix->min() : '-',
                'avg' => $releaseBrix->count() > 0 ? round($releaseBrix->avg(), 2) : '-',
                'max' => $releaseBrix->count() > 0 ? $releaseBrix->max() : '-',
                'data' => $releaseBrix->toArray(),
                'cts' => $this->calculateCTS($releaseBrix, 67, 73)
            ],
            'nacl' => [
                'min' => $releaseNacl->count() > 0 ? $releaseNacl->min() : '-',
                'avg' => $releaseNacl->count() > 0 ? round($releaseNacl->avg(), 2) : '-',
                'max' => $releaseNacl->count() > 0 ? $releaseNacl->max() : '-',
                'data' => $releaseNacl->toArray(),
                'cts' => $this->calculateCTS($releaseNacl, 15, 17)
            ],
            'organo' => [
                'count' => $blendingRelease->groupBy('organo')->map(fn($items) => $items->count()),
                'cts' => 100.00
            ],
            'bj' => [
                'min' => $releaseBj->count() > 0 ? $releaseBj->min() : '-',
                'avg' => $releaseBj->count() > 0 ? round($releaseBj->avg(), 3) : '-',
                'max' => $releaseBj->count() > 0 ? $releaseBj->max() : '-',
                'data' => $releaseBj->toArray(),
                'cts' => $this->calculateCTS($releaseBj, 1.22, 1.30)
            ],
        ];

        // Warna Bloke for Release
        $warnaBloke = $blendingRelease->groupBy('color_id')->map(function ($items) {
            return [
                'count' => $items->count(),
                'color_name' => $items->first()->color->name ?? 'Unknown'
            ];
        });

        // Organo Bloke for Release
        $organoBloke = $blendingRelease->groupBy('organo')->map(function ($items) {
            return $items->count();
        });

        // === PIE CHART DISPOSITION ===
        $dispositionList = ['Release', 'Release Bersyarat', 'Resampling', 'Reject', 'Repro', 'Jalan Bareng', 'Leveling'];

        $dispositionCounts = array_fill_keys($dispositionList, 0);

        foreach ($pelarutan1Data as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $dispositionCounts[$item->disposition]++;
            }
        }

        foreach ($pelarutan2Data as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $dispositionCounts[$item->disposition]++;
            }
        }

        foreach ($blendingData as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $dispositionCounts[$item->disposition]++;
            }
        }

        $totalDisposition = array_sum($dispositionCounts);

        $totalPelarutan1 = 0;
        $totalPelarutan2 = 0;
        $totalBlending = 0;

        foreach ($pelarutan1Data as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $totalPelarutan1++;
            }
        }
        foreach ($pelarutan2Data as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $totalPelarutan2++;
            }
        }
        foreach ($blendingData as $item) {
            if (isset($dispositionCounts[$item->disposition])) {
                $totalBlending++;
            }
        }

        $sourceBreakdown = [
            ['label' => 'Pelarutan 1', 'count' => $totalPelarutan1, 'percentage' => $totalDisposition > 0 ? round(($totalPelarutan1 / $totalDisposition) * 100, 1) : 0],
            ['label' => 'Pelarutan 2', 'count' => $totalPelarutan2, 'percentage' => $totalDisposition > 0 ? round(($totalPelarutan2 / $totalDisposition) * 100, 1) : 0],
            ['label' => 'Blending', 'count' => $totalBlending, 'percentage' => $totalDisposition > 0 ? round(($totalBlending / $totalDisposition) * 100, 1) : 0],
        ];

        $dispositionData = collect($dispositionCounts)->map(function ($count, $label) use ($totalDisposition) {
            return [
                'label' => $label,
                'count' => $count,
                'percentage' => $totalDisposition > 0 ? round(($count / $totalDisposition) * 100, 1) : 0
            ];
        })->values();

        // === CATATAN PROSES MASAK ===
        $catatanProses = collect();

        // From Pelarutan 1
        $pelarutan1Remarks = $pelarutan1Data->map(function ($item) {
            return [
                'tgl' => $item->scanned_at ? Carbon::parse($item->scanned_at)->format('Y-m-d') : '-',
                'batch' => $item->dissolver_number ?? '-',
                'catatan' => $item->disposition_remark ?? '-',
                'type' => 'Dissolver Pelarutan 1'
            ];
        });

        // From Pelarutan 2
        $pelarutan2Remarks = $pelarutan2Data->map(function ($item) {
            return [
                'tgl' => $item->scanned_at ? Carbon::parse($item->scanned_at)->format('Y-m-d') : '-',
                'batch' => $item->dissolver_number ?? '-',
                'catatan' => $item->disposition_remark ?? '-',
                'type' => 'Dissolver Pelarutan 2'
            ];
        });

        // From Blending
        $blendingRemarks = $blendingData->map(function ($item) {
            return [
                'tgl' => $item->scanned_at ? Carbon::parse($item->scanned_at)->format('Y-m-d') : '-',
                'batch' => $item->batch_range ?? '-',
                'catatan' => $item->disposition_remark ?? '-',
                'type' => 'Blending'
            ];
        });

        $catatanProses = $pelarutan1Remarks->merge($pelarutan2Remarks)->merge($blendingRemarks)
            ->filter(function ($item) {
                return $item['catatan'] !== '-' && !empty($item['catatan']);
            })
            ->sortByDesc('tgl')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'dissolverStats' => $dissolverStats,
                'blendingAwalStats' => $blendingAwalStats,
                'blendingReleaseStats' => $blendingReleaseStats,
                'organoCount' => $organoCount,
                'colorCount' => $colorCount,
                'warnaBloke' => $warnaBloke,
                'organoBloke' => $organoBloke,
                'dispositionData' => $dispositionData->values(),
                'sourceBreakdown' => $sourceBreakdown,
                'catatanProses' => $catatanProses
            ]
        ]);
    }

    public function getWeeks(Request $request)
    {
        $month = $request->input('month');

        if (!$month) {
            return response()->json([
                'success' => false,
                'weeks' => []
            ]);
        }

        // Parse selected month (format: YYYY-MM)
        $monthDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $weeks = [];

        // Get all dates in the selected month from production_batches
        $datesInMonth = ProductionBatch::whereYear('date', $monthDate->year)
            ->whereMonth('date', $monthDate->month)
            ->select('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date');

        // Group dates by week of month
        $weekGroups = $datesInMonth->groupBy(function ($date) {
            return Carbon::parse($date)->weekOfMonth;
        });

        // Create week options
        foreach ($weekGroups as $weekNum => $dates) {
            $weekLabel = "Week {$weekNum} ({$monthDate->format('M Y')})";
            $weeks[$month . '-W' . $weekNum] = $weekLabel;
        }

        return response()->json([
            'success' => true,
            'weeks' => $weeks
        ]);
    }

    public function getFilterOptions()
    {
        // Get list of variants for dropdown
        $variants = ProductionBatch::select('variant')
            ->distinct()
            ->whereNotNull('variant')
            ->orderBy('variant')
            ->pluck('variant');

        // Generate months based on available production dates
        $availableMonths = ProductionBatch::selectRaw('DATE_FORMAT(date, "%Y-%m") as month_key, DATE_FORMAT(date, "%M %Y") as month_label')
            ->distinct()
            ->whereNotNull('date')
            ->orderBy('month_key', 'desc')
            ->get()
            ->pluck('month_label', 'month_key');

        return response()->json([
            'success' => true,
            'variants' => $variants,
            'availableMonths' => $availableMonths
        ]);
    }

    /**
     * Calculate CTS (Capability to Specification) percentage
     * 
     * @param \Illuminate\Support\Collection $data
     * @param float $lowerLimit
     * @param float $upperLimit
     * @return float
     */
    private function calculateCTS($data, $lowerLimit, $upperLimit)
    {
        if ($data->count() === 0) {
            return 0;
        }

        $withinSpec = $data->filter(function ($value) use ($lowerLimit, $upperLimit) {
            return $value >= $lowerLimit && $value <= $upperLimit;
        })->count();

        return round(($withinSpec / $data->count()) * 100, 2);
    }
}
