<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimbanganRetailMesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimbanganRetailExport;

class TimbanganRetailMesinController extends Controller
{
    // ── KONSTANTA VARIANT STANDARDS ──────────────────────────────────────────
    private const VARIANT_STANDARDS = [
        // 'Sachet YB 12,5gr PCS'     => ['min' =>   12.05, 'std' =>   13.05, 'max' =>   14.05, 'tu1' =>   11.93, 'tu2' =>   10.80, 'code' => 'S12.5G-P'],
        // 'Sachet YB 12,5gr RENCENG' => ['min' =>  154.60, 'std' =>  156.60, 'max' =>  168.60, 'tu1' =>  143.10, 'tu2' =>  129.60, 'code' => 'S12.5G-R'],
        'Sachet YB 20gr PCS'       => ['min' =>   19.14, 'std' =>   20.64, 'max' =>   21.64, 'tu1' =>   18.84, 'tu2' =>   17.04, 'code' => 'S20G-P'],
        'Sachet YB 20gr RENCENG'   => ['min' =>  244.68, 'std' =>  247.68, 'max' =>  259.68, 'tu1' =>  226.08, 'tu2' =>  204.48, 'code' => 'S20G-R'],
        'Sachet BB 40gr PCS'       => ['min' =>   39.10, 'std' =>   41.10, 'max' =>   42.10, 'tu1' =>   37.50, 'tu2' =>   33.90, 'code' => 'S40G-P'],
        'Sachet BB 40gr RENCENG'   => ['min' =>  489.20, 'std' =>  493.20, 'max' =>  505.20, 'tu1' =>  450.00, 'tu2' =>  406.80, 'code' => 'S40G-R'],
        'Pouch YB 77gr'            => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20, 'code' => 'P77G-YB'],
        'Pouch BB 77gr'            => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20, 'code' => 'P77G-BB'],
        'Pouch YB 250gr'           => ['min' =>  253.00, 'std' =>  255.00, 'max' =>  257.00, 'tu1' =>  246.00, 'tu2' =>  237.00, 'code' => 'P250G'],
        'Pouch BB 270gr'           => ['min' =>  273.00, 'std' =>  275.00, 'max' =>  277.00, 'tu1' =>  266.00, 'tu2' =>  257.00, 'code' => 'P270G'],
        'Pouch YB 550gr'           => ['min' =>  556.00, 'std' =>  561.00, 'max' =>  566.00, 'tu1' =>  545.80, 'tu2' =>  530.80, 'code' => 'P550G'],
        'Pouch YB 700gr'           => ['min' =>  706.00, 'std' =>  711.00, 'max' =>  716.00, 'tu1' =>  696.00, 'tu2' =>  681.00, 'code' => 'P700G'],
        'Pouch BB 725gr'           => ['min' =>  730.00, 'std' =>  735.00, 'max' =>  740.00, 'tu1' =>  720.00, 'tu2' =>  705.00, 'code' => 'P725G'],
        'Pouch YB 1000gr'          => ['min' => 1007.50, 'std' => 1012.50, 'max' => 1017.50, 'tu1' =>  997.50, 'tu2' =>  982.50, 'code' => 'P1000G'],
        'Sachet BB 40gr RENCENG (6+1)' => ['min' =>  569.40, 'std' =>  575.40, 'max' =>  589.40, 'tu1' =>  525.00, 'tu2' =>  474.60, 'code' => 'S40G-R(6+1)'],
        'Sachet YB 20gr RENCENG (6+1)' => ['min' =>  284.46, 'std' =>  288.96, 'max' =>  302.96, 'tu1' =>  263.76, 'tu2' =>  238.56, 'code' => 'S20G-R(6+1)'],
    ];

    // ── HELPERS ───────────────────────────────────────────────────────────────

    private function isAbnormal(float $berat, string $variant): bool
    {
        $s = self::VARIANT_STANDARDS[$variant] ?? null;
        if (!$s) return false;
        return $berat < $s['tu1'];
    }

    private function getSeverity(float $berat, string $variant): string
    {
        $s = self::VARIANT_STANDARDS[$variant] ?? null;
        if (!$s) return 'normal';
        if ($berat < $s['tu2']) return 'kritis';  // <TU2
        if ($berat < $s['tu1']) return 'warning'; // TU2→TU1 (warning)
        return 'normal';
    }

    private function classify(float $berat, string $variant): string
    {
        $s = self::VARIANT_STANDARDS[$variant] ?? null;
        if (!$s) return 'tu1ToStd';
        if ($berat > $s['max'])  return 'overMax';
        if ($berat >= $s['std']) return 'stdToMax';
        if ($berat >= $s['tu1']) return 'tu1ToStd';
        if ($berat >= $s['tu2']) return 'tu2ToTu1';
        return 'underTu2';
    }

    private function blankCounts(): array
    {
        return ['underTu2' => 0, 'tu2ToTu1' => 0, 'tu1ToStd' => 0, 'stdToMax' => 0, 'overMax' => 0];
    }

    private function buildStats(array $weights, array $counts): array
    {
        if (empty($weights)) {
            return [
                'total' => 0, 'avg' => null, 'min' => null, 'max' => null,
                'under' => 0, 'over' => 0, 'counts' => $counts,
            ];
        }
        $n = count($weights);
        return [
            'total'  => $n,
            'avg'    => round(array_sum($weights) / $n, 3),
            'min'    => round(min($weights), 3),
            'max'    => round(max($weights), 3),
            'under'  => $counts['underTu2'],
            'over'   => $counts['overMax'],
            'counts' => $counts,
        ];
    }

    // ── SHIFT HELPERS ─────────────────────────────────────────────────────────

    private function getShiftRange(string $date): array
    {
        $day = Carbon::parse($date);
        return [
            'start' => $day->copy()->setTime(6, 0, 0),
            'end'   => $day->copy()->addDay()->setTime(5, 59, 59),
        ];
    }

    private function getShiftLabel(Carbon $time): string
    {
        $hour = (int) $time->format('H');
        if ($hour >= 6 && $hour < 14)  return 'Shift 1';
        if ($hour >= 14 && $hour < 22) return 'Shift 2';
        return 'Shift 3';
    }

    private function getShiftKey(string $waktu): string
    {
        $hour = (int) substr($waktu, 11, 2);
        if ($hour >= 6 && $hour < 14)  return 'shift1';
        if ($hour >= 14 && $hour < 22) return 'shift2';
        return 'shift3';
    }

    /**
     * Bangun date range berdasarkan shift.
     *
     * Shift 1 : start_date 06:00 → end_date 13:59:59
     * Shift 2 : start_date 14:00 → end_date 21:59:59
     * Shift 3 : start_date 22:00 → end_date+1 05:59:59  ← lintas tengah malam
     * default  : start_date 06:00 → end_date+1 05:59:59  (semua shift, 1 production day)
     *
     * Dengan range berbasis datetime (bukan TIME()), tidak ada lagi
     * kebocoran data shift 3 malam sebelumnya.
     */
    private function buildDateRangeWithShift(Request $request): array
    {
        $shift     = $request->filled('shift') ? $request->shift : null;
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        return match ($shift) {
            '1' => [
                $startDate->copy()->setTime(6,  0,  0)->toDateTimeString(),
                $endDate->copy()->setTime(13, 59, 59)->toDateTimeString(),
            ],
            '2' => [
                $startDate->copy()->setTime(14,  0,  0)->toDateTimeString(),
                $endDate->copy()->setTime(21, 59, 59)->toDateTimeString(),
            ],
            '3' => [
                $startDate->copy()->setTime(22,  0,  0)->toDateTimeString(),
                $endDate->copy()->addDay()->setTime(5, 59, 59)->toDateTimeString(),
            ],
            default => [
                $startDate->copy()->setTime(6, 0, 0)->toDateTimeString(),
                $endDate->copy()->addDay()->setTime(5, 59, 59)->toDateTimeString(),
            ],
        };
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/filter-options
    // ═══════════════════════════════════════════════════════════════════════
    public function filterOptions()
    {
        $variants = array_keys(self::VARIANT_STANDARDS);
        $mesins   = array_unique(array_merge(
            ['F', 'G', 'H', 'I', 'D', 'E', 'J', 'K', 'C', 'L', 'AE', 'AG'],
            ['B', 'AF', 'AI', 'AJ'],
            ['AH'],
            ['A', 'U', 'V'],
            ['O', 'P', 'W', 'X'],
            ['Q', 'R'],
            ['Y', 'Z']
        ));

        sort($variants);
        sort($mesins);

        return response()->json([
            'success'  => true,
            'variants' => $variants,
            'mesins'   => $mesins,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/data
    // ═══════════════════════════════════════════════════════════════════════
    public function getData(Request $request)
    {
        $request->validate([
            'date'    => 'required|date_format:Y-m-d',
            'variant' => 'nullable|string',
            'mesin'   => 'nullable|string',
        ]);

        $range = $this->getShiftRange($request->date);

        $query = TimbanganRetailMesin::whereBetween('waktu', [
            $range['start']->toDateTimeString(),
            $range['end']->toDateTimeString(),
        ]);

        if ($request->filled('variant')) $query->where('variant', $request->variant);
        if ($request->filled('mesin'))   $query->where('mesin',   $request->mesin);

        $records = $query->orderBy('waktu')->get();

        $shifts = ['Shift 1' => [], 'Shift 2' => [], 'Shift 3' => []];
        foreach ($records as $row) {
            $label = $this->getShiftLabel(Carbon::parse($row->waktu));
            $shifts[$label][] = $row;
        }

        $shiftStats = [];
        foreach ($shifts as $shiftName => $rows) {
            if (empty($rows)) {
                $shiftStats[$shiftName] = ['count' => 0, 'total' => 0, 'average' => null, 'min' => null, 'max' => null];
                continue;
            }
            $berats = array_map(fn ($r) => (float) $r->berat, $rows);
            $shiftStats[$shiftName] = [
                'count'   => count($berats),
                'total'   => round(array_sum($berats), 3),
                'average' => round(array_sum($berats) / count($berats), 3),
                'min'     => round(min($berats), 3),
                'max'     => round(max($berats), 3),
            ];
        }

        $chartData = $records->map(fn ($r) => [
            'x'      => $r->waktu,
            'y'      => (float) $r->berat,
            'mesin'  => $r->mesin,
            'varian' => $r->variant,
            'shift'  => $this->getShiftLabel(Carbon::parse($r->waktu)),
        ]);

        $allBerats = $records->pluck('berat')->map(fn ($b) => (float) $b);
        $summary = [
            'total_transaksi' => $records->count(),
            'total_berat'     => round($allBerats->sum(), 3),
            'average_berat'   => $records->count() > 0 ? round($allBerats->average(), 3) : null,
        ];

        $perMesin = $records->groupBy('mesin')->map(function ($rows, $mesinName) {
            $berats = $rows->pluck('berat')->map(fn ($b) => (float) $b);
            $latest = $rows->sortByDesc('waktu')->first();
            return [
                'mesin'             => $mesinName,
                'jumlah_transaksi'  => $rows->count(),
                'average_berat'     => round($berats->average(), 3),
                'min_berat'         => round($berats->min(), 3),
                'max_berat'         => round($berats->max(), 3),
                'transaksi_terbaru' => [
                    'waktu'   => $latest?->waktu,
                    'berat'   => $latest?->berat,
                    'status'  => $latest?->status,
                    'variant' => $latest?->variant,
                ],
            ];
        })->values();

        return response()->json([
            'success'     => true,
            'date'        => $request->date,
            'summary'     => $summary,
            'shift_stats' => $shiftStats,
            'per_mesin'   => $perMesin,
            'chart_data'  => $chartData,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/export
    // ═══════════════════════════════════════════════════════════════════════
    public function export(Request $request)
    {
        $request->validate([
            'date'    => 'required|date_format:Y-m-d',
            'variant' => 'nullable|string',
            'mesin'   => 'nullable|string',
        ]);

        $range = $this->getShiftRange($request->date);

        $query = TimbanganRetailMesin::whereBetween('waktu', [
            $range['start']->toDateTimeString(),
            $range['end']->toDateTimeString(),
        ])->orderBy('waktu');

        if ($request->filled('variant')) $query->where('variant', $request->variant);
        if ($request->filled('mesin'))   $query->where('mesin',   $request->mesin);

        $records = $query->get()->map(function ($row) {
            return [
                'Mesin'   => $row->mesin,
                'Variant' => $row->variant,
                'Waktu'   => $row->waktu,
                'Shift'   => $this->getShiftLabel(Carbon::parse($row->waktu)),
                'Status'  => $row->status,
                'Berat'   => $row->berat,
                'Unit'    => $row->unit,
                'NIK'     => $row->nik,
            ];
        });

        $filename = 'timbangan-retail-' . $request->date;
        if ($request->filled('variant')) $filename .= '-' . $request->variant;
        if ($request->filled('mesin'))   $filename .= '-' . $request->mesin;
        $filename .= '.xlsx';

        return Excel::download(new TimbanganRetailExport($records), $filename);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/average-minmax
    // ═══════════════════════════════════════════════════════════════════════
    public function getAverageMinMax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
            'shift'      => 'nullable|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // FIX: pakai buildDateRangeWithShift — tidak ada lagi TIME() filter yang bocor
        [$start, $end] = $this->buildDateRangeWithShift($request);

        $rows = DB::table('timbangan_retail_mesin')
            ->select('waktu', 'berat', 'variant', 'mesin')
            ->whereBetween('waktu', [$start, $end])
            ->when($request->filled('varian'), fn ($q) => $q->where('variant', trim($request->varian)))
            ->when($request->filled('mesin'),  fn ($q) => $q->where('mesin',   trim($request->mesin)))
            ->orderBy('waktu')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success'  => true,
                'shift1'   => ['total_transaksi' => 0, 'avg' => null, 'min' => null, 'max' => null, 'counts' => $this->blankCounts()],
                'shift2'   => ['total_transaksi' => 0, 'avg' => null, 'min' => null, 'max' => null, 'counts' => $this->blankCounts()],
                'shift3'   => ['total_transaksi' => 0, 'avg' => null, 'min' => null, 'max' => null, 'counts' => $this->blankCounts()],
                'variants' => [],
                'mesins'   => [],
            ]);
        }

        $shifts = [
            'shift1' => ['weights' => [], 'counts' => $this->blankCounts()],
            'shift2' => ['weights' => [], 'counts' => $this->blankCounts()],
            'shift3' => ['weights' => [], 'counts' => $this->blankCounts()],
        ];
        $variants = [];
        $mesins   = [];

        foreach ($rows as $row) {
            $berat   = (float) $row->berat;
            $variant = $row->variant ?? '';
            $mesin   = $row->mesin   ?? '';

            $shiftKey = $this->getShiftKey($row->waktu);
            $cls      = $this->classify($berat, $variant);

            $shifts[$shiftKey]['weights'][]    = $berat;
            $shifts[$shiftKey]['counts'][$cls]++;

            if (!isset($variants[$variant])) {
                $variants[$variant] = ['weights' => [], 'counts' => $this->blankCounts()];
            }
            $variants[$variant]['weights'][]    = $berat;
            $variants[$variant]['counts'][$cls]++;

            if (!isset($mesins[$mesin])) {
                $mesins[$mesin] = [];
            }
            if (!isset($mesins[$mesin][$variant])) {
                $mesins[$mesin][$variant] = ['weights' => [], 'counts' => $this->blankCounts()];
            }
            $mesins[$mesin][$variant]['weights'][]    = $berat;
            $mesins[$mesin][$variant]['counts'][$cls]++;
        }

        $shiftResult = [];
        foreach ($shifts as $k => $d) {
            $stats = $this->buildStats($d['weights'], $d['counts']);
            $shiftResult[$k] = [
                'total_transaksi' => $stats['total'],
                'avg'             => $stats['avg'],
                'min'             => $stats['min'],
                'max'             => $stats['max'],
                'counts'          => $stats['counts'],
            ];
        }

        $variantResult = [];
        foreach ($variants as $v => $d) {
            $variantResult[$v] = $this->buildStats($d['weights'], $d['counts']);
        }

        $mesinResult = [];
        foreach ($mesins as $m => $variantBuckets) {
            $allWeights = [];
            $allCounts  = $this->blankCounts();
            $perVariant = [];

            foreach ($variantBuckets as $variantName => $d) {
                $perVariant[$variantName] = $this->buildStats($d['weights'], $d['counts']);
                $allWeights = array_merge($allWeights, $d['weights']);
                foreach ($d['counts'] as $cls => $cnt) {
                    $allCounts[$cls] += $cnt;
                }
            }

            $mesinResult[$m] = [
                'variants' => $perVariant,
                'combined' => $this->buildStats($allWeights, $allCounts),
            ];
        }

        return response()->json([
            'success'    => true,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'varian'     => $request->varian,
            'mesin'      => $request->mesin,
            'shift1'     => $shiftResult['shift1'],
            'shift2'     => $shiftResult['shift2'],
            'shift3'     => $shiftResult['shift3'],
            'variants'   => $variantResult,
            'mesins'     => $mesinResult,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/chart
    // ═══════════════════════════════════════════════════════════════════════
    public function getChartData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
            'shift'      => 'nullable|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // FIX: pakai buildDateRangeWithShift — tidak ada lagi TIME() filter yang bocor
        [$start, $end] = $this->buildDateRangeWithShift($request);

        $rows = DB::table('timbangan_retail_mesin')
            ->select(['mesin', 'variant', 'waktu', 'berat', 'status'])
            ->whereBetween('waktu', [$start, $end])
            ->when($request->filled('varian'), fn ($q) => $q->where('variant', trim($request->varian)))
            ->when($request->filled('mesin'),  fn ($q) => $q->where('mesin',   trim($request->mesin)))
            ->orderBy('waktu')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => true,
                'shift1'  => ['samples' => []],
                'shift2'  => ['samples' => []],
                'shift3'  => ['samples' => []],
                'mesins'  => [],
                'data'    => [],
            ]);
        }

        $shiftSamples = ['shift1' => [], 'shift2' => [], 'shift3' => []];
        $mesinSamples = [];
        $flatData     = [];

        foreach ($rows as $row) {
            $shiftKey = $this->getShiftKey($row->waktu);
            $m        = $row->mesin;
            $v        = $row->variant;

            $flatData[] = [
                'mesin'   => $m,
                'variant' => $v,
                'waktu'   => $row->waktu,
                'berat'   => (float) $row->berat,
                'status'  => $row->status,
                'shift'   => ucfirst(str_replace('shift', 'Shift ', $shiftKey)),
            ];

            $shiftSamples[$shiftKey][] = ['berat' => (float) $row->berat, 'waktu' => $row->waktu];

            if (!isset($mesinSamples[$m])) {
                $mesinSamples[$m] = ['variants' => []];
            }
            if (!isset($mesinSamples[$m]['variants'][$v])) {
                $mesinSamples[$m]['variants'][$v] = ['samples' => []];
            }
            $mesinSamples[$m]['variants'][$v]['samples'][] = [
                'berat' => (float) $row->berat,
                'waktu' => $row->waktu,
            ];
        }

        return response()->json([
            'success'    => true,
            'filters'    => [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'varian'     => $request->varian,
                'mesin'      => $request->mesin,
            ],
            'total_data' => count($flatData),
            'shift1'     => ['samples' => $shiftSamples['shift1']],
            'shift2'     => ['samples' => $shiftSamples['shift2']],
            'shift3'     => ['samples' => $shiftSamples['shift3']],
            'mesins'     => $mesinSamples,
            'data'       => $flatData,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/mesin/dashboard
    // ═══════════════════════════════════════════════════════════════════════
    public function getDashboardData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'shift'   => 'nullable|in:1,2,3',
            'status'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tanggal = $request->filled('tanggal') ? $request->tanggal : today()->format('Y-m-d');
        $shift   = $request->shift;
        $status  = $request->status;

        $query = TimbanganRetailMesin::whereDate('waktu', $tanggal);

        if ($request->filled('shift')) {
            if ($shift == '1') {
                $query->whereTime('waktu', '>=', '06:00:00')->whereTime('waktu', '<', '14:00:00');
            } elseif ($shift == '2') {
                $query->whereTime('waktu', '>=', '14:00:00')->whereTime('waktu', '<', '22:00:00');
            } elseif ($shift == '3') {
                $tanggalBesok = date('Y-m-d', strtotime($tanggal . ' +1 day'));
                $query->where(function ($q) use ($tanggal, $tanggalBesok) {
                    $q->where(fn ($s) => $s->whereDate('waktu', $tanggal)->whereTime('waktu', '>=', '22:00:00'))
                        ->orWhere(fn ($s) => $s->whereDate('waktu', $tanggalBesok)->whereTime('waktu', '<', '06:00:00'));
                });
            }
        }

        if ($request->filled('status')) $query->where('status', $status);

        $beratHariIni     = (clone $query)->sum('berat');
        $transaksiHariIni = (clone $query)->count();
        $rataRataBerat    = (clone $query)->avg('berat');
        $mesinAktif       = (clone $query)->select('mesin', DB::raw('count(*) as total'))->groupBy('mesin')->orderBy('total', 'desc')->first();
        $dataMesin        = (clone $query)->select('mesin', DB::raw('count(*) as total_transaksi'), DB::raw('sum(berat) as total_berat'))->groupBy('mesin')->orderBy('total_transaksi', 'desc')->limit(10)->get();
        $transaksiTerbaru = (clone $query)->orderBy('waktu', 'desc')->limit(10)->get();
        $transaksiPerJam  = (clone $query)->select(DB::raw('HOUR(waktu) as jam'), DB::raw('count(*) as total'))->groupBy('jam')->orderBy('jam')->get();
        $beratPerVariant  = (clone $query)->select('variant', DB::raw('sum(berat) as total_berat'))->groupBy('variant')->orderBy('total_berat', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'filters'    => ['tanggal' => $tanggal, 'shift' => $shift, 'status' => $status],
                'statistics' => [
                    'berat_hari_ini'     => (float) $beratHariIni,
                    'transaksi_hari_ini' => $transaksiHariIni,
                    'rata_rata_berat'    => (float) ($rataRataBerat ?? 0),
                    'mesin_aktif'        => $mesinAktif ? ['mesin' => $mesinAktif->mesin, 'total' => $mesinAktif->total] : null,
                ],
                'data_mesin'        => $dataMesin,
                'transaksi_terbaru' => $transaksiTerbaru,
                'transaksi_per_jam' => $transaksiPerJam,
                'berat_per_variant' => $beratPerVariant,
            ],
        ], 200);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // POST /api/mesin
    // ═══════════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik'     => 'required|string|max:50',
            'mesin'   => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'waktu'   => 'required|date',
            'status'  => 'required|string',
            'berat'   => 'required|numeric',
            'unit'    => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $mesin = TimbanganRetailMesin::create($request->only(['nik', 'mesin', 'variant', 'waktu', 'status', 'berat', 'unit']));

        return response()->json(['success' => true, 'data' => $mesin], 201);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // POST /api/mesin2
    // ═══════════════════════════════════════════════════════════════════════
    public function store2(Request $request)
    {
        $validVariants = array_keys(self::VARIANT_STANDARDS);

        $validator = Validator::make($request->all(), [
            'nik'     => 'required|string|max:50',
            'mesin'   => 'required|string|max:255',
            'variant' => ['required', 'string', 'max:255', 'in:' . implode(',', $validVariants)],
            'waktu'   => 'required|date',
            'status'  => 'required|in:OK,NOT OK',
            'filler'  => 'nullable|in:1,2,3,4,5,6,7,8',
            'berat'   => 'required|numeric|between:0,999999',
            'unit'    => 'required|in:g,kg',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $mesin = TimbanganRetailMesin::create($request->only(['nik', 'mesin', 'variant', 'waktu', 'status', 'filler', 'berat', 'unit']));

        return response()->json(['success' => true, 'data' => $mesin], 201);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // POST /api/timbangan-retail/import
    // ═══════════════════════════════════════════════════════════════════════
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $headerRowIndex = null;
            $columnMap = [];

            // Scan first 10 rows to find header
            foreach ($rows as $index => $row) {
                $normalizedRow = array_map(fn($val) => strtolower(trim($val ?? '')), $row);

                $hasMesin = in_array('mesin', $normalizedRow) || in_array('machine', $normalizedRow);
                $hasVariant = in_array('variant', $normalizedRow) || in_array('varian', $normalizedRow);
                $hasBerat = in_array('berat', $normalizedRow) || in_array('weight', $normalizedRow);
                $hasTanggal = in_array('tanggal', $normalizedRow) || in_array('date', $normalizedRow);

                if ($hasMesin && $hasVariant && $hasBerat) {
                    $headerRowIndex = $index;
                    foreach ($row as $colKey => $cellValue) {
                        $normalizedName = strtolower(trim($cellValue ?? ''));
                        if ($normalizedName === 'nik' || $normalizedName === 'operator') {
                            $columnMap['nik'] = $colKey;
                        } elseif ($normalizedName === 'mesin' || $normalizedName === 'machine') {
                            $columnMap['mesin'] = $colKey;
                        } elseif ($normalizedName === 'variant' || $normalizedName === 'varian' || $normalizedName === 'produk') {
                            $columnMap['variant'] = $colKey;
                        } elseif ($normalizedName === 'filler') {
                            $columnMap['filler'] = $colKey;
                        } elseif ($normalizedName === 'tanggal' || $normalizedName === 'date') {
                            $columnMap['tanggal'] = $colKey;
                        } elseif ($normalizedName === 'waktu' || $normalizedName === 'time') {
                            $columnMap['waktu'] = $colKey;
                        } elseif ($normalizedName === 'berat' || $normalizedName === 'weight') {
                            $columnMap['berat'] = $colKey;
                        } elseif ($normalizedName === 'unit' || $normalizedName === 'satuan') {
                            $columnMap['unit'] = $colKey;
                        } elseif ($normalizedName === 'status') {
                            $columnMap['status'] = $colKey;
                        }
                    }
                    break;
                }
            }

            if ($headerRowIndex === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file Excel tidak sesuai. Pastikan file Excel memiliki baris header dengan kolom "NIK", "Mesin", "Variant", "Tanggal", "Waktu", dan "Berat".'
                ], 422);
            }

            $successCount = 0;
            $duplicateCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if ($index <= $headerRowIndex) {
                    continue;
                }

                $isEmpty = true;
                foreach ($row as $val) {
                    if ($val !== null && trim($val) !== '') {
                        $isEmpty = false;
                        break;
                    }
                }
                if ($isEmpty) {
                    continue;
                }

                $nik = isset($columnMap['nik']) ? trim($row[$columnMap['nik']] ?? '') : 'UNKNOWN';
                $mesin = isset($columnMap['mesin']) ? trim($row[$columnMap['mesin']] ?? '') : '';
                $variant = isset($columnMap['variant']) ? trim($row[$columnMap['variant']] ?? '') : '';
                $filler = isset($columnMap['filler']) ? trim($row[$columnMap['filler']] ?? '') : null;
                $tanggalVal = isset($columnMap['tanggal']) ? trim($row[$columnMap['tanggal']] ?? '') : '';
                $waktuVal = isset($columnMap['waktu']) ? trim($row[$columnMap['waktu']] ?? '') : '';
                $beratVal = isset($columnMap['berat']) ? trim($row[$columnMap['berat']] ?? '') : '';
                $unit = isset($columnMap['unit']) ? trim($row[$columnMap['unit']] ?? '') : 'g';
                $status = isset($columnMap['status']) ? trim($row[$columnMap['status']] ?? '') : '';

                if (empty($mesin) && empty($variant) && empty($beratVal)) {
                    continue;
                }

                if (empty($mesin) || empty($variant) || empty($beratVal)) {
                    $failedCount++;
                    $errors[] = "Baris $index: Kolom Mesin, Variant, atau Berat kosong.";
                    continue;
                }

                // Parse Date and Time
                $waktu = null;
                try {
                    $rawTanggal = $sheet->getCell($columnMap['tanggal'] . $index)->getValue();
                    $rawWaktu = isset($columnMap['waktu']) ? $sheet->getCell($columnMap['waktu'] . $index)->getValue() : null;

                    $dateStr = '';
                    if (is_numeric($rawTanggal)) {
                        $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTanggal);
                        $dateStr = $dateObj->format('Y-m-d');
                    } else {
                        $dateStr = date('Y-m-d', strtotime(str_replace('/', '-', $tanggalVal)));
                    }

                    $timeStr = '00:00:00';
                    if ($rawWaktu !== null) {
                        if (is_numeric($rawWaktu)) {
                            $timeObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawWaktu);
                            $timeStr = $timeObj->format('H:i:s');
                        } else {
                            $timeStr = date('H:i:s', strtotime($waktuVal));
                        }
                    }

                    $waktu = $dateStr . ' ' . $timeStr;
                    if (strtotime($waktu) === false) {
                        throw new \Exception("Format waktu tidak valid");
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Baris $index: Format Tanggal/Waktu tidak valid ('$tanggalVal $waktuVal').";
                    continue;
                }

                // Normalize Variant mapping
                $matchedVariant = null;
                $cleanVariant = strtolower(trim($variant));
                foreach (self::VARIANT_STANDARDS as $key => $std) {
                    if (strtolower(trim($key)) === $cleanVariant || strtolower(trim($std['code'] ?? '')) === $cleanVariant) {
                        $matchedVariant = $key;
                        break;
                    }
                }

                if (!$matchedVariant) {
                    $failedCount++;
                    $errors[] = "Baris $index: Variant '$variant' tidak terdaftar di sistem.";
                    continue;
                }

                // Clean weight
                $berat = filter_var($beratVal, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if (!is_numeric($berat)) {
                    $failedCount++;
                    $errors[] = "Baris $index: Nilai Berat '$beratVal' bukan angka valid.";
                    continue;
                }
                $berat = (float) $berat;

                // Check duplicates
                $query = TimbanganRetailMesin::where('mesin', $mesin)
                    ->where('variant', $matchedVariant)
                    ->where('waktu', $waktu)
                    ->where('berat', $berat)
                    ->where('nik', $nik);
                if ($filler !== null) {
                    $query->where('filler', $filler);
                }
                $exists = $query->exists();

                if ($exists) {
                    $duplicateCount++;
                    continue;
                }

                // Determine status
                if (empty($status)) {
                    $status = $this->isAbnormal($berat, $matchedVariant) ? 'NOT OK' : 'OK';
                } else {
                    $status = strtoupper(trim($status));
                    if ($status !== 'OK' && $status !== 'NOT OK') {
                        $status = $this->isAbnormal($berat, $matchedVariant) ? 'NOT OK' : 'OK';
                    }
                }

                // Create record
                TimbanganRetailMesin::create([
                    'nik' => $nik,
                    'mesin' => $mesin,
                    'variant' => $matchedVariant,
                    'waktu' => $waktu,
                    'filler' => $filler,
                    'berat' => $berat,
                    'unit' => $unit,
                    'status' => $status
                ]);
                $successCount++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Proses import selesai.',
                'stats' => [
                    'imported' => $successCount,
                    'skipped' => $duplicateCount,
                    'failed' => $failedCount
                ],
                'errors' => $errors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor file Excel: ' . $e->getMessage()
            ], 500);
        }
    }


    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/abnormal-log
    // Params: start_date, end_date, shift?, varian?, mesin?, nik?, severity?, per_page?, page?
    // ═══════════════════════════════════════════════════════════════════════
    public function getAbnormalLog(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'shift'      => 'nullable|in:1,2,3',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
            'nik'        => 'nullable|string',
            'severity'   => 'nullable|in:kritis,warning,over',
            'per_page'   => 'nullable|integer|min:10|max:200',
        ]);

        // FIX: pakai buildDateRangeWithShift — hapus applyShiftFilter
        [$start, $end] = $this->buildDateRangeWithShift($request);

        $query = DB::table('timbangan_retail_mesin')
            ->select(['id', 'nik', 'mesin', 'variant', 'waktu', 'berat', 'status'])
            ->whereBetween('waktu', [$start, $end])
            ->orderBy('waktu', 'desc');

        if ($request->filled('varian')) $query->where('variant', trim($request->varian));
        if ($request->filled('mesin'))  $query->where('mesin',   trim($request->mesin));
        if ($request->filled('nik'))    $query->where('nik',     trim($request->nik));

        $rows = $query->get();

        $abnormal = $rows->filter(function ($row) {
            return $this->isAbnormal((float) $row->berat, $row->variant ?? '');
        })->map(function ($row) {
            $berat    = (float) $row->berat;
            $variant  = $row->variant ?? '';
            $std      = self::VARIANT_STANDARDS[$variant] ?? null;
            $severity = $this->getSeverity($berat, $variant);

            return [
                'id'           => $row->id,
                'waktu'        => $row->waktu,
                'nik'          => $row->nik,
                'mesin'        => $row->mesin,
                'variant'      => $variant,
                'variant_code' => $std['code'] ?? '—',
                'shift'        => $this->getShiftLabel(Carbon::parse($row->waktu)),
                'berat'        => $berat,
                'std_value'    => $std['std']  ?? null,
                'selisih'      => $std ? round($berat - $std['std'], 3) : null,
                'batas_min'    => $std['tu1']  ?? null,
                'batas_max'    => $std['max']  ?? null,
                'severity'     => $severity,
                'status'       => $row->status,
            ];
        })->values();

        if ($request->filled('severity')) {
            $abnormal = $abnormal->filter(fn ($r) => $r['severity'] === $request->severity)->values();
        }

        $perPage = (int) ($request->per_page ?? 50);
        $page    = (int) ($request->page ?? 1);
        $total   = $abnormal->count();
        $items   = $abnormal->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'success'     => true,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
            'data'        => $items,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/abnormal-summary
    // Params: start_date, end_date, shift?, varian?, mesin?
    // ═══════════════════════════════════════════════════════════════════════
    public function getAbnormalSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'shift'      => 'nullable|in:1,2,3',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
        ]);

        // FIX: pakai buildDateRangeWithShift — hapus applyShiftFilter
        [$start, $end] = $this->buildDateRangeWithShift($request);

        $query = DB::table('timbangan_retail_mesin')
            ->select(['nik', 'mesin', 'variant', 'waktu', 'berat'])
            ->whereBetween('waktu', [$start, $end]);

        if ($request->filled('varian')) $query->where('variant', trim($request->varian));
        if ($request->filled('mesin'))  $query->where('mesin',   trim($request->mesin));

        $rows = $query->orderBy('waktu')->get();

        $totalSampel = $rows->count();
        $kritis = $warning = $over = 0;
        $paretoMesin   = [];
        $paretoVariant = [];

        foreach ($rows as $row) {
            $berat   = (float) $row->berat;
            $variant = $row->variant ?? '';

            if (!$this->isAbnormal($berat, $variant)) continue;

            $sev = $this->getSeverity($berat, $variant);
            if ($sev === 'kritis')      $kritis++;
            elseif ($sev === 'warning') $warning++;
            elseif ($sev === 'over')    $over++;

            $paretoMesin[$row->mesin] = ($paretoMesin[$row->mesin] ?? 0) + 1;
            $paretoVariant[$variant]  = ($paretoVariant[$variant]  ?? 0) + 1;
        }

        $totalAbnormal = $kritis + $warning + $over;
        arsort($paretoMesin);
        arsort($paretoVariant);

        $paretoMesinOut = [];
        $cumul = 0;
        foreach (array_slice($paretoMesin, 0, 10, true) as $m => $cnt) {
            $cumul += $cnt;
            $paretoMesinOut[] = [
                'mesin'      => $m,
                'count'      => $cnt,
                'pct'        => $totalAbnormal > 0 ? round($cnt / $totalAbnormal * 100, 1) : 0,
                'cumulative' => $totalAbnormal > 0 ? round($cumul / $totalAbnormal * 100, 1) : 0,
            ];
        }

        $paretoVariantOut = [];
        $cumul = 0;
        foreach (array_slice($paretoVariant, 0, 10, true) as $v => $cnt) {
            $cumul += $cnt;
            $std = self::VARIANT_STANDARDS[$v] ?? null;
            $paretoVariantOut[] = [
                'variant'    => $v,
                'code'       => $std['code'] ?? $v,
                'count'      => $cnt,
                'pct'        => $totalAbnormal > 0 ? round($cnt / $totalAbnormal * 100, 1) : 0,
                'cumulative' => $totalAbnormal > 0 ? round($cumul / $totalAbnormal * 100, 1) : 0,
            ];
        }

        return response()->json([
            'success'        => true,
            'total_sampel'   => $totalSampel,
            'total_abnormal' => $totalAbnormal,
            'pct_abnormal'   => $totalSampel > 0 ? round($totalAbnormal / $totalSampel * 100, 2) : 0,
            'kritis'         => $kritis,
            'warning'        => $warning,
            'over'           => $over,
            'pareto_mesin'   => $paretoMesinOut,
            'pareto_variant' => $paretoVariantOut,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/operator-stats
    // Params: start_date, end_date, shift?, varian?, mesin?
    // ═══════════════════════════════════════════════════════════════════════
    public function getOperatorStats(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'shift'      => 'nullable|in:1,2,3',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
        ]);

        // FIX: pakai buildDateRangeWithShift — hapus applyShiftFilter
        [$start, $end] = $this->buildDateRangeWithShift($request);

        $query = DB::table('timbangan_retail_mesin')
            ->select(['nik', 'mesin', 'variant', 'waktu', 'berat'])
            ->whereBetween('waktu', [$start, $end]);

        if ($request->filled('varian')) $query->where('variant', trim($request->varian));
        if ($request->filled('mesin'))  $query->where('mesin',   trim($request->mesin));

        $rows = $query->orderBy('waktu')->get();

        $ops = [];
        foreach ($rows as $row) {
            $nik   = $row->nik ?? 'UNKNOWN';
            $berat = (float) $row->berat;
            $var   = $row->variant ?? '';

            if (!isset($ops[$nik])) {
                $ops[$nik] = [
                    'nik'       => $nik,
                    'total'     => 0,
                    'abnormal'  => 0,
                    'kritis'    => 0,
                    'warning'   => 0,
                    'over'      => 0,
                    'mesins'    => [],
                    'last_seen' => $row->waktu,
                ];
            }

            $ops[$nik]['total']++;
            if ($row->waktu > $ops[$nik]['last_seen']) {
                $ops[$nik]['last_seen'] = $row->waktu;
            }
            if (!in_array($row->mesin, $ops[$nik]['mesins'])) {
                $ops[$nik]['mesins'][] = $row->mesin;
            }

            if ($this->isAbnormal($berat, $var)) {
                $ops[$nik]['abnormal']++;
                $sev = $this->getSeverity($berat, $var);
                $ops[$nik][$sev]++;
            }
        }

        $result = collect($ops)->map(function ($op) {
            return [
                'nik'          => $op['nik'],
                'total'        => $op['total'],
                'abnormal'     => $op['abnormal'],
                'pct_abnormal' => $op['total'] > 0 ? round($op['abnormal'] / $op['total'] * 100, 1) : 0,
                'kritis'       => $op['kritis'],
                'warning'      => $op['warning'],
                'over'         => $op['over'],
                'mesins'       => implode(', ', $op['mesins']),
                'last_seen'    => $op['last_seen'],
            ];
        })->sortByDesc('abnormal')->values();

        return response()->json([
            'success'        => true,
            'total_operator' => $result->count(),
            'data'           => $result,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/hourly-heatmap
    // Params: start_date, end_date, varian?, mesin?
    // ═══════════════════════════════════════════════════════════════════════
    public function getHourlyHeatmap(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
        ]);

        // Heatmap selalu tampilkan full production day (tidak ada filter shift)
        $start = Carbon::parse($request->start_date)->setTime(6, 0, 0)->toDateTimeString();
        $end   = Carbon::parse($request->end_date)->addDay()->setTime(5, 59, 59)->toDateTimeString();

        $rows = DB::table('timbangan_retail_mesin')
            ->select(['variant', 'waktu', 'berat'])
            ->whereBetween('waktu', [$start, $end])
            ->when($request->filled('varian'), fn ($q) => $q->where('variant', trim($request->varian)))
            ->when($request->filled('mesin'),  fn ($q) => $q->where('mesin',   trim($request->mesin)))
            ->orderBy('waktu')
            ->get();

        $matrix = array_fill(0, 24, ['total' => 0, 'abnormal' => 0]);

        foreach ($rows as $row) {
            $hour  = (int) substr($row->waktu, 11, 2);
            $berat = (float) $row->berat;
            $var   = $row->variant ?? '';

            $matrix[$hour]['total']++;
            if ($this->isAbnormal($berat, $var)) {
                $matrix[$hour]['abnormal']++;
            }
        }

        $result = [];
        for ($h = 0; $h < 24; $h++) {
            $t = $matrix[$h]['total'];
            $a = $matrix[$h]['abnormal'];

            if ($h >= 6 && $h < 14)       $shift = 'Shift 1';
            elseif ($h >= 14 && $h < 22)   $shift = 'Shift 2';
            else                            $shift = 'Shift 3';

            $result[] = [
                'jam'          => sprintf('%02d:00', $h),
                'shift'        => $shift,
                'total'        => $t,
                'abnormal'     => $a,
                'pct_abnormal' => $t > 0 ? round($a / $t * 100, 1) : 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/mesin-ranking
    // Params: start_date, end_date, shift?, varian?, mesin?
    // ═══════════════════════════════════════════════════════════════════════
    public function getMesinRanking(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'shift'      => 'nullable|in:1,2,3',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
        ]);

        [$start, $end] = $this->buildDateRangeWithShift($request);

        $rankingData = $this->calculateRankingForRange(
            $start,
            $end,
            $request->varian,
            $request->mesin
        );

        return response()->json(array_merge([
            'success'          => true,
            'variant_standards' => collect(self::VARIANT_STANDARDS)->map(fn ($s, $name) => [
                'name' => $name,
                'code' => $s['code'],
                'min'  => $s['min'],
                'std'  => $s['std'],
                'max'  => $s['max'],
                'tu1'  => $s['tu1'],
                'tu2'  => $s['tu2'],
            ])->values(),
        ], $rankingData));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // GET /api/timbangan-retail/realtime-ranking
    // Params: varian?, mesin?
    // ═══════════════════════════════════════════════════════════════════════
    public function getRealtimeRanking(Request $request)
    {
        $varian = $request->filled('varian') ? $request->varian : null;
        $mesin  = $request->filled('mesin') ? $request->mesin : null;

        $now = Carbon::now();
        $hour = (int)$now->format('H');

        // Determine shift ranges
        if ($hour >= 6 && $hour < 14) {
            $currentLabel = 'Shift 1';
            $currentStart = $now->copy()->setTime(6, 0, 0);
            $currentEnd = $now->copy()->setTime(13, 59, 59);

            $prevLabel = 'Shift 3';
            $prevStart = $now->copy()->subDay()->setTime(22, 0, 0);
            $prevEnd = $now->copy()->setTime(5, 59, 59);
        } elseif ($hour >= 14 && $hour < 22) {
            $currentLabel = 'Shift 2';
            $currentStart = $now->copy()->setTime(14, 0, 0);
            $currentEnd = $now->copy()->setTime(21, 59, 59);

            $prevLabel = 'Shift 1';
            $prevStart = $now->copy()->setTime(6, 0, 0);
            $prevEnd = $now->copy()->setTime(13, 59, 59);
        } else {
            $currentLabel = 'Shift 3';
            if ($hour >= 22) {
                $currentStart = $now->copy()->setTime(22, 0, 0);
                $currentEnd = $now->copy()->addDay()->setTime(5, 59, 59);

                $prevLabel = 'Shift 2';
                $prevStart = $now->copy()->setTime(14, 0, 0);
                $prevEnd = $now->copy()->setTime(21, 59, 59);
            } else {
                $currentStart = $now->copy()->subDay()->setTime(22, 0, 0);
                $currentEnd = $now->copy()->setTime(5, 59, 59);

                $prevLabel = 'Shift 2';
                $prevStart = $now->copy()->subDay()->setTime(14, 0, 0);
                $prevEnd = $now->copy()->subDay()->setTime(21, 59, 59);
            }
        }

        // Fetch previous shift data
        $prevData = $this->calculateRankingForRange(
            $prevStart->toDateTimeString(),
            $prevEnd->toDateTimeString(),
            $varian,
            $mesin
        );

        // Fetch current running shift data
        $currentData = $this->calculateRankingForRange(
            $currentStart->toDateTimeString(),
            $currentEnd->toDateTimeString(),
            $varian,
            $mesin
        );

        return response()->json([
            'success' => true,
            'previous' => [
                'label'      => $prevLabel,
                'start'      => $prevStart->toDateTimeString(),
                'end'        => $prevEnd->toDateTimeString(),
                'date_label' => $prevStart->format('d M Y'),
                'time_label' => $prevStart->format('H:i') . ' - ' . $prevEnd->format('H:i'),
                'stats'      => $prevData,
            ],
            'current' => [
                'label'      => $currentLabel,
                'start'      => $currentStart->toDateTimeString(),
                'end'        => $currentEnd->toDateTimeString(),
                'date_label' => $currentStart->format('d M Y'),
                'time_label' => $currentStart->format('H:i') . ' - ' . $currentEnd->format('H:i'),
                'stats'      => $currentData,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER: Calculate Ranking for specific datetime range
    // ═══════════════════════════════════════════════════════════════════════
    private function calculateRankingForRange($start, $end, $varian = null, $mesin = null)
    {
        $rows = DB::table('timbangan_retail_mesin')
            ->select(['mesin', 'variant', 'waktu', 'berat', 'status', 'nik', 'filler'])
            ->whereBetween('waktu', [$start, $end])
            ->when($varian, fn ($q) => $q->where('variant', trim($varian)))
            ->when($mesin,  fn ($q) => $q->where('mesin',   trim($mesin)))
            ->orderBy('waktu')
            ->get();

        $groups = [];
        $globalTotal = 0;
        $globalAbnormal = 0;
        $globalCounts = ['underTu2' => 0, 'tu2ToTu1' => 0, 'tu1ToStd' => 0, 'stdToMax' => 0, 'overMax' => 0];

        foreach ($rows as $row) {
            $berat   = (float) $row->berat;
            $variant = $row->variant ?? '';
            $m       = $row->mesin ?? '';
            $key     = $m . '|' . $variant;

            $cls = $this->classify($berat, $variant);
            $globalCounts[$cls]++;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'mesin'       => $m,
                    'variant'     => $variant,
                    'total'       => 0,
                    'abnormal'    => 0,
                    'kritis'      => 0,
                    'warning'     => 0,
                    'over'        => 0,
                    'operators'   => [],
                    'total_berat' => 0,
                    'last_waktu'  => $row->waktu,
                ];
            }

            $groups[$key]['total']++;
            $groups[$key]['total_berat'] += $berat;
            $globalTotal++;

            if ($row->waktu > $groups[$key]['last_waktu']) {
                $groups[$key]['last_waktu'] = $row->waktu;
            }

            $nik = $row->nik ?? 'UNKNOWN';
            if (!in_array($nik, $groups[$key]['operators'])) {
                $groups[$key]['operators'][] = $nik;
            }

            if ($this->isAbnormal($berat, $variant)) {
                $groups[$key]['abnormal']++;
                $globalAbnormal++;

                $sev = $this->getSeverity($berat, $variant);
                if (isset($groups[$key][$sev])) {
                    $groups[$key][$sev]++;
                }
            }
        }

        // Build result sorted by abnormal percentage descending
        $result = collect($groups)->map(function ($data) use ($globalAbnormal) {
            $pctAbn = $data['total'] > 0 ? round($data['abnormal'] / $data['total'] * 100, 2) : 0;
            $std = self::VARIANT_STANDARDS[$data['variant']] ?? null;
            $variantCode = $std['code'] ?? $data['variant'];

            return [
                'mesin'            => $data['mesin'],
                'variant'          => $data['variant'],
                'variant_code'     => $variantCode,
                'total'            => $data['total'],
                'abnormal'         => $data['abnormal'],
                'pct_abnormal'     => $pctAbn,
                'kritis'           => $data['kritis'],
                'warning'          => $data['warning'],
                'over'             => $data['over'],
                'total_berat'      => round($data['total_berat'], 2),
                'avg_berat'        => $data['total'] > 0 ? round($data['total_berat'] / $data['total'], 2) : 0,
                'operator_count'   => count($data['operators']),
                'last_activity'    => $data['last_waktu'],
                'kontribusi_abnormal' => $globalAbnormal > 0
                    ? round($data['abnormal'] / $globalAbnormal * 100, 1)
                    : 0,
            ];
        })->sortByDesc('pct_abnormal')->values();

        return [
            'total_mesin'    => $result->count(),
            'total_sampel'   => $globalTotal,
            'total_abnormal' => $globalAbnormal,
            'pct_abnormal'   => $globalTotal > 0 ? round($globalAbnormal / $globalTotal * 100, 2) : 0,
            'counts'         => $globalCounts,
            'data'           => $result,
        ];
    }
}

