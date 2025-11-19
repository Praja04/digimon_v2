<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\GgaRequest;
use App\Models\Color;
use App\Models\GGA;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

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
        $colors = Color::orderBy('name', 'asc')->get();

        return view('app.gga.show', compact(['productionBatch', 'colors']));
    }

    public function show_batch($id)
    {
        $gga = GGA::with('productionBatch')->findOrFail($id);
        $colors = Color::orderBy('name', 'asc')->get();

        return view('app.gga.show_batch', compact(['gga', 'colors']));
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
            $isUpdate = !is_null($gga->status_disposition);
            $userRole = auth()->user()->role;

            // Validasi akses untuk update
            if ($isUpdate && $userRole === 'Produksi') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengubah data yang sudah ada disposisi.'
                ], 403);
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

            // Cek apakah status_disposition berubah
            $statusChanged = ($gga->status !== $status_disposition);

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
                    $disposition = $gga->disposition;
                }
            }

            $updateData = [
                'brix' => $request->brix,
                'nacl' => $request->nacl,
                'color_id' => $request->color,
                'disposition' => $disposition,
                'disposition_remark' => $remark,
                'status' => $status_disposition,
            ];

            if (!$isUpdate) {
                $updateData['created_by'] = auth()->user()->id;
            }

            // Handle Adjustment
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

            if ($request->filled('revisi')) {
                $updateData['revisi'] = $request->revisi;
            } else {
                $updateData['revisi'] = $gga->revisi;
            }

            $gga->update($updateData);

            // Build remark text for API payload
            if ($remark !== null && $remark !== '-' && $disposition !== 'Adjustment') {
                $remarkText = $remark;
            } elseif ($disposition === 'Adjustment') {
                $remarkText = sprintf(
                    'Adjustment Air: %s Liter, Garam: %s Kg, Gula: %s Kg',
                    $request->adjustment_qty_air ?? 0,
                    $request->adjustment_qty_garam ?? 0,
                    $request->adjustment_qty_gula ?? 0
                );
            } elseif ($updateData['not_standard'] ?? false) {
                $remarkText = 'Adjustment';
            } else {
                $remarkText = '-';
            }

            // Prepare payload for external API
            $apiPayload = [
                'disposition' => $disposition,
                'revisi' => $updateData['revisi'] ?? null,
                'not_standard' => $updateData['not_standard'] ?? false,
                'status' => $status_disposition,
                'disposition_remark' => $remarkText,
                'jam_selesai_gga' => now()->format('Y-m-d H:i:s'),
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

            $message = $isUpdate
                ? 'Data GGA berhasil diperbarui.'
                : 'Data GGA berhasil disimpan.';

            // Trigger event hanya jika status berubah ke NOT OK atau Adjustment
            if ($statusChanged && in_array($status_disposition, ['NOT OK', 'Adjustment'])) {
                event(new ProcessOutsideDisposition(
                    "GGA - Batch " . $gga->batch_number,
                    $gga->production_batch_id,
                    'GGA',
                    $status_disposition,
                    $remarkText,
                ));
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
