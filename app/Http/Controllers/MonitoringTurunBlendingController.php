<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonitoringTurunBlendingStoreRequest;
use App\Http\Requests\MonitoringTurunBlendingUpdateRequest;
use App\Http\Requests\ProductionBatchRequest;
use App\Models\BlendingAwal;
use App\Models\MonitoringTurunBlending;
use App\Models\MonitoringTurunBlendingRelation;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MonitoringTurunBlendingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('monitoringTurunBlending')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $monitoringTurunBlending = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $monitoringTurunBlending = $monitoringTurunBlending->filter(function ($batch) {
                        return $batch->isMonitoringTurunBlendingComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $monitoringTurunBlending = $monitoringTurunBlending->filter(function ($batch) {
                        return !$batch->isMonitoringTurunBlendingComplete();
                    });
                }
            }

            $monitoringTurunBlending = $monitoringTurunBlending->sortBy(function ($batch) {
                return ($batch->isMonitoringTurunBlendingComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($monitoringTurunBlending)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $isComplete = $data->isMonitoringTurunBlendingComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('detail', function ($data) {
                    $showUrl = route('monitoring-turun-blending.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i>
                    </a>
                ';
                })
                ->addColumn('action', function ($data) {
                    return '
                    <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                       <span class="mdi mdi-pencil"></span> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                        <span class="mdi mdi-trash-can"></span> Hapus
                    </button>
                ';
                })
                ->rawColumns(['status', 'detail', 'action'])
                ->make(true);
        }
        return view('app.monitoring_turun_blending.index');
    }

    public function edit($id)
    {
        try {
            $data = ProductionBatch::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ProductionBatchRequest $request)
    {
        try {
            $data = [
                'po_number' => $request->po_number,
                'variant' => $request->variant,
                'date' => $request->date,
                'batch_range' => $request->batch_range,
                'description' => $request->description,
            ];

            ProductionBatch::updateOrCreate(
                ['id' => $request->id],
                $data
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = ProductionBatch::find($request->id);

            if ($data) {
                $data->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try againrred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'MonitoringTurunBlending' => fn($query) => $query->with('additionalBatches')
        ])->findOrFail($id);

        $validDispositions = ['Release', 'Release Bersyarat'];

        $blendingAwal = BlendingAwal::where('production_batch_id', $id)
            ->whereIn('disposition', $validDispositions)
            ->get();

        $all = $blendingAwal;

        // helper: expand a range string into array of integers
        $expandToNumbers = function (?string $str) {
            $str = trim((string)$str);
            if ($str === '') {
                return [];
            }
            // canonical "start-end"
            if (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $str, $m)) {
                return range((int)$m[1], (int)$m[2]);
            }
            // chained like "1-2-3" or single number "5"
            if (strpos($str, '-') !== false) {
                $parts = array_filter(array_map('trim', explode('-', $str)), fn($p) => $p !== '');
                return array_map(fn($p) => (int) filter_var($p, FILTER_SANITIZE_NUMBER_INT), $parts);
            }
            return [(int) filter_var($str, FILTER_SANITIZE_NUMBER_INT)];
        };

        // Build raw groups with numeric expansions
        $grouped = $all->groupBy('batch_range');
        $rawBatchGroups = []; // each item: ['string' => '1-2-3', 'numbers' => [1,2,3]]

        foreach ($grouped as $batchRange => $items) {
            $chosen = $items->sortByDesc(
                fn($item) =>
                is_numeric($item->revisi) ? (int)$item->revisi : 0
            )->first();

            if (! $chosen) continue;

            $fullStringParts = [];
            $numbers = [];

            // main range numbers
            $mainNums = $expandToNumbers($chosen->batch_range);
            $numbers = array_merge($numbers, $mainNums);
            $fullStringParts[] = $chosen->batch_range;

            // related batches from blending_awal_relations
            $relatedBatches = DB::table('blending_awal_relations')
                ->where('blending_awal_id', $chosen->id)
                ->pluck('batch')
                ->toArray();

            foreach ($relatedBatches as $relRange) {
                $relRange = trim((string)$relRange);
                if ($relRange === '') continue;
                $relNums = $expandToNumbers($relRange);
                $numbers = array_merge($numbers, $relNums);
                $fullStringParts[] = $relRange;
            }

            $numbers = array_values(array_unique($numbers));
            sort($numbers, SORT_NUMERIC);

            $rawBatchGroups[] = [
                'string' => implode('-', $fullStringParts),
                'numbers' => $numbers,
            ];
        }

        // Collect numbers already used in MonitoringTurunBlending (for this production batch)
        $usedMonitoringNumbers = [];
        foreach ($productionBatch->MonitoringTurunBlending as $mEntry) {
            $usedMonitoringNumbers = array_merge($usedMonitoringNumbers, $expandToNumbers($mEntry->batch_range));

            $mRelated = DB::table('monitoring_turun_blending_relations')
                ->where('monitoring_turun_blending_id', $mEntry->id)
                ->pluck('batch')
                ->toArray();

            foreach ($mRelated as $mr) {
                $usedMonitoringNumbers = array_merge($usedMonitoringNumbers, $expandToNumbers($mr));
            }
        }
        $usedMonitoringNumbers = array_values(array_unique($usedMonitoringNumbers));

        // Filter out any candidate that overlaps with already used numbers
        $candidates = array_filter($rawBatchGroups, function ($grp) use ($usedMonitoringNumbers) {
            if (empty($grp['numbers'])) return false;
            return empty(array_intersect($grp['numbers'], $usedMonitoringNumbers));
        });

        // Remove candidates that are subsets of another candidate (keep only maximal groups)
        $finalCandidates = [];
        foreach ($candidates as $i => $cand) {
            $isSubset = false;
            foreach ($candidates as $j => $other) {
                if ($i === $j) continue;
                if (empty(array_diff($cand['numbers'], $other['numbers']))) {
                    $isSubset = true;
                    break;
                }
            }
            if (! $isSubset) {
                $finalCandidates[] = $cand;
            }
        }

        // Convert to strings for view
        $filteredBatchGroups = array_map(fn($g) => $g['string'], $finalCandidates);

        foreach ($productionBatch->MonitoringTurunBlending as $data) {
            $data->has_relation = $data->additionalBatches && $data->additionalBatches->isNotEmpty();
            $data->related_batches = $data->has_relation
                ? $data->additionalBatches->pluck('batch')->implode(', ')
                : null;

            foreach ($data->additionalBatches as $addBatch) {
                $po = ProductionBatch::find($addBatch->production_batch_id);
                $addBatch->po_number = $po->po_number ?? null;
            }
        }

        return view('app.monitoring_turun_blending.show', compact(
            'productionBatch',
            'filteredBatchGroups'
        ));
    }

    public function store(MonitoringTurunBlendingStoreRequest $request)
    {
        try {
            $exists = MonitoringTurunBlending::where('production_batch_id', $request->production_batch_id)
                ->where('batch_range', $request->batch)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Batch range ini sudah digunakan untuk Production Batch yang sama.',
                ], 409);
            }

            MonitoringTurunBlending::create([
                'production_batch_id' => $request->production_batch_id,
                'batch_range' => $request->batch_range,
                'nomor_blending' => $request->nomor_blending,
                'volume' => $request->volume,
                'storage' => $request->storage,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLastRevisi(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|integer|exists:production_batches,id',
            'batch_range' => 'required|string',
        ]);


        $lastRevisi = MonitoringTurunBlending::where('production_batch_id', $request->production_batch_id)
            ->where('batch_range', $request->batch_range)
            ->max('revisi');

        // Jika belum ada, revisi dimulai dari 1
        $nextRevisi = is_null($lastRevisi) ? 1 : $lastRevisi + 1;

        return response()->json(['revisi' => $nextRevisi]);
    }

    public function getAvailableAdditionalBatch(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|integer|exists:production_batches,id',
            'exclude_batch' => 'required|string'
        ]);

        $exclude = array_map('intval', explode('-', $request->exclude_batch));

        $validDispositions = ['Release', 'Release Bersyarat', 'Adjustment', 'Resampling'];

        $currentPo = ProductionBatch::findOrFail($request->production_batch_id);
        $poNumber = $currentPo->po_number;

        // helper: proses kandidat untuk daftar production_batch_id tertentu
        $processCandidates = function (array $productionBatchIds) use ($validDispositions, $exclude) {
            $all = BlendingAwal::whereIn('production_batch_id', $productionBatchIds)
                ->whereIn('disposition', $validDispositions)
                ->get();

            // group by composite key production_batch_id|batch_range to avoid mixing across PO
            $grouped = $all->groupBy(function ($item) {
                return $item->production_batch_id . '|' . $item->batch_range;
            });

            $allCandidates = [];

            foreach ($grouped as $key => $items) {
                $chosen = $items->sortByDesc(function ($item) {
                    return is_numeric($item->revisi) ? (int) $item->revisi : 0;
                })->first();

                if (! $chosen) continue;

                $numbers = [];

                // expand main batch_range (contoh "1-3" => [1,2,3])
                if (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $chosen->batch_range, $m)) {
                    $numbers = range((int)$m[1], (int)$m[2]);
                } else {
                    $numbers = [(int) filter_var($chosen->batch_range, FILTER_SANITIZE_NUMBER_INT)];
                }

                // ambil relasi tambahan dan expand tiap entry (bisa "5" atau "7-8")
                $related = DB::table('blending_awal_relations')
                    ->where('blending_awal_id', $chosen->id)
                    ->pluck('batch')
                    ->toArray();

                foreach ($related as $rel) {
                    $rel = trim($rel);
                    if (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $rel, $rm)) {
                        $numbers = array_merge($numbers, range((int)$rm[1], (int)$rm[2]));
                    } elseif ($rel !== '') {
                        $numbers[] = (int) filter_var($rel, FILTER_SANITIZE_NUMBER_INT);
                    }
                }

                // unique dan sort ascending
                $numbers = array_values(array_unique($numbers));
                sort($numbers, SORT_NUMERIC);

                // jika ada irisan dengan exclude, skip
                if (!empty(array_intersect($numbers, $exclude))) {
                    continue;
                }

                // jika batch sudah ada di monitoring_turun_blending untuk PO yang sama, skip
                $monitoringEntries = MonitoringTurunBlending::where('production_batch_id', $chosen->production_batch_id)->get();

                $usedMonitoringNumbers = [];
                foreach ($monitoringEntries as $mEntry) {
                    // expand monitoring main batch_range
                    if (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $mEntry->batch_range, $mm)) {
                        $usedMonitoringNumbers = array_merge($usedMonitoringNumbers, range((int)$mm[1], (int)$mm[2]));
                    } else {
                        $usedMonitoringNumbers[] = (int) filter_var($mEntry->batch_range, FILTER_SANITIZE_NUMBER_INT);
                    }

                    // expand related monitoring batches
                    $mRelated = DB::table('monitoring_turun_blending_relations')
                        ->where('monitoring_turun_blending_id', $mEntry->id)
                        ->pluck('batch')
                        ->toArray();

                    foreach ($mRelated as $mr) {
                        $mr = trim($mr);
                        if (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $mr, $rmr)) {
                            $usedMonitoringNumbers = array_merge($usedMonitoringNumbers, range((int)$rmr[1], (int)$rmr[2]));
                        } elseif ($mr !== '') {
                            $usedMonitoringNumbers[] = (int) filter_var($mr, FILTER_SANITIZE_NUMBER_INT);
                        }
                    }
                }

                $usedMonitoringNumbers = array_values(array_unique($usedMonitoringNumbers));

                // jika ada overlap dengan monitoring yang sudah ada, skip
                if (!empty(array_intersect($numbers, $usedMonitoringNumbers))) {
                    continue;
                }

                $allCandidates[] = [
                    'batch_range' => implode('-', $numbers),
                    'numbers' => $numbers,
                    'source_blending_id' => $chosen->id,
                    'production_batch_id' => $chosen->production_batch_id,
                ];
            }

            // ✅ FILTER: Hapus kandidat yang merupakan subset dari kandidat lain
            $result = [];

            foreach ($allCandidates as $candidate) {
                $isSubset = false;

                // Cek apakah kandidat ini adalah subset dari kandidat lain
                foreach ($allCandidates as $other) {
                    // Skip jika membandingkan dengan diri sendiri
                    if ($candidate['source_blending_id'] === $other['source_blending_id']) {
                        continue;
                    }

                    // Cek apakah semua numbers dari candidate ada di other
                    $diff = array_diff($candidate['numbers'], $other['numbers']);

                    // Jika diff kosong, berarti candidate adalah subset dari other
                    if (empty($diff) && count($candidate['numbers']) < count($other['numbers'])) {
                        $isSubset = true;
                        break;
                    }
                }

                // Hanya tambahkan jika bukan subset
                if (!$isSubset) {
                    $result[] = [
                        'batch_range' => $candidate['batch_range'],
                        'source_blending_id' => $candidate['source_blending_id'],
                        'production_batch_id' => $candidate['production_batch_id'],
                    ];
                }
            }

            return $result;
        };

        // Prioritas: semua production_batch dengan po_number yang sama
        $poGroupIds = ProductionBatch::where('po_number', $poNumber)->pluck('id')->toArray();
        $available = $processCandidates($poGroupIds);

        // Jika tidak ada di PO yang sama, cari dari PO lain (semua production_batch yang po_number berbeda)
        if (empty($available)) {
            $otherPoIds = ProductionBatch::where('po_number', '!=', $poNumber)->pluck('id')->toArray();
            $available = $processCandidates($otherPoIds);
        }

        // lampirkan po_number ke setiap hasil
        if (!empty($available)) {
            $poIds = array_unique(array_column($available, 'production_batch_id'));
            $poMap = ProductionBatch::whereIn('id', $poIds)->pluck('po_number', 'id')->toArray();

            foreach ($available as &$row) {
                $row['po_number'] = $poMap[$row['production_batch_id']] ?? null;
            }
            unset($row);
        }

        return response()->json(['data' => $available]);
    }

    public function getJalanBareng(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id'
        ]);

        $productionBatchId = $request->production_batch_id;
        $excludedDispositions = ['Resampling', 'Reject', 'Repro', 'Adjustment', 'Jalan Bareng', 'Leveling'];

        $usedBatchIds = MonitoringTurunBlendingRelation::pluck('monitoring_turun_blending_id')->toArray();

        // Ambil dari PO yang sama dulu
        $mainSame = MonitoringTurunBlending::where('production_batch_id', $productionBatchId)
            ->whereNotIn('disposition', $excludedDispositions)
            ->whereNotIn('id', $usedBatchIds)
            ->with('productionBatch')
            ->orderByDesc('id')
            ->get();

        if ($mainSame->isNotEmpty()) {
            $mainBlending = $mainSame;
        } else {
            // Jika tidak ada di PO yang sama, ambil dari PO lain
            $mainBlending = MonitoringTurunBlending::where('production_batch_id', '!=', $productionBatchId)
                ->whereNotIn('disposition', $excludedDispositions)
                ->whereNotIn('id', $usedBatchIds)
                ->with('productionBatch')
                ->orderByDesc('id')
                ->get();
        }

        $result = $mainBlending->map(function ($item) {
            return [
                'id' => $item->id,
                'batch_range' => $item->batch_range,
                'po_id' => $item->production_batch_id,
                'po_number' => $item->productionBatch?->po_number ?? null,
                'nomor_blending' => $item->nomor_blending,
            ];
        })->values();

        return response()->json(['data' => $result]);
    }

    public function storeRevisi(MonitoringTurunBlendingUpdateRequest $request)
    {
        try {
            $blending = MonitoringTurunBlending::findOrFail($request->id_revisi_blending);
            $disposition_revisi = $blending->disposition;

            // ✅ Konversi ke array terlebih dahulu jika berupa string
            $additionalBatchesRaw = $request->additional_batch;
            if (is_string($additionalBatchesRaw)) {
                $additionalBatchesRaw = !empty($additionalBatchesRaw) ? [$additionalBatchesRaw] : [];
            } elseif (!is_array($additionalBatchesRaw)) {
                $additionalBatchesRaw = [];
            }

            $additionalBatches = array_filter($additionalBatchesRaw);

            // Validasi batch tambahan
            if ($disposition_revisi === 'Leveling') {
                if (count($additionalBatches) < 1) {
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Minimal 1 batch tambahan wajib diisi untuk disposisi "Leveling".'
                        ],
                        409
                    );
                }
            } elseif ($disposition_revisi === 'Jalan Bareng') {
                if (count($additionalBatches) < 1) {
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Batch tambahan wajib diisi untuk disposisi "Jalan Bareng".'
                        ],
                        409
                    );
                }
            }

            // Cek duplikasi revisi
            $exists = MonitoringTurunBlending::where('production_batch_id', $request->id_productbatch_revisi_blending)
                ->where('batch_range', $request->batch_number_blending)
                ->where('revisi', $request->revisi_blending)
                ->exists();

            if ($exists) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Data revisi sudah ada, coba generate ulang.'
                    ],
                    409
                );
            }

            // ✅ Pastikan production_batch_id_leveling juga di-handle dengan benar
            $poLevelingsRaw = $request->production_batch_id_leveling;
            if (is_string($poLevelingsRaw)) {
                $poLevelingsRaw = !empty($poLevelingsRaw) ? [$poLevelingsRaw] : [];
            } elseif (!is_array($poLevelingsRaw)) {
                $poLevelingsRaw = [];
            }

            $poLevelings = array_filter($poLevelingsRaw);

            $base = [
                'production_batch_id' => $request->id_productbatch_revisi_blending,
                'batch_range' => $request->batch_number_blending,
                'nomor_blending' => $request->no_blending_revisi,
                'volume' => $request->volume_revisi,
                'revisi' => $request->revisi_blending,
                'storage' => $request->storage_revisi ?? null,
                'not_standard' => false,
            ];

            if ($disposition_revisi === 'Leveling') {
                $allBatches = $this->mergeBatchRanges($request->batch_number_blending, $additionalBatches);

                $new = MonitoringTurunBlending::create($base + [
                    'batch_range' => $allBatches,
                    'storage' => $request->storage_revisi ?? null,
                    'is_adjustment' => true
                ]);

                foreach ($additionalBatches as $i => $batch) {
                    DB::table('monitoring_turun_blending_relations')->insert([
                        'monitoring_turun_blending_id' => $new->id,
                        'batch' => $batch,
                        'production_batch_id' => $poLevelings[$i] ?? $request->id_productbatch_revisi_blending,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($disposition_revisi === 'Jalan Bareng') {
                $allBatches = $this->mergeBatchRanges($request->batch_number_blending, $additionalBatches);

                $new = MonitoringTurunBlending::create($base + [
                    'batch_range' => $allBatches,
                    'is_adjustment' => false
                ]);

                foreach ($additionalBatches as $i => $batch) {
                    DB::table('monitoring_turun_blending_relations')->insert([
                        'monitoring_turun_blending_id' => $new->id,
                        'batch' => $batch,
                        'production_batch_id' => $poLevelings[$i] ?? $request->id_productbatch_revisi_blending,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                MonitoringTurunBlending::create($base + ['is_adjustment' => false]);
            }

            $blending->update(['not_standard' => false]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function mergeBatchRanges($mainBatch, $additionalBatches)
    {
        // Parse batch utama
        $allNumbers = [];

        // Ambil angka dari batch utama (misal: "5-6" atau "5")
        if (strpos($mainBatch, '-') !== false) {
            $parts = explode('-', $mainBatch);
            foreach ($parts as $part) {
                $allNumbers[] = trim($part);
            }
        } else {
            $allNumbers[] = trim($mainBatch);
        }

        // ✅ Pastikan $additionalBatches adalah array
        if (!is_array($additionalBatches)) {
            $additionalBatches = [];
        }

        // Tambahkan batch tambahan
        foreach ($additionalBatches as $batch) {
            // Handle batch range (misal: "7-8") atau single batch (misal: "7")
            if (strpos($batch, '-') !== false) {
                $parts = explode('-', $batch);
                foreach ($parts as $part) {
                    $num = trim($part);
                    if (!in_array($num, $allNumbers)) {
                        $allNumbers[] = $num;
                    }
                }
            } else {
                $num = trim($batch);
                if (!in_array($num, $allNumbers)) {
                    $allNumbers[] = $num;
                }
            }
        }

        // Urutkan angka
        sort($allNumbers, SORT_NUMERIC);

        // Gabungkan dengan strip
        return implode('-', $allNumbers);
    }
}
