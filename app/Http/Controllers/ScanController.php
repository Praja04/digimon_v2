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
        ];

        if (!isset($qcMapping[$type])) {
            return response()->json(['status' => 'error', 'message' => 'Tipe QC tidak dikenali.'], 400);
        }

        $config = $qcMapping[$type];

        try {
            DB::beginTransaction();

            // 1. Cek keberadaan data QC
            $config['model']::findOrFail($id);

            // 2. Catat Log Scan
            QRScanLog::create([
                'qc_type'           => $type,
                'qc_id'             => $id,
                'scanned_at'        => Carbon::now(),
                'user_id'           => auth()->id(),
            ]);

            // 3. Update scanned_at di model QC
            $config['model']::where('id', $id)
                ->whereNull('scanned_at')
                ->update([
                    'scanned_at' => Carbon::now(),
                ]);

            DB::commit();

            // 4. Kembalikan URL Redirect
            return response()->json([
                'status'       => 'success',
                'redirect_url' => route($config['route'],  $id),
                'name'         => $config['name'],
                'qc_type'      => $type,
                'qc_id'        => $id,
                'message'      => "{$type} #{$id} berhasil dipindai."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Scan Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses data.'], 500);
        }
    }
}
