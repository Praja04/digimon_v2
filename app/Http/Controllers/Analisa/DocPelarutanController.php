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
     * Tampilkan Halaman Form Hasil Analisis Proses Pelarutan
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
     */
    private function getDocumentData($poId)
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

        // Auto-generate Batch Blocks dari data Pelarutan 1 & 2
        $p1Items = $batch->pelarutan_1 ?? collect();
        $p2Items = $batch->pelarutan_2 ?? collect();

        // Ambil semua nomor batch unik
        $batchNumbers = $p1Items->pluck('batch_number')
            ->merge($p2Items->pluck('batch_number'))
            ->unique()
            ->values();

        $batchBlocks = [];
        $detectedAnalis = '';

        if ($batchNumbers->count() > 0) {
            foreach ($batchNumbers as $bNum) {
                $p1ForBatch = $p1Items->where('batch_number', $bNum)->values();
                $p2ForBatch = $p2Items->where('batch_number', $bNum)->values();

                $dissolver = $p1ForBatch->first()?->dissolver_number ?: ($p2ForBatch->first()?->dissolver_number ?: '1');
                $maxRows = max(1, $p1ForBatch->count(), $p2ForBatch->count());
                $rows = [];

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

                // Pad rows to at least 4 rows for standard template appearance
                while (count($rows) < 4) {
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
                    'jam_produksi' => '08:00',
                    'kode_shift_grup' => 'Shift 1 / Grup A',
                    'batch' => (string)$bNum,
                    'no_dissolver' => (string)$dissolver,
                    'volume' => '5000 L',
                    'rows' => $rows,
                ];
            }

            // Jika hanya 1 blok yang terdeteksi, tambahkan blok kedua sebagai template
            if (count($batchBlocks) == 1) {
                $block2Rows = [];
                for ($r = 1; $r <= 4; $r++) {
                    $block2Rows[] = [
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
                    'batch' => '2',
                    'no_dissolver' => '2',
                    'volume' => '5000 L',
                    'rows' => $block2Rows,
                ];
            }
        } else {
            // Default 2 blok kosong dengan 4 baris per blok seperti template resmi
            for ($b = 1; $b <= 2; $b++) {
                $defaultRows = [];
                for ($r = 1; $r <= 4; $r++) {
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
                    'volume' => '5000 L',
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
     * Simpan / Perbarui Form Dokumen Pelarutan
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'tanggal_record_doc' => 'nullable|date',
        ]);

        $batchBlocks = $request->input('batch_blocks', []);

        $doc = DocPelarutan::updateOrCreate(
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
            'message' => 'Formulir Hasil Analisis Proses Pelarutan berhasil disimpan!',
            'data' => $doc
        ]);
    }

    /**
     * Export Dokumen ke Excel Sesuai Format Resmi Gambar
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
     * Tampilan Khusus Cetak / Print Dokumen
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
