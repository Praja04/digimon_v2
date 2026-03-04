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
            'leleh' => 'required',
            'warna' => 'required',
            'campuran' => 'required',
            'aroma' => 'required',
            'sesuai_std' => 'required',

        ]);
        $validated['created_by'] = auth()->user()->id;

        $fields = ['leleh', 'warna', 'campuran', 'aroma', 'sesuai_std'];
        foreach ($fields as $field) {
            if (!empty($validated[$field])) {
                if (preg_match('/^no,\s*karena\s+(.+)$/i', $validated[$field], $matches)) {
                    $validated[$field] = 'no';
                    $validated['keterangan_' . $field] = $matches[1];
                } else {
                    $validated['keterangan_' . $field] = null;
                }
            }
        }
        $existing = SamplingFisikRaw::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Raw sudah pernah disimpan untuk ID ini.'
            ], 409);
        }

        SamplingFisikRaw::create([
            'id_identitas'          => $request->id_identitas,
            'leleh'                 => $request->leleh,
            'keterangan_leleh'      => $request->keterangan_leleh,
            'warna'                 => $request->warna,
            'keterangan_warna'      => $request->keterangan_warna,
            'campuran'              => $request->campuran,
            'keterangan_campuran'   => $request->keterangan_campuran,
            'aroma'                 => $request->aroma,
            'keterangan_aroma'      => $request->keterangan_aroma,
            'sesuai_std'            => $request->sesuai_std,
            'keterangan_sesuai_std' => $request->keterangan_sesuai_std,
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
            'leleh' => 'required',
            'warna' => 'required',
            'campuran' => 'required',
            'aroma' => 'required',
            'sesuai_std' => 'required',
        ]);

        $data = SamplingFisikRaw::findOrFail($request->id);

        $data->leleh = $request->leleh;
        $data->warna = $request->warna;
        $data->campuran = $request->campuran;
        $data->aroma = $request->aroma;
        $data->sesuai_std = $request->sesuai_std;

        $data->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Raw berhasil disimpan.'
        ], 201);
    }
}
