<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analisa\MonitoringPasteurisasiUpdateRequest;
use App\Models\Color;
use App\Models\MonitoringPasteurisasi;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MonitoringPasteurisasiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('monitoringPasteurisasi')
                ->has('monitoringPasteurisasi')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $monitoringPasteurisasi = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $monitoringPasteurisasi = $monitoringPasteurisasi->filter(function ($batch) {
                        return $batch->isMonitoringPasteurisasiComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $monitoringPasteurisasi = $monitoringPasteurisasi->filter(function ($batch) {
                        return !$batch->isMonitoringPasteurisasiComplete();
                    });
                }
            }

            $monitoringPasteurisasi = $monitoringPasteurisasi->sortBy(function ($batch) {
                return ($batch->isMonitoringPasteurisasiComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($monitoringPasteurisasi)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return  $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return  $data->monitoringPasteurisasi->count() ?? '-';
                })
                ->addColumn('status', function ($data) {
                    $isComplete = $data->isMonitoringPasteurisasiComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('analisa.monitoring-pasteurisasi.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('app.analisa.monitoring_pasteurisasi.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'monitoringPasteurisasi.additionalBatches'
        ])->findOrFail($id);

        foreach ($productionBatch->monitoringPasteurisasi as $blending) {
            // Tambahkan properti custom 'additional_batch_info' ke setiap data
            $blending->additional_batch_info = $blending->additionalBatches->isNotEmpty()
                ? $blending->additionalBatches
                : null;

            $blending->po_number = $productionBatch->po_number;
        }

        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.analisa.monitoring_pasteurisasi.show', compact('colors', 'productionBatch'));
    }

    public function show_batch($id)
    {
        $blending = MonitoringPasteurisasi::with([
            'additionalBatches',
            'productionBatch',
        ])->findOrFail($id);

        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.analisa.monitoring_pasteurisasi.show_batch', compact('colors', 'blending'));
    }

    public function edit($id)
    {
        try {
            $data = MonitoringPasteurisasi::with('color', 'user')->find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(MonitoringPasteurisasiUpdateRequest $request)
    {
        try {
            $id = $request->id;

            $blending = MonitoringPasteurisasi::findOrFail($id);
            $isUpdate = !is_null($blending->status_disposition);
            $userRole = auth()->user()->role;

            if ($blending->disposition) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data dengan ID ini sudah memiliki disposisi.'
                ], 409);
            }

            $disposition = $request->disposition;
            $status_disposition = $request->status_disposition;
            $remark = $request->disposition_remark ?? null;

            // Validasi remarks wajib untuk status tertentu
            if (in_array($status_disposition, ['NOT OK', 'Adjustment']) && empty($remark)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
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

            // Cek apakah sudah ada data di shift ini dengan disposisi yang sama
            $existingShift = MonitoringPasteurisasi::where('production_batch_id', $blending->production_batch_id)
                ->where('batch_range', $blending->batch_range)
                ->where('shift', $shift)
                ->where('disposition', $disposition)
                ->where('id', '!=', $id)
                ->first();

            if ($existingShift) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data untuk shift ' . $shift . ' dengan disposisi ' . $disposition . ' sudah ada.'
                ], 409);
            }
            $totalShifts = MonitoringPasteurisasi::where('production_batch_id', $blending->production_batch_id)
                ->where('batch_range', $blending->batch_range)
                ->where('id', '!=', $id)
                ->distinct('shift')
                ->count('shift');

            if ($totalShifts >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sudah mencapai maksimal 3 shift.'
                ], 409);
            }

            // Cek apakah status_disposition berubah
            $statusChanged = ($blending->status !== $status_disposition);

            // Tentukan disposition
            if ($statusChanged) {
                // Status berubah, hitung ulang disposition
                if ($status_disposition === 'OK') {
                    $disposition = 'Release';
                } elseif ($userRole === 'Foreman' && $request->filled('disposition')) {
                    $disposition = $request->disposition;
                } else {
                    $disposition = match ($status_disposition) {
                        'NOT OK' => null,
                        'Adjustment' => 'Adjustment',
                        default => null,
                    };
                }
            } else {
                // Status tidak berubah
                if ($userRole === 'Foreman' && $request->filled('disposition')) {
                    // Foreman boleh update disposisi manual
                    $disposition = $request->disposition;
                } else {
                    // Pertahankan disposition yang sudah ada
                    $disposition = $blending->disposition;
                }
            }

            $updateData = [
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'bj' => $request->bj,
                'visco' => $request->visco,
                'aw' => $request->aw,
                'buih' => $request->buih,
                'ph' => $request->ph,
                'organo' => $request->organo,
                'endapan' => $request->endapan,
                'color_id' => $request->color,
                'disposition' => $disposition,
                'disposition_remark' => $remark,
                'status' => $status_disposition,
                'shift' => $shift,
            ];

            if (!$isUpdate) {
                $updateData['created_by'] = auth()->user()->id;
            }

            if ($status_disposition === 'Adjustment') {
                $updateData['adjustment_qty_air'] = $request->adjustment_qty_air;
                $updateData['adjustment_qty_garam'] = $request->adjustment_qty_garam;
                $updateData['adjustment_qty_gula'] = $request->adjustment_qty_gula;
                $updateData['not_standard'] = true;
            } else {
                // Jika status bukan Adjustment lagi, clear adjustment data
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] = null;
                    $updateData['adjustment_qty_garam'] = null;
                    $updateData['adjustment_qty_gula'] = null;
                }
            }

            // Handle Resampling
            if ($disposition === 'Resampling') {
                $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                $updateData['not_standard'] = true;
            } elseif ($disposition === 'Release Bersyarat') {
                $updateData['status'] = 'OK';
            }

            if ($disposition === 'Jalan Bareng') {
                $updateData['not_standard'] = true;
            }

            if ($disposition === 'Leveling') {
                $updateData['not_standard'] = true;
            }

            $blending->update($updateData);

            $message = $isUpdate
                ? 'Data berhasil diperbarui.'
                : 'Data berhasil disimpan.';

            // Trigger event hanya jika status berubah ke NOT OK atau Adjustment
            if ($statusChanged && in_array($status_disposition, ['NOT OK', 'Adjustment'])) {
                event(new ProcessOutsideDisposition(
                    "Monitoring Pasteurisasi - Batch " . $blending->batch_range,
                    $blending->production_batch_id,
                    'Monitoring Pasteurisasi',
                    $status_disposition,
                    $remark,
                ));
            }

            return response()->json([
                'status'  => 'success',
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
