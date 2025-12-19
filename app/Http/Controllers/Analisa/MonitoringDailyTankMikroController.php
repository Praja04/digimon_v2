<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Models\MonitoringDailyTank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonitoringDailyTankMikroController extends Controller
{
    public function show($id)
    {
        $monitoringDailyTank = MonitoringDailyTank::with('qcField')->findOrFail($id);
        return view('app.analisa.monitoring-daily-tank.mikro.show', compact('monitoringDailyTank'));
    }

    public function getData(Request $request)
    {
        try {
            $monitoringDailyTank = MonitoringDailyTank::where('id', $request->id)->first();
            if (!$monitoringDailyTank) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $monitoringDailyTank->id,
                    'shift' => $monitoringDailyTank->shift,
                    'nama_analis' => $monitoringDailyTank->nama_analis,
                    'eb' => $monitoringDailyTank->eb,
                    'tpc' => $monitoringDailyTank->tpc,
                    'ym' => $monitoringDailyTank->ym,
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

            $monitoringDailyTank = MonitoringDailyTank::find($request->id);

            if (!$monitoringDailyTank) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $rules = [];
            $updateData = [];

            if ($request->filled('shift_analis') || $request->filled('nama_analis')) {
                $rules['shift_analis'] = 'required|integer|min:1|max:3';
                $rules['nama_analis'] = 'required|string|max:255';

                $updateData['shift'] = $request->shift_analis;
                $updateData['nama_analis'] = $request->nama_analis;
            }

            if ($request->filled('eb')) {
                if (empty($monitoringDailyTank->shift) || empty($monitoringDailyTank->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input EB.'
                    ], 409);
                }
                $rules['eb'] = 'required|numeric|min:0';
                $updateData['eb'] = $request->eb;
            }

            if ($request->filled('tpc')) {
                if (empty($monitoringDailyTank->shift) || empty($monitoringDailyTank->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input TPC.'
                    ], 409);
                }
                $rules['tpc'] = 'required|numeric|min:0';
                $updateData['tpc'] = $request->tpc;
            }

            if ($request->filled('ym')) {
                if (empty($monitoringDailyTank->shift) || empty($monitoringDailyTank->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input YM.'
                    ], 409);
                }
                $rules['ym'] = 'required|numeric|min:0';
                $updateData['ym'] = $request->ym;
            }

            // ✅ Validasi hanya field yang ada di rules
            if (!empty($rules)) {
                $validator = Validator::make($request->only(array_keys($rules)), $rules, [
                    'shift_analis.required' => 'Shift wajib diisi.',
                    'shift_analis.integer' => 'Shift harus berupa angka.',
                    'shift_analis.min' => 'Shift minimal 1.',
                    'shift_analis.max' => 'Shift maksimal 3.',
                    'nama_analis.required' => 'Nama Analis wajib diisi.',
                    'nama_analis.string' => 'Nama Analis harus berupa teks.',
                    'nama_analis.max' => 'Nama Analis maksimal 255 karakter.',
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

            // Update data
            $monitoringDailyTank->update($updateData);

            // ✅ Refresh data dari database
            $monitoringDailyTank->refresh();

            // ✅ Tentukan hasil berdasarkan kelengkapan dan kriteria
            $hasil = 'PENDING';

            if ($monitoringDailyTank->eb !== null && $monitoringDailyTank->tpc !== null && $monitoringDailyTank->ym !== null) {
                // ✅ EB = 0, TPC = 30, YM = 0 → OK
                if ($monitoringDailyTank->eb == 0 && $monitoringDailyTank->tpc == 30 && $monitoringDailyTank->ym == 0) {
                    $hasil = 'OK';
                } else {
                    $hasil = 'NOT OK';
                }
            }

            // Update hasil
            $monitoringDailyTank->update(['hasil' => $hasil]);

            // Tentukan nama field untuk pesan
            $fieldName = '';
            if (isset($updateData['eb'])) $fieldName = 'EB';
            if (isset($updateData['tpc'])) $fieldName = 'TPC';
            if (isset($updateData['ym'])) $fieldName = 'YM';

            $message = "Data {$fieldName} berhasil disimpan.";

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
                'data' => [
                    'eb' => $monitoringDailyTank->eb,
                    'tpc' => $monitoringDailyTank->tpc,
                    'ym' => $monitoringDailyTank->ym,
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
