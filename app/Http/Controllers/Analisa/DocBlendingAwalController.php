<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocBlendingAwalExport;
use App\Http\Controllers\Controller;
use App\Models\DocBlendingAwal;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class DocBlendingAwalController extends Controller
{
    /**
     * Tampilkan Halaman Form Hasil Analisis Proses Blending (FRM/QLB/04/104/005-01)
     */
    public function index(Request $request)
    {
        $batches = ProductionBatch::where(function ($q) {
                $q->has('BlendingAwal')
                  ->orHas('blendingAfterAdjustMikro');
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        if ($batches->isEmpty()) {
            $batches = ProductionBatch::orderBy('date', 'desc')->get();
        }

        $selectedBatchId = $request->input('po_id') ?? $batches->first()?->id;
        $initialData = null;

        if ($selectedBatchId) {
            $initialData = $this->getDocumentData($selectedBatchId);
        }

        return view('app.doc_blending_awal.index', [
            'batches' => $batches,
            'selectedBatchId' => $selectedBatchId,
            'initialData' => $initialData
        ]);
    }

    /**
     * AJAX Endpoint untuk Fetch Data berdasarkan PO ID
     */
    public function fetchData(Request $request)
    {
        $poId = $request->input('po_id');
        if (!$poId) {
            return response()->json([
                'status' => 'error',
                'message' => 'PO ID wajib dipilih.'
            ], 400);
        }

        $docData = $this->getDocumentData($poId);

        return response()->json([
            'status' => 'success',
            'data' => $docData
        ]);
    }

    /**
     * Helper untuk mengambil data dokumen tersimpan atau auto-generate dari database
     * 1 Lembar = 1 Tangki / Blending (1 Pasangan Batch)
     */
    public function getDocumentData($poId)
    {
        $batch = ProductionBatch::with([
            'BlendingAwal.user',
            'BlendingAwal.color',
            'blendingAfterAdjustMikro',
            'monitoringTurunBlending.user',
        ])->find($poId);

        if (!$batch) {
            return null;
        }

        $savedDoc = DocBlendingAwal::where('production_batch_id', $poId)->first();

        // 1. Ambil data operasional Blending dari API Portal Produksi (127.0.0.1:8001)
        // (Jam Mulai dari dissolver.jam_selesai_transfer, Jam Selesai dari blending_awal.updated_at, dan Kode Shift & Grup)
        $prdDocInfo = null;
        try {
            $prdUrl = rtrim(env('PRODUCTION_URL', 'http://127.0.0.1:8001/'), '/') . '/api/blending-awal/doc-info';
            $prdRes = Http::timeout(3)->get($prdUrl, [
                'po_number' => $batch->po_number,
                'production_batch_id' => $batch->id,
            ]);
            if ($prdRes->successful()) {
                $prdDocInfo = $prdRes->json('data');
            }
        } catch (\Throwable $e) {}

        // Auto-generate Batch Blocks dari data Blending Awal
        // Pembagian Blending dibuat 1 Halaman untuk 1 Pasangan Batch Blending (1-2, 3-4, 5-6, …)
        // Halaman 1: Batch 1-2 Awal, After Adj 1, Batch 7-8 Awal, After Adj 1, dst (Tangki yang sama)
        // Halaman 2: Batch 3-4 Awal, After Adj 1, Batch 9-10 Awal, After Adj 1, After Adj 2, dst (Tangki yang sama)
        // Halaman 3: Batch 5-6 Awal, After Adj 1, Batch 11-12 Awal, After Adj 1, dst (Tangki yang sama)
        $blendingItems = $batch->BlendingAwal ? $batch->BlendingAwal->sortBy('id')->values() : collect();
        $turunItems = $batch->monitoringTurunBlending ? $batch->monitoringTurunBlending->sortBy('id')->values() : collect();
        $batchBlocks = [];
        $detectedAnalis = '';

        if ($blendingItems->count() > 0) {
            // Pengelompokan Halaman berdasarkan Pasangan Batch (batch_range):
            // 1 Lembar = 1 Pasangan Batch (e.g. 1-2, 3-4, 5-6)
            // Setiap batch_range unik menjadi 1 halaman/lembar tersendiri
            $groupedByBatchRange = $blendingItems->groupBy(function($item) {
                return (string)($item->batch_range ?? 'unknown');
            });

            // Urutkan grup berdasarkan nomor batch awal (ascending)
            $sortedTankGroups = $groupedByBatchRange->sortBy(function($items, $key) {
                if (preg_match('/^(\d+)/', $key, $m)) {
                    return (int)$m[1];
                }
                return 9999;
            })->values();

            $totalCycles = $sortedTankGroups->count();

            foreach ($sortedTankGroups as $cIdx => $tankItems) {
                if ($tankItems->isEmpty()) continue;

                // Urutkan item di dalam lembar: berdasarkan nomor batch awal (e.g. 1-2 sebelum 7-8), lalu berdasarkan ID
                $sortedItems = $tankItems->sortBy(function($item) {
                    $bNum = 0;
                    if (preg_match('/^(\d+)/', (string)$item->batch_range, $m)) {
                        $bNum = (int)$m[1];
                    }
                    return sprintf('%05d_%010d', $bNum, $item->id);
                })->values();

                $processedItems = [];
                foreach ($sortedItems as $item) {
                    $isZeroRecheck = ($item->bj == 0 && $item->brix == 0 && ($item->visco == 0 || $item->visco === null) && ($item->ph == 0 || $item->ph === null));
                    if ($isZeroRecheck && !empty($processedItems)) {
                        $lastIdx = count($processedItems) - 1;
                        $adjParts = [];
                        if ($item->adjustment_qty_air) $adjParts[] = 'Air: ' . $item->adjustment_qty_air . ' L';
                        if ($item->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $item->adjustment_qty_garam . ' kg';
                        if ($item->adjustment_qty_gula) $adjParts[] = 'Gula: ' . $item->adjustment_qty_gula . ' kg';
                        if ($item->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $item->adjustment_qty_caramel . ' kg';
                        if ($item->adjustment_remark) $adjParts[] = $item->adjustment_remark;
                        $recheckNote = !empty($adjParts) ? implode('; ', $adjParts) : '';

                        if ($recheckNote && $recheckNote !== '-') {
                            $processedItems[$lastIdx]['merged_adj'] = (!empty($processedItems[$lastIdx]['merged_adj']) ? ($processedItems[$lastIdx]['merged_adj'] . ' | ') : '') . $recheckNote;
                        }
                        continue;
                    }

                    $processedItems[] = [
                        'model' => $item,
                        'merged_adj' => '',
                    ];
                }

                if (empty($processedItems)) continue;

                $first = $processedItems[0]['model'];
                $last = end($processedItems)['model'];

                $cycleAnalis = '';
                foreach ($processedItems as $ci) {
                    if ($ci['model']->user) {
                        $cycleAnalis = $ci['model']->user->name;
                        break;
                    }
                }

                if (!$detectedAnalis && $cycleAnalis) {
                    $detectedAnalis = $cycleAnalis;
                }

                $rows = [];
                $samplingIndex = 1;

                foreach ($processedItems as $entry) {
                    $item = $entry['model'];

                    // Jam Serah Terima = Jam Scan (prioritas scanned_at / qr_scan_logs, fallback created_at waktu input)
                    $scanTime = $item->scanned_at;
                    if (!$scanTime) {
                        $scanTime = DB::table('qr_scan_logs')
                            ->whereIn('qc_type', ['blending-awal', 'blending_awal'])
                            ->where('qc_id', $item->id)
                            ->value('scanned_at');
                    }
                    if (!$scanTime) {
                        $scanTime = $item->created_at;
                    }
                    $jamScan = $scanTime ? Carbon::parse($scanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '';

                    // Jam di Tabel “Waktu & Adjustment” = Jam Disposisi Foreman QC
                    $jamDisp = '';
                    if ($item->disposition && $item->updated_at) {
                        $jamDisp = $item->updated_at->setTimezone('Asia/Jakarta')->format('H:i');
                    } else {
                        try {
                            $prodBl = DB::table('production.blending_awal')
                                ->where('id', $item->id)
                                ->first();
                            if (!$prodBl && !empty($item->batch_range)) {
                                $prodBatch = DB::table('production.production_batches')
                                    ->where('po_number', $batch->po_number)
                                    ->orWhere('po_number', 'LIKE', '%' . $batch->po_number . '%')
                                    ->first();
                                $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
                                $prodBl = DB::table('production.blending_awal')
                                    ->where('production_batch_id', $pBatchId)
                                    ->where('batch_range', $item->batch_range)
                                    ->when($item->revisi !== null, fn($q) => $q->where('revisi', $item->revisi))
                                    ->first();
                            }
                            if ($prodBl && $prodBl->disposition && $prodBl->updated_at) {
                                $jamDisp = Carbon::parse($prodBl->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                            }
                        } catch (\Throwable $e) {}
                    }

                    $adjParts = [];
                    if ($item->adjustment_qty_air) $adjParts[] = 'Air: ' . $item->adjustment_qty_air . ' L';
                    if ($item->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $item->adjustment_qty_garam . ' kg';
                    if ($item->adjustment_qty_gula) $adjParts[] = 'Gula: ' . $item->adjustment_qty_gula . ' kg';
                    if ($item->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $item->adjustment_qty_caramel . ' kg';
                    if ($item->adjustment_remark) $adjParts[] = $item->adjustment_remark;
                    $adjDetail = !empty($adjParts) ? implode('; ', $adjParts) : '';

                    if (!empty($entry['merged_adj'])) {
                        $adjDetail = ($adjDetail ? ($adjDetail . ' | ') : '') . $entry['merged_adj'];
                    }

                    $waktuAdjLines = [];
                    if ($jamDisp) $waktuAdjLines[] = $jamDisp;
                    if ($adjDetail) $waktuAdjLines[] = $adjDetail;
                    $waktuAdjStr = !empty($waktuAdjLines) ? implode("\n", $waktuAdjLines) : ($jamDisp ?: '-');

                    // Disposisi & Keterangan
                    $disp = $item->disposition ?: 'Release';
                    $dispRemark = $item->disposition_remark ?: '';
                    $dispStr = ($dispRemark && $dispRemark !== $disp) ? ($disp . "\n" . $dispRemark) : $disp;

                    $rows[] = [
                        'sampling_ke' => (string)$samplingIndex,
                        'vol_tangki' => $item->volume !== null ? (string)$item->volume : '',
                        'serah_terima_jam' => $jamScan,
                        'serah_terima_pic' => $item->user ? $item->user->name : '',
                        'bj' => $this->formatBlankIfZero($item->bj),
                        'brix' => $this->formatBlankIfZero($item->brix),
                        'ph' => $this->formatBlankIfZero($item->ph),
                        'nacl' => $this->formatBlankIfZero($item->nacl),
                        'visco' => $this->formatBlankIfZero($item->visco),
                        'organo' => $item->organo ?: 'OK',
                        'aroma' => $item->aroma ?: 'OK',
                        'warna' => $item->color ? $item->color->name : 'Hitam',
                        'buih' => 'Tidak Ada',
                        'aw' => $this->formatBlankIfZero($item->aw),
                        'waktu_adjustment' => $waktuAdjStr,
                        'disposisi' => $dispStr,
                    ];
                    $samplingIndex++;
                }

                // Pad rows hingga minimal 4 baris untuk tampilan awal
                while (count($rows) < 4) {
                    $rows[] = [
                        'sampling_ke' => (string)(count($rows) + 1),
                        'vol_tangki' => '',
                        'serah_terima_jam' => '',
                        'serah_terima_pic' => '',
                        'bj' => '',
                        'brix' => '',
                        'ph' => '',
                        'nacl' => '',
                        'visco' => '',
                        'organo' => '',
                        'aroma' => '',
                        'warna' => '',
                        'buih' => '',
                        'aw' => '',
                        'waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
                }

                // Lookup Shift & Grup dan Jam Produksi dari Portal Produksi (API / Direct Query)
                $bRange = (string)($first->batch_range ?? '');
                $noBl = (string)($first->nomor_blending ?? '');
                $blendingShiftGroup = '';
                $jamProduksiStr = '';

                // 1. Cek dari response API Portal Produksi (127.0.0.1:8001)
                if ($prdDocInfo) {
                    $prdItem = null;
                    if ($bRange !== '' && isset($prdDocInfo['by_batch_range'][$bRange])) {
                        $prdItem = $prdDocInfo['by_batch_range'][$bRange];
                    } elseif ($noBl !== '' && isset($prdDocInfo['by_nomor_blending'][$noBl])) {
                        $prdItem = $prdDocInfo['by_nomor_blending'][$noBl];
                    }
                    if ($prdItem) {
                        $blendingShiftGroup = $prdItem['kode_shift_grup'] ?? '';
                        $jamProduksiStr = $prdItem['jam_produksi'] ?? '';
                    }
                }

                // 2. Fallback query jika API PRD belum terhubung / kosong
                if (empty($jamProduksiStr) || empty($blendingShiftGroup)) {
                    $jamStart = '';
                    $jamRelease = '';
                    try {
                        $pBatchId = $this->resolveProductionBatchId($batch->po_number, $batch->id);

                        $blRow = null;
                        if ($pBatchId) {
                            if ($bRange !== '') {
                                $blRow = DB::table('production.blending_awal')
                                    ->where('production_batch_id', $pBatchId)
                                    ->where('batch_range', $bRange)
                                    ->first();
                            }
                            if (!$blRow && $noBl !== '') {
                                $blRow = DB::table('production.blending_awal')
                                    ->where('production_batch_id', $pBatchId)
                                    ->where('nomor_blending', $noBl)
                                    ->first();
                            }
                            if (!$blRow) {
                                $blRow = DB::table('production.monitoring_turun_blending')
                                    ->where('production_batch_id', $pBatchId)
                                    ->when($bRange !== '', fn($q) => $q->where('batch_range', $bRange))
                                    ->first();
                            }
                            if (!$blRow) {
                                $blRow = DB::table('production.blending_awal')
                                    ->where('production_batch_id', $pBatchId)
                                    ->orderBy('id')
                                    ->first();
                            }

                            // Jam Mulai dari dissolver.jam_selesai_transfer
                            $nums = [];
                            if (strpos($bRange, '-') !== false) {
                                [$s, $e] = explode('-', $bRange);
                                for ($i = (int)$s; $i <= (int)$e; $i++) $nums[] = (string)$i;
                            } elseif ($bRange !== '') {
                                $nums[] = $bRange;
                            }
                            if (!empty($nums)) {
                                $dissTransfer = DB::table('production.dissolver')
                                    ->where('production_batch_id', $pBatchId)
                                    ->whereIn('batch_number', $nums)
                                    ->whereNotNull('jam_selesai_transfer')
                                    ->orderByDesc('jam_selesai_transfer')
                                    ->value('jam_selesai_transfer');
                                if ($dissTransfer) {
                                    $jamStart = substr((string)$dissTransfer, 0, 5);
                                }
                            }
                        }

                        if ($blRow) {
                            if (empty($blendingShiftGroup)) {
                                $blendingShiftGroup = $this->formatShiftGroup($blRow->shift ?? null, $blRow->group ?? null);
                            }
                            if (empty($jamStart)) {
                                if (!empty($blRow->waktu_kirim_sample)) {
                                    $jamStart = Carbon::parse($blRow->waktu_kirim_sample)->setTimezone('Asia/Jakarta')->format('H:i');
                                } elseif (!empty($blRow->created_at)) {
                                    $jamStart = Carbon::parse($blRow->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                                }
                            }
                            if (!empty($blRow->updated_at)) {
                                $jamRelease = Carbon::parse($blRow->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                            }
                        }
                    } catch (\Throwable $e) {}

                    if (empty($jamStart) && $first && $first->created_at) {
                        $jamStart = Carbon::parse($first->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                    }

                    if (empty($jamRelease)) {
                        $releaseItem = $tankItems->filter(fn($i) => stripos((string)$i->disposition, 'release') !== false)->last();
                        if (!$releaseItem) $releaseItem = $tankItems->last();
                        if ($releaseItem && $releaseItem->updated_at) {
                            $jamRelease = Carbon::parse($releaseItem->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    }

                    if (empty($jamProduksiStr)) {
                        if (!empty($jamStart) && !empty($jamRelease) && $jamRelease !== $jamStart) {
                            $jamProduksiStr = "{$jamStart} - {$jamRelease}";
                        } elseif (!empty($jamStart)) {
                            $jamProduksiStr = $jamStart;
                        } elseif (!empty($jamRelease)) {
                            $jamProduksiStr = $jamRelease;
                        } else {
                            $jamProduksiStr = '-';
                        }
                    }
                }

                if (empty($blendingShiftGroup)) {
                    $blendingShiftGroup = 'Shift 1 - Grup A';
                }

                $distinctBatches = $tankItems->pluck('batch_range')->filter()->unique()->values()->implode(', ');

                $batchBlocks[] = [
                    'halaman' => ($cIdx + 1) . ' / ' . $totalCycles,
                    'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                    'tanggal_produksi' => $first->created_at ? $first->created_at->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                    'jam_produksi' => $jamProduksiStr,
                    'kode_shift_grup' => $blendingShiftGroup,
                    'batch' => $distinctBatches ?: (string)($first->batch_range ?: (($cIdx * 2 + 1) . '-' . ($cIdx * 2 + 2))),
                    'no_blending' => (string)($first->nomor_blending ?: ($cIdx + 1)),
                    'volume_awal' => $first->volume ? ($first->volume . ' L') : '',
                    'catatan' => $savedDoc?->catatan ?: '',
                    'pic_sampling' => $savedDoc?->pic_sampling ?: '',
                    'pic_analis' => $cycleAnalis ?: ($detectedAnalis ?: ($savedDoc?->pic_analis ?: (auth()->user()->name ?? ''))),
                    'pic_checker' => $savedDoc?->pic_checker ?: '',
                    'rows' => $rows,
                ];
            }
        } else {
            $pBatchId = $this->resolveProductionBatchId($batch->po_number, $batch->id);
            for ($p = 1; $p <= 3; $p++) {
                $bStart = ($p - 1) * 2 + 1;
                $bEnd = $bStart + 1;
                $bRange = "{$bStart}-{$bEnd}";
                $shiftStr = '';
                $jamStr = '';
                if ($prdDocInfo) {
                    $prdItem = $prdDocInfo['by_batch_range'][$bRange] ?? ($prdDocInfo['by_nomor_blending'][(string)$p] ?? null);
                    if ($prdItem) {
                        $shiftStr = $prdItem['kode_shift_grup'] ?? '';
                        $jamStr = $prdItem['jam_produksi'] ?? '';
                    }
                }

                if (empty($shiftStr) || empty($jamStr)) {
                    if ($pBatchId) {
                        $blRow = DB::table('production.blending_awal')
                            ->where('production_batch_id', $pBatchId)
                            ->where(function($q) use ($bRange, $p) {
                                $q->where('batch_range', $bRange)->orWhere('nomor_blending', (string)$p);
                            })
                            ->first();
                        if (!$blRow) {
                            $blRow = DB::table('production.monitoring_turun_blending')
                                ->where('production_batch_id', $pBatchId)
                                ->where(function($q) use ($bRange, $p) {
                                    $q->where('batch_range', $bRange)->orWhere('nomor_blending', (string)$p);
                                })
                                ->first();
                        }
                        if (!$blRow) {
                            $blRow = DB::table('production.blending_awal')
                                ->where('production_batch_id', $pBatchId)
                                ->first();
                        }
                        if ($blRow) {
                            if (empty($shiftStr)) {
                                $shiftStr = $this->formatShiftGroup($blRow->shift ?? null, $blRow->group ?? null);
                            }
                            if (empty($jamStr)) {
                                if (!empty($blRow->waktu_kirim_sample)) {
                                    $jamStr = Carbon::parse($blRow->waktu_kirim_sample)->setTimezone('Asia/Jakarta')->format('H:i');
                                } elseif (!empty($blRow->created_at)) {
                                    $jamStr = Carbon::parse($blRow->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                                }
                            }
                        }
                    }
                }

                if (empty($shiftStr)) {
                    $shiftStr = 'Shift 1 - Grup A';
                }

                $defaultRows = [];
                for ($r = 1; $r <= 6; $r++) {
                    $defaultRows[] = [
                        'sampling_ke' => (string)$r,
                        'vol_tangki' => '',
                        'serah_terima_jam' => '',
                        'serah_terima_pic' => '',
                        'bj' => '',
                        'brix' => '',
                        'ph' => '',
                        'nacl' => '',
                        'visco' => '',
                        'organo' => $r === 1 ? 'OK' : '',
                        'aroma' => $r === 1 ? 'OK' : '',
                        'warna' => $r === 1 ? 'Hitam' : '',
                        'buih' => $r === 1 ? 'Tidak Ada' : '',
                        'aw' => '',
                        'waktu_adjustment' => '',
                        'disposisi' => $r === 1 ? 'Release' : '',
                    ];
                }

                // Turun Blending baris ke-7
                $defaultRows[] = [
                    'sampling_ke' => '',
                    'vol_tangki' => 'Awal',
                    'serah_terima_jam' => '',
                    'serah_terima_pic' => '',
                    'bj' => '',
                    'brix' => '',
                    'ph' => '',
                    'nacl' => '',
                    'visco' => '',
                    'organo' => '',
                    'aroma' => '',
                    'warna' => '',
                    'buih' => '',
                    'aw' => '',
                    'waktu_adjustment' => '',
                    'disposisi' => '',
                ];

                for ($r = 7; $r <= 10; $r++) {
                    $defaultRows[] = [
                        'sampling_ke' => (string)$r,
                        'vol_tangki' => '',
                        'serah_terima_jam' => '',
                        'serah_terima_pic' => '',
                        'bj' => '',
                        'brix' => '',
                        'ph' => '',
                        'nacl' => '',
                        'visco' => '',
                        'organo' => '',
                        'aroma' => '',
                        'warna' => '',
                        'buih' => '',
                        'aw' => '',
                        'waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
                }

                $batchBlocks[] = [
                    'halaman' => $p . ' / 3',
                    'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => $jamStr ?: '08:00 - 15:00',
                    'kode_shift_grup' => $shiftStr,
                    'batch' => "{$bStart}-{$bEnd}",
                    'no_blending' => (string)$p,
                    'volume_awal' => '10000 L',
                    'catatan' => $savedDoc?->catatan ?: '',
                    'pic_sampling' => $savedDoc?->pic_sampling ?: '',
                    'pic_analis' => $savedDoc?->pic_analis ?: (auth()->user()->name ?? ''),
                    'pic_checker' => $savedDoc?->pic_checker ?: '',
                    'rows' => $defaultRows,
                ];
            }
        }

        return [
            'is_saved' => $savedDoc ? true : false,
            'id' => $savedDoc?->id,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $savedDoc?->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
            'halaman' => $savedDoc?->halaman ?: '1',
            'batch_blocks' => $batchBlocks,
            'catatan' => $savedDoc?->catatan ?: '',
            'pic_sampling' => $savedDoc?->pic_sampling ?: '',
            'pic_analis' => $savedDoc?->pic_analis ?: ($detectedAnalis ?: (auth()->user()->name ?? '')),
            'pic_checker' => $savedDoc?->pic_checker ?: '',
        ];
    }

    /**
     * Simpan / Perbarui Form Dokumen Blending
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'tanggal_record_doc' => 'nullable|date',
        ]);

        $batchBlocks = $request->input('batch_blocks', []);
        $firstBlock = !empty($batchBlocks[0]) ? $batchBlocks[0] : [];
        $totalBlocks = count($batchBlocks);

        $doc = DocBlendingAwal::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', $totalBlocks > 0 ? "1 / {$totalBlocks}" : '1'),
                'batch_blocks' => $batchBlocks,
                'catatan' => $request->input('catatan') ?? ($firstBlock['catatan'] ?? ''),
                'pic_sampling' => $request->input('pic_sampling') ?? ($firstBlock['pic_sampling'] ?? ''),
                'pic_analis' => $request->input('pic_analis') ?? ($firstBlock['pic_analis'] ?? ''),
                'pic_checker' => $request->input('pic_checker') ?? ($firstBlock['pic_checker'] ?? ''),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir Hasil Analisis Proses Blending berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi FRM/QLB/04/104/005-01
     * (1 Lembar / Sheet = 1 Siklus Blending / Tangki)
     */
    public function exportExcel(Request $request)
    {
        if (!auth()->check() || !in_array(strtolower(trim(auth()->user()->role ?? '')), ['foreman', 'supervisor', 'head of dapartement', 'head of department', 'dept head'], true)) {
            abort(403, 'Akses ditolak. Fitur export dokumen resmi hanya diperuntukkan bagi Foreman, Supervisor, atau Head of Department.');
        }

        $poId = $request->input('po_id');
        if (!$poId) {
            return redirect()->back()->with('error', 'Silakan pilih PO terlebih dahulu.');
        }

        $docData = $this->getDocumentData($poId);
        if (!$docData) {
            return redirect()->back()->with('error', 'Data PO tidak ditemukan.');
        }

        $cleanPo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $docData['po_number']);
        $fileName = 'HASIL_ANALISIS_PROSES_BLENDING_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DocBlendingAwalExport($docData),
            $fileName
        );
    }

    /**
     * Tampilan Khusus Cetak / Print Dokumen (1 Lembar = 1 Tangki/Blending)
     */
    public function printView($id)
    {
        if (!auth()->check() || !in_array(strtolower(trim(auth()->user()->role ?? '')), ['foreman', 'supervisor', 'head of dapartement', 'head of department', 'dept head'], true)) {
            abort(403, 'Akses ditolak. Fitur cetak/print hanya dapat diakses oleh role Foreman, Supervisor, atau Head of Department.');
        }

        $docData = $this->getDocumentData($id);
        if (!$docData) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return view('app.doc_blending_awal.print', [
            'data' => $docData
        ]);
    }

    /**
     * Format nilai analisis: jika 0, 0.0, null, atau kosong maka dijadikan blank
     */
    private function formatBlankIfZero($val): string
    {
        if ($val === null || $val === '' || $val === 'null' || $val === '-' || $val === '0' || $val === 0 || $val === 0.0 || $val === '0.0' || $val === '0.00') {
            return '';
        }
        if (is_numeric($val) && floatval($val) == 0) {
            return '';
        }
        return (string)$val;
    }

    /**
     * Cari ID production_batches di DB produksi secara akurat via PO number
     */
    private function resolveProductionBatchId($poNumber, $digimonBatchId = null)
    {
        if (empty($poNumber)) return null;

        $pb = DB::table('production.production_batches')->where('po_number', $poNumber)->first();
        if ($pb) return $pb->id;

        $norm = trim(preg_replace('/^PO[\s\-_]*/i', '', $poNumber));
        if (!empty($norm)) {
            $pb = DB::table('production.production_batches')->where('po_number', $norm)->first();
            if ($pb) return $pb->id;
        }

        $numeric = preg_replace('/\D/', '', $poNumber);
        if (strlen($numeric) >= 6) {
            $pb = DB::table('production.production_batches')
                ->where('po_number', $numeric)
                ->orWhere('po_number', 'LIKE', "%{$numeric}%")
                ->first();
            if ($pb) return $pb->id;
        }

        if ($digimonBatchId) {
            $pb = DB::table('production.production_batches')->where('id', $digimonBatchId)->first();
            if ($pb) return $pb->id;
        }

        return null;
    }

    /**
     * Format Shift & Grup standar: Shift 1 - Grup A
     */
    private function formatShiftGroup($shift, $group): string
    {
        $s = trim((string)$shift);
        $g = trim((string)$group);
        if ($s !== '' && $g !== '') {
            return "Shift {$s} - Grup {$g}";
        }
        if ($s !== '') {
            return "Shift {$s}";
        }
        if ($g !== '') {
            return "Grup {$g}";
        }
        return '';
    }
}
