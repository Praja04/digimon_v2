<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AnalisaGaramGula;
use App\Models\AnalisaShortTerm;
use App\Models\IdentitasRM;
use App\Models\SamplingDokumen;
use App\Models\SamplingFisikKemasan;
use App\Models\SamplingKondisiMobil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RMPMController extends Controller
{
    public function getStatistics(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $totalKedatangan = $query->count();
        $identitasIds = $query->pluck('id');

        $acceptCount = $this->getAcceptanceCount($identitasIds);
        if ($totalKedatangan === 0) {
            $acceptanceRate = 0;
            $rejectionRate = 0;
        } else {
            $acceptanceRate = round(($acceptCount / $totalKedatangan) * 100, 1);
            $rejectionRate = round((($totalKedatangan - $acceptCount) / $totalKedatangan) * 100, 1);
        }

        $avgSamplingTime = $this->calculateAverageSamplingTime($identitasIds);

        $activeSuppliers = IdentitasRM::when($date, function ($q) use ($date) {
            return $q->whereDate('tanggal_kedatangan', $date);
        })
            ->distinct('supplier')
            ->count('supplier');

        $documentCompleteness = $this->calculateDocumentCompleteness($identitasIds);

        return response()->json([
            'success' => true,
            'data' => [
                'total_kedatangan' => $totalKedatangan,
                'acceptance_rate' => $acceptanceRate,
                'rejection_rate' => $rejectionRate,
                'document_completeness' => $documentCompleteness,
            ]
        ]);
    }

    public function getTrendData(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $data = $query->select(
            DB::raw('DATE(tanggal_kedatangan) as date'),
            DB::raw('COUNT(*) as total'),
            'jenis'
        )
            ->groupBy('date', 'jenis')
            ->orderBy('date')
            ->get();

        $dates = $data->pluck('date')->unique()->values();
        $rawMaterial = [];
        $packaging = [];

        foreach ($dates as $dateItem) {
            $rawCount = $data->where('date', $dateItem)->where('jenis', '!=', 'Kemasan')->sum('total');
            $packagingCount = $data->where('date', $dateItem)->where('jenis', 'Kemasan')->sum('total');

            $rawMaterial[] = $rawCount;
            $packaging[] = $packagingCount;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $dates->map(function ($dateItem) {
                    return Carbon::parse($dateItem)->format('d M');
                }),
                'raw_material' => $rawMaterial,
                'packaging' => $packaging,
            ]
        ]);
    }

    public function getDispositionData(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $identitasIds = $query->pluck('id');

        // Get disposition from analisa tables
        $garamGulaDisposisi = AnalisaGaramGula::whereIn('id_identitas', $identitasIds)
            ->select('disposisi', DB::raw('COUNT(*) as count'))
            ->groupBy('disposisi')
            ->get();

        $shortTermDisposisi = AnalisaShortTerm::whereIn('id_identitas', $identitasIds)
            ->select('disposisi', DB::raw('COUNT(*) as count'))
            ->groupBy('disposisi')
            ->get();

        $allDisposisi = $garamGulaDisposisi->merge($shortTermDisposisi);

        $release = $allDisposisi->where('disposisi', 'Release')->sum('count');
        $reject = $allDisposisi->where('disposisi', 'Reject')->sum('count');
        $pending = $identitasIds->count() - ($release + $reject);

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Release', 'Reject', 'Pending Analysis'],
                'values' => [$release, $reject, $pending],
            ]
        ]);
    }

    public function getTopMaterials(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $topMaterials = $query->select('jenis', DB::raw('COUNT(*) as count'))
            ->groupBy('jenis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $topMaterials->pluck('jenis'),
                'values' => $topMaterials->pluck('count'),
            ]
        ]);
    }

    public function getSupplierPerformance(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $suppliers = $query->select('supplier')
            ->distinct()
            ->limit(5)
            ->get();

        $performance = [];

        foreach ($suppliers as $supplier) {
            $identitasQuery = IdentitasRM::where('supplier', $supplier->supplier);

            if ($date) {
                $identitasQuery->whereDate('tanggal_kedatangan', $date);
            }

            $identitasIds = $identitasQuery->pluck('id');

            $total = $identitasIds->count();
            $accepted = $this->getAcceptanceCount($identitasIds);

            $rate = $total > 0 ? round(($accepted / $total) * 100, 1) : 0;

            $performance[] = [
                'supplier' => $supplier->supplier,
                'rate' => $rate,
            ];
        }

        // Sort by rate descending
        usort($performance, function ($a, $b) {
            return $b['rate'] <=> $a['rate'];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_column($performance, 'supplier'),
                'values' => array_column($performance, 'rate'),
            ]
        ]);
    }

    public function getVehicleCondition(Request $request)
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();

        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        $identitasIds = $query->pluck('id');

        if ($identitasIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $conditions = SamplingKondisiMobil::whereIn('id_identitas', $identitasIds)->get();

        if ($conditions->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $total = $conditions->count();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => [
                    'Bersih',
                    'Kering',
                    'Tidak Ada Benda Asing',
                    'Tidak Cacat',
                    'Segel Baik',
                    'Tidak Berbau'
                ],
                'values' => [
                    round(($conditions->where('bersih', 'yes')->count() / $total) * 100, 1),
                    round(($conditions->where('kering', 'yes')->count() / $total) * 100, 1),
                    round(($conditions->where('benda_asing', 'no')->count() / $total) * 100, 1),
                    round(($conditions->where('cacat', 'no')->count() / $total) * 100, 1),
                    round(($conditions->where('segel', 'yes')->count() / $total) * 100, 1),
                    round(($conditions->where('berbau', 'no')->count() / $total) * 100, 1),
                ]
            ]
        ]);
    }

    public function getPackagingFindings(Request $request)
    {
        $period = $request->input('period', 'month');
        $dateRange = $this->getDateRange($period);

        $identitasIds = IdentitasRM::whereBetween('tanggal_kedatangan', $dateRange)
            ->pluck('id');

        $findings = SamplingFisikKemasan::whereIn('id_identitas', $identitasIds)->get();

        $kotor = $findings->where('kotor', 'yes')->count();
        $rusak = $findings->where('rusak', 'yes')->count();
        $tidakSesuai = $findings->where('sesuai_std', 'no')->count();
        $berair = $findings->where('berair', 'yes')->count();
        $basah = $findings->where('basah', 'yes')->count();
        $campuran = $findings->where('campuran', 'yes')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Kotor', 'Rusak', 'Tidak Sesuai Std', 'Berair', 'Basah', 'Campuran'],
                'values' => [$kotor, $rusak, $tidakSesuai, $berair, $basah, $campuran],
            ]
        ]);
    }

    public function getRecentData(Request $request)
    {
        $date = $request->input('date');
        $limit = $request->input('limit', 10);

        $data = IdentitasRM::with([
            'analisaGaramGula:id,id_identitas,disposisi',
            'analisaShortTerm:id,id_identitas,disposisi',
            'samplingDokumen'
        ])
            ->whereDate('tanggal_kedatangan', $date)
            ->orderByDesc('tanggal_kedatangan')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $disposisi = $item->analisaGaramGula->first()->disposisi ??
                    $item->analisaShortTerm->first()->disposisi ??
                    'Pending';

                $dokumen = $item->samplingDokumen;
                $dokumenStatus = 'Lengkap';

                if ($dokumen) {
                    if (!$dokumen->coa) $dokumenStatus = 'COA Missing';
                    elseif (!$dokumen->surat_jalan) $dokumenStatus = 'Surat Jalan Missing';
                    elseif (!$dokumen->packing_list) $dokumenStatus = 'Packing List Missing';
                } else {
                    $dokumenStatus = 'Belum Sampling';
                }

                return [
                    'tanggal' => Carbon::parse($item->tanggal_kedatangan)->format('d M Y'),
                    'supplier' => $item->supplier,
                    'lot_batch' => $item->lot_batch,
                    'jumlah' => $item->jumlah_kedatangan,
                    'jenis' => $item->jenis,
                    'disposisi' => $disposisi,
                    'dokumen_status' => $dokumenStatus,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getSuppliers()
    {
        $suppliers = IdentitasRM::select('supplier')
            ->distinct()
            ->orderBy('supplier')
            ->pluck('supplier');

        return response()->json([
            'success' => true,
            'data' => $suppliers,
        ]);
    }

    private function getDateRange($period)
    {
        $end = Carbon::now();

        switch ($period) {
            case 'today':
                $start = Carbon::today();
                break;
            case 'week':
                $start = Carbon::now()->subDays(7);
                break;
            case 'quarter':
                $start = Carbon::now()->subMonths(3);
                break;
            default: // month
                $start = Carbon::now()->subDays(30);
                break;
        }

        return [$start, $end];
    }

    private function getAcceptanceCount($identitasIds)
    {
        $garamGulaAccept = AnalisaGaramGula::whereIn('id_identitas', $identitasIds)
            ->where('disposisi', 'Release')
            ->count();

        $shortTermAccept = AnalisaShortTerm::whereIn('id_identitas', $identitasIds)
            ->where('disposisi', 'Release')
            ->count();

        return $garamGulaAccept + $shortTermAccept;
    }

    private function calculateAverageSamplingTime($identitasIds)
    {
        $samplings = IdentitasRM::whereIn('id', $identitasIds)
            ->with(['analisaGaramGula', 'analisaShortTerm'])
            ->get();

        $totalHours = 0;
        $count = 0;

        foreach ($samplings as $sampling) {
            $analisa = $sampling->analisaGaramGula->first() ?? $sampling->analisaShortTerm->first();

            if ($analisa && $analisa->created_at) {
                $kedatangan = Carbon::parse($sampling->tanggal_kedatangan);
                $selesai = Carbon::parse($analisa->created_at);

                $totalHours += $kedatangan->diffInHours($selesai);
                $count++;
            }
        }

        return $count > 0 ? round($totalHours / $count, 1) : 0;
    }

    private function calculateDocumentCompleteness($identitasIds)
    {
        $total = $identitasIds->count();

        if ($total === 0) return 0;

        $complete = SamplingDokumen::whereIn('id_identitas', $identitasIds)
            ->whereNotNull('coa')
            ->whereNotNull('surat_jalan')
            ->whereNotNull('packing_list')
            ->count();

        return round(($complete / $total) * 100, 1);
    }
}
