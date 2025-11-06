<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GGAS;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GgasController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        GGAS::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data GGAS berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request)
    {
        $data = GGAS::find($request->id_old);
        $data->not_standard = false;
        $data->save();

        GGAS::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
            'revisi' => $request->revisi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data GGAS berhasil diupdate.',
        ], Response::HTTP_OK);
    }
}
