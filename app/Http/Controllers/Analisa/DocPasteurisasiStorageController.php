<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPasteurisasiStorageExport;
use App\Http\Controllers\Controller;
use App\Models\DocPasteurisasiStorage;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        if ($savedDoc) {
            return [
                'is_saved' => true,
                'id' => $savedDoc->id,
                'production_batch_id' => $savedDoc->production_batch_id,
                'po_number' => $batch->po_number,
                'variant' => $savedDoc->jenis_produk ?: ($batch->variant ?: ''),
                'jenis_produk' => $savedDoc->jenis_produk ?: ($batch->variant ?: ''),
                'tanggal_produksi' => $savedDoc->tanggal_produksi ? $savedDoc->tanggal_produksi->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : ''),
                'jam_produksi_start' => $savedDoc->jam_produksi_start ?: '',
                'jam_produksi_end' => $savedDoc->jam_produksi_end ?: '',
                'tanggal_record_doc' => $savedDoc->tanggal_record_doc ? $savedDoc->tanggal_record_doc->format('Y-m-d') : ($batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d')),
                'halaman' => $savedDoc->halaman ?: '1',
                'kode_shift_grup' => $savedDoc->kode_shift_grup ?: '',
                'batch' => $savedDoc->batch ?: ($savedDoc->batch_range ?: ($batch->batch_range ?: '')),
                'batch_range' => $savedDoc->batch ?: ($savedDoc->batch_range ?: ($batch->batch_range ?: '')),
                'kode_st' => $savedDoc->kode_st ?: 'ST 01',
                'pasteurisasi_rows' => $savedDoc->pasteurisasi_rows ?: [],
                'storage_rows' => $savedDoc->storage_rows ?: [],
                'catatan' => $savedDoc->catatan ?: '',
                'pic_sampling' => $savedDoc->pic_sampling ?: '',
                'pic_serah_terima' => $savedDoc->pic_serah_terima ?: '',
                'pic_analis' => $savedDoc->pic_analis ?: '',
                'pic_checker' => $savedDoc->pic_checker ?: '',
                'doc_code' => $savedDoc->doc_code ?: 'FRM/QLB/04/104/006-01',
            ];
        }

        // Auto-generate dari data monitoring yang ada di database
        $pasteurisasiRows = [];
        $detectedShift = '';
        $detectedAnalis = '';
        $jamStart = '';
        $jamEnd = '';

        if ($batch->monitoringPasteurisasi && $batch->monitoringPasteurisasi->count() > 0) {
            foreach ($batch->monitoringPasteurisasi as $pIdx => $mp) {
                if (!$detectedShift && $mp->shift) {
                    $detectedShift = 'Shift ' . $mp->shift;
                }
                if (!$detectedAnalis && $mp->user) {
                    $detectedAnalis = $mp->user->name;
                }

                $jamStr = $mp->created_at ? $mp->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
                if (!$jamStart && $jamStr) $jamStart = $jamStr;
                if ($jamStr) $jamEnd = $jamStr;

                $disposisiFull = $mp->disposition ?: '';
                if ($mp->disposition_remark) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $mp->disposition_remark;
                }

                $samplingLabel = ($batch->monitoringPasteurisasi->count() === 1) ? 'Tengah' : ('Tengah ' . ($pIdx + 1));

                $pasteurisasiRows[] = [
                    'sampling' => $samplingLabel,
                    'jam' => $jamStr,
                    'pic' => $mp->user->name ?? '',
                    'bj' => $mp->bj !== null ? (string)$mp->bj : '',
                    'brix' => $mp->brix !== null ? (string)$mp->brix : '',
                    'ph' => $mp->ph !== null ? (string)$mp->ph : '',
                    'aw' => $mp->aw !== null ? (string)$mp->aw : '',
                    'viskositas' => $mp->visco !== null ? (string)$mp->visco : '',
                    'organo' => $mp->organo ?: '',
                    'aroma' => $mp->aroma ?? '',
                    'warna' => $mp->color ? $mp->color->name : '',
                    'buih' => $mp->buih !== null ? (string)$mp->buih : '',
                    'endapan' => $mp->endapan ?: '',
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
        }

        $storageRows = [];
        $detectedSt = 'ST 01';
        $storageDefaultStages = ['Awal', 'Tengah', 'Akhir', 'Outlet', 'Inlet'];

        if ($batch->monitoringStorageKimia && $batch->monitoringStorageKimia->count() > 0) {
            foreach ($batch->monitoringStorageKimia as $sIdx => $msk) {
                if ($msk->storage) {
                    $detectedSt = $msk->storage;
                }
                if (!$detectedAnalis && $msk->user) {
                    $detectedAnalis = $msk->user->name;
                }

                $disposisiFull = $msk->disposition ?: '';
                if ($msk->disposition_remark) {
                    $disposisiFull .= ($disposisiFull ? ' - ' : '') . $msk->disposition_remark;
                }

                $stLabel = $storageDefaultStages[$sIdx] ?? ('Sampling ' . ($sIdx + 1));

                $storageRows[] = [
                    'sampling' => $stLabel,
                    'jam' => $msk->created_at ? $msk->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '',
                    'pic' => $msk->user->name ?? '',
                    'bj' => $msk->bj !== null ? (string)$msk->bj : '',
                    'brix' => $msk->brix !== null ? (string)$msk->brix : '',
                    'ph' => $msk->ph !== null ? (string)$msk->ph : '',
                    'nacl' => $msk->nacl !== null ? (string)$msk->nacl : '',
                    'viskositas' => $msk->visco !== null ? (string)$msk->visco : '',
                    'organo' => $msk->organo ?: '',
                    'aroma' => $msk->aroma ?? '',
                    'warna' => $msk->color ? $msk->color->name : '',
                    'buih' => $msk->buih !== null ? (string)$msk->buih : '',
                    'endapan' => $msk->endapan ?: '',
                    'kristal' => $msk->kristal ?? '',
                    'aw' => $msk->aw !== null ? (string)$msk->aw : '',
                    'disposisi' => $disposisiFull ?: '',
                ];
            }
        }

        // Jika baris masih kosong, buat minimal 1 baris kosong sebagai template
        if (empty($pasteurisasiRows)) {
            $pasteurisasiRows[] = [
                'sampling' => 'Tengah',
                'jam' => '',
                'pic' => '',
                'bj' => '',
                'brix' => '',
                'ph' => '',
                'aw' => '',
                'viskositas' => '',
                'organo' => '',
                'aroma' => '',
                'warna' => '',
                'buih' => '',
                'endapan' => '',
                'disposisi' => '',
            ];
        }

        if (empty($storageRows)) {
            $storageRows[] = [
                'sampling' => 'Awal',
                'jam' => '',
                'pic' => '',
                'bj' => '',
                'brix' => '',
                'ph' => '',
                'nacl' => '',
                'viskositas' => '',
                'organo' => '',
                'aroma' => '',
                'warna' => '',
                'buih' => '',
                'endapan' => '',
                'kristal' => '',
                'aw' => '',
                'disposisi' => '',
            ];
        }

        return [
            'is_saved' => false,
            'id' => null,
            'production_batch_id' => $batch->id,
            'po_number' => $batch->po_number,
            'variant' => $batch->variant,
            'jenis_produk' => $batch->variant ?: '',
            'tanggal_produksi' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'jam_produksi_start' => $jamStart,
            'jam_produksi_end' => $jamEnd,
            'tanggal_record_doc' => $batch->date ? Carbon::parse($batch->date)->format('Y-m-d') : date('Y-m-d'),
            'halaman' => '1',
            'kode_shift_grup' => $detectedShift,
            'batch' => $batch->batch_range ?: '',
            'batch_range' => $batch->batch_range ?: '',
            'kode_st' => $detectedSt,
            'pasteurisasi_rows' => $pasteurisasiRows,
            'storage_rows' => $storageRows,
            'catatan' => '',
            'pic_sampling' => '',
            'pic_serah_terima' => '',
            'pic_analis' => $detectedAnalis ?: (auth()->user()->name ?? ''),
            'pic_checker' => '',
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

        $pasteurisasiRows = $request->input('pasteurisasi_rows', []);
        $storageRows = $request->input('storage_rows', []);

        // Filter baris kosong jika ada
        if (is_array($pasteurisasiRows)) {
            $pasteurisasiRows = array_values(array_filter($pasteurisasiRows, function ($row) {
                return !empty(array_filter($row));
            }));
        }

        if (is_array($storageRows)) {
            $storageRows = array_values(array_filter($storageRows, function ($row) {
                return !empty(array_filter($row));
            }));
        }

        $doc = DocPasteurisasiStorage::updateOrCreate(
            ['production_batch_id' => $request->input('production_batch_id')],
            [
                'jenis_produk' => $request->input('jenis_produk'),
                'tanggal_produksi' => $request->input('tanggal_produksi'),
                'jam_produksi_start' => $request->input('jam_produksi_start'),
                'jam_produksi_end' => $request->input('jam_produksi_end'),
                'tanggal_record_doc' => $request->input('tanggal_record_doc'),
                'halaman' => $request->input('halaman', '1'),
                'kode_shift_grup' => $request->input('kode_shift_grup'),
                'batch' => $request->input('batch') ?: $request->input('batch_range'),
                'batch_range' => $request->input('batch') ?: $request->input('batch_range'),
                'kode_st' => $request->input('kode_st'),
                'pasteurisasi_rows' => $pasteurisasiRows,
                'storage_rows' => $storageRows,
                'catatan' => $request->input('catatan'),
                'pic_sampling' => $request->input('pic_sampling'),
                'pic_serah_terima' => $request->input('pic_serah_terima'),
                'pic_analis' => $request->input('pic_analis'),
                'pic_checker' => $request->input('pic_checker'),
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
}
