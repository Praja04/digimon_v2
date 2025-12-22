<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Models\MonitoringStorageMikro;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MonitoringStorageMikroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('monitoringStorageMikro')
                ->has('monitoringStorageMikro')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $monitoringStorageMikro = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $monitoringStorageMikro = $monitoringStorageMikro->filter(function ($batch) {
                        return $batch->isMonitoringStorageMikroComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $monitoringStorageMikro = $monitoringStorageMikro->filter(function ($batch) {
                        return !$batch->isMonitoringStorageMikroComplete();
                    });
                }
            }

            $monitoringStorageMikro = $monitoringStorageMikro->sortBy(function ($batch) {
                return ($batch->isMonitoringStorageMikroComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($monitoringStorageMikro)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return  $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return  $data->monitoringStorageMikro->count() ?? '-';
                })
                ->addColumn('status', function ($data) {
                    $isComplete = $data->isMonitoringStorageMikroComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('analisa.monitoring-storage-mikro.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('app.analisa.monitoring_storage_mikro.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'monitoringStorageMikro'
        ])->findOrFail($id);

        return view('app.analisa.monitoring_storage_mikro.show', compact('productionBatch'));
    }

    public function show_batch($id)
    {
        $monitoringStorageMikro = MonitoringStorageMikro::with('productionBatch')->findOrFail($id);

        return view('app.analisa.monitoring_storage_mikro.show_batch', compact('monitoringStorageMikro'));
    }

    public function getData(Request $request)
    {
        try {
            $monitoringStorageMikro = MonitoringStorageMikro::where('id', $request->id)->first();

            if (!$monitoringStorageMikro) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $monitoringStorageMikro->id,
                    'shift' => $monitoringStorageMikro->shift,
                    'nama_analis' => $monitoringStorageMikro->nama_analis,
                    'eb' => $monitoringStorageMikro->eb,
                    'tpc' => $monitoringStorageMikro->tpc,
                    'ym' => $monitoringStorageMikro->ym,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $mergeData = [];

            if ($request->filled('eb')) {
                $mergeData['eb'] = str_replace(',', '.', $request->eb);
            }

            if ($request->filled('tpc')) {
                $mergeData['tpc'] = str_replace(',', '.', $request->tpc);
            }

            if ($request->filled('ym')) {
                $mergeData['ym'] = str_replace(',', '.', $request->ym);
            }

            $request->merge($mergeData);

            $monitoringStorageMikro = MonitoringStorageMikro::find($request->id);

            if (!$monitoringStorageMikro) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $rules = [];
            $updateData = [];

            if ($request->filled('shift_analis') || $request->filled('nama_analis')) {
                $rules['shift_analis'] = 'required|integer|min:1|max:3';
                $rules['nama_analis'] = 'required|string|max:255';

                $updateData['shift'] = $request->shift_analis;
                $updateData['nama_analis'] = $request->nama_analis;
            }

            if ($request->filled('eb')) {
                if (empty($monitoringStorageMikro->shift) || empty($monitoringStorageMikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input EB.'
                    ], 409);
                }
                $rules['eb'] = 'required|numeric|min:0';
                $updateData['eb'] = $request->eb;
            }

            if ($request->filled('tpc')) {
                if (empty($monitoringStorageMikro->shift) || empty($monitoringStorageMikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input TPC.'
                    ], 409);
                }
                $rules['tpc'] = 'required|numeric|min:0';
                $updateData['tpc'] = $request->tpc;
            }

            if ($request->filled('ym')) {
                if (empty($monitoringStorageMikro->shift) || empty($monitoringStorageMikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input YM.'
                    ], 409);
                }
                $rules['ym'] = 'required|numeric|min:0';
                $updateData['ym'] = $request->ym;
            }

            // ✅ Validasi hanya field yang ada di rules
            if (!empty($rules)) {
                $validator = Validator::make($request->only(array_keys($rules)), $rules, [
                    'shift_analis.required' => 'Shift wajib diisi.',
                    'shift_analis.integer' => 'Shift harus berupa angka.',
                    'shift_analis.min' => 'Shift minimal 1.',
                    'shift_analis.max' => 'Shift maksimal 3.',
                    'nama_analis.required' => 'Nama Analis wajib diisi.',
                    'nama_analis.string' => 'Nama Analis harus berupa teks.',
                    'nama_analis.max' => 'Nama Analis maksimal 255 karakter.',
                    'eb.required' => 'EB wajib diisi.',
                    'eb.numeric' => 'EB harus berupa angka.',
                    'eb.min' => 'EB tidak boleh negatif.',
                    'tpc.required' => 'TPC wajib diisi.',
                    'tpc.numeric' => 'TPC harus berupa angka.',
                    'tpc.min' => 'TPC tidak boleh negatif.',
                    'ym.required' => 'YM wajib diisi.',
                    'ym.numeric' => 'YM harus berupa angka.',
                    'ym.min' => 'YM tidak boleh negatif.',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 409);
                }
            }

            // Cek apakah ada data yang akan diupdate
            if (empty($updateData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang diinput.'
                ], 409);
            }

            // Update data
            $monitoringStorageMikro->update($updateData);

            // ✅ Refresh data dari database
            $monitoringStorageMikro->refresh();

            // ✅ Tentukan hasil berdasarkan kelengkapan dan kriteria
            $hasil = 'PENDING';
            $shouldCallApi = false; // Flag untuk menentukan kapan panggil API

            if ($monitoringStorageMikro->eb !== null && $monitoringStorageMikro->tpc !== null && $monitoringStorageMikro->ym !== null) {
                // ✅ EB = 0, TPC = 30, YM = 0 → OK
                if ($monitoringStorageMikro->eb == 0 && $monitoringStorageMikro->tpc == 30 && $monitoringStorageMikro->ym == 0) {
                    $hasil = 'OK';
                } else {
                    $hasil = 'NOT OK';
                }

                // Hanya panggil API jika hasil sudah final (OK atau NOT OK)
                $shouldCallApi = true;
            }

            // Update hasil
            $monitoringStorageMikro->update(['hasil' => $hasil]);

            // Panggil API hanya jika hasil sudah final (bukan PENDING)
            if ($shouldCallApi) {
                try {
                    Http::post(env('PRODUCTION_URL') . 'api/monitoring-storage-mikro', [
                        'id' => $monitoringStorageMikro->id,
                        'production_batch_id' => $monitoringStorageMikro->production_batch_id,
                        'batch_range' => $monitoringStorageMikro->batch_range,
                        'nomor_blending' => $monitoringStorageMikro->nomor_blending,
                        'volume' => $monitoringStorageMikro->volume,
                        'hasil' => $hasil,
                        'storage' => $monitoringStorageMikro->storage,
                    ]);
                } catch (\Exception $apiException) {
                    Log::error('API Monitoring Storage Mikro Error: ' . $apiException->getMessage());
                }
            }

            // Tentukan nama field untuk pesan
            $fieldName = '';
            if (isset($updateData['eb'])) $fieldName = 'EB';
            if (isset($updateData['tpc'])) $fieldName = 'TPC';
            if (isset($updateData['ym'])) $fieldName = 'YM';

            $message = "Data {$fieldName} berhasil disimpan.";

            if ($hasil !== 'PENDING') {
                if ($hasil === 'OK') {
                    $message .= " ✅ Status: LOLOS (OK)";
                } else {
                    $message .= " ❌ Status: TIDAK LOLOS (NOT OK)";
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'hasil' => $hasil,
                'data' => [
                    'eb' => $monitoringStorageMikro->eb,
                    'tpc' => $monitoringStorageMikro->tpc,
                    'ym' => $monitoringStorageMikro->ym,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
