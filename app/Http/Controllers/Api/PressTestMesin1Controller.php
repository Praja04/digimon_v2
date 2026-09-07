<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PressTestMesin1;
use Illuminate\Http\Request;

class PressTestMesin1Controller extends Controller
{
    public function index()
    {
        try {
            $pressTests = PressTestMesin1::orderBy('created_at', 'desc')->first();

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $pressTests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAll(Request $request)
    {
        try {
            $query = PressTestMesin1::query();

            if ($request->filled('tanggal')) {
                $tanggal = $request->tanggal;
                $query->where(function ($q) use ($tanggal) {
                    $q->whereDate('created_at', $tanggal)
                        ->orWhere('date', $tanggal);
                });
            }

            if ($request->filled('variant') || $request->filled('varian')) {
                $variant = $request->input('variant') ?? $request->input('varian');
                $query->where(function ($q) use ($variant) {
                    $q->where('variant', $variant)
                        ->orWhere('varian', $variant);
                });
            }

            if ($request->filled('status') || $request->filled('statusSensor')) {
                $status = $request->input('status') ?? $request->input('statusSensor');
                $query->where(function ($q) use ($status) {
                    $q->where('status', $status)
                        ->orWhere('status_sensor', $status);
                });
            }

            if ($request->filled('shift')) {
                $shift = $request->shift;

                $query->where(function ($q) use ($shift, $request) {
                    $q->where('shift', $shift);

                    if ($shift == '1') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('created_at', '>=', '06:00:00')
                                ->whereTime('created_at', '<', '14:00:00');
                        });
                    } elseif ($shift == '2') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('created_at', '>=', '14:00:00')
                                ->whereTime('created_at', '<', '22:00:00');
                        });
                    } elseif ($shift == '3') {
                        if ($request->filled('tanggal')) {
                            $tanggal = $request->tanggal;
                            $tanggalBesok = date('Y-m-d', strtotime($tanggal . ' +1 day'));

                            $q->orWhere(function ($sub) use ($tanggal, $tanggalBesok) {
                                $sub->where(function ($s) use ($tanggal) {
                                    $s->whereDate('created_at', $tanggal)
                                        ->whereTime('created_at', '>=', '22:00:00');
                                })->orWhere(function ($s) use ($tanggalBesok) {
                                    $s->whereDate('created_at', $tanggalBesok)
                                        ->whereTime('created_at', '<', '06:00:00');
                                });
                            });
                        } else {
                            $q->orWhere(function ($sub) {
                                $sub->whereTime('created_at', '>=', '22:00:00')
                                    ->orWhereTime('created_at', '<', '06:00:00');
                            });
                        }
                    }
                });
            }

            $limit = $request->get('limit', 25);

            $data = $query
                ->orderBy('id', 'desc')
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => 'nullable|string',
                'time' => 'nullable|string',
                'grup' => 'nullable|string|max:50',
                'shift' => 'nullable|string|max:50',
                'mesin' => 'nullable|string|max:50',
                'press' => 'nullable|string|max:50',
                'varian' => 'nullable|string|max:255',
                'variant' => 'nullable|string|max:255',
                'sample' => 'nullable|string|max:100',
                'jarak' => 'required',
                'batas' => 'nullable',
                'statusSensor' => 'nullable|string|max:100',
                'status_sensor' => 'nullable|string|max:100',
                'status' => 'nullable|string|max:100',
                'verifManual' => 'nullable|string|max:100',
                'verif_manual' => 'nullable|string|max:100',
                'jenisBocor' => 'nullable|string|max:255',
                'jenis_bocor' => 'nullable|string|max:255',
            ]);

            $varianVal = $request->input('varian') ?? $request->input('variant');
            if (empty($varianVal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => ['varian' => ['The varian/variant field is required.']],
                ], 422);
            }

            $statusSensorVal = $request->input('statusSensor') ?? $request->input('status_sensor') ?? $request->input('status');
            $verifManualVal  = $request->input('verifManual') ?? $request->input('verif_manual');
            $jenisBocorVal   = $request->input('jenisBocor') ?? $request->input('jenis_bocor');

            $data = [
                'date' => $request->input('date', date('Y-m-d')),
                'time' => $request->input('time', date('H:i')),
                'grup' => $request->input('grup'),
                'shift' => $request->input('shift'),
                'mesin' => $request->input('mesin'),
                'press' => $request->input('press'),
                'varian' => $varianVal,
                'variant' => $varianVal,
                'sample' => $request->input('sample'),
                'jarak' => $request->input('jarak'),
                'batas' => $request->input('batas'),
                'status_sensor' => $statusSensorVal,
                'status' => $request->input('status') ?? $statusSensorVal,
                'verif_manual' => $verifManualVal,
                'jenis_bocor' => $jenisBocorVal,
            ];

            $pressTest = PressTestMesin1::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully.',
                'data' => $pressTest,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
