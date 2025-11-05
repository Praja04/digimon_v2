<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Models\BlendingAfterAdjustMikro;
use App\Models\KonfirmasiBlendingAfterAdjustMikro;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BlendingAwalMikroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('blendingAfterAdjustMikro')
                ->has('blendingAfterAdjustMikro')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $blendingAwalMikro = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $blendingAwalMikro = $blendingAwalMikro->filter(function ($batch) {
                        return $batch->isBlendingAwalMikroComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $blendingAwalMikro = $blendingAwalMikro->filter(function ($batch) {
                        return !$batch->isBlendingAwalMikroComplete();
                    });
                }
            }

            $blendingAwalMikro = $blendingAwalMikro->sortBy(function ($batch) {
                return ($batch->isBlendingAwalMikroComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($blendingAwalMikro)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return  $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return  $data->blendingAfterAdjustMikro->count() ?? '-';
                })
                ->addColumn('status_blending_awal', function ($data) {
                    $isComplete = $data->isBlendingAwalMikroComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('analisa.blending-awal-mikro.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status_blending_awal', 'action'])
                ->make(true);
        }
        return view('app.analisa.blending_awal_mikro.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'blendingAfterAdjustMikro'
        ])->findOrFail($id);

        return view('app.analisa.blending_awal_mikro.show', compact('productionBatch'));
    }

    public function show_batch($id)
    {
        $blending = BlendingAfterAdjustMikro::with('productionBatch')->findOrFail($id);

        return view('app.analisa.blending_awal_mikro.show_batch', compact('blending'));
    }

    public function getBlendingData(Request $request)
    {
        try {
            $blending = BlendingAfterAdjustMikro::where('id', $request->id)->first();

            if (!$blending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $blending->id,
                    'eb' => $blending->eb,
                    'tpc' => $blending->tpc,
                    'ym' => $blending->ym,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // ✅ Hanya merge field yang benar-benar ada dan tidak kosong
            $mergeData = [];

            if ($request->filled('eb')) {
                $mergeData['eb'] = str_replace(',', '.', $request->eb);
            }

            if ($request->filled('tpc')) {
                $mergeData['tpc'] = str_replace(',', '.', $request->tpc);
            }

            if ($request->filled('ym')) {
                $mergeData['ym'] = str_replace(',', '.', $request->ym);
            }

            $request->merge($mergeData);

            $blending = BlendingAfterAdjustMikro::find($request->id);

            if (!$blending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // ✅ Validasi hanya field yang benar-benar dikirim dan tidak kosong
            $rules = [];
            $updateData = [];

            if ($request->filled('eb')) {
                $rules['eb'] = 'required|numeric|min:0';
                $updateData['eb'] = $request->eb;
            }

            if ($request->filled('tpc')) {
                $rules['tpc'] = 'required|numeric|min:0';
                $updateData['tpc'] = $request->tpc;
            }

            if ($request->filled('ym')) {
                $rules['ym'] = 'required|numeric|min:0';
                $updateData['ym'] = $request->ym;
            }

            // ✅ Validasi hanya field yang ada di rules
            if (!empty($rules)) {
                $validator = Validator::make($request->only(array_keys($rules)), $rules, [
                    'eb.required' => 'EB wajib diisi.',
                    'eb.numeric' => 'EB harus berupa angka.',
                    'eb.min' => 'EB tidak boleh negatif.',
                    'tpc.required' => 'TPC wajib diisi.',
                    'tpc.numeric' => 'TPC harus berupa angka.',
                    'tpc.min' => 'TPC tidak boleh negatif.',
                    'ym.required' => 'YM wajib diisi.',
                    'ym.numeric' => 'YM harus berupa angka.',
                    'ym.min' => 'YM tidak boleh negatif.',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }
            }

            // Cek apakah ada data yang akan diupdate
            if (empty($updateData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang diinput.'
                ], 409);
            }

            // ✅ TENTUKAN SHIFT OTOMATIS BERDASARKAN JAM
            $currentHour = (int) now()->format('H');
            if ($currentHour >= 6 && $currentHour < 14) {
                $shift = 1;
            } elseif ($currentHour >= 14 && $currentHour < 22) {
                $shift = 2;
            } else {
                $shift = 3;
            }

            // ✅ SIMPAN KONFIRMASI UNTUK SETIAP PROSES (EB, TPC, YM)
            // Simpan konfirmasi sebelum update data utama
            if ($request->filled('eb') || $request->filled('tpc') || $request->filled('ym')) {
                KonfirmasiBlendingAfterAdjustMikro::create([
                    'blending_after_adjust_mikro_id' => $blending->id,
                    'shift' => $shift,
                    'created_by' => auth()->id(),
                ]);
            }

            // Update data
            $blending->update($updateData);

            // ✅ Refresh data dari database
            $blending->refresh();

            // ✅ Tentukan hasil berdasarkan kelengkapan dan kriteria
            $hasil = 'PENDING';

            if ($blending->eb !== null && $blending->tpc !== null && $blending->ym !== null) {
                // ✅ EB = 0, TPC = 30, YM = 0 → OK
                if ($blending->eb == 0 && $blending->tpc == 30 && $blending->ym == 0) {
                    $hasil = 'OK';
                } else {
                    $hasil = 'NOT OK';
                }
            }

            // Update hasil
            $blending->update(['hasil' => $hasil]);

            // Tentukan nama field untuk pesan
            $fieldName = '';
            if (isset($updateData['eb'])) $fieldName = 'EB';
            if (isset($updateData['tpc'])) $fieldName = 'TPC';
            if (isset($updateData['ym'])) $fieldName = 'YM';

            $message = "Data {$fieldName} berhasil disimpan (Shift {$shift}).";

            if ($hasil !== 'PENDING') {
                if ($hasil === 'OK') {
                    $message .= " ✅ Status: LOLOS (OK)";
                } else {
                    $message .= " ❌ Status: TIDAK LOLOS (NOT OK)";
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'hasil' => $hasil,
                'shift' => $shift,
                'data' => [
                    'eb' => $blending->eb,
                    'tpc' => $blending->tpc,
                    'ym' => $blending->ym,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
