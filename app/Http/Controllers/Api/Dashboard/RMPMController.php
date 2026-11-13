<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AnalisaLongTerm;
use App\Models\IdentitasRM;
use App\Models\MasterJenisBahan;
use App\Models\MasterStandarRm;
use App\Models\PackagingIncoming;
use App\Models\PackagingInnerOuterSampling;
use App\Models\PackagingKartonSampling;
use App\Models\PackagingPouchSampling;
use App\Models\SamplingDokumen;
use App\Models\SamplingFisikKemasan;
use App\Models\SamplingKondisiMobil;
use App\Models\TransaksiAnalisaRM;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RMPMController extends Controller
{
    /**
     * Helper untuk cek filter kategori
     */
    private function shouldIncludeRm(string $category): bool
    {
        return in_array($category, ['all', 'rm'], true);
    }

    private function shouldIncludePm(string $category): bool
    {
        return in_array($category, ['all', 'pm'], true);
    }

    /**
     * Helper untuk memfilter query RM berdasarkan periode, tanggal, dan kategori bahan
     */
    private function applyRmFilters($query, Request $request)
    {
        $period = $request->input('period', 'all');
        $date = $request->input('date');
        $materialType = $request->input('material_type', 'all');

        if ($period === 'today') {
            $query->whereDate('tanggal_kedatangan', now()->toDateString());
        } elseif ($period === '7days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(7)->toDateString());
        } elseif ($period === '30days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(30)->toDateString());
        } elseif ($period === 'custom' && $date) {
            $query->whereDate('tanggal_kedatangan', $date);
        } elseif ($period === 'all') {
            // Tanpa filter tanggal (semua data)
        } elseif ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        if ($materialType === 'gula') {
            $query->where(function ($q) {
                $q->where('jenis', 'LIKE', '%GULA%')
                  ->orWhere('jenis', 'LIKE', '%SUGAR%')
                  ->orWhere('jenis', 'LIKE', '%KELAPA%')
                  ->orWhere('jenis', 'LIKE', '%TEBU%')
                  ->orWhere('jenis', '=', 'GK')
                  ->orWhere('jenis', '=', 'GT');
            });
        } elseif ($materialType === 'garam') {
            $query->where('jenis', 'LIKE', '%GARAM%');
        } elseif ($materialType === 'cair') {
            $query->where(function ($q) {
                $q->where('jenis', 'LIKE', '%GLUKOSA%')
                  ->orWhere('jenis', 'LIKE', '%FRUCTOSE%')
                  ->orWhere('jenis', 'LIKE', '%HFCS%')
                  ->orWhere('jenis', 'LIKE', '%KECAP%')
                  ->orWhere('jenis', 'LIKE', '%HAITIAN%')
                  ->orWhere('jenis', 'LIKE', '%CARAMEL%')
                  ->orWhere('jenis', 'LIKE', '%KARAMEL%')
                  ->orWhere('jenis', 'LIKE', '%LIQUID%');
            });
        } elseif ($materialType === 'kering') {
            $query->where(function ($q) {
                $q->where('jenis', 'NOT LIKE', '%GULA%')
                  ->where('jenis', 'NOT LIKE', '%SUGAR%')
                  ->where('jenis', 'NOT LIKE', '%KELAPA%')
                  ->where('jenis', 'NOT LIKE', '%TEBU%')
                  ->where('jenis', '!=', 'GK')
                  ->where('jenis', '!=', 'GT')
                  ->where('jenis', 'NOT LIKE', '%GARAM%')
                  ->where('jenis', 'NOT LIKE', '%GLUKOSA%')
                  ->where('jenis', 'NOT LIKE', '%FRUCTOSE%')
                  ->where('jenis', 'NOT LIKE', '%HFCS%')
                  ->where('jenis', 'NOT LIKE', '%KECAP%')
                  ->where('jenis', 'NOT LIKE', '%HAITIAN%')
                  ->where('jenis', 'NOT LIKE', '%CARAMEL%')
                  ->where('jenis', 'NOT LIKE', '%KARAMEL%')
                  ->where('jenis', 'NOT LIKE', '%LIQUID%');
            });
        }

        return $query;
    }

    /**
     * Helper untuk memfilter query PM berdasarkan periode, tanggal, dan kategori kemasan
     */
    private function applyPmFilters($query, Request $request)
    {
        $period = $request->input('period', 'all');
        $date = $request->input('date');
        $pmCategory = $request->input('pm_category', 'all');

        if ($period === 'today') {
            $query->whereDate('tanggal_kedatangan', now()->toDateString());
        } elseif ($period === '7days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(7)->toDateString());
        } elseif ($period === '30days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(30)->toDateString());
        } elseif ($period === 'custom' && $date) {
            $query->whereDate('tanggal_kedatangan', $date);
        } elseif ($period === 'all') {
            // Tanpa filter tanggal (semua data)
        } elseif ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        if ($pmCategory === 'pouch') {
            $query->where(function ($q) {
                $q->where('jenis_incoming_id', 8)
                  ->orWhereHas('jenisIncoming', function ($sub) {
                      $sub->where('nama', 'LIKE', '%pouch%');
                  });
            });
        } elseif ($pmCategory === 'karton') {
            $query->where(function ($q) {
                $q->whereIn('jenis_incoming_id', [3, 5])
                  ->orWhereHas('jenisIncoming', function ($sub) {
                      $sub->where('nama', 'LIKE', '%karton%')
                          ->orWhere('nama', 'LIKE', '%kardus%');
                  });
            });
        } elseif ($pmCategory === 'inner_outer') {
            $query->where(function ($q) {
                $q->whereIn('jenis_incoming_id', [1, 2, 4])
                  ->orWhereHas('jenisIncoming', function ($sub) {
                      $sub->where('nama', 'LIKE', '%inner%')
                          ->orWhere('nama', 'LIKE', '%outer%');
                  });
            });
        }

        return $query;
    }

    /**
     * 1. Get KPI Statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $rmCount = 0;
        $rmIds = collect();
        $totalVolumeKg = 0;

        if ($includeRm) {
            $rmQuery = $this->applyRmFilters(IdentitasRM::query(), $request);
            $rmIds = (clone $rmQuery)->pluck('id');
            $rmCount = $rmIds->count();
            $totalVolumeKg = (float) (clone $rmQuery)->sum('jumlah_kedatangan');
        }

        $pmCount = 0;
        $pmIds = collect();
        $pmPouchCount = 0;
        $pmKartonCount = 0;
        $pmInnerOuterCount = 0;
        $pmTotalQty = 0;
        $pouchBreakdown = [];
        $kartonBreakdown = [];
        $innerOuterBreakdown = [];

        if ($includePm) {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request);
            $pmIds = (clone $pmQuery)->pluck('id');
            $pmCount = $pmIds->count();
            $pmTotalQty = (float) (clone $pmQuery)->sum('jumlah');

            if ($pmIds->isNotEmpty()) {
                $pmListAll = (clone $pmQuery)->with(['jenisIncoming', 'jenisMaterial'])->get();
                foreach ($pmListAll as $p) {
                    $jName = strtolower(trim((string) ($p->jenisIncoming?->nama ?? '')));
                    $matName = trim((string) ($p->jenisMaterial?->nama ?? $p->mid ?? 'Lainnya'));

                    if (str_contains($jName, 'pouch')) {
                        $pmPouchCount++;
                        $pouchBreakdown[$matName] = ($pouchBreakdown[$matName] ?? 0) + 1;
                    } elseif (str_contains($jName, 'karton') || str_contains($jName, 'kardus')) {
                        $pmKartonCount++;
                        $kartonBreakdown[$matName] = ($kartonBreakdown[$matName] ?? 0) + 1;
                    } else {
                        $pmInnerOuterCount++;
                        $innerOuterBreakdown[$matName] = ($innerOuterBreakdown[$matName] ?? 0) + 1;
                    }
                }
            }
        }

        $totalKedatangan = $rmCount + $pmCount;

        // Acceptance, Rejection & Pending
        $acceptCount = 0;
        $rejectCount = 0;
        $pendingCount = 0;
        $pmTestedCount = 0;
        $pmDefectCount = 0;

        // RM Dispositions
        if ($rmIds->isNotEmpty()) {
            $analisaRecords = TransaksiAnalisaRM::whereIn('id_identitas', $rmIds)->get();
            $groupedById = $analisaRecords->groupBy('id_identitas');

            foreach ($rmIds as $id) {
                $recs = $groupedById->get($id);
                if (!$recs || $recs->isEmpty()) {
                    $pendingCount++;
                    continue;
                }
                $disp = strtolower(trim((string) ($recs->first()->disposisi ?? '')));
                if (in_array($disp, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $acceptCount++;
                } elseif (in_array($disp, ['reject', 'ditolak'], true)) {
                    $rejectCount++;
                } else {
                    $pendingCount++;
                }
            }
        }

        // PM Dispositions & Defect Tracking
        if ($pmIds->isNotEmpty()) {
            $pouchSamplings = PackagingPouchSampling::whereIn('packaging_incoming_id', $pmIds)->get();
            $kartonSamplings = PackagingKartonSampling::whereIn('packaging_incoming_id', $pmIds)->get();
            $ioSamplings = PackagingInnerOuterSampling::whereIn('packaging_incoming_id', $pmIds)->get();

            $allSamplings = collect()->merge($pouchSamplings)->merge($kartonSamplings)->merge($ioSamplings);

            foreach ($allSamplings as $s) {
                $pmTestedCount++;
                $rekLower = strtolower(trim((string) ($s->rekomendasi ?? '')));
                if (in_array($rekLower, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $acceptCount++;
                } elseif (in_array($rekLower, ['reject', 'ditolak'], true)) {
                    $rejectCount++;
                }

                $konf = strtolower(trim((string) ($s->konfirmasi_ketidaksesuaian ?? '')));
                $hasDefectArray = !empty($s->jenis_ketidaksesuaian) && is_array($s->jenis_ketidaksesuaian) && count($s->jenis_ketidaksesuaian) > 0;
                if ($konf === 'ada' || $hasDefectArray) {
                    $pmDefectCount++;
                }
            }

            if ($category === 'pm') {
                $pendingCount = max(0, $pmCount - $pmTestedCount);
            }
        }

        $activeDenom = ($category === 'pm') ? max(1, $pmCount) : max(1, $totalKedatangan);
        $acceptanceRate = round(($acceptCount / $activeDenom) * 100, 1);
        $rejectionRate = round(($rejectCount / $activeDenom) * 100, 1);

        // Document Completeness (COA)
        $completeDocs = 0;
        if ($rmIds->isNotEmpty()) {
            $completeDocs += SamplingDokumen::whereIn('id_identitas', $rmIds)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('coa')->where('coa', '!=', '')
                            ->whereNotNull('surat_jalan')->where('surat_jalan', '!=', '');
                    });
                })
                ->count();
        }

        if ($pmIds->isNotEmpty()) {
            $completeDocs += collect()
                ->merge(PackagingPouchSampling::whereIn('packaging_incoming_id', $pmIds)->whereNotNull('coa')->where('coa', '!=', '')->pluck('packaging_incoming_id'))
                ->merge(PackagingInnerOuterSampling::whereIn('packaging_incoming_id', $pmIds)->whereNotNull('coa')->where('coa', '!=', '')->pluck('packaging_incoming_id'))
                ->merge(PackagingKartonSampling::whereIn('packaging_incoming_id', $pmIds)->whereNotNull('coa')->where('coa', '!=', '')->pluck('packaging_incoming_id'))
                ->unique()
                ->count();
        }

        $documentCompleteness = $totalKedatangan > 0 ? round(($completeDocs / $totalKedatangan) * 100, 1) : 0;

        // Kepatuhan Truk / Armada (Sampling Kondisi Mobil RM)
        $vehicleCleanCount = 0;
        if ($rmIds->isNotEmpty()) {
            $vehicleCleanCount = SamplingKondisiMobil::whereIn('id_identitas', $rmIds)
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(bersih))'), ['ya', 'yes', '1', 'bersih', 'sesuai']);
                })
                ->count();
        }
        $vehicleCleanlinessRate = $rmCount > 0 ? round(($vehicleCleanCount / $rmCount) * 100, 1) : 0;

        // Defect Free Rate (PM)
        $defectFreeRate = $pmTestedCount > 0 ? round((($pmTestedCount - $pmDefectCount) / $pmTestedCount) * 100, 1) : 100;

        // Format volume
        $totalVolumeFormatted = $category === 'pm'
            ? number_format($pmTotalQty, 0, ',', '.') . ' Pcs/Unit'
            : ($totalVolumeKg >= 1000
                ? number_format($totalVolumeKg / 1000, 1, ',', '.') . ' Ton'
                : number_format($totalVolumeKg, 0, ',', '.') . ' Kg');

        return response()->json([
            'success' => true,
            'data' => [
                'total_kedatangan' => $totalKedatangan,
                'total_arrivals' => $totalKedatangan,
                'total_volume_kg' => $totalVolumeKg,
                'total_volume_formatted' => $totalVolumeFormatted,
                'accept_count' => $acceptCount,
                'reject_count' => $rejectCount,
                'pending_count' => $pendingCount,
                'acceptance_rate' => $acceptanceRate,
                'rejection_rate' => $rejectionRate,
                'document_completeness' => $documentCompleteness,
                'vehicle_cleanliness_rate' => $vehicleCleanlinessRate,
                'defect_free_rate' => $defectFreeRate,
                'pouch_count' => $pmPouchCount,
                'karton_count' => $pmKartonCount,
                'inner_outer_count' => $pmInnerOuterCount,
                'category_breakdowns' => [
                    'all' => [
                        'labels' => ['Pouch (Refill/Sachet)', 'Karton (Master Box)', 'Inner / Outer (Roll/Film)'],
                        'values' => [$pmPouchCount, $pmKartonCount, $pmInnerOuterCount],
                        'colors' => ['#8b5cf6', '#f59e0b', '#06b6d4'],
                        'total' => $pmCount,
                        'title' => 'Total Masuk',
                        'pills' => [
                            ['label' => 'Pouch (Refill/Sachet)', 'val' => $pmPouchCount, 'color' => 'purple'],
                            ['label' => 'Karton (Master Box)', 'val' => $pmKartonCount, 'color' => 'warning'],
                            ['label' => 'Inner / Outer (Film/Roll)', 'val' => $pmInnerOuterCount, 'color' => 'info']
                        ]
                    ],
                    'pouch' => [
                        'labels' => array_keys($pouchBreakdown),
                        'values' => array_values($pouchBreakdown),
                        'colors' => ['#8b5cf6', '#a855f7', '#c084fc', '#d8b4fe', '#7c3aed'],
                        'total' => $pmPouchCount,
                        'title' => 'Total Pouch',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'purple'];
                        }, array_keys($pouchBreakdown), array_values($pouchBreakdown))
                    ],
                    'karton' => [
                        'labels' => array_keys($kartonBreakdown),
                        'values' => array_values($kartonBreakdown),
                        'colors' => ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a', '#d97706'],
                        'total' => $pmKartonCount,
                        'title' => 'Total Karton',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'warning'];
                        }, array_keys($kartonBreakdown), array_values($kartonBreakdown))
                    ],
                    'inner_outer' => [
                        'labels' => array_keys($innerOuterBreakdown),
                        'values' => array_values($innerOuterBreakdown),
                        'colors' => ['#06b6d4', '#22d3ee', '#67e8f9', '#a5f3fc', '#0891b2'],
                        'total' => $pmInnerOuterCount,
                        'title' => 'Total Inner/Outer',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'info'];
                        }, array_keys($innerOuterBreakdown), array_values($innerOuterBreakdown))
                    ]
                ],
                'pm_tested_count' => $pmTestedCount,
            ]
        ]);
    }

    /**
     * 2. Get Trend Kedatangan Bahan (Trend 7 Hari Terakhir Menuju Tanggal Filter)
     */
    public function getTrendData(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $endDate = $date ? Carbon::parse($date) : Carbon::today();
        $startDate = $endDate->copy()->subDays(6);

        $labels = [];
        $rawMaterial = [];
        $rawMaterialVolume = [];
        $packaging = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $curStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $rmDailyCount = 0;
            $rmDailyVol = 0;
            if ($includeRm) {
                $dayRmQuery = $this->applyRmFilters(IdentitasRM::whereDate('tanggal_kedatangan', $curStr), $request);
                $rmDailyCount = (clone $dayRmQuery)->count();
                $rmDailyVol = (float) (clone $dayRmQuery)->sum('jumlah_kedatangan');
            }

            $pmDaily = $includePm
                ? PackagingIncoming::whereDate('tanggal_kedatangan', $curStr)->count()
                : 0;

            $rawMaterial[] = $rmDailyCount;
            $rawMaterialVolume[] = round($rmDailyVol, 1);
            $packaging[] = $pmDaily;

            $current->addDay();
        }

        // Jika trend RM murni kosong pada rentang tersebut, ambil 7 tanggal transaksi terakhir agar chart tetap hidup
        if ($category === 'rm' && array_sum($rawMaterial) === 0) {
            $latestActiveDates = IdentitasRM::select('tanggal_kedatangan')
                ->whereNotNull('tanggal_kedatangan')
                ->groupBy('tanggal_kedatangan')
                ->orderByDesc('tanggal_kedatangan')
                ->limit(7)
                ->pluck('tanggal_kedatangan')
                ->reverse();

            if ($latestActiveDates->isNotEmpty()) {
                $labels = [];
                $rawMaterial = [];
                $rawMaterialVolume = [];
                foreach ($latestActiveDates as $tgl) {
                    $labels[] = Carbon::parse($tgl)->format('d M');
                    $tglRmQuery = $this->applyRmFilters(IdentitasRM::whereDate('tanggal_kedatangan', $tgl), $request);
                    $rawMaterial[] = (clone $tglRmQuery)->count();
                    $rawMaterialVolume[] = round((float) (clone $tglRmQuery)->sum('jumlah_kedatangan'), 1);
                }
            }
        }

        // Jika trend PM kosong pada rentang tersebut, ambil 7 tanggal transaksi terakhir PM
        if ($category === 'pm' && array_sum($packaging) === 0) {
            $latestPmDates = PackagingIncoming::select('tanggal_kedatangan')
                ->whereNotNull('tanggal_kedatangan')
                ->groupBy('tanggal_kedatangan')
                ->orderByDesc('tanggal_kedatangan')
                ->limit(7)
                ->pluck('tanggal_kedatangan')
                ->reverse();

            if ($latestPmDates->isNotEmpty()) {
                $labels = [];
                $packaging = [];
                foreach ($latestPmDates as $tgl) {
                    $labels[] = Carbon::parse($tgl)->format('d M');
                    $tglPmQuery = $this->applyPmFilters(PackagingIncoming::whereDate('tanggal_kedatangan', $tgl), $request);
                    $packaging[] = (clone $tglPmQuery)->count();
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'raw_material' => $rawMaterial,
                'raw_material_volume' => $rawMaterialVolume,
                'packaging' => $packaging,
            ]
        ]);
    }

    /**
     * 3. Get Distribusi Disposisi (Donut Chart)
     */
    public function getDispositionData(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $release = 0;
        $bersyarat = 0;
        $reject = 0;
        $pending = 0;
        $totalItems = 0;
        $releaseBreakdown = [];
        $bersyaratBreakdown = [];
        $rejectBreakdown = [];
        $pendingBreakdown = [];

        if ($includeRm) {
            $rmQuery = $this->applyRmFilters(IdentitasRM::query(), $request)->with('transaksiAnalisa');
            $rmList = $rmQuery->get();
            $totalItems += $rmList->count();

            foreach ($rmList as $rm) {
                $jenisBahan = trim((string) ($rm->jenis ?? 'Raw Material'));
                $disp = strtolower(trim((string) ($rm->transaksiAnalisa->first()?->disposisi ?? '')));

                if (in_array($disp, ['release', 'diterima'], true)) {
                    $release++;
                    $releaseBreakdown[$jenisBahan] = ($releaseBreakdown[$jenisBahan] ?? 0) + 1;
                } elseif (in_array($disp, ['release bersyarat', 'diterima bersyarat'], true)) {
                    $bersyarat++;
                    $bersyaratBreakdown[$jenisBahan] = ($bersyaratBreakdown[$jenisBahan] ?? 0) + 1;
                } elseif (in_array($disp, ['reject', 'ditolak'], true)) {
                    $reject++;
                    $rejectBreakdown[$jenisBahan] = ($rejectBreakdown[$jenisBahan] ?? 0) + 1;
                } else {
                    $pending++;
                    $pendingBreakdown[$jenisBahan] = ($pendingBreakdown[$jenisBahan] ?? 0) + 1;
                }
            }
        }

        if ($includePm) {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request)->with([
                'jenisIncoming',
                'jenisMaterial',
                'packagingPouchSampling',
                'packagingInnerOuterSampling',
                'packagingKartonSampling'
            ]);
            $pmList = $pmQuery->get();
            $totalItems += $pmList->count();

            foreach ($pmList as $p) {
                $jName = trim((string) ($p->jenisIncoming?->nama ?? ''));
                $catName = 'Inner / Outer';
                $jLower = strtolower($jName);
                if (str_contains($jLower, 'pouch')) {
                    $catName = 'Pouch';
                } elseif (str_contains($jLower, 'karton') || str_contains($jLower, 'kardus')) {
                    $catName = 'Karton Box';
                }

                $rek = $p->packagingPouchSampling?->rekomendasi
                    ?? $p->packagingInnerOuterSampling?->rekomendasi
                    ?? $p->packagingKartonSampling?->rekomendasi
                    ?? '';
                $rekLower = strtolower(trim((string) $rek));

                if (in_array($rekLower, ['release', 'diterima'], true)) {
                    $release++;
                    $releaseBreakdown[$catName] = ($releaseBreakdown[$catName] ?? 0) + 1;
                } elseif (in_array($rekLower, ['release bersyarat', 'diterima bersyarat'], true)) {
                    $bersyarat++;
                    $bersyaratBreakdown[$catName] = ($bersyaratBreakdown[$catName] ?? 0) + 1;
                } elseif (in_array($rekLower, ['reject', 'ditolak'], true)) {
                    $reject++;
                    $rejectBreakdown[$catName] = ($rejectBreakdown[$catName] ?? 0) + 1;
                } else {
                    $pending++;
                    $pendingBreakdown[$catName] = ($pendingBreakdown[$catName] ?? 0) + 1;
                }
            }
        }

        // Jika tidak ada data sama sekali pada tanggal tersebut, return array kosong untuk empty state
        if ($totalItems === 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => [],
                    'values' => [],
                    'total' => 0,
                    'disposition_breakdowns' => []
                ]
            ]);
        }

        $greenPalette = ['#10b981', '#059669', '#34d399', '#14b8a6', '#0d9488', '#6ee7b7', '#a7f3d0', '#047857', '#065f46', '#0f766e'];
        $warningPalette = ['#f59e0b', '#d97706', '#fbbf24', '#fcd34d', '#fde68a', '#b45309', '#92400e', '#78350f', '#ca8a04', '#eab308'];
        $dangerPalette = ['#ef4444', '#dc2626', '#f87171', '#fca5a5', '#fecaca', '#b91c1c', '#991b1b', '#7f1d1d', '#e11d48', '#be123c'];
        $primaryPalette = ['#3b82f6', '#2563eb', '#60a5fa', '#93c5fd', '#bfdbfe', '#1d4ed8', '#1e40af', '#1e3a8a', '#0284c7', '#0369a1'];

        $allTitle = ($category === 'rm') ? 'Total Analisa' : 'Total Sampel';

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Release (Diterima)', 'Diterima Bersyarat', 'Reject (Ditolak)', 'Pending / Belum Sampling'],
                'values' => [$release, $bersyarat, $reject, $pending],
                'total' => $totalItems,
                'disposition_breakdowns' => [
                    'all' => [
                        'labels' => ['Release (Diterima)', 'Diterima Bersyarat', 'Reject (Ditolak)', 'Pending / Belum Sampling'],
                        'values' => [$release, $bersyarat, $reject, $pending],
                        'colors' => ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
                        'total' => $totalItems,
                        'title' => $allTitle,
                        'pills' => [
                            ['label' => 'Release (Diterima)', 'val' => $release, 'color' => 'success'],
                            ['label' => 'Diterima Bersyarat', 'val' => $bersyarat, 'color' => 'warning'],
                            ['label' => 'Reject (Ditolak)', 'val' => $reject, 'color' => 'danger'],
                            ['label' => 'Pending / Belum', 'val' => $pending, 'color' => 'primary']
                        ]
                    ],
                    'release' => [
                        'labels' => array_keys($releaseBreakdown),
                        'values' => array_values($releaseBreakdown),
                        'colors' => $greenPalette,
                        'total' => $release,
                        'title' => 'Total Release',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'success'];
                        }, array_keys($releaseBreakdown), array_values($releaseBreakdown))
                    ],
                    'bersyarat' => [
                        'labels' => array_keys($bersyaratBreakdown),
                        'values' => array_values($bersyaratBreakdown),
                        'colors' => $warningPalette,
                        'total' => $bersyarat,
                        'title' => 'Total Bersyarat',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'warning'];
                        }, array_keys($bersyaratBreakdown), array_values($bersyaratBreakdown))
                    ],
                    'reject' => [
                        'labels' => array_keys($rejectBreakdown),
                        'values' => array_values($rejectBreakdown),
                        'colors' => $dangerPalette,
                        'total' => $reject,
                        'title' => 'Total Reject',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'danger'];
                        }, array_keys($rejectBreakdown), array_values($rejectBreakdown))
                    ],
                    'pending' => [
                        'labels' => array_keys($pendingBreakdown),
                        'values' => array_values($pendingBreakdown),
                        'colors' => $primaryPalette,
                        'total' => $pending,
                        'title' => 'Total Pending',
                        'pills' => array_map(function($k, $v) {
                            return ['label' => $k, 'val' => $v, 'color' => 'primary'];
                        }, array_keys($pendingBreakdown), array_values($pendingBreakdown))
                    ]
                ]
            ]
        ]);
    }

    /**
     * 4. Get Top 5 Bahan Paling Sering Datang
     */
    public function getTopMaterials(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $materialCounts = collect();
        $materialVolumes = collect();

        if ($includeRm) {
            $rmQuery = $this->applyRmFilters(IdentitasRM::query(), $request);
            $rmMaterials = $rmQuery->select('jenis', DB::raw('COUNT(*) as total'), DB::raw('SUM(jumlah_kedatangan) as total_volume'))
                ->whereNotNull('jenis')
                ->where('jenis', '!=', '')
                ->groupBy('jenis')
                ->get();

            // Fallback jika kosong pada rentang filter
            if ($rmMaterials->isEmpty()) {
                $rmMaterials = IdentitasRM::select('jenis', DB::raw('COUNT(*) as total'), DB::raw('SUM(jumlah_kedatangan) as total_volume'))
                    ->whereNotNull('jenis')
                    ->where('jenis', '!=', '')
                    ->groupBy('jenis')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();
            }

            foreach ($rmMaterials as $rm) {
                $name = trim((string) $rm->jenis);
                $materialCounts[$name] = ($materialCounts[$name] ?? 0) + $rm->total;
                $materialVolumes[$name] = ($materialVolumes[$name] ?? 0) + (float) $rm->total_volume;
            }
        }

        $materialMeta = [];
        if ($includePm) {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request)->with(['jenisIncoming', 'jenisMaterial', 'uom', 'supplier']);
            $pmList = $pmQuery->get();

            if ($pmList->isEmpty() && $category === 'pm') {
                $pmList = PackagingIncoming::with(['jenisIncoming', 'jenisMaterial', 'uom', 'supplier'])->latest()->take(20)->get();
            }

            foreach ($pmList as $pm) {
                $midCode = trim((string) ($pm->mid ?? ''));
                $matName = trim((string) ($pm->jenisMaterial?->nama ?? $pm->jenisIncoming?->nama ?? 'Packaging'));

                // When category is PM or user requests PM, group by MID as requested by user
                if ($category === 'pm') {
                    $name = !empty($midCode) ? (string) $midCode : $matName;
                } else {
                    $name = !empty($midCode) ? "MID " . $midCode : $matName;
                }

                $name = (string) $name;
                $materialCounts[$name] = ($materialCounts[$name] ?? 0) + 1;
                $uomName = $pm->uom?->nama ?? 'Unit';
                $materialVolumes[$name] = ($materialVolumes[$name] ?? 0) + (float) $pm->jumlah;

                if (!isset($materialMeta[$name])) {
                    $materialMeta[$name] = [
                        'mid' => $midCode,
                        'material' => $matName,
                        'supplier' => $pm->supplier?->nama ?? '',
                    ];
                }
            }
        }

        $sorted = $materialCounts->sortDesc()->take(5);

        $volumesFormatted = [];
        $materialsFormatted = [];
        foreach ($sorted->keys() as $matName) {
            $vol = $materialVolumes[$matName] ?? 0;
            if ($category === 'pm') {
                $volumesFormatted[] = number_format($vol, 0, ',', '.') . ' Unit';
            } else {
                $volumesFormatted[] = $vol >= 1000
                    ? number_format($vol / 1000, 1, ',', '.') . ' Ton'
                    : number_format($vol, 0, ',', '.') . ' Kg';
            }
            $materialsFormatted[] = $materialMeta[$matName]['material'] ?? '';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $sorted->keys()->map(fn($k) => (string) $k)->values(),
                'values' => $sorted->values(),
                'volumes' => $volumesFormatted,
                'materials' => $materialsFormatted,
            ]
        ]);
    }

    /**
     * 5. Get Performance Supplier (Acceptance Rate)
     */
    public function getSupplierPerformance(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $suppliersData = [];

        if ($includeRm) {
            $rmQuery = $this->applyRmFilters(IdentitasRM::query(), $request);
            $rmList = $rmQuery->with('transaksiAnalisa')->get();

            if ($rmList->isEmpty()) {
                $rmList = IdentitasRM::with('transaksiAnalisa')
                    ->whereNotNull('supplier')
                    ->where('supplier', '!=', '')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            }

            foreach ($rmList as $rm) {
                $sup = trim((string) ($rm->supplier ?? ''));
                if ($sup === '') continue;

                if (!isset($suppliersData[$sup])) {
                    $suppliersData[$sup] = ['total' => 0, 'accepted' => 0];
                }
                $suppliersData[$sup]['total']++;

                $disp = strtolower(trim((string) ($rm->transaksiAnalisa->first()?->disposisi ?? '')));
                if (in_array($disp, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $suppliersData[$sup]['accepted']++;
                }
            }
        }

        if ($includePm) {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request)->with([
                'supplier',
                'packagingPouchSampling',
                'packagingInnerOuterSampling',
                'packagingKartonSampling'
            ]);
            $pmList = $pmQuery->get();

            if ($pmList->isEmpty() && $category === 'pm') {
                $pmList = PackagingIncoming::with([
                    'supplier',
                    'packagingPouchSampling',
                    'packagingInnerOuterSampling',
                    'packagingKartonSampling'
                ])->latest()->take(50)->get();
            }

            foreach ($pmList as $pm) {
                $sup = trim((string) ($pm->supplier?->nama ?? $pm->supplier?->nama_supplier ?? ''));
                if ($sup === '') continue;

                if (!isset($suppliersData[$sup])) {
                    $suppliersData[$sup] = ['total' => 0, 'accepted' => 0];
                }
                $suppliersData[$sup]['total']++;

                $rek = strtolower(trim((string) (
                    $pm->packagingPouchSampling?->rekomendasi
                    ?? $pm->packagingInnerOuterSampling?->rekomendasi
                    ?? $pm->packagingKartonSampling?->rekomendasi
                    ?? ''
                )));

                if (in_array($rek, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $suppliersData[$sup]['accepted']++;
                }
            }
        }

        $performance = [];
        foreach ($suppliersData as $supName => $d) {
            $rate = $d['total'] > 0 ? round(($d['accepted'] / $d['total']) * 100, 1) : 0;
            $performance[] = [
                'supplier' => $supName,
                'rate' => $rate,
                'total' => $d['total']
            ];
        }

        // Sort descending by total then rate
        usort($performance, function ($a, $b) {
            return ($b['total'] <=> $a['total']) ?: ($b['rate'] <=> $a['rate']);
        });

        $topSuppliers = array_slice($performance, 0, 5);

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_column($topSuppliers, 'supplier'),
                'values' => array_column($topSuppliers, 'rate'),
                'totals' => array_column($topSuppliers, 'total'),
            ]
        ]);
    }

    /**
     * 6. Get Kondisi Kendaraan (Radar Chart)
     */
    public function getVehicleCondition(Request $request): JsonResponse
    {
        $query = $this->applyRmFilters(IdentitasRM::query(), $request);
        $identitasIds = $query->pluck('id');

        $conditions = SamplingKondisiMobil::whereIn('id_identitas', $identitasIds)->get();

        // Jika tidak ada pada tanggal filter, ambil sampel kondisi mobil terbaru agar radar chart tetap informatif
        if ($conditions->isEmpty()) {
            $conditions = SamplingKondisiMobil::latest()->limit(50)->get();
        }

        if ($conditions->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $total = $conditions->count();

        $checkField = function (string $field) use ($conditions, $total): float {
            $passCount = $conditions->filter(function ($item) use ($field) {
                $val = strtolower(trim((string) ($item->{$field} ?? '')));
                return in_array($val, ['yes', 'ya', '1', 'sesuai', 'bersih', 'kering', 'segel', 'tidak berbau', 'tidak ada benda asing', 'tidak cacat'], true);
            })->count();

            return round(($passCount / $total) * 100, 1);
        };

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => [
                    'Kebersihan Truk',
                    'Kondisi Kering',
                    'Bebas Benda Asing',
                    'Kondisi Tidak Cacat',
                    'Keutuhan Segel',
                    'Bebas Bau Asing'
                ],
                'values' => [
                    $checkField('bersih'),
                    $checkField('kering'),
                    $checkField('benda_asing'),
                    $checkField('cacat'),
                    $checkField('segel'),
                    $checkField('berbau'),
                ]
            ]
        ]);
    }

    /**
     * 7. Get Temuan Fisik Kemasan / Cacat Kemasan (Bar Chart)
     */
    public function getPackagingFindings(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        if ($category === 'pm') {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request);
            $pmIds = $pmQuery->pluck('id');

            $allSamplings = collect();
            if ($pmIds->isNotEmpty()) {
                $allSamplings = $allSamplings
                    ->merge(PackagingPouchSampling::whereIn('packaging_incoming_id', $pmIds)->get())
                    ->merge(PackagingKartonSampling::whereIn('packaging_incoming_id', $pmIds)->get())
                    ->merge(PackagingInnerOuterSampling::whereIn('packaging_incoming_id', $pmIds)->get());
            }

            // Fallback jika kosong pada filter tanggal
            if ($allSamplings->isEmpty()) {
                $allSamplings = collect()
                    ->merge(PackagingPouchSampling::latest()->take(30)->get())
                    ->merge(PackagingKartonSampling::latest()->take(30)->get())
                    ->merge(PackagingInnerOuterSampling::latest()->take(30)->get());
            }

            $defectCounts = [
                'Miss Print' => 0,
                'Berat Under' => 0,
                'Dimensi Out Spec' => 0,
                'Delaminasi' => 0,
                'Salah Design' => 0,
                'Barcode Error' => 0,
            ];

            foreach ($allSamplings as $s) {
                $defects = $s->jenis_ketidaksesuaian;
                if (!empty($defects) && is_array($defects)) {
                    foreach ($defects as $d) {
                        $dStr = ucwords(strtolower(trim((string)$d)));
                        if (isset($defectCounts[$dStr])) {
                            $defectCounts[$dStr]++;
                        } else {
                            $defectCounts[$dStr] = ($defectCounts[$dStr] ?? 0) + 1;
                        }
                    }
                }
            }

            $defectCollection = collect($defectCounts)->sortDesc()->take(6);

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $defectCollection->keys()->values(),
                    'values' => $defectCollection->values(),
                ]
            ]);
        }

        // RM Kemasan
        $query = IdentitasRM::query();
        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }
        $identitasIds = $query->pluck('id');

        $findings = SamplingFisikKemasan::whereIn('id_identitas', $identitasIds)->get();

        // Jika tidak ada data spesifik pada hari itu, ambil data temuan terkini agar visual tetap berguna
        if ($findings->isEmpty()) {
            $findings = SamplingFisikKemasan::latest()->limit(50)->get();
        }

        $countDefect = function (string $field, bool $invert = false) use ($findings): int {
            return $findings->filter(function ($item) use ($field, $invert) {
                $val = strtolower(trim((string) ($item->{$field} ?? '')));
                if ($invert) {
                    return in_array($val, ['no', 'tidak', '0', 'tidak sesuai'], true);
                }
                return in_array($val, ['yes', 'ya', '1', 'ada'], true);
            })->count();
        };

        $kotor = $countDefect('kotor');
        $rusak = $countDefect('rusak');
        $tidakSesuai = $countDefect('sesuai_std', true);
        $berair = $countDefect('berair');
        $basah = $countDefect('basah');
        $campuran = $countDefect('campuran');

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Kotor', 'Rusak', 'Tidak Sesuai Std', 'Berair', 'Basah', 'Campuran'],
                'values' => [$kotor, $rusak, $tidakSesuai, $berair, $basah, $campuran],
            ]
        ]);
    }

    /**
     * Resolves MasterJenisBahan model based on material string
     */
    public function resolveMasterJenisBahan(?string $jenis): ?MasterJenisBahan
    {
        if (!$jenis) {
            return null;
        }

        $cleanJenis = strtoupper(trim($jenis));

        // 1. Exact match
        $jb = MasterJenisBahan::whereRaw('UPPER(nama) = ?', [$cleanJenis])->first();
        if ($jb) {
            return $jb;
        }

        // 2. Substring or LIKE match
        $jb = MasterJenisBahan::where('nama', 'LIKE', '%' . trim($jenis) . '%')
            ->orWhereRaw('? LIKE CONCAT("%", UPPER(nama), "%")', [$cleanJenis])
            ->first();
        if ($jb) {
            return $jb;
        }

        // 3. Fallbacks for specific common material aliases
        if (str_contains($cleanJenis, 'GARAM')) {
            return MasterJenisBahan::where('nama', 'LIKE', '%GARAM%')->first();
        }
        if (str_contains($cleanJenis, 'RAFINASI') || (str_contains($cleanJenis, 'GULA') && !str_contains($cleanJenis, 'KELAPA') && !str_contains($cleanJenis, 'TEBU'))) {
            return MasterJenisBahan::where('nama', 'LIKE', '%RAFINASI%')->first();
        }
        if (str_contains($cleanJenis, 'CARAMEL') || str_contains($cleanJenis, 'KARAMEL')) {
            return MasterJenisBahan::where('nama', 'LIKE', '%CARAMEL%')->first();
        }
        if (str_contains($cleanJenis, 'FRUC') || str_contains($cleanJenis, 'FRUKTOSA') || str_contains($cleanJenis, 'HFCS')) {
            return MasterJenisBahan::where('nama', 'LIKE', '%FRUC%')->first();
        }
        if (str_contains($cleanJenis, 'SARI KECAP') || str_contains($cleanJenis, 'SKM')) {
            return MasterJenisBahan::where('nama', 'LIKE', '%SARI KECAP%')->first();
        }

        return null;
    }

    /**
     * Build detailed quality standards & laboratory test results for a single RM transaction
     */
    public function buildTransactionStandardsDetail(IdentitasRM $rm): array
    {
        $jb = $this->resolveMasterJenisBahan($rm->jenis);
        $masterStandards = $jb
            ? MasterStandarRm::where('status', true)->where('id_jenis_bahan', $jb->id)->get()
            : collect();

        $tx = $rm->transaksiAnalisa->first();
        $details = $tx ? $tx->details : collect();
        $groupedDetails = $details->groupBy(function ($d) {
            return strtoupper(trim((string)$d->parameter));
        });

        $paramsList = [];
        $totalParams = 0;
        $compliantCount = 0;
        $nonCompliantCount = 0;
        $processedKeys = [];

        foreach ($masterStandards as $std) {
            $key = strtoupper(trim((string)$std->parameter));
            $processedKeys[] = $key;
            $stdDetail = $groupedDetails->get($key)?->first();

            $min = $std->min_standar;
            $max = $std->max_standar;
            $uom = trim((string)$std->uom);
            $target = trim((string)$std->target_text);

            $stdRange = '';
            if ($target !== '') {
                $stdRange = $target;
            } elseif ($min !== null && $max !== null) {
                $stdRange = "{$min} - {$max}" . ($uom ? " {$uom}" : '');
            } elseif ($min !== null) {
                $stdRange = "Min {$min}" . ($uom ? " {$uom}" : '');
            } elseif ($max !== null) {
                $stdRange = "Maks {$max}" . ($uom ? " {$uom}" : '');
            } else {
                $stdRange = 'Standar';
            }

            $actualVal = null;
            $isSesuai = null;
            $sampleValues = [];

            if ($stdDetail) {
                $samples = $groupedDetails->get($key)->pluck('nilai')->filter(fn($v) => $v !== null && $v !== '')->values();
                if ($samples->isNotEmpty()) {
                    $sampleValues = $samples->all();
                    $numericSamples = $samples->filter(fn($v) => is_numeric($v));
                    if ($numericSamples->count() === $samples->count() && $samples->count() > 1) {
                        $avg = round($numericSamples->avg(), 2);
                        $actualVal = "{$avg}" . ($uom ? " {$uom}" : '') . " (n={$samples->count()})";
                    } else {
                        $actualVal = $samples->implode(', ') . ($uom && is_numeric($samples->first()) ? " {$uom}" : '');
                    }
                }
                $isSesuai = $stdDetail->is_sesuai ?? true;
            }

            // Fallback from legacy / accessor
            if ($actualVal === null && $tx) {
                $legacyVal = $tx->getDetailValue($key) ?? $tx->getAttribute(strtolower($key));
                if ($legacyVal !== null && $legacyVal !== '') {
                    $actualVal = (string)$legacyVal . ($uom ? " {$uom}" : '');
                    $isSesuai = true;
                }
            }

            if ($actualVal !== null) {
                if ($isSesuai) {
                    $compliantCount++;
                } else {
                    $nonCompliantCount++;
                }
            }
            $totalParams++;

            $statusLabel = 'Belum Diuji';
            $badgeClass = 'bg-secondary-subtle text-muted';
            if ($actualVal !== null) {
                if ($isSesuai) {
                    $statusLabel = 'Sesuai Standar';
                    $badgeClass = 'bg-success-subtle text-success';
                } else {
                    $statusLabel = 'Menyimpang (Out of Spec)';
                    $badgeClass = 'bg-danger-subtle text-danger';
                }
            }

            $paramsList[] = [
                'parameter'     => $std->parameter,
                'jenis_standar' => $std->jenis_standar,
                'standar'       => $stdRange,
                'min'           => $min,
                'max'           => $max,
                'target'        => $target,
                'uom'           => $uom,
                'hasil'         => $actualVal ?? 'Belum Diuji',
                'sample_values' => $sampleValues,
                'is_tested'     => ($actualVal !== null),
                'is_compliant'  => $isSesuai,
                'status_label'  => $statusLabel,
                'badge_class'   => $badgeClass,
            ];
        }

        // Check if there are extra parameters in details that were not in Master Standards
        foreach ($groupedDetails as $extraKey => $extraItems) {
            if (!in_array($extraKey, $processedKeys, true)) {
                $extraFirst = $extraItems->first();
                $samples = $extraItems->pluck('nilai')->filter(fn($v) => $v !== null && $v !== '')->values();
                $actualVal = $samples->implode(', ');
                $isSesuai = $extraFirst->is_sesuai ?? true;

                if ($actualVal !== '') {
                    if ($isSesuai) {
                        $compliantCount++;
                    } else {
                        $nonCompliantCount++;
                    }
                }
                $totalParams++;

                $paramsList[] = [
                    'parameter'     => $extraFirst->parameter ?: $extraKey,
                    'jenis_standar' => 'custom',
                    'standar'       => 'Standar Pabrik',
                    'min'           => null,
                    'max'           => null,
                    'target'        => 'Standar Pabrik',
                    'uom'           => '',
                    'hasil'         => $actualVal !== '' ? $actualVal : 'Belum Diuji',
                    'sample_values' => $samples->all(),
                    'is_tested'     => ($actualVal !== ''),
                    'is_compliant'  => $isSesuai,
                    'status_label'  => $isSesuai ? 'Sesuai Standar' : 'Menyimpang',
                    'badge_class'   => $isSesuai ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger',
                ];
            }
        }

        // Add Sugar Stability & Kristalisasi info for Sugar
        $jenisUpper = strtoupper(trim((string)$rm->jenis));
        $isSugar = str_contains($jenisUpper, 'GULA') 
            || str_contains($jenisUpper, 'SUGAR') 
            || str_contains($jenisUpper, 'KELAPA') 
            || str_contains($jenisUpper, 'TEBU')
            || $jenisUpper === 'GK' 
            || $jenisUpper === 'GT';

        $sugarDetails = null;
        if ($isSugar) {
            $lt = $rm->analisaLongTerm->first();
            if ($lt) {
                $rawGroup = strtoupper(trim((string)($lt->group ?? '')));
                $ujiKristal = strtolower(trim((string)($lt->uji_kristal ?? '')));
                $groupName = 'Grup A';
                $shelfLife = '~30 Hari (Stabil)';
                $badge = 'success';

                if (str_contains($rawGroup, 'B') || $rawGroup === 'B') {
                    $groupName = 'Grup B';
                    $shelfLife = '~14 Hari (Sedang)';
                    $badge = 'warning';
                } elseif (str_contains($rawGroup, 'C') || $rawGroup === 'C' || $ujiKristal === 'positif') {
                    $groupName = 'Grup C';
                    $shelfLife = '3 - 7 Hari (Kritis)';
                    $badge = 'danger';
                }

                $sugarDetails = [
                    'uji_kristal' => $lt->uji_kristal ?: ($ujiKristal === 'positif' ? 'Positif (Kristalisasi)' : 'Negatif'),
                    'group'       => $groupName,
                    'shelf_life'  => $shelfLife,
                    'badge'       => $badge,
                    'warna'       => $lt->warna ?? '-',
                    'aroma'       => $lt->aroma ?? '-',
                ];
            }
        }

        $testedCount = $compliantCount + $nonCompliantCount;
        $pct = $totalParams > 0 ? round(($compliantCount / $totalParams) * 100) : 0;

        return [
            'material_type'        => $jb ? $jb->nama : ($rm->jenis ?? 'Raw Material'),
            'category'             => $rm->material_category ?? 'general',
            'standards'            => $paramsList,
            'sugar_analysis'       => $sugarDetails,
            'total_params'         => $totalParams,
            'tested_count'         => $testedCount,
            'compliant_count'      => $compliantCount,
            'non_compliant_count'  => $nonCompliantCount,
            'compliance_pct'       => $pct,
            'is_fully_compliant'   => ($nonCompliantCount === 0 && $testedCount > 0),
        ];
    }

    /**
     * Dedicated Endpoint: Get Transaction Standards Breakdown (Raw Material)
     * Menampilkan detail standar mutu dan parameter per transaksi kedatangan RM
     */
    public function getTransactionStandards(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 25);
        $statusFilter = $request->input('status', 'all');
        $search = $request->input('search');

        $query = $this->applyRmFilters(IdentitasRM::query(), $request);

        if (!empty($search)) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('no_spb', 'LIKE', "%{$s}%")
                  ->orWhere('lot_batch', 'LIKE', "%{$s}%")
                  ->orWhere('supplier', 'LIKE', "%{$s}%")
                  ->orWhere('jenis', 'LIKE', "%{$s}%");
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'release') {
                $query->whereHas('transaksiAnalisa', function ($q) {
                    $q->where('disposisi', 'LIKE', '%release%')->where('disposisi', 'NOT LIKE', '%bersyarat%');
                });
            } elseif ($statusFilter === 'bersyarat') {
                $query->whereHas('transaksiAnalisa', function ($q) {
                    $q->where('disposisi', 'LIKE', '%bersyarat%');
                });
            } elseif ($statusFilter === 'reject') {
                $query->whereHas('transaksiAnalisa', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('disposisi', 'LIKE', '%reject%')->orWhere('disposisi', 'LIKE', '%tolak%');
                    });
                });
            } elseif ($statusFilter === 'pending' || $statusFilter === 'draft') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('transaksiAnalisa')
                      ->orWhereHas('transaksiAnalisa', function ($sub) {
                          $sub->whereNull('disposisi')->orWhere('disposisi', '')->orWhere('status', 'draft');
                      });
                });
            }
        }

        $query->with([
            'transaksiAnalisa.details',
            'samplingDokumen',
            'samplingKondisiMobil',
            'analisaLongTerm' => function ($q) {
                $q->orderByDesc('id');
            }
        ]);

        $rmData = $query->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();

        // Fallback jika hasil kosong pada filter tanggal
        if ($rmData->isEmpty() && empty($search)) {
            $rmData = IdentitasRM::with([
                'transaksiAnalisa.details',
                'samplingDokumen',
                'samplingKondisiMobil',
                'analisaLongTerm' => function ($q) {
                    $q->orderByDesc('id');
                }
            ])->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();
        }

        $items = [];
        $totalTransactions = 0;
        $totalParamsTested = 0;
        $totalCompliantParams = 0;
        $totalNonCompliantParams = 0;

        foreach ($rmData as $rm) {
            $totalTransactions++;
            $disp = $rm->transaksiAnalisa->first()?->disposisi ?? 'Pending';
            $keterangan = $rm->transaksiAnalisa->first()?->keterangan ?? '-';

            $qty = $rm->jumlah_kedatangan !== null
                ? number_format((float) $rm->jumlah_kedatangan, 0, ',', '.') . ' Kg'
                : '-';

            $standardsDetail = $this->buildTransactionStandardsDetail($rm);
            $totalParamsTested += $standardsDetail['tested_count'];
            $totalCompliantParams += $standardsDetail['compliant_count'];
            $totalNonCompliantParams += $standardsDetail['non_compliant_count'];

            $dokumen = $rm->samplingDokumen;
            $dokumenStatus = 'Lengkap';
            if ($dokumen) {
                if (!$dokumen->coa) $dokumenStatus = 'COA Kurang';
                elseif (!$dokumen->surat_jalan) $dokumenStatus = 'SJ Kurang';
                elseif (!$dokumen->packing_list) $dokumenStatus = 'PL Kurang';
            } else {
                $dokumenStatus = 'Belum Sampling';
            }

            $trukStatus = 'Belum Diperiksa';
            if ($rm->samplingKondisiMobil) {
                $isClean = in_array(strtolower(trim((string)$rm->samplingKondisiMobil->bersih)), ['ya', 'yes', '1', 'bersih', 'sesuai']);
                $trukStatus = $isClean ? 'Sesuai Standar' : 'Tidak Sesuai';
            }

            $items[] = [
                'id'                  => $rm->id,
                'no_spb'              => $rm->no_spb ?: ('ID-' . $rm->id),
                'lot_batch'           => $rm->lot_batch ?: ($rm->no_spb ?: '-'),
                'tanggal'             => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y, H:i') : '-',
                'tanggal_short'       => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y') : '-',
                'supplier'            => $rm->supplier ?? '-',
                'jenis'               => $rm->jenis ?? 'Raw Material',
                'material_category'   => $rm->material_category ?? 'general',
                'jumlah'              => $qty,
                'disposisi'           => $disp ?: 'Pending',
                'keterangan'          => $keterangan,
                'dokumen_status'      => $dokumenStatus,
                'truk_status'         => $trukStatus,
                'standards_detail'    => $standardsDetail,
                'compliance_summary'  => [
                    'total'         => $standardsDetail['total_params'],
                    'compliant'     => $standardsDetail['compliant_count'],
                    'non_compliant' => $standardsDetail['non_compliant_count'],
                    'tested'        => $standardsDetail['tested_count'],
                    'pct'           => $standardsDetail['compliance_pct'],
                ],
                'detail_url'          => route('rmpm.show', $rm->id),
                'analisa_url'         => route('rmpm.analisa', $rm->id),
            ];
        }

        $overallPct = $totalParamsTested > 0 ? round(($totalCompliantParams / $totalParamsTested) * 100) : 100;

        return response()->json([
            'success' => true,
            'data'    => $items,
            'summary' => [
                'total_transactions'      => $totalTransactions,
                'total_parameters_tested' => $totalParamsTested,
                'total_compliant'         => $totalCompliantParams,
                'total_non_compliant'     => $totalNonCompliantParams,
                'overall_compliance_pct'  => $overallPct,
            ]
        ]);
    }

    /**
     * Get Material Quality Trends & Standard Limits (Diagram Analisa Mutu vs Standar)
     * Menampilkan perbandingan diagram nilai parameter lab vs standar mutu (Brix, pH, KA, Kotoran, dll)
     */
    public function getMaterialQualityTrends(Request $request): JsonResponse
    {
        $materialInput = $request->input('material', $request->input('jenis', 'Gula Tebu'));
        $period = $request->input('period', 'all');
        $date = $request->input('date');

        // Resolve MasterJenisBahan
        $jb = $this->resolveMasterJenisBahan($materialInput);
        if (!$jb) {
            $jb = MasterJenisBahan::where('status', true)->where('nama', 'LIKE', '%GULA TEBU%')->first();
        }

        $masterName = $jb ? $jb->nama : $materialInput;

        // Query available materials from IdentitasRM for quick switcher
        $availableMaterials = IdentitasRM::select('jenis')
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->distinct()
            ->get()
            ->map(function ($item) {
                $jUpper = strtoupper(trim((string)$item->jenis));
                $category = 'kering';
                if (str_contains($jUpper, 'GULA') || str_contains($jUpper, 'SUGAR') || str_contains($jUpper, 'KELAPA') || str_contains($jUpper, 'TEBU')) {
                    $category = 'gula';
                } elseif (str_contains($jUpper, 'GARAM')) {
                    $category = 'garam';
                } elseif (str_contains($jUpper, 'CAIR') || str_contains($jUpper, 'GLUKOSA') || str_contains($jUpper, 'KECAP') || str_contains($jUpper, 'CARAMEL') || str_contains($jUpper, 'KARAMEL') || str_contains($jUpper, 'FRUC')) {
                    $category = 'cair';
                }
                $cnt = IdentitasRM::where('jenis', $item->jenis)->count();
                return [
                    'name'     => $item->jenis,
                    'category' => $category,
                    'count'    => $cnt,
                ];
            })
            ->sortByDesc('count')
            ->values();

        // Standards for this resolved material
        $standards = $jb
            ? MasterStandarRm::where('status', true)->where('id_jenis_bahan', $jb->id)->get()
            : collect();

        // Query transactions for this material
        $query = IdentitasRM::query();

        // Filter by period / date
        if ($period === 'today') {
            $query->whereDate('tanggal_kedatangan', now()->toDateString());
        } elseif ($period === '7days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(7)->toDateString());
        } elseif ($period === '30days') {
            $query->whereDate('tanggal_kedatangan', '>=', now()->subDays(30)->toDateString());
        } elseif ($period === 'custom' && $date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }

        // Match material name
        $query->where(function ($q) use ($masterName, $materialInput) {
            $cleanMaster = strtoupper(trim((string)$masterName));
            $cleanInput = strtoupper(trim((string)$materialInput));

            $q->whereRaw('UPPER(jenis) = ?', [$cleanMaster])
              ->orWhereRaw('UPPER(jenis) = ?', [$cleanInput])
              ->orWhere('jenis', 'LIKE', "%{$cleanMaster}%")
              ->orWhere('jenis', 'LIKE', "%{$cleanInput}%");

            if (str_contains($cleanMaster, 'GULA TEBU')) {
                $q->orWhere('jenis', 'GT')->orWhere('jenis', 'Gula Tebu');
            } elseif (str_contains($cleanMaster, 'GULA KELAPA')) {
                $q->orWhere('jenis', 'GK')->orWhere('jenis', 'Gula Kelapa');
            } elseif (str_contains($cleanMaster, 'GARAM')) {
                $q->orWhere('jenis', 'LIKE', '%GARAM%');
            }
        });

        $query->with([
            'transaksiAnalisa.details',
            'analisaLongTerm' => function ($q) {
                $q->orderByDesc('id');
            }
        ])->orderBy('tanggal_kedatangan', 'asc')->orderBy('id', 'asc');

        $rms = $query->get();

        // Fallback to all data if empty
        if ($rms->isEmpty()) {
            $fallbackQuery = IdentitasRM::query();
            $fallbackQuery->where(function ($q) use ($masterName, $materialInput) {
                $cleanMaster = strtoupper(trim((string)$masterName));
                $cleanInput = strtoupper(trim((string)$materialInput));

                $q->whereRaw('UPPER(jenis) = ?', [$cleanMaster])
                  ->orWhereRaw('UPPER(jenis) = ?', [$cleanInput])
                  ->orWhere('jenis', 'LIKE', "%{$cleanMaster}%")
                  ->orWhere('jenis', 'LIKE', "%{$cleanInput}%");

                if (str_contains($cleanMaster, 'GULA TEBU')) {
                    $q->orWhere('jenis', 'GT')->orWhere('jenis', 'Gula Tebu');
                } elseif (str_contains($cleanMaster, 'GULA KELAPA')) {
                    $q->orWhere('jenis', 'GK')->orWhere('jenis', 'Gula Kelapa');
                } elseif (str_contains($cleanMaster, 'GARAM')) {
                    $q->orWhere('jenis', 'LIKE', '%GARAM%');
                }
            });
            $rms = $fallbackQuery->with([
                'transaksiAnalisa.details',
                'analisaLongTerm' => function ($q) {
                    $q->orderByDesc('id');
                }
            ])->orderBy('tanggal_kedatangan', 'asc')->orderBy('id', 'asc')->get();
        }

        // Build Parameter Charts Data
        $parametersData = [];

        foreach ($standards as $std) {
            $pName = trim((string)$std->parameter);
            $min = $std->min_standar !== null ? (float)$std->min_standar : null;
            $max = $std->max_standar !== null ? (float)$std->max_standar : null;
            $uom = trim((string)$std->uom);
            $target = trim((string)$std->target_text);

            $stdLabel = '';
            if ($target !== '') {
                $stdLabel = $target;
            } elseif ($min !== null && $max !== null) {
                $stdLabel = "{$min} - {$max}" . ($uom ? " {$uom}" : '');
            } elseif ($min !== null) {
                $stdLabel = "Min {$min}" . ($uom ? " {$uom}" : '');
            } elseif ($max !== null) {
                $stdLabel = "Maks {$max}" . ($uom ? " {$uom}" : '');
            } else {
                $stdLabel = 'Standar';
            }

            $categories = [];
            $values = [];
            $pointDetails = [];
            $numericValues = [];
            $inSpecCount = 0;

            foreach ($rms as $rm) {
                $tx = $rm->transaksiAnalisa->first();
                if (!$tx) continue;

                $d = $tx->details->first(function ($item) use ($pName) {
                    return strtoupper(trim((string)$item->parameter)) === strtoupper($pName);
                });

                $val = null;
                if ($d && $d->nilai !== null && $d->nilai !== '') {
                    $val = $d->nilai;
                } else {
                    $val = $tx->getDetailValue($pName) ?? $tx->getAttribute(strtolower($pName));
                }

                if ($val !== null && $val !== '' && is_numeric($val)) {
                    $numVal = (float)$val;
                    $isCompliant = true;
                    if ($min !== null && $numVal < $min) $isCompliant = false;
                    if ($max !== null && $numVal > $max) $isCompliant = false;

                    if ($isCompliant) $inSpecCount++;
                    $numericValues[] = $numVal;

                    $spbLabel = $rm->no_spb ?: ('ID-' . $rm->id);
                    $dateLabel = $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d/m') : '-';
                    $fullDate = $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y, H:i') : '-';

                    $xLabel = $spbLabel;
                    $categories[] = $xLabel;
                    $values[] = $numVal;

                    $pointDetails[] = [
                        'spb'          => $spbLabel,
                        'lot'          => $rm->lot_batch ?: $spbLabel,
                        'date'         => $dateLabel,
                        'full_date'    => $fullDate,
                        'supplier'     => $rm->supplier ?? '-',
                        'value'        => $numVal,
                        'formatted'    => "{$numVal}" . ($uom ? " {$uom}" : ''),
                        'is_compliant' => $isCompliant,
                        'status_text'  => $isCompliant ? 'Sesuai Standar' : 'Menyimpang (Out of Spec)',
                        'disposisi'    => $tx->disposisi ?: 'Pending',
                    ];
                }
            }

            // Only add if there are points or if it's a quantitative standard with min/max
            if (count($values) > 0 || ($min !== null || $max !== null)) {
                $totalPoints = count($values);
                $avg = $totalPoints > 0 ? round(array_sum($numericValues) / $totalPoints, 2) : 0;
                $pct = $totalPoints > 0 ? round(($inSpecCount / $totalPoints) * 100, 1) : 0;

                $parametersData[] = [
                    'parameter'      => $pName,
                    'jenis_standar'  => $std->jenis_standar ?: 'kuanti',
                    'uom'            => $uom,
                    'min'            => $min,
                    'max'            => $max,
                    'target'         => $target,
                    'standard_label' => $stdLabel,
                    'categories'     => $categories,
                    'values'         => $values,
                    'point_details'  => $pointDetails,
                    'stats'          => [
                        'total'       => $totalPoints,
                        'in_spec'     => $inSpecCount,
                        'out_spec'    => $totalPoints - $inSpecCount,
                        'in_spec_pct' => $pct,
                        'avg'         => $avg,
                        'min_actual'  => count($numericValues) > 0 ? min($numericValues) : null,
                        'max_actual'  => count($numericValues) > 0 ? max($numericValues) : null,
                    ]
                ];
            }
        }

        // Summary log of transactions for this material
        $transactionLog = $rms->map(function ($rm) {
            $tx = $rm->transaksiAnalisa->first();
            $disp = $tx?->disposisi ?? 'Pending';
            return [
                'id'        => $rm->id,
                'no_spb'    => $rm->no_spb ?: ('ID-' . $rm->id),
                'lot_batch' => $rm->lot_batch ?: ($rm->no_spb ?: '-'),
                'date'      => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y') : '-',
                'supplier'  => $rm->supplier ?? '-',
                'jumlah'    => $rm->jumlah_kedatangan !== null ? number_format((float)$rm->jumlah_kedatangan, 0, ',', '.') . ' Kg' : '-',
                'disposisi' => $disp ?: 'Pending',
                'analisa_url' => route('rmpm.analisa', $rm->id),
                'detail_url'  => route('rmpm.show', $rm->id),
            ];
        })->reverse()->values()->take(10);

        return response()->json([
            'success'             => true,
            'material_name'       => $masterName,
            'material_category'   => $jb ? $jb->kategori : 'gula',
            'total_transactions'  => $rms->count(),
            'available_materials' => $availableMaterials,
            'parameters'          => $parametersData,
            'recent_transactions' => $transactionLog,
        ]);
    }

    /**
     * 8. Get Recent Data Table (Data Kedatangan Bahan Terbaru)
     */
    public function getRecentData(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');
        $limit = (int) $request->input('limit', 10);

        $includeRm = $this->shouldIncludeRm($category);
        $includePm = $this->shouldIncludePm($category);

        $items = collect();

        if ($includeRm) {
            $rmQuery = $this->applyRmFilters(IdentitasRM::query(), $request);
            $rmQuery->with([
                'transaksiAnalisa.details',
                'samplingDokumen',
                'samplingKondisiMobil',
                'analisaLongTerm' => function ($q) {
                    $q->orderByDesc('id');
                }
            ]);

            $rmData = $rmQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();

            // Fallback jika kosong pada filter tanggal
            if ($rmData->isEmpty()) {
                $rmData = IdentitasRM::with([
                    'transaksiAnalisa.details',
                    'samplingDokumen',
                    'samplingKondisiMobil',
                    'analisaLongTerm' => function ($q) {
                        $q->orderByDesc('id');
                    }
                ])->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();
            }

            foreach ($rmData as $rm) {
                $disp = $rm->transaksiAnalisa->first()?->disposisi ?? 'Pending';
                $dokumen = $rm->samplingDokumen;
                $dokumenStatus = 'Lengkap';

                if ($dokumen) {
                    if (!$dokumen->coa) $dokumenStatus = 'COA Kurang';
                    elseif (!$dokumen->surat_jalan) $dokumenStatus = 'SJ Kurang';
                    elseif (!$dokumen->packing_list) $dokumenStatus = 'PL Kurang';
                } else {
                    $dokumenStatus = 'Belum Sampling';
                }

                $trukStatus = 'Belum Diperiksa';
                if ($rm->samplingKondisiMobil) {
                    $isClean = in_array(strtolower(trim((string)$rm->samplingKondisiMobil->bersih)), ['ya', 'yes', '1', 'bersih', 'sesuai']);
                    $trukStatus = $isClean ? 'Sesuai Standar' : 'Tidak Sesuai';
                }

                $qty = $rm->jumlah_kedatangan !== null
                    ? number_format((float) $rm->jumlah_kedatangan, 0, ',', '.') . ' Kg'
                    : '-';

                $jenisUpper = strtoupper(trim((string) $rm->jenis));
                $isSugar = str_contains($jenisUpper, 'GULA') 
                    || str_contains($jenisUpper, 'SUGAR') 
                    || str_contains($jenisUpper, 'KELAPA') 
                    || str_contains($jenisUpper, 'TEBU')
                    || $jenisUpper === 'GK' 
                    || $jenisUpper === 'GT';

                $sugarShelfLife = null;
                $sugarGroup = null;
                $sugarBadge = null;

                if ($isSugar) {
                    $lt = $rm->analisaLongTerm->first();
                    $rawGroup = strtoupper(trim((string) ($lt?->group ?? '')));
                    $ujiKristal = strtolower(trim((string) ($lt?->uji_kristal ?? '')));

                    if (str_contains($rawGroup, 'GROUP A') || str_contains($rawGroup, 'GRUP A') || $rawGroup === 'A' || ($ujiKristal === 'negatif' && empty($rawGroup))) {
                        $sugarGroup = 'Grup A';
                        $sugarShelfLife = 'Tahan ~30 Hari';
                        $sugarBadge = 'success';
                    } elseif (str_contains($rawGroup, 'GROUP B') || str_contains($rawGroup, 'GRUP B') || $rawGroup === 'B') {
                        $sugarGroup = 'Grup B';
                        $sugarShelfLife = 'Tahan ~14 Hari';
                        $sugarBadge = 'warning';
                    } elseif (str_contains($rawGroup, 'GROUP C') || str_contains($rawGroup, 'GRUP C') || $rawGroup === 'C' || $ujiKristal === 'positif') {
                        $sugarGroup = 'Grup C';
                        $sugarShelfLife = '3 - 7 Hari (Kritis)';
                        $sugarBadge = 'danger';
                    } else {
                        $sugarGroup = 'Standar';
                        $sugarShelfLife = '~14 - 30 Hari';
                        $sugarBadge = 'info';
                    }
                }

                $standardsDetail = $this->buildTransactionStandardsDetail($rm);

                $items->push([
                    'id' => $rm->id,
                    'tanggal_raw' => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->timestamp : 0,
                    'tanggal' => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y') : '-',
                    'tanggal_time' => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y, H:i') : '-',
                    'no_spb' => $rm->no_spb ?: ('ID-' . $rm->id),
                    'supplier' => $rm->supplier ?? '-',
                    'lot_batch' => $rm->lot_batch ?: ($rm->no_spb ?: '-'),
                    'jumlah' => $qty,
                    'jenis' => $rm->jenis ?? 'Raw Material',
                    'kategori_bahan' => $rm->material_category,
                    'disposisi' => $disp ?: 'Pending',
                    'dokumen_status' => $dokumenStatus,
                    'truk_status' => $trukStatus,
                    'is_sugar' => $isSugar,
                    'sugar_group' => $sugarGroup,
                    'sugar_shelf_life' => $sugarShelfLife,
                    'sugar_badge' => $sugarBadge,
                    'standards_detail' => $standardsDetail,
                    'compliance_summary' => [
                        'total' => $standardsDetail['total_params'],
                        'compliant' => $standardsDetail['compliant_count'],
                        'non_compliant' => $standardsDetail['non_compliant_count'],
                        'tested' => $standardsDetail['tested_count'],
                        'pct' => $standardsDetail['compliance_pct'],
                    ],
                    'detail_url' => route('rmpm.show', $rm->id),
                    'analisa_url' => route('rmpm.analisa', $rm->id),
                ]);
            }
        }

        if ($includePm) {
            $pmQuery = $this->applyPmFilters(PackagingIncoming::query(), $request)->with([
                'supplier',
                'jenisIncoming',
                'jenisMaterial',
                'uom',
                'packagingPouchSampling',
                'packagingInnerOuterSampling',
                'packagingKartonSampling'
            ]);

            $pmData = $pmQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();

            if ($pmData->isEmpty()) {
                $pmData = PackagingIncoming::with([
                    'supplier',
                    'jenisIncoming',
                    'jenisMaterial',
                    'uom',
                    'packagingPouchSampling',
                    'packagingInnerOuterSampling',
                    'packagingKartonSampling'
                ])->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();
            }

            foreach ($pmData as $pm) {
                $disp = $pm->packagingPouchSampling?->rekomendasi
                    ?? $pm->packagingInnerOuterSampling?->rekomendasi
                    ?? $pm->packagingKartonSampling?->rekomendasi
                    ?? 'Pending';

                $hasCoa = $pm->packagingPouchSampling?->coa
                    || $pm->packagingInnerOuterSampling?->coa
                    || $pm->packagingKartonSampling?->coa;

                $dokumenStatus = $hasCoa ? 'COA Lengkap' : 'Belum Sampling';

                $uomName = $pm->uom?->nama ?? 'Unit';
                $qty = $pm->jumlah !== null
                    ? number_format((float) $pm->jumlah, 0, ',', '.') . ' ' . $uomName
                    : '-';

                $jIncomingName = trim((string)($pm->jenisIncoming?->nama ?? 'Packaging'));
                $jMaterialName = trim((string)($pm->jenisMaterial?->nama ?? $jIncomingName));

                // Determine category type: Karton, Pouch, Inner/Outer
                $catLower = strtolower($jIncomingName);
                $categoryType = 'Inner/Outer';
                $samplingUrl = route('rmpm.pm.inner-outer.sampling', $pm->id);

                if (str_contains($catLower, 'pouch')) {
                    $categoryType = 'Pouch';
                    $samplingUrl = route('rmpm.pm.pouch.sampling', $pm->id);
                } elseif (str_contains($catLower, 'karton') || str_contains($catLower, 'kardus')) {
                    $categoryType = 'Karton Box';
                    $samplingUrl = route('rmpm.pm.karton.display', $pm->id);
                }

                // Check defect
                $sObj = $pm->packagingPouchSampling ?? $pm->packagingKartonSampling ?? $pm->packagingInnerOuterSampling;
                $hasDefect = false;
                $defectList = [];
                if ($sObj) {
                    if (strtolower(trim((string)$sObj->konfirmasi_ketidaksesuaian)) === 'ada' || (!empty($sObj->jenis_ketidaksesuaian) && count($sObj->jenis_ketidaksesuaian) > 0)) {
                        $hasDefect = true;
                        $defectList = (array) $sObj->jenis_ketidaksesuaian;
                    }
                }

                $items->push([
                    'id' => $pm->id,
                    'tanggal_raw' => $pm->tanggal_kedatangan ? Carbon::parse($pm->tanggal_kedatangan)->timestamp : 0,
                    'tanggal' => $pm->tanggal_kedatangan ? Carbon::parse($pm->tanggal_kedatangan)->format('d M Y') : '-',
                    'no_spb' => $pm->no_spb ?: ('SPB-' . $pm->id),
                    'no_batch' => $pm->no_batch ?: '-',
                    'no_mobil' => $pm->no_mobil ?: '-',
                    'supplier' => $pm->supplier?->nama ?? $pm->supplier?->nama_supplier ?? '-',
                    'category_name' => $categoryType,
                    'jenis' => $jMaterialName,
                    'lot_batch' => $pm->no_batch ?: ($pm->no_spb ?: '-'),
                    'jumlah' => $qty,
                    'disposisi' => $disp ?: 'Belum Sampling',
                    'dokumen_status' => $dokumenStatus,
                    'has_defect' => $hasDefect,
                    'defect_notes' => !empty($defectList) ? implode(', ', $defectList) : ($hasDefect ? 'Ada Temuan' : 'Bebas Cacat'),
                    'sampling_url' => $samplingUrl,
                    'edit_url' => route('rmpm.pm.incoming.edit', $pm->id),
                    'is_sugar' => false,
                    'sugar_group' => null,
                    'sugar_shelf_life' => null,
                    'sugar_badge' => null,
                ]);
            }
        }

        // Sort gabungan by tanggal_raw desc, ambil limit
        $sortedItems = $items->sortByDesc('tanggal_raw')->values()->take($limit);

        return response()->json([
            'success' => true,
            'data' => $sortedItems,
        ]);
    }

    /**
     * 9. Get Sugar Stability & Shelf Life Analysis (Analisa Ketahanan & Kualitas Gula)
     */
    public function getSugarStability(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $category = $request->input('category', 'all');

        // Jika filter di-set spesifik ke PM murni, kembalikan is_available = false
        if ($category === 'pm') {
            return response()->json([
                'success' => true,
                'is_available' => false,
                'message' => 'Analisa ketahanan gula hanya berlaku untuk Raw Material (RM).',
                'data' => [
                    'labels' => ['Grup A (~30 Hari)', 'Grup B (~14 Hari)', 'Grup C (3-7 Hari)'],
                    'values' => [0, 0, 0],
                    'total_batches' => 0,
                    'group_a' => 0,
                    'group_b' => 0,
                    'group_c' => 0,
                    'kristal_negatif' => 0,
                    'kristal_positif' => 0,
                    'critical_alerts' => [],
                ]
            ]);
        }

        $sugarQuery = IdentitasRM::query()
            ->where(function ($q) {
                $q->where('jenis', 'LIKE', '%GULA%')
                  ->orWhere('jenis', 'LIKE', '%SUGAR%')
                  ->orWhere('jenis', 'LIKE', '%KELAPA%')
                  ->orWhere('jenis', 'LIKE', '%TEBU%')
                  ->orWhere('jenis', '=', 'GK')
                  ->orWhere('jenis', '=', 'GT');
            })
            ->with([
                'analisaLongTerm' => function ($q) {
                    $q->orderByDesc('id');
                },
                'transaksiAnalisa'
            ]);

        // Jika ada filter tanggal, periksa data pada tanggal tersebut
        if ($date) {
            $sugarDateQuery = (clone $sugarQuery)->whereDate('tanggal_kedatangan', $date);
            if ($sugarDateQuery->count() > 0) {
                $sugarItems = $sugarDateQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->get();
            } else {
                // Ambil data gula terbaru sebelum/pada tanggal filter
                $sugarItems = (clone $sugarQuery)
                    ->whereDate('tanggal_kedatangan', '<=', $date)
                    ->orderByDesc('tanggal_kedatangan')
                    ->orderByDesc('id')
                    ->limit(25)
                    ->get();
            }
        } else {
            $sugarItems = $sugarQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit(25)->get();
        }

        // Jika masih kosong sama sekali, ambil data gula terakhir yang ada
        if ($sugarItems->isEmpty()) {
            $sugarItems = IdentitasRM::query()
                ->where(function ($q) {
                    $q->where('jenis', 'LIKE', '%GULA%')
                      ->orWhere('jenis', 'LIKE', '%SUGAR%')
                      ->orWhere('jenis', 'LIKE', '%KELAPA%')
                      ->orWhere('jenis', 'LIKE', '%TEBU%')
                      ->orWhere('jenis', '=', 'GK')
                      ->orWhere('jenis', '=', 'GT');
                })
                ->with([
                    'analisaLongTerm' => function ($q) {
                        $q->orderByDesc('id');
                    },
                    'transaksiAnalisa'
                ])
                ->orderByDesc('id')
                ->limit(25)
                ->get();
        }

        $groupACount = 0;
        $groupBCount = 0;
        $groupCCount = 0;
        $kristalNegatif = 0;
        $kristalPositif = 0;
        $criticalAlerts = collect();

        foreach ($sugarItems as $sugar) {
            $lt = $sugar->analisaLongTerm->first();
            $rawGroup = strtoupper(trim((string) ($lt?->group ?? '')));
            $ujiKristal = strtolower(trim((string) ($lt?->uji_kristal ?? '')));
            $disp = $lt?->disposisi ?? $sugar->transaksiAnalisa->first()?->disposisi;

            if ($ujiKristal === 'negatif') {
                $kristalNegatif++;
            } elseif ($ujiKristal === 'positif') {
                $kristalPositif++;
            }

            if (str_contains($rawGroup, 'GROUP C') || str_contains($rawGroup, 'GRUP C') || $rawGroup === 'C' || $ujiKristal === 'positif') {
                $groupCCount++;

                $tglObj = $sugar->tanggal_kedatangan ? Carbon::parse($sugar->tanggal_kedatangan) : now();
                $agingHari = (int) $tglObj->diffInDays(now());

                $criticalAlerts->push([
                    'id' => $sugar->id,
                    'spb' => $sugar->no_spb ?: ('ID-' . $sugar->id),
                    'supplier' => $sugar->supplier ?? '-',
                    'jenis' => $sugar->jenis ?? 'Gula Kelapa / Tebu',
                    'tanggal' => $tglObj->format('d M Y'),
                    'aging_hari' => $agingHari,
                    'group' => 'Grup C',
                    'masa_simpan' => '3 - 7 Hari',
                    'kristal_status' => $ujiKristal === 'positif' ? 'Kristal Positif' : 'Stabilitas Rendah',
                    'rekomendasi' => 'Prioritas Pelarutan Segera',
                ]);
            } elseif (str_contains($rawGroup, 'GROUP B') || str_contains($rawGroup, 'GRUP B') || $rawGroup === 'B' || $disp === 'Release Bersyarat') {
                $groupBCount++;
            } else {
                // Default ke Grup A (Stabil / Tahan Lama)
                $groupACount++;
                if (empty($ujiKristal)) {
                    $kristalNegatif++;
                }
            }
        }

        $totalBatches = $groupACount + $groupBCount + $groupCCount;
        $groupAPct = $totalBatches > 0 ? round(($groupACount / $totalBatches) * 100, 1) : 0;
        $groupBPct = $totalBatches > 0 ? round(($groupBCount / $totalBatches) * 100, 1) : 0;
        $groupCPct = $totalBatches > 0 ? round(($groupCCount / $totalBatches) * 100, 1) : 0;

        $topUrgent = $criticalAlerts->sortByDesc('aging_hari')->first();

        // Data khusus untuk Grafik Umur Simpan (Aging) Batch Gula vs Batas Kritis
        $agingCategories = [];
        $agingValues = [];
        $agingColors = [];
        $agingDetails = [];

        $sortedForChart = $sugarItems->map(function ($s) {
            $lt = $s->analisaLongTerm->first();
            $rawGroup = strtoupper(trim((string) ($lt?->group ?? '')));
            $ujiKristal = strtolower(trim((string) ($lt?->uji_kristal ?? '')));
            $disp = $lt?->disposisi ?? $s->transaksiAnalisa->first()?->disposisi;

            $group = 'Grup A';
            $limitDays = 30;
            if (str_contains($rawGroup, 'GROUP C') || str_contains($rawGroup, 'GRUP C') || $rawGroup === 'C' || $ujiKristal === 'positif') {
                $group = 'Grup C';
                $limitDays = 7;
            } elseif (str_contains($rawGroup, 'GROUP B') || str_contains($rawGroup, 'GRUP B') || $rawGroup === 'B' || $disp === 'Release Bersyarat') {
                $group = 'Grup B';
                $limitDays = 14;
            }

            $tglObj = $s->tanggal_kedatangan ? Carbon::parse($s->tanggal_kedatangan) : now();
            $agingHari = (int) $tglObj->diffInDays(now());

            $label = $s->no_spb ? (strlen($s->no_spb) > 10 ? '...' . substr($s->no_spb, -8) : $s->no_spb) : ('ID-' . $s->id);

            return [
                'id' => $s->id,
                'label' => $label,
                'spb' => $s->no_spb ?: ('ID-' . $s->id),
                'supplier' => $s->supplier ?? '-',
                'jenis' => $s->jenis ?? 'Gula',
                'tanggal' => $tglObj->format('d M Y'),
                'aging_hari' => $agingHari,
                'group' => $group,
                'limit_days' => $limitDays,
                'is_overdue' => $agingHari > $limitDays,
            ];
        })->sortByDesc('aging_hari')->values()->take(8);

        foreach ($sortedForChart as $item) {
            $agingCategories[] = $item['label'];
            $agingValues[] = $item['aging_hari'];
            if ($item['aging_hari'] > $item['limit_days']) {
                $agingColors[] = '#ef4444'; // Merah (Kritis)
            } elseif ($item['aging_hari'] > ($item['limit_days'] * 0.7)) {
                $agingColors[] = '#f59e0b'; // Kuning (Waspada)
            } else {
                $agingColors[] = '#10b981'; // Hijau (Aman)
            }
            $agingDetails[] = $item;
        }

        return response()->json([
            'success' => true,
            'is_available' => true,
            'data' => [
                'labels' => ['Grup A (~30 Hari)', 'Grup B (~14 Hari)', 'Grup C (3-7 Hari)'],
                'values' => [$groupACount, $groupBCount, $groupCCount],
                'total_batches' => $totalBatches,
                'group_a' => $groupACount,
                'group_b' => $groupBCount,
                'group_c' => $groupCCount,
                'group_a_pct' => $groupAPct,
                'group_b_pct' => $groupBPct,
                'group_c_pct' => $groupCPct,
                'top_urgent' => $topUrgent,
                'kristal_negatif' => $kristalNegatif,
                'kristal_positif' => $kristalPositif,
                'critical_alerts' => $criticalAlerts->sortByDesc('aging_hari')->values()->take(5),
                'aging_chart' => [
                    'categories' => $agingCategories,
                    'values' => $agingValues,
                    'colors' => $agingColors,
                    'details' => $agingDetails,
                    'threshold_days' => 7,
                ],
            ],
        ]);
    }
}
