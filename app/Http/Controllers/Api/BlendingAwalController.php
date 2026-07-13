<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlendingAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BlendingAwalController extends Controller
{
    public function show($id)
    {
        try {
            $blendingAwal = BlendingAwal::with([
                'productionBatch:id,po_number,variant',
                'color:id,name,code',
                'user:id,name,email'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $blendingAwal
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
        BlendingAwal::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_range' => $request->batch_range,
            'nomor_blending' => $request->nomor_blending,
            'volume' => $request->volume,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Blending Awal berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $blendingAwal = BlendingAwal::updateOrCreate(
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

            // Jika ada additional_batches, simpan ke blending_awal_relations
            if (!empty($request->additional_batches) && is_array($request->additional_batches)) {
                DB::table('blending_awal_relations')
                    ->where('blending_awal_id', $blendingAwal->id)
                    ->delete();

                // Insert relasi baru dengan ID yang sama dari Produksi
                foreach ($request->additional_batches as $additionalBatch) {
                    DB::table('blending_awal_relations')->insert([
                        'id' => $additionalBatch['id'],
                        'blending_awal_id' => $additionalBatch['blending_awal_id'],
                        'batch' => $additionalBatch['batch'],
                        'borrower_production_batch_id' => $additionalBatch['borrower_production_batch_id'] ?? null,
                        'production_batch_id' => $additionalBatch['production_batch_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Blending Awal revisi berhasil disimpan.',
                'data' => $blendingAwal,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateBatchRange(Request $request): JsonResponse
    {
        $blendingAwal = BlendingAwal::find($request->id);

        if (!$blendingAwal) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Data belum digenerate di QC, dilewati.',
            ], Response::HTTP_OK);
        }

        $blendingAwal->update([
            'batch_range' => $request->batch_range,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Batch range berhasil diupdate di QC.',
        ], Response::HTTP_OK);
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $blendingAwal = BlendingAwal::find($id);
            if ($blendingAwal) {
                DB::table('blending_awal_relations')->where('blending_awal_id', $id)->delete();
                $blendingAwal->delete();
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Blending Awal berhasil dihapus di QC.',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data di QC: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
