<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BlendingAwalMikroController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validated = $request->validate([
                'id' => 'required|integer',
                'production_batch_id' => 'required|integer|exists:production_batches,id',
                'batch_range' => 'nullable|string',
                'nomor_blending' => 'nullable|integer',
                'volume' => 'nullable|numeric',
            ]);

            // Insert dengan ID yang sama dari Produksi
            DB::table('blending_after_adjust_mikro')->insert([
                'id' => $validated['id'],
                'production_batch_id' => $validated['production_batch_id'],
                'batch_range' => $validated['batch_range'],
                'nomor_blending' => $validated['nomor_blending'],
                'volume' => $validated['volume'],
                'eb' => null,
                'tpc' => null,
                'ym' => null,
                'hasil' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Blending After Adjust Mikro berhasil disimpan.',
                'data' => [
                    'id' => $validated['id'],
                    'production_batch_id' => $validated['production_batch_id'],
                    'batch_range' => $validated['batch_range'],
                    'nomor_blending' => $validated['nomor_blending'],
                    'volume' => $validated['volume'],
                ]
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
