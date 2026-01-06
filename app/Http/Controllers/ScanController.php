<?php

namespace App\Http\Controllers;

use App\Models\BlendingAfterAdjustMikro;
use App\Models\BlendingAwal;
use App\Models\GGA;
use App\Models\GGAS;
use App\Models\MonitoringDailyTank;
use App\Models\MonitoringOnGoingKimia;
use App\Models\MonitoringOnGoingMikro;
use App\Models\MonitoringPasteurisasi;
use App\Models\MonitoringStorageBeforeUse;
use App\Models\MonitoringStorageKimia;
use App\Models\MonitoringStorageMikro;
use App\Models\MonitoringTurunBlending;
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
    public function index()
    {
        return view('app.scan.index');
    }

    public function store(Request $request)
    {
        $fullUrl = $request->url;
        $path = parse_url($fullUrl, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $type = $segments[count($segments) - 2] ?? null;
        $id   = $segments[count($segments) - 1] ?? null;

        $qcMapping = [
            'gga' => [
                'model' => GGA::class,
                'route' => 'gga.show_batch',
                'name'  => 'GGA'
            ],
            'ggas' => [
                'model' => GGAS::class,
                'route' => 'ggas.show_batch',
                'name'  => 'GGAS'
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
                'update_scanned_resolver' => function ($id, $qcData) {
                    $userRole = auth()->user()->role;

                    $shelfLifeSamplingId = $qcData->shelf_life_sampling_detail_id;

                    $modelClass = $userRole === 'Analis Kimia'
                        ? ShelfLifeSamplingKimia::class
                        : ShelfLifeSamplingMikro::class;

                    $modelClass::updateOrCreate(
                        ['shelf_life_sampling_detail_id' => $id],
                        ['scanned_at' => Carbon::now()]
                    );
                }
            ],

        ];

        if (!isset($qcMapping[$type])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipe QC tidak dikenali.'
            ], 400);
        }

        $config = $qcMapping[$type];

        try {
            DB::beginTransaction();

            if (isset($config['model_resolver']) && is_callable($config['model_resolver'])) {
                $modelClass = $config['model_resolver']();
            } else {
                $modelClass = $config['model'];
            }

            $qcData = $modelClass::findOrFail($id);

            if (isset($config['route_resolver']) && is_callable($config['route_resolver'])) {
                $routeData = $config['route_resolver']($id, $qcData);
                $routeName = $routeData['route'];
                $redirectId = $routeData['id'];
            } else {
                $routeName = $config['route'];
                $redirectId = $id;
            }

            QRScanLog::create([
                'qc_type'    => $type,
                'qc_id'      => $id,
                'scanned_at' => Carbon::now(),
                'user_id'    => auth()->id(),
            ]);

            try {
                if (isset($config['update_scanned_resolver']) && is_callable($config['update_scanned_resolver'])) {
                    $config['update_scanned_resolver']($id, $qcData);
                } else {
                    $modelClass::where('id', $id)
                        ->whereNull('scanned_at')
                        ->update([
                            'scanned_at' => Carbon::now(),
                        ]);
                }
            } catch (\Exception $e) {
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
            Log::error("Scan Error - Data not found: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Scan Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses data: ' . $e->getMessage()
            ], 500);
        }
    }
}
