<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlendingAwalStoreRequest;
use App\Http\Requests\BlendingAwalUpdateRequest;
use App\Http\Requests\ProductionBatchRequest;
use App\Models\BlendingAwal;
use App\Models\BlendingAwalRelation;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BlendingAwalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('BlendingAwal')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $blendingAwal = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $blendingAwal = $blendingAwal->filter(function ($batch) {
                        return $batch->isBlendingAwalComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $blendingAwal = $blendingAwal->filter(function ($batch) {
                        return !$batch->isBlendingAwalComplete();
                    });
                }
            }

            $blendingAwal = $blendingAwal->sortBy(function ($batch) {
                return ($batch->isBlendingAwalComplete()) ? 1 : 0;
            })->values();

            return DataTables::of($blendingAwal)
                ->addIndexColumn()
                ->addColumn('status_blending_awal', function ($data) {
                    $isComplete = $data->isBlendingAwalComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('detail', function ($data) {
                    $showUrl = route('blending-awal.show', ['id' => $data->id]);

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
                ->rawColumns(['status_blending_awal', 'detail', 'action'])
                ->make(true);
        }
        return view('app.blending_awal.index');
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
        $productionBatch = ProductionBatch::with(['BlendingAwal.additionalBatches', 'blendingAfterAdjustMikro'])
            ->findOrFail($id);

        // Ambil semua batch dari range PO
        $allBatches = $productionBatch->batch_range_array;

        // Helper function untuk parse batch range
        $parseBatchRange = function ($range) {
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $range, $matches)) {
                return range((int) $matches[1], (int) $matches[2]);
            }
            return [(int) $range];
        };

        // Ambil IDs BlendingAwal sekali saja
        $blendingAwalIds = $productionBatch->BlendingAwal->pluck('id');

        // Ambil batch yang sudah digunakan di BlendingAwal
        $usedInBlendingAwal = $productionBatch->BlendingAwal
            ->flatMap(fn($item) => $parseBatchRange($item->batch_range))
            ->unique()
            ->toArray();

        // Ambil batch yang sudah digunakan di relasi tambahan
        $usedInRelations = BlendingAwalRelation::whereIn('blending_awal_id', $blendingAwalIds)
            ->pluck('batch')
            ->flatMap(fn($batch) => $parseBatchRange($batch))
            ->unique()
            ->toArray();

        // Batch yang belum terpakai
        $availableBatches = array_values(array_diff($allBatches, $usedInBlendingAwal, $usedInRelations));

        // Ambil batch GGAS yang valid (Release atau Release Bersyarat)
        $validGgasBatches = $productionBatch->ggas
            ->whereIn('disposition', ['Release', 'Release Bersyarat'])
            ->pluck('batch_number')
            ->map(fn($b) => (int) $b)
            ->unique()
            ->toArray();

        // Batch GGAS valid yang masih tersedia
        $availableValidBatches = array_values(
            array_diff(
                array_intersect($availableBatches, $validGgasBatches),
                $usedInRelations
            )
        );

        // Cek apakah semua batch sudah terpakai
        $allCovered = empty($availableValidBatches);

        // Load PO numbers untuk additionalBatches sekali saja
        $additionalBatchPoIds = $productionBatch->BlendingAwal
            ->flatMap(fn($b) => $b->additionalBatches->pluck('production_batch_id'))
            ->unique();

        $poNumbers = ProductionBatch::whereIn('id', $additionalBatchPoIds)
            ->pluck('po_number', 'id');

        // Tandai relasi dan tambahkan PO number
        foreach ($productionBatch->BlendingAwal as $blending) {
            $hasRelation = $blending->additionalBatches->isNotEmpty();

            $blending->has_relation = $hasRelation;
            $blending->related_batches = $hasRelation
                ? $blending->additionalBatches->pluck('batch')->implode(', ')
                : null;

            // Attach PO numbers
            $blending->additionalBatches->each(function ($addBatch) use ($poNumbers) {
                $addBatch->po_number = $poNumbers[$addBatch->production_batch_id] ?? null;
            });
        }

        return view('app.blending_awal.show', compact(['productionBatch', 'allBatches', 'availableValidBatches', 'allCovered']));
    }

    public function store(BlendingAwalStoreRequest $request)
    {
        try {
            $start = (int) $request->batch_start;
            $end = (int) $request->batch_end;

            if ($start > $end) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Batch pertama tidak boleh lebih besar dari Batch kedua.'
                    ],
                    409
                );
            }

            // Ambil angka-angka batch yang sudah dipakai
            $usedNumbers = [];

            $existingRanges = BlendingAwal::where('production_batch_id', $request->production_batch_id)
                ->pluck('batch_range');

            foreach ($existingRanges as $range) {
                [$existingStart, $existingEnd] = explode('-', $range);
                $existingStart = (int) $existingStart;
                $existingEnd = (int) $existingEnd;

                for ($i = $existingStart; $i <= $existingEnd; $i++) {
                    $usedNumbers[] = $i;
                }
            }

            // Validasi angka yang akan digunakan
            for ($i = $start; $i <= $end; $i++) {
                if (in_array($i, $usedNumbers)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Angka batch $i sudah digunakan sebelumnya dan tidak boleh dipakai lagi."
                    ], 422);
                }
            }

            $batchRange = "$start-$end";

            BlendingAwal::create([
                'production_batch_id' => $request->production_batch_id,
                'batch_range' => $batchRange,
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

    public function getLastRevisiBlendingAwal(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|integer|exists:production_batches,id',
            'batch_range' => 'required|string',
        ]);

        $lastRevisi = BlendingAwal::where('production_batch_id', $request->production_batch_id)
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
            'exclude_batch' => 'required|string',
        ]);

        $validDispositions = ['Release', 'Release Bersyarat'];

        // Ambil batch yang ingin dicari tambahan batch-nya
        $selectedBatch = ProductionBatch::findOrFail($request->production_batch_id);
        $poNumber = $selectedBatch->po_number;

        // Convert exclude_batch dari format "1-2" menjadi array [1, 2]
        $exclude = explode('-', $request->exclude_batch);
        $exclude = array_map('intval', $exclude);

        // Helper untuk ambil batch yang valid dari satu PO
        $getAvailableBatchesByPo = function ($po) use ($validDispositions, $exclude) {
            $validGgasBatches = $po->GGAS()
                ->whereIn('disposition', $validDispositions)
                ->pluck('batch_number')
                ->map(fn($b) => (int) $b)
                ->unique()
                ->toArray();

            $usedInBlending = $po->BlendingAwal->flatMap(function ($item) {
                if (preg_match('/(\d+)\s*-\s*(\d+)/', $item->batch_range, $matches)) {
                    return range((int) $matches[1], (int) $matches[2]);
                }
                return [(int) $item->batch_range];
            })->toArray();

            $usedInRelation = DB::table('blending_awal_relations')
                ->join('blending_awal', 'blending_awal_relations.blending_awal_id', '=', 'blending_awal.id')
                ->where('blending_awal.production_batch_id', $po->id)
                ->pluck('batch')
                ->map(fn($b) => (int) $b)
                ->toArray();

            $availableBatches = array_values(array_diff($validGgasBatches, $usedInBlending, $usedInRelation, $exclude));

            return array_map(fn($batch) => [
                'po_id' => $po->id,
                'po_number' => $po->po_number,
                'batch_number' => $batch,
            ], $availableBatches);
        };

        $available = [];

        // Cari batch dari semua production_batch dengan po_number yang sama
        $poGroup = ProductionBatch::where('po_number', $poNumber)->get();

        foreach ($poGroup as $batch) {
            $result = $getAvailableBatchesByPo($batch);
            if (!empty($result)) {
                $available = array_merge($available, $result);
            }
        }

        // Jika belum ketemu batch yang available, cari dari PO lain
        if (empty($available)) {
            $otherPOs = ProductionBatch::where('po_number', '!=', $poNumber)->get();

            foreach ($otherPOs as $po) {
                $result = $getAvailableBatchesByPo($po);
                if (!empty($result)) {
                    $available = array_merge($available, $result);
                }
            }
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

        $usedBatchIds = BlendingAwalRelation::pluck('blending_awal_id')->toArray();

        // Ambil dari PO yang sama dulu
        $mainSame = BlendingAwal::where('production_batch_id', $productionBatchId)
            ->whereNotIn('disposition', $excludedDispositions)
            ->whereNotIn('id', $usedBatchIds)
            ->with('productionBatch')
            ->orderByDesc('id')
            ->get();

        if ($mainSame->isNotEmpty()) {
            $mainBlending = $mainSame;
        } else {
            // Jika tidak ada di PO yang sama, ambil dari PO lain
            $mainBlending = BlendingAwal::where('production_batch_id', '!=', $productionBatchId)
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

    public function storeRevisi(BlendingAwalUpdateRequest $request)
    {
        try {
            $blending = BlendingAwal::findOrFail($request->id_revisi_blending);
            $disposition_revisi = $blending->disposition;

            // Validasi batch tambahan
            if ($disposition_revisi === 'Leveling') {
                if (empty($request->additional_batch) || count(array_filter($request->additional_batch)) < 1) {
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Minimal 1 batch tambahan wajib diisi untuk disposisi "Leveling".'
                        ],
                        409
                    );
                }
            } elseif ($disposition_revisi === 'Jalan Bareng') {
                if (empty($request->additional_batch) || count(array_filter($request->additional_batch)) < 1) {
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
            $exists = BlendingAwal::where('production_batch_id', $request->id_productbatch_revisi_blending)
                ->where('batch_range', $request->batch_range_revisi_blending)
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

            $additionalBatches = is_array($request->additional_batch) ? array_filter($request->additional_batch) : [];
            $poLevelings = is_array($request->production_batch_id_leveling) ? array_filter($request->production_batch_id_leveling) : [];

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

                $new = BlendingAwal::create($base + [
                    'batch_range' => $allBatches,
                    'storage' => $request->storage_revisi ?? null,
                    'is_adjustment' => true
                ]);

                foreach ($additionalBatches as $i => $batch) {
                    DB::table('blending_awal_relations')->insert([
                        'blending_awal_id' => $new->id,
                        'batch' => $batch,
                        'production_batch_id' => $poLevelings[$i] ?? $request->id_productbatch_revisi_blending,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($disposition_revisi === 'Jalan Bareng') {
                $allBatches = $this->mergeBatchRanges($request->batch_range_revisi_blending, $additionalBatches);

                $new = BlendingAwal::create($base + [
                    'batch_range' => $allBatches,
                    'is_adjustment' => false
                ]);

                foreach ($additionalBatches as $i => $batch) {
                    DB::table('blending_awal_relations')->insert([
                        'blending_awal_id' => $new->id,
                        'batch' => $batch,
                        'production_batch_id' => $poLevelings[$i] ?? $request->id_productbatch_revisi_blending,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($disposition_revisi === 'Adjustment') {
                $new = BlendingAwal::create($base + ['is_adjustment' => false]);

                DB::table('blending_after_adjust_mikro')->insert([
                    'production_batch_id' => $request->id_productbatch_revisi_blending,
                    'batch_range' => $request->batch_number_blending,
                    'nomor_blending' => $request->no_blending_revisi,
                    'volume' => $request->volume_revisi,
                ]);
            } else {
                BlendingAwal::create($base + ['is_adjustment' => false]);
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
