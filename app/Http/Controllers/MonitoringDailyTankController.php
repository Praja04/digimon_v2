<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonitoringDailyTankStoreRequest;
use App\Models\MonitoringDailyTank;
use App\Models\MonitoringStorageKimia;
use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS2DFacade;
use Yajra\DataTables\DataTables;

class MonitoringDailyTankController extends Controller
{
    public function menu()
    {
        return view('app.monitoring_daily_tank.menu');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MonitoringDailyTank::orderBy('created_at', 'desc');

            // Filter data berdasarkan role user
            if (auth()->user()->role == 'Analis Mikro') {
                $query->where('jenis_analisa', 'Mikro');
            } elseif (auth()->user()->role == 'Analis Kimia') {
                $query->where('jenis_analisa', 'Kimia');
            }

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $monitoringDailyTank = $query->get();

            return DataTables::of($monitoringDailyTank)
                ->addIndexColumn()
                ->editColumn('tanggal_sampling', function ($data) {
                    if (!$data->tanggal_sampling) {
                        return '-';
                    }
                    return \Carbon\Carbon::parse($data->tanggal_sampling)
                        ->locale('id')
                        ->translatedFormat('d F Y, H:i');
                })
                ->addColumn('detail', function ($data) {
                    return '
                        <button class="btn btn-sm btn-info btn-detail" data-id="' . $data->id . '" title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    ';
                })
                ->editColumn('nomor_po', function ($data) {
                    return $data->productionBatch->po_number ?? '-';
                })
                ->addColumn('analisa', function ($data) {
                    $html = '';
                    if ($data->jenis_analisa == 'Mikro' && in_array(auth()->user()->role, ['Analis Mikro', 'Foreman'])) {
                        $html .= '
                        <a class="btn btn-sm btn-primary me-1" id="btnAnalisa" href="' . route('analisa.monitoring-daily-tank-mikro.show', $data->id) . '">
                           <span class="mdi mdi-test-tube"></span>
                        </a>
                        ';
                    } elseif ($data->jenis_analisa == 'Kimia' && in_array(auth()->user()->role, ['Analis Kimia', 'Foreman'])) {
                        $html .= '
                        <a class="btn btn-sm btn-primary me-1" id="btnAnalisa" href="' . route('analisa.monitoring-daily-tank-kimia.show', $data->id) . '">
                           <span class="mdi mdi-test-tube"></span>
                        </a>
                        ';
                    }

                    return $html;
                })
                ->addColumn('hasil_analisa', function ($data) {
                    if ($data->jenis_analisa == 'Mikro') {
                        if (!$data->hasil) {
                            return '<span class="badge bg-secondary">Belum dianalisa</span>';
                        } else {
                            $badgeClass = $data->hasil == 'OK' ? 'bg-success' : 'bg-danger';
                            return '<span class="badge ' . $badgeClass . '">' . $data->hasil . '</span>';
                        }
                    }
                    if ($data->jenis_analisa == 'Kimia') {
                        if (!$data->status) {
                            return '<span class="badge bg-secondary">Belum dianalisa</span>';
                        } elseif ($data->status && $data->disposisi == null) {
                            return '<span class="badge bg-primary">Menunggu disposisi</span>';
                        } else {
                            $badgeClass = $data->disposisi == 'Release' ? 'bg-success' : ($data->disposisi == 'Reject' ? 'bg-danger' : 'bg-info');
                            return '<span class="badge ' . $badgeClass . '">' . $data->disposisi . '</span>';
                        }
                    }
                    return '-';
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
                ->rawColumns(['detail', 'analisa', 'hasil_analisa', 'action'])
                ->make(true);
        }
        return view('app.monitoring_daily_tank.index');
    }

    public function store(MonitoringDailyTankStoreRequest $request)
    {
        try {
            $data = [
                'production_batch_id' => $request->nomor_po,
                'storage'  => $request->storage,
                'tanggal_sampling' => now(),
                'sampling_point' => $request->sampling_point,
                'status_pemakaian' => 'Filling',
                'jenis_analisa' => $request->jenis_analisa,
                'jenis_sample' => $request->jenis_sample,
                'qc_field' => auth()->user()->id,
                'keterangan_level' => $request->keterangan_level,
            ];

            MonitoringDailyTank::updateOrCreate(
                ['id' => $request->id],
                $data
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $data = MonitoringDailyTank::find($id);

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
            $data = MonitoringDailyTank::with(['qcField', 'qcAnalisa', 'color', 'productionBatch'])->find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            // Generate QR Code
            if ($data->jenis_analisa == 'Mikro') {
                $qrText = route('analisa.monitoring-daily-tank-mikro.show', $data->id);
            } else {
                $qrText = route('analisa.monitoring-daily-tank-kimia.show', $data->id);
            }
            $qrCode = DNS2DFacade::getBarcodePNG($qrText, 'QRCODE');

            // Format data untuk response
            $response = [
                'qr_code' => $qrCode,
                'id' => $data->id,
                'storage' => $data->storage,
                'tanggal_sampling' => $data->tanggal_sampling ? \Carbon\Carbon::parse($data->tanggal_sampling)->locale('id')->translatedFormat('d F Y, H:i') : null,
                'sampling_point' => $data->sampling_point,
                'status_pemakaian' => $data->status_pemakaian,
                'jenis_analisa' => $data->jenis_analisa,
                'jenis_sample' => $data->jenis_sample,
                'keterangan_level' => $data->keterangan_level,

                // QC & Lab
                'qc_field_name' => $data->qcField ? $data->qcField->name : null,
                'tanggal_diterima_lab' => $data->tanggal_diterima_lab ? \Carbon\Carbon::parse($data->tanggal_diterima_lab)->locale('id')->translatedFormat('d F Y, H:i') : null,

                // Analisa
                'shift_analisa' => $data->shift_analisa,
                'qc_analisa_name' => $data->qcAnalisa ? $data->qcAnalisa->name : null,
                'tanggal_analisa' => $data->tanggal_analisa ? \Carbon\Carbon::parse($data->tanggal_analisa)->locale('id')->translatedFormat('d F Y, H:i') : null,

                // Parameter Uji MIKRO
                'eb' => $data->eb,
                'tpc' => $data->tpc,
                'ym' => $data->ym,
                'hasil' => $data->hasil,

                // Parameter Uji KIMIA
                'brix' => $data->brix,
                'nacl' => $data->nacl,
                'bj' => $data->bj,
                'visco' => $data->visco,
                'aw' => $data->aw,
                'ph' => $data->ph,
                'buih' => $data->buih,
                'organo' => $data->organo,
                'endapan' => $data->endapan,
                'color_name' => $data->color ? $data->color->name . ' (' . $data->color->code . ')' : null,
                'status' => $data->status,

                // Hasil & Catatan
                'catatan_analis' => $data->catatan_analis,
                'tanggal_input_hasil' => $data->tanggal_input_hasil ? \Carbon\Carbon::parse($data->tanggal_input_hasil)->locale('id')->translatedFormat('d F Y, H:i') : null,

                // Disposisi
                'disposisi' => $data->disposisi,
                'alasan_disposisi' => $data->alasan_disposisi,
                'tindakan_lanjutan' => $data->tindakan_lanjutan,

                // Created At
                'created_at_formatted' => $data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('id')->translatedFormat('d F Y, H:i') : null,

                'po_number' => $data->productionBatch ? $data->productionBatch->po_number : null,
                'date' => $data->productionBatch ? $data->productionBatch->date : null,
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

    public function destroy($id)
    {
        try {
            $data = MonitoringDailyTank::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $data->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data.',
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

        $data_release = MonitoringStorageKimia::query()
            ->where('storage', $storage)
            ->whereIn('disposition', ['Release', 'Release Bersyarat'])
            ->whereHas('productionBatch', function ($query) use ($tanggal_produksi) {
                $query->whereDate('date', $tanggal_produksi);
            })
            ->join('production_batches', 'monitoring_storage_kimia.production_batch_id', '=', 'production_batches.id')
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
}
