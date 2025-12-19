<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\MonitoringOnGoingMikroRequest;
use App\Models\Color;
use App\Models\MonitoringDailyTank;
use App\Models\MonitoringOnGoingMikro;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\Facades\DNS2DFacade;
use Yajra\DataTables\DataTables;

class MonitoringOnGoingMikroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $monitoringOnGoings = MonitoringOnGoingMikro::with('productionBatch')
                ->orderByRaw('hasil IS NULL DESC')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($monitoringOnGoings)
                ->addIndexColumn()
                ->addColumn('po_number', function ($data) {
                    return $data->productionBatch->po_number ?? '-';
                })
                ->editColumn('filling_date', function ($data) {
                    if (!$data->filling_date) {
                        return '-';
                    }
                    return \Carbon\Carbon::parse($data->filling_date)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('detail', function ($data) {
                    return '
                        <button class="btn btn-sm btn-info btn-detail" data-id="' . $data->id . '" title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    ';
                })
                ->addColumn('analisa', function ($data) {
                    return '
                        <a class="btn btn-sm btn-primary me-1" id="btnAnalisa" href="' . route('monitoring-ongoing-mikro.analisa', $data->id) . '">
                           <span class="mdi mdi-test-tube"></span>
                        </a>
                        ';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                           <span class="mdi mdi-pencil"></span> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                            <span class="mdi mdi-trash-can"></span> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['action', 'detail', 'analisa'])
                ->make(true);
        }

        $passedPoNumbers = ProductionBatch::whereHas('monitoringPasteurisasi', function ($q) {
            $q->whereIn('disposition', ['Release', 'Release Bersyarat']);
        })
            ->get(['id', 'po_number'])
            ->mapWithKeys(fn($item) => [$item->id => $item->po_number])
            ->unique()
            ->filter();

        return view('app.monitoring_on_going_mikro.index', compact('passedPoNumbers'));
    }

    public function edit($id)
    {
        try {
            $data = MonitoringOnGoingMikro::where('id', $id)->first();

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

    public function store(MonitoringOnGoingMikroRequest $request)
    {
        try {
            $isKempuOrJeriken = stripos($request->variant, 'kempu') !== false ||
                stripos($request->variant, 'jeriken') !== false;

            $noKempuJeriken = $isKempuOrJeriken ? $request->no_kempu_jeriken : null;

            $monitoring = MonitoringOnGoingMikro::updateOrCreate(
                ['id' => $request->id],
                [
                    'storage' => $request->storage,
                    'production_batch_id' => $request->nomor_po,
                    'variant' => $request->variant,
                    'no_filler' => $request->no_filler,
                    'no_kempu_jeriken' => $noKempuJeriken,
                    'running_number' => $request->running_number,
                    'koding' => $request->koding,
                    'jam_koding' => $request->jam_koding,
                    'jenis_sampel_1' => $request->jenis_sampel_1,
                    'jenis_sampel_2' => $request->jenis_sampel_2,
                    'jenis_sampel_3' => $request->jenis_sampel_3,
                    'filling_date' => $request->filling_date,
                    'keterangan' => $request->keterangan,
                ]
            );

            $message = $monitoring->wasRecentlyCreated
                ? 'Data berhasil disimpan'
                : 'Data berhasil diperbarui';

            return response()->json([
                'message' => $message,
                'data' => $monitoring
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = MonitoringOnGoingMikro::find($request->id);

            if ($data) {
                $data->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try againrred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $monitoring = MonitoringOnGoingMikro::with([
                'productionBatch',
            ])->findOrFail($id);

            $qrCode = DNS2DFacade::getBarcodePNG(route('monitoring-ongoing-mikro.analisa', $monitoring->id), 'QRCODE');

            // Gabungkan jenis sampel
            $jenisSampel = collect([
                $monitoring->jenis_sampel_1,
                $monitoring->jenis_sampel_2,
                $monitoring->jenis_sampel_3
            ])->filter()->implode(', ');

            $response = [
                'id' => $monitoring->id,
                'storage' => $monitoring->storage,
                'po_number' => $monitoring->productionBatch->po_number ?? '-',
                'formulation' => $monitoring->formulation ?? '-',
                'variant' => $monitoring->variant,
                'no_filler' => $monitoring->no_filler,
                'no_kempu_jeriken' => $monitoring->no_kempu_jeriken,
                'running_number' => $monitoring->running_number,
                'filling_date' => $monitoring->filling_date,
                'filling_date_formatted' => $monitoring->filling_date ?
                    \Carbon\Carbon::parse($monitoring->filling_date)->locale('id')->translatedFormat('d F Y') : '-',
                'koding' => $monitoring->koding,
                'jam_koding' => $monitoring->jam_koding,
                'jenis_sampel' => $jenisSampel ?: '-',
                'keterangan' => $monitoring->keterangan ?? '-',
                'created_at_formatted' => $monitoring->created_at ?
                    $monitoring->created_at->locale('id')->translatedFormat('d F Y, H:i') : '-',
                'qr_code' => $qrCode,

                // Analis
                'analis_eb_name' => $monitoring->analisEb->name ?? '-',
                'analis_tpc_name' => $monitoring->analisTpc->name ?? '-',
                'analis_ym_name' => $monitoring->analisYm->name ?? '-',
                'analis_benda_asing_name' => $monitoring->analisBendaAsing->name ?? '-',
                'shift' => $monitoring->shift ?? '-',
                'received_at_formatted' => $monitoring->received_at ?
                    \Carbon\Carbon::parse($monitoring->received_at)->locale('id')->translatedFormat('d F Y, H:i') : '-',

                // Parameter Mikro
                'eb' => $monitoring->eb ?? '-',
                'tpc' => $monitoring->tpc ?? '-',
                'ym' => $monitoring->ym ?? '-',
                'benda_asing' => $monitoring->benda_asing ?? '-',

                // Hasil & Disposisi
                'hasil' => $monitoring->hasil ?? '-',
                'disposition' => $monitoring->disposition ?? '-',
                'remarks' => $monitoring->remarks ?? '-',
                'updated_at_formatted' => $monitoring->updated_at ?
                    $monitoring->updated_at->locale('id')->translatedFormat('d F Y, H:i') : '-',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function analisa($id)
    {
        $monitoringOnGoing = MonitoringOnGoingMikro::with('productionBatch')->find($id);
        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.monitoring_on_going_mikro.show', compact('monitoringOnGoing', 'colors'));
    }

    public function storeAnalisaMikro(Request $request)
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

            $monitoringOnGoing = MonitoringOnGoingMikro::find($request->id);

            if (!$monitoringOnGoing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // ✅ Validasi hanya field yang benar-benar dikirim dan tidak kosong
            $rules = [];
            $updateData = [];

            if ($request->filled('shift_analis') || $request->filled('nama_analis')) {
                $rules['shift_analis'] = 'required|integer|min:1|max:3';
                $rules['nama_analis'] = 'required|string|max:255';

                $updateData['shift'] = $request->shift_analis;
                $updateData['nama_analis'] = $request->nama_analis;
            }

            if ($request->filled('eb')) {
                if (empty($monitoringOnGoing->shift) || empty($monitoringOnGoing->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input EB.'
                    ], 409);
                }
                $rules['eb'] = 'required|numeric|min:0';
                $updateData['eb'] = $request->eb;
            }

            if ($request->filled('tpc')) {
                if (empty($monitoringOnGoing->shift) || empty($monitoringOnGoing->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input TPC.'
                    ], 409);
                }
                $rules['tpc'] = 'required|numeric|min:0';
                $updateData['tpc'] = $request->tpc;
            }

            if ($request->filled('ym')) {
                if (empty($monitoringOnGoing->shift) || empty($monitoringOnGoing->nama_analis)) {
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
                    ], 422);
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
            $monitoringOnGoing->update($updateData);

            // ✅ Refresh data dari database
            $monitoringOnGoing->refresh();

            // ✅ Tentukan hasil berdasarkan kelengkapan dan kriteria
            $hasil = 'PENDING';

            if ($monitoringOnGoing->eb !== null && $monitoringOnGoing->tpc !== null && $monitoringOnGoing->ym !== null) {
                // ✅ EB = 0, TPC = 30, YM = 0 → OK
                if ($monitoringOnGoing->eb == 0 && $monitoringOnGoing->tpc == 30 && $monitoringOnGoing->ym == 0) {
                    $hasil = 'OK';
                } else {
                    $hasil = 'NOT OK';
                }
            }

            // Update hasil
            $monitoringOnGoing->update(['hasil' => $hasil]);

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
                    'eb' => $monitoringOnGoing->eb,
                    'tpc' => $monitoringOnGoing->tpc,
                    'ym' => $monitoringOnGoing->ym,
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

    public function storeAnalisaKimia(Request $request)
    {
        try {
            $monitoringOnGoing = MonitoringOnGoingMikro::find($request->id);

            if (!$monitoringOnGoing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Validasi benda_asing
            $rules = [
                'benda_asing' => 'required|in:Tidak Ada,Ada'
            ];

            $validator = Validator::make($request->all(), $rules, [
                'benda_asing.required' => 'Status benda asing wajib dipilih.',
                'benda_asing.in' => 'Status benda asing harus berupa "Tidak Ada" atau "Ada".'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update benda_asing
            $disposition = $request->benda_asing === 'Ada' ? 'NOT OK' : 'OK';
            $monitoringOnGoing->update([
                'benda_asing' => $request->benda_asing,
                'disposition' => $disposition
            ]);


            if (in_array($request->benda_asing, ['Ada'])) {
                event(new ProcessOutsideDisposition(
                    "Monitoring On Going - Mikro - Batch " . $monitoringOnGoing->productionBatch->batch_number,
                    $monitoringOnGoing->productionBatch->id,
                    'Monitoring On Going - Mikro',
                    $request->benda_asing,
                    'Terdapat benda asing',
                ));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data benda asing berhasil disimpan.',
                'data' => [
                    'benda_asing' => $monitoringOnGoing->benda_asing
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

    public function getPoByDateAndStorage(Request $request)
    {
        $tanggal_produksi = $request->input('tanggal_produksi');
        $storage = $request->input('storage');

        if (!$tanggal_produksi || !$storage) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tanggal Produksi dan Storage harus diisi.',
                'data' => []
            ]);
        }

        $data_release = MonitoringDailyTank::query()
            ->where('storage', $storage)
            ->whereIn('disposisi', ['Release', 'Release Bersyarat'])
            ->whereHas('productionBatch', function ($query) use ($tanggal_produksi) {
                $query->whereDate('date', $tanggal_produksi);
            })
            ->join('production_batches', 'monitoring_daily_tank.production_batch_id', '=', 'production_batches.id')
            ->select('production_batches.po_number', 'production_batches.id')
            ->distinct()
            ->get();

        $po_data = $data_release->unique('po_number')->map(function ($item) {
            return [
                'id' => $item->id,
                'po_number' => $item->po_number
            ];
        })->values();

        $count = $po_data->count();

        $response = [
            'status' => 'success',
            'count' => $count,
            'po_list' => $po_data,
            'selected_id' => null
        ];

        if ($count === 1) {
            $response['selected_id'] = $po_data->first()['id'];
        }

        return response()->json($response);
    }

    public function getVariantByPo(Request $request)
    {
        try {
            $production_batch_id = $request->input('production_batch_id');

            if (!$production_batch_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Production batch ID harus diisi.',
                    'data' => []
                ]);
            }

            $productionBatch = ProductionBatch::find($production_batch_id);

            if (!$productionBatch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data production batch tidak ditemukan.',
                    'data' => []
                ]);
            }

            $variantKecapCode = $productionBatch->variant;

            if (!$variantKecapCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Variant kecap tidak ditemukan pada production batch ini.',
                    'data' => []
                ]);
            }

            $response = Http::timeout(10)->get(env('PRODUCTION_URL') . 'api/varian/kecap');

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengambil data variant dari API Production.',
                    'data' => []
                ], 500);
            }

            $variantKecapFromAPI = $response->json()['data'] ?? [];

            if (empty($variantKecapFromAPI)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data variant tidak tersedia dari API.',
                    'data' => []
                ]);
            }

            $filteredVariants = collect($variantKecapFromAPI)->filter(function ($item) use ($variantKecapCode) {
                return isset($item['variant_kecap']['code']) &&
                    $item['variant_kecap']['code'] === $variantKecapCode;
            })->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'display_name' => $item['variant_fg']['name'] . ' - ' . $item['variant_kecap']['code'],
                    'variant_fg_name' => $item['variant_fg']['name'],
                    'variant_kecap_code' => $item['variant_kecap']['code']
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'count' => $filteredVariants->count(),
                'variant_list' => $filteredVariants,
                'variant_kecap_code' => $variantKecapCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
