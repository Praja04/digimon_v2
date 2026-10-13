<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocAdjustmentExport;
use App\Http\Controllers\Controller;
use App\Models\DocAdjustment;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                        'volume_batch' => $savedDoc->volume_batch ?: '10000 L',
                        'shift' => ($savedDoc->shift && $savedDoc->shift !== 'Shift 1 / Grup A' && !str_starts_with($savedDoc->shift, 'Shift ')) ? $savedDoc->shift : $this->resolveShiftGroup($batch, $savedDoc->no_batch),
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

            // Pastikan setiap lembar memiliki minimal 9 baris bahan (7 standar + 2 baris tambahan sesuai form resmi)
            foreach ($sheets as &$sh) {
                if (empty($sh['shift']) || $sh['shift'] === 'Shift 1 / Grup A' || str_starts_with($sh['shift'], 'Shift ')) {
                    $sh['shift'] = $this->resolveShiftGroup($batch, $sh['no_batch'] ?? null);
                }
                if (!isset($sh['bahan_rows']) || !is_array($sh['bahan_rows'])) {
                    $sh['bahan_rows'] = [];
                }
                foreach ($sh['bahan_rows'] as &$bRow) {
                    if (isset($bRow['adj1'])) $bRow['adj1'] = $this->formatBlankIfZero($bRow['adj1']);
                    if (isset($bRow['adj2'])) $bRow['adj2'] = $this->formatBlankIfZero($bRow['adj2']);
                    if (isset($bRow['adj3'])) $bRow['adj3'] = $this->formatBlankIfZero($bRow['adj3']);
                }
                unset($bRow);

                while (count($sh['bahan_rows']) < 9) {
                    $sh['bahan_rows'][] = ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''];
                }
            }
            unset($sh);

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
                        $scanTime = $item->scanned_at ?: $item->created_at;
                        $jamStr = $scanTime ? Carbon::parse($scanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '';
                        $curValues = [];
                        if ($item->adjustment_qty_air && floatval($item->adjustment_qty_air) > 0) $curValues['Air'] = $item->adjustment_qty_air . ' L';
                        if ($item->adjustment_qty_garam && floatval($item->adjustment_qty_garam) > 0) $curValues['Garam Kasar'] = $item->adjustment_qty_garam . ' kg';
                        if ($item->adjustment_qty_caramel && floatval($item->adjustment_qty_caramel) > 0) $curValues['Karamel (Jenis)'] = $item->adjustment_qty_caramel . ' kg';
                        if (!empty($item->adjustment_qty_gula_kelapa) && floatval($item->adjustment_qty_gula_kelapa) > 0) $curValues['Gula Kelapa'] = $item->adjustment_qty_gula_kelapa . ' kg';
                        if (!empty($item->adjustment_qty_gula_tebu) && floatval($item->adjustment_qty_gula_tebu) > 0) $curValues['Gula Tebu'] = $item->adjustment_qty_gula_tebu . ' kg';
                        if (!empty($item->adjustment_qty_gula) && floatval($item->adjustment_qty_gula) > 0 && empty($curValues['Gula Kelapa']) && empty($curValues['Karamel (Jenis)'])) {
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
                // 2 baris tambahan sesuai format spreadsheet resmi FRM/QLB/04/104/011-00
                $bahanRows[] = ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''];
                $bahanRows[] = ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''];

                $adjustmentSheets[] = [
                    'proses' => 'Blending',
                    'jenis_kecap' => $batch->variant ?: 'Kecap Sedap',
                    'no_batch' => (string)($first->batch_range ?: ($cIdx + 1)),
                    'tanggal_produksi' => $first->created_at ? $first->created_at->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                    'volume_batch' => $first->volume ? ($first->volume . ' L') : '10000 L',
                    'shift' => $this->resolveShiftGroup($batch, (string)($first->batch_range ?: ($cIdx + 1))),
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
            // 2 baris tambahan sesuai format spreadsheet resmi FRM/QLB/04/104/011-00
            $bahanRows[] = ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''];
            $bahanRows[] = ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''];

            $adjustmentSheets[] = [
                'proses' => 'Blending',
                'jenis_kecap' => $batch->variant ?: 'Kecap Sedap',
                'no_batch' => (string)($batch->batch_range ?: '1'),
                'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
                'volume_batch' => '10000 L',
                'shift' => $this->resolveShiftGroup($batch, (string)($batch->batch_range ?: '1')),
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

    /**
     * Format nilai analisis: jika 0, 0.0, null, '-', atau kosong maka dijadikan blank
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
     * Lookup Shift & Grup dinamis dari production.blending_awal (e.g. 1/A atau 3/C)
     */
    private function resolveShiftGroup($batch, $batchRange = null): string
    {
        try {
            $prodBatch = DB::table('production.production_batches')
                ->where('po_number', $batch->po_number)
                ->orWhere('po_number', 'LIKE', '%' . $batch->po_number . '%')
                ->first();
            $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;

            $blRow = DB::table('production.blending_awal')
                ->where(function($q) use ($pBatchId, $batch) {
                    $q->where('production_batch_id', $pBatchId)
                      ->orWhere('production_batch_id', $batch->id);
                })
                ->when($batchRange, fn($q) => $q->where('batch_range', $batchRange))
                ->first();

            if (!$blRow) {
                $blRow = DB::table('production.monitoring_turun_blending')
                    ->where(function($q) use ($pBatchId, $batch) {
                        $q->where('production_batch_id', $pBatchId)
                          ->orWhere('production_batch_id', $batch->id);
                    })
                    ->when($batchRange, fn($q) => $q->where('batch_range', $batchRange))
                    ->first();
            }

            if ($blRow) {
                $s = $blRow->shift ? (string)$blRow->shift : '';
                $g = $blRow->group ? (string)$blRow->group : '';
                if ($s !== '' && $g !== '') return "{$s}/{$g}";
                if ($s !== '') return $s;
                if ($g !== '') return $g;
            }
        } catch (\Throwable $e) {}
        return '';
    }
}
