<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\BlendingExport;
use App\Exports\DocBlendingAwalExport;
use App\Http\Controllers\Controller;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class BlendingExportController extends Controller
{
    /**
     * Tampilkan Halaman Dokumen Analisis Blending (Export & Cetak) & response DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with(['BlendingAwal.user', 'BlendingAwal.color', 'blendingAfterAdjustMikro'])
                ->where(function ($q) {
                    $q->has('BlendingAwal')
                      ->orHas('blendingAfterAdjustMikro');
                })
                ->orderBy('date', 'desc');

            // Filter Tanggal Mulai
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            // Filter Tanggal Akhir
            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $batches = $query->get();

            // Filter Status (Complete / Progress)
            if ($request->filled('status')) {
                if ($request->status === 'complete') {
                    $batches = $batches->filter(function ($batch) {
                        $kimiaHas = $batch->BlendingAwal->count() > 0;
                        $mikroHas = $batch->blendingAfterAdjustMikro->count() > 0;
                        $kimiaComplete = $kimiaHas ? $batch->isBlendingAwalComplete() : true;
                        $mikroComplete = $mikroHas ? $batch->isBlendingAwalMikroComplete() : true;
                        return ($kimiaHas || $mikroHas) && $kimiaComplete && $mikroComplete;
                    });
                } elseif ($request->status === 'progress') {
                    $batches = $batches->filter(function ($batch) {
                        $kimiaHas = $batch->BlendingAwal->count() > 0;
                        $mikroHas = $batch->blendingAfterAdjustMikro->count() > 0;
                        $kimiaComplete = $kimiaHas ? $batch->isBlendingAwalComplete() : true;
                        $mikroComplete = $mikroHas ? $batch->isBlendingAwalMikroComplete() : true;
                        return !($kimiaComplete && $mikroComplete);
                    });
                }
            }

            $batches = $batches->values();

            return DataTables::of($batches)
                ->addIndexColumn()
                ->addColumn('po_number', function ($data) {
                    return '<span class="fw-semibold text-primary">' . e($data->po_number) . '</span>';
                })
                ->addColumn('date', function ($data) {
                    return $data->date ? Carbon::parse($data->date)->format('Y-m-d') : '-';
                })
                ->addColumn('blending_count', function ($data) {
                    $kimiaCount = $data->BlendingAwal->count();
                    $mikroCount = $data->blendingAfterAdjustMikro->count();
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    if ($kimiaCount > 0) {
                        $html .= '<span class="badge bg-primary-subtle text-primary fs-12">Kimia: ' . $kimiaCount . '</span>';
                    }
                    if ($mikroCount > 0) {
                        $html .= '<span class="badge bg-success-subtle text-success fs-12">Mikro: ' . $mikroCount . '</span>';
                    }
                    if ($kimiaCount === 0 && $mikroCount === 0) {
                        $html .= '<span class="text-muted">-</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status', function ($data) {
                    $kimiaHas = $data->BlendingAwal->count() > 0;
                    $mikroHas = $data->blendingAfterAdjustMikro->count() > 0;
                    $kimiaComplete = $kimiaHas ? $data->isBlendingAwalComplete() : true;
                    $mikroComplete = $mikroHas ? $data->isBlendingAwalMikroComplete() : true;
                    $isAllComplete = ($kimiaHas || $mikroHas) && $kimiaComplete && $mikroComplete;

                    $icon = $isAllComplete ? '✅' : '⌛';
                    $text = $isAllComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $exportBlendingUrl = route('doc-blending-awal.index', ['po_id' => $data->id]);
                    $exportAdjustPoUrl = route('doc-adjustment.index', ['po_id' => $data->id]);
                    $traceUrl = route('analisa.blending-awal.export.trace', ['id' => $data->id]);

                    return '
                        <div class="d-flex flex-wrap gap-1 text-nowrap">
                            <a href="' . $exportBlendingUrl . '" class="btn btn-sm btn-success" title="Export Blending">
                                <i class="ri-file-excel-2-line"></i> Export Blending
                            </a>
                            <a href="' . $exportAdjustPoUrl . '" class="btn btn-sm btn-warning text-white" title="Export Adjust Blending">
                                <i class="ri-file-excel-line"></i> Export Adjust Blending
                            </a>
                            <a href="' . $traceUrl . '" class="btn btn-sm btn-info" title="Trace">
                                <i class="ri-route-line"></i> Trace
                            </a>
                        </div>
                    ';
                })
                ->rawColumns([
                    'po_number',
                    'blending_count',
                    'status',
                    'action',
                ])
                ->make(true);
        }

        return view('app.analisa.blending_awal.export');
    }

    /**
     * Tampilkan Halaman Trace Batching Blending PO
     */
    public function trace($id)
    {
        $productionBatch = ProductionBatch::with([
            'BlendingAwal.user',
            'BlendingAwal.color',
            'blendingAfterAdjustMikro'
        ])->findOrFail($id);

        return view('app.analisa.blending_awal.trace', compact('productionBatch'));
    }

    /**
     * Download Rekapitulasi Excel Blending (Awal / Kimia) Berdasarkan Filter
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $fileName = 'REKAP_ANALISIS_BLENDING_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new BlendingExport($startDate, $endDate, 'kimia', $status),
            $fileName
        );
    }

    /**
     * Download Rekapitulasi Excel Adjust Blending (Mikro / Adjustment) Berdasarkan Filter
     */
    public function exportAdjustExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $fileName = 'REKAP_ANALISIS_ADJUST_BLENDING_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new BlendingExport($startDate, $endDate, 'mikro', $status),
            $fileName
        );
    }

    /**
     * Download Excel Adjust Blending Spesifik per Nomor PO
     */
    public function exportAdjustPoExcel($id)
    {
        $batch = ProductionBatch::findOrFail($id);
        $cleanPo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $batch->po_number);
        $fileName = 'HASIL_ANALISIS_ADJUST_BLENDING_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new BlendingExport(null, null, 'mikro', null, $batch->id),
            $fileName
        );
    }

    /**
     * Download Excel Spesifik per Nomor PO Sesuai Dokumen FRM/QLB/04/104/005-01
     */
    public function exportPoExcel($id)
    {
        $docController = new DocBlendingAwalController();
        $docData = $docController->getDocumentData($id);

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
}
