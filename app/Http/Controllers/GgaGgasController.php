<?php

namespace App\Http\Controllers;

use App\Http\Requests\GgaGgasRequest;
use App\Http\Requests\GgasUpdateRevisiRequest;
use App\Http\Requests\GgaUpdateRevisiRequest;
use App\Http\Requests\ProductionBatchRequest;
use App\Models\GGA;
use App\Models\GGAS;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GgaGgasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('gga')->orderBy('created_at', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $productionBatches = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $productionBatches = $productionBatches->filter(function ($batch) {
                        return $batch->isGGaComplete() && $batch->isGGasComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $productionBatches = $productionBatches->filter(function ($batch) {
                        return !$batch->isGGaComplete() || !$batch->isGGasComplete();
                    });
                }
            }

            $productionBatches = $productionBatches->sortBy(function ($batch) {
                return ($batch->isGGaComplete() && $batch->isGGasComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($productionBatches)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return $data->description ?? '-';
                })
                ->addColumn('status_gga', function ($data) {
                    $isComplete = $data->isGGaComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('status_ggas', function ($data) {
                    $isComplete = $data->isGGasComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('detail', function ($data) {
                    $showUrl = route('gga-ggas.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i>
                    </a>
                ';
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
                ->rawColumns(['status_gga', 'status_ggas', 'detail', 'action'])
                ->make(true);
        }
        return view('app.GgaGgas.index');
    }

    public function edit($id)
    {
        try {
            $data = ProductionBatch::find($id);

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

    public function update(ProductionBatchRequest $request)
    {
        try {
            $data = [
                'po_number' => $request->po_number,
                'variant' => $request->variant,
                'date' => $request->date,
                'batch_range' => $request->batch_range,
                'description' => $request->description,
            ];

            ProductionBatch::updateOrCreate(
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

    public function show($id)
    {
        $productionBatch = ProductionBatch::findOrFail($id);

        $batches = $productionBatch->batch_range_array;

        $validGgaBatches = $productionBatch->gga
            ->whereNotIn('disposition', ['Resampling', 'Reject', 'Repro', 'Adjustment', null])
            ->pluck('batch_number')
            ->map(fn($b) => (int)$b)
            ->toArray();

        return view('app.GgaGgas.show', compact(['productionBatch', 'batches', 'validGgaBatches']));
    }

    public function store(GgaGgasRequest $request)
    {
        try {
            $batchNumber = (int) $request->batch_number;
            $type = strtoupper($request->type);

            $exists = false;

            if ($type === 'GGA') {
                $exists = GGA::where('production_batch_id', $request->production_batch_id)
                    ->where('batch_number', $batchNumber)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Batch $batchNumber sudah diinput untuk GGA."
                    ], 409);
                }

                GGA::create([
                    'production_batch_id' => $request->production_batch_id,
                    'batch_number' => $batchNumber,
                    'dissolver_number' => $request->dissolver_number,
                ]);
            } elseif ($type === 'GGAS') {
                $exists = GGAS::where('production_batch_id', $request->production_batch_id)
                    ->where('batch_number', $batchNumber)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Batch $batchNumber sudah diinput untuk GGAS."
                    ], 409);
                }

                GGAS::create([
                    'production_batch_id' => $request->production_batch_id,
                    'batch_number' => $batchNumber,
                    'dissolver_number' => $request->dissolver_number,
                ]);
            }

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

    public function destroy(Request $request)
    {
        try {
            $data = ProductionBatch::find($request->id);

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
                'message' => 'Error occurred, please try againrred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show_revisi_gga($id)
    {
        try {
            $data = GGA::find($id);

            $lastRevisi = GGA::where('production_batch_id', $data->production_batch_id)
                ->where('batch_number', $data->batch_number)
                ->max('revisi');

            $nextRevisi = is_null($lastRevisi) ? 1 : $lastRevisi + 1;

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'data' => $data,
                'revisi' => $nextRevisi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_revisi_gga(GgaUpdateRevisiRequest $request)
    {
        try {
            $exists = GGA::where('production_batch_id', $request->id_productbatch_gga)
                ->where('batch_number', $request->batch_number_gga)
                ->where('revisi', $request->revisi_gga)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data revisi sudah ada, coba generate ulang.'
                ], 409);
            }

            $gga = GGA::findOrFail($request->id_revisi_gga);

            GGA::create([
                'production_batch_id' => $request->id_productbatch_gga,
                'batch_number' => $request->batch_number_gga,
                'dissolver_number' => $gga->dissolver_number,
                'revisi' => $request->revisi_gga
            ]);

            $gga->not_standard = false;
            $gga->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show_revisi_ggas($id)
    {
        try {
            $data = GGAS::find($id);

            $lastRevisi = GGAS::where('production_batch_id', $data->production_batch_id)
                ->where('batch_number', $data->batch_number)
                ->max('revisi');

            $nextRevisi = is_null($lastRevisi) ? 1 : $lastRevisi + 1;

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'data' => $data,
                'revisi' => $nextRevisi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_revisi_ggas(GgasUpdateRevisiRequest $request)
    {
        try {
            $exists = GGAS::where('production_batch_id', $request->id_productbatch_ggas)
                ->where('batch_number', $request->batch_number_ggas)
                ->where('revisi', $request->revisi_ggas)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data revisi sudah ada, coba generate ulang.'
                ], 409);
            }

            $ggas = GGAS::findOrFail($request->id_revisi_ggas);

            GGAS::create([
                'production_batch_id' => $request->id_productbatch_ggas,
                'batch_number' => $request->batch_number_ggas,
                'dissolver_number' => $ggas->dissolver_number,
                'revisi' => $request->revisi_ggas
            ]);

            $ggas->not_standard = false;
            $ggas->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
