<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimbanganRetailMesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimbanganRetailMesinController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mesin' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'waktu' => 'required|date',
            'status' => 'required|string',
            'berat' => 'required|numeric',
            'unit' => 'required|string|max:50'
        ], [], [
            'mesin' => 'Mesin',
            'variant' => 'Variant',
            'waktu' => 'Waktu',
            'status' => 'Status',
            'berat' => 'Berat',
            'unit' => 'Unit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mesin = TimbanganRetailMesin::create([
            'mesin' => $request->mesin,
            'variant' => $request->variant,
            'waktu' => $request->waktu,
            'status' => $request->status,
            'berat' => $request->berat,
            'unit' => $request->unit
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data mesin berhasil ditambahkan',
            'data' => $mesin
        ], 201);
    }
}
