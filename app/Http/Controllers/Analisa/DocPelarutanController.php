<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPelarutanExport;
use App\Http\Controllers\Controller;
use App\Models\DocPelarutan;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocPelarutanController extends Controller
{
    /**
     * Tampilkan Halaman Form Hasil Analisis Proses Pelarutan (FRM/QLB/04/104/004-01)
     */
    public function index(Request $request)
    {
        $batches = ProductionBatch::where(function ($q) {
                $q->has('pelarutan_1')
                  ->orHas('pelarutan_2');
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

        return view('app.doc_pelarutan.index', [
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
     * Format: 1 Lembar = 2 Batch (Dissolver)
     */
    public function getDocumentData($poId)
    {
        $batch = ProductionBatch::with([
            'pelarutan_1.user',
            'pelarutan_2.user'
        ])->find($poId);

        if (!$batch) {
            return null;
        }

        $savedDoc = DocPelarutan::where('production_batch_id', $poId)->first();

        if ($savedDoc) {
            $blocks = $savedDoc->batch_blocks ?: [];
            $totalSheets = max(1, (int)ceil(count($blocks) / 2));

            // Pastikan setiap blok terisi 5 baris minimum
            foreach ($blocks as &$blk) {
                if (!isset($blk['rows'])) $blk['rows'] = [];
                while (count($blk['rows']) < 5) {
                    $nextSamp = count($blk['rows']) + 1;
                    $blk['rows'][] = [
                        'p1_sampling_ke' => (string)$nextSamp,
                        'p1_jam' => '',
                        'p1_pic' => '',
                        'p1_brix' => '',
                        'p1_nacl' => '',
                        'p1_warna' => '',
                        'p1_organo' => '',
                        'p1_waktu_adjustment' => '',
                        'p2_sampling_ke' => (string)$nextSamp,
                        'p2_jam' => '',
                        'p2_pic' => '',
                        'p2_brix' => '',
                        'p2_nacl' => '',
                        'p2_warna' => '',
                        'p2_organo' => '',
                        'p2_waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
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
                'halaman' => $savedDoc->halaman ?: "1 / {$totalSheets}",
                'batch_blocks' => $blocks,
                'catatan' => $savedDoc->catatan ?: '',
                'pic_sampling' => $savedDoc->pic_sampling ?: '',
                'pic_analis' => $savedDoc->pic_analis ?: '',
                'pic_checker' => $savedDoc->pic_checker ?: '',
            ];
        }

        // Auto-generate Batch Blocks dari data Pelarutan 1 & 2
        $p1Items = $batch->pelarutan_1 ? $batch->pelarutan_1->sortBy('id')->values() : collect();
        $p2Items = $batch->pelarutan_2 ? $batch->pelarutan_2->sortBy('id')->values() : collect();

        // Ambil semua nomor batch unik
        $batchNumbers = $p1Items->pluck('batch_number')
            ->merge($p2Items->pluck('batch_number'))
            ->filter()
            ->unique()
            ->values();

        if ($batchNumbers->isEmpty() && !empty($batch->batch_range_array)) {
            $batchNumbers = collect($batch->batch_range_array);
        }

        $batchBlocks = [];
        $detectedAnalis = '';

        if ($batchNumbers->count() > 0) {
            foreach ($batchNumbers as $bNum) {
                $p1ForBatch = $p1Items->where('batch_number', (string)$bNum)->values();
                $p2ForBatch = $p2Items->where('batch_number', (string)$bNum)->values();

                $dissolver = $p1ForBatch->first()?->dissolver_number ?: ($p2ForBatch->first()?->dissolver_number ?: '1');
                $maxRows = max(1, $p1ForBatch->count(), $p2ForBatch->count());
                $rows = [];

                $p1First = $p1ForBatch->first();
                $p2Last = $p2ForBatch->last();

                $jamStart = $p1First && $p1First->created_at ? $p1First->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '08:00';
                $jamEnd = $p2Last && $p2Last->created_at ? $p2Last->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
                $jamProduksiStr = $jamEnd ? "{$jamStart} - {$jamEnd}" : $jamStart;

                for ($i = 0; $i < $maxRows; $i++) {
                    $p1 = $p1ForBatch->get($i);
                    $p2 = $p2ForBatch->get($i);

                    if (!$detectedAnalis) {
                        if ($p1 && $p1->user) $detectedAnalis = $p1->user->name;
                        elseif ($p2 && $p2->user) $detectedAnalis = $p2->user->name;
                    }

                    // Adjustment P1
                    $adj1 = [];
                    if ($p1) {
                        if ($p1->adjustment_qty_gula_tebu) $adj1[] = 'Tebu: ' . $p1->adjustment_qty_gula_tebu . ' kg';
                        if ($p1->adjustment_qty_gula_kelapa) $adj1[] = 'Kelapa: ' . $p1->adjustment_qty_gula_kelapa . ' kg';
                        if ($p1->disposition_remark) $adj1[] = $p1->disposition_remark;
                    }
                    $p1AdjStr = !empty($adj1) ? implode('; ', $adj1) : '-';

                    // Adjustment P2
                    $adj2 = [];
                    if ($p2) {
                        if ($p2->adjustment_qty_gula_tebu) $adj2[] = 'Tebu: ' . $p2->adjustment_qty_gula_tebu . ' kg';
                        if ($p2->adjustment_qty_gula_kelapa) $adj2[] = 'Kelapa: ' . $p2->adjustment_qty_gula_kelapa . ' kg';
                        if ($p2->disposition_remark) $adj2[] = $p2->disposition_remark;
                    }
                    $p2AdjStr = !empty($adj2) ? implode('; ', $adj2) : '-';

                    $disposisi = ($p2 && $p2->disposition) ? $p2->disposition : (($p1 && $p1->disposition) ? $p1->disposition : 'Release');

                    $rows[] = [
                        'p1_sampling_ke' => (string)($i + 1),
                        'p1_jam' => $p1 && $p1->created_at ? $p1->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p1_pic' => $p1 && $p1->user ? $p1->user->name : '',
                        'p1_brix' => $p1 && $p1->brix !== null ? (string)$p1->brix : '',
                        'p1_nacl' => $p1 && $p1->nacl !== null ? (string)$p1->nacl : '',
                        'p1_warna' => 'Standar',
                        'p1_organo' => $p1 && $p1->organo ? $p1->organo : 'OK',
                        'p1_waktu_adjustment' => $p1AdjStr,

                        'p2_sampling_ke' => (string)($i + 1),
                        'p2_jam' => $p2 && $p2->created_at ? $p2->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p2_pic' => $p2 && $p2->user ? $p2->user->name : '',
                        'p2_brix' => $p2 && $p2->brix !== null ? (string)$p2->brix : '',
                        'p2_nacl' => $p2 && $p2->nacl !== null ? (string)$p2->nacl : '',
                        'p2_warna' => 'Standar',
                        'p2_organo' => $p2 && $p2->organo ? $p2->organo : 'OK',
                        'p2_waktu_adjustment' => $p2AdjStr,

                        'disposisi' => $disposisi,
                    ];
                }

                // Pad rows to standard 5 rows
                while (count($rows) < 5) {
                    $nextSamp = count($rows) + 1;
                    $rows[] = [
                        'p1_sampling_ke' => (string)$nextSamp,
                        'p1_jam' => '',
                        'p1_pic' => '',
                        'p1_brix' => '',
                        'p1_nacl' => '',
                        'p1_warna' => '',
                        'p1_organo' => '',
                        'p1_waktu_adjustment' => '',
                        'p2_sampling_ke' => (string)$nextSamp,
                        'p2_jam' => '',
                        'p2_pic' => '',
                        'p2_brix' => '',
                        'p2_nacl' => '',
                        'p2_warna' => '',
                        'p2_organo' => '',
                        'p2_waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
                }

                $batchBlocks[] = [
                    'jenis_produk' => $batch->variant ?: 'Kecap Manis',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => $jamProduksiStr,
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)$bNum,
                    'no_dissolver' => (string)$dissolver,
                    'volume' => '',
                    'rows' => $rows,
                ];
            }

            // Pastikan jumlah blok genap (1 Lembar = 2 Batch)
            if (count($batchBlocks) % 2 !== 0) {
                $nextB = count($batchBlocks) + 1;
                $blockRows = [];
                for ($r = 1; $r <= 5; $r++) {
                    $blockRows[] = [
                        'p1_sampling_ke' => (string)$r,
                        'p1_jam' => '',
                        'p1_pic' => '',
                        'p1_brix' => '',
                        'p1_nacl' => '',
                        'p1_warna' => '',
                        'p1_organo' => '',
                        'p1_waktu_adjustment' => '',
                        'p2_sampling_ke' => (string)$r,
                        'p2_jam' => '',
                        'p2_pic' => '',
                        'p2_brix' => '',
                        'p2_nacl' => '',
                        'p2_warna' => '',
                        'p2_organo' => '',
                        'p2_waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
                }

                $batchBlocks[] = [
                    'jenis_produk' => $batch->variant ?: 'Kecap Manis',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => '08:00',
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)$nextB,
                    'no_dissolver' => (string)$nextB,
                    'volume' => '',
                    'rows' => $blockRows,
                ];
            }
        } else {
            // Default 2 blok kosong (1 lembar = 2 batch)
            for ($b = 1; $b <= 2; $b++) {
                $defaultRows = [];
                for ($r = 1; $r <= 5; $r++) {
                    $defaultRows[] = [
                        'p1_sampling_ke' => (string)$r,
                        'p1_jam' => '',
                        'p1_pic' => '',
                        'p1_brix' => '',
                        'p1_nacl' => '',
                        'p1_warna' => '',
                        'p1_organo' => '',
                        'p1_waktu_adjustment' => '',
                        'p2_sampling_ke' => (string)$r,
                        'p2_jam' => '',
                        'p2_pic' => '',
                        'p2_brix' => '',
                        'p2_nacl' => '',
                        'p2_warna' => '',
                        'p2_organo' => '',
                        'p2_waktu_adjustment' => '',
                        'disposisi' => '',
                    ];
                }

                $batchBlocks[] = [
                    'jenis_produk' => $batch->variant ?: 'Kecap Manis',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => '08:00',
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)$b,
                    'no_dissolver' => (string)$b,
                    'volume' => '',
                    'rows' => $defaultRows,
                ];
            }
        }

        $totalSheets = max(1, (int)ceil(count($batchBlocks) / 2));

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => "1 / {$totalSheets}",
            'batch_blocks' => $batchBlocks,
            'catatan' => '',
            'pic_sampling' => '',
            'pic_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'pic_checker' => '',
        ];
    }

    /**
     * Simpan / Perbarui Form Dokumen Pelarutan
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'tanggal_record_doc' => 'nullable|date',
        ]);

        $batchBlocks = $request->input('batch_blocks', []);
        $totalSheets = max(1, (int)ceil(count($batchBlocks) / 2));

        $doc = DocPelarutan::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', "1 / {$totalSheets}"),
                'batch_blocks' => $batchBlocks,
                'catatan' => $request->input('catatan'),
                'pic_sampling' => $request->input('pic_sampling'),
                'pic_analis' => $request->input('pic_analis'),
                'pic_checker' => $request->input('pic_checker'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir Hasil Analisis Proses Pelarutan berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi (1 Lembar = 2 Batch)
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
        $fileName = 'HASIL_ANALISIS_PROSES_PELARUTAN_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DocPelarutanExport($docData),
            $fileName
        );
    }

    /**
     * Tampilan Khusus Cetak / Print Dokumen (1 Lembar = 2 Batch)
     */
    public function printView($id)
    {
        $docData = $this->getDocumentData($id);
        if (!$docData) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return view('app.doc_pelarutan.print', [
            'data' => $docData
        ]);
    }
}
