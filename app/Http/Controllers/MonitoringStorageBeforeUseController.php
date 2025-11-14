<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
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
                ->addColumn('tahap_flushing', function ($data) {
                    if ($data->tahap_flushing) {
                        return '<span class="badge bg-info">' . $data->tahap_flushing . '</span>';
                    }
                    return '-';
                })
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
                ->rawColumns(['waktu_selesai_pemakaian', 'estimasi_kadaluarsa', 'hasil', 'detail', 'action', 'tahap_flushing'])
                ->make(true);
        }
        return view('app.monitoring_storage_before_use.index');
    }

    public function store(MonitoringStorageBeforeUseStoreRequest $request)
    {
        try {
            if (strtolower($request->jenis_sample) === 'flushing') {
                $tahapFlushing = [
                    'Before Inlate',
                    'Before Outlate',
                    'After Inlate',
                    'After Outlate'
                ];

                foreach ($tahapFlushing as $tahap) {
                    $data = [
                        'storage' => $request->storage,
                        'variant' => $request->variant,
                        'jenis_sample' => $request->jenis_sample,
                        'tahap_flushing' => $tahap,
                        'waktu_selesai_pemakaian' => $request->waktu_selesai_pemakaian,
                        'estimasi_kadaluarsa' => $request->estimasi_kadaluarsa,
                    ];

                    MonitoringStorageBeforeUse::create($data);
                }

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data flushing berhasil disimpan (4 tahap dibuat).',
                ], 201);
            } else {
                $data = [
                    'storage'  => $request->storage,
                    'variant' => $request->variant,
                    'jenis_sample' => $request->jenis_sample,
                    'tahap_flushing' => null,
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
            }
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

            $visco = $request->visco;
            $brix = $request->brix;
            $aw = $request->aw;
            $variant = $data->variant;

            // Standar berdasarkan varian
            $standards = [
                'MSD NR1' => [
                    'brix_min' => 74,
                    'brix_max' => 76,
                    'visco_min' => 7.00,
                    'visco_max' => 10.00,
                    'aw_max' => 0.6800
                ],
                'MSD NR2' => [
                    'brix_min' => 74,
                    'brix_max' => 76,
                    'visco_min' => 7.00,
                    'visco_max' => 10.00,
                    'aw_max' => 0.6800
                ],
                'JB' => [
                    'brix_min' => 76,
                    'brix_max' => null,
                    'visco_min' => 16.00,
                    'visco_max' => 25.00,
                    'aw_max' => 0.7100
                ],
                'SS1' => [
                    'brix_min' => 75,
                    'brix_max' => null,
                    'visco_min' => 14.00,
                    'visco_max' => 22.00,
                    'aw_max' => 0.7100
                ],
                'SS2' => [
                    'brix_min' => 75,
                    'brix_max' => null,
                    'visco_min' => 16.00,
                    'visco_max' => 24.00,
                    'aw_max' => 0.7100
                ],
                'BB' => [
                    'brix_min' => 77,
                    'brix_max' => null,
                    'visco_min' => 17.00,
                    'visco_max' => 28.00,
                    'aw_max' => 0.7100
                ],
            ];

            // Default jika variant tidak ditemukan
            if (!isset($standards[$variant])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Varian tidak valid.',
                ], 400);
            }

            $std = $standards[$variant];

            // Cek apakah semua parameter sudah diisi
            if ($visco === null || $brix === null || $aw === null) {
                $hasil = 'PENDING';
                $validation_details = [
                    'visco' => ['status' => 'pending', 'message' => 'Belum diisi'],
                    'brix' => ['status' => 'pending', 'message' => 'Belum diisi'],
                    'aw' => ['status' => 'pending', 'message' => 'Belum diisi']
                ];
            } else {
                // Array untuk menyimpan detail validasi
                $validation_details = [];

                // Validasi Viskositas
                $isViscoValid = ($visco >= $std['visco_min'] && $visco <= $std['visco_max']);
                $validation_details['visco'] = [
                    'value' => $visco,
                    'standard' => $std['visco_min'] . ' - ' . $std['visco_max'],
                    'status' => $isViscoValid ? 'OK' : 'NOT OK',
                    'message' => $isViscoValid
                        ? "Viskositas OK ({$visco} dalam range {$std['visco_min']}-{$std['visco_max']})"
                        : "Viskositas NOT OK ({$visco} diluar range {$std['visco_min']}-{$std['visco_max']})"
                ];

                // Validasi Brix
                $isBrixValid = ($brix >= $std['brix_min']);
                if ($std['brix_max'] !== null) {
                    $isBrixValid = $isBrixValid && ($brix <= $std['brix_max']);
                    $brixStandard = $std['brix_min'] . ' - ' . $std['brix_max'];
                    $brixMessage = $isBrixValid
                        ? "Brix OK ({$brix} dalam range {$std['brix_min']}-{$std['brix_max']})"
                        : "Brix NOT OK ({$brix} diluar range {$std['brix_min']}-{$std['brix_max']})";
                } else {
                    $brixStandard = 'Min ' . $std['brix_min'];
                    $brixMessage = $isBrixValid
                        ? "Brix OK ({$brix} >= {$std['brix_min']})"
                        : "Brix NOT OK ({$brix} < {$std['brix_min']})";
                }
                $validation_details['brix'] = [
                    'value' => $brix,
                    'standard' => $brixStandard,
                    'status' => $isBrixValid ? 'OK' : 'NOT OK',
                    'message' => $brixMessage
                ];

                // Validasi AW
                $isAwValid = ($aw < $std['aw_max']);
                $validation_details['aw'] = [
                    'value' => $aw,
                    'standard' => '< ' . $std['aw_max'],
                    'status' => $isAwValid ? 'OK' : 'NOT OK',
                    'message' => $isAwValid
                        ? "AW OK ({$aw} < {$std['aw_max']})"
                        : "AW NOT OK ({$aw} >= {$std['aw_max']})"
                ];

                // Tentukan hasil akhir
                if ($isViscoValid && $isBrixValid && $isAwValid) {
                    $hasil = 'OK';
                } else {
                    $hasil = 'NOT OK';
                }
            }

            $data->update([
                'visco' => $request->visco,
                'brix' => $request->brix,
                'aw' => $request->aw,
                'hasil' => $hasil,
            ]);

            if (in_array($hasil, ['NOT OK'])) {
                event(new ProcessOutsideDisposition(
                    "Monitoring Before Use - Storage " . $data->storage . ' (' . $data->variant . ')',
                    'Monitoring Before Use',
                    $hasil,
                    "Hasil Analisa: $hasil",
                ));
            }


            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
                'hasil' => $hasil,
                'variant' => $variant,
                'standard' => $std,
                'validation_details' => $validation_details
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
