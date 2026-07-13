<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelarutan1;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Pelarutan1Controller extends Controller
{
    public function store(Request $request): JsonResponse
    {
        Pelarutan1::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 1 berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request)
    {
        $data = Pelarutan1::find($request->id_old);
        $data->not_standard = false;
        $data->save();

        Pelarutan1::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
            'revisi' => $request->revisi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 1 berhasil diupdate.',
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $data = Pelarutan1::find($id);
        if ($data) {
            $data->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 1 berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
