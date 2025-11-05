<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use App\Models\SamplingDokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'coa' => 'required',
            'surat_jalan' => 'required',
            'packing_list' => 'required',
            'identitas_kemasan' => 'required',
            'logo_halal' => 'required',
            'kesesuaian_matriks_bahan' => 'required',
        ]);

        $validated['created_by'] = auth()->user()->id;
        $existing = SamplingDokumen::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Dokumen sudah pernah disimpan untuk ID ini.'
            ], 409);
        }

        SamplingDokumen::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Dokumen berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'coa' => 'required',
            'surat_jalan' => 'required',
            'packing_list' => 'required',
            'identitas_kemasan' => 'required',
            'logo_halal' => 'required',
            'kesesuaian_matriks_bahan' => 'required',
        ]);

        $dokumen = SamplingDokumen::findOrFail($request->id);

        $dokumen->update($request->all());

          return response()->json([
            'status' => 'success',
            'message' => 'Sampling Dokumen berhasil disimpan.'
        ], 201);
    }
}
