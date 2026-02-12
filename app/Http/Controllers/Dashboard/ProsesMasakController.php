<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionBatch;
use Carbon\Carbon;

class ProsesMasakController extends Controller
{
    public function index(Request $request)
    {
        $variant = $request->input('variant');
        $formulasi = $request->input('formulasi');
        $month = $request->input('month');
        $week = $request->input('week');
        $date = $request->input('date');

        $today = Carbon::now()->format('Y-m-d');
        if (!$variant && !$formulasi && !$month && !$week && !$date) {
            $date = $today;
        }

        $variants = ProductionBatch::select('variant')
            ->distinct()
            ->whereNotNull('variant')
            ->orderBy('variant')
            ->pluck('variant');

        $availableMonths = ProductionBatch::selectRaw('DATE_FORMAT(date, "%Y-%m") as month_key, DATE_FORMAT(date, "%M %Y") as month_label')
            ->distinct()
            ->whereNotNull('date')
            ->orderBy('month_key', 'desc')
            ->get()
            ->pluck('month_label', 'month_key');

        $weeks = [];
        $selectedMonth = $month;

        if ($selectedMonth) {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();

            $datesInMonth = ProductionBatch::whereYear('date', $monthDate->year)
                ->whereMonth('date', $monthDate->month)
                ->select('date')
                ->distinct()
                ->orderBy('date')
                ->pluck('date');

            $weekGroups = $datesInMonth->groupBy(function ($date) {
                return Carbon::parse($date)->weekOfMonth;
            });

            foreach ($weekGroups as $weekNum => $dates) {
                $weekLabel = "Week {$weekNum} ({$monthDate->format('M Y')})";
                $weeks[$selectedMonth . '-W' . $weekNum] = $weekLabel;
            }
        }

        return view('dashboard.proses-masak.index', compact(
            'variants',
            'availableMonths',
            'weeks',
            'variant',
            'formulasi',
            'month',
            'week',
            'date'
        ));
    }
}
