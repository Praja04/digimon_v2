<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Analisa\BlendingAwalUpdateRequest;
use App\Models\BlendingAwal;
use App\Models\BlendingAwalDraft;
use App\Models\BlendingAwalForemanDraft;
use App\Models\Color;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BlendingAwalController extends Controller
{
    public function menu()
    {
        return view('app.blending_awal.menu');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('BlendingAwal')
                ->has('BlendingAwal')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $blendingAwal = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $blendingAwal = $blendingAwal->filter(function ($batch) {
                        return $batch->isBlendingAwalComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $blendingAwal = $blendingAwal->filter(function ($batch) {
                        return !$batch->isBlendingAwalComplete();
                    });
                }
            }

            $blendingAwal = $blendingAwal
                ->sortBy(function ($batch) {
                    return $batch->isBlendingAwalComplete() ? 1 : 0;
                })
                ->values();

            return DataTables::of($blendingAwal)
                ->addIndexColumn()

                ->addColumn('description', function ($data) {
                    return $data->description ?? '-';
                })

                ->addColumn('blending_count', function ($data) {
                    return $data->BlendingAwal->count() ?? '-';
                })

                ->addColumn('status_blending_awal', function ($data) {
                    $isComplete = $data->isBlendingAwalComplete();

                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })

                ->addColumn('action', function ($data) {
                    $showUrl = route(
                        'analisa.blending-awal.show',
                        ['id' => $data->id]
                    );

                    return '
                        <a href="' . $showUrl . '"
                            class="btn btn-sm btn-primary"
                            title="Lihat Detail">
                            <i class="mdi mdi-eye"></i> Lihat
                        </a>
                    ';
                })

                ->rawColumns([
                    'status_blending_awal',
                    'action',
                ])

                ->make(true);
        }

        return view('app.analisa.blending_awal.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'BlendingAwal.additionalBatches',
        ])->findOrFail($id);

        $parseBatchRange = function ($range) {
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $range, $matches)) {
                return range(
                    (int) $matches[1],
                    (int) $matches[2]
                );
            }

            return [(int) $range];
        };

        $getFirstNumber = function ($range) use ($parseBatchRange) {
            $numbers = $parseBatchRange($range);

            return !empty($numbers)
                ? min($numbers)
                : PHP_INT_MAX;
        };

        foreach ($productionBatch->BlendingAwal as $blending) {
            $blending->additional_batch_info =
                $blending->additionalBatches->isNotEmpty()
                    ? $blending->additionalBatches
                    : null;

            $blending->po_number =
                $productionBatch->po_number;

            $blending->sort_key =
                $getFirstNumber($blending->batch_range);
        }

        $productionBatch->setRelation(
            'BlendingAwal',
            $productionBatch->BlendingAwal
                ->sortBy('sort_key')
                ->values()
        );

        $colors = Color::orderBy('name', 'asc')->get();

        return view(
            'app.analisa.blending_awal.show',
            compact(
                'colors',
                'productionBatch'
            )
        );
    }

    public function show_batch($id)
    {
        $blending = BlendingAwal::with([
            'additionalBatches',
            'productionBatch',
        ])->findOrFail($id);

        $colors = Color::orderBy('name', 'asc')->get();

        /*
        |--------------------------------------------------------------------------
        | Draft Analis Kimia
        |--------------------------------------------------------------------------
        */
        $draft = null;

        if (
            auth()->check() &&
            auth()->user()->role === 'Analis Kimia'
        ) {
            $draft = BlendingAwalDraft::where(
                'blending_awal_id',
                $blending->id
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Draft Foreman
        |--------------------------------------------------------------------------
        */
        $foremanDraft = null;

        if (
            auth()->check() &&
            auth()->user()->role === 'Foreman'
        ) {
            $foremanDraft = BlendingAwalForemanDraft::where(
                'blending_awal_id',
                $blending->id
            )->first();
        }

        return view(
            'app.analisa.blending_awal.show_batch',
            compact(
                'colors',
                'blending',
                'draft',
                'foremanDraft'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN SEMENTARA ANALIS KIMIA
    |--------------------------------------------------------------------------
    */
    public function saveDraft(Request $request)
    {
        if (auth()->user()->role !== 'Analis Kimia') {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Simpan sementara hanya dapat dilakukan oleh Analis Kimia.',
            ], 403);
        }

        $data = $request->all();

        $numericFields = [
            'brix',
            'nacl',
            'bj',
            'visco',
            'aw',
            'ph',
            'adjustment_qty_air',
            'adjustment_qty_garam',
            'adjustment_qty_caramel',
        ];

        foreach ($numericFields as $field) {
            if (
                array_key_exists($field, $data) &&
                is_string($data[$field])
            ) {
                $value = str_replace(
                    ' ',
                    '',
                    $data[$field]
                );

                $value = str_replace(
                    ',',
                    '.',
                    $value
                );

                $data[$field] =
                    $value === ''
                        ? null
                        : $value;
            }
        }

        $validator = Validator::make(
            $data,
            [
                'id' =>
                    'required|exists:blending_awal,id',

                'brix' =>
                    'nullable|numeric|min:0|max:100',

                'nacl' =>
                    'nullable|numeric|min:0|max:100',

                'bj' =>
                    'nullable|numeric',

                'visco' =>
                    'nullable|numeric',

                'aw' =>
                    'nullable|numeric',

                'ph' =>
                    'nullable|numeric',

                'organo' =>
                    'nullable|string|max:255',

                'aroma' =>
                    'nullable|string|max:255',

                'color' =>
                    'nullable|exists:colors,id',

                'status_disposition' =>
                    'nullable|in:OK,NOT OK,Adjustment',

                'disposition_remark' =>
                    'nullable|string|max:1000',

                'adjustment_qty_air' =>
                    'nullable|numeric',

                'adjustment_qty_garam' =>
                    'nullable|numeric',

                'adjustment_qty_caramel' =>
                    'nullable|numeric',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data draft tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $blending = BlendingAwal::findOrFail(
            $data['id']
        );

        /*
         * Analis hanya boleh draft
         * selama belum Simpan Final.
         */
        if (!is_null($blending->status)) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Data sudah disimpan final dan tidak dapat disimpan sebagai draft.',
            ], 409);
        }

        $draft = BlendingAwalDraft::updateOrCreate(
            [
                'blending_awal_id' =>
                    $blending->id,
            ],
            [
                'brix' =>
                    $data['brix'] ?? null,

                'nacl' =>
                    $data['nacl'] ?? null,

                'bj' =>
                    $data['bj'] ?? null,

                'visco' =>
                    $data['visco'] ?? null,

                'aw' =>
                    $data['aw'] ?? null,

                'ph' =>
                    $data['ph'] ?? null,

                'organo' =>
                    $data['organo'] ?? null,

                'aroma' =>
                    $data['aroma'] ?? null,

                'color_id' =>
                    $data['color'] ?? null,

                'status_disposition' =>
                    $data['status_disposition'] ?? null,

                'disposition_remark' =>
                    $data['disposition_remark'] ?? null,

                'adjustment_qty_air' =>
                    $data['adjustment_qty_air'] ?? null,

                'adjustment_qty_garam' =>
                    $data['adjustment_qty_garam'] ?? null,

                'adjustment_qty_caramel' =>
                    $data['adjustment_qty_caramel'] ?? null,

                'created_by' =>
                    auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' =>
                'Data berhasil disimpan sementara.',
            'draft_id' =>
                $draft->id,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN SEMENTARA FOREMAN
    |--------------------------------------------------------------------------
    */
    public function saveForemanDraft(Request $request)
    {
        if (auth()->user()->role !== 'Foreman') {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Simpan sementara hanya dapat dilakukan oleh Foreman.',
            ], 403);
        }

        $data = $request->all();

        $numericFields = [
            'adjustment_qty_air',
            'adjustment_qty_garam',
            'adjustment_qty_caramel',
        ];

        foreach ($numericFields as $field) {
            if (
                array_key_exists($field, $data) &&
                is_string($data[$field])
            ) {
                $value = str_replace(
                    ' ',
                    '',
                    $data[$field]
                );

                $value = str_replace(
                    ',',
                    '.',
                    $value
                );

                $data[$field] =
                    $value === ''
                        ? null
                        : $value;
            }
        }

        $validator = Validator::make(
            $data,
            [
                'id' =>
                    'required|exists:blending_awal,id',

                'disposition' =>
                    'nullable|in:Release,Release Bersyarat,Resampling,Adjustment,Reject,Repro,Jalan Bareng,Leveling',

                'disposition_remark' =>
                    'nullable|string|max:1000',

                'adjustment_qty_air' =>
                    'nullable|numeric',

                'adjustment_qty_garam' =>
                    'nullable|numeric',

                'adjustment_qty_caramel' =>
                    'nullable|numeric',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Data draft Foreman tidak valid.',
                'errors' =>
                    $validator->errors(),
            ], 422);
        }

        $blending = BlendingAwal::findOrFail(
            $data['id']
        );

        /*
         * Foreman baru boleh draft
         * setelah Analis Simpan Final.
         */
        if (is_null($blending->status)) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Data belum difinalkan oleh Analis Kimia.',
            ], 409);
        }

        /*
         * Kalau sudah punya disposisi final,
         * draft tidak boleh dibuat lagi.
         */
        if (!is_null($blending->disposition)) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Data sudah memiliki disposisi final.',
            ], 409);
        }

        /*
         * Satu Blending Awal
         * satu draft Foreman.
         */
        $draft = BlendingAwalForemanDraft::updateOrCreate(
            [
                'blending_awal_id' =>
                    $blending->id,
            ],
            [
                'disposition' =>
                    $data['disposition'] ?? null,

                'disposition_remark' =>
                    $data['disposition_remark'] ?? null,

                'adjustment_qty_air' =>
                    $data['adjustment_qty_air'] ?? null,

                'adjustment_qty_garam' =>
                    $data['adjustment_qty_garam'] ?? null,

                'adjustment_qty_caramel' =>
                    $data['adjustment_qty_caramel'] ?? null,

                'created_by' =>
                    auth()->id(),
            ]
        );

        return response()->json([
            'status' =>
                'success',

            'message' =>
                'Data Foreman berhasil disimpan sementara.',

            'draft_id' =>
                $draft->id,
        ], 200);
    }

    public function edit($id)
    {
        try {
            $data = BlendingAwal::with(
                'color',
                'user'
            )->find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            if (
                auth()->check() &&
                auth()->user()->role === 'Foreman'
            ) {
                $foremanDraft =
                    BlendingAwalForemanDraft::where(
                        'blending_awal_id',
                        $data->id
                    )->first();

                $data->setAttribute(
                    'foreman_draft',
                    $foremanDraft
                );
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Terjadi kesalahan, silakan coba lagi.',
                'error' =>
                    $e->getMessage(),
            ], 500);
        }
    }

    public function update(
        BlendingAwalUpdateRequest $request
    ) {
        DB::beginTransaction();

        try {
            $id = $request->id;

            $blending =
                BlendingAwal::findOrFail($id);

            $isUpdate =
                !is_null($blending->status);

            $userRole =
                auth()->user()->role;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI ROLE
            |--------------------------------------------------------------------------
            */
            if ($userRole === 'Analis Kimia') {

                if (!is_null($blending->disposition)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' =>
                            'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.',
                    ], 403);
                }

            } elseif ($userRole === 'Foreman') {

                if (is_null($blending->status)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' =>
                            'Belum ada status dari Analis. Tidak dapat memberi disposisi.',
                    ], 403);
                }
            }

            $status_disposition =
                $request->status_disposition;

            $remark =
                $request->disposition_remark
                ?? null;

            /*
             * Release oleh Foreman
             * memaksa status menjadi OK.
             */
            if (
                $userRole === 'Foreman' &&
                $request->disposition === 'Release'
            ) {
                $status_disposition = 'OK';
            }

            /*
             * NOT OK / Adjustment
             * wajib catatan.
             */
            if (
                in_array(
                    $status_disposition,
                    [
                        'NOT OK',
                        'Adjustment',
                    ]
                ) &&
                empty($remark)
            ) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'Kolom keterangan (remarks) wajib diisi untuk status ini.',
                ], 409);
            }

            $statusChanged =
                $blending->status
                !== $status_disposition;

            $updateData = [
                'brix' =>
                    $request->brix,

                'nacl' =>
                    $request->nacl,

                'bj' =>
                    $request->bj,

                'visco' =>
                    $request->visco,

                'aw' =>
                    $request->aw,

                'ph' =>
                    $request->ph,

                'organo' =>
                    $request->organo,

                'aroma' =>
                    $request->aroma,

                'color_id' =>
                    $request->color,

                'disposition_remark' =>
                    $remark,

                'status' =>
                    $status_disposition,
            ];

            if ($userRole === 'Analis Kimia') {

                $updateData['disposition'] =
                    null;

                if (!$isUpdate) {
                    $updateData['created_by'] =
                        auth()->id();
                }

            } elseif ($userRole === 'Foreman') {

                if (!$request->filled('disposition')) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' =>
                            'Foreman wajib memilih disposisi.',
                    ], 409);
                }

                $updateData['disposition'] =
                    $request->disposition;
            }

            $adjustmentAir = null;
            $adjustmentGaram = null;
            $adjustmentCaramel = null;

            /*
            |--------------------------------------------------------------------------
            | ADJUSTMENT
            |--------------------------------------------------------------------------
            */
            if (
                $status_disposition === 'Adjustment' ||
                (
                    $userRole === 'Foreman' &&
                    $request->disposition === 'Adjustment'
                )
            ) {
                if (!empty($request->adjustment_qty_air)) {
                    $adjustmentAir =
                        str_replace(
                            ',',
                            '.',
                            $request->adjustment_qty_air
                        );
                }

                if (!empty($request->adjustment_qty_garam)) {
                    $adjustmentGaram =
                        str_replace(
                            ',',
                            '.',
                            $request->adjustment_qty_garam
                        );
                }

                if (!empty($request->adjustment_qty_caramel)) {
                    $adjustmentCaramel =
                        str_replace(
                            ',',
                            '.',
                            $request->adjustment_qty_caramel
                        );
                }

                $updateData['adjustment_qty_air'] =
                    $adjustmentAir;

                $updateData['adjustment_qty_garam'] =
                    $adjustmentGaram;

                $updateData['adjustment_qty_caramel'] =
                    $adjustmentCaramel;

                $updateData['not_standard'] =
                    true;

            } else {
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] =
                        null;

                    $updateData['adjustment_qty_garam'] =
                        null;

                    $updateData['adjustment_qty_caramel'] =
                        null;

                    $updateData['not_standard'] =
                        false;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DISPOSISI FOREMAN
            |--------------------------------------------------------------------------
            */
            if ($userRole === 'Foreman') {

                if (
                    $updateData['disposition']
                    === 'Resampling'
                ) {
                    $updateData['disposition_remark'] =
                        $remark
                            ? $remark . ' (Resampling)'
                            : 'Resampling';

                    $updateData['not_standard'] =
                        true;
                }

                if (
                    in_array(
                        $updateData['disposition'],
                        [
                            'Jalan Bareng',
                            'Leveling',
                        ]
                    )
                ) {
                    $updateData['not_standard'] =
                        true;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | REVISI
            |--------------------------------------------------------------------------
            */
            $updateData['revisi'] =
                $request->filled('revisi')
                    ? $request->revisi
                    : $blending->revisi;

            $blending->update($updateData);

            /*
            |--------------------------------------------------------------------------
            | BUILD REMARK
            |--------------------------------------------------------------------------
            */
            if (
                $remark !== null &&
                $remark !== '-' &&
                $status_disposition !== 'Adjustment'
            ) {
                $remarkText =
                    $remark;

            } elseif (
                $status_disposition === 'Adjustment' ||
                (
                    $userRole === 'Foreman' &&
                    $request->disposition === 'Adjustment'
                )
            ) {
                $remarkText = sprintf(
                    'Adjustment Air: %s Liter, Garam: %s Kg, Caramel: %s Kg',
                    $adjustmentAir ?? 0,
                    $adjustmentGaram ?? 0,
                    $adjustmentCaramel ?? 0
                );

            } elseif (
                $updateData['not_standard']
                ?? false
            ) {
                $remarkText =
                    'Adjustment';

            } else {
                $remarkText =
                    '-';
            }

            /*
            |--------------------------------------------------------------------------
            | SYNC EXISTING
            |--------------------------------------------------------------------------
            */
            Http::post(
                env('PRODUCTION_URL')
                    . 'api/blending-awal/'
                    . $blending->id,
                [
                    'disposition' =>
                        $updateData['disposition']
                        ?? null,

                    'disposition_remark' =>
                        $remarkText,

                    'revisi' =>
                        $updateData['revisi'],

                    'is_adjustment' =>
                        $status_disposition
                        === 'Adjustment',

                    'not_standard' =>
                        $updateData['not_standard']
                        ?? false,

                    'status' =>
                        $status_disposition,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | HAPUS DRAFT SETELAH FINAL
            |--------------------------------------------------------------------------
            */
            if ($userRole === 'Analis Kimia') {
                BlendingAwalDraft::where(
                    'blending_awal_id',
                    $blending->id
                )->delete();
            }

            if ($userRole === 'Foreman') {
                BlendingAwalForemanDraft::where(
                    'blending_awal_id',
                    $blending->id
                )->delete();
            }

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */
            $shouldSendNotification =
                false;

            $notificationTitle =
                'Blending Awal - Batch '
                . $blending->batch_range;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification =
                    true;

                $notificationTitle .=
                    ' - Menunggu Review Foreman';
            }

            if ($shouldSendNotification) {
                event(
                    new ProcessOutsideDisposition(
                        $notificationTitle,
                        $blending->production_batch_id,
                        'Blending Awal',
                        $status_disposition,
                        $remarkText,
                        route(
                            'analisa.blending-awal.show',
                            $blending->production_batch_id
                        )
                    )
                );
            }

            if ($userRole === 'Analis Kimia') {
                $message =
                    $isUpdate
                        ? 'Data berhasil diperbarui.'
                        : 'Data berhasil disimpan.';

            } elseif ($userRole === 'Foreman') {
                $message =
                    'Disposisi berhasil diberikan.';

            } else {
                $message =
                    'Data berhasil disimpan.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' =>
                    'Terjadi kesalahan, silakan coba lagi.',
                'error' =>
                    $e->getMessage(),
            ], 500);
        }
    }

    public function formulasi(Request $request)
    {
        try {
            $blendingAwal =
                BlendingAwal::with(
                    'productionBatch:id,po_number,variant,date,batch_range'
                )->findOrFail(
                    $request->id
                );

            $apiUrl =
                url(
                    env('PRODUCTION_URL')
                    . 'api/formulasi/blending-awal'
                );

            $response =
                Http::get(
                    $apiUrl,
                    [
                        'blending_awal_id' =>
                            $blendingAwal->id,
                    ]
                );

            if ($response->successful()) {
                $data =
                    $response->json();

                if ($data['success']) {
                    return response()->json([
                        'success' =>
                            true,

                        'message' =>
                            'Data formulasi berhasil diambil',

                        'production_batch' =>
                            $data['data']['production_batch']
                            ?? null,

                        'formulasi' =>
                            $data['data']['formulasi']
                            ?? [],

                        'dissolver_info' =>
                            $data['data']['dissolver_info']
                            ?? null,

                        'formulasi_source' =>
                            $data['data']['formulasi_source']
                            ?? null,

                    ], 200);
                }

                return response()->json([
                    'success' =>
                        false,

                    'message' =>
                        $data['message']
                        ?? 'Data formulasi tidak ditemukan',

                    'blending_awal_info' => [
                        'id' =>
                            $blendingAwal->id,

                        'batch_range' =>
                            $blendingAwal->batch_range,

                        'production_batch_id' =>
                            $blendingAwal->production_batch_id,
                    ],

                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' =>
                    'Gagal mengambil data dari API',
            ], 500);

        } catch (
            \Illuminate\Database\Eloquent\ModelNotFoundException $e
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Data Blending Awal tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Terjadi kesalahan: '
                    . $e->getMessage(),
            ], 500);
        }
    }
}