<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Analisa\MonitoringOngoingKimiaRequest as AnalisaMonitoringOngoingKimiaRequest;
use App\Http\Requests\MonitoringOngoingKimiaRequest;
use App\Models\Color;
use App\Models\MonitoringDailyTank;
use App\Models\MonitoringOnGoingKimia;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Milon\Barcode\Facades\DNS2DFacade;
use Yajra\DataTables\DataTables;

class MonitoringOnGoingKimiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $monitoringOnGoings = MonitoringOnGoingKimia::with('productionBatch')
                ->orderByRaw('status IS NULL DESC')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($monitoringOnGoings)
                ->addIndexColumn()
                ->addColumn('po_number', function ($data) {
                    return $data->productionBatch->po_number ?? '-';
                })
                ->addColumn('status', function ($data) {
                    $status = $data->status ?? '-';
                    $badgeClass = match ($status) {
                        'OK' => 'badge bg-success',
                        'NOT OK' => 'badge bg-danger',
                        default => 'badge bg-secondary',
                    };
                    return '<span class="' . $badgeClass . '">' . $status . '</span>';
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
                        <a class="btn btn-sm btn-primary me-1" id="btnAnalisa" href="' . route('monitoring-ongoing-kimia.analisa', $data->id) . '">
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
                ->rawColumns(['action', 'detail', 'analisa', 'status'])
                ->make(true);
        }

        $passedPoNumbers = ProductionBatch::whereHas('monitoringPasteurisasi', function ($q) {
            $q->whereIn('disposition', ['Release', 'Release Bersyarat']);
        })
            ->get(['id', 'po_number'])
            ->mapWithKeys(fn($item) => [$item->id => $item->po_number])
            ->unique()
            ->filter();

        return view('app.monitoring_on_going_kimia.index', compact('passedPoNumbers'));
    }

    public function store(MonitoringOngoingKimiaRequest $request)
    {
        try {
            $monitoring = MonitoringOnGoingKimia::updateOrCreate(
                ['id' => $request->id],
                [
                    'storage' => $request->storage,
                    'production_batch_id' => $request->nomor_po,
                    'variant' => $request->variant,
                    'filling_date' => $request->filling_date,
                    'jam_koding' => $request->jam_koding,
                    'koding' => $request->koding,
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

    public function edit($id)
    {
        try {
            $data = MonitoringOnGoingKimia::find($id);

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

    public function show($id)
    {
        try {
            $monitoring = MonitoringOnGoingKimia::with([
                'productionBatch',
                'analis',
                'color'
            ])->findOrFail($id);

            $qrCode = DNS2DFacade::getBarcodePNG(route('monitoring-ongoing-kimia.analisa', $monitoring->id), 'QRCODE');

            $response = [
                'id' => $monitoring->id,
                'storage' => $monitoring->storage,
                'po_number' => $monitoring->productionBatch->po_number ?? '-',
                'koding' => $monitoring->koding,
                'variant' => $monitoring->variant,
                'filling_date' => $monitoring->filling_date,
                'filling_date_formatted' => $monitoring->filling_date ?
                    \Carbon\Carbon::parse($monitoring->filling_date)->locale('id')->translatedFormat('d F Y') : '-',
                'jam_koding' => $monitoring->jam_koding,
                'created_at_formatted' => $monitoring->created_at ?
                    $monitoring->created_at->locale('id')->translatedFormat('d F Y, H:i') : '-',
                'qr_code' => $qrCode,
                'analis_name' => $monitoring->analis->name ?? '-',
                'shift' => $monitoring->shift,
                'received_at_formatted' => $monitoring->received_at ?
                    \Carbon\Carbon::parse($monitoring->received_at)->locale('id')->translatedFormat('d F Y, H:i') : '-',
                'berat_jenis' => $monitoring->berat_jenis,
                'visco' => $monitoring->visco,
                'brix' => $monitoring->brix,
                'aw' => $monitoring->aw,
                'nacl' => $monitoring->nacl,
                'ph' => $monitoring->ph,
                'color_name' => $monitoring->color->name ?? '-',
                'color_code' => $monitoring->color->code ?? '-',
                'organo' => $monitoring->organo,
                'status' => $monitoring->status,
                'disposition' => $monitoring->disposition,
                'remarks' => $monitoring->remarks,
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

    public function destroy(Request $request)
    {
        try {
            $data = MonitoringOnGoingKimia::find($request->id);

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

    public function analisa($id)
    {
        $monitoringOnGoing = MonitoringOnGoingKimia::with('productionBatch')->find($id);
        $colors = Color::orderBy('name', 'asc')->get();
        return view('app.monitoring_on_going_kimia.show', compact('monitoringOnGoing', 'colors'));
    }

    public function storeAnalisa(AnalisaMonitoringOngoingKimiaRequest $request)
    {
        try {
            $monitoring = MonitoringOnGoingKimia::with('productionBatch')->find($request->id);

            if (!$monitoring) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            if (in_array($request->status_disposition, ['NOT OK']) && empty($request->remark)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kolom keterangan (catatan) wajib diisi untuk status ini.'
                ], 409);
            }

            $currentHour = (int) now()->format('H');
            if ($currentHour >= 6 && $currentHour < 14) {
                $shift = 1;
            } elseif ($currentHour >= 14 && $currentHour < 22) {
                $shift = 2;
            } else {
                $shift = 3;
            }

            $monitoring->analis_id = auth()->user()->id;
            $monitoring->shift = $shift;
            $monitoring->berat_jenis = $request->berat_jenis;
            $monitoring->visco = $request->visco;
            $monitoring->brix = $request->brix;
            $monitoring->aw = $request->aw;
            $monitoring->nacl = $request->nacl;
            $monitoring->ph = $request->ph;
            $monitoring->color_id = $request->color;
            $monitoring->organo = $request->organo;
            $monitoring->status = $request->status_disposition;
            $monitoring->disposition = $request->status_disposition === 'OK' ? 'Release' : 'Hold';
            $monitoring->remarks = $request->remark;
            $monitoring->save();

            if (in_array($request->status_disposition, ['NOT OK'])) {
                event(new ProcessOutsideDisposition(
                    "Monitoring On Going - Kimia - Batch " . $monitoring->productionBatch->batch_number,
                    $monitoring->productionBatch->id,
                    'Monitoring On Going - Kimia',
                    $request->status_disposition,
                    $request->remark,
                ));
            }

            return response()->json([
                'message' => 'Data analisa berhasil disimpan',
                'data' => $monitoring
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data analisa',
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
