<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GGA;
use App\Models\GGAS;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GgaGgasController extends Controller
{
    public function analisaGgaGgas(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        // === GGA Data ===
        $ggaQuery = GGA::with(['productionBatch:id,po_number,variant'])
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

        $ggaData = $ggaQuery->orderBy('created_at')
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

        // === GGAS Data ===
        $ggasQuery = GGAS::with(['productionBatch:id,po_number,variant'])
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

        $ggasData = $ggasQuery->orderBy('created_at')
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
            'gga'  => $ggaData,
            'ggas' => $ggasData,
        ]);
    }

    public function analisaDisposisi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $variant   = $request->input('variant');

        // GGA analysis
        $ggaQuery = GGA::whereNotNull('disposition')
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

        $ggaDispositions = $ggaQuery->get()
            ->groupBy('disposition')
            ->map(fn($group) => $group->count());

        // GGAS analysis
        $ggasQuery = GGAS::whereNotNull('disposition')
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

        $ggasDispositions = $ggasQuery->get()
            ->groupBy('disposition')
            ->map(fn($group) => $group->count());

        return response()->json([
            'gga'  => $ggaDispositions,
            'ggas' => $ggasDispositions,
        ]);
    }
}
