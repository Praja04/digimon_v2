<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Pelarutan2Request;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Pelarutan2;

class Pelarutan2Controller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('pelarutan_2')
                ->has('pelarutan_2')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $pelarutan_2 = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $pelarutan_2 = $pelarutan_2->filter(function ($batch) {
                        return $batch->isPelarutan2Complete();
                    });
                } elseif ($request->status == 'progress') {
                    $pelarutan_2 = $pelarutan_2->filter(function ($batch) {
                        return !$batch->isPelarutan2Complete();
                    });
                }
            }

            $pelarutan_2 = $pelarutan_2->sortBy(function ($batch) {
                return ($batch->isPelarutan2Complete()) ? 1 : 0;
            })->values();

            return DataTables::of($pelarutan_2)
                ->addIndexColumn()
                ->addColumn('pelarutan_2_count', function ($data) {
                    return '<span>' . $data->pelarutan_2->count() . '</span>';
                })
                ->addColumn('status_pelarutan_2', function ($data) {
                    $isComplete = $data->isPelarutan2Complete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('pelarutan-2.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['pelarutan_2_count', 'status_pelarutan_2', 'action'])
                ->make(true);
        }
        return view('app.pelarutan-2.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with('pelarutan_2')->findOrFail($id);

        return view('app.pelarutan-2.show', compact(['productionBatch']));
    }

    public function show_batch($id)
    {
        $pelarutan_2 = Pelarutan2::with('productionBatch')->findOrFail($id);
        return view('app.pelarutan-2.show_batch', compact(['pelarutan_2']));
    }

    public function edit($id)
    {
        try {
            $data = Pelarutan2::find($id);

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

    public function update(Pelarutan2Request $request)
    {
        DB::beginTransaction();
        try {
            $pelarutan_2 = Pelarutan2::findOrFail($request->id);
            $isUpdate = !is_null($pelarutan_2->status_disposition);
            $userRole = auth()->user()->role;

            // 🔒 Validasi role
            if ($userRole === 'Analis Kimia') {
                if (!is_null($pelarutan_2->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di disposisi oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($pelarutan_2->status)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum ada status dari Analis. Tidak dapat memberi disposisi.'
                    ], 403);
                }
            }

            $remark = $request->disposition_remark ?? null;
            $status_disposition = $request->status_disposition;

            // ✅ Override: jika Foreman pilih Release → status = OK
            if ($userRole === 'Foreman' && $request->disposition === 'Release') {
                $status_disposition = 'OK';
            }

            // 🔒 Validasi remark
            if (in_array($status_disposition, ['NOT OK']) && empty($remark)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (remarks) wajib diisi untuk status ini.'
                ], 409);
            }

            $updateData = [
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'visco' => $request->visco,
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
                    ], 409);
                }

                $disposition = $request->disposition;
                $updateData['disposition'] = $disposition;
            }

            // 🔁 Handle Resampling
            if ($userRole === 'Foreman' && ($updateData['disposition'] ?? null) === 'Resampling') {
                $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                $updateData['not_standard'] = true;
            }

            // 🔁 Revisi
            $updateData['revisi'] = $request->filled('revisi')
            ? $request->revisi
                : $pelarutan_2->revisi;

            $pelarutan_2->update($updateData);

            // 🧠 Build remark untuk API
            if ($remark !== null && $remark !== '-') {
                $remarkText = $remark;
            } elseif ($updateData['not_standard'] ?? false) {
                $remarkText = 'Adjustment';
            } else {
                $remarkText = '-';
            }

            $jamSelesai = ($status_disposition === 'OK')
            ? now()->format('Y-m-d H:i:s')
            : null;

            $apiPayload = [
                'disposition' => $updateData['disposition'] ?? null,
                'revisi' => $updateData['revisi'] ?? null,
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
                'disposition_remark' => $remarkText,
                'jam_selesai' => $jamSelesai,
            ];

            $client = new \GuzzleHttp\Client();
            $apiResponse = $client->request('POST', env('PRODUCTION_URL') . "api/pelarutan-2/{$pelarutan_2->id}", [
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

            // 🔔 Notifikasi
            $shouldSendNotification = false;
            $notificationTitle = "Pelarutan 2 - Batch " . $pelarutan_2->batch_number;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $pelarutan_2->production_batch_id,
                    'Pelarutan 2',
                    $status_disposition,
                    $remarkText,
                    route('pelarutan-2.show', $pelarutan_2->production_batch_id)
                ));
            }

            // 📨 Response message
            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data Pelarutan 2 berhasil diperbarui.'
                    : 'Data Pelarutan 2 berhasil disimpan.';
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

    public function formulasi(Request $request)
    {
        try {
            $pelarutan_2 = Pelarutan2::with('productionBatch:id,po_number,variant,date,batch_range')
                ->findOrFail($request->id);

            $apiUrl = url(env('PRODUCTION_URL') . 'api/formulasi/dissolver');

            $response = Http::get($apiUrl, [
                'production_batch_id' => $pelarutan_2->production_batch_id,
                'batch_number' => $pelarutan_2->batch_number,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data formulasi berhasil diambil',
                        'pelarutan_2_info' => [
                            'id' => $pelarutan_2->id,
                            'batch_number' => $pelarutan_2->batch_number,
                            'dissolver_number' => $pelarutan_2->dissolver_number,
                            'production_batch_id' => $pelarutan_2->production_batch_id,
                            'brix' => $pelarutan_2->brix,
                            'nacl' => $pelarutan_2->nacl,
                            'organo' => $pelarutan_2->organo,
                            'disposition' => $pelarutan_2->disposition,
                            'status' => $pelarutan_2->status,
                        ],
                        'production_batch' => $data['data']['production_batch'] ?? null,
                        'formulasi' => $data['data']['formulasi'] ?? [],
                        'dissolver_info' => $data['data']['dissolver_info'] ?? null,
                        'formulasi_source' => $data['data']['formulasi_source'] ?? null,
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $data['message'] ?? 'Data formulasi tidak ditemukan',
                        'pelarutan_2_info' => [
                            'id' => $pelarutan_2->id,
                            'batch_number' => $pelarutan_2->batch_number,
                            'dissolver_number' => $pelarutan_2->dissolver_number,
                            'production_batch_id' => $pelarutan_2->production_batch_id,
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
                'message' => 'Data Pelarutan 2 tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
