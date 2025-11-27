<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analisa\MonitoringDailyTankKimiaUpdateRequest;
use App\Models\Color;
use App\Models\MonitoringDailyTank;
use Illuminate\Http\Request;

class MonitoringDailyTankKimiaController extends Controller
{
    public function show($id)
    {
        $monitoringDailyTank = MonitoringDailyTank::with('qcField')->findOrFail($id);
        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.analisa.monitoring-daily-tank.kimia.show', compact('monitoringDailyTank', 'colors'));
    }

    public function update(MonitoringDailyTankKimiaUpdateRequest $request)
    {
        try {
            // Validasi tambahan untuk alasan disposisi
            $statusParameter = $request->status_parameter;
            $statusDisposisi = $request->status_disposisi;
            $alasanDisposisi = $request->alasan_disposisi;

            // Jika bukan OK + Release, maka alasan disposisi wajib diisi
            if (!($statusParameter === 'OK' && $statusDisposisi === 'Release')) {
                if (empty($alasanDisposisi)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Alasan disposisi wajib diisi untuk status dan disposisi yang dipilih!'
                    ], 409);
                }
            }

            // Cari data monitoring
            $monitoringDailyTank = MonitoringDailyTank::with('productionBatch')->findOrFail($request->id);

            // Cek apakah data sudah pernah dianalisa
            if ($monitoringDailyTank->tanggal_analisa !== null && $monitoringDailyTank->disposisi !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data ini sudah pernah dianalisa dan tidak dapat diubah!'
                ], 409);
            }

            $currentHour = (int) now()->format('H');
            if ($currentHour >= 6 && $currentHour < 14) {
                $shift = 1;
            } elseif ($currentHour >= 14 && $currentHour < 22) {
                $shift = 2;
            } else {
                $shift = 3;
            }

            $monitoringDailyTank->update([
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'bj' => $request->bj,
                'visco' => $request->visco,
                'aw' => $request->aw,
                'ph' => $request->ph,
                'buih' => $request->buih,
                'organo' => strtoupper($request->organo),
                'endapan' => $request->endapan ? strtoupper($request->endapan) : null,
                'color_id' => $request->color,
                'status' => $request->status_parameter,
                'disposisi' => $request->status_disposisi,
                'alasan_disposisi' => $request->alasan_disposisi ? strtoupper($request->alasan_disposisi) : null,
                'qc_analisa' => auth()->id(),
                'tanggal_analisa' => now(),
                'shift_analisa' => $shift,
                'tanggal_input_hasil' => now(),
            ]);

            if ($request->status_parameter == 'NOT OK') {
                event(new ProcessOutsideDisposition(
                    "Monitoring Daily Tank Kimia - Batch " . $monitoringDailyTank->productionBatch->batch_number,
                    $monitoringDailyTank->productionBatch->id,
                    'Monitoring Daily Tank Kimia',
                    $request->status_disposisi,
                    $request->alasan_disposisi,
                ));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data analisa berhasil disimpan!'
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
