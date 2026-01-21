<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MonitoringStorageMikro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringStorageMikroController extends Controller
{
    public function analisaMonitoringStorageMikro(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $variant = $request->input('variant');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $query = MonitoringStorageMikro::with(['productionBatch:id,po_number,variant'])
            ->select(
                'batch_range',
                'nomor_blending',
                'volume',
                'eb',
                'tpc',
                'ym',
                'hasil',
                'production_batch_id',
                'created_at'
            )
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by variant if provided
        if ($variant) {
            $query->whereHas('productionBatch', function ($q) use ($variant) {
                $q->where('variant', $variant);
            });
        }

        $rawData = $query->orderBy('created_at')->get();

        $filtered = $rawData
            ->map(function ($item) {
                return [
                    'batch_range' => $item->batch_range,
                    'nomor_blending' => $item->nomor_blending,
                    'volume' => $item->volume,
                    'eb' => $item->eb,
                    'tpc' => $item->tpc,
                    'ym' => $item->ym,
                    'hasil' => $item->hasil,
                    'created_at' => $item->created_at,
                    'po_number' => optional($item->productionBatch)->po_number,
                    'variant' => optional($item->productionBatch)->variant
                ];
            });

        return response()->json(['monitoring_storage_mikro' => $filtered]);
    }

    public function analisaDisposisi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $variant = $request->input('variant');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $query = MonitoringStorageMikro::whereNotNull('hasil')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($variant) {
            $query->whereHas('productionBatch', function ($q) use ($variant) {
                $q->where('variant', $variant);
            });
        }

        $dispositions = $query->get()
            ->groupBy('hasil')
            ->map(fn($group) => $group->count());

        return response()->json([
            'disposition_summary' => $dispositions,
        ]);
    }
}
