<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocBlendingAwalExport;
use App\Http\Controllers\Controller;
use App\Models\DocBlendingAwal;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if ($savedDoc && !request()->has('force_db') && !request()->has('reset')) {
            $blocks = $savedDoc->batch_blocks ?: [];
            $totalBlocks = count($blocks);

            // Pastikan setiap blok memiliki properti catatan & PIC lengkap dan baris ternormalisasi
            foreach ($blocks as $idx => &$blk) {
                if (!isset($blk['catatan']) || $blk['catatan'] === '') {
                    $blk['catatan'] = $savedDoc->catatan ?: '';
                }
                if (!isset($blk['pic_sampling']) || $blk['pic_sampling'] === '') {
                    $blk['pic_sampling'] = $savedDoc->pic_sampling ?: '';
                }
                if (!isset($blk['pic_analis']) || $blk['pic_analis'] === '') {
                    $blk['pic_analis'] = $savedDoc->pic_analis ?: '';
                }
                if (!isset($blk['pic_checker']) || $blk['pic_checker'] === '') {
                    $blk['pic_checker'] = $savedDoc->pic_checker ?: '';
                }
                if (!isset($blk['halaman']) || $blk['halaman'] === '') {
                    $blk['halaman'] = ($idx + 1) . ' / ' . max(1, $totalBlocks);
                }

                // Perbaiki kode_shift_grup jika masih bernilai dummy atau format lama
                if (empty($blk['kode_shift_grup']) || $blk['kode_shift_grup'] === 'Shift 1 / Grup A' || str_starts_with($blk['kode_shift_grup'], 'Shift ')) {
                    try {
                        $prodBatch = DB::table('production.production_batches')
                            ->where('po_number', $batch->po_number)
                            ->orWhere('po_number', 'LIKE', '%' . $batch->po_number . '%')
                            ->first();
                        $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
                        $noBl = $blk['no_blending'] ?? null;
                        $bRange = $blk['batch'] ?? null;

                        $blRow = DB::table('production.blending_awal')
                            ->where(function($q) use ($pBatchId, $batch) {
                                $q->where('production_batch_id', $pBatchId)
                                  ->orWhere('production_batch_id', $batch->id);
                            })
                            ->when($bRange, fn($q) => $q->where('batch_range', $bRange))
                            ->first();

                        if (!$blRow && $noBl) {
                            $blRow = DB::table('production.blending_awal')
                                ->where(function($q) use ($pBatchId, $batch) {
                                    $q->where('production_batch_id', $pBatchId)
                                      ->orWhere('production_batch_id', $batch->id);
                                })
                                ->where('nomor_blending', $noBl)
                                ->first();
                        }

                        if (!$blRow) {
                            $blRow = DB::table('production.monitoring_turun_blending')
                                ->where(function($q) use ($pBatchId, $batch) {
                                    $q->where('production_batch_id', $pBatchId)
                                      ->orWhere('production_batch_id', $batch->id);
                                })
                                ->when($bRange, fn($q) => $q->where('batch_range', $bRange))
                                ->first();
                        }

                        if ($blRow) {
                            $s = $blRow->shift ? (string)$blRow->shift : '';
                            $g = $blRow->group ? (string)$blRow->group : '';
                            if ($s !== '' && $g !== '') {
                                $blk['kode_shift_grup'] = "{$s}/{$g}";
                            } elseif ($s !== '') {
                                $blk['kode_shift_grup'] = $s;
                            } elseif ($g !== '') {
                                $blk['kode_shift_grup'] = $g;
                            } else {
                                $blk['kode_shift_grup'] = '';
                            }
                        } else {
                            $blk['kode_shift_grup'] = '';
                        }
                    } catch (\Throwable $e) {}
                }

                // Perbaiki jam_produksi jika masih bernilai dummy atau format rentang lama (Jam Tambah Blending Awal)
                if (empty($blk['jam_produksi']) || $blk['jam_produksi'] === '08:00' || str_contains($blk['jam_produksi'], ' - ')) {
                    try {
                        if (!empty($blRow?->created_at)) {
                            $blk['jam_produksi'] = Carbon::parse($blRow->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                        } elseif (!empty($blRow?->waktu_kirim_sample)) {
                            $blk['jam_produksi'] = Carbon::parse($blRow->waktu_kirim_sample)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    } catch (\Throwable $e) {}
                }

                // Normalisasi baris jika sebelumnya tersimpan dengan format lama & sanitasi nilai 0 jadi blank
                if (!empty($blk['rows'])) {
                    foreach ($blk['rows'] as $rIdx => &$row) {
                        if (isset($row['sampling_ke']) && strtolower(trim((string)$row['sampling_ke'])) === 'awal') {
                            $row['sampling_ke'] = (string)($rIdx + 1);
                            $row['vol_tangki'] = 'Awal';
                        }
                        if (isset($row['bj'])) $row['bj'] = $this->formatBlankIfZero($row['bj']);
                        if (isset($row['brix'])) $row['brix'] = $this->formatBlankIfZero($row['brix']);
                        if (isset($row['ph'])) $row['ph'] = $this->formatBlankIfZero($row['ph']);
                        if (isset($row['nacl'])) $row['nacl'] = $this->formatBlankIfZero($row['nacl']);
                        if (isset($row['visco'])) $row['visco'] = $this->formatBlankIfZero($row['visco']);
                        if (isset($row['aw'])) $row['aw'] = $this->formatBlankIfZero($row['aw']);
                    }
                    unset($row);
                }
            }
            unset($blk);

            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $batch->variant,
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $savedDoc->halaman ?: '1',
                'batch_blocks' => $blocks,
                'catatan' => $savedDoc->catatan ?: '',
                'pic_sampling' => $savedDoc->pic_sampling ?: '',
                'pic_analis' => $savedDoc->pic_analis ?: '',
                'pic_checker' => $savedDoc->pic_checker ?: '',
            ];
        }

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
            // Pengelompokan Halaman berdasarkan Pasangan Tangki Blending (Rotasi 3 Tangki):
            // Halaman 1 (Tangki 1): Batch 1-2 Awal/Adj, Batch 7-8 Awal/Adj, dst.
            // Halaman 2 (Tangki 2): Batch 3-4 Awal/Adj, Batch 9-10 Awal/Adj, dst.
            // Halaman 3 (Tangki 3): Batch 5-6 Awal/Adj, Batch 11-12 Awal/Adj, dst.
            $groupedByTankSlot = $blendingItems->groupBy(function($item) {
                // 1. Jika nomor_blending secara eksplisit ada (1, 2, atau 3)
                if (!empty($item->nomor_blending)) {
                    if (preg_match('/(\d+)/', (string)$item->nomor_blending, $m)) {
                        $n = (int)$m[1];
                        if ($n >= 1 && $n <= 3) {
                            return $n - 1; // 0: Tank 1, 1: Tank 2, 2: Tank 3
                        }
                    }
                }

                // 2. Berdasarkan rotasi siklus nomor batch awal
                $bRange = (string)($item->batch_range ?? '');
                if (preg_match('/^(\d+)/', $bRange, $m)) {
                    $firstBatch = (int)$m[1];
                    $pairIndex = max(0, (int)ceil($firstBatch / 2) - 1);
                    return $pairIndex % 3; // 0 => Halaman 1, 1 => Halaman 2, 2 => Halaman 3
                }

                return 0;
            });

            // Urutkan grup tangki berdasarkan slot rotasi (0, 1, 2)
            $sortedTankGroups = collect();
            foreach ([0, 1, 2] as $slot) {
                if ($groupedByTankSlot->has($slot) && $groupedByTankSlot[$slot]->isNotEmpty()) {
                    $sortedTankGroups->push($groupedByTankSlot[$slot]);
                }
            }

            // Fallback jika ada slot lain
            foreach ($groupedByTankSlot as $key => $items) {
                if (!in_array($key, [0, 1, 2], true) && $items->isNotEmpty()) {
                    $sortedTankGroups->push($items);
                }
            }

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

                    // Jam Serah Terima = Jam Scan (HANYA jam scan oleh analis kimia, BUKAN jam created_at)
                    $scanTime = $item->scanned_at;
                    if (!$scanTime) {
                        $scanTime = DB::table('qr_scan_logs')
                            ->whereIn('qc_type', ['blending-awal', 'blending_awal'])
                            ->where('qc_id', $item->id)
                            ->value('scanned_at');
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

                // Lengkapi sisa baris hingga minimal 11 baris standar
                while (count($rows) < 11) {
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

                // Lookup Shift & Grup dinamis dari production.blending_awal (e.g. 3/C)
                $blendingShiftGroup = '';
                try {
                    $prodBatch = DB::table('production.production_batches')
                        ->where('po_number', $batch->po_number)
                        ->orWhere('po_number', 'LIKE', '%' . $batch->po_number . '%')
                        ->first();
                    $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
                    $bRange = $first->batch_range ?? null;
                    $noBl = $first->nomor_blending ?? null;

                    // 1. Coba cari berdasarkan id item langsung
                    $blRow = DB::table('production.blending_awal')
                        ->where('id', $first->id)
                        ->first();

                    // 2. Jika tidak cocok, cari berdasarkan production_batch_id + batch_range
                    if (!$blRow && $bRange) {
                        $blRow = DB::table('production.blending_awal')
                            ->where(function($q) use ($pBatchId, $batch) {
                                $q->where('production_batch_id', $pBatchId)
                                  ->orWhere('production_batch_id', $batch->id);
                            })
                            ->where('batch_range', $bRange)
                            ->first();
                    }

                    // 3. Jika tidak cocok, cari berdasarkan production_batch_id + nomor_blending
                    if (!$blRow && $noBl) {
                        $blRow = DB::table('production.blending_awal')
                            ->where(function($q) use ($pBatchId, $batch) {
                                $q->where('production_batch_id', $pBatchId)
                                  ->orWhere('production_batch_id', $batch->id);
                            })
                            ->where('nomor_blending', $noBl)
                            ->first();
                    }

                    // 4. Fallback ke monitoring_turun_blending
                    if (!$blRow) {
                        $blRow = DB::table('production.monitoring_turun_blending')
                            ->where(function($q) use ($pBatchId, $batch) {
                                $q->where('production_batch_id', $pBatchId)
                                  ->orWhere('production_batch_id', $batch->id);
                            })
                            ->when($bRange, fn($q) => $q->where('batch_range', $bRange))
                            ->first();
                    }

                    if ($blRow) {
                        $s = $blRow->shift ? (string)$blRow->shift : '';
                        $g = $blRow->group ? (string)$blRow->group : '';
                        if ($s !== '' && $g !== '') {
                            $blendingShiftGroup = "{$s}/{$g}";
                        } elseif ($s !== '') {
                            $blendingShiftGroup = $s;
                        } elseif ($g !== '') {
                            $blendingShiftGroup = $g;
                        }
                    }
                } catch (\Throwable $e) {}

                // Jam Produksi = Jam Tambah Blending Awal
                $jamProduksiStr = '';
                if ($first && $first->created_at) {
                    $jamProduksiStr = Carbon::parse($first->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                } elseif (!empty($blRow?->created_at)) {
                    $jamProduksiStr = Carbon::parse($blRow->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                } elseif (!empty($blRow?->waktu_kirim_sample)) {
                    $jamProduksiStr = Carbon::parse($blRow->waktu_kirim_sample)->setTimezone('Asia/Jakarta')->format('H:i');
                } else {
                    $jamProduksiStr = '08:00';
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
                    'catatan' => '',
                    'pic_sampling' => '',
                    'pic_analis' => $cycleAnalis ?: ($detectedAnalis ?: (auth()->user()->name ?? '')),
                    'pic_checker' => '',
                    'rows' => $rows,
                ];
            }
        } else {
            // Default 3 blok halaman pasangan batch blending (1-2, 3-4, 5-6)
            for ($p = 1; $p <= 3; $p++) {
                $bStart = ($p - 1) * 2 + 1;
                $bEnd = $bStart + 1;
                $defaultRows = [];
                for ($r = 1; $r <= 11; $r++) {
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

                $batchBlocks[] = [
                    'halaman' => $p . ' / 3',
                    'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => '08:00',
                    'kode_shift_grup' => '',
                    'batch' => "{$bStart}-{$bEnd}",
                    'no_blending' => (string)$p,
                    'volume_awal' => '10000 L',
                    'catatan' => '',
                    'pic_sampling' => '',
                    'pic_analis' => auth()->user()->name ?? '',
                    'pic_checker' => '',
                    'rows' => $defaultRows,
                ];
            }
        }

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => '1',
            'batch_blocks' => $batchBlocks,
            'catatan' => '',
            'pic_sampling' => '',
            'pic_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'pic_checker' => '',
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
}
