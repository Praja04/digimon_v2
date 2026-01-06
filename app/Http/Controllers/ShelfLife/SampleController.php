<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelfLife\SampleDetailStoreRequest;
use App\Http\Requests\ShelfLife\SampleStoreRequest;
use App\Models\MonitoringOnGoingKimia;
use App\Models\ShelfLifeSamples;
use App\Models\ShelfLifeSamplingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ShelfLifeSamples::with(['productionBatch', 'shelfLifeSamplingDetails'])
                ->orderBy('created_at', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $shelfLifeSamples = $query->get();

            return DataTables::of($shelfLifeSamples)
                ->addIndexColumn()
                ->editColumn('tanggal_produksi', function ($data) {
                    if (!$data->productionBatch->date) {
                        return '-';
                    }
                    return \Carbon\Carbon::parse($data->productionBatch->date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('progress', function ($data) {
                    $maxBulan = $data->shelfLifeSamplingDetails->max('bulan_ke') ?? 0;
                    $totalDetails = $data->shelfLifeSamplingDetails->count();

                    $targetBulan = 16;

                    if ($targetBulan == 0 && $maxBulan == 0) {
                        $targetBulan = 1;
                    }

                    $progressPercentage = $maxBulan > 0 ? min(($maxBulan / $targetBulan) * 100, 100) : 0;

                    $badgeColor = 'secondary';
                    $statusText = 'Belum Dimulai';

                    if ($maxBulan > 0) {
                        if ($maxBulan >= $targetBulan) {
                            $badgeColor = 'success';
                            $statusText = 'Selesai';
                        } else {
                            $badgeColor = 'warning';
                            $statusText = 'Berjalan';
                        }
                    }

                    return '
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 20px; min-width: 100px;">
                                <div class="progress-bar bg-' . $badgeColor . '" role="progressbar" 
                                     style="width: ' . $progressPercentage . '%;" 
                                     aria-valuenow="' . $progressPercentage . '" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    ' . round($progressPercentage) . '%
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <span class="badge bg-' . $badgeColor . '">' . $statusText . '</span>
                            Bulan ke-' . $maxBulan . ' dari ' . $targetBulan . ' 
                            <span class="text-muted">(' . $totalDetails . ' detail)</span>
                        </small>
                    </div>
                ';
                })
                ->addColumn('detail', function ($data) {
                    return '
                    <a href="' . route("shelf-life.sample.show", $data->id) . '" class="btn btn-sm btn-info" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i>
                    </a>
                ';
                })
                ->editColumn('nomor_po', function ($data) {
                    return $data->productionBatch->po_number ?? '-';
                })
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
                ->rawColumns(['detail', 'action', 'progress'])
                ->make(true);
        }
        return view('app.shelf_life.sample.index');
    }

    public function edit($id)
    {
        try {
            $data = ShelfLifeSamples::with('productionBatch')->find($id);

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

    public function getPoByDateAndStorage(Request $request)
    {
        $tanggal_produksi = $request->input('tanggal_produksi');
        $storage = $request->input('storage');

        if (!$tanggal_produksi || !$storage) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tanggal Produksi dan Storage harus diisi.',
                'data' => []
            ]);
        }

        $data_release = MonitoringOnGoingKimia::query()
            ->where('storage', $storage)
            ->whereIn('disposition', ['Release', 'Release Bersyarat'])
            ->whereHas('productionBatch', function ($query) use ($tanggal_produksi) {
                $query->whereDate('date', $tanggal_produksi);
            })
            ->join('production_batches', 'monitoring_on_going_kimia.production_batch_id', '=', 'production_batches.id')
            ->select('production_batches.po_number', 'production_batches.id')
            ->distinct()
            ->get();

        $po_data = $data_release->unique('po_number')->map(function ($item) {
            return [
                'id' => $item->id,
                'po_number' => $item->po_number
            ];
        })->values();

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
    }

    public function store(SampleStoreRequest $request)
    {
        try {
            $data = ShelfLifeSamples::updateOrCreate(
                ['id' => $request->id],
                [
                    'production_batch_id' => $request->nomor_po,
                    'storage' => $request->storage,
                ]
            );

            $message = $data->wasRecentlyCreated
                ? 'Data berhasil disimpan'
                : 'Data berhasil diperbarui';

            return response()->json([
                'message' => $message,
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $data = ShelfLifeSamples::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $data->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $data = ShelfLifeSamples::with('productionBatch')->find($id);

        if (!$data) {
            abort(404, 'Data tidak ditemukan');
        }

        $kecapCode = $data->productionBatch->variant ?? null;

        $response = Http::timeout(10)->get(env('PRODUCTION_URL') . 'api/varian/kecap/by-code', [
            'code' => $kecapCode
        ]);

        if ($response->successful()) {
            $kecap = $response->json();
        } else {
            $kecap = [
                'status' => 'error',
                'message' => 'Gagal mengambil data varian kecap',
                'data' => []
            ];
        }

        $shelfLifeSamplingDetails = ShelfLifeSamplingDetail::where('shelf_life_sample_id', $data->id)->orderBy('bulan_ke', 'asc')->get();

        return view('app.shelf_life.sample.show', compact('data', 'kecap', 'shelfLifeSamplingDetails'));
    }

    public function storeSamplingDetail(SampleDetailStoreRequest $request)
    {
        try {
            $data = ShelfLifeSamplingDetail::updateOrCreate(
                ['id' => $request->id],
                [
                    'shelf_life_sample_id' => $request->shelf_life_sample_id,
                    'variant_fg' => $request->variant_fg,
                    'kelompok_sample' => $request->kelompok_sample,
                    'kelompok_tanggal' => $request->kelompok_tanggal,
                    'koding' => $request->koding,
                    'jam_koding' => $request->jam_koding,
                    'bulan_ke' => $request->bulan_ke,
                    'ruang_sl' => $request->ruang_sl,
                    'bin_location' => $request->bin_location,
                ]
            );

            $message = $data->wasRecentlyCreated
                ? 'Data berhasil disimpan'
                : 'Data berhasil diperbarui';

            return response()->json([
                'message' => $message,
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editSamplingDetail($id)
    {
        try {
            $data = ShelfLifeSamplingDetail::with('shelfLifeSample.productionBatch')->find($id);

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

    public function destroySamplingDetail($id)
    {
        try {
            $data = ShelfLifeSamplingDetail::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $data->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
