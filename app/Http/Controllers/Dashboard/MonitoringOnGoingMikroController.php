<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;

class MonitoringOnGoingMikroController extends Controller
{
    public function index(Request $request)
    {
        $variants = ProductionBatch::select('variant')
            ->distinct()
            ->whereNotNull('variant')
            ->where('variant', '!=', '')
            ->orderBy('variant')
            ->pluck('variant')
            ->toArray();

        return view('dashboard.monitoring_on_going_mikro.index', compact('variants'));
    }
}
