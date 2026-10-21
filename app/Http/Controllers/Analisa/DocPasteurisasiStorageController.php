<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPasteurisasiStorageExport;
use App\Http\Controllers\Controller;
use App\Models\DocPasteurisasiStorage;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class DocPasteurisasiStorageController extends Controller
{
    /**
     * Tampilkan Halaman Form Dokumen Hasil Analisis Pasteurisasi dan Storage Tank
     * FRM/QLB/04/104/006-01
     */
    public function index(Request $request)
    {
        $batches = ProductionBatch::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $selectedBatchId = $request->input('po_id') ?? $batches->first()?->id;
        $initialData = null;

        if ($selectedBatchId) {
            $initialData = $this->getDocumentData($selectedBatchId);
        }

        return view('app.doc_pasteurisasi_storage.index', [
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
     * 1 Lembar = 1 Blending / Pasangan Batch
     */
    public function getDocumentData($poId)
    {
        $batch = ProductionBatch::with([
            'monitoringPasteurisasi.user',
            'monitoringPasteurisasi.color',
            'monitoringStorageKimia.user',
            'monitoringStorageKimia.color'
        ])->find($poId);

        if (!$batch) {
            return null;
        }

        $savedDoc = DocPasteurisasiStorage::where('production_batch_id', $poId)->first();

        $pBatchId = null;
        try {
            $pBatchId = $this->resolveProductionBatchId($batch->po_number, $batch->id);
        } catch (\Throwable $e) {}

        // Ambil info resmi dari Portal Produksi via API (Port 8001)
        $prdDocInfo = null;
        try {
            $prdUrl = rtrim(env('PRODUCTION_URL', 'http://127.0.0.1:8001/'), '/') . '/api/pasteurisasi-storage/doc-info';
            $prdRes = Http::timeout(3)->get($prdUrl, [
                'po_number' => $batch->po_number,
                'production_batch_id' => $pBatchId ?: $batch->id,
            ]);
            if ($prdRes->successful()) {
                $prdDocInfo = $prdRes->json('data');
            }
        } catch (\Throwable $e) {}

        // Ambil data Pasteurisasi dari QC
        $pasteurisasiItems = $batch->monitoringPasteurisasi ? $batch->monitoringPasteurisasi->sortBy('id')->values() : collect();

        // Ambil data Storage Kimia (direct, relation, & production fallback)
        $storageItems = $batch->monitoringStorageKimia ? $batch->monitoringStorageKimia->sortBy('id')->values() : collect();

        try {
            $relStorageIds = DB::table('monitoring_storage_kimia_relations')
                ->where('production_batch_id', $batch->id)
                ->pluck('monitoring_storage_kimia_id')
                ->toArray();
            if (!empty($relStorageIds)) {
                $extraStorage = MonitoringStorageKimia::whereIn('id', $relStorageIds)->with(['color', 'user'])->get();
                $storageItems = $storageItems->merge($extraStorage)->unique('id')->values();
            }

            if ($storageItems->isEmpty() && $pBatchId) {
                $prodStorage = DB::table('production.monitoring_storage_kimia')
                    ->where('production_batch_id', $pBatchId)
                    ->orderBy('id')
                    ->get();
                if ($prodStorage->isNotEmpty()) {
                    $storageItems = $prodStorage;
                }
            }
        } catch (\Throwable $e) {}

        // Pengelompokan 1 Lembar = 1 Blending / Pasangan Batch (e.g. 1-2, 3-4, 5-6, ..., 23-24)
        $groupedPast = collect();
        if ($pasteurisasiItems->isNotEmpty()) {
            $currentPairIdx = 0;
            foreach ($pasteurisasiItems as $pIdx => $item) {
                $bRange = trim((string)($item->batch_range ?? ''));
                if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $bRange, $m) && ((int)$m[2] - (int)$m[1] <= 2)) {
                    $key = $bRange;
                } elseif (preg_match('/^(\d+)$/', $bRange, $m)) {
                    $bNum = (int)$m[1];
                    $pairNum = (int)ceil($bNum / 2);
                    $key = (($pairNum - 1) * 2 + 1) . '-' . ($pairNum * 2);
                } else {
                    $isRevisi = (!empty($item->revisi) || !empty($item->is_adjustment));
                    if ($pIdx > 0 && !$isRevisi) {
                        $currentPairIdx++;
                    }
                    $bStart = $currentPairIdx * 2 + 1;
                    $bEnd = $bStart + 1;
                    $key = "{$bStart}-{$bEnd}";
                }
                if (!$groupedPast->has($key)) {
                    $groupedPast->put($key, collect());
                }
                $groupedPast[$key]->push($item);
            }
        } else {
            // Fallback pasangan batch berdasarkan batch_range PO
            $maxB = 2;
            if (preg_match('/(\d+)\s*-\s*(\d+)/', (string)$batch->batch_range, $m)) {
                $maxB = (int)$m[2];
            } elseif (preg_match('/(\d+)/', (string)$batch->batch_range, $m)) {
                $maxB = (int)$m[1];
            }
            $pairCount = max(1, (int)ceil($maxB / 2));
            for ($p = 0; $p < $pairCount; $p++) {
                $bStart = $p * 2 + 1;
                $bEnd = $bStart + 1;
                $groupedPast->put("{$bStart}-{$bEnd}", collect());
            }
        }

        $totalBlocks = $groupedPast->count();
        $batchBlocks = [];
        $cIdx = 0;

        foreach ($groupedPast as $batchKey => $groupItems) {
            $firstItem = $groupItems->first();

            // Nomor batch awal (e.g. "1-2" => 1, "7-8" => 7)
            $bStart = 1;
            if (preg_match('/^(\d+)/', $batchKey, $m)) {
                $bStart = (int)$m[1];
            }

            // Kode ST (Storage tujuan transfer)
            $blockKodeSt = '-';
            if ($firstItem && !empty($firstItem->storage)) {
                $blockKodeSt = $firstItem->storage;
            }

            // Jam Produksi & Shift Lookup
            $jamStart = '';
            $jamEnd = '';
            $blockShift = '';
            $jamProduksi = '';
            $prodPast = null;

            // 1. Prioritas Utama: Ambil dari API Portal Produksi (Port 8001)
            $prdItem = null;
            if (!empty($prdDocInfo['items'])) {
                foreach ($prdDocInfo['items'] as $it) {
                    $itemRange = trim((string)($it['batch_range'] ?? ''));
                    if ($itemRange === $batchKey) {
                        $prdItem = $it;
                        break;
                    }
                    if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $itemRange, $mRange)) {
                        $rStart = (int)$mRange[1];
                        $rEnd = (int)$mRange[2];
                        if ($bStart >= $rStart && $bStart <= $rEnd) {
                            $prdItem = $it;
                            break;
                        }
                    } elseif ($itemRange === (string)$bStart) {
                        $prdItem = $it;
                        break;
                    }
                }
                if (!$prdItem && isset($prdDocInfo['items'][$cIdx])) {
                    $prdItem = $prdDocInfo['items'][$cIdx];
                }
            }

            if ($prdItem) {
                if (!empty($prdItem['kode_shift_grup'])) {
                    $blockShift = $prdItem['kode_shift_grup'];
                }
                if (!empty($prdItem['jam_mulai'])) {
                    $jamStart = $prdItem['jam_mulai'];
                }
                if (!empty($prdItem['jam_selesai'])) {
                    $jamEnd = $prdItem['jam_selesai'];
                }
                if (!empty($prdItem['jam_produksi']) && $prdItem['jam_produksi'] !== '-') {
                    $jamProduksi = $prdItem['jam_produksi'];
                }
                if (!empty($prdItem['storage'])) {
                    $blockKodeSt = $prdItem['storage'];
                }
            }

            // 2. Fallback: Direct DB jika API Portal Produksi belum terjangkau/kosong
            if (empty($blockShift) && empty($jamStart) && $pBatchId) {
                try {
                    $prodPast = DB::table('production.monitoring_pasteurisasi')
                        ->where('production_batch_id', $pBatchId)
                        ->where(function($q) use ($batchKey, $bStart) {
                            $q->where('batch_range', $batchKey)
                              ->orWhere('batch_range', 'LIKE', $bStart . '-%')
                              ->orWhere('batch_range', (string)$bStart);
                        })
                        ->orderBy('id')
                        ->first();

                    if (!$prodPast) {
                        $prodPast = DB::table('production.monitoring_pasteurisasi')
                            ->where('production_batch_id', $pBatchId)
                            ->skip($cIdx)
                            ->first();
                    }

                    if ($prodPast) {
                        if (!empty($prodPast->shift)) {
                            $blockShift = $this->formatShiftGroup($prodPast->shift, $prodPast->group ?? null);
                        }
                        if (!empty($prodPast->jam_mulai_pasteurisasi)) {
                            $jamStart = substr($prodPast->jam_mulai_pasteurisasi, 0, 5);
                        } elseif (!empty($prodPast->created_at)) {
                            $jamStart = Carbon::parse($prodPast->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                        }

                        if (!empty($prodPast->jam_selesai_pasteurisasi)) {
                            $jamEnd = substr($prodPast->jam_selesai_pasteurisasi, 0, 5);
                        } elseif (!empty($prodPast->updated_at) && !empty($prodPast->status_pasteurisasi) && stripos($prodPast->status_pasteurisasi, 'selesai') !== false) {
                            $jamEnd = Carbon::parse($prodPast->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                        }

                        if (!empty($prodPast->storage)) {
                            $blockKodeSt = $prodPast->storage;
                        }
                    }
                } catch (\Throwable $e) {}
            }

            // Fallback Jam & Shift dari QC Item Digimon jika di database produksi belum ada
            if ($firstItem) {
                if (empty($jamStart)) {
                    // Jam Tambah Data dari waktu input/scan data di Digimon
                    $scanTime = $firstItem->scanned_at ?: $firstItem->created_at;
                    if ($scanTime) {
                        $jamStart = Carbon::parse($scanTime)->setTimezone('Asia/Jakarta')->format('H:i');
                    }
                }
                if (empty($jamEnd)) {
                    // Jam Selesai Pasteurisasi dari data terakhir di kelompok sampling yang sama
                    $lastItem = $groupItems->last();
                    if ($lastItem && $lastItem !== $firstItem) {
                        $endScan = $lastItem->scanned_at ?: $lastItem->created_at;
                        if ($endScan) {
                            $jamEnd = Carbon::parse($endScan)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    } elseif (!empty($firstItem->updated_at) && $firstItem->updated_at != $firstItem->created_at) {
                        $jamEnd = Carbon::parse($firstItem->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                    }
                }
                if (empty($blockShift) && !empty($firstItem->shift)) {
                    $blockShift = $this->formatShiftGroup($firstItem->shift, null);
                }
            }

            // Fallback Shift berdasarkan Jam jika belum ada
            if (empty($blockShift) && !empty($jamStart)) {
                $hour = (int)substr($jamStart, 0, 2);
                if ($hour >= 6 && $hour < 14) {
                    $blockShift = 'Shift 1 - Grup A';
                } elseif ($hour >= 14 && $hour < 22) {
                    $blockShift = 'Shift 2 - Grup A';
                } else {
                    $blockShift = 'Shift 3 - Grup A';
                }
            }
            if (empty($blockShift)) {
                $blockShift = 'Shift 1 - Grup A';
            }

            // Build Pasteurisasi Rows (min 6 baris)
            $pastRows = [];
            foreach ($groupItems as $mpIdx => $mp) {
                $disposisiFull = $mp->disposition ?: '';
                if (!empty($mp->disposition_remark)) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $mp->disposition_remark;
                }
                $mpScan = $mp->scanned_at ?: $mp->created_at;
                $sampName = !empty($mp->revisi) ? $mp->revisi : '';
                $pastRows[] = [
                    'sampling' => $sampName,
                    'jam' => $mpScan ? Carbon::parse($mpScan)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                    'pic' => $mp->user->name ?? '',
                    'bj' => $this->formatBlankIfZero($mp->bj),
                    'brix' => $this->formatBlankIfZero($mp->brix),
                    'ph' => $this->formatBlankIfZero($mp->ph),
                    'aw' => $this->formatBlankIfZero($mp->aw),
                    'viskositas' => $this->formatBlankIfZero($mp->visco),
                    'organo' => $mp->organo ?: '',
                    'aroma' => $mp->aroma ?? '',
                    'warna' => $mp->color ? ($mp->color->code ?: $mp->color->name) : '',
                    'buih' => $this->formatBlankIfZero($mp->buih),
                    'endapan' => $mp->endapan ?: '',
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
            while (count($pastRows) < 6) {
                $pastRows[] = [
                    'sampling' => '',
                    'jam' => '', 'pic' => '', 'bj' => '', 'brix' => '', 'ph' => '', 'aw' => '',
                    'viskositas' => '', 'organo' => '', 'aroma' => '', 'warna' => '', 'buih' => '', 'endapan' => '', 'disposisi' => '',
                ];
            }

            // Fallback disposisi after cooling dari production jika belum diisi di QC
            $prodDispo = $prdItem['disposition'] ?? ($prodPast?->disposition ?? '');
            if (empty($pastRows[0]['disposisi']) && !empty($prodDispo)) {
                $pastDispo = $prodDispo;
                if (!empty($prodPast?->disposition_remark) && $prodPast->disposition_remark !== '-') {
                    $pastDispo .= ' - ' . $prodPast->disposition_remark;
                }
                $pastRows[0]['disposisi'] = $pastDispo;
                if (empty($pastRows[0]['jam']) && !empty($jamStart)) {
                    $pastRows[0]['jam'] = $jamStart;
                }
            }

            // Build Storage Tank Rows (min 5 baris)
            $matchedSt = $storageItems->filter(function($st) use ($batchKey, $bStart, $blockKodeSt) {
                if (!empty($st->batch_range) && (strpos($st->batch_range, $batchKey) !== false || strpos($st->batch_range, (string)$bStart) !== false)) {
                    return true;
                }
                if (!empty($st->storage) && strcasecmp($st->storage, $blockKodeSt) === 0) {
                    return true;
                }
                return false;
            });

            if ($matchedSt->isEmpty() && $storageItems->isNotEmpty() && $storageItems->count() <= 3) {
                $matchedSt = $storageItems;
            }

            $storageRows = [];
            foreach ($matchedSt as $sIdx => $msk) {
                if (!empty($msk->storage)) $blockKodeSt = $msk->storage;
                $disposisiFull = $msk->disposition ?: '';
                if (!empty($msk->disposition_remark)) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $msk->disposition_remark;
                }
                $mskScan = $msk->scanned_at ?? ($msk->created_at ?? null);
                $storageRows[] = [
                    'sampling' => !empty($msk->jenis_sampel) ? $msk->jenis_sampel : ($msk->stage ?? ($sIdx === 0 ? 'Awal' : ($sIdx === 1 ? 'Outlet' : 'Akhir'))),
                    'jam' => $mskScan ? Carbon::parse($mskScan)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                    'pic' => $msk->user->name ?? '',
                    'bj' => $this->formatBlankIfZero($msk->bj ?? null),
                    'brix' => $this->formatBlankIfZero($msk->brix ?? null),
                    'ph' => $this->formatBlankIfZero($msk->ph ?? null),
                    'nacl' => $this->formatBlankIfZero($msk->nacl ?? null),
                    'viskositas' => $this->formatBlankIfZero($msk->visco ?? null),
                    'organo' => $msk->organo ?? '',
                    'aroma' => $msk->aroma ?? '',
                    'warna' => isset($msk->color) ? ($msk->color->code ?? ($msk->color->name ?? '')) : '',
                    'buih' => $this->formatBlankIfZero($msk->buih ?? null),
                    'endapan' => $msk->endapan ?? '',
                    'kristal' => $msk->kristal ?? '',
                    'aw' => $this->formatBlankIfZero($msk->aw ?? null),
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
            while (count($storageRows) < 5) {
                $sIndex = count($storageRows);
                $defaultSamp = '';
                if ($sIndex === 0) $defaultSamp = 'Awal';
                elseif ($sIndex === 1) $defaultSamp = 'Outlet';

                $storageRows[] = [
                    'sampling' => $defaultSamp,
                    'jam' => '', 'pic' => '', 'bj' => '', 'brix' => '', 'ph' => '', 'nacl' => '',
                    'viskositas' => '', 'organo' => '', 'aroma' => '', 'warna' => '', 'buih' => '', 'endapan' => '', 'kristal' => '', 'aw' => '', 'disposisi' => '',
                ];
            }

            // Analis QC
            $analisName = '';
            if ($firstItem && !empty($firstItem->user)) {
                $analisName = $firstItem->user->name;
            } elseif (auth()->check()) {
                $analisName = auth()->user()->name;
            }

            $batchBlocks[] = [
                'block_index' => $cIdx,
                'halaman' => ($cIdx + 1) . ' / ' . $totalBlocks,
                'batch' => $batchKey,
                'kode_st' => $blockKodeSt,
                'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                'jam_produksi_start' => $jamStart,
                'jam_produksi_end' => $jamEnd,
                'jam_produksi' => !empty($jamProduksi) ? $jamProduksi : (($jamStart || $jamEnd) ? ($jamStart . ($jamEnd ? ' - ' . $jamEnd : '')) : '-'),
                'kode_shift_grup' => $blockShift,
                'pasteurisasi_rows' => $pastRows,
                'storage_rows' => $storageRows,
                'catatan' => $savedDoc?->catatan ?: '',
                'pic_sampling_1' => $savedDoc?->pic_sampling ?: '',
                'pic_sampling_2' => $savedDoc?->pic_serah_terima ?: '',
                'pic_analis' => $analisName,
                'pic_checker' => $savedDoc?->pic_checker ?: '',
            ];

            $cIdx++;
        }

        $firstBlock = $batchBlocks[0] ?? [];

        return [
            'is_saved' => $savedDoc ? true : false,
            'id' => $savedDoc?->id,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'jenis_produk' => $firstBlock['jenis_produk'] ?? ($batch->variant ?: 'Kecap Sedap'),
            'tanggal_produksi' => $firstBlock['tanggal_produksi'] ?? ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
            'jam_produksi_start' => $firstBlock['jam_produksi_start'] ?? '',
            'jam_produksi_end' => $firstBlock['jam_produksi_end'] ?? '',
            'kode_shift_grup' => $firstBlock['kode_shift_grup'] ?? '',
            'batch' => $firstBlock['batch'] ?? ($batch->batch_range ?: '1-2'),
            'batch_range' => $batch->batch_range ?: '',
            'kode_st' => $firstBlock['kode_st'] ?? 'ST 01',
            'tanggal_record_doc' => $savedDoc?->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
            'halaman' => $firstBlock['halaman'] ?? '1 / 1',
            'pasteurisasi_rows' => $firstBlock['pasteurisasi_rows'] ?? [],
            'storage_rows' => $firstBlock['storage_rows'] ?? [],
            'catatan' => $savedDoc?->catatan ?: '',
            'pic_sampling_1' => $savedDoc?->pic_sampling ?: '',
            'pic_sampling_2' => $savedDoc?->pic_serah_terima ?: '',
            'pic_analis' => $firstBlock['pic_analis'] ?? '',
            'pic_checker' => $savedDoc?->pic_checker ?: '',
            'batch_blocks' => $batchBlocks,
            'doc_code' => 'FRM/QLB/04/104/006-01',
        ];
    }

    /**
     * Simpan / Perbarui Form Dokumen
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'tanggal_record_doc' => 'nullable|date',
        ]);

        $batchBlocks = $request->input('batch_blocks', []);
        $firstBlock = !empty($batchBlocks[0]) ? $batchBlocks[0] : [];

        $doc = DocPasteurisasiStorage::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'batch_blocks' => $batchBlocks,
                'jenis_produk' => $firstBlock['jenis_produk'] ?? $request->input('jenis_produk'),
                'tanggal_produksi' => $firstBlock['tanggal_produksi'] ?? $request->input('tanggal_produksi'),
                'jam_produksi_start' => $firstBlock['jam_produksi_start'] ?? $request->input('jam_produksi_start'),
                'jam_produksi_end' => $firstBlock['jam_produksi_end'] ?? $request->input('jam_produksi_end'),
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', '1'),
                'kode_shift_grup' => $firstBlock['kode_shift_grup'] ?? $request->input('kode_shift_grup'),
                'batch' => $firstBlock['batch'] ?? $request->input('batch'),
                'batch_range' => $firstBlock['batch'] ?? $request->input('batch_range'),
                'kode_st' => $firstBlock['kode_st'] ?? $request->input('kode_st'),
                'pasteurisasi_rows' => $firstBlock['pasteurisasi_rows'] ?? $request->input('pasteurisasi_rows', []),
                'storage_rows' => $firstBlock['storage_rows'] ?? $request->input('storage_rows', []),
                'catatan' => $firstBlock['catatan'] ?? $request->input('catatan'),
                'pic_sampling' => $firstBlock['pic_sampling_1'] ?? $request->input('pic_sampling'),
                'pic_serah_terima' => $firstBlock['pic_sampling_2'] ?? $request->input('pic_serah_terima'),
                'pic_analis' => $firstBlock['pic_analis'] ?? $request->input('pic_analis'),
                'pic_checker' => $firstBlock['pic_checker'] ?? $request->input('pic_checker'),
                'doc_code' => $request->input('doc_code', 'FRM/QLB/04/104/006-01'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir Dokumen FRM/QLB/04/104/006-01 berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi FRM/QLB/04/104/006-01
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
        $fileName = 'HASIL_ANALISIS_PASTEURISASI_DAN_STORAGE_TANK_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DocPasteurisasiStorageExport($docData),
            $fileName
        );
    }

    /**
     * Tampilan Khusus Cetak / Print Dokumen
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

        return view('app.doc_pasteurisasi_storage.print', [
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
