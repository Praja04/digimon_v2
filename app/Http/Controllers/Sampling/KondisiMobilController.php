<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use App\Models\SamplingKondisiMobil;
use Illuminate\Http\Request;

class KondisiMobilController extends Controller
{
    public function show() {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'bersih' => 'required',
            'kering' => 'required',
            'benda_asing' => 'required',
            'cacat' => 'required',
            'segel' => 'required',
            'berbau' => 'required',
        ]);

        // Cek apakah data dengan id_identitas sudah ada
        $existing = SamplingKondisiMobil::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Kondisi Mobil sudah pernah disimpan untuk ID ini.'
            ], 409); // 409 = Conflict
        }

        // Tambahkan user ke data yang akan disimpan
        $validated['created_by'] = auth()->user()->id;

        SamplingKondisiMobil::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Mobil berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'bersih' => 'required',
            'kering' => 'required',
            'benda_asing' => 'required',
            'cacat' => 'required',
            'segel' => 'required',
            'berbau' => 'required',
        ]);

        $mobil = SamplingKondisiMobil::find($request->id);
        $mobil->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Mobil berhasil disimpan.'
        ], 201);
    }
}
