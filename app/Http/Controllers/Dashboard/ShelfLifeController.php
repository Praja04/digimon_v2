<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ShelfLifeSamples;
use App\Models\ShelfLifeSamplingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelfLifeController extends Controller
{
    public function index()
    {
        return view('dashboard.shelf_life.index');
    }

    public function getFilterOptions(Request $request)
    {
        $type = $request->type;

        switch ($type) {
            case 'variant':
                $data = ShelfLifeSamplingDetail::select('variant_fg')
                    ->distinct()
                    ->whereNotNull('variant_fg')
                    ->where('is_checked', true)
                    ->orderBy('variant_fg', 'asc')
                    ->pluck('variant_fg');
                break;

            case 'bulan_ke':
                $data = ShelfLifeSamplingDetail::select('bulan_ke')
                    ->distinct()
                    ->whereNotNull('bulan_ke')
                    ->where('is_checked', true)
                    ->orderBy('bulan_ke', 'asc')
                    ->pluck('bulan_ke');
                break;

            case 'stk':
                $data = ShelfLifeSamples::select('storage')
                    ->distinct()
                    ->whereNotNull('storage')
                    ->orderBy('storage', 'asc')
                    ->pluck('storage');
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    public function getChartData(Request $request)
    {
        $query = ShelfLifeSamplingDetail::with(['shelfLifeSample.productionBatch', 'shelfLifeSamplingKimia', 'shelfLifeSamplingMikro'])
            ->where('is_checked', true);

        $hasDateFilter = $request->filled('tanggal_produksi') ||
            $request->filled('tanggal_filling') ||
            $request->filled('bulan_filling') ||
            $request->filled('tahun_filling');

        if (!$hasDateFilter) {
            $query->whereDate('tanggal_filling', now()->toDateString());
        }

        if ($request->filled('variant_fg') && !empty($request->variant_fg)) {
            $query->whereIn('variant_fg', $request->variant_fg);
        }

        if ($request->filled('bulan_ke')) {
            $query->where('bulan_ke', $request->bulan_ke);
        }

        if ($request->filled('tanggal_produksi')) {
            $query->whereHas('shelfLifeSample.productionBatch', function ($q) use ($request) {
                $q->whereDate('date', $request->tanggal_produksi);
            });
        }
        if ($request->filled('stk')) {
            $query->whereHas('shelfLifeSample', function ($q) use ($request) {
                $q->where('storage', $request->stk);
            });
        }

        if ($request->filled('tanggal_filling')) {
            $query->whereDate('tanggal_filling', $request->tanggal_filling);
        }

        if ($request->filled('bulan_filling')) {
            $query->whereMonth('tanggal_filling', $request->bulan_filling);
        }

        if ($request->filled('tahun_filling')) {
            $query->whereYear('tanggal_filling', $request->tahun_filling);
        }

        $details = $query->orderBy('bulan_ke', 'asc')->get();

        $groupedData = $details->groupBy('bulan_ke');

        $bulanKe = [];

        // Kimia parameters
        $naclData = [];
        $brixData = [];
        $awData = [];
        $phData = [];
        $bjData = [];
        $buihData = [];
        $viscoData = [];
        $totalNitrogenData = [];

        // Mikro parameters
        $ebData = [];
        $saData = [];
        $tpcData = [];
        $ymData = [];

        foreach ($groupedData as $bulan => $items) {
            $bulanKe[] = $bulan;

            $naclValues = [];
            $brixValues = [];
            $awValues = [];
            $phValues = [];
            $bjValues = [];
            $buihValues = [];
            $viscoValues = [];
            $totalNitrogenValues = [];

            $ebValues = [];
            $saValues = [];
            $tpcValues = [];
            $ymValues = [];

            foreach ($items as $detail) {
                if ($detail->shelfLifeSamplingKimia) {
                    $kimia = $detail->shelfLifeSamplingKimia;

                    if (!is_null($kimia->nacl)) $naclValues[] = $kimia->nacl;
                    if (!is_null($kimia->brix)) $brixValues[] = $kimia->brix;
                    if (!is_null($kimia->aw)) $awValues[] = $kimia->aw;
                    if (!is_null($kimia->ph)) $phValues[] = $kimia->ph;
                    if (!is_null($kimia->bj)) $bjValues[] = $kimia->bj;
                    if (!is_null($kimia->buih)) $buihValues[] = $kimia->buih;
                    if (!is_null($kimia->visco)) $viscoValues[] = $kimia->visco;
                    if (!is_null($kimia->total_nitrogen)) $totalNitrogenValues[] = $kimia->total_nitrogen;
                }

                if ($detail->shelfLifeSamplingMikro) {
                    $mikro = $detail->shelfLifeSamplingMikro;

                    if (!is_null($mikro->eb)) $ebValues[] = $mikro->eb;
                    if (!is_null($mikro->sa)) $saValues[] = $mikro->sa;
                    if (!is_null($mikro->tpc)) $tpcValues[] = $mikro->tpc;
                    if (!is_null($mikro->ym)) $ymValues[] = $mikro->ym;
                }
            }

            $naclData[] = !empty($naclValues) ? round(array_sum($naclValues) / count($naclValues), 2) : null;
            $brixData[] = !empty($brixValues) ? round(array_sum($brixValues) / count($brixValues), 2) : null;
            $awData[] = !empty($awValues) ? round(array_sum($awValues) / count($awValues), 4) : null;
            $phData[] = !empty($phValues) ? round(array_sum($phValues) / count($phValues), 2) : null;
            $bjData[] = !empty($bjValues) ? round(array_sum($bjValues) / count($bjValues), 4) : null;
            $buihData[] = !empty($buihValues) ? round(array_sum($buihValues) / count($buihValues), 2) : null;
            $viscoData[] = !empty($viscoValues) ? round(array_sum($viscoValues) / count($viscoValues), 2) : null;
            $totalNitrogenData[] = !empty($totalNitrogenValues) ? round(array_sum($totalNitrogenValues) / count($totalNitrogenValues), 2) : null;

            $ebData[] = !empty($ebValues) ? round(array_sum($ebValues) / count($ebValues), 2) : null;
            $saData[] = !empty($saValues) ? round(array_sum($saValues) / count($saValues), 2) : null;
            $tpcData[] = !empty($tpcValues) ? round(array_sum($tpcValues) / count($tpcValues), 2) : null;
            $ymData[] = !empty($ymValues) ? round(array_sum($ymValues) / count($ymValues), 2) : null;
        }

        return response()->json([
            'bulan_ke' => $bulanKe,
            // Kimia
            'nacl' => $naclData,
            'brix' => $brixData,
            'aw' => $awData,
            'ph' => $phData,
            'bj' => $bjData,
            'buih' => $buihData,
            'visco' => $viscoData,
            'total_nitrogen' => $totalNitrogenData,
            // Mikro
            'eb' => $ebData,
            'sa' => $saData,
            'tpc' => $tpcData,
            'ym' => $ymData,
        ]);
    }

    public function getKelompokTanggal(Request $request)
    {
        $kelompokSample = $request->kelompok_sample;

        $kelompokTanggal = ShelfLifeSamplingDetail::select('kelompok_tanggal')
            ->distinct()
            ->whereNotNull('kelompok_tanggal')
            ->where('kelompok_sample', $kelompokSample)
            ->where('is_checked', true)
            ->orderBy('kelompok_tanggal', 'desc')
            ->pluck('kelompok_tanggal');

        return response()->json($kelompokTanggal);
    }
}
