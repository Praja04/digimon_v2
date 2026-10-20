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

        // Auto-generate Batch Blocks secara LIVE langsung dari data Pelarutan 1 & 2 dan Database Produksi
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

                // 1. Lookup production.dissolver dengan multi-tier matching PO & batch number
                $dissolverShiftGroup = '';
                $dissolverName = (string)$dissolver;
                $jamProduksiStr = '';
                $dRow = null;

                try {
                    $pBatchId = $this->resolveProductionBatchId($batch->po_number, $batch->id);
                    $cleanBNum = preg_replace('/\D/', '', (string)$bNum);

                    if ($pBatchId) {
                        $dRow = DB::table('production.dissolver')
                            ->where('production_batch_id', $pBatchId)
                            ->where(function($q) use ($bNum, $cleanBNum) {
                                $q->where('batch_number', (string)$bNum);
                                if ($cleanBNum !== '') {
                                    $q->orWhere('batch_number', $cleanBNum);
                                }
                            })
                            ->first();

                        if (!$dRow) {
                            $dRow = DB::table('production.dissolver')
                                ->where('production_batch_id', $pBatchId)
                                ->orderBy('id')
                                ->first();
                        }
                    }

                    if ($dRow) {
                        $dissolverShiftGroup = $this->formatShiftGroup($dRow->shift, $dRow->group);

                        if (!empty($dRow->dissolver_number)) {
                            $dissolverName = $dRow->dissolver_number;
                        }

                        if (!empty($dRow->jam_mulai_proses)) {
                            $jamStart = substr($dRow->jam_mulai_proses, 0, 5);
                        } elseif (!empty($dRow->created_at)) {
                            $jamStart = Carbon::parse($dRow->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    }
                } catch (\Throwable $e) {}

                if (empty($jamStart) && $p1ForBatch->first() && $p1ForBatch->first()->created_at) {
                    $jamStart = Carbon::parse($p1ForBatch->first()->created_at)->setTimezone('Asia/Jakarta')->format('H:i');
                }

                $jamEnd = '';
                // 1. Cek jam selesai dari production.pelarutan_2 (Jam Disposisi Release Pelarutan 2)
                if ($dRow) {
                    try {
                        $lastProdP2 = DB::table('production.pelarutan_2')
                            ->where('dissolver_id', $dRow->id)
                            ->whereNotNull('jam_selesai')
                            ->orderByDesc('id')
                            ->first();
                        if ($lastProdP2 && !empty($lastProdP2->jam_selesai)) {
                            $jamEnd = Carbon::parse($lastProdP2->jam_selesai)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    } catch (\Throwable $e) {}
                }
                // 2. Fallback production.pelarutan_1 jika p2 belum ada
                if (empty($jamEnd) && $dRow) {
                    try {
                        $lastProdP1 = DB::table('production.pelarutan_1')
                            ->where('dissolver_id', $dRow->id)
                            ->whereNotNull('jam_selesai')
                            ->orderByDesc('id')
                            ->first();
                        if ($lastProdP1 && !empty($lastProdP1->jam_selesai)) {
                            $jamEnd = Carbon::parse($lastProdP1->jam_selesai)->setTimezone('Asia/Jakarta')->format('H:i');
                        }
                    } catch (\Throwable $e) {}
                }
                // 3. Fallback ke QC pelarutan 2 / 1 updated_at (jam disposisi analis)
                if (empty($jamEnd)) {
                    $lastP2 = $p2ForBatch->last();
                    if ($lastP2 && $lastP2->updated_at) {
                        $jamEnd = Carbon::parse($lastP2->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                    } elseif ($p1ForBatch->last() && $p1ForBatch->last()->updated_at) {
                        $jamEnd = Carbon::parse($p1ForBatch->last()->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                    } elseif ($dRow && !empty($dRow->updated_at) && $dRow->updated_at != $dRow->created_at) {
                        $jamEnd = Carbon::parse($dRow->updated_at)->setTimezone('Asia/Jakarta')->format('H:i');
                    }
                }

                if (!empty($jamStart) && !empty($jamEnd) && $jamEnd !== $jamStart) {
                    $jamProduksiStr = "{$jamStart} - {$jamEnd}";
                } elseif (!empty($jamStart)) {
                    $jamProduksiStr = $jamStart;
                } else {
                    $jamProduksiStr = '-';
                }

                // Fallback Shift & Grup jika belum ada
                if (empty($dissolverShiftGroup) && $p1ForBatch->first() && !empty($p1ForBatch->first()->shift)) {
                    $dissolverShiftGroup = $this->formatShiftGroup($p1ForBatch->first()->shift, null);
                }
                if (empty($dissolverShiftGroup) && !empty($jamStart) && $jamStart !== '-') {
                    $hour = (int)substr($jamStart, 0, 2);
                    if ($hour >= 6 && $hour < 14) {
                        $dissolverShiftGroup = 'Shift 1 - Grup A';
                    } elseif ($hour >= 14 && $hour < 22) {
                        $dissolverShiftGroup = 'Shift 2 - Grup A';
                    } else {
                        $dissolverShiftGroup = 'Shift 3 - Grup A';
                    }
                }
                if (empty($dissolverShiftGroup)) {
                    $dissolverShiftGroup = 'Shift 1 - Grup A';
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

                    // Jam serah terima: Jam scan analis kimia, fallback ke created_at waktu input
                    $p1ScanTime = $p1?->scanned_at;
                    if (!$p1ScanTime && $p1) {
                        $p1ScanTime = DB::table('qr_scan_logs')
                            ->where('qc_type', 'pelarutan_1')
                            ->where('qc_id', $p1->id)
                            ->value('scanned_at');
                    }
                    if (!$p1ScanTime && $p1) {
                        $p1ScanTime = $p1->created_at;
                    }
                    $p2ScanTime = $p2?->scanned_at;
                    if (!$p2ScanTime && $p2) {
                        $p2ScanTime = DB::table('qr_scan_logs')
                            ->where('qc_type', 'pelarutan_2')
                            ->where('qc_id', $p2->id)
                            ->value('scanned_at');
                    }
                    if (!$p2ScanTime && $p2) {
                        $p2ScanTime = $p2->created_at;
                    }

                    $rows[] = [
                        'p1_sampling_ke' => (string)($i + 1),
                        'p1_jam' => $p1ScanTime ? Carbon::parse($p1ScanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p1_pic' => $p1 && $p1->user ? $p1->user->name : '',
                        'p1_brix' => $this->formatBlankIfZero($p1?->brix),
                        'p1_nacl' => $this->formatBlankIfZero($p1?->nacl),
                        'p1_warna' => 'Standar',
                        'p1_organo' => $p1 && $p1->organo ? $p1->organo : 'OK',
                        'p1_waktu_adjustment' => $p1AdjStr,

                        'p2_sampling_ke' => (string)($i + 1),
                        'p2_jam' => $p2ScanTime ? Carbon::parse($p2ScanTime)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                        'p2_pic' => $p2 && $p2->user ? $p2->user->name : '',
                        'p2_brix' => $this->formatBlankIfZero($p2?->brix),
                        'p2_nacl' => $this->formatBlankIfZero($p2?->nacl),
                        'p2_visco' => $this->formatBlankIfZero($p2?->visco),
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
                        'p2_visco' => '',
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
                        'p2_visco' => '',
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
                        'p2_visco' => '',
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
            'is_saved' => $savedDoc ? true : false,
            'id' => $savedDoc?->id,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'tanggal_record_doc' => $savedDoc?->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
            'halaman' => $savedDoc?->halaman ?: "1 / {$totalSheets}",
            'batch_blocks' => $batchBlocks,
            'catatan' => $savedDoc?->catatan ?: '',
            'pic_sampling' => $savedDoc?->pic_sampling ?: '',
            'pic_analis' => $savedDoc?->pic_analis ?: ($detectedAnalis ?: (auth()->user()->name ?? '')),
            'pic_checker' => $savedDoc?->pic_checker ?: '',
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
        if (!auth()->check() || !in_array(strtolower(trim(auth()->user()->role ?? '')), ['foreman', 'supervisor', 'head of dapartement', 'head of department', 'dept head'], true)) {
            abort(403, 'Akses ditolak. Fitur cetak/print hanya dapat diakses oleh role Foreman, Supervisor, atau Head of Department.');
        }

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
