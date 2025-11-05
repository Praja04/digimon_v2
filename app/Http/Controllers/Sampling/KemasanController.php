<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use App\Models\SamplingFisikKemasan;
use Illuminate\Http\Request;

class KemasanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'kotor' => 'required',
            'rusak' => 'required',
            'sesuai_std' => 'required',
            'lain_lain' => 'nullable',
            'berair' => 'nullable',
            'basah' => 'nullable',
            'campuran' => 'nullable',
        ]);

        // Tambahkan user ke data yang akan disimpan
        $validated['created_by'] = auth()->user()->id;

        // Pisahkan nilai dan keterangan untuk field-field tertentu
        $fields = ['kotor', 'rusak', 'sesuai_std', 'berair', 'basah', 'campuran', 'lain-lain'];
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
        $existing = SamplingFisikKemasan::where('id_identitas', $validated['id_identitas'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sampling Kemasan sudah pernah disimpan untuk ID ini.'
            ], 409); // 409 = Conflict
        }

        // Simpan data
        SamplingFisikKemasan::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Kemasan berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request)
    {
        $kemasan = SamplingFisikKemasan::with('identitas')->findOrFail($request->id);
        if ($kemasan->identitas->jenis_gula == 'Garam') {
            $request->validate([
                'kotor' => 'required',
                'berair' => 'required',
                'rusak' => 'required',
                'sesuai_std' => 'required',
                'lain_lain' => 'nullable',
                'basah' => 'required',
                'campuran' => 'required',
            ]);
        } else {
            $request->validate([
                'kotor' => 'required',
                'rusak' => 'required',
                'sesuai_std' => 'required',
                'lain_lain' => 'nullable',
                'berair' => 'nullable',
                'basah' => 'nullable',
                'campuran' => 'nullable',
            ]);
        }

        $kemasan->kotor = $request->kotor;
        $kemasan->rusak = $request->rusak;
        $kemasan->sesuai_std = $request->sesuai_std;
        $kemasan->lain_lain = $request->lain_lain;
        $kemasan->berair = $request->berair;
        $kemasan->basah = $request->basah;
        $kemasan->campuran = $request->campuran;
        $kemasan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sampling Kondisi Kemasan berhasil disimpan.'
        ], 201);
    }
}
