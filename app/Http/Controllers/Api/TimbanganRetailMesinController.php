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
    public function getDashboardData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'shift' => 'nullable|in:1,2,3',
            'status' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggal = $request->filled('tanggal') ? $request->tanggal : today()->format('Y-m-d');
        $shift = $request->shift;
        $status = $request->status;

        $query = TimbanganRetailMesin::whereDate('waktu', $tanggal);

        // Filter berdasarkan shift
        if ($request->filled('shift')) {
            if ($shift == '1') {
                $query->whereTime('waktu', '>=', '06:00:00')
                    ->whereTime('waktu', '<', '14:00:00');
            } elseif ($shift == '2') {
                $query->whereTime('waktu', '>=', '14:00:00')
                    ->whereTime('waktu', '<', '22:00:00');
            } elseif ($shift == '3') {
                $tanggalBesok = date('Y-m-d', strtotime($tanggal . ' +1 day'));

                $query->where(function ($q) use ($tanggal, $tanggalBesok) {
                    $q->where(function ($sub) use ($tanggal) {
                        $sub->whereDate('waktu', $tanggal)
                            ->whereTime('waktu', '>=', '22:00:00');
                    })
                        ->orWhere(function ($sub) use ($tanggalBesok) {
                            $sub->whereDate('waktu', $tanggalBesok)
                                ->whereTime('waktu', '<', '06:00:00');
                        });
                });
            }
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        // Statistik
        $beratHariIni = (clone $query)->sum('berat');
        $transaksiHariIni = (clone $query)->count();
        $rataRataBerat = (clone $query)->avg('berat');

        // Mesin paling aktif
        $mesinAktif = (clone $query)
            ->select('mesin', DB::raw('count(*) as total'))
            ->groupBy('mesin')
            ->orderBy('total', 'desc')
            ->first();

        // Data per mesin
        $dataMesin = (clone $query)
            ->select('mesin', DB::raw('count(*) as total_transaksi'), DB::raw('sum(berat) as total_berat'))
            ->groupBy('mesin')
            ->orderBy('total_transaksi', 'desc')
            ->limit(10)
            ->get();

        // Transaksi terbaru
        $transaksiTerbaru = (clone $query)
            ->orderBy('waktu', 'desc')
            ->limit(10)
            ->get();

        // Transaksi per jam
        $transaksiPerJam = (clone $query)
            ->select(DB::raw('HOUR(waktu) as jam'), DB::raw('count(*) as total'))
            ->groupBy('jam')
            ->orderBy('jam')
            ->get();

        // Berat per variant (Top 5)
        $beratPerVariant = (clone $query)
            ->select('variant', DB::raw('sum(berat) as total_berat'))
            ->groupBy('variant')
            ->orderBy('total_berat', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'filters' => [
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'status' => $status
                ],
                'statistics' => [
                    'berat_hari_ini' => (float) $beratHariIni,
                    'transaksi_hari_ini' => $transaksiHariIni,
                    'rata_rata_berat' => (float) ($rataRataBerat ?? 0),
                    'mesin_aktif' => $mesinAktif ? [
                        'mesin' => $mesinAktif->mesin,
                        'total' => $mesinAktif->total
                    ] : null
                ],
                'data_mesin' => $dataMesin,
                'transaksi_terbaru' => $transaksiTerbaru,
                'transaksi_per_jam' => $transaksiPerJam,
                'berat_per_variant' => $beratPerVariant
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik'     => 'required|string|max:50',
            'mesin' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'waktu' => 'required|date',
            'status' => 'required|string',
            'berat' => 'required|numeric',
            'unit' => 'required|string|max:50'
        ], [], [
            'nik'     => 'NIK', 
            'mesin' => 'Mesin',
            'variant' => 'Variant',
            'waktu' => 'Waktu',
            'status' => 'Status',
            'berat' => 'Berat',
            'unit' => 'Unit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mesin = TimbanganRetailMesin::create([
            'nik'     => $request->nik,
            'mesin' => $request->mesin,
            'variant' => $request->variant,
            'waktu' => $request->waktu,
            'status' => $request->status,
            'berat' => $request->berat,
            'unit' => $request->unit
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data mesin berhasil ditambahkan',
            'data' => $mesin
        ], 201);
    }
    public function store2(Request $request)
    {
        // ✅ 14 VARIANT RESMI dari Python
        $validVariants = [
            'Sachet YB 12,5gr PCS',
            'Sachet YB 12,5gr RENCENG',
            'Sachet YB 20gr PCS',
            'Sachet YB 20gr RENCENG',
            'Sachet BB 40gr PCS',
            'Sachet BB 40gr RENCENG',
            'Pouch YB 77gr',
            'Pouch BB 77gr',
            'Pouch YB 250gr',
            'Pouch BB 270gr',
            'Pouch YB 550gr',
            'Pouch YB 700gr',
            'Pouch BB 725gr',
            'Pouch YB 1000gr'
        ];

        $validator = Validator::make($request->all(), [
            'nik'     => 'required|string|max:50',
            'mesin'   => 'required|string|max:255',
            'variant' => [
                'required',
                'string',
                'max:255',
                "in:" . implode(',', $validVariants)  // ✅ VALIDASI PYTHON
            ],
            'waktu'   => 'required|date',
            'status'  => 'required|in:OK,NOT OK',  // ✅ Hanya OK/NOT OK
            'filler'  => 'nullable|in:1,2,3,4,5,6,7,8',  // ✅ 1-8
            'berat'   => 'required|numeric|between:0,999999',
            'unit'    => 'required|in:g,kg'  // ✅ Unit dari timbangan
        ], [], [
            'nik'     => 'NIK Operator',
            'mesin'   => 'Mesin',
            'variant' => 'Variant Produk',
            'waktu'   => 'Waktu',
            'status'  => 'Status',
            'filler'  => 'Filler',
            'berat'   => 'Berat',
            'unit'    => 'Unit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mesin = TimbanganRetailMesin::create([
            'nik'     => $request->nik,
            'mesin'   => $request->mesin,
            'variant' => $request->variant,
            'waktu'   => $request->waktu,
            'status'  => $request->status,
            'berat'   => $request->berat,
            'filler'  => $request->filler,
            'unit'    => $request->unit
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data timbangan berhasil disimpan',
            'data' => $mesin
        ], 201);
    }

    /**
     * Hitung rentang shift berdasarkan tanggal.
     * Shift 1: 06:00:00 - 13:59:59 (hari yang sama)
     * Shift 2: 14:00:00 - 21:59:59 (hari yang sama)
     * Shift 3: 22:00:00 - 05:59:59 (hari berikutnya)
     *
     * Jika tanggal = "5", maka tampilkan:
     *   - Shift 1 tgl 5: 05-06:00 s/d 05-13:59:59
     *   - Shift 2 tgl 5: 05-14:00 s/d 05-21:59:59
     *   - Shift 3 tgl 5: 05-22:00 s/d 06-05:59:59
     */
    private function getShiftRange(string $date): array
    {
        $day = Carbon::parse($date);
        $nextDay = $day->copy()->addDay();

        return [
            'start' => $day->copy()->setTime(6, 0, 0),
            'end'   => $nextDay->copy()->setTime(5, 59, 59),
        ];
    }

    /**
     * Tentukan label shift dari waktu.
     */
    private function getShiftLabel(Carbon $time): string
    {
        $hour = (int) $time->format('H');
        if ($hour >= 6 && $hour < 14) return 'Shift 1';
        if ($hour >= 14 && $hour < 22) return 'Shift 2';
        return 'Shift 3';
    }

    /**
     * GET /api/timbangan-retail/data
     * Query params:
     *   - date       (required) format: Y-m-d
     *   - variant    (optional)
     *   - mesin      (optional)
     */
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

        if ($request->filled('variant')) {
            $query->where('variant', $request->variant);
        }

        if ($request->filled('mesin')) {
            $query->where('mesin', $request->mesin);
        }

        $records = $query->orderBy('waktu')->get();

        // Kelompokkan per shift
        $shifts = [
            'Shift 1' => [],
            'Shift 2' => [],
            'Shift 3' => [],
        ];

        foreach ($records as $row) {
            $time = Carbon::parse($row->waktu);
            $label = $this->getShiftLabel($time);
            $shifts[$label][] = $row;
        }

        // Hitung statistik per shift
        $shiftStats = [];
        foreach ($shifts as $shiftName => $rows) {
            if (empty($rows)) {
                $shiftStats[$shiftName] = [
                    'count'   => 0,
                    'total'   => 0,
                    'average' => null,
                    'min'     => null,
                    'max'     => null,
                    'unit'    => null,
                ];
                continue;
            }

            $berats = array_column(
                array_map(fn ($r) => ['berat' => (float) $r->berat], $rows),
                'berat'
            );

            $shiftStats[$shiftName] = [
                'count'   => count($berats),
                'total'   => round(array_sum($berats), 3),
                'average' => round(array_sum($berats) / count($berats), 3),
                'min'     => round(min($berats), 3),
                'max'     => round(max($berats), 3),
                'unit'    => $rows[0]->unit ?? null,
            ];
        }

        // Data untuk line chart (semua record terurut waktu)
        $chartData = $records->map(fn ($r) => [
            'x'     => $r->waktu,
            'y'     => (float) $r->berat,
            'mesin' => $r->mesin,
            'shift' => $this->getShiftLabel(Carbon::parse($r->waktu)),
        ]);

        // Ringkasan keseluruhan
        $allBerats = $records->pluck('berat')->map(fn ($b) => (float) $b);
        $summary = [
            'total_transaksi' => $records->count(),
            'total_berat'     => round($allBerats->sum(), 3),
            'average_berat'   => $records->count() > 0 ? round($allBerats->average(), 3) : null,
            'unit'            => $records->first()?->unit,
        ];

        // Daftar mesin unik dan transaksi terbaru (gabung)
        $perMesin = $records->groupBy('mesin')->map(function ($rows, $mesinName) {
            $berats = $rows->pluck('berat')->map(fn ($b) => (float) $b);
            $latest = $rows->sortByDesc('waktu')->first();
            return [
                'mesin'           => $mesinName,
                'jumlah_transaksi' => $rows->count(),
                'total_berat'     => round($berats->sum(), 3),
                'average_berat'   => round($berats->average(), 3),
                'min_berat'       => round($berats->min(), 3),
                'max_berat'       => round($berats->max(), 3),
                'unit'            => $rows->first()->unit,
                'transaksi_terbaru' => [
                    'waktu'   => $latest?->waktu,
                    'berat'   => $latest?->berat,
                    'status'  => $latest?->status,
                    'variant' => $latest?->variant,
                    'nik'     => $latest?->nik,
                ],
            ];
        })->values();

        return response()->json([
            'success'     => true,
            'date'        => $request->date,
            'filters'     => [
                'variant' => $request->variant,
                'mesin'   => $request->mesin,
            ],
            'summary'     => $summary,
            'shift_stats' => $shiftStats,
            'per_mesin'   => $perMesin,
            'chart_data'  => $chartData,
        ]);
    }

    /**
     * GET /api/timbangan-retail/filter-options
     * Mengembalikan daftar variant dan mesin yang tersedia.
     */
    public function filterOptions()
    {
        // ── DATA VARIAN + STANDAR ───────────────────────────────────────
        $variantStandards = [
            "Sachet YB 12,5gr PCS"    => ["min" =>  12.05, "std" =>  13.05, "max" =>  14.05, "tu1" =>  11.93, "tu2" =>  10.80, "code" => "S12.5G-P"],
            "Sachet YB 12,5gr RENCENG" => ["min" => 154.60, "std" => 156.60, "max" => 168.60, "tu1" => 143.10, "tu2" => 129.60, "code" => "S12.5G-R"],
            "Sachet YB 20gr PCS"      => ["min" =>  19.14, "std" =>  20.64, "max" =>  21.64, "tu1" =>  18.84, "tu2" =>  17.04, "code" => "S20G-P"],
            "Sachet YB 20gr RENCENG"  => ["min" => 244.68, "std" => 247.68, "max" => 259.68, "tu1" => 226.08, "tu2" => 204.48, "code" => "S20G-R"],
            "Sachet BB 40gr PCS"      => ["min" =>  39.10, "std" =>  41.10, "max" =>  42.10, "tu1" =>  37.50, "tu2" =>  33.90, "code" => "S40G-P"],
            "Sachet BB 40gr RENCENG"  => ["min" => 489.20, "std" => 493.20, "max" => 505.20, "tu1" => 450.00, "tu2" => 406.80, "code" => "S40G-R"],
            "Pouch YB 77gr"           => ["min" =>  78.70, "std" =>  79.20, "max" =>  82.70, "tu1" =>  74.70, "tu2" =>  70.20, "code" => "P77G-YB"],
            "Pouch BB 77gr"           => ["min" =>  78.70, "std" =>  79.20, "max" =>  82.70, "tu1" =>  74.70, "tu2" =>  70.20, "code" => "P77G-BB"],
            "Pouch YB 250gr"          => ["min" => 253.00, "std" => 255.00, "max" => 257.00, "tu1" => 246.00, "tu2" => 237.00, "code" => "P250G"],
            "Pouch BB 270gr"          => ["min" => 273.00, "std" => 275.00, "max" => 277.00, "tu1" => 266.00, "tu2" => 257.00, "code" => "P270G"],
            "Pouch YB 550gr"          => ["min" => 556.00, "std" => 561.00, "max" => 566.00, "tu1" => 545.80, "tu2" => 530.80, "code" => "P550G"],
            "Pouch YB 700gr"          => ["min" => 706.00, "std" => 711.00, "max" => 716.00, "tu1" => 696.00, "tu2" => 681.00, "code" => "P700G"],
            "Pouch BB 725gr"          => ["min" => 730.00, "std" => 735.00, "max" => 740.00, "tu1" => 720.00, "tu2" => 705.00, "code" => "P725G"],
            "Pouch YB 1000gr"         => ["min" => 1007.50, "std" => 1012.50, "max" => 1017.50, "tu1" => 997.50, "tu2" => 982.50, "code" => "P1000G"],
        ];

        // ── RELASI VARIAN → MESIN ────────────────────────────────────────
        $variantMesin = [
            "Sachet YB 12,5gr PCS"    => ["Y", "Z"],
            "Sachet YB 12,5gr RENCENG" => ["Y", "Z"],
            "Sachet YB 20gr PCS"      => ["O", "P", "W", "X"],
            "Sachet YB 20gr RENCENG"  => ["O", "P", "W", "X"],
            "Sachet BB 40gr PCS"      => ["Q", "R"],
            "Sachet BB 40gr RENCENG"  => ["Q", "R"],
            "Pouch YB 77gr"           => ["F", "G", "H", "I", "D", "E", "J", "K", "C", "L", "AE", "AG"],
            "Pouch BB 77gr"           => ["C", "L", "AE", "AG", "B", "AF", "AI", "AJ"],
            "Pouch YB 250gr"          => ["AH"],
            "Pouch BB 270gr"          => ["AH"],
            "Pouch YB 550gr"          => ["A", "U", "V"],
            "Pouch YB 700gr"          => ["A", "U", "V"],
            "Pouch BB 725gr"          => ["A", "U", "V"],
            "Pouch YB 1000gr"         => ["A", "U", "V"],
        ];

        $variants = array_keys($variantStandards);
        $mesins = array_unique(array_merge(...array_values($variantMesin)));

        // Sort alphabetically
        sort($variants);
        sort($mesins);

        return response()->json([
            'success'  => true,
            'variants' => $variants,
            'mesins'   => $mesins,
        ]);
    }

    /**
     * GET /api/timbangan-retail/export
     * Query params:
     *   - date       (required) format: Y-m-d
     *   - variant    (optional)
     *   - mesin      (optional)
     *
     * Export Excel sesuai rentang shift dari tanggal yang dipilih.
     */
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

        if ($request->filled('variant')) {
            $query->where('variant', $request->variant);
        }

        if ($request->filled('mesin')) {
            $query->where('mesin', $request->mesin);
        }

        $records = $query->get()->map(function ($row) {
            $time  = Carbon::parse($row->waktu);
            $shift = $this->getShiftLabel($time);
            return [
                'Mesin'    => $row->mesin,
                'Variant'  => $row->variant,
                'Waktu'    => $row->waktu,
                'Shift'    => $shift,
                'Status'   => $row->status,
                'Berat'    => $row->berat,
                'Unit'     => $row->unit,
                'NIK'      => $row->nik,
            ];
        });

        $filename = 'timbangan-retail-' . $request->date;
        if ($request->filled('variant')) $filename .= '-' . $request->variant;
        if ($request->filled('mesin'))   $filename .= '-' . $request->mesin;
        $filename .= '.xlsx';

        return Excel::download(
            new TimbanganRetailExport($records),
            $filename
        );
    }

    public function getAverageMinMax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'varian'     => 'nullable|string',
            'mesin'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $variantStandards = [
            'Sachet YB 12,5gr PCS'     => ['min' =>  12.05, 'std' =>  13.05, 'max' =>  14.05, 'tu1' =>  11.93, 'tu2' =>  10.80],
            'Sachet YB 12,5gr RENCENG' => ['min' => 154.60, 'std' => 156.60, 'max' => 168.60, 'tu1' => 143.10, 'tu2' => 129.60],
            'Sachet YB 20gr PCS'       => ['min' =>  19.14, 'std' =>  20.64, 'max' =>  21.64, 'tu1' =>  18.84, 'tu2' =>  17.04],
            'Sachet YB 20gr RENCENG'   => ['min' => 244.68, 'std' => 247.68, 'max' => 259.68, 'tu1' => 226.08, 'tu2' => 204.48],
            'Sachet BB 40gr PCS'       => ['min' =>  39.10, 'std' =>  41.10, 'max' =>  42.10, 'tu1' =>  37.50, 'tu2' =>  33.90],
            'Sachet BB 40gr RENCENG'   => ['min' => 489.20, 'std' => 493.20, 'max' => 505.20, 'tu1' => 450.00, 'tu2' => 406.80],
            'Pouch YB 77gr'            => ['min' =>  78.70, 'std' =>  79.20, 'max' =>  82.70, 'tu1' =>  74.70, 'tu2' =>  70.20],
            'Pouch BB 77gr'            => ['min' =>  78.70, 'std' =>  79.20, 'max' =>  82.70, 'tu1' =>  74.70, 'tu2' =>  70.20],
            'Pouch YB 250gr'           => ['min' => 253.00, 'std' => 255.00, 'max' => 257.00, 'tu1' => 246.00, 'tu2' => 237.00],
            'Pouch BB 270gr'           => ['min' => 273.00, 'std' => 275.00, 'max' => 277.00, 'tu1' => 266.00, 'tu2' => 257.00],
            'Pouch YB 550gr'           => ['min' => 556.00, 'std' => 561.00, 'max' => 566.00, 'tu1' => 545.80, 'tu2' => 530.80],
            'Pouch YB 700gr'           => ['min' => 706.00, 'std' => 711.00, 'max' => 716.00, 'tu1' => 696.00, 'tu2' => 681.00],
            'Pouch BB 725gr'           => ['min' => 730.00, 'std' => 735.00, 'max' => 740.00, 'tu1' => 720.00, 'tu2' => 705.00],
            'Pouch YB 1000gr'          => ['min' => 1007.50, 'std' => 1012.50, 'max' => 1017.50, 'tu1' => 997.50, 'tu2' => 982.50],
        ];

        $start = Carbon::parse($request->start_date)->setTime(6, 0, 0)->toDateTimeString();
        $end   = Carbon::parse($request->end_date)->addDay()->setTime(5, 59, 59)->toDateTimeString();

        $query = DB::table('timbangan_retail_mesin')
        ->select('waktu', 'berat', 'variant', 'mesin');

        if ($request->filled('varian')) {
            $query->where('variant', trim($request->varian));
        }
        if ($request->filled('mesin')) {
            $query->where('mesin', trim($request->mesin));
        }

        $rows = $query->whereBetween('waktu', [$start, $end])->get();

        if ($rows->isEmpty()) {
            return response()->json(['success' => true, 'data' => null], 200);
        }

        // ── CLASSIFY HELPER ───────────────────────────────────────────
        $classify = function (float $berat, string $variant) use ($variantStandards): string {
            if (!isset($variantStandards[$variant])) return 'tu1ToStd';
            $s = $variantStandards[$variant];
            if ($berat > $s['max'])  return 'overMax';
            if ($berat >= $s['std']) return 'stdToMax';
            if ($berat >= $s['min']) return 'tu1ToStd';
            if ($berat >= $s['tu1']) return 'tu2ToTu1';
            return 'underTu2';
        };

        $blankCounts = fn () => ['underTu2' => 0, 'tu2ToTu1' => 0, 'tu1ToStd' => 0, 'stdToMax' => 0, 'overMax' => 0];

        // ── CONTAINERS ────────────────────────────────────────────────
        $shifts   = [
            'shift1' => ['weights' => [], 'counts' => $blankCounts()],
            'shift2' => ['weights' => [], 'counts' => $blankCounts()],
            'shift3' => ['weights' => [], 'counts' => $blankCounts()],
        ];
        $variants = []; // [variantName => ['weights'=>[], 'counts'=>[], 'under'=>0, 'over'=>0]]
        $mesins   = []; // [mesinName   => ['weights'=>[], 'counts'=>[]]]

        // ── LOOP ROWS ─────────────────────────────────────────────────
        foreach ($rows as $row) {
            $berat   = (float) $row->berat;
            $variant = $row->variant ?? '';
            $mesin   = $row->mesin   ?? '';
            $hour    = (int) substr($row->waktu, 11, 2);

            if ($hour >= 6 && $hour < 14)      $shiftKey = 'shift1';
            elseif ($hour >= 14 && $hour < 22) $shiftKey = 'shift2';
            else                                $shiftKey = 'shift3';

            $cls = $classify($berat, $variant);

            // Shift
            $shifts[$shiftKey]['weights'][]      = $berat;
            $shifts[$shiftKey]['counts'][$cls]++;

            // Variant
            if (!isset($variants[$variant])) {
                $variants[$variant] = ['weights' => [], 'counts' => $blankCounts()];
            }
            $variants[$variant]['weights'][] = $berat;
            $variants[$variant]['counts'][$cls]++;

            // Mesin
            if (!isset($mesins[$mesin])) {
                $mesins[$mesin] = ['weights' => [], 'counts' => $blankCounts()];
            }
            $mesins[$mesin]['weights'][] = $berat;
            $mesins[$mesin]['counts'][$cls]++;
        }

        // ── BUILD STATS HELPER ────────────────────────────────────────
        $buildStats = function (array $weights, array $counts) {
            if (empty($weights)) {
                return [
                    'total' => 0, 'avg' => null,
                    'min'   => null, 'max' => null,
                    'under' => 0, 'over' => 0,
                    'counts' => $counts,
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
        };

        // ── SHIFT RESULT ──────────────────────────────────────────────
        $shiftResult = [];
        foreach ($shifts as $k => $d) {
            $stats = $buildStats($d['weights'], $d['counts']);
            $shiftResult[$k] = [
                'total_transaksi' => $stats['total'],
                'avg'             => $stats['avg'],
                'min'             => $stats['min'],
                'max'             => $stats['max'],
                'counts'          => $stats['counts'],
            ];
        }

        // ── VARIANT RESULT ────────────────────────────────────────────
        $variantResult = [];
        foreach ($variants as $v => $d) {
            $variantResult[$v] = $buildStats($d['weights'], $d['counts']);
        }

        // ── MESIN RESULT ──────────────────────────────────────────────
        $mesinResult = [];
        foreach ($mesins as $m => $d) {
            $mesinResult[$m] = $buildStats($d['weights'], $d['counts']);
        }

        return response()->json([
            'success'    => true,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'varian'     => $request->varian,
            'mesin'      => $request->mesin,
            // Slide 1 — per shift
            'shift1'     => $shiftResult['shift1'],
            'shift2'     => $shiftResult['shift2'],
            'shift3'     => $shiftResult['shift3'],
            // Slide 2 — per variant & per mesin
            'variants'   => $variantResult,
            'mesins'     => $mesinResult,
        ]);
    }

    public function getChartData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
            'varian'     => 'nullable|string',  // ← sesuai blade
            'mesin'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $start = Carbon::parse($request->start_date)->setTime(6, 0, 0)->toDateTimeString();
        $end   = Carbon::parse($request->end_date)->addDay()->setTime(5, 59, 59)->toDateTimeString();

        $query = DB::table('timbangan_retail_mesin')
        ->select(['mesin', 'variant', 'waktu', 'berat', 'status']);

        if ($request->filled('varian')) {
            $query->where('variant', trim($request->varian));
        }
        if ($request->filled('mesin')) {
            $query->where('mesin', trim($request->mesin));
        }

        $rows = $query->whereBetween('waktu', [$start, $end])->orderBy('waktu')->get();

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

        // Group per shift → samples array untuk line chart blade
        $shiftSamples = ['shift1' => [], 'shift2' => [], 'shift3' => []];

        // Group per mesin → untuk slide2
        $mesinSamples = [];

        $flatData = [];

        foreach ($rows as $row) {
            $hour = (int) substr($row->waktu, 11, 2);
            if ($hour >= 6 && $hour < 14)      $shiftKey = 'shift1';
            elseif ($hour >= 14 && $hour < 22) $shiftKey = 'shift2';
            else                                $shiftKey = 'shift3';

            $item = [
                'mesin'   => $row->mesin,
                'variant' => $row->variant,
                'waktu'   => $row->waktu,
                'berat'   => (float) $row->berat,
                'status'  => $row->status,
                'shift'   => ucfirst(str_replace('shift', 'Shift ', $shiftKey)),
            ];

            // Samples per shift — blade akses: chartData['shift1'].samples
            $shiftSamples[$shiftKey][] = ['berat' => (float) $row->berat, 'waktu' => $row->waktu];

            // Samples per mesin — blade akses: chartData.mesins['A'].samples
            $m = $row->mesin;
            if (!isset($mesinSamples[$m])) $mesinSamples[$m] = ['samples' => []];
            $mesinSamples[$m]['samples'][] = ['berat' => (float) $row->berat, 'waktu' => $row->waktu];

            $flatData[] = $item;
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
            // Grouped per shift — untuk slide1 line chart
            'shift1'     => ['samples' => $shiftSamples['shift1']],
            'shift2'     => ['samples' => $shiftSamples['shift2']],
            'shift3'     => ['samples' => $shiftSamples['shift3']],
            // Grouped per mesin — untuk slide2
            'mesins'     => $mesinSamples,
            // Flat array tetap ada
            'data'       => $flatData,
        ]);
    }
}
