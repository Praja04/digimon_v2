<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimbanganRetailMesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            'mesin' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'waktu' => 'required|date',
            'status' => 'required|string',
            'berat' => 'required|numeric',
            'unit' => 'required|string|max:50'
        ], [], [
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
}
