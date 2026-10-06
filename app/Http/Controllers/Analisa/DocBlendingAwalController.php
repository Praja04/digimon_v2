<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocBlendingAwalExport;
use App\Http\Controllers\Controller;
use App\Models\DocBlendingAwal;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        if ($savedDoc) {
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

                // Normalisasi baris Turun Blending jika sebelumnya tersimpan dengan vol_tangki = Awal
                if (!empty($blk['rows'])) {
                    foreach ($blk['rows'] as &$row) {
                        if (isset($row['vol_tangki']) && strtolower(trim((string)$row['vol_tangki'])) === 'awal') {
                            $row['vol_tangki'] = '';
                            if (empty($row['sampling_ke']) || $row['sampling_ke'] === '4') {
                                $row['sampling_ke'] = 'Awal';
                            }
                            if (isset($row['waktu_adjustment']) && ($row['waktu_adjustment'] === ($row['serah_terima_jam'] ?? '') || $row['waktu_adjustment'] === '')) {
                                $row['waktu_adjustment'] = '-';
                            }
                        }
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

        // Auto-generate Batch Blocks dari data Blending Awal (1 Lembar = 1 Blending / Tangki)
        $blendingItems = $batch->BlendingAwal ? $batch->BlendingAwal->sortBy('id')->values() : collect();
        $turunItems = $batch->monitoringTurunBlending ? $batch->monitoringTurunBlending->sortBy('id')->values() : collect();
        $batchBlocks = [];
        $detectedAnalis = '';

        if ($blendingItems->count() > 0) {
            // Group items into separate Blending Cycles / Tanks (1 Lembar = 1 Blending)
            $cycles = [];
            $currentCycle = [];
            $prevItem = null;

            foreach ($blendingItems as $item) {
                // Tentukan apakah siklus baru harus dimulai:
                $isNewCycle = false;
                if (!empty($currentCycle) && $prevItem) {
                    $prevDisp = strtolower(trim((string)$prevItem->disposition));
                    $prevIsRelease = (strpos($prevDisp, 'release') !== false);

                    $noBlChanged = ($item->nomor_blending && $prevItem->nomor_blending && (string)$item->nomor_blending !== (string)$prevItem->nomor_blending);
                    $batchChanged = ($item->batch_range && $prevItem->batch_range && (string)$item->batch_range !== (string)$prevItem->batch_range);

                    if ($prevIsRelease || $noBlChanged || $batchChanged) {
                        $isNewCycle = true;
                    }
                }

                if ($isNewCycle) {
                    $cycles[] = $currentCycle;
                    $currentCycle = [];
                }

                // Periksa baris kosong / recheck nilai 0 (BJ=0, Brix=0, pH=0)
                $isZeroRecheck = ($item->bj == 0 && $item->brix == 0 && ($item->visco == 0 || $item->visco === null) && ($item->ph == 0 || $item->ph === null));
                if ($isZeroRecheck && !empty($currentCycle)) {
                    // Gabungkan catatan adjustment ke baris sebelumnya
                    $lastIdx = count($currentCycle) - 1;
                    $adjParts = [];
                    if ($item->adjustment_qty_air) $adjParts[] = 'Air: ' . $item->adjustment_qty_air . ' L';
                    if ($item->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $item->adjustment_qty_garam . ' kg';
                    if ($item->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $item->adjustment_qty_caramel . ' kg';
                    if ($item->disposition_remark) $adjParts[] = $item->disposition_remark;
                    $recheckNote = !empty($adjParts) ? implode('; ', $adjParts) : ($item->disposition ?: 'Recheck');

                    if ($recheckNote && $recheckNote !== '-') {
                        $currentCycle[$lastIdx]['merged_adj'] = (!empty($currentCycle[$lastIdx]['merged_adj']) ? ($currentCycle[$lastIdx]['merged_adj'] . ' | ') : '') . $recheckNote;
                    }
                    continue;
                }

                $currentCycle[] = [
                    'model' => $item,
                    'merged_adj' => '',
                ];
                $prevItem = $item;
            }

            if (!empty($currentCycle)) {
                $cycles[] = $currentCycle;
            }

            $totalCycles = count($cycles);

            foreach ($cycles as $cIdx => $cycleItems) {
                if (empty($cycleItems)) continue;

                $first = $cycleItems[0]['model'];
                $last = end($cycleItems)['model'];

                $cycleAnalis = '';
                foreach ($cycleItems as $ci) {
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

                foreach ($cycleItems as $entry) {
                    $item = $entry['model'];

                    // Waktu & Adjustment description
                    $adjParts = [];
                    if ($item->adjustment_qty_air) $adjParts[] = 'Air: ' . $item->adjustment_qty_air . ' L';
                    if ($item->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $item->adjustment_qty_garam . ' kg';
                    if ($item->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $item->adjustment_qty_caramel . ' kg';
                    if ($item->disposition_remark) $adjParts[] = $item->disposition_remark;
                    $adjStr = !empty($adjParts) ? implode('; ', $adjParts) : '-';

                    if (!empty($entry['merged_adj'])) {
                        $adjStr = ($adjStr !== '-' ? ($adjStr . ' | ') : '') . $entry['merged_adj'];
                    }

                    $rows[] = [
                        'sampling_ke' => (string)$samplingIndex++,
                        'vol_tangki' => $item->volume !== null ? (string)$item->volume : '',
                        'serah_terima_jam' => $item->created_at ? $item->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'serah_terima_pic' => $item->user ? $item->user->name : '',
                        'bj' => $item->bj !== null ? (string)$item->bj : '',
                        'brix' => $item->brix !== null ? (string)$item->brix : '',
                        'ph' => $item->ph !== null ? (string)$item->ph : '',
                        'nacl' => $item->nacl !== null ? (string)$item->nacl : '',
                        'visco' => $item->visco !== null ? (string)$item->visco : '',
                        'organo' => $item->organo ?: 'OK',
                        'aroma' => $item->aroma ?: 'OK',
                        'warna' => $item->color ? $item->color->name : 'Hitam',
                        'buih' => 'Tidak Ada',
                        'aw' => $item->aw !== null ? (string)$item->aw : '',
                        'waktu_adjustment' => $adjStr,
                        'disposisi' => $item->disposition ?: 'Release',
                    ];
                }

                // Cek apakah ada data Monitoring Turun Blending yang sesuai untuk siklus ini
                $turun = $turunItems->get($cIdx);
                if ($turun) {
                    $rows[] = [
                        'sampling_ke' => 'Awal',
                        'vol_tangki' => '',
                        'serah_terima_jam' => $turun->created_at ? $turun->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'serah_terima_pic' => $turun->user ? $turun->user->name : ($first->user ? $first->user->name : ''),
                        'bj' => isset($turun->bj) ? (string)$turun->bj : ($last->bj !== null ? (string)$last->bj : ''),
                        'brix' => $turun->brix !== null ? (string)$turun->brix : ($last->brix !== null ? (string)$last->brix : ''),
                        'ph' => isset($turun->ph) ? (string)$turun->ph : ($last->ph !== null ? (string)$last->ph : ''),
                        'nacl' => isset($turun->nacl) ? (string)$turun->nacl : ($last->nacl !== null ? (string)$last->nacl : ''),
                        'visco' => $turun->visco !== null ? (string)$turun->visco : ($last->visco !== null ? (string)$last->visco : ''),
                        'organo' => isset($turun->organo) ? $turun->organo : ($last->organo ?: 'OK'),
                        'aroma' => isset($turun->aroma) ? $turun->aroma : ($last->aroma ?: 'OK'),
                        'warna' => ($last->color ? $last->color->name : 'Hitam'),
                        'buih' => 'Tidak Ada',
                        'aw' => $turun->aw !== null ? (string)$turun->aw : ($last->aw !== null ? (string)$last->aw : ''),
                        'waktu_adjustment' => '-',
                        'disposisi' => $turun->status ?: ($turun->disposition ?: 'Release'),
                    ];
                }

                // Pad rows up to 4 rows for clean initial display in UI
                while (count($rows) < 4) {
                    $nextSamp = count($rows) + 1;
                    $rows[] = [
                        'sampling_ke' => (string)$nextSamp,
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

                $jamStart = $first->created_at ? $first->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '08:00';
                $jamEnd = ($last && $last->created_at && $last->id !== $first->id) ? $last->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
                $jamProduksiStr = $jamEnd ? "{$jamStart} - {$jamEnd}" : $jamStart;

                $batchBlocks[] = [
                    'halaman' => ($cIdx + 1) . ' / ' . $totalCycles,
                    'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                    'tanggal_produksi' => $first->created_at ? $first->created_at->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                    'jam_produksi' => $jamProduksiStr,
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)($first->batch_range ?: ($cIdx + 1)),
                    'no_blending' => (string)($first->nomor_blending ?: ($cIdx + 1)),
                    'volume_awal' => $first->volume ? ($first->volume . ' L') : '10000 L',
                    'catatan' => '',
                    'pic_sampling' => '',
                    'pic_analis' => $cycleAnalis ?: ($detectedAnalis ?: (auth()->user()->name ?? '')),
                    'pic_checker' => '',
                    'rows' => $rows,
                ];
            }
        } else {
            // Default 1 blok template kosong
            $defaultRows = [];
            for ($r = 1; $r <= 4; $r++) {
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
                'halaman' => '1 / 1',
                'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                'jam_produksi' => '08:00',
                'kode_shift_grup' => 'Shift 1 / Grup A',
                'batch' => (string)($batch->batch_range ?: '1'),
                'no_blending' => '1',
                'volume_awal' => '10000 L',
                'catatan' => '',
                'pic_sampling' => '',
                'pic_analis' => auth()->user()->name ?? '',
                'pic_checker' => '',
                'rows' => $defaultRows,
            ];
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
}
