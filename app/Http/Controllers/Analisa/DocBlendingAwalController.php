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
     */
    public function getDocumentData($poId)
    {
        $batch = ProductionBatch::with([
            'BlendingAwal.user',
            'BlendingAwal.color',
            'blendingAfterAdjustMikro'
        ])->find($poId);

        if (!$batch) {
            return null;
        }

        $savedDoc = DocBlendingAwal::where('production_batch_id', $poId)->first();

        if ($savedDoc) {
            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $batch->variant,
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $savedDoc->halaman ?: '1',
                'batch_blocks' => $savedDoc->batch_blocks ?: [],
                'catatan' => $savedDoc->catatan ?: '',
                'pic_sampling' => $savedDoc->pic_sampling ?: '',
                'pic_analis' => $savedDoc->pic_analis ?: '',
                'pic_checker' => $savedDoc->pic_checker ?: '',
            ];
        }

        // Auto-generate Batch Blocks dari data Blending Awal
        $blendingItems = $batch->BlendingAwal ?? collect();
        $batchBlocks = [];
        $detectedAnalis = '';

        if ($blendingItems->count() > 0) {
            // Group berdasarkan nomor_blending atau per item
            $groupedBlending = $blendingItems->groupBy(function($item) {
                return $item->nomor_blending ?: $item->id;
            });

            foreach ($groupedBlending as $noBlending => $items) {
                $first = $items->first();
                if (!$detectedAnalis && $first->user) {
                    $detectedAnalis = $first->user->name;
                }

                $rows = [];
                $samplingIndex = 1;
                foreach ($items as $item) {
                    // Waktu & Adjustment description
                    $adjParts = [];
                    if ($item->adjustment_qty_air) $adjParts[] = 'Air: ' . $item->adjustment_qty_air . ' L';
                    if ($item->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $item->adjustment_qty_garam . ' kg';
                    if ($item->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $item->adjustment_qty_caramel . ' kg';
                    if ($item->disposition_remark) $adjParts[] = $item->disposition_remark;
                    $adjStr = !empty($adjParts) ? implode('; ', $adjParts) : '-';

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
                        'aroma' => $item->aroma ?: 'Khas',
                        'warna' => $item->color ? $item->color->name : 'Standar',
                        'buih' => 'Tidak Ada',
                        'aw' => $item->aw !== null ? (string)$item->aw : '',
                        'waktu_adjustment' => $adjStr,
                        'disposisi' => $item->disposition ?: 'Release',
                    ];
                }

                // Pad rows to at least 4 rows for standard template appearance
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

                $batchBlocks[] = [
                    'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                    'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                    'jam_produksi' => $first->created_at ? $first->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '08:00',
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)($first->batch_range ?: '1'),
                    'no_blending' => (string)($first->nomor_blending ?: $noBlending),
                    'volume_awal' => $first->volume ? ($first->volume . ' L') : '5000 L',
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
                'jenis_produk' => $batch->variant ?: 'Kecap Sedap',
                'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                'jam_produksi' => '08:00',
                'kode_shift_grup' => 'Shift 1 / Grup A',
                'batch' => (string)($batch->batch_range ?: '1'),
                'no_blending' => '1',
                'volume_awal' => '5000 L',
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

        $doc = DocBlendingAwal::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', '1'),
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
            'message' => 'Formulir Hasil Analisis Proses Blending berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi FRM/QLB/04/104/005-01
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
     * Tampilan Khusus Cetak / Print Dokumen
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
