<?php

namespace App\Http\Controllers;

use App\Http\Requests\PressTestDataRequest;
use App\Models\PressTestData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class PressTestDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pressTestData = PressTestData::orderBy('created_at', 'desc')->get();
            return DataTables::of($pressTestData)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
                        <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                           <span class="mdi mdi-pencil"></span> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                            <span class="mdi mdi-trash-can"></span> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('app.press-test-data.index');
    }

    public function store(PressTestDataRequest $request)
    {
        try {
            $currentHour = (int) now()->format('H');
            if ($currentHour >= 6 && $currentHour < 14) {
                $shift = 1;
            } elseif ($currentHour >= 14 && $currentHour < 22) {
                $shift = 2;
            } else {
                $shift = 3;
            }

            $variantName = $request->variant_name ?: $request->variant;
            $variantCode = $request->variant ?: $request->variant_name;

            $okMax = $request->filled('ok_max') ? (float)$request->ok_max : null;
            $bocorMin = $request->filled('bocor_min') ? (float)$request->bocor_min : null;
            $gap = $request->filled('gap') ? (float)$request->gap : (!is_null($bocorMin) && !is_null($okMax) ? round($bocorMin - $okMax, 2) : null);

            $data = [
                'nama_analis_field' => $request->nama_analis_field,
                'shift'             => $shift,
                'variant'           => $variantCode,
                'variant_name'      => $variantName,
                'batas'             => $request->batas,
                'mesin_press_test'  => $request->mesin_press_test,
                'ok_min'            => $request->filled('ok_min') ? (float)$request->ok_min : null,
                'ok_max'            => $okMax,
                'bocor_min'         => $bocorMin,
                'bocor_max'         => $request->filled('bocor_max') ? (float)$request->bocor_max : null,
                'gap'               => $gap,
                'note'              => $request->note,
            ];

            PressTestData::updateOrCreate(
                ['id' => $request->id],
                $data
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $data = PressTestData::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = PressTestData::find($request->id);

            if ($data) {
                $data->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
