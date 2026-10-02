<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\PelarutanExport;
use App\Http\Controllers\Controller;
use App\Models\ProductionBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PelarutanExportController extends Controller
{
    /**
     * Tampilkan halaman Export Data Pelarutan & response DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with(['pelarutan_1', 'pelarutan_2'])
                ->where(function ($q) {
                    $q->has('pelarutan_1')
                      ->orHas('pelarutan_2');
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

            // Filter Kategori / Jenis Pelarutan
            if ($request->filled('pelarutan_type') && $request->pelarutan_type !== 'all') {
                if ($request->pelarutan_type === 'pelarutan_1') {
                    $query->has('pelarutan_1');
                } elseif ($request->pelarutan_type === 'pelarutan_2') {
                    $query->has('pelarutan_2');
                }
            }

            $batches = $query->get();

            // Filter Status (Complete / Progress)
            if ($request->filled('status')) {
                if ($request->status === 'complete') {
                    $batches = $batches->filter(function ($batch) {
                        $p1Complete = $batch->pelarutan_1->count() > 0 ? $batch->isPelarutan1Complete() : true;
                        $p2Complete = $batch->pelarutan_2->count() > 0 ? $batch->isPelarutan2Complete() : true;
                        return ($batch->pelarutan_1->count() > 0 || $batch->pelarutan_2->count() > 0) && $p1Complete && $p2Complete;
                    });
                } elseif ($request->status === 'progress') {
                    $batches = $batches->filter(function ($batch) {
                        $p1Complete = $batch->pelarutan_1->count() > 0 ? $batch->isPelarutan1Complete() : true;
                        $p2Complete = $batch->pelarutan_2->count() > 0 ? $batch->isPelarutan2Complete() : true;
                        return !($p1Complete && $p2Complete);
                    });
                }
            }

            return DataTables::of($batches)
                ->addIndexColumn()
                ->addColumn('po_number', function ($data) {
                    return '<span class="fw-semibold text-primary">' . e($data->po_number) . '</span>';
                })
                ->addColumn('date', function ($data) {
                    return $data->date ? Carbon::parse($data->date)->format('Y-m-d') : '-';
                })
                ->addColumn('pelarutan_1_count', function ($data) {
                    $count = $data->pelarutan_1->count();
                    if ($count > 0) {
                        return '<span class="badge bg-primary-subtle text-primary fs-12">' . $count . ' Batch</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('pelarutan_2_count', function ($data) {
                    $count = $data->pelarutan_2->count();
                    if ($count > 0) {
                        return '<span class="badge bg-success-subtle text-success fs-12">' . $count . ' Batch</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('status', function ($data) {
                    $p1Has = $data->pelarutan_1->count() > 0;
                    $p2Has = $data->pelarutan_2->count() > 0;
                    $p1Complete = $p1Has ? $data->isPelarutan1Complete() : true;
                    $p2Complete = $p2Has ? $data->isPelarutan2Complete() : true;
                    $isAllComplete = ($p1Has || $p2Has) && $p1Complete && $p2Complete;

                    $icon = $isAllComplete ? '✅' : '⌛';
                    $text = $isAllComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $docPelarutanUrl = route('doc-pelarutan.index', ['po_id' => $data->id]);
                    $traceUrl = route('pelarutan.export.trace', ['id' => $data->id]);

                    return '
                        <div class="d-flex gap-1">
                            <a href="' . $docPelarutanUrl . '" class="btn btn-sm btn-success" title="Buka Formulir Dokumen HASIL ANALISIS PROSES PELARUTAN">
                                <i class="ri-file-excel-2-line"></i> Export
                            </a>
                            <a href="' . $traceUrl . '" class="btn btn-sm btn-info" title="Trace Batching">
                                <i class="ri-route-line"></i> Trace
                            </a>
                        </div>
                    ';
                })
                ->rawColumns([
                    'po_number',
                    'pelarutan_1_count',
                    'pelarutan_2_count',
                    'status',
                    'action'
                ])
                ->make(true);
        }

        return view('app.pelarutan_1.export');
    }

    /**
     * Tampilkan Halaman Trace Batching PO
     */
    public function trace($id)
    {
        $productionBatch = ProductionBatch::with([
            'pelarutan_1.user',
            'pelarutan_2.user'
        ])->findOrFail($id);

        return view('app.pelarutan_1.trace', compact('productionBatch'));
    }

    /**
     * Download Rekapitulasi Excel Berdasarkan Filter
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pelarutanType = $request->input('pelarutan_type', 'all');
        $status = $request->input('status');

        $fileName = 'Export_Pelarutan_' .
            ($pelarutanType === 'all' ? 'All' : ($pelarutanType === 'pelarutan_1' ? 'P1' : 'P2')) . '_' .
            ($startDate ? $startDate : 'Awal') . '_sd_' .
            ($endDate ? $endDate : 'Akhir') . '_' .
            now()->timestamp . '.xlsx';

        return Excel::download(
            new PelarutanExport(
                $startDate,
                $endDate,
                $pelarutanType,
                $status
            ),
            $fileName
        );
    }

    /**
     * Download Excel Spesifik per Nomor PO
     */
    public function exportPoExcel($id)
    {
        $batch = ProductionBatch::findOrFail($id);
        $fileName = 'Export_Pelarutan_PO_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $batch->po_number) . '_' . now()->timestamp . '.xlsx';

        return Excel::download(
            new PelarutanExport(
                null,
                null,
                'all',
                null,
                $id
            ),
            $fileName
        );
    }
}
