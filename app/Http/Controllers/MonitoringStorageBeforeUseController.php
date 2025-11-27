<?php

namespace App\Http\Controllers;

use App\Events\ProcessOutsideDisposition;
use App\Http\Requests\Analisa\MonitoringStorageBeforeUseStoreRequest as AnalisaMonitoringStorageBeforeUseStoreRequest;
use App\Http\Requests\MonitoringStorageBeforeUseStoreRequest;
use App\Models\MonitoringStorageBeforeUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                ->addColumn('hasil', function ($data) {
                    if (!$data->hasil) {
                        return '<span class="badge bg-secondary">Belum Ada</span>';
                    }

                    $hasilUpper = strtoupper($data->hasil);
                    $badgeClass = match (true) {
                        str_contains($hasilUpper, 'OK') && !str_contains($hasilUpper, 'NOT') => 'bg-success',
                        $hasilUpper === 'NOT OK' => 'bg-danger',
                        $hasilUpper === 'PENDING' => 'bg-warning text-dark',
                        default => 'bg-secondary'
                    };

                    return '<span class="badge ' . $badgeClass . '">' . $hasilUpper . '</span>';
                })
                ->addColumn('detail', function ($data) {
                    return '
                        <button class="btn btn-sm btn-info btn-detail" data-id="' . $data->id . '" title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    ';
                })
                ->addColumn('status_approval', function ($data) {
                    if ($data->status_approval === 'waiting_approval') {
                        return '<span class="badge bg-warning"><i class="mdi mdi-clock-outline"></i> Menunggu Approval</span>';
                    } elseif ($data->status_approval === 'approved') {
                        return '<span class="badge bg-success"><i class="mdi mdi-check"></i> Disetujui</span>';
                    } elseif ($data->status_approval === 'rejected') {
                        return '<span class="badge bg-danger"><i class="mdi mdi-close"></i> Ditolak</span>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($data) {
                    $html = '';

                    if (auth()->check()) {
                        if (auth()->user()->role === 'Analis Kimia') {
                            $html .= '
                                <button class="btn btn-sm btn-primary me-1" id="btnAnalisa" data-id="' . $data->id . '">
                                <span class="mdi mdi-test-tube"></span> Analisa
                                </button>';
                        } elseif (auth()->user()->role === 'Foreman') {
                            if ($data->hasil === 'NOT OK' && $data->status_approval === 'waiting_approval') {
                                $html .= '
                                <button class="btn btn-sm btn-success me-1" id="btnApprove" data-id="' . $data->id . '" data-status="approve">
                                    <span class="mdi mdi-check"></span> Disetujui
                                </button>
                                <button class="btn btn-sm btn-danger me-1" id="btnReject" data-id="' . $data->id . '" data-status="rejected">
                                    <span class="mdi mdi-close"></span> Ditolak
                                </button>';
                            } elseif (auth()->user()->role === 'Foreman' || auth()->user()->role === 'Analis Field') {
                                $html .= '
                                <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                                <span class="mdi mdi-pencil"></span> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                                    <span class="mdi mdi-trash-can"></span> Hapus
                                </button>';
                            }
                        } else {
                            if ($data->hasil === 'NOT OK' && $data->status_approval === 'approved') {
                                $html .= '
                                <button class="btn btn-sm btn-info me-1" id="btnFlushing" data-id="' . $data->id . '">
                                    <span class="mdi mdi-autorenew"></span> Flushing
                                </button>';
                            }
                        }
                    }

                    return $html;
                })
                ->rawColumns(['waktu_selesai_pemakaian', 'estimasi_kadaluarsa', 'hasil', 'detail', 'action', 'tahap_flushing', 'status_approval'])
                ->make(true);
        }
        $variantKecap = Http::get(env('PRODUCTION_URL') . 'api/varian/kecap')->json()['data'];

        return view('app.monitoring_storage_before_use.index', compact('variantKecap'));
    }

    public function store(MonitoringStorageBeforeUseStoreRequest $request)
    {
        try {
            if (strtolower($request->jenis_sample) === 'flushing') {
                $tahapFlushing = [
                    'Before Inlet',
                    'Before Outlet',
                    'After Inlet',
                    'After Outlet'
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
                    $statusApproval = null;
                } else {
                    $hasil = 'NOT OK';
                    $statusApproval = 'waiting_approval';
                }
            }

            $updateData = [
                'visco' => $request->visco,
                'brix' => $request->brix,
                'aw' => $request->aw,
                'hasil' => $hasil,
            ];

            // Jika NOT OK, set status approval
            if ($hasil === 'NOT OK') {
                $updateData['status_approval'] = 'waiting_approval';
                $updateData['final_status'] = null;
            } else if ($hasil === 'OK') {
                $updateData['status_approval'] = null;
                $updateData['final_status'] = 'released';
            }

            $data->update($updateData);

            event(new ProcessOutsideDisposition(
                title: "Monitoring Before Use - Storage " . $data->storage . ' (' . $data->variant . ')',
                production_batch_id: null,
                process: "Monitoring",
                status_disposition: $hasil === 'NOT OK' ? 'Waiting Approval' : $hasil,
                message: $hasil === 'NOT OK'
                    ? "Hasil Analisa: $hasil - Menunggu persetujuan Foreman"
                    : "Hasil Analisa: $hasil",
            ));


            return response()->json([
                'status'  => 'success',
                'message' => $hasil === 'NOT OK'
                    ? 'Data berhasil disimpan. Menunggu persetujuan Foreman untuk hasil NOT OK.'
                    : 'Data berhasil disimpan.',
                'hasil' => $hasil,
                'status_approval' => $statusApproval ?? null,
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

    public function approve(Request $request, $id)
    {
        try {
            $data = MonitoringStorageBeforeUse::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            // Cek apakah user adalah Foreman
            if (auth()->user()->role !== 'Foreman') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk approve.',
                ], 403);
            }

            // Cek apakah masih waiting approval
            if ($data->status_approval !== 'waiting_approval') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak dalam status menunggu approval.',
                ], 400);
            }

            $action = $request->action;

            // Jika rejected, ubah hasil menjadi OK
            if ($action === 'approve') {
                $data->update([
                    'hasil' => 'NOT OK',
                    'status_approval' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'final_status' => 'need_flushing'
                ]);

                $message = 'Sample dikonfirmasi NOT OK dan perlu dilakukan FLUSHING.';
                // $dispositionMessage = "Foreman menyetujui hasil NOT OK - Sample perlu FLUSHING";
            } else {
                // REJECT = Tidak setuju NOT OK, OVERRIDE menjadi OK
                $data->update([
                    'hasil' => 'OK (Override by Foreman)',
                    'status_approval' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'final_status' => 'released'
                ]);

                $message = 'Sample di-OVERRIDE menjadi OK oleh Foreman dan dirilis untuk digunakan.';
                // $dispositionMessage = "Foreman meng-override hasil NOT OK menjadi OK - Sample RELEASED";
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'action' => $action,
                'final_status' => $data->final_status,
                'hasil' => $data->hasil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function flushing($id)
    {
        try {
            $record = MonitoringStorageBeforeUse::find($id);

            if (!$record) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            $tahapFlushing = [
                'Before Inlet',
                'Before Outlet',
                'After Inlet',
                'After Outlet'
            ];

            foreach ($tahapFlushing as $tahap) {
                $data = [
                    'storage' => $record->storage,
                    'variant' => $record->variant,
                    'jenis_sample' => $record->jenis_sample,
                    'tahap_flushing' => $tahap,
                    'waktu_selesai_pemakaian' => $record->waktu_selesai_pemakaian,
                    'estimasi_kadaluarsa' => $record->estimasi_kadaluarsa,
                ];

                MonitoringStorageBeforeUse::create($data);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Data flushing berhasil disimpan (4 tahap dibuat).',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
