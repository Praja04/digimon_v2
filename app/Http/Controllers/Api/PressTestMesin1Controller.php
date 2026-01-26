<?php

namespace App\Http\Controllers\Api;

use App\Events\PressTestMesin1Created;
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
                $query->whereDate('created_at', $request->tanggal);
            }

            if ($request->filled('variant')) {
                $query->where('variant', $request->variant);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('shift')) {
                $shift = $request->shift;

                if ($shift == '1') {
                    $query->whereTime('created_at', '>=', '06:00:00')
                        ->whereTime('created_at', '<', '14:00:00');
                } elseif ($shift == '2') {
                    $query->whereTime('created_at', '>=', '14:00:00')
                        ->whereTime('created_at', '<', '22:00:00');
                } elseif ($shift == '3') {
                    if ($request->filled('tanggal')) {
                        $tanggal = $request->tanggal;
                        $tanggalBesok = date('Y-m-d', strtotime($tanggal . ' +1 day'));

                        $query->where(function ($q) use ($tanggal, $tanggalBesok) {
                            $q->where(function ($sub) use ($tanggal) {
                                $sub->whereDate('created_at', $tanggal)
                                    ->whereTime('created_at', '>=', '22:00:00');
                            })
                                ->orWhere(function ($sub) use ($tanggalBesok) {
                                    $sub->whereDate('created_at', $tanggalBesok)
                                        ->whereTime('created_at', '<', '06:00:00');
                                });
                        });
                    } else {
                        $query->where(function ($q) {
                            $q->whereTime('created_at', '>=', '22:00:00')
                                ->orWhereTime('created_at', '<', '06:00:00');
                        });
                    }
                }
            }

            $limit = $request->get('limit', 25);

            $data = $query
                ->orderBy('created_at', 'desc')
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
            $validated = $request->validate([
                'variant' => 'required|string|max:255',
                'jarak' => 'required',
                'batas' => 'nullable',
                'status' => 'nullable|string|max:20',
            ]);

            $pressTest = PressTestMesin1::create($validated);

            return response()->json([
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
