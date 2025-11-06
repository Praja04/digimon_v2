<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GGA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GgaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        GGA::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data GGA berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request)
    {
        $data = GGA::find($request->id_old);
        $data->not_standard = false;
        $data->save();

        GGA::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
            'revisi' => $request->revisi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data GGA berhasil diupdate.',
        ], Response::HTTP_OK);
    }
}
