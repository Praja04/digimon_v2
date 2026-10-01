<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocAdjustmentExport;
use App\Http\Controllers\Controller;
use App\Models\DocAdjustment;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocAdjustmentController extends Controller
{
    /**
     * Tampilkan Halaman Form Adjustment (FRM/QLB/04/104/011-00)
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

        return view('app.doc_adjustment.index', [
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

        $savedDoc = DocAdjustment::where('production_batch_id', $poId)->first();

        if ($savedDoc) {
            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $batch->variant,
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $savedDoc->halaman ?: '1',
                'proses' => $savedDoc->proses ?: 'Blending',
                'jenis_kecap' => $savedDoc->jenis_kecap ?: ($batch->variant ?: 'Kecap Sedap'),
                'no_batch' => $savedDoc->no_batch ?: ($batch->batch_range ?: '1'),
                'tanggal_produksi' => $savedDoc->tanggal_produksi ? $savedDoc->tanggal_produksi->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'volume_batch' => $savedDoc->volume_batch ?: '5000 L',
                'shift' => $savedDoc->shift ?: '1',
                'bahan_rows' => $savedDoc->bahan_rows ?: [],
                'disposisi' => $savedDoc->disposisi ?: '',
                'keterangan' => $savedDoc->keterangan ?: '',
                'adj1_jam' => $savedDoc->adj1_jam ?: '',
                'adj1_status' => $savedDoc->adj1_status ?: 'Sudah dilakukan',
                'adj1_petugas' => $savedDoc->adj1_petugas ?: '',
                'adj2_jam' => $savedDoc->adj2_jam ?: '',
                'adj2_status' => $savedDoc->adj2_status ?: 'Sudah dilakukan',
                'adj2_petugas' => $savedDoc->adj2_petugas ?: '',
                'adj3_jam' => $savedDoc->adj3_jam ?: '',
                'adj3_status' => $savedDoc->adj3_status ?: 'Sudah dilakukan',
                'adj3_petugas' => $savedDoc->adj3_petugas ?: '',
                'qc_analis' => $savedDoc->qc_analis ?: '',
                'doc_code' => $savedDoc->doc_code ?: 'FRM/QLB/04/104/011-00',
            ];
        }

        // Auto-generate bahan rows dari Blending Awal adjustments
        $defaultBahans = [
            'Gula Kelapa',
            'Gula Tebu',
            'Larutan Garam',
            'Garam Kasar',
            'Air',
            'Garam Halus',
            'Karamel (Jenis)',
            '',
            '',
        ];

        $blendingItems = $batch->BlendingAwal ?? collect();
        $adj1Values = [];
        $adj2Values = [];
        $adj3Values = [];
        $detectedAnalis = '';
        $detectedDisposisi = '';
        $detectedVolume = '';

        foreach ($blendingItems as $idx => $bItem) {
            if (!$detectedAnalis && $bItem->user) {
                $detectedAnalis = $bItem->user->name;
            }
            if (!$detectedDisposisi && $bItem->disposition) {
                $detectedDisposisi = $bItem->disposition;
            }
            if (!$detectedVolume && $bItem->volume) {
                $detectedVolume = $bItem->volume . ' L';
            }

            if ($idx === 0) {
                if ($bItem->adjustment_qty_air) $adj1Values['Air'] = $bItem->adjustment_qty_air . ' L';
                if ($bItem->adjustment_qty_garam) $adj1Values['Garam Kasar'] = $bItem->adjustment_qty_garam . ' kg';
                if ($bItem->adjustment_qty_caramel) $adj1Values['Karamel (Jenis)'] = $bItem->adjustment_qty_caramel . ' kg';
            } elseif ($idx === 1) {
                if ($bItem->adjustment_qty_air) $adj2Values['Air'] = $bItem->adjustment_qty_air . ' L';
                if ($bItem->adjustment_qty_garam) $adj2Values['Garam Kasar'] = $bItem->adjustment_qty_garam . ' kg';
                if ($bItem->adjustment_qty_caramel) $adj2Values['Karamel (Jenis)'] = $bItem->adjustment_qty_caramel . ' kg';
            } elseif ($idx === 2) {
                if ($bItem->adjustment_qty_air) $adj3Values['Air'] = $bItem->adjustment_qty_air . ' L';
                if ($bItem->adjustment_qty_garam) $adj3Values['Garam Kasar'] = $bItem->adjustment_qty_garam . ' kg';
                if ($bItem->adjustment_qty_caramel) $adj3Values['Karamel (Jenis)'] = $bItem->adjustment_qty_caramel . ' kg';
            }
        }

        $bahanRows = [];
        foreach ($defaultBahans as $bName) {
            $bahanRows[] = [
                'bahan' => $bName,
                'adj1' => $adj1Values[$bName] ?? '',
                'adj2' => $adj2Values[$bName] ?? '',
                'adj3' => $adj3Values[$bName] ?? '',
            ];
        }

        $firstBlending = $blendingItems->first();

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => '1',
            'proses' => 'Blending',
            'jenis_kecap' => $batch->variant ?: 'Kecap Sedap',
            'no_batch' => (string)($batch->batch_range ?: ($firstBlending?->batch_range ?: '1')),
            'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'volume_batch' => $detectedVolume ?: '5000 L',
            'shift' => '1',
            'bahan_rows' => $bahanRows,
            'disposisi' => $detectedDisposisi ?: 'Release',
            'keterangan' => '',
            'adj1_jam' => $firstBlending?->created_at ? $firstBlending->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '08:00',
            'adj1_status' => 'Sudah dilakukan',
            'adj1_petugas' => '',
            'adj2_jam' => '',
            'adj2_status' => 'Sudah dilakukan',
            'adj2_petugas' => '',
            'adj3_jam' => '',
            'adj3_status' => 'Sudah dilakukan',
            'adj3_petugas' => '',
            'qc_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'doc_code' => 'FRM/QLB/04/104/011-00',
        ];
    }

    /**
     * Simpan / Perbarui Form Dokumen Adjustment
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'tanggal_record_doc' => 'nullable|date',
        ]);

        $doc = DocAdjustment::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', '1'),
                'proses' => $request->input('proses', 'Blending'),
                'jenis_kecap' => $request->input('jenis_kecap'),
                'no_batch' => $request->input('no_batch'),
                'tanggal_produksi' => $request->input('tanggal_produksi'),
                'volume_batch' => $request->input('volume_batch'),
                'shift' => $request->input('shift'),
                'bahan_rows' => $request->input('bahan_rows', []),
                'disposisi' => $request->input('disposisi'),
                'keterangan' => $request->input('keterangan'),
                'adj1_jam' => $request->input('adj1_jam'),
                'adj1_status' => $request->input('adj1_status'),
                'adj1_petugas' => $request->input('adj1_petugas'),
                'adj2_jam' => $request->input('adj2_jam'),
                'adj2_status' => $request->input('adj2_status'),
                'adj2_petugas' => $request->input('adj2_petugas'),
                'adj3_jam' => $request->input('adj3_jam'),
                'adj3_status' => $request->input('adj3_status'),
                'adj3_petugas' => $request->input('adj3_petugas'),
                'qc_analis' => $request->input('qc_analis'),
                'doc_code' => $request->input('doc_code', 'FRM/QLB/04/104/011-00'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir Form Adjustment (FRM/QLB/04/104/011-00) berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi FRM/QLB/04/104/011-00
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
        $fileName = 'FORM_ADJUSTMENT_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DocAdjustmentExport($docData),
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

        return view('app.doc_adjustment.print', [
            'data' => $docData
        ]);
    }
}
