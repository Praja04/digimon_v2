<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlendingAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlendingAwalController extends Controller
{

    private function baseQuery($startDate, $endDate, $variant = null)
    {
        $query = BlendingAwal::with(['productionBatch:id,po_number,variant'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($variant) {
            $query->whereHas('productionBatch', function ($q) use ($variant) {
                $q->where('variant', $variant);
            });
        }

        return $query;
    }

    private function classifyData($rawData)
    {
        $grouped = $rawData->groupBy(fn($item) => $item->production_batch_id . '_' . $item->batch_range);

        $blendingAwal = collect();
        $blendingAfterAdjust = collect();

        foreach ($grouped as $group) {
            $awal = $group->firstWhere('revisi', null) ?? $group->firstWhere('revisi', 0);

            if ($awal) {
                $blendingAwal->push($awal);

                $revisis = $group->where('revisi', '>', 0)->sortByDesc('revisi')->values();

                foreach ($revisis as $rev) {
                    if ($awal->disposition === 'Adjustment') {
                        $blendingAfterAdjust->push($rev);
                    } else {
                        $blendingAwal->push($rev);
                    }
                }
            }
        }

        return [$blendingAwal, $blendingAfterAdjust];
    }

    private function transformData($collection)
    {
        return $collection->map(function ($item) {
            return [
                'id'                   => $item->id,
                'production_batch_id'  => $item->production_batch_id,
                'batch_range'          => $item->batch_range,
                'disposition'          => $item->disposition,
                'created_at'           => $item->created_at,
                'nomor_blending'       => $item->nomor_blending,
                'volume'               => $item->volume,
                'brix'                 => $item->brix,
                'nacl'                 => $item->nacl,
                'bj'                   => $item->bj,
                'visco'                => $item->visco,
                'aw'                   => $item->aw,
                'buih'                 => $item->buih,
                'organo'               => $item->organo,
                'ph'                   => $item->ph,
                'endapan'              => $item->endapan,
                'warna'                => $item->warna,
                'adjustment_qty_air'   => $item->adjustment_qty_air,
                'adjustment_qty_garam' => $item->adjustment_qty_garam,
                'adjustment_qty_gula'  => $item->adjustment_qty_gula,
                'disposition_remarks'  => $item->disposition_remarks,
                'revisi'               => $item->revisi,
                'is_adjustment'        => $item->is_adjustment,
                'updated_at'           => $item->updated_at,
                'not_standar'          => $item->not_standar,
                'created_by'           => $item->created_by,
                'po_number'            => $item->productionBatch->po_number ?? null,
                'variant'              => $item->productionBatch->variant ?? null,
            ];
        });
    }

    private function getDateRange(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        return [$startDate, $endDate];
    }

    public function analisaBlendingAwal(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        $variant = $request->input('variant');

        $rawData = $this->baseQuery($startDate, $endDate, $variant)->get();
        [$blendingAwal] = $this->classifyData($rawData);

        return response()->json([
            'blending_awal' => $this->transformData($blendingAwal)->values()
        ]);
    }

    public function analisaDisposisi(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        $variant = $request->input('variant');

        $rawData = $this->baseQuery($startDate, $endDate, $variant)->get();
        [$blendingAwal] = $this->classifyData($rawData);

        $dispositions = $blendingAwal->whereNotNull('disposition')
            ->groupBy('disposition')
            ->map(fn($g) => $g->count());

        return response()->json([
            'disposition_summary' => $dispositions,
            'total_batches'       => $blendingAwal->count()
        ]);
    }
}
