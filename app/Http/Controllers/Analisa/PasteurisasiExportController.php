<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\DocPasteurisasiStorageExport;
use App\Http\Controllers\Controller;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PasteurisasiExportController extends Controller
{
    /**
     * Tampilkan Halaman Dokumen Analisis Pasteurisasi & Storage (Export & Cetak) & response DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with([
                'monitoringTurunBlending.user',
                'monitoringPasteurisasi.user',
                'monitoringStorageKimia.user',
                'monitoringStorageMikro.user'
            ])
            ->where(function ($q) {
                $q->has('monitoringPasteurisasi')
                  ->orHas('monitoringStorageKimia')
                  ->orHas('monitoringTurunBlending')
                  ->orHas('monitoringStorageMikro');
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
                        $pastHas = $batch->monitoringPasteurisasi->count() > 0;
                        $storageHas = $batch->monitoringStorageKimia->count() > 0;
                        $pastComplete = $pastHas ? $batch->isMonitoringPasteurisasiComplete() : true;
                        $storageComplete = $storageHas ? $batch->isMonitoringStorageKimiaComplete() : true;
                        return ($pastHas || $storageHas) && $pastComplete && $storageComplete;
                    });
                } elseif ($request->status === 'progress') {
                    $batches = $batches->filter(function ($batch) {
                        $pastHas = $batch->monitoringPasteurisasi->count() > 0;
                        $storageHas = $batch->monitoringStorageKimia->count() > 0;
                        $pastComplete = $pastHas ? $batch->isMonitoringPasteurisasiComplete() : true;
                        $storageComplete = $storageHas ? $batch->isMonitoringStorageKimiaComplete() : true;
                        return !($pastComplete && $storageComplete);
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
                ->addColumn('monitoring_count', function ($data) {
                    $turunCount = $data->monitoringTurunBlending->count();
                    $pastCount = $data->monitoringPasteurisasi->count();
                    $storageKimiaCount = $data->monitoringStorageKimia->count();
                    $storageMikroCount = $data->monitoringStorageMikro->count();

                    $html = '<div class="d-flex flex-wrap gap-1">';
                    if ($turunCount > 0) {
                        $html .= '<span class="badge bg-primary-subtle text-primary fs-12">Turun: ' . $turunCount . '</span>';
                    }
                    if ($pastCount > 0) {
                        $html .= '<span class="badge bg-info-subtle text-info fs-12">Past: ' . $pastCount . '</span>';
                    }
                    if ($storageKimiaCount > 0) {
                        $html .= '<span class="badge bg-warning-subtle text-warning fs-12">ST Kimia: ' . $storageKimiaCount . '</span>';
                    }
                    if ($storageMikroCount > 0) {
                        $html .= '<span class="badge bg-success-subtle text-success fs-12">ST Mikro: ' . $storageMikroCount . '</span>';
                    }
                    if ($turunCount === 0 && $pastCount === 0 && $storageKimiaCount === 0 && $storageMikroCount === 0) {
                        $html .= '<span class="text-muted">-</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status', function ($data) {
                    $pastHas = $data->monitoringPasteurisasi->count() > 0;
                    $storageHas = $data->monitoringStorageKimia->count() > 0;
                    $pastComplete = $pastHas ? $data->isMonitoringPasteurisasiComplete() : true;
                    $storageComplete = $storageHas ? $data->isMonitoringStorageKimiaComplete() : true;
                    $isAllComplete = ($pastHas || $storageHas) && $pastComplete && $storageComplete;

                    $icon = $isAllComplete ? '✅' : '⌛';
                    $text = $isAllComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $exportPoUrl = route('doc-pasteurisasi-storage.index', ['po_id' => $data->id]);
                    $traceUrl = route('analisa.monitoring-turun-blending.export.trace', ['id' => $data->id]);

                    return '
                        <div class="d-flex gap-1">
                            <a href="' . $exportPoUrl . '" class="btn btn-sm btn-success" title="Buka Formulir Dokumen FRM/QLB/04/104/006-01">
                                <i class="ri-file-excel-2-line"></i> Export
                            </a>
                            <a href="' . $traceUrl . '" class="btn btn-sm btn-info" title="Trace Batching Pasteurisasi & Storage">
                                <i class="ri-route-line"></i> Trace
                            </a>
                        </div>
                    ';
                })
                ->rawColumns([
                    'po_number',
                    'monitoring_count',
                    'status',
                    'action',
                ])
                ->make(true);
        }

        return view('app.analisa.monitoring_turun_blending.export');
    }

    /**
     * Tampilkan Halaman Trace Batching Pasteurisasi & Storage PO
     */
    public function trace($id)
    {
        $productionBatch = ProductionBatch::with([
            'monitoringTurunBlending.user',
            'monitoringPasteurisasi.user',
            'monitoringPasteurisasi.color',
            'monitoringStorageKimia.user',
            'monitoringStorageKimia.color',
            'monitoringStorageMikro.user'
        ])->findOrFail($id);

        return view('app.analisa.monitoring_turun_blending.trace', compact('productionBatch'));
    }

    /**
     * Download Rekapitulasi Excel Berdasarkan Filter
     */
    public function exportExcel(Request $request)
    {
        $docController = new DocPasteurisasiStorageController();

        $query = ProductionBatch::where(function ($q) {
                $q->has('monitoringPasteurisasi')
                  ->orHas('monitoringStorageKimia')
                  ->orHas('monitoringTurunBlending')
                  ->orHas('monitoringStorageMikro');
            })
            ->orderBy('date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $batches = $query->get();
        $selectedBatch = $batches->first();

        if (!$selectedBatch) {
            return redirect()->back()->with('error', 'Tidak ada data PO yang sesuai filter.');
        }

        $docData = $docController->getDocumentData($selectedBatch->id);
        $cleanPo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $docData['po_number']);
        $fileName = 'HASIL_ANALISIS_PASTEURISASI_DAN_STORAGE_TANK_' . $cleanPo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DocPasteurisasiStorageExport($docData),
            $fileName
        );
    }

    /**
     * Download Excel Spesifik per Nomor PO Sesuai Dokumen FRM/QLB/04/104/006-01
     */
    public function exportPoExcel($id)
    {
        $docController = new DocPasteurisasiStorageController();
        $docData = $docController->getDocumentData($id);

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
}
