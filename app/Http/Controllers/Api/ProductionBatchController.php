<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionBatchRequest;
use App\Http\Resources\ProductionBatchResource;
use App\Models\ProductionBatch;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateProductionBatchRequest;
class ProductionBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ProductionBatch::query()->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Production Batch berhasil diambil.',
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $data = ProductionBatch::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Production Batch berhasil diambil.',
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductionBatchRequest $request): JsonResponse
    {
        ProductionBatch::create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Production Batch berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductionBatchRequest $request, $id): JsonResponse
    {
        $data = ProductionBatch::findOrFail($id);

        $data->update([
            'po_number' => $request->po_number,
            'variant' => $request->variant,
            'date' => $request->date,
            'batch_range' => $request->batch_range,
            'formulasi' => $request->formulasi,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $data = ProductionBatch::find($id);

        if ($data) {
            $data->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Production Batch berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
