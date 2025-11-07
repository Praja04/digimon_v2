<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringStorageMikro;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MonitoringStorageMikroController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        MonitoringStorageMikro::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_range' => $request->batch_range,
            'nomor_blending' => $request->nomor_blending,
            'volume' => $request->volume,
            'storage' => $request->storage,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Monitoring Storage Kimia berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }
}
