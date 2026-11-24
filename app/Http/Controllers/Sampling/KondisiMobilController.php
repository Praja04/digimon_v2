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
            'keterangan_bersih' => 'nullable|string',
            'keterangan_kering' => 'nullable|string',
            'keterangan_benda_asing' => 'nullable|string',
            'keterangan_cacat' => 'nullable|string',
            'keterangan_segel' => 'nullable|string',
            'keterangan_berbau' => 'nullable|string',
        ]);

        $existing = SamplingKondisiMobil::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Kondisi Mobil sudah pernah disimpan untuk ID ini.'
            ], 409);
        }

        foreach (['bersih', 'kering', 'benda_asing', 'cacat', 'segel', 'berbau'] as $f) {
            if (($validated[$f] ?? '') === 'yes') {
                $validated['keterangan_' . $f] = null;
            }
        }

        $validated['created_by'] = auth()->user()->id;

        SamplingKondisiMobil::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Mobil berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:sampling_kondisi_mobil,id',
            'bersih' => 'required',
            'kering' => 'required',
            'benda_asing' => 'required',
            'cacat' => 'required',
            'segel' => 'required',
            'berbau' => 'required',
            'keterangan_bersih' => 'nullable|string',
            'keterangan_kering' => 'nullable|string',
            'keterangan_benda_asing' => 'nullable|string',
            'keterangan_cacat' => 'nullable|string',
            'keterangan_segel' => 'nullable|string',
            'keterangan_berbau' => 'nullable|string',
        ]);

        $mobil = SamplingKondisiMobil::findOrFail($request->id);

        foreach (['bersih', 'kering', 'benda_asing', 'cacat', 'segel', 'berbau'] as $f) {
            if (($validated[$f] ?? '') === 'yes') {
                $validated['keterangan_' . $f] = null;
            }
        }

        $mobil->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Mobil berhasil disimpan.'
        ], 201);
    }
}
