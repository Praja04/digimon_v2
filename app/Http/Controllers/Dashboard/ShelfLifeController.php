<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ShelfLifeSamplingDetail;
use Illuminate\Http\Request;

class ShelfLifeController extends Controller
{
    public function index()
    {
        return view('dashboard.shelf_life.index');
    }

    public function getChartData(Request $request)
    {
        $kelompokSample = $request->kelompok_sample;
        $kelompokTanggal = $request->kelompok_tanggal;

        $query = ShelfLifeSamplingDetail::with(['shelfLifeSamplingKimia', 'shelfLifeSamplingMikro'])
            ->where('is_checked', true);

        if ($kelompokSample) {
            $query->where('kelompok_sample', $kelompokSample);
        }

        if ($kelompokTanggal) {
            $query->where('kelompok_tanggal', $kelompokTanggal);
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

            // Collect all values for KIMIA parameters
            $naclValues = [];
            $brixValues = [];
            $awValues = [];
            $phValues = [];
            $bjValues = [];
            $buihValues = [];
            $viscoValues = [];
            $totalNitrogenValues = [];

            // Collect all values for MIKRO parameters
            $ebValues = [];
            $saValues = [];
            $tpcValues = [];
            $ymValues = [];

            foreach ($items as $detail) {
                // Collect KIMIA data
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

                // Collect MIKRO data
                if ($detail->shelfLifeSamplingMikro) {
                    $mikro = $detail->shelfLifeSamplingMikro;

                    if (!is_null($mikro->eb)) $ebValues[] = $mikro->eb;
                    if (!is_null($mikro->sa)) $saValues[] = $mikro->sa;
                    if (!is_null($mikro->tpc)) $tpcValues[] = $mikro->tpc;
                    if (!is_null($mikro->ym)) $ymValues[] = $mikro->ym;
                }
            }

            // Calculate averages for KIMIA
            $naclData[] = !empty($naclValues) ? round(array_sum($naclValues) / count($naclValues), 2) : null;
            $brixData[] = !empty($brixValues) ? round(array_sum($brixValues) / count($brixValues), 2) : null;
            $awData[] = !empty($awValues) ? round(array_sum($awValues) / count($awValues), 4) : null;
            $phData[] = !empty($phValues) ? round(array_sum($phValues) / count($phValues), 2) : null;
            $bjData[] = !empty($bjValues) ? round(array_sum($bjValues) / count($bjValues), 4) : null;
            $buihData[] = !empty($buihValues) ? round(array_sum($buihValues) / count($buihValues), 2) : null;
            $viscoData[] = !empty($viscoValues) ? round(array_sum($viscoValues) / count($viscoValues), 2) : null;
            $totalNitrogenData[] = !empty($totalNitrogenValues) ? round(array_sum($totalNitrogenValues) / count($totalNitrogenValues), 2) : null;

            // Calculate averages for MIKRO
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
