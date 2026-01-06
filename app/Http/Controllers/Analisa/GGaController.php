<?php

namespace App\Http\Controllers\Analisa;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\GgaRequest;
use App\Models\GGA;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class GGaController extends Controller
{
    public function menu()
    {
        return view('app.GgaGgas.menu');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('gga')
                ->has('gga')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $gga = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $gga = $gga->filter(function ($batch) {
                        return $batch->isGGaComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $gga = $gga->filter(function ($batch) {
                        return !$batch->isGGaComplete();
                    });
                }
            }

            $gga = $gga->sortBy(function ($batch) {
                return ($batch->isGGaComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($gga)
                ->addIndexColumn()
                ->addColumn('gga_count', function ($data) {
                    return '<span>' . $data->gga->count() . '</span>';
                })
                ->addColumn('status_gga', function ($data) {
                    $isComplete = $data->isGGaComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('gga.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['gga_count', 'status_gga', 'action'])
                ->make(true);
        }
        return view('app.gga.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with('gga')->findOrFail($id);

        return view('app.gga.show', compact(['productionBatch']));
    }

    public function show_batch($id)
    {
        $gga = GGA::with('productionBatch')->findOrFail($id);

        return view('app.gga.show_batch', compact(['gga']));
    }

    public function edit($id)
    {
        try {
            $data = GGA::find($id);

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

    public function update(GgaRequest $request)
    {
        DB::beginTransaction();
        try {
            $gga = GGA::findOrFail($request->id);
            $isUpdate = !is_null($gga->status);
            $userRole = auth()->user()->role;

            // Validasi akses berdasarkan role
            if ($userRole === 'Analis Kimia') {
                if (!is_null($gga->disposition)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data sudah di disposisi oleh Foreman. Tidak dapat diubah.'
                    ], 403);
                }
            } elseif ($userRole === 'Foreman') {
                if (is_null($gga->status)) {
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
            $statusChanged = ($gga->status !== $status_disposition);
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
                    ], 409);
                }

                $disposition = $request->disposition;
                $dispositionChanged = ($gga->disposition !== $disposition);
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
                $updateData['revisi'] = $gga->revisi;
            }

            $gga->update($updateData);

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

            $jamSelesaiGga = ($status_disposition === 'OK')
                ? now()->format('Y-m-d H:i:s')
                : null;

            // Prepare payload for external API
            $apiPayload = [
                'disposition' => $updateData['disposition'] ?? null,
                'revisi' => $updateData['revisi'] ?? null,
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
                'disposition_remark' => $remarkText,
                'jam_selesai_gga' => $jamSelesaiGga,
            ];

            // Call external API
            $client = new \GuzzleHttp\Client();
            $apiResponse = $client->request('POST', env('PRODUCTION_URL') . "api/gga/{$gga->id}", [
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
            $notificationTitle = "GGA - Batch " . $gga->batch_number;

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
                    $gga->production_batch_id,
                    'GGA',
                    $status_disposition,
                    $remarkText,
                    route('gga.show', $gga->production_batch_id)
                ));
            }

            // Pesan response berdasarkan role
            if ($userRole === 'Analis Kimia') {
                $message = $isUpdate
                    ? 'Data GGA berhasil diperbarui.'
                    : 'Data GGA berhasil disimpan.';
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
            // Ambil data GGA berdasarkan ID
            $gga = Gga::with('productionBatch:id,po_number,variant,date,batch_range')
                ->findOrFail($request->id);

            $apiUrl = url(env('PRODUCTION_URL') . 'api/formulasi/dissolver');

            $response = Http::get($apiUrl, [
                'production_batch_id' => $gga->production_batch_id,
                'batch_number' => $gga->batch_number,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data formulasi berhasil diambil',
                        'gga_info' => [
                            'id' => $gga->id,
                            'batch_number' => $gga->batch_number,
                            'dissolver_number' => $gga->dissolver_number,
                            'production_batch_id' => $gga->production_batch_id,
                            'brix' => $gga->brix,
                            'nacl' => $gga->nacl,
                            'organo' => $gga->organo,
                            'disposition' => $gga->disposition,
                            'status' => $gga->status,
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
                        'gga_info' => [
                            'id' => $gga->id,
                            'batch_number' => $gga->batch_number,
                            'dissolver_number' => $gga->dissolver_number,
                            'production_batch_id' => $gga->production_batch_id,
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
                'message' => 'Data GGA tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
