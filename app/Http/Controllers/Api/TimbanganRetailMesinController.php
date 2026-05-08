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
        $variants = TimbanganRetailMesin::select('variant')
        ->distinct()
            ->whereNotNull('variant')
            ->orderBy('variant')
            ->pluck('variant');

        $mesins = TimbanganRetailMesin::select('mesin')
        ->distinct()
            ->whereNotNull('mesin')
            ->orderBy('mesin')
            ->pluck('mesin');

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
}
