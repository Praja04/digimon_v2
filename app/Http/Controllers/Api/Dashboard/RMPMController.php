<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\IdentitasRM;
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
        if ($includeRm) {
            $rmQuery = IdentitasRM::query();
            if ($date) {
                $rmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $rmIds = $rmQuery->pluck('id');
            $rmCount = $rmIds->count();
        }

        $pmCount = 0;
        $pmIds = collect();
        if ($includePm) {
            $pmQuery = PackagingIncoming::query();
            if ($date) {
                $pmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $pmIds = $pmQuery->pluck('id');
            $pmCount = $pmIds->count();
        }

        $totalKedatangan = $rmCount + $pmCount;

        // Acceptance & Rejection
        $acceptCount = 0;
        $rejectCount = 0;

        // RM Dispositions
        if ($rmIds->isNotEmpty()) {
            $rmDispositions = TransaksiAnalisaRM::whereIn('id_identitas', $rmIds)
                ->whereNotNull('disposisi')
                ->where('disposisi', '!=', '')
                ->pluck('disposisi');

            foreach ($rmDispositions as $disp) {
                $dispLower = strtolower(trim((string) $disp));
                if (in_array($dispLower, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $acceptCount++;
                } elseif (in_array($dispLower, ['reject', 'ditolak'], true)) {
                    $rejectCount++;
                }
            }
        }

        // PM Dispositions
        if ($pmIds->isNotEmpty()) {
            $pmRekomendasi = collect()
                ->merge(PackagingPouchSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'))
                ->merge(PackagingInnerOuterSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'))
                ->merge(PackagingKartonSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'));

            foreach ($pmRekomendasi as $rek) {
                $rekLower = strtolower(trim((string) $rek));
                if (in_array($rekLower, ['release', 'diterima', 'release bersyarat', 'diterima bersyarat'], true)) {
                    $acceptCount++;
                } elseif (in_array($rekLower, ['reject', 'ditolak'], true)) {
                    $rejectCount++;
                }
            }
        }

        if ($totalKedatangan === 0) {
            $acceptanceRate = 0;
            $rejectionRate = 0;
        } else {
            $acceptanceRate = round(($acceptCount / $totalKedatangan) * 100, 1);
            $rejectionRate = round(($rejectCount / $totalKedatangan) * 100, 1);
        }

        // Document Completeness
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
        $packaging = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $curStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $rmDaily = $includeRm
                ? IdentitasRM::whereDate('tanggal_kedatangan', $curStr)->count()
                : 0;

            $pmDaily = $includePm
                ? PackagingIncoming::whereDate('tanggal_kedatangan', $curStr)->count()
                : 0;

            $rawMaterial[] = $rmDaily;
            $packaging[] = $pmDaily;

            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'raw_material' => $rawMaterial,
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
        $totalItems = 0;

        if ($includeRm) {
            $rmQuery = IdentitasRM::query();
            if ($date) {
                $rmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $rmIds = $rmQuery->pluck('id');
            $totalItems += $rmIds->count();

            if ($rmIds->isNotEmpty()) {
                $rmDispositions = TransaksiAnalisaRM::whereIn('id_identitas', $rmIds)
                    ->whereNotNull('disposisi')
                    ->where('disposisi', '!=', '')
                    ->pluck('disposisi');

                foreach ($rmDispositions as $disp) {
                    $dispLower = strtolower(trim((string) $disp));
                    if ($dispLower === 'release' || $dispLower === 'diterima') {
                        $release++;
                    } elseif ($dispLower === 'release bersyarat' || $dispLower === 'diterima bersyarat') {
                        $bersyarat++;
                    } elseif ($dispLower === 'reject' || $dispLower === 'ditolak') {
                        $reject++;
                    }
                }
            }
        }

        if ($includePm) {
            $pmQuery = PackagingIncoming::query();
            if ($date) {
                $pmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $pmIds = $pmQuery->pluck('id');
            $totalItems += $pmIds->count();

            if ($pmIds->isNotEmpty()) {
                $pmRekomendasi = collect()
                    ->merge(PackagingPouchSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'))
                    ->merge(PackagingInnerOuterSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'))
                    ->merge(PackagingKartonSampling::whereIn('packaging_incoming_id', $pmIds)->pluck('rekomendasi'));

                foreach ($pmRekomendasi as $rek) {
                    $rekLower = strtolower(trim((string) $rek));
                    if ($rekLower === 'release' || $rekLower === 'diterima') {
                        $release++;
                    } elseif ($rekLower === 'release bersyarat' || $rekLower === 'diterima bersyarat') {
                        $bersyarat++;
                    } elseif ($rekLower === 'reject' || $rekLower === 'ditolak') {
                        $reject++;
                    }
                }
            }
        }

        $pending = max(0, $totalItems - ($release + $bersyarat + $reject));

        // Jika tidak ada data sama sekali pada tanggal tersebut, return array kosong untuk empty state
        if ($totalItems === 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => [],
                    'values' => [],
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Release', 'Diterima Bersyarat', 'Reject', 'Pending / Belum Sampling'],
                'values' => [$release, $bersyarat, $reject, $pending],
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

        if ($includeRm) {
            $rmQuery = IdentitasRM::query();
            if ($date) {
                $rmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $rmMaterials = $rmQuery->select('jenis', DB::raw('COUNT(*) as total'))
                ->whereNotNull('jenis')
                ->where('jenis', '!=', '')
                ->groupBy('jenis')
                ->get();

            foreach ($rmMaterials as $rm) {
                $name = trim((string) $rm->jenis);
                $materialCounts[$name] = ($materialCounts[$name] ?? 0) + $rm->total;
            }
        }

        if ($includePm) {
            $pmQuery = PackagingIncoming::query()->with(['jenisIncoming', 'jenisMaterial']);
            if ($date) {
                $pmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $pmList = $pmQuery->get();

            foreach ($pmList as $pm) {
                $name = trim((string) ($pm->jenisMaterial?->nama ?? $pm->jenisIncoming?->nama ?? 'Packaging'));
                $materialCounts[$name] = ($materialCounts[$name] ?? 0) + 1;
            }
        }

        $sorted = $materialCounts->sortDesc()->take(5);

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $sorted->keys()->values(),
                'values' => $sorted->values(),
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
            $rmQuery = IdentitasRM::query();
            if ($date) {
                $rmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $rmList = $rmQuery->with('transaksiAnalisa')->get();

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
            $pmQuery = PackagingIncoming::query()->with([
                'supplier',
                'packagingPouchSampling',
                'packagingInnerOuterSampling',
                'packagingKartonSampling'
            ]);
            if ($date) {
                $pmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $pmList = $pmQuery->get();

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

        // Sort descending by rate then by total
        usort($performance, function ($a, $b) {
            return ($b['rate'] <=> $a['rate']) ?: ($b['total'] <=> $a['total']);
        });

        $topSuppliers = array_slice($performance, 0, 5);

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_column($topSuppliers, 'supplier'),
                'values' => array_column($topSuppliers, 'rate'),
            ]
        ]);
    }

    /**
     * 6. Get Kondisi Kendaraan (Radar Chart)
     */
    public function getVehicleCondition(Request $request): JsonResponse
    {
        $date = $request->input('date');

        $query = IdentitasRM::query();
        if ($date) {
            $query->whereDate('tanggal_kedatangan', $date);
        }
        $identitasIds = $query->pluck('id');

        $conditions = SamplingKondisiMobil::whereIn('id_identitas', $identitasIds)->get();

        // Jika tidak ada pada tanggal filter, ambil sampel kondisi mobil terbaru (30 hari terakhir) agar radar chart tetap informatif
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
                    'Kebersihan',
                    'Kondisi Kering',
                    'Bebas Benda Asing',
                    'Kondisi Cacat / Fisik',
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
     * 7. Get Temuan Fisik Kemasan (Bar Chart)
     */
    public function getPackagingFindings(Request $request): JsonResponse
    {
        $date = $request->input('date');

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
                    // misal 'sesuai_std': cacat jika val == 'no'
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
            $rmQuery = IdentitasRM::query()->with(['transaksiAnalisa', 'samplingDokumen']);
            if ($date) {
                $rmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $rmData = $rmQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();

            foreach ($rmData as $rm) {
                $disp = $rm->transaksiAnalisa->first()?->disposisi ?? 'Pending';
                $dokumen = $rm->samplingDokumen;
                $dokumenStatus = 'Lengkap';

                if ($dokumen) {
                    if (!$dokumen->coa) $dokumenStatus = 'COA Missing';
                    elseif (!$dokumen->surat_jalan) $dokumenStatus = 'SJ Missing';
                    elseif (!$dokumen->packing_list) $dokumenStatus = 'PL Missing';
                } else {
                    $dokumenStatus = 'Belum Sampling';
                }

                $qty = $rm->jumlah_kedatangan !== null
                    ? number_format((float) $rm->jumlah_kedatangan, 0, ',', '.') . ' Kg'
                    : '-';

                $items->push([
                    'tanggal_raw' => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->timestamp : 0,
                    'tanggal' => $rm->tanggal_kedatangan ? Carbon::parse($rm->tanggal_kedatangan)->format('d M Y') : '-',
                    'supplier' => $rm->supplier ?? '-',
                    'lot_batch' => $rm->lot_batch ?: ($rm->no_spb ?: '-'),
                    'jumlah' => $qty,
                    'jenis' => $rm->jenis ?? 'Raw Material',
                    'disposisi' => $disp ?: 'Pending',
                    'dokumen_status' => $dokumenStatus,
                ]);
            }
        }

        if ($includePm) {
            $pmQuery = PackagingIncoming::query()->with([
                'supplier',
                'jenisIncoming',
                'jenisMaterial',
                'uom',
                'packagingPouchSampling',
                'packagingInnerOuterSampling',
                'packagingKartonSampling'
            ]);
            if ($date) {
                $pmQuery->whereDate('tanggal_kedatangan', $date);
            }
            $pmData = $pmQuery->orderByDesc('tanggal_kedatangan')->orderByDesc('id')->limit($limit)->get();

            foreach ($pmData as $pm) {
                $disp = $pm->packagingPouchSampling?->rekomendasi
                    ?? $pm->packagingInnerOuterSampling?->rekomendasi
                    ?? $pm->packagingKartonSampling?->rekomendasi
                    ?? 'Pending';

                $hasCoa = $pm->packagingPouchSampling?->coa
                    || $pm->packagingInnerOuterSampling?->coa
                    || $pm->packagingKartonSampling?->coa;

                $dokumenStatus = $hasCoa ? 'Lengkap' : 'Belum Sampling';

                $uomName = $pm->uom?->nama ?? 'Pcs';
                $qty = $pm->jumlah !== null
                    ? number_format((float) $pm->jumlah, 0, ',', '.') . ' ' . $uomName
                    : '-';

                $jenisName = $pm->jenisMaterial?->nama
                    ?? $pm->jenisIncoming?->nama
                    ?? 'Packaging';

                $items->push([
                    'tanggal_raw' => $pm->tanggal_kedatangan ? Carbon::parse($pm->tanggal_kedatangan)->timestamp : 0,
                    'tanggal' => $pm->tanggal_kedatangan ? Carbon::parse($pm->tanggal_kedatangan)->format('d M Y') : '-',
                    'supplier' => $pm->supplier?->nama ?? $pm->supplier?->nama_supplier ?? '-',
                    'lot_batch' => $pm->no_batch ?: ($pm->no_spb ?: '-'),
                    'jumlah' => $qty,
                    'jenis' => $jenisName,
                    'disposisi' => $disp ?: 'Pending',
                    'dokumen_status' => $dokumenStatus,
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
}
