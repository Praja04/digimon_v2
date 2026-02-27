<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Pelarutan1Request;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Pelarutan1;

class Pelarutan1Controller extends Controller
{
    public function menu()
    {
        return view('app.pelarutan_1.menu');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('pelarutan_1')
                ->has('pelarutan_1')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $pelarutan_1 = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $pelarutan_1 = $pelarutan_1->filter(function ($batch) {
                        return $batch->isPelarutan1Complete();
                    });
                } elseif ($request->status == 'progress') {
                    $pelarutan_1 = $pelarutan_1->filter(function ($batch) {
                        return !$batch->isPelarutan1Complete();
                    });
                }
            }

            $pelarutan_1 = $pelarutan_1->sortBy(function ($batch) {
                return ($batch->isPelarutan1Complete()) ? 1 : 0;
            })->values();

            return DataTables::of($pelarutan_1)
                ->addIndexColumn()
                ->addColumn('pelarutan_1_count', function ($data) {
                    return '<span>' . $data->pelarutan_1->count() . '</span>';
                })
                ->addColumn('status_pelarutan_1', function ($data) {
                    $isComplete = $data->isPelarutan1Complete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('pelarutan-1.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['pelarutan_1_count', 'status_pelarutan_1', 'action'])
                ->make(true);
        }
        return view('app.pelarutan_1.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with('pelarutan_1')->findOrFail($id);

        return view('app.pelarutan_1.show', compact(['productionBatch']));
    }

    public function show_batch($id)
    {
        $pelarutan_1 = Pelarutan1::with('productionBatch')->findOrFail($id);

        return view('app.pelarutan_1.show_batch', compact(['pelarutan_1']));
    }

    public function edit($id)
    {
        try {
            $data = Pelarutan1::find($id);

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

    public function update(Pelarutan1Request $request)
    {
        DB::beginTransaction();
        try {
            $pelarutan_1 = Pelarutan1::findOrFail($request->id);
            $isUpdate = !is_null($pelarutan_1->status);
            $userRole = auth()->user()->role;

            // Validasi akses berdasarkan role
            if ($userRole === 'Analis Kimia') {
                if (!is_null($pelarutan_1->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di disposisi oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($pelarutan_1->status)) {
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
            $statusChanged = ($pelarutan_1->status !== $status_disposition);

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
                    ], 409);
                }

                $disposition = $request->disposition;
                $updateData['disposition'] = $disposition;
            }

            // Handle Adjustment
            $adjustmentGulaTebu = null;
            $adjustmentGulaKelapa = null;

            if ($status_disposition === 'Adjustment') {
                if (!empty($request->adjustment_qty_gula_tebu)) {
                    $adjustmentGulaTebu = str_replace(',', '.', $request->adjustment_qty_gula_tebu);
                }
                if (!empty($request->adjustment_qty_gula_kelapa)) {
                    $adjustmentGulaKelapa = str_replace(',', '.', $request->adjustment_qty_gula_kelapa);
                }

                $updateData['adjustment_qty_gula_tebu'] = $adjustmentGulaTebu;
                $updateData['adjustment_qty_gula_kelapa'] = $adjustmentGulaKelapa;
                $updateData['not_standard'] = true;
            } else {
                // Jika status bukan Adjustment lagi, clear adjustment data
                if ($statusChanged) {
                    $updateData['adjustment_qty_gula_tebu'] = null;
                    $updateData['adjustment_qty_gula_kelapa'] = null;
                    $updateData['not_standard'] = false;
                }
            }

            // Handle Resampling (hanya untuk Foreman)
            if ($userRole === 'Foreman') {
                if ($updateData['disposition'] === 'Resampling') {
                    $updateData['disposition_remark'] = $remark ? $remark . ' (Resampling)' : 'Resampling';
                    $updateData['not_standard'] = true;
                }
            }

            if ($request->filled('revisi')) {
                $updateData['revisi'] = $request->revisi;
            } else {
                $updateData['revisi'] = $pelarutan_1->revisi;
            }

            $pelarutan_1->update($updateData);

            // Build remark text for API payload
            if ($remark !== null && $remark !== '-' && $status_disposition !== 'Adjustment') {
                $remarkText = $remark;
            } elseif ($status_disposition === 'Adjustment') {
                $remarkText = sprintf(
                    'Adjustment Gula Tebu: %s Kg, Gula Kelapa: %s Kg',
                    $adjustmentGulaTebu ?? 0,
                    $adjustmentGulaKelapa ?? 0
                );
            } else {
                $remarkText = '-';
            }

            $jamSelesai = ($status_disposition === 'OK')
                ? now()->format('Y-m-d H:i:s')
                : null;

            // Prepare payload for external API
            $apiPayload = [
                'disposition' => $updateData['disposition'] ?? null,
                'revisi' => $updateData['revisi'] ?? null,
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
                'disposition_remark' => $remarkText,
                'jam_selesai' => $jamSelesai,
            ];

            // Call external API
            $client = new \GuzzleHttp\Client();
            $apiResponse = $client->request('POST', env('PRODUCTION_URL') . "api/pelarutan-1/{$pelarutan_1->id}", [
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
            $notificationTitle = "Pelarutan 1 - Batch " . $pelarutan_1->batch_number;

            if ($userRole === 'Analis Kimia') {
                $shouldSendNotification = true;
                $notificationTitle .= " - Menunggu Review Foreman";
            }

            if ($shouldSendNotification) {
                event(new ProcessOutsideDisposition(
                    $notificationTitle,
                    $pelarutan_1->production_batch_id,
                    'Pelarutan 1',
                    $status_disposition,
                    $remarkText,
                    route('pelarutan-1.show', $pelarutan_1->production_batch_id)
                ));
            }

            // Pesan response berdasarkan role
            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data Pelarutan 1 berhasil diperbarui.'
                    : 'Data Pelarutan 1 berhasil disimpan.';
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
            $pelarutan_1 = Pelarutan1::with('productionBatch:id,po_number,variant,date,batch_range')
                ->findOrFail($request->id);

            $apiUrl = url(env('PRODUCTION_URL') . 'api/formulasi/dissolver');

            $response = Http::get($apiUrl, [
                'production_batch_id' => $pelarutan_1->production_batch_id,
                'batch_number' => $pelarutan_1->batch_number,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data formulasi berhasil diambil',
                        'pelarutan_1_info' => [
                            'id' => $pelarutan_1->id,
                            'batch_number' => $pelarutan_1->batch_number,
                            'dissolver_number' => $pelarutan_1->dissolver_number,
                            'production_batch_id' => $pelarutan_1->production_batch_id,
                            'brix' => $pelarutan_1->brix,
                            'nacl' => $pelarutan_1->nacl,
                            'organo' => $pelarutan_1->organo,
                            'disposition' => $pelarutan_1->disposition,
                            'status' => $pelarutan_1->status,
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
                        'pelarutan_1_info' => [
                            'id' => $pelarutan_1->id,
                            'batch_number' => $pelarutan_1->batch_number,
                            'dissolver_number' => $pelarutan_1->dissolver_number,
                            'production_batch_id' => $pelarutan_1->production_batch_id,
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
                'message' => 'Data Pelarutan 1 tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
