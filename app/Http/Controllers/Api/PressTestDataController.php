<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PressTestData;
use Illuminate\Support\Facades\Validator;

class PressTestDataController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PressTestData::query();

            // Optional Filters
            if ($request->filled('mesin') || $request->filled('mesin_press_test')) {
                $mesin = $request->input('mesin') ?? $request->input('mesin_press_test');
                $query->where('mesin_press_test', $mesin);
            }

            if ($request->filled('variant_name')) {
                $query->where('variant_name', 'like', '%' . $request->variant_name . '%');
            } elseif ($request->filled('variant') || $request->filled('varian')) {
                $variant = $request->input('variant') ?? $request->input('varian');
                $query->where(function ($q) use ($variant) {
                    $q->where('variant', $variant)
                      ->orWhere('variant_name', 'like', '%' . $variant . '%');
                });
            }

            $records = $query->orderBy('id', 'asc')->get();

            $data = $records->map(function ($item) {
                $gapVal = $item->gap;
                if (is_null($gapVal) && !is_null($item->bocor_min) && !is_null($item->ok_max)) {
                    $gapVal = round((float) $item->bocor_min - (float) $item->ok_max, 2);
                }

                return [
                    'id'                => $item->id,
                    'variant_name'      => $item->variant_name ?? $item->variant ?? '',
                    'ok_min'            => !is_null($item->ok_min) ? (float) $item->ok_min : null,
                    'ok_max'            => !is_null($item->ok_max) ? (float) $item->ok_max : null,
                    'bocor_min'         => !is_null($item->bocor_min) ? (float) $item->bocor_min : null,
                    'bocor_max'         => !is_null($item->bocor_max) ? (float) $item->bocor_max : null,
                    'gap'               => !is_null($gapVal) ? (float) $gapVal : null,
                    'note'              => $item->note ?? '',
                    'nama_analis_field' => $item->nama_analis_field,
                    'shift'             => $item->shift,
                    'mesin_press_test'  => $item->mesin_press_test,
                    'created_at'        => $item->created_at,
                    'updated_at'        => $item->updated_at,
                ];
            });

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'message' => 'Data varian berhasil dimuat',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Gagal memuat data varian',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'variant_name' => 'nullable|string|max:255',
                'variant'      => 'nullable|string|max:255',
                'ok_min'       => 'nullable|numeric',
                'ok_max'       => 'nullable|numeric',
                'bocor_min'    => 'nullable|numeric',
                'bocor_max'    => 'nullable|numeric',
                'gap'          => 'nullable|numeric',
                'note'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $input = $request->all();
            if (empty($input['variant_name']) && !empty($input['variant'])) {
                $input['variant_name'] = $input['variant'];
            }
            if (empty($input['variant']) && !empty($input['variant_name'])) {
                $input['variant'] = $input['variant_name'];
            }

            if (!isset($input['gap']) && isset($input['bocor_min']) && isset($input['ok_max'])) {
                $input['gap'] = round((float) $input['bocor_min'] - (float) $input['ok_max'], 2);
            }

            $pressTestData = PressTestData::create($input);

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'message' => 'Data varian berhasil disimpan',
                'data'    => $pressTestData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Gagal menyimpan data varian',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $item = PressTestData::find($id);

            if (!$item) {
                return response()->json([
                    'status'  => 'error',
                    'success' => false,
                    'message' => 'Data varian tidak ditemukan'
                ], 404);
            }

            $gapVal = $item->gap;
            if (is_null($gapVal) && !is_null($item->bocor_min) && !is_null($item->ok_max)) {
                $gapVal = round((float) $item->bocor_min - (float) $item->ok_max, 2);
            }

            $data = [
                'id'                => $item->id,
                'variant_name'      => $item->variant_name ?? $item->variant ?? '',
                'ok_min'            => !is_null($item->ok_min) ? (float) $item->ok_min : null,
                'ok_max'            => !is_null($item->ok_max) ? (float) $item->ok_max : null,
                'bocor_min'         => !is_null($item->bocor_min) ? (float) $item->bocor_min : null,
                'bocor_max'         => !is_null($item->bocor_max) ? (float) $item->bocor_max : null,
                'gap'               => !is_null($gapVal) ? (float) $gapVal : null,
                'note'              => $item->note ?? '',
                'nama_analis_field' => $item->nama_analis_field,
                'shift'             => $item->shift,
                'mesin_press_test'  => $item->mesin_press_test,
                'created_at'        => $item->created_at,
                'updated_at'        => $item->updated_at,
            ];

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Gagal mengambil data varian',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $pressTestData = PressTestData::find($id);

            if (!$pressTestData) {
                return response()->json([
                    'status'  => 'error',
                    'success' => false,
                    'message' => 'Data varian tidak ditemukan'
                ], 404);
            }

            $input = $request->all();
            if (empty($input['variant_name']) && !empty($input['variant'])) {
                $input['variant_name'] = $input['variant'];
            }
            if (empty($input['variant']) && !empty($input['variant_name'])) {
                $input['variant'] = $input['variant_name'];
            }

            if (!isset($input['gap']) && isset($input['bocor_min']) && isset($input['ok_max'])) {
                $input['gap'] = round((float) $input['bocor_min'] - (float) $input['ok_max'], 2);
            }

            $pressTestData->update($input);

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'message' => 'Data varian berhasil diperbarui',
                'data'    => $pressTestData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Gagal memperbarui data varian',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $pressTestData = PressTestData::find($id);

            if (!$pressTestData) {
                return response()->json([
                    'status'  => 'error',
                    'success' => false,
                    'message' => 'Data varian tidak ditemukan'
                ], 404);
            }

            $pressTestData->delete();

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'message' => 'Data varian berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Gagal menghapus data varian',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
