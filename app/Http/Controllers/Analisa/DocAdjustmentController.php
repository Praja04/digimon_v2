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
     * 1 Lembar: 1 Batch atau 1 Pasangan Batch (1 Lembar untuk 3x Adjustment)
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
            $rawBahan = $savedDoc->bahan_rows;
            $sheets = [];

            // Cek apakah bahan_rows menyimpan array multi-sheet atau baris tunggal
            if (is_array($rawBahan) && isset($rawBahan[0]) && isset($rawBahan[0]['bahan_rows'])) {
                $sheets = $rawBahan;
            } else {
                $sheets = [
                    [
                        'proses' => $savedDoc->proses ?: 'Blending',
                        'jenis_kecap' => $savedDoc->jenis_kecap ?: ($batch->variant ?: 'Kecap Sedap'),
                        'no_batch' => $savedDoc->no_batch ?: ($batch->batch_range ?: '1'),
                        'tanggal_produksi' => $savedDoc->tanggal_produksi ? $savedDoc->tanggal_produksi->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                        'volume_batch' => $savedDoc->volume_batch ?: '5000 L',
                        'shift' => $savedDoc->shift ?: 'Shift 1 / Grup A',
                        'bahan_rows' => is_array($rawBahan) ? $rawBahan : [],
                        'disposisi' => $savedDoc->disposisi ?: 'Release',
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
                    ]
                ];
            }

            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $batch->variant,
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $savedDoc->halaman ?: '1 / ' . count($sheets),
                'adjustment_sheets' => $sheets,
                'qc_analis' => $savedDoc->qc_analis ?: '',
                'doc_code' => $savedDoc->doc_code ?: 'FRM/QLB/04/104/011-00',
            ];
        }

        // Auto-generate Adjustment Sheets dari data Blending Awal
        $defaultBahans = [
            'Gula Kelapa',
            'Gula Tebu',
            'Larutan Garam',
            'Garam Kasar',
            'Air',
            'Garam Halus',
            'Karamel (Jenis)',
        ];

        $blendingItems = $batch->BlendingAwal ? $batch->BlendingAwal->sortBy('id')->values() : collect();
        $adjustmentSheets = [];
        $detectedAnalis = '';

        if ($blendingItems->count() > 0) {
            // Group items into separate Blending Cycles / Tanks (1 Lembar = 1 Pasangan Batch)
            $cycles = [];
            $currentCycle = [];
            $prevItem = null;

            foreach ($blendingItems as $item) {
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

                $currentCycle[] = $item;
                $prevItem = $item;
            }

            if (!empty($currentCycle)) {
                $cycles[] = $currentCycle;
            }

            foreach ($cycles as $cIdx => $cycleItems) {
                if (empty($cycleItems)) continue;

                $first = $cycleItems[0];
                $last = end($cycleItems);

                if (!$detectedAnalis && $first->user) {
                    $detectedAnalis = $first->user->name;
                }

                // Kumpulkan data adjustment 1, 2, 3 dalam siklus ini
                $adj1Values = [];
                $adj2Values = [];
                $adj3Values = [];
                $adj1Jam = ''; $adj1Status = 'Sudah dilakukan';
                $adj2Jam = ''; $adj2Status = 'Sudah dilakukan';
                $adj3Jam = ''; $adj3Status = 'Sudah dilakukan';

                $adjCount = 0;
                foreach ($cycleItems as $item) {
                    $hasAdj = ($item->adjustment_qty_air || $item->adjustment_qty_garam || $item->adjustment_qty_caramel || !empty($item->adjustment_qty_gula) || !empty($item->adjustment_qty_gula_kelapa) || !empty($item->adjustment_qty_gula_tebu) || strtolower((string)$item->disposition) === 'adjustment');
                    if ($hasAdj) {
                        $adjCount++;
                        $jamStr = $item->created_at ? $item->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
                        $curValues = [];
                        if ($item->adjustment_qty_air) $curValues['Air'] = $item->adjustment_qty_air . ' L';
                        if ($item->adjustment_qty_garam) $curValues['Garam Kasar'] = $item->adjustment_qty_garam . ' kg';
                        if ($item->adjustment_qty_caramel) $curValues['Karamel (Jenis)'] = $item->adjustment_qty_caramel . ' kg';
                        if (!empty($item->adjustment_qty_gula_kelapa)) $curValues['Gula Kelapa'] = $item->adjustment_qty_gula_kelapa . ' kg';
                        if (!empty($item->adjustment_qty_gula_tebu)) $curValues['Gula Tebu'] = $item->adjustment_qty_gula_tebu . ' kg';
                        if (!empty($item->adjustment_qty_gula) && empty($curValues['Gula Kelapa']) && empty($curValues['Karamel (Jenis)'])) {
                            $curValues['Gula Kelapa'] = $item->adjustment_qty_gula . ' kg';
                        }

                        if ($adjCount === 1) {
                            $adj1Jam = $jamStr;
                            $adj1Values = $curValues;
                        } elseif ($adjCount === 2) {
                            $adj2Jam = $jamStr;
                            $adj2Values = $curValues;
                        } elseif ($adjCount === 3) {
                            $adj3Jam = $jamStr;
                            $adj3Values = $curValues;
                        }
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

                $adjustmentSheets[] = [
                    'proses' => 'Blending',
                    'jenis_kecap' => $batch->variant ?: 'Kecap Sedap',
                    'no_batch' => (string)($first->batch_range ?: ($cIdx + 1)),
                    'tanggal_produksi' => $first->created_at ? $first->created_at->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                    'volume_batch' => $first->volume ? ($first->volume . ' L') : '10000 L',
                    'shift' => 'Shift 1 / Grup A',
                    'bahan_rows' => $bahanRows,
                    'disposisi' => $last->disposition ?: 'Release',
                    'keterangan' => '',
                    'adj1_jam' => $adj1Jam,
                    'adj1_status' => $adj1Status,
                    'adj1_petugas' => '',
                    'adj2_jam' => $adj2Jam,
                    'adj2_status' => $adj2Status,
                    'adj2_petugas' => '',
                    'adj3_jam' => $adj3Jam,
                    'adj3_status' => $adj3Status,
                    'adj3_petugas' => '',
                    'qc_analis' => $first->user ? $first->user->name : ($detectedAnalis ?: (auth()->user()->name ?? '')),
                ];
            }
        } else {
            // Default 1 lembar template
            $bahanRows = [];
            foreach ($defaultBahans as $bName) {
                $bahanRows[] = [
                    'bahan' => $bName,
                    'adj1' => '',
                    'adj2' => '',
                    'adj3' => '',
                ];
            }

            $adjustmentSheets[] = [
                'proses' => 'Blending',
                'jenis_kecap' => $batch->variant ?: 'Kecap Sedap',
                'no_batch' => (string)($batch->batch_range ?: '1'),
                'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                'volume_batch' => '10000 L',
                'shift' => 'Shift 1 / Grup A',
                'bahan_rows' => $bahanRows,
                'disposisi' => 'Release',
                'keterangan' => '',
                'adj1_jam' => '08:00',
                'adj1_status' => 'Sudah dilakukan',
                'adj1_petugas' => '',
                'adj2_jam' => '',
                'adj2_status' => 'Sudah dilakukan',
                'adj2_petugas' => '',
                'adj3_jam' => '',
                'adj3_status' => 'Sudah dilakukan',
                'adj3_petugas' => '',
                'qc_analis' => auth()->user()->name ?? '',
            ];
        }

        $totalSheets = count($adjustmentSheets);

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => "1 / {$totalSheets}",
            'adjustment_sheets' => $adjustmentSheets,
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

        $sheets = $request->input('adjustment_sheets', []);
        $firstSheet = !empty($sheets[0]) ? $sheets[0] : [];
        $totalSheets = max(1, count($sheets));

        $doc = DocAdjustment::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', "1 / {$totalSheets}"),
                'proses' => $firstSheet['proses'] ?? 'Blending',
                'jenis_kecap' => $firstSheet['jenis_kecap'] ?? null,
                'no_batch' => $firstSheet['no_batch'] ?? null,
                'tanggal_produksi' => $firstSheet['tanggal_produksi'] ?? null,
                'volume_batch' => $firstSheet['volume_batch'] ?? null,
                'shift' => $firstSheet['shift'] ?? null,
                'bahan_rows' => $sheets,
                'disposisi' => $firstSheet['disposisi'] ?? 'Release',
                'keterangan' => $firstSheet['keterangan'] ?? null,
                'adj1_jam' => $firstSheet['adj1_jam'] ?? null,
                'adj1_status' => $firstSheet['adj1_status'] ?? null,
                'adj1_petugas' => $firstSheet['adj1_petugas'] ?? null,
                'adj2_jam' => $firstSheet['adj2_jam'] ?? null,
                'adj2_status' => $firstSheet['adj2_status'] ?? null,
                'adj2_petugas' => $firstSheet['adj2_petugas'] ?? null,
                'adj3_jam' => $firstSheet['adj3_jam'] ?? null,
                'adj3_status' => $firstSheet['adj3_status'] ?? null,
                'adj3_petugas' => $firstSheet['adj3_petugas'] ?? null,
                'qc_analis' => $request->input('qc_analis') ?? ($firstSheet['qc_analis'] ?? null),
                'doc_code' => 'FRM/QLB/04/104/011-00',
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
