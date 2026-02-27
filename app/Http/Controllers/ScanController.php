<?php

namespace App\Http\Controllers;

use App\Models\BlendingAfterAdjustMikro;
use App\Models\BlendingAwal;
use App\Models\MonitoringDailyTank;
use App\Models\MonitoringOnGoingKimia;
use App\Models\MonitoringOnGoingMikro;
use App\Models\MonitoringPasteurisasi;
use App\Models\MonitoringStorageBeforeUse;
use App\Models\MonitoringStorageKimia;
use App\Models\MonitoringStorageMikro;
use App\Models\MonitoringTurunBlending;
use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
use App\Models\QRScanLog;
use App\Models\ShelfLifeSamplingDetail;
use App\Models\ShelfLifeSamplingKimia;
use App\Models\ShelfLifeSamplingMikro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    // Definisi hak akses berdasarkan role
    private $rolePermissions = [
        'Analis Kimia' => [
            'pelarutan-1',
            'pelarutan-2',
            'blending-awal',
            'monitoring-turun-blending',
            'monitoring-pasteurisasi',
            'monitoring-storage-kimia',
            'monitoring-storage-before-use',
            'monitoring-daily-tank-kimia',
            'monitoring-ongoing-kimia',
            'monitoring-ongoing-mikro',
            'shelf-life-sampling'
        ],
        'Analis Mikro' => [
            'blending-awal-mikro',
            'monitoring-storage-mikro',
            'monitoring-daily-tank-mikro',
            'monitoring-ongoing-mikro',
            'shelf-life-sampling'
        ]
    ];

    public function index()
    {
        return view('app.scan.index');
    }

    private function validateAccess($type, $userRole)
    {
        if (!isset($this->rolePermissions[$userRole])) {
            return [
                'valid' => false,
                'message' => 'Role Anda tidak memiliki akses untuk scan QR code.'
            ];
        }

        if (!in_array($type, $this->rolePermissions[$userRole])) {
            return [
                'valid' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan scan pada QR Code ini.'
            ];
        }

        return ['valid' => true];
    }

    public function store(Request $request)
    {
        $input = $request->url;

        // Mapping prefix kode manual ke type sistem
        $prefixMapping = [
            'PELARUTAN-1' => 'pelarutan-1',
            'PELARUTAN-2' => 'pelarutan-2',
            'BLENDING-AWAL' => 'blending-awal',
            'BLENDING-AFTER-ADJUST-MIKRO' => 'blending-awal-mikro',
            'MONITORING-TURUN-BLENDING' => 'monitoring-turun-blending',
            'MONITORING-PASTEURISASI' => 'monitoring-pasteurisasi',
            'MONITORING-STORAGE-KIMIA' => 'monitoring-storage-kimia',
            'MONITORING-STORAGE-MIKRO' => 'monitoring-storage-mikro',
            'MONITORING-STORAGE-BEFORE-USE' => 'monitoring-storage-before-use',
            'MONITORING-DAILY-TANK-KIMIA' => 'monitoring-daily-tank-kimia',
            'MONITORING-DAILY-TANK-MIKRO' => 'monitoring-daily-tank-mikro',
            'MONITORING-ONGOING-KIMIA' => 'monitoring-ongoing-kimia',
            'MONITORING-ONGOING-MIKRO' => 'monitoring-ongoing-mikro',
            'SHELF-LIFE-SAMPLING' => 'shelf-life-sampling',
        ];

        // Cek apakah input berupa URL atau kode manual
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            // ===== FORMAT URL =====
            $path = parse_url($input, PHP_URL_PATH);
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            $type = $segments[count($segments) - 2] ?? null;
            $id   = $segments[count($segments) - 1] ?? null;

            Log::info("URL Scan - Type: {$type}, ID: {$id}");
        } else {
            // ===== FORMAT KODE MANUAL: PROSES/PO/DATE/ID =====
            $segments = explode('/', trim($input));

            // Validasi HARUS 4 segment
            if (count($segments) !== 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format kode tidak valid. Gunakan format: PROSES/PO/DATE/ID' . "\n\n" .
                        'Contoh: PELARUTAN-1/1002001/2026-02-10/3'
                ], 400);
            }

            $prefix = strtoupper($segments[0]); // PROSES
            $po = $segments[1];                  // PO/BATCH
            $date = $segments[2];                // DATE
            $id = $segments[3];                  // ID

            // Validasi prefix apakah ada di mapping
            if (!isset($prefixMapping[$prefix])) {
                $validPrefixes = array_keys($prefixMapping);
                $prefixList = implode(', ', $validPrefixes);

                return response()->json([
                    'status' => 'error',
                    'message' => "Prefix '{$prefix}' tidak dikenali.\n\nPrefix yang valid:\n{$prefixList}"
                ], 400);
            }

            // Validasi DATE format (YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format tanggal tidak valid. Gunakan format: YYYY-MM-DD' . "\n\n" .
                        'Contoh: 2026-02-10'
                ], 400);
            }

            // Validasi ID harus numerik
            if (!is_numeric($id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID harus berupa angka.'
                ], 400);
            }

            $type = $prefixMapping[$prefix];

            Log::info("Manual Code Scan - Prefix: {$prefix}, PO: {$po}, Date: {$date}, ID: {$id}, Type: {$type}");
        }

        // Validasi type dan id tidak boleh kosong
        if (!$type || !$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format kode tidak valid. Type atau ID tidak ditemukan.'
            ], 400);
        }

        // Mapping semua QC type dengan konfigurasi masing-masing
        $qcMapping = [
            'pelarutan-1' => [
                'model' => Pelarutan1::class,
                'route' => 'pelarutan-1.show_batch',
                'name'  => 'Pelarutan 1'
            ],
            'pelarutan-2' => [
                'model' => Pelarutan2::class,
                'route' => 'pelarutan-2.show_batch',
                'name'  => 'Pelarutan 2'
            ],
            'blending-awal' => [
                'model' => BlendingAwal::class,
                'route' => 'analisa.blending-awal.show_batch',
                'name'  => 'Blending Awal'
            ],
            'blending-awal-mikro' => [
                'model' => BlendingAfterAdjustMikro::class,
                'route' => 'analisa.blending-awal-mikro.show_batch',
                'name'  => 'Blending Awal Mikro'
            ],
            'monitoring-turun-blending' => [
                'model' => MonitoringTurunBlending::class,
                'route' => 'analisa.monitoring-turun-blending.show_batch',
                'name'  => 'Monitoring Turun Blending'
            ],
            'monitoring-pasteurisasi' => [
                'model' => MonitoringPasteurisasi::class,
                'route' => 'analisa.monitoring-pasteurisasi.show_batch',
                'name'  => 'Monitoring Pasteurisasi'
            ],
            'monitoring-storage-kimia' => [
                'model' => MonitoringStorageKimia::class,
                'route' => 'analisa.monitoring-storage-kimia.show_batch',
                'name'  => 'Monitoring Storage Kimia'
            ],
            'monitoring-storage-mikro' => [
                'model' => MonitoringStorageMikro::class,
                'route' => 'analisa.monitoring-storage-mikro.show_batch',
                'name'  => 'Monitoring Storage Mikro'
            ],
            'monitoring-storage-before-use' => [
                'model' => MonitoringStorageBeforeUse::class,
                'route' => 'monitoring-storage-before-use.analisa',
                'name'  => 'Monitoring Storage Before Use'
            ],
            'monitoring-daily-tank-kimia' => [
                'model' => MonitoringDailyTank::class,
                'route' => 'analisa.monitoring-daily-tank-kimia.show',
                'name'  => 'Monitoring Daily Tank - Kimia'
            ],
            'monitoring-daily-tank-mikro' => [
                'model' => MonitoringDailyTank::class,
                'route' => 'analisa.monitoring-daily-tank-mikro.show',
                'name'  => 'Monitoring Daily Tank - Mikro'
            ],
            'monitoring-ongoing-kimia' => [
                'model' => MonitoringOnGoingKimia::class,
                'route' => 'monitoring-ongoing-kimia.analisa',
                'name'  => 'Monitoring Ongoing - Kimia'
            ],
            'monitoring-ongoing-mikro' => [
                'model' => MonitoringOnGoingMikro::class,
                'route' => 'monitoring-ongoing-mikro.analisa',
                'name'  => 'Monitoring Ongoing - Mikro'
            ],
            'shelf-life-sampling' => [
                'model' => ShelfLifeSamplingDetail::class,
                'name'  => 'Shelf Life Analisis',
                'route_resolver' => function ($id, $qcData) {
                    $userRole = auth()->user()->role;

                    $roleRouteMap = [
                        'Analis Kimia' => 'shelf-life.analysis-kimia.show',
                        'Analis Mikro' => 'shelf-life.analysis-mikro.show',
                    ];

                    $shelfLifeSamplingId = $qcData->shelf_life_sampling_detail_id ?? $id;

                    return [
                        'route' => $roleRouteMap[$userRole] ?? null,
                        'id' => $shelfLifeSamplingId
                    ];
                },
                'validate_before_scan' => function ($qcData) {
                    if (!$qcData->is_checked) {
                        return [
                            'valid' => false,
                            'message' => 'Sample belum di-checklist. Silakan checklist terlebih dahulu.',
                            'redirect_url' => route('shelf-life.checksheet.index')
                        ];
                    }
                    return ['valid' => true];
                },
                'update_scanned_resolver' => function ($id, $qcData) {
                    $userRole = auth()->user()->role;

                    $modelClass = $userRole === 'Analis Kimia'
                        ? ShelfLifeSamplingKimia::class
                        : ShelfLifeSamplingMikro::class;

                    if ($qcData->is_checked) {
                        $modelClass::updateOrCreate(
                            ['shelf_life_sampling_detail_id' => $id],
                            ['scanned_at' => Carbon::now()]
                        );
                    }
                }
            ],
        ];

        // Validasi apakah type ada di mapping
        if (!isset($qcMapping[$type])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipe QC tidak dikenali.'
            ], 400);
        }

        // Validasi akses berdasarkan role user
        $userRole = auth()->user()->role;
        $accessValidation = $this->validateAccess($type, $userRole);

        if (!$accessValidation['valid']) {
            Log::warning("Access Denied - User: {$userRole}, Type: {$type}, ID: {$id}");
            return response()->json([
                'status' => 'error',
                'message' => $accessValidation['message']
            ], 403);
        }

        $config = $qcMapping[$type];

        try {
            DB::beginTransaction();

            // Resolve model class jika ada custom resolver
            if (isset($config['model_resolver']) && is_callable($config['model_resolver'])) {
                $modelClass = $config['model_resolver']();
            } else {
                $modelClass = $config['model'];
            }

            // Cari data berdasarkan ID
            $qcData = $modelClass::findOrFail($id);

            // Validasi khusus sebelum scan (jika ada)
            if (isset($config['validate_before_scan']) && is_callable($config['validate_before_scan'])) {
                $validation = $config['validate_before_scan']($qcData);

                if (!$validation['valid']) {
                    DB::rollBack();
                    Log::warning("Validation Failed - Type: {$type}, ID: {$id}, Reason: {$validation['message']}");
                    return response()->json([
                        'status' => 'error',
                        'message' => $validation['message'],
                        'redirect_url' => $validation['redirect_url'] ?? null
                    ], 403);
                }
            }

            // Resolve route name dan redirect ID (jika ada custom resolver)
            if (isset($config['route_resolver']) && is_callable($config['route_resolver'])) {
                $routeData = $config['route_resolver']($id, $qcData);
                $routeName = $routeData['route'];
                $redirectId = $routeData['id'];
            } else {
                $routeName = $config['route'];
                $redirectId = $id;
            }

            // Validasi route harus ada
            if (!$routeName) {
                DB::rollBack();
                Log::error("Route not found for type: {$type}, User Role: {$userRole}");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Route tidak ditemukan untuk role Anda.'
                ], 500);
            }

            // Simpan log scan ke database
            QRScanLog::create([
                'qc_type'    => $type,
                'qc_id'      => $id,
                'scanned_at' => Carbon::now(),
                'user_id'    => auth()->id(),
            ]);

            // Update scanned_at pada data QC
            try {
                if (isset($config['update_scanned_resolver']) && is_callable($config['update_scanned_resolver'])) {
                    // Jika ada custom resolver untuk update (contoh: shelf-life)
                    $config['update_scanned_resolver']($id, $qcData);
                } else {
                    // Update scanned_at default (hanya jika belum pernah di-scan)
                    $modelClass::where('id', $id)
                        ->whereNull('scanned_at')
                        ->update([
                            'scanned_at' => Carbon::now(),
                        ]);
                }

                Log::info("Scan Success - Type: {$type}, ID: {$id}, User: " . auth()->user()->name);
            } catch (\Exception $e) {
                // Jika table tidak punya kolom scanned_at, skip saja (tidak perlu rollback)
                Log::info("scanned_at update skipped for {$type}: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'status'       => 'success',
                'redirect_url' => route($routeName, $redirectId),
                'name'         => $config['name'],
                'qc_type'      => $type,
                'qc_id'        => $id,
                'message'      => "{$config['name']} #{$id} berhasil dipindai."
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("Scan Error - Data not found: Type: {$type}, ID: {$id}");
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan. Pastikan ID yang Anda masukkan benar.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Scan Error: " . $e->getMessage() . " | Type: {$type}, ID: {$id}");
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses data: ' . $e->getMessage()
            ], 500);
        }
    }
}
