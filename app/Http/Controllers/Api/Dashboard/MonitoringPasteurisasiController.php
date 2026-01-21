<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MonitoringPasteurisasi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringPasteurisasiController extends Controller
{
    public function analisaMonitoringPasteurisasi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfDay();
        $endDate   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $query = MonitoringPasteurisasi::with([
            'productionBatch:id,po_number,variant'
        ])->whereBetween('created_at', [$startDate, $endDate]);

        if ($variant) {
            $query->whereHas('productionBatch', function ($q) use ($variant) {
                $q->where('variant', $variant);
            });
        }

        $rawMonitorings = $query->orderBy('revisi', 'desc')->get();

        $filteredMonitorings = $rawMonitorings
            ->groupBy(fn($item) => $item->batch_range . '__' . $item->nomor_blending)
            ->map(fn($group) => $group->first())
            ->values();

        $flattened = [];
        foreach ($filteredMonitorings as $monitoring) {
            $flattened[] = [
                'nomor_blending' => $monitoring->nomor_blending,
                'batch_range'    => $monitoring->batch_range,
                'shift'          => $monitoring->shift,
                'brix'           => $monitoring->brix,
                'nacl'           => $monitoring->nacl,
                'bj'             => $monitoring->bj,
                'visco'          => $monitoring->visco,
                'aw'             => $monitoring->aw,
                'buih'           => $monitoring->buih,
                'organo'         => $monitoring->organo,
                'ph'             => $monitoring->ph,
                'revisi'         => $monitoring->revisi,
                'created_at'     => $monitoring->created_at,
                'po_number'      => optional($monitoring->productionBatch)->po_number,
                'variant'        => optional($monitoring->productionBatch)->variant
            ];
        }

        return response()->json([
            'monitoring_pasteurisasi' => $flattened
        ]);
    }

    public function analisaDisposisi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfDay();
        $endDate   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $query = MonitoringPasteurisasi::whereNotNull('disposition')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($variant) {
            $query->whereHas('productionBatch', function ($q) use ($variant) {
                $q->where('variant', $variant);
            });
        }

        $dispositions = $query->get()
            ->groupBy('disposition')
            ->map(fn($group) => $group->count());

        return response()->json([
            'disposition_summary' => $dispositions,
        ]);
    }
}
