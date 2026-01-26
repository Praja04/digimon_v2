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
            if ($request->has('get_tanggal_analisa')) {
                $tanggalAnalisa = ShelfLifeSamplingDetail::select('tanggal_analisa')
                    ->distinct()
                    ->whereNotNull('tanggal_analisa')
                    ->where('kelompok_sample', $request->kelompok_sample)
                    ->orderBy('tanggal_analisa', 'asc')
                    ->pluck('tanggal_analisa');

                return response()->json($tanggalAnalisa);
            }

            if (empty($request->kelompok_sample) || empty($request->tanggal_analisa)) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->make(true);
            }

            $query = ShelfLifeSamplingDetail::with('shelfLifeSample.productionBatch')
                ->where('kelompok_sample', $request->kelompok_sample)
                ->where('tanggal_analisa', $request->tanggal_analisa)
                ->orderBy('bulan_ke', 'asc')
                ->orderBy('variant_fg', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tanggal_produksi_formatted', function ($data) {
                    return \Carbon\Carbon::parse($data->shelfLifeSample->productionBatch->date)->locale('id')->translatedFormat('d F Y');
                })
                ->addColumn('nomor_po', function ($data) {
                    return $data->shelfLifeSample->productionBatch->po_number;
                })  
                ->addColumn('tanggal_analisa_formatted', function ($data) {
                    return \Carbon\Carbon::parse($data->tanggal_analisa)->locale('id')->translatedFormat('d F Y');
                })
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
