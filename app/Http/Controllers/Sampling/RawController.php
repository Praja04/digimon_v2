<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use App\Models\SamplingFisikRaw;
use Illuminate\Http\Request;

class RawController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'sesuai_std'   => 'required',
            'warna'        => 'required',
            'aroma'        => 'required',
            'leleh'        => 'nullable',
            'campuran'     => 'nullable',
        ]);

        $existing = SamplingFisikRaw::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Raw sudah pernah disimpan untuk ID ini.'
            ], 409);
        }

        SamplingFisikRaw::create([
            'id_identitas'          => $request->id_identitas,
            'sesuai_std'            => $request->sesuai_std,
            'keterangan_sesuai_std' => $request->keterangan_sesuai_std,
            'warna'                 => $request->warna,
            'keterangan_warna'      => $request->keterangan_warna,
            'aroma'                 => $request->aroma,
            'keterangan_aroma'      => $request->keterangan_aroma,
            'leleh'                 => $request->leleh ?? null,
            'keterangan_leleh'      => $request->keterangan_leleh ?? null,
            'campuran'              => $request->campuran ?? null,
            'keterangan_campuran'   => $request->keterangan_campuran ?? null,
            'created_by'            => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Raw berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:sampling_fisik_raw,id',
            'sesuai_std' => 'required',
            'warna'      => 'required',
            'aroma'      => 'required',
        ]);

        $data = SamplingFisikRaw::findOrFail($request->id);

        $data->sesuai_std = $request->sesuai_std;
        $data->keterangan_sesuai_std = $request->keterangan_sesuai_std;
        $data->warna = $request->warna;
        $data->keterangan_warna = $request->keterangan_warna;
        $data->aroma = $request->aroma;
        $data->keterangan_aroma = $request->keterangan_aroma;

        if ($request->has('leleh')) $data->leleh = $request->leleh;
        if ($request->has('campuran')) $data->campuran = $request->campuran;

        $data->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Raw berhasil disimpan.'
        ], 201);
    }
}
