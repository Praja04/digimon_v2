<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPelarutanExport;
use App\Http\Controllers\Controller;
use App\Models\DocPelarutan;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            // Pastikan setiap blok terisi 5 baris minimum & nilai 0 / kosong dijadikan blank
            foreach ($blocks as &$blk) {
                if (!isset($blk['rows'])) $blk['rows'] = [];
                foreach ($blk['rows'] as &$r) {
                    $r['p1_nacl'] = ''; // Revisi: NaCl Pelarutan 1 selalu blank
                    $r['p1_brix'] = $this->formatBlankIfZero($r['p1_brix'] ?? '');
                    $r['p2_brix'] = $this->formatBlankIfZero($r['p2_brix'] ?? '');
                    if (isset($r['p2_visco'])) $r['p2_visco'] = $this->formatBlankIfZero($r['p2_visco']);
                    if (isset($r['p2_nacl'])) $r['p2_nacl'] = $this->formatBlankIfZero($r['p2_nacl']);
                }
                unset($r);

                // Perbaiki kode_shift_grup jika masih bernilai dummy atau kosong
                if (empty($blk['kode_shift_grup']) || $blk['kode_shift_grup'] === 'Shift 1 / Grup A' || request()->has('force_db')) {
                    try {
                        $prodBatch = DB::table('production.production_batches')->where('po_number', $batch->po_number)->first();
                        $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
                        $bNumVal = $blk['batch'] ?? '1';
                        $dRow = DB::table('production.dissolver')
                            ->where('production_batch_id', $pBatchId)
                            ->where('batch_number', (string)$bNumVal)
                            ->first();
                        if (!$dRow && $pBatchId != $batch->id) {
                            $dRow = DB::table('production.dissolver')
                                ->where('production_batch_id', $batch->id)
                                ->where('batch_number', (string)$bNumVal)
                                ->first();
                        }
                        if ($dRow) {
                            $s = $dRow->shift ? (string)$dRow->shift : '';
                            $g = $dRow->group ? (string)$dRow->group : '';
                            if ($s !== '' && $g !== '') {
                                $blk['kode_shift_grup'] = "{$s}/{$g}";
                            } elseif ($s !== '') {
                                $blk['kode_shift_grup'] = $s;
                            } elseif ($g !== '') {
                                $blk['kode_shift_grup'] = $g;
                            } else {
                                $blk['kode_shift_grup'] = '';
                            }
                            if (!empty($dRow->dissolver_number) && (empty($blk['no_dissolver']) || $blk['no_dissolver'] === $bNumVal)) {
                                $blk['no_dissolver'] = $dRow->dissolver_number;
                            }
                        } else {
                            $blk['kode_shift_grup'] = '';
                        }
                    } catch (\Throwable $e) {}
                }

                // Perbaiki jam_produksi sesuai jadwal shift berlangsung
                if (empty($blk['jam_produksi']) || $blk['jam_produksi'] === '08:00' || request()->has('force_db')) {
                    $sVal = $blk['kode_shift_grup'] ?? '';
                    if (str_starts_with($sVal, '1')) $blk['jam_produksi'] = '06:00 - 14:00';
                    elseif (str_starts_with($sVal, '2')) $blk['jam_produksi'] = '14:00 - 22:00';
                    elseif (str_starts_with($sVal, '3')) $blk['jam_produksi'] = '22:00 - 06:00';
                    else $blk['jam_produksi'] = '';
                }

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

                // 1. Lookup production.dissolver
                $dissolverShiftGroup = '';
                $dissolverName = (string)$dissolver;
                $dRow = null;

                try {
                    $prodBatch = DB::table('production.production_batches')->where('po_number', $batch->po_number)->first();
                    $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
                    $dRow = DB::table('production.dissolver')
                        ->where('production_batch_id', $pBatchId)
                        ->where('batch_number', (string)$bNum)
                        ->first();
                    if (!$dRow && $pBatchId != $batch->id) {
                        $dRow = DB::table('production.dissolver')
                            ->where('production_batch_id', $batch->id)
                            ->where('batch_number', (string)$bNum)
                            ->first();
                    }
                    if ($dRow) {
                        $s = $dRow->shift ? (string)$dRow->shift : '';
                        $g = $dRow->group ? (string)$dRow->group : '';
                        if ($s !== '' && $g !== '') {
                            $dissolverShiftGroup = "{$s}/{$g}";
                        } elseif ($s !== '') {
                            $dissolverShiftGroup = $s;
                        } elseif ($g !== '') {
                            $dissolverShiftGroup = $g;
                        }
                        if (!empty($dRow->dissolver_number)) {
                            $dissolverName = $dRow->dissolver_number;
                        }
                    }
                } catch (\Throwable $e) {}

                // 2. Jam Produksi: jam shift berlangsung
                // Shift 1 -> 06:00 - 14:00
                // Shift 2 -> 14:00 - 22:00
                // Shift 3 -> 22:00 - 06:00
                $shiftScheduleMap = [
                    1 => '06:00 - 14:00',
                    2 => '14:00 - 22:00',
                    3 => '22:00 - 06:00',
                    '1' => '06:00 - 14:00',
                    '2' => '14:00 - 22:00',
                    '3' => '22:00 - 06:00',
                ];

                $detectedShiftNum = $dRow->shift ?? null;
                if (!$detectedShiftNum && $dissolverShiftGroup) {
                    if (preg_match('/^(\d+)/', $dissolverShiftGroup, $m)) {
                        $detectedShiftNum = $m[1];
                    }
                }

                if ($detectedShiftNum && isset($shiftScheduleMap[$detectedShiftNum])) {
                    $jamProduksiStr = $shiftScheduleMap[$detectedShiftNum];
                } else {
                    $jamProduksiStr = '';
                }

                // 3. Query production.pelarutan_1 dan pelarutan_2 untuk Jam Disposisi
                $prodP1Items = collect();
                $prodP2Items = collect();
                if ($dRow) {
                    try {
                        $prodP1Items = DB::table('production.pelarutan_1')->where('dissolver_id', $dRow->id)->orderBy('id')->get();
                        $prodP2Items = DB::table('production.pelarutan_2')->where('dissolver_id', $dRow->id)->orderBy('id')->get();
                    } catch (\Throwable $e) {}
                }

                for ($i = 0; $i < $maxRows; $i++) {
                    $p1 = $p1ForBatch->get($i);
                    $p2 = $p2ForBatch->get($i);

                    if (!$detectedAnalis) {
                        if ($p1 && $p1->user) $detectedAnalis = $p1->user->name;
                        elseif ($p2 && $p2->user) $detectedAnalis = $p2->user->name;
                    }

                    // Adjustment & Jam Disposisi P1
                    $adj1 = [];
                    if ($p1) {
                        if ($p1->adjustment_qty_gula_tebu) $adj1[] = 'Tebu: ' . $p1->adjustment_qty_gula_tebu . ' kg';
                        if ($p1->adjustment_qty_gula_kelapa) $adj1[] = 'Kelapa: ' . $p1->adjustment_qty_gula_kelapa . ' kg';
                        if ($p1->disposition_remark && $p1->disposition_remark !== '-') $adj1[] = $p1->disposition_remark;
                    }
                    $prodP1Row = $prodP1Items->get($i) ?: $prodP1Items->last();
                    $p1JamDisp = '';
                    if ($prodP1Row && $prodP1Row->jam_selesai) {
                        $p1JamDisp = Carbon::parse($prodP1Row->jam_selesai)->setTimezone('Asia/Jakarta')->format('H:i');
                    } elseif ($p1 && $p1->disposition && $p1->updated_at) {
                        $p1JamDisp = $p1->updated_at->setTimezone('Asia/Jakarta')->format('H:i');
                    }
                    $p1AdjParts = [];
                    if ($p1JamDisp) $p1AdjParts[] = $p1JamDisp;
                    if (!empty($adj1)) $p1AdjParts[] = implode('; ', $adj1);
                    $p1AdjStr = !empty($p1AdjParts) ? implode("\n", $p1AdjParts) : ($p1JamDisp ?: '-');

                    // Adjustment & Jam Disposisi P2
                    $adj2 = [];
                    if ($p2) {
                        if ($p2->adjustment_qty_gula_tebu) $adj2[] = 'Tebu: ' . $p2->adjustment_qty_gula_tebu . ' kg';
                        if ($p2->adjustment_qty_gula_kelapa) $adj2[] = 'Kelapa: ' . $p2->adjustment_qty_gula_kelapa . ' kg';
                        if ($p2->disposition_remark && $p2->disposition_remark !== '-') $adj2[] = $p2->disposition_remark;
                    }
                    $prodP2Row = $prodP2Items->get($i) ?: $prodP2Items->last();
                    $p2JamDisp = '';
                    if ($prodP2Row && $prodP2Row->jam_selesai) {
                        $p2JamDisp = Carbon::parse($prodP2Row->jam_selesai)->setTimezone('Asia/Jakarta')->format('H:i');
                    } elseif ($p2 && $p2->disposition && $p2->updated_at) {
                        $p2JamDisp = $p2->updated_at->setTimezone('Asia/Jakarta')->format('H:i');
                    }
                    $p2AdjParts = [];
                    if ($p2JamDisp) $p2AdjParts[] = $p2JamDisp;
                    if (!empty($adj2)) $p2AdjParts[] = implode('; ', $adj2);
                    $p2AdjStr = !empty($p2AdjParts) ? implode("\n", $p2AdjParts) : ($p2JamDisp ?: '-');

                    $disposisi = ($p2 && $p2->disposition) ? $p2->disposition : (($p1 && $p1->disposition) ? $p1->disposition : ($prodP2Row?->disposition ?: ($prodP1Row?->disposition ?: 'Release')));

                    // Jam serah terima: HANYA jam scan oleh analis kimia (bukan jam tambah batch)
                    $p1ScanTime = $p1?->scanned_at;
                    if (!$p1ScanTime && $p1) {
                        $p1ScanTime = DB::table('qr_scan_logs')
                            ->where('qc_type', 'pelarutan_1')
                            ->where('qc_id', $p1->id)
                            ->value('scanned_at');
                    }
                    $p2ScanTime = $p2?->scanned_at;
                    if (!$p2ScanTime && $p2) {
                        $p2ScanTime = DB::table('qr_scan_logs')
                            ->where('qc_type', 'pelarutan_2')
                            ->where('qc_id', $p2->id)
                            ->value('scanned_at');
                    }

                    $p2ViscoVal = ($p2 && $p2->visco !== null && $p2->visco !== '') ? (string)$p2->visco : (($p2 && $p2->nacl !== null && $p2->nacl !== '') ? (string)$p2->nacl : '');

                    $rows[] = [
                        'p1_sampling_ke' => (string)($i + 1),
                        'p1_jam' => $p1ScanTime ? Carbon::parse($p1ScanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p1_pic' => $p1 && $p1->user ? $p1->user->name : '',
                        'p1_brix' => $this->formatBlankIfZero($p1?->brix),
                        'p1_nacl' => '', // Revisi resmi: NaCl Pelarutan 1 (GGA) selalu dibuat blank
                        'p1_warna' => 'Standar',
                        'p1_organo' => $p1 && $p1->organo ? $p1->organo : 'OK',
                        'p1_waktu_adjustment' => $p1AdjStr,

                        'p2_sampling_ke' => (string)($i + 1),
                        'p2_jam' => $p2ScanTime ? Carbon::parse($p2ScanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p2_pic' => $p2 && $p2->user ? $p2->user->name : '',
                        'p2_brix' => $this->formatBlankIfZero($p2?->brix),
                        'p2_visco' => $this->formatBlankIfZero($p2ViscoVal),
                        'p2_nacl' => $this->formatBlankIfZero($p2ViscoVal),
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
                        'p2_visco' => '',
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
                    'kode_shift_grup' => $dissolverShiftGroup,
                    'batch' => (string)$bNum,
                    'no_dissolver' => (string)$dissolverName,
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
                    'jam_produksi' => $jamProduksiStr,
                    'kode_shift_grup' => $dissolverShiftGroup,
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
                    'jam_produksi' => '06:00 - 14:00',
                    'kode_shift_grup' => '',
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
