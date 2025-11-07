<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringTurunBlending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MonitoringTurunBlendingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        MonitoringTurunBlending::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_range' => $request->batch_range,
            'nomor_blending' => $request->nomor_blending,
            'volume' => $request->volume,
            'storage' => $request->storage,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Monitoring Turun Blending berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request): JsonResponse
    {
        Log::info('Menerima request revisi monitoring turun blending dari Produksi', [
            'request_data' => $request->all(),
        ]);

        DB::beginTransaction();

        try {
            // Create atau update monitoring_turun_blending
            $monitoringTurunBlending = MonitoringTurunBlending::updateOrCreate(
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

            // Jika ada additional_batches, simpan ke monitoring_turun_blending_relations
            if (!empty($request->additional_batches) && is_array($request->additional_batches)) {
                // Hapus relasi lama jika ada
                DB::table('monitoring_turun_blending_relations')
                    ->where('monitoring_turun_blending_id', $monitoringTurunBlending->id)
                    ->delete();

                // Insert relasi baru dengan ID yang sama dari Produksi
                foreach ($request->additional_batches as $additionalBatch) {
                    DB::table('monitoring_turun_blending_relations')->insert([
                        'id' => $additionalBatch['id'],
                        'monitoring_turun_blending_id' => $additionalBatch['monitoring_turun_blending_id'],
                        'batch' => $additionalBatch['batch'],
                        'production_batch_id' => $additionalBatch['production_batch_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            Log::info('Berhasil menyimpan revisi monitoring turun blending dari Produksi', [
                'id' => $monitoringTurunBlending->id,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Monitoring Turun Blending revisi berhasil disimpan.',
                'data' => $monitoringTurunBlending,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error menyimpan revisi monitoring turun blending', [
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
