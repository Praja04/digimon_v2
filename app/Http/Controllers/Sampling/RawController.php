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
        // Tambahkan user ke data yang akan disimpan
        $validated['created_by'] = auth()->user()->id;

        // Pisahkan nilai dan keterangan untuk field-field tertentu
        $fields = ['leleh', 'warna', 'campuran', 'aroma', 'sesuai_std'];
        foreach ($fields as $field) {
            if (!empty($validated[$field])) {
                // Cek apakah ada pola "no, karena ..."
                if (preg_match('/^no,\s*karena\s+(.+)$/i', $validated[$field], $matches)) {
                    $validated[$field] = 'no';
                    $validated['keterangan_' . $field] = $matches[1];
                } else {
                    $validated['keterangan_' . $field] = null;
                }
            }
        }
        // Cek apakah data dengan id_identitas sudah ada
        $existing = SamplingFisikRaw::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Raw sudah pernah disimpan untuk ID ini.'
            ], 409); // 409 = Conflict
        }

        SamplingFisikRaw::create($validated);

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
