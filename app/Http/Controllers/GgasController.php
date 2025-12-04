<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\GgasRequest;
use App\Models\Color;
use App\Models\GGAS;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GgasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('ggas')
                ->has('ggas')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $ggas = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $ggas = $ggas->filter(function ($batch) {
                        return $batch->isGGasComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $ggas = $ggas->filter(function ($batch) {
                        return !$batch->isGGasComplete();
                    });
                }
            }

            $ggas = $ggas->sortBy(function ($batch) {
                return ($batch->isGGasComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($ggas)
                ->addIndexColumn()
                ->addColumn('ggas_count', function ($data) {
                    return '<span>' . $data->ggas->count() . '</span>';
                })
                ->addColumn('status_ggas', function ($data) {
                    $isComplete = $data->isGGasComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('ggas.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['ggas_count', 'status_ggas', 'action'])
                ->make(true);
        }
        return view('app.ggas.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with('ggas')->findOrFail($id);

        return view('app.ggas.show', compact(['productionBatch']));
    }

    public function show_batch($id)
    {
        $ggas = GGAS::with('productionBatch')->findOrFail($id);
        return view('app.ggas.show_batch', compact(['ggas']));
    }

    public function edit($id)
    {
        try {
            $data = GGAS::find($id);

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

    public function update(GgasRequest $request)
    {
        DB::beginTransaction();
        try {
            $ggas = GGAS::findOrFail($request->id);
            $isUpdate = !is_null($ggas->status_disposition);
            $userRole = auth()->user()->role;

            if ($userRole === 'Analis Kimia') {
                if (!is_null($ggas->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di disposisi oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($ggas->status)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.'
                    ], 403);
                }
            }

            $status_disposition = $request->status_disposition;
            $remark = $request->disposition_remark ?? null;

            if (in_array($status_disposition, ['NOT OK']) && empty($remark)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
                ], 409);
            }

            $status_disposition = $request->status_disposition;
            $dispositionChanged = false;

            $updateData = [
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'organo' => $request->organo,
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
                    ], 422);
                }

                $disposition = $request->disposition;
                $dispositionChanged = ($ggas->disposition !== $disposition);
                $updateData['disposition'] = $disposition;
            }

            // Handle Resampling (hanya untuk Foreman)
            if ($userRole === 'Foreman') {
                if ($updateData['disposition'] === 'Resampling') {
                    $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                    $updateData['not_standard'] = true;
                } elseif ($updateData['disposition'] === 'Release Bersyarat') {
                    $updateData['status'] = 'OK';
                }
            }

            if ($request->filled('revisi')) {
                $updateData['revisi'] = $request->revisi;
            } else {
                $updateData['revisi'] = $ggas->revisi;
            }

            $ggas->update($updateData);

            // Build remark text for API payload
            if ($remark !== null && $remark !== '-') {
                $remarkText = $remark;
            } elseif ($updateData['not_standard'] ?? false) {
                $remarkText = 'Adjustment';
            } else {
                $remarkText = '-';
            }

            $jamSelesaiGgas = ($status_disposition === 'OK')
                ? now()->format('Y-m-d H:i:s')
                : null;

            $apiPayload = [
                'disposition' => $updateData['disposition'] ?? null,
                'revisi' => $updateData['revisi'] ?? null,
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
                'disposition_remark' => $remarkText,
                'jam_selesai_ggas' => $jamSelesaiGgas,
            ];

            $client = new \GuzzleHttp\Client();
            $apiResponse = $client->request('POST', env('PRODUCTION_URL') . "api/ggas/{$ggas->id}", [
                'json' => $apiPayload,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            if ($apiResponse->getStatusCode() !== 200) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal update data ke API eksternal.',
                ], 500);
            }

            DB::commit();

            // Kirim notifikasi berdasarkan kondisi
            $shouldSendNotification = false;
            $notificationTitle = "GGAS - Batch " . $ggas->batch_number;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            } elseif ($userRole === 'Foreman' && $dispositionChanged) {
                $shouldSendNotification = true;
                $notificationTitle .= " - Disposition: " . ($updateData['disposition'] ?? '-');
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $ggas->production_batch_id,
                    'GGAS',
                    $status_disposition,
                    $remarkText,
                    route('ggas.show', $ggas->production_batch_id)
                ));
            }

            // Pesan response berdasarkan role
            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data GGAS berhasil diperbarui.'
                    : 'Data GGAS berhasil disimpan.';
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
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
