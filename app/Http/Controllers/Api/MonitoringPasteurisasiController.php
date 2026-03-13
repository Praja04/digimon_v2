<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringPasteurisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MonitoringPasteurisasiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MonitoringPasteurisasi::with([
                'productionBatch:id,po_number,variant',
                'user:id,name,email'
            ]);

            if ($request->has('production_batch_id')) {
                $query->where('production_batch_id', $request->production_batch_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data'    => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching monitoring pasteurisasi', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $monitoringPasteurisasi = MonitoringPasteurisasi::with([
                'productionBatch:id,po_number,variant',
                'user:id,name,email'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $monitoringPasteurisasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        MonitoringPasteurisasi::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_range' => $request->batch_range,
            'nomor_blending' => $request->nomor_blending,
            'volume' => $request->volume,
            'storage' => $request->storage,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Monitoring Pasteurisasi berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request): JsonResponse
    {
        Log::info('Menerima request revisi monitoring pasteurisasi dari Produksi', [
            'request_data' => $request->all(),
        ]);

        DB::beginTransaction();

        try {
            // Create atau update monitoring_pasteurisasi
            $monitoringPasteurisasi = MonitoringPasteurisasi::updateOrCreate(
                ['id' => $request->id],
                [
                    'production_batch_id' => $request->production_batch_id,
                    'batch_range' => $request->batch_range,
                    'nomor_blending' => $request->nomor_blending,
                    'volume' => $request->volume,
                    'storage' => $request->storage,
                    'revisi' => $request->revisi,
                    'disposition' => null,
                    'disposition_remark' => null,
                    'is_adjustment' => $request->is_adjustment ?? 0,
                    'not_standard' => $request->not_standard ?? 0,
                    'status' => null,
                ]
            );

            // Jika ada additional_batches, simpan ke monitoring_pasteurisasi_relations
            if (!empty($request->additional_batches) && is_array($request->additional_batches)) {
                // Hapus relasi lama jika ada
                DB::table('monitoring_pasteurisasi_relations')
                    ->where('monitoring_pasteurisasi_id', $monitoringPasteurisasi->id)
                    ->delete();

                // Insert relasi baru dengan ID yang sama dari Produksi
                foreach ($request->additional_batches as $additionalBatch) {
                    DB::table('monitoring_pasteurisasi_relations')->insert([
                        'id' => $additionalBatch['id'],
                        'monitoring_pasteurisasi_id' => $additionalBatch['monitoring_pasteurisasi_id'],
                        'batch' => $additionalBatch['batch'],
                        'production_batch_id' => $additionalBatch['production_batch_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            Log::info('Berhasil menyimpan revisi monitoring pasteurisasi dari Produksi', [
                'id' => $monitoringPasteurisasi->id,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Monitoring Pasteurisasi revisi berhasil disimpan.',
                'data' => $monitoringPasteurisasi,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error menyimpan revisi monitoring pasteurisasi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
