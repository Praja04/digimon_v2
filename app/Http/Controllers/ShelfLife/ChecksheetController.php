<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use App\Models\ShelfLifeSamplingDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ChecksheetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('get_kelompok_tanggal')) {
                $kelompokTanggal = ShelfLifeSamplingDetail::select('kelompok_tanggal')
                    ->distinct()
                    ->whereNotNull('kelompok_tanggal')
                    ->where('kelompok_sample', $request->kelompok_sample)
                    ->orderBy('kelompok_tanggal', 'asc')
                    ->pluck('kelompok_tanggal');

                return response()->json($kelompokTanggal);
            }

            if (empty($request->kelompok_sample) || empty($request->kelompok_tanggal)) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->make(true);
            }

            $query = ShelfLifeSamplingDetail::query()
                ->where('kelompok_sample', $request->kelompok_sample)
                ->where('kelompok_tanggal', $request->kelompok_tanggal)
                ->orderBy('bulan_ke', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $checked = $data->is_checked ? 'checked' : '';
                    return '
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input checksheet-checkbox" 
                               type="checkbox" 
                               data-id="' . $data->id . '" 
                               ' . $checked . '
                               style="cursor: pointer; width: 20px; height: 20px;">
                    </div>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('app.shelf_life.checksheet.index');
    }

    public function updateStatus(Request $request)
    {
        try {
            $data = ShelfLifeSamplingDetail::find($request->id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $data->is_checked = $request->is_checked;
            $data->save();

            $message = $request->is_checked
                ? 'Data berhasil di-check'
                : 'Check berhasil dibatalkan';

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
