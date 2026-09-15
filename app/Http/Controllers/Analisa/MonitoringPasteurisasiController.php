<?php

namespace App\Http\Controllers\Analisa;

use App\Events\MonitoringPasteurisasiUpdated;
use App\Events\ProcessOutsideDisposition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analisa\MonitoringPasteurisasiUpdateRequest;
use App\Models\Color;
use App\Models\MonitoringPasteurisasi;
use App\Models\MonitoringPasteurisasiDraft;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
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
                    return $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return $data->monitoringPasteurisasi->count() ?? '-';
                })
                ->addColumn('status', function ($data) {
                    $isComplete = $data->isMonitoringPasteurisasiComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route(
                        'analisa.monitoring-pasteurisasi.show',
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

        return view('app.analisa.monitoring_pasteurisasi.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'monitoringPasteurisasi.additionalBatches'
        ])->findOrFail($id);

        foreach ($productionBatch->monitoringPasteurisasi as $blending) {
            $blending->additional_batch_info = $blending->additionalBatches->isNotEmpty()
                ? $blending->additionalBatches
                : null;

            $blending->po_number = $productionBatch->po_number;
        }

        return view(
            'app.analisa.monitoring_pasteurisasi.show',
            compact('productionBatch')
        );
    }

    public function show_batch($id)
    {
        $pasteurisasi = MonitoringPasteurisasi::with([
            'additionalBatches',
            'productionBatch',
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Load Draft
        |--------------------------------------------------------------------------
        | Kalau user sebelumnya pernah Simpan Sementara,
        | draft akan dikirim ke Blade agar bisa dilanjutkan.
        */
        $draft = MonitoringPasteurisasiDraft::where(
            'monitoring_pasteurisasi_id',
            $pasteurisasi->id
        )->first();

        return view(
            'app.analisa.monitoring_pasteurisasi.show_batch',
            compact('pasteurisasi', 'draft')
        );
    }

    public function edit($id)
    {
        try {
            $data = MonitoringPasteurisasi::with('user')->find($id);

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

    /**
     * Simpan Sementara / Draft.
     *
     * Draft boleh belum lengkap, sehingga tidak menggunakan
     * MonitoringPasteurisasiUpdateRequest yang berisi validasi final.
     */
    public function saveDraft(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Normalisasi angka koma menjadi titik
        |--------------------------------------------------------------------------
        */
        $numericFields = [
            'brix',
            'nacl',
            'bj',
            'visco',
            'aw',
            'buih',
            'ph',
            'endapan',
            'adjustment_qty_air',
            'adjustment_qty_garam',
            'adjustment_qty_gula',
        ];

        $preparedData = [];

        foreach ($numericFields as $field) {
            if (
                $request->has($field)
                && is_string($request->input($field))
                && trim($request->input($field)) !== ''
            ) {
                $cleanedValue = str_replace(
                    ' ',
                    '',
                    $request->input($field)
                );

                $preparedData[$field] = str_replace(
                    ',',
                    '.',
                    $cleanedValue
                );
            }
        }

        $request->merge($preparedData);

        /*
        |--------------------------------------------------------------------------
        | Validasi Draft
        |--------------------------------------------------------------------------
        | Semua field analisa nullable.
        */
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                'exists:monitoring_pasteurisasi,id',
            ],

            'brix' => 'nullable|numeric|min:0|max:100',
            'nacl' => 'nullable|numeric|min:0|max:100',
            'bj' => 'nullable|numeric',
            'visco' => 'nullable|numeric',
            'aw' => 'nullable|numeric',
            'buih' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'endapan' => 'nullable|numeric',

            'organo' => 'nullable|string',
            'aroma' => 'nullable|string',

            'status_disposition' => 'nullable|in:OK,NOT OK,Adjustment',

            'disposition' => [
                'nullable',
                'in:Release,Release Bersyarat,Adjustment,Resampling,Reject,Repro,Jalan Bareng,Leveling',
            ],

            'disposition_remark' => 'nullable|string|max:1000',

            'adjustment_qty_air' => 'nullable|numeric',
            'adjustment_qty_garam' => 'nullable|numeric',
            'adjustment_qty_gula' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data draft tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $pasteurisasi = MonitoringPasteurisasi::findOrFail(
                $request->id
            );

            $userRole = auth()->user()->role;

            /*
            |--------------------------------------------------------------------------
            | Proteksi data yang sudah final
            |--------------------------------------------------------------------------
            | Kalau status sudah ada, non-Foreman tidak boleh mengubah data.
            */
            if (
                !is_null($pasteurisasi->status)
                && $userRole !== 'Foreman'
            ) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sudah final. Hanya Foreman yang dapat melakukan koreksi.',
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Pertahankan role lock existing
            |--------------------------------------------------------------------------
            */
            if ($userRole === 'Analis Kimia') {
                if (!is_null($pasteurisasi->disposition)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.',
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($pasteurisasi->status)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.',
                    ], 403);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Draft Existing
            |--------------------------------------------------------------------------
            */
            $existingDraft = MonitoringPasteurisasiDraft::where(
                'monitoring_pasteurisasi_id',
                $pasteurisasi->id
            )->first();

            /*
            |--------------------------------------------------------------------------
            | Simpan / Update Draft
            |--------------------------------------------------------------------------
            |
            | Satu monitoring_pasteurisasi_id hanya punya satu draft.
            */
            $draft = MonitoringPasteurisasiDraft::updateOrCreate(
                [
                    'monitoring_pasteurisasi_id' => $pasteurisasi->id,
                ],
                [
                    'brix' => $request->input('brix'),
                    'nacl' => $request->input('nacl'),
                    'bj' => $request->input('bj'),
                    'visco' => $request->input('visco'),
                    'aw' => $request->input('aw'),
                    'buih' => $request->input('buih'),
                    'ph' => $request->input('ph'),
                    'organo' => $request->input('organo'),
                    'endapan' => $request->input('endapan'),
                    'aroma' => $request->input('aroma'),

                    'status_disposition' => $request->input(
                        'status_disposition'
                    ),

                    'disposition' => $request->input(
                        'disposition'
                    ),

                    'disposition_remark' => $request->input(
                        'disposition_remark'
                    ),

                    'adjustment_qty_air' => $request->input(
                        'adjustment_qty_air'
                    ),

                    'adjustment_qty_garam' => $request->input(
                        'adjustment_qty_garam'
                    ),

                    'adjustment_qty_gula' => $request->input(
                        'adjustment_qty_gula'
                    ),

                    /*
                    | created_by tidak berubah setiap update draft.
                    */
                    'created_by' => $existingDraft?->created_by
                        ?? auth()->id(),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan sementara.',
                'data' => [
                    'draft_id' => $draft->id,
                    'monitoring_pasteurisasi_id' =>
                        $draft->monitoring_pasteurisasi_id,
                    'created_by' => $draft->created_by,
                    'created_at' => $draft->created_at,
                    'updated_at' => $draft->updated_at,
                ],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data sementara.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(MonitoringPasteurisasiUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;

            $pasteurisasi = MonitoringPasteurisasi::findOrFail($id);
            $isUpdate = !is_null($pasteurisasi->status);
            $userRole = auth()->user()->role;

            /*
            |--------------------------------------------------------------------------
            | Data final hanya boleh dikoreksi Foreman
            |--------------------------------------------------------------------------
            */
            if ($isUpdate && $userRole !== 'Foreman') {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sudah final. Hanya Foreman yang dapat melakukan koreksi.',
                ], 403);
            }

            // Validasi akses berdasarkan role
            if ($userRole === 'Analis Kimia') {
                // Analis hanya bisa input/update jika belum ada disposition
                if (!is_null($pasteurisasi->disposition)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                // Foreman hanya bisa update disposition jika sudah ada status dari Analis
                if (is_null($pasteurisasi->status)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.'
                    ], 403);
                }
            }

            $status_disposition = $request->status_disposition;
            $remark = $request->disposition_remark ?? null;

            // Validasi remarks wajib untuk status tertentu
            if (
                in_array(
                    $status_disposition,
                    ['NOT OK', 'Adjustment']
                )
                && empty($remark)
            ) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
                ], 409);
            }

            // Hitung shift otomatis
            $currentHour = (int) now()->format('H');

            if ($currentHour >= 6 && $currentHour < 14) {
                $shift = 1;
            } elseif ($currentHour >= 14 && $currentHour < 22) {
                $shift = 2;
            } else {
                $shift = 3;
            }

            // Cek apakah status berubah
            $statusChanged = (
                $pasteurisasi->status !== $status_disposition
            );

            $dispositionChanged = false;

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
                'aroma' => $request->aroma,
                'disposition_remark' => $remark,
                'status' => $status_disposition,
                'shift' => $shift,
            ];

            // PERBAIKAN: Logic berdasarkan Role
            if ($userRole === 'Analis Kimia') {
                // Analis hanya update status, disposition tetap null (menunggu Foreman)
                $updateData['disposition'] = null;

                if (!$isUpdate) {
                    $updateData['created_by'] = auth()->user()->id;
                }

                // // Validasi shift untuk Analis (hanya saat create/update status)
                // $existingShift = MonitoringPasteurisasi::where('production_batch_id', $pasteurisasi->production_batch_id)
                //     ->where('batch_range', $pasteurisasi->batch_range)
                //     ->where('shift', $shift)
                //     ->where('id', '!=', $id)
                //     ->whereNotNull('status')
                //     ->first();

                // if ($existingShift) {
                //     DB::rollBack();
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Data untuk shift ' . $shift . ' sudah ada. Silakan tunggu shift berikutnya.'
                //     ], 409);
                // }

                // // Validasi maksimal 3 shift
                // $totalShifts = MonitoringPasteurisasi::where('production_batch_id', $pasteurisasi->production_batch_id)
                //     ->where('batch_range', $pasteurisasi->batch_range)
                //     ->where('id', '!=', $id)
                //     ->whereNotNull('status')
                //     ->distinct('shift')
                //     ->count('shift');

                // if ($totalShifts >= 3) {
                //     DB::rollBack();
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Data sudah mencapai maksimal 3 shift.'
                //     ], 409);
                // }
            } elseif ($userRole === 'Foreman') {
                // Foreman wajib pilih disposition
                if (!$request->filled('disposition')) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Foreman wajib memilih disposisi.'
                    ], 409);
                }

                $disposition = $request->disposition;

                $dispositionChanged = (
                    $pasteurisasi->disposition !== $disposition
                );

                $updateData['disposition'] = $disposition;

                // // Validasi disposition untuk shift yang sama (khusus Foreman)
                // $existingDisposition = MonitoringPasteurisasi::where('production_batch_id', $pasteurisasi->production_batch_id)
                //     ->where('batch_range', $pasteurisasi->batch_range)
                //     ->where('shift', $shift)
                //     ->where('disposition', $disposition)
                //     ->where('id', '!=', $id)
                //     ->first();

                // if ($existingDisposition) {
                //     DB::rollBack();
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Data untuk shift ' . $shift . ' dengan disposisi ' . $disposition . ' sudah ada.'
                //     ], 409);
                // }
            }

            // Handle Adjustment
            $adjustmentAir = null;
            $adjustmentGaram = null;
            $adjustmentGula = null;

            if ($status_disposition === 'Adjustment') {
                if (!empty($request->adjustment_qty_air)) {
                    $adjustmentAir = str_replace(
                        ',',
                        '.',
                        $request->adjustment_qty_air
                    );
                }

                if (!empty($request->adjustment_qty_garam)) {
                    $adjustmentGaram = str_replace(
                        ',',
                        '.',
                        $request->adjustment_qty_garam
                    );
                }

                if (!empty($request->adjustment_qty_gula)) {
                    $adjustmentGula = str_replace(
                        ',',
                        '.',
                        $request->adjustment_qty_gula
                    );
                }

                $updateData['adjustment_qty_air'] = $adjustmentAir;
                $updateData['adjustment_qty_garam'] = $adjustmentGaram;
                $updateData['adjustment_qty_gula'] = $adjustmentGula;
                $updateData['not_standard'] = true;
            } else {
                // Jika status bukan Adjustment lagi, clear adjustment data
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] = null;
                    $updateData['adjustment_qty_garam'] = null;
                    $updateData['adjustment_qty_gula'] = null;
                    $updateData['not_standard'] = false;
                }
            }

            // Handle disposition khusus (hanya untuk Foreman)
            if ($userRole === 'Foreman') {
                if ($updateData['disposition'] === 'Resampling') {
                    $updateData['disposition_remark'] = $remark
                        ? $remark . ' (Resampling)'
                        : 'Resampling';

                    $updateData['not_standard'] = true;
                }

                if ($updateData['disposition'] === 'Jalan Bareng') {
                    $updateData['not_standard'] = true;
                }

                if ($updateData['disposition'] === 'Leveling') {
                    $updateData['not_standard'] = true;
                }
            }

            if ($request->filled('revisi')) {
                $updateData['revisi'] = $request->revisi;
            } else {
                $updateData['revisi'] = $pasteurisasi->revisi;
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan Final
            |--------------------------------------------------------------------------
            */
            $pasteurisasi->update($updateData);

            /*
            |--------------------------------------------------------------------------
            | Hapus Draft setelah Final
            |--------------------------------------------------------------------------
            |
            | Cek dulu draft berdasarkan monitoring_pasteurisasi_id.
            */
            $draft = MonitoringPasteurisasiDraft::where(
                'monitoring_pasteurisasi_id',
                $pasteurisasi->id
            )->first();

            if ($draft) {
                $draft->delete();
            }

            // Build remark text for API payload
            if (
                $remark !== null
                && $remark !== '-'
                && $status_disposition !== 'Adjustment'
            ) {
                $remarkText = $remark;
            } elseif ($status_disposition === 'Adjustment') {
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

            Http::post(
                env('PRODUCTION_URL')
                    . 'api/monitoring-pasteurisasi/'
                    . $pasteurisasi->id,
                [
                    'disposition' => $updateData['disposition'] ?? null,
                    'disposition_remark' => $remarkText,
                    'revisi' => $updateData['revisi'],
                    'is_adjustment' => $status_disposition === 'Adjustment',
                    'not_standard' => $updateData['not_standard'] ?? false,
                    'status' => $status_disposition,
                ]
            );

            DB::commit();

            $this->broadcastLatest();

            $shouldSendNotification = false;

            $notificationTitle = "Monitoring Pasteurisasi - Batch "
                . $pasteurisasi->batch_range;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $pasteurisasi->production_batch_id,
                    'Monitoring Pasteurisasi',
                    $status_disposition,
                    $remarkText,
                    route(
                        'analisa.monitoring-pasteurisasi.show',
                        $pasteurisasi->production_batch_id
                    )
                ));
            }

            // Pesan response berdasarkan role
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
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function broadcastLatest(): void
    {
        $items = MonitoringPasteurisasi::with([
            'productionBatch:id,po_number,variant',
            'user:id,name,email',
        ])->latest()->get()->toArray();

        broadcast(new MonitoringPasteurisasiUpdated($items));
    }
}