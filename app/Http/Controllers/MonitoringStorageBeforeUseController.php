<?php

namespace App\Http\Controllers;

use App\Http\Requests\Analisa\MonitoringStorageBeforeUseStoreRequest as AnalisaMonitoringStorageBeforeUseStoreRequest;
use App\Http\Requests\MonitoringStorageBeforeUseStoreRequest;
use App\Models\MonitoringStorageBeforeUse;
use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS2DFacade;
use Yajra\DataTables\Facades\DataTables;

class MonitoringStorageBeforeUseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MonitoringStorageBeforeUse::orderBy('created_at', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $monitoringStorageBeforeUse = $query->get();

            return DataTables::of($monitoringStorageBeforeUse)
                ->addIndexColumn()
                ->editColumn('waktu_selesai_pemakaian', function ($data) {
                    if (!$data->waktu_selesai_pemakaian) {
                        return '-';
                    }
                    return \Carbon\Carbon::parse($data->waktu_selesai_pemakaian)
                        ->locale('id')
                        ->translatedFormat('d F Y, H:i');
                })
                ->editColumn('estimasi_kadaluarsa', function ($data) {
                    if (!$data->estimasi_kadaluarsa) {
                        return '-';
                    }
                    return \Carbon\Carbon::parse($data->estimasi_kadaluarsa)
                        ->locale('id')
                        ->translatedFormat('d F Y, H:i');
                })
                ->addColumn('hasil', function ($data) {
                    if (!$data->hasil) {
                        return '<span class="badge bg-secondary">Belum Ada</span>';
                    }

                    $badgeClass = match (strtoupper($data->hasil)) {
                        'OK' => 'bg-success',
                        'NOT OK' => 'bg-danger',
                        'PENDING' => 'bg-warning text-dark',
                        default => 'bg-secondary'
                    };

                    return '<span class="badge ' . $badgeClass . '">' . strtoupper($data->hasil) . '</span>';
                })
                ->addColumn('detail', function ($data) {
                    return '
                        <button class="btn btn-sm btn-info btn-detail" data-id="' . $data->id . '" title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    ';
                })
                ->addColumn('action', function ($data) {
                    $html = '';

                    if (auth()->check() && auth()->user()->role === 'Analis Kimia') {
                        $html .= '
                        <button class="btn btn-sm btn-primary me-1" id="btnAnalisa" data-id="' . $data->id . '">
                           <span class="mdi mdi-test-tube"></span> Analisa
                        </button>';
                    } else {
                        $html .= '
                        <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                        <span class="mdi mdi-pencil"></span> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                            <span class="mdi mdi-trash-can"></span> Hapus
                        </button>
                        ';
                    }

                    return $html;
                })
                ->rawColumns(['waktu_selesai_pemakaian', 'estimasi_kadaluarsa', 'hasil', 'detail', 'action'])
                ->make(true);
        }
        return view('app.monitoring_storage_before_use.index');
    }

    public function store(MonitoringStorageBeforeUseStoreRequest $request)
    {
        try {
            $data = [
                'storage'  => $request->storage,
                'jenis_sample' => $request->jenis_sample,
                'waktu_selesai_pemakaian' => $request->waktu_selesai_pemakaian,
                'estimasi_kadaluarsa' => $request->estimasi_kadaluarsa,
            ];

            MonitoringStorageBeforeUse::updateOrCreate(
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

    public function storeAnalisa(AnalisaMonitoringStorageBeforeUseStoreRequest $request)
    {
        try {
            $data = MonitoringStorageBeforeUse::find($request->id_analisa);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $visco  = $request->visco;
            $brix = $request->brix;
            $aw  = $request->aw;

            // Standar
            $standard_visco = 10;
            $standard_brix = 0;
            $standard_aw = 0;

            // Hitung status
            if (
                ($visco !== null && $visco > $standard_visco) ||
                ($brix !== null && $brix > $standard_brix) ||
                ($aw !== null && $aw > $standard_aw)
            ) {
                $hasil = 'NOT OK';
            } elseif ($visco === null || $brix === null || $aw === null) {
                $hasil = 'PENDING'; // menunggu parameter lain
            } else {
                $hasil = 'OK';
            }

            $data->update([
                'visco' => $request->visco,
                'brix' => $request->brix,
                'aw' => $request->aw,
                'hasil' => $hasil,
            ]);

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
            $data = MonitoringStorageBeforeUse::find($id);

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
            $data = MonitoringStorageBeforeUse::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            // Generate QR Code
            $qrText = route('monitoring-storage-before-use.analisa', $data->id);
            $qrCode = DNS2DFacade::getBarcodePNG($qrText, 'QRCODE');

            return response()->json([
                'id' => $data->id,
                'storage' => $data->storage,
                'jenis_sample' => $data->jenis_sample,
                'waktu_selesai_pemakaian' => $data->waktu_selesai_pemakaian,
                'waktu_selesai_pemakaian_formatted' => $data->waktu_selesai_pemakaian
                    ? \Carbon\Carbon::parse($data->waktu_selesai_pemakaian)->locale('id')->translatedFormat('d F Y, H:i')
                    : null,
                'estimasi_kadaluarsa' => $data->estimasi_kadaluarsa,
                'estimasi_kadaluarsa_formatted' => $data->estimasi_kadaluarsa
                    ? \Carbon\Carbon::parse($data->estimasi_kadaluarsa)->locale('id')->translatedFormat('d F Y, H:i')
                    : null,
                'visco' => $data->visco,
                'brix' => $data->brix,
                'aw' => $data->aw,
                'hasil' => $data->hasil,
                'created_at_formatted' => $data->created_at
                    ? \Carbon\Carbon::parse($data->created_at)->locale('id')->translatedFormat('d F Y, H:i')
                    : null,
                'qr_code' => $qrCode,
            ]);
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
            $data = MonitoringStorageBeforeUse::find($request->id);

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
        $monitoringStorageBeforeUse = MonitoringStorageBeforeUse::find($id);

        return view('app.monitoring_storage_before_use.analisa', compact('monitoringStorageBeforeUse'));
    }
}
