<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analisa\MonitoringTurunBlendingUpdateRequest;
use App\Models\Color;
use App\Models\MonitoringTurunBlending;
use App\Models\MonitoringTurunBlendingDraft;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MonitoringTurunBlendingController extends Controller
{
    public function menu()
    {
        return view('app.monitoring_turun_blending.menu');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('monitoringTurunBlending')
                ->has('monitoringTurunBlending')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $monitoringTurunBlending = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $monitoringTurunBlending = $monitoringTurunBlending->filter(function ($batch) {
                        return $batch->isMonitoringTurunBlendingComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $monitoringTurunBlending = $monitoringTurunBlending->filter(function ($batch) {
                        return !$batch->isMonitoringTurunBlendingComplete();
                    });
                }
            }

            $monitoringTurunBlending = $monitoringTurunBlending
                ->sortBy(function ($batch) {
                    return ($batch->isMonitoringTurunBlendingComplete()) ? 1 : 0;
                })
                ->values();

            return DataTables::of($monitoringTurunBlending)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return $data->monitoringTurunBlending->count() ?? '-';
                })
                ->addColumn('status', function ($data) {
                    $isComplete = $data->isMonitoringTurunBlendingComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route(
                        'analisa.monitoring-turun-blending.show',
                        ['id' => $data->id]
                    );

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('app.analisa.monitoring_turun_blending.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'monitoringTurunBlending.additionalBatches'
        ])->findOrFail($id);

        foreach ($productionBatch->monitoringTurunBlending as $blending) {
            $blending->additional_batch_info = $blending->additionalBatches->isNotEmpty()
                ? $blending->additionalBatches
                : null;

            $blending->po_number = $productionBatch->po_number;
        }

        return view(
            'app.analisa.monitoring_turun_blending.show',
            compact('productionBatch')
        );
    }

    public function show_batch($id)
    {
        $blending = MonitoringTurunBlending::with([
            'additionalBatches',
            'productionBatch',
        ])->findOrFail($id);

        $draft = MonitoringTurunBlendingDraft::where(
            'monitoring_turun_blending_id',
            $blending->id
        )->first();

        return view(
            'app.analisa.monitoring_turun_blending.show_batch',
            compact('blending', 'draft')
        );
    }

    public function edit($id)
    {
        try {
            $data = MonitoringTurunBlending::with('user')->find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $responseData = $data->toArray();

            $draft = MonitoringTurunBlendingDraft::where(
                'monitoring_turun_blending_id',
                $data->id
            )->first();

            $responseData['draft'] = $draft
                ? $draft->toArray()
                : null;

            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveDraft(Request $request)
    {
        $userRole = auth()->user()->role;

        if (!in_array($userRole, ['Analis Kimia', 'Foreman'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Simpan sementara hanya dapat dilakukan oleh Analis Kimia atau Foreman.',
            ], 403);
        }

        $data = $request->all();

        foreach ([
            'brix',
            'visco',
            'aw',
            'adjustment_qty_air',
            'adjustment_qty_gula',
            'adjustment_qty_garam',
        ] as $field) {
            if (
                isset($data[$field]) &&
                is_string($data[$field]) &&
                $data[$field] !== ''
            ) {
                $data[$field] = str_replace(
                    ',',
                    '.',
                    str_replace(' ', '', $data[$field])
                );
            }
        }

        $validator = Validator::make($data, [
            'id' => [
                'required',
                'integer',
                'exists:monitoring_turun_blending,id',
            ],
            'brix' => ['nullable', 'numeric', 'min:0'],
            'visco' => ['nullable', 'numeric', 'min:0'],
            'aw' => ['nullable', 'numeric', 'min:0'],
            'status_disposition' => [
                'nullable',
                'in:OK,NOT OK,Adjustment',
            ],
            'disposition' => [
                'nullable',
                'in:Release,Release Bersyarat,Resampling,Adjustment,Reject,Repro,Jalan Bareng,Leveling',
            ],
            'disposition_remark' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'adjustment_qty_air' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'adjustment_qty_gula' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'adjustment_qty_garam' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data sementara tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $blending = MonitoringTurunBlending::findOrFail($data['id']);

        if ($userRole === 'Analis Kimia' && !is_null($blending->status)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data sudah disimpan final oleh Analis Kimia.',
            ], 409);
        }

        if ($userRole === 'Foreman' && is_null($blending->status)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Analis Kimia belum final. Foreman belum dapat menyimpan sementara.',
            ], 409);
        }

        $draft = MonitoringTurunBlendingDraft::updateOrCreate(
            [
                'monitoring_turun_blending_id' => $blending->id,
            ],
            [
                'brix' => $data['brix'] ?? null,
                'visco' => $data['visco'] ?? null,
                'aw' => $data['aw'] ?? null,
                'status_disposition' => $data['status_disposition'] ?? null,
                'disposition' => $data['disposition'] ?? null,
                'disposition_remark' => $data['disposition_remark'] ?? null,
                'adjustment_qty_air' => $data['adjustment_qty_air'] ?? null,
                'adjustment_qty_gula' => $data['adjustment_qty_gula'] ?? null,
                'adjustment_qty_garam' => $data['adjustment_qty_garam'] ?? null,
                'created_by' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => $userRole === 'Foreman'
                ? 'Data Foreman berhasil disimpan sementara.'
                : 'Data Analis Kimia berhasil disimpan sementara.',
            'data' => $draft,
        ]);
    }

    public function update(MonitoringTurunBlendingUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;

            $blending = MonitoringTurunBlending::findOrFail($id);
            $isUpdate = !is_null($blending->status);
            $userRole = auth()->user()->role;

            if ($userRole === 'Analis Kimia') {
                if (!is_null($blending->disposition)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.',
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($blending->status)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.',
                    ], 403);
                }
            }

            $statusDisposition = $request->status_disposition;
            $remark = $request->disposition_remark ?? null;

            if (
                in_array($statusDisposition, ['NOT OK', 'Adjustment'], true) &&
                empty($remark)
            ) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.',
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

            $statusChanged = ($blending->status !== $statusDisposition);

            $updateData = [
                'brix' => $request->brix,
                'visco' => $request->visco,
                'aw' => $request->aw,
                'disposition_remark' => $remark,
                'status' => $statusDisposition,
                'shift' => $shift,
            ];

            if ($userRole === 'Analis Kimia') {
                $updateData['disposition'] = null;

                if (!$isUpdate) {
                    $updateData['created_by'] = auth()->user()->id;
                }
            } elseif ($userRole === 'Foreman') {
                if (!$request->filled('disposition')) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Foreman wajib memilih disposisi.',
                    ], 409);
                }

                $disposition = $request->disposition;
                $updateData['disposition'] = $disposition;
            }

            $adjustmentAir = null;
            $adjustmentGaram = null;
            $adjustmentGula = null;

            if ($statusDisposition === 'Adjustment') {
                if (!empty($request->adjustment_qty_air)) {
                    $adjustmentAir = str_replace(',', '.', $request->adjustment_qty_air);
                }

                if (!empty($request->adjustment_qty_garam)) {
                    $adjustmentGaram = str_replace(',', '.', $request->adjustment_qty_garam);
                }

                if (!empty($request->adjustment_qty_gula)) {
                    $adjustmentGula = str_replace(',', '.', $request->adjustment_qty_gula);
                }

                $updateData['adjustment_qty_air'] = $adjustmentAir;
                $updateData['adjustment_qty_garam'] = $adjustmentGaram;
                $updateData['adjustment_qty_gula'] = $adjustmentGula;
                $updateData['not_standard'] = true;
            } else {
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] = null;
                    $updateData['adjustment_qty_garam'] = null;
                    $updateData['adjustment_qty_gula'] = null;
                    $updateData['not_standard'] = false;
                }
            }

            if ($userRole === 'Foreman') {
                if (($updateData['disposition'] ?? null) === 'Resampling') {
                    $updateData['disposition_remark'] = $remark
                        ? $remark . ' (Resampling)'
                        : 'Resampling';

                    $updateData['not_standard'] = true;
                }

                if (($updateData['disposition'] ?? null) === 'Jalan Bareng') {
                    $updateData['not_standard'] = true;
                }

                if (($updateData['disposition'] ?? null) === 'Leveling') {
                    $updateData['not_standard'] = true;
                }
            }

            if ($request->filled('revisi')) {
                $updateData['revisi'] = $request->revisi;
            } else {
                $updateData['revisi'] = $blending->revisi;
            }

            $blending->update($updateData);

            if (
                $remark !== null &&
                $remark !== '-' &&
                $statusDisposition !== 'Adjustment'
            ) {
                $remarkText = $remark;
            } elseif ($statusDisposition === 'Adjustment') {
                $remarkText = sprintf(
                    'Adjustment Air: %s Liter, Garam: %s Kg, Gula: %s Kg',
                    $adjustmentAir ?? 0,
                    $adjustmentGaram ?? 0,
                    $adjustmentGula ?? 0
                );
            } elseif ($updateData['not_standard'] ?? false) {
                $remarkText = 'Adjustment';
            } else {
                $remarkText = '-';
            }

            $apiResponse = Http::post(
                env('PRODUCTION_URL') . 'api/monitoring-turun-blending/' . $blending->id,
                [
                    'disposition' => $updateData['disposition'] ?? null,
                    'disposition_remark' => $remarkText,
                    'revisi' => $updateData['revisi'],
                    'is_adjustment' => $statusDisposition === 'Adjustment',
                    'not_standard' => $updateData['not_standard'] ?? false,
                    'status' => $statusDisposition,
                ]
            );

            if (!$apiResponse->successful()) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui data Monitoring Turun Blending ke Production.',
                ], 500);
            }

            MonitoringTurunBlendingDraft::where(
                'monitoring_turun_blending_id',
                $blending->id
            )->delete();

            DB::commit();

            $shouldSendNotification = false;
            $notificationTitle = 'Monitoring Turun Blending - Batch ' . $blending->batch_range;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= ' - Menunggu Review Foreman';
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $blending->production_batch_id,
                    'Monitoring Turun Blending',
                    $statusDisposition,
                    $remarkText,
                    route(
                        'analisa.monitoring-turun-blending.show',
                        $blending->production_batch_id
                    )
                ));
            }

            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data berhasil diperbarui.'
                    : 'Data berhasil disimpan.';
            } elseif ($userRole === 'Foreman') {
                $message = 'Disposisi berhasil diberikan.';
            } else {
                $message = 'Data berhasil disimpan.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
