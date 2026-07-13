<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelarutan2;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Pelarutan2Controller extends Controller
{
    public function store(Request $request): JsonResponse
    {
        Pelarutan2::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 2 berhasil disimpan.',
        ], Response::HTTP_CREATED);
    }

    public function update_revisi(Request $request)
    {
        $data = Pelarutan2::find($request->id_old);
        $data->not_standard = false;
        $data->save();

        Pelarutan2::create([
            'id' => $request->id,
            'production_batch_id' => $request->production_batch_id,
            'batch_number' => $request->batch_number,
            'dissolver_number' => $request->dissolver_number,
            'revisi' => $request->revisi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 2 berhasil diupdate.',
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $data = Pelarutan2::find($id);
        if ($data) {
            $data->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Pelarutan 2 berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
