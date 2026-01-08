<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use App\Models\MonitoringOnGoingKimia;
use App\Models\ProductionBatch;
use App\Models\ShelfLifeSamplingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResultController extends Controller
{
    public function index()
    {
        return view('app.shelf_life.result.index');
    }

    public function getPoByDateAndStorage(Request $request)
    {
        try {
            $tanggal_produksi = $request->input('tanggal_produksi');
            $storage = $request->input('storage');

            if (!$tanggal_produksi || !$storage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal Produksi dan Storage harus diisi.',
                    'count' => 0,
                    'po_list' => []
                ]);
            }

            $data_release = MonitoringOnGoingKimia::query()
                ->where('storage', $storage)
                ->whereIn('disposition', ['Release', 'Release Bersyarat'])
                ->whereHas('productionBatch', function ($query) use ($tanggal_produksi) {
                    $query->whereDate('date', $tanggal_produksi);
                })
                ->with('productionBatch:id,po_number')
                ->get();

            $po_data = $data_release->map(function ($item) {
                return [
                    'id' => $item->production_batch_id,
                    'po_number' => $item->productionBatch->po_number
                ];
            })->unique('id')->values();

            $count = $po_data->count();

            $response = [
                'status' => 'success',
                'count' => $count,
                'po_list' => $po_data,
                'selected_id' => null
            ];

            if ($count === 1) {
                $response['selected_id'] = $po_data->first()['id'];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'count' => 0,
                'po_list' => []
            ]);
        }
    }

    public function getData(Request $request)
    {
        try {
            $tanggal_produksi = $request->input('tanggal_produksi');
            $storage = $request->input('storage');
            $production_batch_id = $request->input('nomor_po');

            if (!$tanggal_produksi || !$storage || !$production_batch_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Filter tidak lengkap',
                    'data' => []
                ]);
            }

            $data = ShelfLifeSamplingDetail::with([
                'shelfLifeSample.productionBatch',
                'shelfLifeSamplingKimia.color',
                'shelfLifeSamplingMikro'
            ])
                ->whereHas('shelfLifeSample', function ($q) use ($storage, $production_batch_id, $tanggal_produksi) {
                    $q->where('storage', $storage)
                        ->where('production_batch_id', $production_batch_id)
                        ->whereHas('productionBatch', function ($query) use ($tanggal_produksi) {
                            $query->whereDate('date', $tanggal_produksi);
                        });
                })
                ->orderBy('bulan_ke', 'asc')
                ->get();

            $productionBatch = ProductionBatch::find($production_batch_id);

            $response = [
                'status' => 'success',
                'data' => $data,
                'po_number' => $productionBatch ? $productionBatch->po_number : null,
                'count' => $data->count()
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
