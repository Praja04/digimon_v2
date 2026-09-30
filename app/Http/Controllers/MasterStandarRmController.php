<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBahan;
use App\Models\MasterStandarRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterStandarRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterStandarRm::query()
                ->with('jenisBahan');

            if ($request->filled('filter_jenis_bahan')) {
                $data->where('id_jenis_bahan', $request->filter_jenis_bahan);
            }

            $data->orderBy('id_jenis_bahan')->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterStandarRm $item) {
                    return $item->jenisBahan?->nama ?? '-';
                })
                ->addColumn('standar_display', function (MasterStandarRm $item) {
                    $parts = [];
                    if ($item->min_standar !== null && $item->max_standar !== null) {
                        $parts[] = $item->min_standar . ' – ' . $item->max_standar . ($item->uom ? ' ' . e($item->uom) : '');
                    } elseif ($item->min_standar !== null) {
                        $parts[] = 'Min ' . $item->min_standar . ($item->uom ? ' ' . e($item->uom) : '');
                    } elseif ($item->max_standar !== null) {
                        $parts[] = 'Maks ' . $item->max_standar . ($item->uom ? ' ' . e($item->uom) : '');
                    }

                    if ($item->target_text) {
                        $parts[] = '<span class="text-muted small">(' . e($item->target_text) . ')</span>';
                    }

                    return !empty($parts) ? implode(' ', $parts) : '<span class="text-muted fst-italic">-</span>';
                })
                ->editColumn('status', function (MasterStandarRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterStandarRm $item) {
                    return '
                        <div class="d-flex gap-1">
                            <button
                                type="button"
                                class="btn btn-warning btn-sm btnEdit"
                                data-id="' . $item->id . '"
                                title="Edit"
                            >
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnDelete"
                                data-id="' . $item->id . '"
                                data-nama="' . e(($item->jenisBahan?->nama ?? '') . ' - ' . $item->parameter) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['standar_display', 'status', 'action'])
                ->make(true);
        }

        $jenisBahans = MasterJenisBahan::where('status', true)->orderBy('nama')->get();

        return view('master-data-rm.standar.index', compact('jenisBahans'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'id_jenis_bahan' => [
                'required',
                'integer',
                'exists:master_jenis_bahans,id',
            ],
            'parameter' => [
                'required',
                'string',
                'max:100',
            ],
            'min_standar' => [
                'nullable',
                'numeric',
            ],
            'max_standar' => [
                'nullable',
                'numeric',
            ],
            'target_text' => [
                'nullable',
                'string',
                'max:150',
            ],
            'uom' => [
                'nullable',
                'string',
                'max:20',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'id_jenis_bahan.required' => 'Jenis bahan wajib dipilih.',
            'id_jenis_bahan.exists'   => 'Jenis bahan tidak valid.',
            'parameter.required'      => 'Nama parameter wajib diisi.',
            'min_standar.numeric'     => 'Min standar harus berupa angka.',
            'max_standar.numeric'     => 'Max standar harus berupa angka.',
            'status.required'         => 'Status wajib dipilih.',
        ]);

        MasterStandarRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data standar mutu RM berhasil diperbarui.' : 'Data standar mutu RM berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterStandarRm::with('jenisBahan')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterStandarRm::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data standar mutu RM berhasil dihapus.',
        ]);
    }

    public function getActiveByJenis(Request $request): JsonResponse
    {
        $jenisName = $request->input('jenis');
        $jenisId = $request->input('id_jenis_bahan');

        $query = MasterStandarRm::where('status', true);

        if ($jenisId) {
            $query->where('id_jenis_bahan', $jenisId);
        } elseif ($jenisName) {
            $query->whereHas('jenisBahan', function ($q) use ($jenisName) {
                $q->where('nama', $jenisName);
            });
        }

        $standards = $query->get();

        $map = [];
        foreach ($standards as $s) {
            $key = strtoupper(trim($s->parameter));
            $map[$key] = [
                'parameter'   => $s->parameter,
                'min'         => $s->min_standar,
                'max'         => $s->max_standar,
                'target_text' => $s->target_text,
                'uom'         => $s->uom,
            ];
        }

        return response()->json([
            'status'    => true,
            'standards' => $map,
        ]);
    }
}
