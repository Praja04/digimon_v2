<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MonitoringOnGoingMikro;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringOnGoingMikroController extends Controller
{
    /**
     * Get dashboard data
     */
    public function getData(Request $request)
    {
        // Get filter parameters
        $selectedWeek = $request->input('week', $this->getCurrentWeek());
        $selectedVariants = $request->input('variants', []);

        // Get all variants if none selected
        if (empty($selectedVariants)) {
            $selectedVariants = $this->getAllVariants();
        }

        // Parse selected week
        $weekDates = $this->parseWeek($selectedWeek);

        // Get all data
        $data = [
            'summary' => $this->getSummaryData($weekDates, $selectedVariants),
            'weeklySummary' => $this->getWeeklySummary($weekDates, $selectedVariants),
            'chartData' => $this->getChartData($weekDates, $selectedVariants)
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get available weeks
     */
    public function getWeeks()
    {
        $weeks = [];
        $currentDate = Carbon::now();

        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subWeeks($i);
            $weekNumber = $date->week;
            $year = $date->year;
            $weekValue = $date->format('Y-\WW');

            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();

            $weeks[] = [
                'value' => $weekValue,
                'week_number' => $weekNumber,
                'year' => $year,
                'date_range' => $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y')
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $weeks
        ]);
    }

    /**
     * Get current week in format YYYY-Www
     */
    private function getCurrentWeek()
    {
        return Carbon::now()->format('Y-\WW');
    }

    /**
     * Parse week string to get start and end dates
     */
    private function parseWeek($weekString)
    {
        // Format: YYYY-Www (e.g., 2026-W07)
        $parts = explode('-W', $weekString);

        // Validate week format
        if (count($parts) !== 2) {
            // If invalid format, use current week
            $weekString = $this->getCurrentWeek();
            $parts = explode('-W', $weekString);
        }

        $year = $parts[0];
        $week = $parts[1];

        $date = Carbon::now()->setISODate($year, $week);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return [
            'start' => $startOfWeek,
            'end' => $endOfWeek,
            'week_number' => $week,
            'year' => $year
        ];
    }

    /**
     * Get all variants from production_batches
     */
    private function getAllVariants()
    {
        return ProductionBatch::select('variant')
            ->distinct()
            ->whereNotNull('variant')
            ->where('variant', '!=', '')
            ->orderBy('variant')
            ->pluck('variant')
            ->toArray();
    }

    /**
     * Get summary data for selected week and variants
     */
    private function getSummaryData($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->whereHas('productionBatch', function ($query) use ($variants) {
                $query->whereIn('variant', $variants);
            })
            ->whereBetween('filling_date', [$weekDates['start'], $weekDates['end']])
            ->selectRaw('
                COUNT(*) as total_sample,
                MAX(tpc) as tpc_max,
                MAX(ym) as ym_max,
                SUM(CASE WHEN hasil = "NOT OK" THEN 1 ELSE 0 END) as ng_count
            ')
            ->first();

        $ngPercentage = $data->total_sample > 0
            ? ($data->ng_count / $data->total_sample) * 100
            : 0;

        return [
            'total_sample' => $data->total_sample ?? 0,
            'tpc_max' => $data->tpc_max ?? 0,
            'ym_max' => $data->ym_max ?? 0,
            'ng_count' => $data->ng_count ?? 0,
            'ng_percentage' => $ngPercentage
        ];
    }

    /**
     * Get weekly summary grouped by variant
     */
    private function getWeeklySummary($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
            ->whereBetween('monitoring_on_going_mikro.filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereIn('pb.variant', $variants)
            ->groupBy('pb.variant')
            ->orderBy('pb.variant')
            ->selectRaw('
                pb.variant,
                COUNT(*) as total_sample,
                MAX(monitoring_on_going_mikro.tpc) as tpc_max,
                MAX(monitoring_on_going_mikro.ym) as ym_max,
                MAX(monitoring_on_going_mikro.eb) as eb_max,
                SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NOT OK" THEN 1 ELSE 0 END) as ng_count
            ')
            ->get();

        return $data->map(function ($item) {
            $ngPercentage = $item->total_sample > 0
                ? ($item->ng_count / $item->total_sample) * 100
                : 0;

            return [
                'variant' => $item->variant,
                'total_sample' => $item->total_sample,
                'tpc_max' => $item->tpc_max ?? 0,
                'ym_max' => $item->ym_max ?? 0,
                'eb_max' => $item->eb_max ?? 0,
                'ng_count' => $item->ng_count,
                'ng_percentage' => $ngPercentage
            ];
        })->toArray();
    }

    /**
     * Get all chart data
     */
    private function getChartData($weekDates, $variants)
    {
        return [
            'dayToDayFilling' => $this->getDayToDayFilling($weekDates, $variants),
            'dayToDayProduction' => $this->getDayToDayProduction($weekDates, $variants),
            'ngWeekByWeek' => $this->getNgWeekByWeek($variants),
            'ngPerVariant' => $this->getNgPerVariant($variants),
            'histogramTpc' => $this->getHistogramTpc($weekDates, $variants),
            'histogramYm' => $this->getHistogramYm($weekDates, $variants),
            'frekMicroSample' => $this->getFrekMicroSample($weekDates, $variants),
            'fillingMachineReview' => $this->getFillingMachineReview($weekDates, $variants)
        ];
    }

    /**
     * Day-to-Day by Filling Date
     */
    private function getDayToDayFilling($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
            ->whereBetween('monitoring_on_going_mikro.filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereIn('pb.variant', $variants)
            ->groupBy(DB::raw('DATE(monitoring_on_going_mikro.filling_date)'))
            ->orderBy(DB::raw('DATE(monitoring_on_going_mikro.filling_date)'))
            ->selectRaw('
                DATE(monitoring_on_going_mikro.filling_date) as date,
                SUM(CASE WHEN monitoring_on_going_mikro.tpc > 0 OR monitoring_on_going_mikro.ym > 0 OR monitoring_on_going_mikro.eb > 0 THEN 1 ELSE 0 END) as frek_micro,
                SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NOT OK" THEN 1 ELSE 0 END) as frek_ng
            ')
            ->get();

        $labels = [];
        $frekMicro = [];
        $frekNG = [];

        foreach ($data as $item) {
            $labels[] = Carbon::parse($item->date)->format('d/m');
            $frekMicro[] = $item->frek_micro;
            $frekNG[] = $item->frek_ng;
        }

        return [
            'labels' => $labels,
            'frekMicro' => $frekMicro,
            'frekNG' => $frekNG
        ];
    }

    /**
     * Day-to-Day by Production Date
     */
    private function getDayToDayProduction($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
            ->whereBetween('pb.date', [$weekDates['start'], $weekDates['end']])
            ->whereIn('pb.variant', $variants)
            ->groupBy(DB::raw('DATE(pb.date)'))
            ->orderBy(DB::raw('DATE(pb.date)'))
            ->selectRaw('
                DATE(pb.date) as date,
                SUM(CASE WHEN monitoring_on_going_mikro.tpc > 0 OR monitoring_on_going_mikro.ym > 0 OR monitoring_on_going_mikro.eb > 0 THEN 1 ELSE 0 END) as frek_micro,
                SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NOT OK" THEN 1 ELSE 0 END) as frek_ng
            ')
            ->get();

        $labels = [];
        $frekMicro = [];
        $frekNG = [];

        foreach ($data as $item) {
            $labels[] = Carbon::parse($item->date)->format('d/m');
            $frekMicro[] = $item->frek_micro;
            $frekNG[] = $item->frek_ng;
        }

        return [
            'labels' => $labels,
            'frekMicro' => $frekMicro,
            'frekNG' => $frekNG
        ];
    }

    /**
     * % NG Week by Week (last 8 weeks)
     */
    private function getNgWeekByWeek($variants)
    {
        $labels = [];
        $data = [];

        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $weekDates = $this->parseWeek($date->format('Y-\WW'));

            $result = MonitoringOnGoingMikro::query()
                ->whereHas('productionBatch', function ($query) use ($variants) {
                    $query->whereIn('variant', $variants);
                })
                ->whereBetween('filling_date', [$weekDates['start'], $weekDates['end']])
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN hasil = "NOT OK" THEN 1 ELSE 0 END) as ng_count
                ')
                ->first();

            $labels[] = 'Week ' . $weekDates['week_number'];
            $ngPercentage = $result->total > 0 ? ($result->ng_count / $result->total) * 100 : 0;
            $data[] = round($ngPercentage, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * % NG Per Variant Week by Week
     */
    private function getNgPerVariant($variants)
    {
        $labels = [];
        $variantData = [];

        // Initialize variant data structure
        foreach ($variants as $variant) {
            $variantData[$variant] = [];
        }

        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $weekDates = $this->parseWeek($date->format('Y-\WW'));
            $labels[] = 'Week ' . $weekDates['week_number'];

            $results = MonitoringOnGoingMikro::query()
                ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
                ->whereBetween('monitoring_on_going_mikro.filling_date', [$weekDates['start'], $weekDates['end']])
                ->whereIn('pb.variant', $variants)
                ->groupBy('pb.variant')
                ->selectRaw('
                    pb.variant,
                    COUNT(*) as total,
                    SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NOT OK" THEN 1 ELSE 0 END) as ng_count
                ')
                ->get();

            foreach ($variants as $variant) {
                $result = $results->firstWhere('variant', $variant);
                if ($result && $result->total > 0) {
                    $ngPercentage = ($result->ng_count / $result->total) * 100;
                    $variantData[$variant][] = round($ngPercentage, 2);
                } else {
                    $variantData[$variant][] = 0;
                }
            }
        }

        $formattedVariants = [];
        foreach ($variantData as $name => $data) {
            $formattedVariants[] = [
                'name' => $name,
                'data' => $data
            ];
        }

        return [
            'labels' => $labels,
            'variants' => $formattedVariants
        ];
    }

    /**
     * Histogram TPC
     */
    private function getHistogramTpc($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->whereHas('productionBatch', function ($query) use ($variants) {
                $query->whereIn('variant', $variants);
            })
            ->whereBetween('filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereNotNull('tpc')
            ->pluck('tpc')
            ->toArray();

        return $this->createHistogram($data, 'TPC');
    }

    /**
     * Histogram YM
     */
    private function getHistogramYm($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->whereHas('productionBatch', function ($query) use ($variants) {
                $query->whereIn('variant', $variants);
            })
            ->whereBetween('filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereNotNull('ym')
            ->pluck('ym')
            ->toArray();

        return $this->createHistogram($data, 'YM');
    }

    /**
     * Create histogram from data
     */
    private function createHistogram($data, $type)
    {
        if (empty($data)) {
            return ['labels' => [], 'data' => []];
        }

        $max = max($data);
        $binSize = $max > 0 ? ceil($max / 10) : 1; // 10 bins

        $bins = [];
        $counts = [];

        for ($i = 0; $i <= 10; $i++) {
            $rangeStart = $i * $binSize;
            $rangeEnd = ($i + 1) * $binSize;

            $count = count(array_filter($data, function ($value) use ($rangeStart, $rangeEnd) {
                return $value >= $rangeStart && $value < $rangeEnd;
            }));

            if ($count > 0 || $i < 5) { // Show at least first 5 bins
                $bins[] = $rangeStart . '-' . $rangeEnd;
                $counts[] = $count;
            }
        }

        return [
            'labels' => $bins,
            'data' => $counts
        ];
    }

    /**
     * Frek Micro by Sample Type (Storage)
     */
    private function getFrekMicroSample($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
            ->whereBetween('monitoring_on_going_mikro.filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereIn('pb.variant', $variants)
            ->groupBy('monitoring_on_going_mikro.storage')
            ->orderBy('monitoring_on_going_mikro.storage')
            ->selectRaw('
                monitoring_on_going_mikro.storage,
                SUM(CASE WHEN monitoring_on_going_mikro.tpc > 0 OR monitoring_on_going_mikro.ym > 0 OR monitoring_on_going_mikro.eb > 0 THEN 1 ELSE 0 END) as frek_micro,
                SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NOT OK" THEN 1 ELSE 0 END) as frek_ng
            ')
            ->get();

        $labels = [];
        $frekMicro = [];
        $frekNG = [];

        foreach ($data as $item) {
            $labels[] = $item->storage ?? 'Unknown';
            $frekMicro[] = $item->frek_micro;
            $frekNG[] = $item->frek_ng;
        }

        return [
            'labels' => $labels,
            'frekMicro' => $frekMicro,
            'frekNG' => $frekNG
        ];
    }

    /**
     * Filling Machine Review
     */
    private function getFillingMachineReview($weekDates, $variants)
    {
        $data = MonitoringOnGoingMikro::query()
            ->join('production_batches as pb', 'monitoring_on_going_mikro.production_batch_id', '=', 'pb.id')
            ->whereBetween('monitoring_on_going_mikro.filling_date', [$weekDates['start'], $weekDates['end']])
            ->whereIn('pb.variant', $variants)
            ->whereNotNull('monitoring_on_going_mikro.no_filler')
            ->groupBy('monitoring_on_going_mikro.no_filler')
            ->orderBy('monitoring_on_going_mikro.no_filler')
            ->selectRaw('
                monitoring_on_going_mikro.no_filler,
                SUM(CASE WHEN monitoring_on_going_mikro.tpc > 0 OR monitoring_on_going_mikro.ym > 0 OR monitoring_on_going_mikro.eb > 0 THEN 1 ELSE 0 END) as frek_micro,
                SUM(CASE WHEN monitoring_on_going_mikro.hasil = "NG" THEN 1 ELSE 0 END) as frek_ng
            ')
            ->get();

        $labels = [];
        $frekMicro = [];
        $frekNG = [];

        foreach ($data as $item) {
            $labels[] = 'Filler ' . ($item->no_filler ?? 'Unknown');
            $frekMicro[] = $item->frek_micro;
            $frekNG[] = $item->frek_ng;
        }

        return [
            'labels' => $labels,
            'frekMicro' => $frekMicro,
            'frekNG' => $frekNG
        ];
    }
}
