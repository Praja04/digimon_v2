<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PelarutanController extends Controller
{
    public function analisaPelarutan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        // === Pelarutan 1 Data ===
        $pelarutan1Query = Pelarutan1::with(['productionBatch:id,po_number,variant'])
            ->select('id', 'batch_number', 'brix', 'nacl', 'production_batch_id', 'created_at')
            ->whereNotNull('brix')
            ->whereNotNull('nacl')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->when($variant, function ($q) use ($variant) {
                $q->whereHas('productionBatch', function ($sub) use ($variant) {
                    $sub->where('variant', $variant);
                });
            });

        $pelarutan1Data = $pelarutan1Query->orderBy('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'batch_number'        => $item->batch_number,
                    'brix'                => $item->brix,
                    'nacl'                => $item->nacl,
                    'production_batch_id' => $item->production_batch_id,
                    'po_number'           => optional($item->productionBatch)->po_number,
                    'variant'             => optional($item->productionBatch)->variant,
                    'created_at'          => $item->created_at->toDateTimeString(),
                ];
            });

        // === Pelarutan 2 Data ===
        $pelarutan2Query = Pelarutan2::with(['productionBatch:id,po_number,variant'])
            ->select('id', 'batch_number', 'brix', 'nacl', 'production_batch_id', 'created_at')
            ->whereNotNull('brix')
            ->whereNotNull('nacl')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->when($variant, function ($q) use ($variant) {
                $q->whereHas('productionBatch', function ($sub) use ($variant) {
                    $sub->where('variant', $variant);
                });
            });

        $pelarutan2Data = $pelarutan2Query->orderBy('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'batch_number'        => $item->batch_number,
                    'brix'                => $item->brix,
                    'nacl'                => $item->nacl,
                    'production_batch_id' => $item->production_batch_id,
                    'po_number'           => optional($item->productionBatch)->po_number,
                    'variant'             => optional($item->productionBatch)->variant,
                    'created_at'          => $item->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'pelarutan1'  => $pelarutan1Data,
            'pelarutan2' => $pelarutan2Data,
        ]);
    }

    public function analisaDisposisi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        // Pelarutan 1 analysis
        $pelarutan1Query = Pelarutan1::whereNotNull('disposition')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->when($variant, function ($q) use ($variant) {
                $q->whereHas('productionBatch', function ($sub) use ($variant) {
                    $sub->where('variant', $variant);
                });
            });

        $pelarutan1Dispositions = $pelarutan1Query->get()
            ->groupBy('disposition')
            ->map(fn($group) => $group->count());

        // Pelarutan 2 analysis
        $pelarutan2Query = Pelarutan2::whereNotNull('disposition')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->when($variant, function ($q) use ($variant) {
                $q->whereHas('productionBatch', function ($sub) use ($variant) {
                    $sub->where('variant', $variant);
                });
            });

        $pelarutan2Dispositions = $pelarutan2Query->get()
            ->groupBy('disposition')
            ->map(fn($group) => $group->count());

        return response()->json([
            'pelarutan1'  => $pelarutan1Dispositions,
            'pelarutan2' => $pelarutan2Dispositions,
        ]);
    }
}
