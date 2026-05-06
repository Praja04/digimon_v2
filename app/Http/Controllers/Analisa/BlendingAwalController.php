<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Analisa\BlendingAwalUpdateRequest;
use App\Models\BlendingAwal;
use App\Models\Color;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

            $blendingAwal = $blendingAwal->sortBy(function ($batch) {
                return ($batch->isBlendingAwalComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($blendingAwal)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return  $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return  $data->BlendingAwal->count() ?? '-';
                })
                ->addColumn('status_blending_awal', function ($data) {
                    $isComplete = $data->isBlendingAwalComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('analisa.blending-awal.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status_blending_awal', 'action'])
                ->make(true);
        }
        return view('app.analisa.blending_awal.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'BlendingAwal.additionalBatches'
        ])->findOrFail($id);

        $parseBatchRange = function ($range) {
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $range, $matches)) {
                return range((int) $matches[1], (int) $matches[2]);
            }
            return [(int) $range];
        };

        $getFirstNumber = function ($range) use ($parseBatchRange) {
            $numbers = $parseBatchRange($range);
            return !empty($numbers) ? min($numbers) : PHP_INT_MAX;
        };

        foreach ($productionBatch->BlendingAwal as $blending) {
            $blending->additional_batch_info = $blending->additionalBatches->isNotEmpty()
                ? $blending->additionalBatches
                : null;

            $blending->po_number = $productionBatch->po_number;
            $blending->sort_key = $getFirstNumber($blending->batch_range);
        }

        $productionBatch->setRelation(
            'BlendingAwal',
            $productionBatch->BlendingAwal->sortBy('sort_key')->values()
        );

        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.analisa.blending_awal.show', compact('colors', 'productionBatch'));
    }

    public function show_batch($id)
    {
        $blending = BlendingAwal::with([
            'additionalBatches',
            'productionBatch',
        ])->findOrFail($id);

        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.analisa.blending_awal.show_batch', compact('colors', 'blending'));
    }

    public function edit($id)
    {
        try {
            $data = BlendingAwal::with('color', 'user')->find($id);

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

    public function update(BlendingAwalUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;

            $blending = BlendingAwal::findOrFail($id);
            $isUpdate = !is_null($blending->status);
            $userRole = auth()->user()->role;

            // 🔒 Validasi role
            if ($userRole === 'Analis Kimia') {
                if (!is_null($blending->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($blending->status)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.'
                    ], 403);
                }
            }

            $status_disposition = $request->status_disposition;
            $remark = $request->disposition_remark ?? null;

            // ✅ OVERRIDE: Foreman pilih Release → status jadi OK
            if ($userRole === 'Foreman' && $request->disposition === 'Release') {
                $status_disposition = 'OK';
            }

            // 🔒 Validasi remark
            if (in_array($status_disposition, ['NOT OK', 'Adjustment']) && empty($remark)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
                ], 409);
            }

            $statusChanged = ($blending->status !== $status_disposition);

            $updateData = [
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'bj' => $request->bj,
                'visco' => $request->visco,
                'aw' => $request->aw,
                'ph' => $request->ph,
                'organo' => $request->organo,
                'aroma' => $request->aroma,
                'color_id' => $request->color,
                'disposition_remark' => $remark,
                'status' => $status_disposition,
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
                        'message' => 'Foreman wajib memilih disposisi.'
                    ], 409);
                }

                $disposition = $request->disposition;
                $updateData['disposition'] = $disposition;
            }

            $adjustmentAir = null;
            $adjustmentGaram = null;
            $adjustmentCaramel = null;

            if ($status_disposition === 'Adjustment') {
                if (!empty($request->adjustment_qty_air)) {
                    $adjustmentAir = str_replace(',', '.', $request->adjustment_qty_air);
                }
                if (!empty($request->adjustment_qty_garam)) {
                    $adjustmentGaram = str_replace(',', '.', $request->adjustment_qty_garam);
                }
                if (!empty($request->adjustment_qty_caramel)) {
                    $adjustmentCaramel = str_replace(',', '.', $request->adjustment_qty_caramel);
                }

                $updateData['adjustment_qty_air'] = $adjustmentAir;
                $updateData['adjustment_qty_garam'] = $adjustmentGaram;
                $updateData['adjustment_qty_caramel'] = $adjustmentCaramel;
                $updateData['not_standard'] = true;
            } else {
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] = null;
                    $updateData['adjustment_qty_garam'] = null;
                    $updateData['adjustment_qty_caramel'] = null;
                    $updateData['not_standard'] = false;
                }
            }

            // 🔁 Handle disposisi Foreman
            if ($userRole === 'Foreman') {
                if ($updateData['disposition'] === 'Resampling') {
                    $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                    $updateData['not_standard'] = true;
                }

                if (in_array($updateData['disposition'], ['Jalan Bareng', 'Leveling'])) {
                    $updateData['not_standard'] = true;
                }
            }

            // 🔁 Revisi
            $updateData['revisi'] = $request->filled('revisi')
            ? $request->revisi
                : $blending->revisi;

            $blending->update($updateData);

            // 🧠 Build remark
            if ($remark !== null && $remark !== '-' && $status_disposition !== 'Adjustment') {
                $remarkText = $remark;
            } elseif ($status_disposition === 'Adjustment') {
                $remarkText = sprintf(
                    'Adjustment Air: %s Liter, Garam: %s Kg, Caramel: %s Kg',
                    $adjustmentAir ?? 0,
                    $adjustmentGaram ?? 0,
                    $adjustmentCaramel ?? 0
                );
            } elseif ($updateData['not_standard'] ?? false) {
                $remarkText = 'Adjustment';
            } else {
                $remarkText = '-';
            }

            $jamSelesaiBlending = ($status_disposition === 'OK')
                ? now()->format('Y-m-d H:i:s')
                : null;

            Http::post(env('PRODUCTION_URL') . 'api/blending-awal/' . $blending->id, [
                'disposition' => $updateData['disposition'] ?? null,
                'disposition_remark' => $remarkText,
                'revisi' => $updateData['revisi'],
                'is_adjustment' => $status_disposition === 'Adjustment',
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
            ]);

            
            DB::commit();

            // 🔔 Notifikasi
            $shouldSendNotification = false;
            $notificationTitle = "Blending Awal - Batch " . $blending->batch_range;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $blending->production_batch_id,
                    'Blending Awal',
                    $status_disposition,
                    $remarkText,
                    route('analisa.blending-awal.show', $blending->production_batch_id)
                ));
            }

            // 📨 Response
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
                'status'  => 'success',
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function formulasi(Request $request)
    {
        try {
            // Ambil data Blending Awal berdasarkan ID
            $blendingAwal = BlendingAwal::with('productionBatch:id,po_number,variant,date,batch_range')
                ->findOrFail($request->id);

            $apiUrl = url(env('PRODUCTION_URL') . 'api/formulasi/blending-awal');

            $response = Http::get($apiUrl, [
                'blending_awal_id' => $blendingAwal->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data formulasi berhasil diambil',
                        'production_batch' => $data['data']['production_batch'] ?? null,
                        'formulasi' => $data['data']['formulasi'] ?? [],
                        'dissolver_info' => $data['data']['dissolver_info'] ?? null,
                        'formulasi_source' => $data['data']['formulasi_source'] ?? null,
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $data['message'] ?? 'Data formulasi tidak ditemukan',
                        'blending_awal_info' => [
                            'id' => $blendingAwal->id,
                            'batch_range' => $blendingAwal->batch_range,
                            'production_batch_id' => $blendingAwal->production_batch_id,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API',
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data Blending Awal tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
