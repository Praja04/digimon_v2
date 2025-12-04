<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Controllers\Controller;
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

        // Loop setiap blending_awal untuk cek apakah ada additional batch
        foreach ($productionBatch->BlendingAwal as $blending) {
            // Tambahkan properti custom 'additional_batch_info' ke setiap data
            $blending->additional_batch_info = $blending->additionalBatches->isNotEmpty()
                ? $blending->additionalBatches
                : null;

            $blending->po_number = $productionBatch->po_number;
        }

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

            // Validasi akses berdasarkan role
            if ($userRole === 'Analis Kimia') {
                // Analis hanya bisa input/update jika belum ada disposition
                if (!is_null($blending->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di-dispose oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                // Foreman hanya bisa update disposition jika sudah ada status dari Analis
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

            // Validasi remarks wajib untuk status tertentu
            if (in_array($status_disposition, ['NOT OK', 'Adjustment']) && empty($remark)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
                ], 409);
            }

            // Cek apakah status berubah
            $statusChanged = ($blending->status !== $status_disposition);
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
                'color_id' => $request->color,
                'disposition_remark' => $remark,
                'status' => $status_disposition,
            ];

            // PERBAIKAN: Logic berdasarkan Role
            if ($userRole === 'Analis Kimia') {
                // Analis hanya update status, disposition tetap null (menunggu Foreman)
                $updateData['disposition'] = null;

                if (!$isUpdate) {
                    $updateData['created_by'] = auth()->user()->id;
                }
            } elseif ($userRole === 'Foreman') {
                // Foreman wajib pilih disposition
                if (!$request->filled('disposition')) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Foreman wajib memilih disposisi.'
                    ], 422);
                }

                $disposition = $request->disposition;
                $dispositionChanged = ($blending->disposition !== $disposition);
                $updateData['disposition'] = $disposition;
            }

            // Handle Adjustment
            $adjustmentAir = null;
            $adjustmentGaram = null;
            $adjustmentGula = null;

            if ($status_disposition === 'Adjustment') {
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
                // Jika status bukan Adjustment lagi, clear adjustment data
                if ($statusChanged) {
                    $updateData['adjustment_qty_air'] = null;
                    $updateData['adjustment_qty_garam'] = null;
                    $updateData['adjustment_qty_gula'] = null;
                    $updateData['not_standard'] = false;
                }
            }

            // Handle Resampling (hanya untuk Foreman)
            if ($userRole === 'Foreman') {
                if ($updateData['disposition'] === 'Resampling') {
                    $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                    $updateData['not_standard'] = true;
                } elseif ($updateData['disposition'] === 'Release Bersyarat') {
                    $updateData['status'] = 'OK';
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
                $updateData['revisi'] = $blending->revisi;
            }

            $blending->update($updateData);

            // Build remark text for API payload
            if ($remark !== null && $remark !== '-' && $status_disposition !== 'Adjustment') {
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

            // PERBAIKAN: Kirim notifikasi berdasarkan kondisi
            $shouldSendNotification = false;
            $notificationTitle = "Blending Awal - Batch " . $blending->batch_range;

            if ($userRole === 'Analis Kimia') {
                // Analis input/update status - kirim notif ke Foreman untuk review
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            } elseif ($userRole === 'Foreman' && $dispositionChanged) {
                // Foreman memberi disposition - kirim notif final ke semua
                $shouldSendNotification = true;
                $notificationTitle .= " - Disposition: " . ($updateData['disposition'] ?? '-');
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

            // Pesan response berdasarkan role
            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data berhasil diperbarui. Menunggu review dari Foreman.'
                    : 'Data berhasil disimpan. Menunggu review dari Foreman.';
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
}
