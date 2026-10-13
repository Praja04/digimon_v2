<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPasteurisasiStorageExport;
use App\Http\Controllers\Controller;
use App\Models\DocPasteurisasiStorage;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $detectedShift = '';
        $detectedAnalis = '';
        $jamStart = '';
        $jamEnd = '';
        $detectedSt = 'ST 01';

        try {
            $prodBatch = DB::table('production.production_batches')->where('po_number', $batch->po_number)->first();
            $pBatchId = $prodBatch ? $prodBatch->id : $batch->id;
            $mpRow = DB::table('production.monitoring_pasteurisasi')
                ->where('production_batch_id', $pBatchId)
                ->first();
            if (!$mpRow && $pBatchId != $batch->id) {
                $mpRow = DB::table('production.monitoring_pasteurisasi')
                    ->where('production_batch_id', $batch->id)
                    ->first();
            }
            if ($mpRow) {
                $s = $mpRow->shift ? (string)$mpRow->shift : '';
                $g = $mpRow->group ? (string)$mpRow->group : '';
                if ($s !== '' && $g !== '') {
                    $detectedShift = "{$s}/{$g}";
                } elseif ($s !== '') {
                    $detectedShift = $s;
                } elseif ($g !== '') {
                    $detectedShift = $g;
                }
            }
        } catch (\Throwable $e) {}

        if ($batch->monitoringPasteurisasi && $batch->monitoringPasteurisasi->count() > 0) {
            $firstMp = $batch->monitoringPasteurisasi->first();
            if (!$detectedShift && $firstMp && $firstMp->shift) $detectedShift = (string)$firstMp->shift;
            if ($firstMp && $firstMp->user) $detectedAnalis = $firstMp->user->name;
            $jamStart = $firstMp && $firstMp->created_at ? $firstMp->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
            $lastMp = $batch->monitoringPasteurisasi->last();
            $jamEnd = $lastMp && $lastMp->created_at ? $lastMp->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
        }

        if ($batch->monitoringStorageKimia && $batch->monitoringStorageKimia->count() > 0) {
            $firstMsk = $batch->monitoringStorageKimia->first();
            if ($firstMsk && $firstMsk->storage) $detectedSt = $firstMsk->storage;
        }

        if ($savedDoc && !request()->has('force_db')) {
            $pastRows = $savedDoc->pasteurisasi_rows ?: [];
            $stRows = $savedDoc->storage_rows ?: [];

            // Jika sebelumnya tersimpan di batch_blocks, ambil barisnya
            if (empty($pastRows) && !empty($savedDoc->batch_blocks)) {
                foreach ($savedDoc->batch_blocks as $blk) {
                    if (!empty($blk['pasteurisasi_rows'])) {
                        $pastRows = array_merge($pastRows, $blk['pasteurisasi_rows']);
                    }
                    if (!empty($blk['storage_rows'])) {
                        $stRows = array_merge($stRows, $blk['storage_rows']);
                    }
                }
            }

            // Sanitasi nilai 0 / kosong jadi blank pada baris tersimpan
            foreach ($pastRows as &$pr) {
                if (isset($pr['bj'])) $pr['bj'] = $this->formatBlankIfZero($pr['bj']);
                if (isset($pr['brix'])) $pr['brix'] = $this->formatBlankIfZero($pr['brix']);
                if (isset($pr['ph'])) $pr['ph'] = $this->formatBlankIfZero($pr['ph']);
                if (isset($pr['aw'])) $pr['aw'] = $this->formatBlankIfZero($pr['aw']);
                if (isset($pr['viskositas'])) $pr['viskositas'] = $this->formatBlankIfZero($pr['viskositas']);
                if (isset($pr['buih'])) $pr['buih'] = $this->formatBlankIfZero($pr['buih']);
            }
            unset($pr);

            foreach ($stRows as &$sr) {
                if (isset($sr['bj'])) $sr['bj'] = $this->formatBlankIfZero($sr['bj']);
                if (isset($sr['brix'])) $sr['brix'] = $this->formatBlankIfZero($sr['brix']);
                if (isset($sr['ph'])) $sr['ph'] = $this->formatBlankIfZero($sr['ph']);
                if (isset($sr['nacl'])) $sr['nacl'] = $this->formatBlankIfZero($sr['nacl']);
                if (isset($sr['viskositas'])) $sr['viskositas'] = $this->formatBlankIfZero($sr['viskositas']);
                if (isset($sr['buih'])) $sr['buih'] = $this->formatBlankIfZero($sr['buih']);
                if (isset($sr['aw'])) $sr['aw'] = $this->formatBlankIfZero($sr['aw']);
            }
            unset($sr);

            // Minimal rows
            while (count($pastRows) < 6) {
                $pIdx = count($pastRows);
                $pastRows[] = [
                    'sampling' => ($pIdx === 0) ? 'Tengah' : '',
                    'jam' => '', 'pic' => '', 'bj' => '', 'brix' => '', 'ph' => '', 'aw' => '',
                    'viskositas' => '', 'organo' => '', 'aroma' => '', 'warna' => '', 'buih' => '', 'endapan' => '', 'disposisi' => ''
                ];
            }
            while (count($stRows) < 5) {
                $sIdx = count($stRows);
                $stRows[] = [
                    'sampling' => ($sIdx === 0) ? 'Awal' : (($sIdx === 1) ? 'Outlet' : ''),
                    'jam' => '', 'pic' => '', 'bj' => '', 'brix' => '', 'ph' => '', 'nacl' => '',
                    'viskositas' => '', 'organo' => '', 'aroma' => '', 'warna' => '', 'buih' => '', 'endapan' => '', 'kristal' => '', 'aw' => '', 'disposisi' => ''
                ];
            }

            $singleBlock = [
                'block_index' => 0,
                'halaman' => $savedDoc->halaman ?: '1 / 1',
                'batch' => $savedDoc->batch ?: ($savedDoc->batch_range ?: ($batch->batch_range ?: '')),
                'kode_st' => $savedDoc->kode_st ?: $detectedSt,
                'jenis_produk' => $savedDoc->jenis_produk ?: ($batch->variant ?: ''),
                'tanggal_produksi' => $savedDoc->tanggal_produksi ? $savedDoc->tanggal_produksi->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'jam_produksi_start' => $savedDoc->jam_produksi_start ?: $jamStart,
                'jam_produksi_end' => $savedDoc->jam_produksi_end ?: $jamEnd,
                'kode_shift_grup' => ($savedDoc->kode_shift_grup && $savedDoc->kode_shift_grup !== 'Shift 1 / Grup A' && !str_starts_with($savedDoc->kode_shift_grup, 'Shift ')) ? $savedDoc->kode_shift_grup : ($detectedShift ?: $savedDoc->kode_shift_grup),
                'pasteurisasi_rows' => $pastRows,
                'storage_rows' => $stRows,
                'catatan' => $savedDoc->catatan ?: '',
                'pic_sampling_1' => $savedDoc->pic_sampling ?: '',
                'pic_sampling_2' => $savedDoc->pic_serah_terima ?: '',
                'pic_analis' => $savedDoc->pic_analis ?: ($detectedAnalis ?: (auth()->user()->name ?? '')),
                'pic_checker' => $savedDoc->pic_checker ?: '',
            ];

            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $batch->variant,
                'jenis_produk' => $singleBlock['jenis_produk'],
                'tanggal_produksi' => $singleBlock['tanggal_produksi'],
                'jam_produksi_start' => $singleBlock['jam_produksi_start'],
                'jam_produksi_end' => $singleBlock['jam_produksi_end'],
                'kode_shift_grup' => $singleBlock['kode_shift_grup'],
                'batch' => $singleBlock['batch'],
                'batch_range' => $singleBlock['batch'],
                'kode_st' => $singleBlock['kode_st'],
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $singleBlock['halaman'],
                'pasteurisasi_rows' => $pastRows,
                'storage_rows' => $stRows,
                'catatan' => $singleBlock['catatan'],
                'pic_sampling_1' => $singleBlock['pic_sampling_1'],
                'pic_sampling_2' => $singleBlock['pic_sampling_2'],
                'pic_analis' => $singleBlock['pic_analis'],
                'pic_checker' => $singleBlock['pic_checker'],
                'batch_blocks' => [$singleBlock],
                'doc_code' => $savedDoc->doc_code ?: 'FRM/QLB/04/104/006-01',
            ];
        }

        // Auto-generate Fresh Data dari Database (1 Lembar Formulir Tunggal)
        $pastRows = [];
        if ($batch->monitoringPasteurisasi && $batch->monitoringPasteurisasi->count() > 0) {
            foreach ($batch->monitoringPasteurisasi as $pIdx => $mp) {
                $disposisiFull = $mp->disposition ?: '';
                if ($mp->disposition_remark) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $mp->disposition_remark;
                }
                $mpScan = $mp->scanned_at ?: $mp->created_at;
                $pastRows[] = [
                    'sampling' => 'Tengah',
                    'jam' => $mpScan ? Carbon::parse($mpScan)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                    'pic' => $mp->user->name ?? '',
                    'bj' => $this->formatBlankIfZero($mp->bj),
                    'brix' => $this->formatBlankIfZero($mp->brix),
                    'ph' => $this->formatBlankIfZero($mp->ph),
                    'aw' => $this->formatBlankIfZero($mp->aw),
                    'viskositas' => $this->formatBlankIfZero($mp->visco),
                    'organo' => $mp->organo ?: '',
                    'aroma' => $mp->aroma ?? '',
                    'warna' => $mp->color ? $mp->color->name : '',
                    'buih' => $this->formatBlankIfZero($mp->buih),
                    'endapan' => $mp->endapan ?: '',
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
        }

        while (count($pastRows) < 6) {
            $pIndex = count($pastRows);
            $pastRows[] = [
                'sampling' => ($pIndex === 0) ? 'Tengah' : '',
                'jam' => '', 'pic' => '', 'bj' => '', 'brix' => '', 'ph' => '', 'aw' => '',
                'viskositas' => '', 'organo' => '', 'aroma' => '', 'warna' => '', 'buih' => '', 'endapan' => '', 'disposisi' => '',
            ];
        }

        $storageRows = [];
        if ($batch->monitoringStorageKimia && $batch->monitoringStorageKimia->count() > 0) {
            foreach ($batch->monitoringStorageKimia as $sIdx => $msk) {
                if ($msk->storage) $detectedSt = $msk->storage;
                $disposisiFull = $msk->disposition ?: '';
                if ($msk->disposition_remark) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $msk->disposition_remark;
                }
                $mskScan = $msk->scanned_at ?: $msk->created_at;
                $storageRows[] = [
                    'sampling' => $msk->stage ?: ($sIdx === 0 ? 'Awal' : ($sIdx === 1 ? 'Outlet' : 'Akhir')),
                    'jam' => $mskScan ? Carbon::parse($mskScan)->setTimezone('Asia/Jakarta')->format('H:i') : '',
                    'pic' => $msk->user->name ?? '',
                    'bj' => $this->formatBlankIfZero($msk->bj),
                    'brix' => $this->formatBlankIfZero($msk->brix),
                    'ph' => $this->formatBlankIfZero($msk->ph),
                    'nacl' => $this->formatBlankIfZero($msk->nacl),
                    'viskositas' => $this->formatBlankIfZero($msk->visco),
                    'organo' => $msk->organo ?: '',
                    'aroma' => $msk->aroma ?? '',
                    'warna' => $msk->color ? $msk->color->name : '',
                    'buih' => $this->formatBlankIfZero($msk->buih),
                    'endapan' => $msk->endapan ?: '',
                    'kristal' => $msk->kristal ?? '',
                    'aw' => $this->formatBlankIfZero($msk->aw),
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
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

        $singleBlock = [
            'block_index' => 0,
            'halaman' => '1 / 1',
            'batch' => $batch->batch_range ?: '',
            'kode_st' => $detectedSt,
            'jenis_produk' => $batch->variant ?: '',
            'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'jam_produksi_start' => $jamStart,
            'jam_produksi_end' => $jamEnd,
            'kode_shift_grup' => $detectedShift,
            'pasteurisasi_rows' => $pastRows,
            'storage_rows' => $storageRows,
            'catatan' => '',
            'pic_sampling_1' => '',
            'pic_sampling_2' => '',
            'pic_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'pic_checker' => '',
        ];

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'jenis_produk' => $batch->variant ?: '',
            'tanggal_produksi' => $singleBlock['tanggal_produksi'],
            'jam_produksi_start' => $jamStart,
            'jam_produksi_end' => $jamEnd,
            'kode_shift_grup' => $detectedShift,
            'batch' => $batch->batch_range ?: '',
            'batch_range' => $batch->batch_range ?: '',
            'kode_st' => $detectedSt,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => '1 / 1',
            'pasteurisasi_rows' => $pastRows,
            'storage_rows' => $storageRows,
            'catatan' => '',
            'pic_sampling_1' => '',
            'pic_sampling_2' => '',
            'pic_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'pic_checker' => '',
            'batch_blocks' => [$singleBlock],
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
}
