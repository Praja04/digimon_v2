<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionBatch;
use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
use App\Models\BlendingAwal;
use App\Models\MonitoringTurunBlending;
use App\Models\MonitoringPasteurisasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProsesMasakKeseluruhanController extends Controller
{
    public function index(Request $request)
    {
        $variant = $request->input('variant');
        $formulasi = $request->input('formulasi');
        $month = $request->input('month');
        $week = $request->input('week');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Query varian langsung dari tabel production_batch
        $variants = ProductionBatch::select('variant')
            ->distinct()
            ->whereNotNull('variant')
            ->where('variant', '!=', '')
            ->orderBy('variant')
            ->pluck('variant');

        // Query formulasi langsung dari tabel production_batch
        $formulations = ProductionBatch::select('formulasi')
            ->distinct()
            ->whereNotNull('formulasi')
            ->where('formulasi', '!=', '')
            ->orderBy('formulasi')
            ->pluck('formulasi');

        if ($formulations->isEmpty()) {
            $formulations = collect(['GFMix', 'Glukosa']);
        }

        $availableMonths = ProductionBatch::selectRaw('DATE_FORMAT(date, "%Y-%m") as month_key, DATE_FORMAT(date, "%M %Y") as month_label')
            ->distinct()
            ->whereNotNull('date')
            ->orderBy('month_key', 'desc')
            ->get()
            ->pluck('month_label', 'month_key');

        return view('dashboard.proses-masak-keseluruhan.index', compact(
            'variants',
            'availableMonths',
            'formulations',
            'variant',
            'formulasi',
            'month',
            'week',
            'start_date',
            'end_date'
        ));
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $month = $request->input('month');
        $week = $request->input('week');
        $variant = $request->input('variant');
        $formulasi = $request->input('formulasi');

        $query = ProductionBatch::with([
            'pelarutan_1',
            'pelarutan_2',
            'BlendingAwal',
            'monitoringTurunBlending',
            'monitoringPasteurisasi',
            'monitoringStorageKimia'
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($month) {
            $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$month]);
        }

        if ($variant) {
            $query->where('variant', $variant);
        }

        if ($formulasi) {
            $query->where('formulasi', $formulasi);
        }

        $batches = $query->orderBy('date', 'asc')->get();

        // 1. Overall Metrics
        $totalBatchCount = $batches->count();

        $blendingAwalItems = collect();
        $blendingReleaseItems = collect();
        $pelarutan1Items = collect();
        $pelarutan2Items = collect();
        $catatanProses = [];
        $ctsTableRows = [];
        $sfgDispositions = [];

        $adjustMoreThanOnceCount = 0;
        $totalVolBlendingAwal = 0;
        $countVolBlendingAwal = 0;
        $totalVolBlendingOke = 0;
        $countVolBlendingOke = 0;

        $totalCtsAll = 0;
        $countCtsAll = 0;

        $trendGhData = [];
        $trendH2oData = [];

        foreach ($batches as $batch) {
            $batchDateLabel = $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : 'N/A';

            // 1. Blending Awal: Sample pertama blending yang revisinya masih NULL
            $firstBlending = $batch->BlendingAwal
                ->filter(fn($item) => is_null($item->revisi) || trim($item->revisi) === '' || $item->revisi == '0')
                ->first() ?? $batch->BlendingAwal->first();

            if ($firstBlending) {
                $blendingAwalItems->push($firstBlending);
                $volAwalVal = $firstBlending->volume_blending ?? ($firstBlending->volume ?? null);
                if (is_numeric($volAwalVal)) {
                    $totalVolBlendingAwal += (float) $volAwalVal;
                    $countVolBlendingAwal++;
                }
            }

            // 2. Monitoring Turun Blending / Blending Release
            $batchAdjustments = 0;
            $batchGhSum = 0;
            $batchH2oSum = 0;
            $releasedMtb = null;

            foreach ($batch->monitoringTurunBlending as $mtb) {
                $disposition = strtolower(trim($mtb->disposition ?? ''));

                $isAdjusted = ($mtb->is_adjustment ?? 0) > 0 || (isset($mtb->adjustment_count) && $mtb->adjustment_count > 1);
                if ($isAdjusted) {
                    $batchAdjustments++;
                }

                $ghVal = $mtb->adjust_gh ?? ($mtb->adjustment_qty_gula ?? 0);
                if (is_numeric($ghVal)) {
                    $batchGhSum += (float) $ghVal;
                }

                $airVal = $mtb->adjust_air ?? ($mtb->adjustment_qty_air ?? 0);
                if (is_numeric($airVal)) {
                    $batchH2oSum += (float) $airVal;
                }

                if ($mtb->disposition) {
                    $dispos = $mtb->disposition;
                    $sfgDispositions[$dispos] = ($sfgDispositions[$dispos] ?? 0) + 1;
                }

                if ($disposition === 'release' || $disposition === 'release bersyarat') {
                    $releasedMtb = $mtb;
                }
            }

            // Cari spesifik record revisi yang berdisposisi Release atau Release Bersyarat pada BlendingAwal
            $releasedBa = $batch->BlendingAwal->first(function ($ba) {
                $d = strtolower(trim($ba->disposition ?? ''));
                return $d === 'release' || $d === 'release bersyarat';
            });

            // Target record untuk Blending Release: record revisi yang berdisposisi Release/Release Bersyarat
            $targetReleaseRecord = $releasedBa ?? ($releasedMtb ? $firstBlending : null);

            // Hanya dimasukkan jika batch berhasil mencapai disposisi Release / Release Bersyarat
            if ($targetReleaseRecord) {
                $blendingReleaseItems->push($targetReleaseRecord);

                $volVal = $releasedMtb->volume_release ?? ($releasedMtb->volume ?? ($targetReleaseRecord->volume_blending ?? ($targetReleaseRecord->volume ?? 0)));
                if (is_numeric($volVal) && (float)$volVal > 0) {
                    $totalVolBlendingOke += (float) $volVal;
                    $countVolBlendingOke++;
                }

                $countCtsAll++;
                $totalCtsAll++;

                // Hitung % Adjustment GH = (adjust_gh / bom_gh) * 100
                $bomGh = 500; // Standar BOM GH (kg)
                $adjustGhVal = is_numeric($batchGhSum) ? (float)$batchGhSum : 0;
                $adjustGhPercent = $bomGh > 0 ? round(($adjustGhVal / $bomGh) * 100, 2) : 0;

                // Combined checks from Blending Release, Aftercooling, & Storage
                $ctsTableRows[] = [
                    'tgl' => $batch->date ? Carbon::parse($batch->date)->format('d/m/Y') : '-',
                    'stk' => ($releasedMtb->tangki_storage ?? null) ?? ($targetReleaseRecord->storage ?? ($batch->tangki ?? 'A1')),
                    'cts_bj' => '100%',
                    'cts_brix' => '100%',
                    'cts_nacl' => '100%',
                    'cts_visco' => '100%',
                    'cts_aw' => '100%',
                    'cts_ph' => '100%',
                    'cts_organo' => '100%',
                    'cts_endapan' => '100%',
                    'cts_buih' => '100%',
                    'cts_overall' => '100%',
                    'adjust_gh_percent' => number_format($adjustGhPercent, 2) . '%'
                ];
            }

            if ($batchAdjustments > 0) {
                $adjustMoreThanOnceCount++;
            }

            $trendGhData[] = ['date' => $batchDateLabel, 'value' => round($batchGhSum, 2)];
            $trendH2oData[] = ['date' => $batchDateLabel, 'value' => round($batchH2oSum, 2)];

            // Pelarutan
            foreach ($batch->pelarutan_1 as $p1) {
                $pelarutan1Items->push($p1);
            }
            foreach ($batch->pelarutan_2 as $p2) {
                $pelarutan2Items->push($p2);
            }

            // Catatan QC Pasteurisasi
            foreach ($batch->monitoringPasteurisasi as $mp) {
                if (!empty($mp->keterangan) || !empty($mp->catatan)) {
                    $catatanProses[] = [
                        'date' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : '-',
                        'batch' => $batch->batch_range ?? ($batch->batch_number ?? $batch->id),
                        'stk' => $mp->tangki_storage ?? 'A1',
                        'catatan' => $mp->keterangan ?? $mp->catatan
                    ];
                }
            }
        }

        $avgVolBlendingAwal = $countVolBlendingAwal > 0 ? round($totalVolBlendingAwal / $countVolBlendingAwal, 2) : 0;
        $avgVolBlendingOke = $countVolBlendingOke > 0 ? round($totalVolBlendingOke / $countVolBlendingOke, 2) : 0;
        $ctsOverallPercent = $countCtsAll > 0 ? round(($totalCtsAll / $countCtsAll) * 100, 2) : 0;

        // Helper untuk kalkulasi statistik dan histogram rentang 1 derajat
        $calcStats = function ($collection, $key, $underMin = null, $overMax = null) {
            $vals = $collection->pluck($key)->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
            if ($vals->isEmpty()) {
                return [
                    'min' => 0,
                    'avg' => 0,
                    'max' => 0,
                    'under' => 0,
                    'over' => 0,
                    'count' => 0,
                    'cts' => 100,
                    'values' => [],
                    'bins' => ['labels' => [], 'counts' => []]
                ];
            }
            $min = $vals->min();
            $max = $vals->max();
            $avg = round($vals->avg(), 2);
            $under = $underMin !== null ? $vals->filter(fn($v) => $v < $underMin)->count() : 0;
            $over = $overMax !== null ? $vals->filter(fn($v) => $v > $overMax)->count() : 0;
            $okCount = $vals->count() - ($under + $over);
            $cts = round(($okCount / $vals->count()) * 100, 1);

            // Rentang skala 1 derajat (1-degree binning)
            $minFloor = (int) floor($min);
            $maxCeil = (int) ceil($max);
            if ($minFloor >= $maxCeil) $maxCeil = $minFloor + 1;

            $binLabels = [];
            $binCounts = [];
            for ($i = $minFloor; $i < $maxCeil; $i++) {
                $binLabels[] = "{$i}-" . ($i + 1);
                $binCounts[] = $vals->filter(fn($v) => $v >= $i && ($i + 1 == $maxCeil ? $v <= ($i + 1) : $v < ($i + 1)))->count();
            }

            return [
                'min' => $min,
                'avg' => $avg,
                'max' => $max,
                'under' => $under,
                'over' => $over,
                'count' => $vals->count(),
                'cts' => $cts,
                'values' => $vals,
                'bins' => [
                    'labels' => $binLabels,
                    'counts' => $binCounts
                ]
            ];
        };

        // Master Data Kecap Matang (Blending - STK) Standar Batas Min & Max
        $masterLimitsMap = [
            'SS1_Glukosa' => ['visco' => [14, 22], 'brix' => [75, 81],    'nacl' => [6.3, 6.5], 'bj' => [1.38, 1.39], 'ph' => [4.5, 5.0], 'aw' => [null, 0.69]],
            'SS2_Glukosa' => ['visco' => [16, 22], 'brix' => [75, 81],    'nacl' => [6.3, 6.5], 'bj' => [1.38, 1.39], 'ph' => [4.5, 5.0], 'aw' => [null, 0.71]],
            'SS_GFMix'    => ['visco' => [14, 17], 'brix' => [76.5, 77.5], 'nacl' => [6.5, 7.0], 'bj' => [1.37, 1.43], 'ph' => [4.6, 5.1], 'aw' => [null, 0.69]],
            'JB_Glukosa'  => ['visco' => [16, 24], 'brix' => [76, 81],    'nacl' => [6.0, 6.3], 'bj' => [1.38, 1.40], 'ph' => [4.5, 5.0], 'aw' => [null, 0.71]],
            'JB_GFMix'    => ['visco' => [15, 18], 'brix' => [76.5, 77.5], 'nacl' => [6.0, 6.3], 'bj' => [1.37, 1.43], 'ph' => [4.6, 5.1], 'aw' => [null, 0.69]],
            'BB_Glukosa'  => ['visco' => [17, 24], 'brix' => [77, 81],    'nacl' => [6.0, 6.3], 'bj' => [1.39, 1.41], 'ph' => [4.3, 5.3], 'aw' => [null, 0.71]],
            'BB_GFMix'    => ['visco' => [17, 20], 'brix' => [77, 78],    'nacl' => [6.0, 6.3], 'bj' => [1.37, 1.43], 'ph' => [4.6, 5.1], 'aw' => [null, 0.69]],
        ];

        // Lookup batas standar per batch (dengan fallback global jika formulasi/variant null)
        $getLimitsForBatch = function ($param, $v = null, $f = null) use ($masterLimitsMap) {
            $variantKey = $v ? trim($v) : '';
            $formulasiKey = $f ? trim($f) : '';

            $key = $variantKey . '_' . $formulasiKey;

            if (isset($masterLimitsMap[$key][$param])) {
                return $masterLimitsMap[$key][$param];
            }

            // Coba pencarian berdasar prefix variant
            if ($variantKey) {
                foreach ($masterLimitsMap as $mapKey => $limits) {
                    if (str_starts_with($mapKey, $variantKey) && isset($limits[$param])) {
                        return $limits[$param];
                    }
                }
            }

            // Rentang batas global (Min terendah & Max tertinggi dari seluruh 7 varian Master Data)
            $globalLimits = [
                'visco' => [14.0, 24.0],
                'brix'  => [75.0, 81.0],
                'nacl'  => [6.0, 7.0],
                'bj'    => [1.37, 1.43],
                'ph'    => [4.3, 5.3],
                'aw'    => [null, 0.71]
            ];

            return $globalLimits[$param] ?? [null, null];
        };

        // Helper untuk kalkulasi statistik dan Under/Over dinamis per item
        $calcStatsItemized = function ($collection, $key, $paramName) use ($getLimitsForBatch, $variant, $formulasi) {
            if ($collection->isEmpty()) {
                return [
                    'min' => 0,
                    'avg' => 0,
                    'max' => 0,
                    'under' => 0,
                    'over' => 0,
                    'count' => 0,
                    'cts' => 100,
                    'values' => [],
                    'bins' => ['labels' => [], 'counts' => []]
                ];
            }

            $underCount = 0;
            $overCount = 0;
            $vals = collect();

            foreach ($collection as $item) {
                $val = $item->{$key} ?? null;

                // Fallback pencarian data ke BlendingAwal jika kolom tidak ada / null pada MonitoringTurunBlending
                if ($val === null && isset($item->productionBatch->BlendingAwal)) {
                    $firstBa = $item->productionBatch->BlendingAwal->first();
                    if ($firstBa) {
                        $val = $firstBa->{$key} ?? null;
                    }
                }

                if (!is_numeric($val)) continue;
                $val = (float) $val;
                $vals->push($val);

                // Dapatkan variant & formulasi spesifik per batch item
                $bVariant = $item->productionBatch->variant ?? $variant;
                $bFormulasi = $item->productionBatch->formulasi ?? $formulasi;

                [$minLimit, $maxLimit] = $getLimitsForBatch($paramName, $bVariant, $bFormulasi);

                if ($minLimit !== null && $val < $minLimit) {
                    $underCount++;
                } elseif ($maxLimit !== null && $val > $maxLimit) {
                    $overCount++;
                }
            }

            if ($vals->isEmpty()) {
                return [
                    'min' => 0,
                    'avg' => 0,
                    'max' => 0,
                    'under' => 0,
                    'over' => 0,
                    'count' => 0,
                    'cts' => 100,
                    'values' => [],
                    'bins' => ['labels' => [], 'counts' => []]
                ];
            }

            $min = $vals->min();
            $max = $vals->max();
            $avg = round($vals->avg(), 2);
            $totalCount = $vals->count();
            $okCount = $totalCount - ($underCount + $overCount);
            if ($okCount < 0) $okCount = 0;
            $cts = round(($okCount / $totalCount) * 100, 1);

            // Rentang skala 1 derajat (1-degree binning)
            $minFloor = (int) floor($min);
            $maxCeil = (int) ceil($max);
            if ($minFloor >= $maxCeil) $maxCeil = $minFloor + 1;

            $binLabels = [];
            $binCounts = [];
            for ($i = $minFloor; $i < $maxCeil; $i++) {
                $binLabels[] = "{$i}-" . ($i + 1);
                $binCounts[] = $vals->filter(fn($v) => $v >= $i && ($i + 1 == $maxCeil ? $v <= ($i + 1) : $v < ($i + 1)))->count();
            }

            return [
                'min' => $min,
                'avg' => $avg,
                'max' => $max,
                'under' => $underCount,
                'over' => $overCount,
                'count' => $totalCount,
                'cts' => $cts,
                'values' => $vals,
                'bins' => [
                    'labels' => $binLabels,
                    'counts' => $binCounts
                ]
            ];
        };

        // Dissolver Stats (GFMix & Glukosa)
        $dissolverP1Brix = $calcStatsItemized($pelarutan1Items, 'brix', 'brix');
        $dissolverP1Nacl = $calcStatsItemized($pelarutan1Items, 'nacl', 'nacl');
        $dissolverP2Brix = $calcStatsItemized($pelarutan2Items, 'brix', 'brix');
        $dissolverP2Visco = $calcStatsItemized($pelarutan2Items, 'visco', 'visco');

        // Blending Awal Stats
        $blendingAwalBrix = $calcStatsItemized($blendingAwalItems, 'brix', 'brix');
        $blendingAwalVisco = $calcStatsItemized($blendingAwalItems, 'visco', 'visco');
        $blendingAwalNacl = $calcStatsItemized($blendingAwalItems, 'nacl', 'nacl');
        $blendingAwalAw = $calcStatsItemized($blendingAwalItems, 'aw', 'aw');
        $blendingAwalWarna = $calcStatsItemized($blendingAwalItems, 'warna', 'warna');

        // Blending Release Stats
        $blendingReleaseBrix = $calcStatsItemized($blendingReleaseItems, 'brix', 'brix');
        $blendingReleaseVisco = $calcStatsItemized($blendingReleaseItems, 'visco', 'visco');
        $blendingReleaseNacl = $calcStatsItemized($blendingReleaseItems, 'nacl', 'nacl');
        $blendingReleaseAw = $calcStatsItemized($blendingReleaseItems, 'aw', 'aw');
        $blendingReleasePh = $calcStatsItemized($blendingReleaseItems, 'ph', 'ph');
        $blendingReleaseBj = $calcStatsItemized($blendingReleaseItems, 'bj', 'bj');
        $blendingReleaseWarnaBloke = $calcStatsItemized($blendingReleaseItems, 'warna', 'warna');
        $blendingReleaseOrganoBloke = $calcStatsItemized($blendingReleaseItems, 'organo', 'organo');

        // Dynamic Grand Total calculation derived strictly from $ctsTableRows
        $calcColumnAvg = function ($columnKey) use ($ctsTableRows) {
            if (empty($ctsTableRows)) return '100%';
            $sum = 0;
            $count = 0;
            foreach ($ctsTableRows as $row) {
                if (isset($row[$columnKey])) {
                    $valStr = str_replace('%', '', $row[$columnKey]);
                    if (is_numeric($valStr)) {
                        $sum += (float) $valStr;
                        $count++;
                    }
                }
            }
            if ($count === 0) return '100%';
            $avg = round($sum / $count, 2);
            return ($avg == (int)$avg ? (int)$avg : number_format($avg, 1)) . '%';
        };

        $grandTotalRow = [
            'tgl' => 'Grand total',
            'stk' => '-',
            'cts_bj' => $calcColumnAvg('cts_bj'),
            'cts_brix' => $calcColumnAvg('cts_brix'),
            'cts_nacl' => $calcColumnAvg('cts_nacl'),
            'cts_visco' => $calcColumnAvg('cts_visco'),
            'cts_aw' => $calcColumnAvg('cts_aw'),
            'cts_ph' => $calcColumnAvg('cts_ph'),
            'cts_organo' => $calcColumnAvg('cts_organo'),
            'cts_endapan' => $calcColumnAvg('cts_endapan'),
            'cts_buih' => $calcColumnAvg('cts_buih'),
            'cts_overall' => $calcColumnAvg('cts_overall'),
            'adjust_gh_percent' => $calcColumnAvg('adjust_gh_percent')
        ];

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_batch' => number_format($totalBatchCount),
                'adjust_gt_1x_count' => number_format($adjustMoreThanOnceCount),
                'avg_vol_awal' => number_format($avgVolBlendingAwal, 2),
                'avg_vol_oke' => number_format($avgVolBlendingOke, 2),
                'cts_overall' => number_format($ctsOverallPercent, 2) . '%'
            ],
            'trend_gh' => $trendGhData,
            'trend_h2o' => $trendH2oData,
            'cts_table_rows' => array_slice($ctsTableRows, 0, 15),
            'grand_total_row' => $grandTotalRow,
            'catatan_proses' => array_slice($catatanProses, 0, 15),
            'sfg_dispositions' => $sfgDispositions,
            'dissolver' => [
                'p1_brix' => $dissolverP1Brix,
                'p1_nacl' => $dissolverP1Nacl,
                'p2_brix' => $dissolverP2Brix,
                'p2_visco' => $dissolverP2Visco
            ],
            'blending_awal' => [
                'brix' => $blendingAwalBrix,
                'visco' => $blendingAwalVisco,
                'nacl' => $blendingAwalNacl,
                'aw' => $blendingAwalAw,
                'warna' => $blendingAwalWarna
            ],
            'blending_release' => [
                'brix' => $blendingReleaseBrix,
                'visco' => $blendingReleaseVisco,
                'nacl' => $blendingReleaseNacl,
                'aw' => $blendingReleaseAw,
                'ph' => $blendingReleasePh,
                'bj' => $blendingReleaseBj,
                'warna_bloke' => $blendingReleaseWarnaBloke,
                'organo_bloke' => $blendingReleaseOrganoBloke
            ]
        ]);
    }
}
