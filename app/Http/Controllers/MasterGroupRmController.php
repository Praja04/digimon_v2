<?php

namespace App\Http\Controllers;

use App\Models\MasterGroupRm;
use App\Models\MasterJenisBahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterGroupRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterGroupRm::query()
                ->with('jenisBahan');

            if ($request->filled('filter_jenis_bahan')) {
                $data->where('jenis_bahan_id', $request->filter_jenis_bahan);
            }

            if ($request->filled('filter_grup')) {
                $data->where('grup', $request->filter_grup);
            }

            if ($request->filled('filter_status')) {
                $data->where('status', $request->filter_status);
            }

            $data->orderBy('jenis_bahan_id')
                ->orderBy('grup')
                ->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterGroupRm $item) {
                    return $item->jenisBahan?->nama ?? '-';
                })
                ->addColumn('grup_badge', function (MasterGroupRm $item) {
                    return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12 fw-semibold">Group ' . e($item->grup) . '</span>';
                })
                ->addColumn('parameter_display', function (MasterGroupRm $item) {
                    return '<span class="fw-semibold text-dark fs-13">' . e($item->parameter) . '</span>';
                })
                ->addColumn('kategori_display', function (MasterGroupRm $item) {
                    return '<span class="text-body">' . e($item->kategori) . '</span>';
                })
                ->addColumn('target_badge', function (MasterGroupRm $item) {
                    return '<span class="badge bg-dark-subtle text-dark border px-2 py-1 fs-12 fw-semibold">' . e($item->target) . '</span>';
                })
                ->editColumn('status', function (MasterGroupRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterGroupRm $item) {
                    $canDelete = $this->canDeleteMaster();
                    $deleteBtn = '';
                    if ($canDelete) {
                        $deleteBtn = '
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnDelete"
                                data-id="' . $item->id . '"
                                data-nama="' . e($item->parameter . ' - ' . $item->kategori) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        ';
                    }

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
                            ' . $deleteBtn . '
                        </div>
                    ';
                })
                ->rawColumns(['grup_badge', 'parameter_display', 'kategori_display', 'target_badge', 'status', 'action'])
                ->make(true);
        }

        $jenisBahans = MasterJenisBahan::where('status', true)->orderBy('nama')->get();
        $grups = MasterGroupRm::select('grup')->distinct()->pluck('grup');

        return view('master-data-rm.group.index', compact('jenisBahans', 'grups'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'jenis_bahan_id' => [
                'required',
                'integer',
                'exists:master_jenis_bahans,id',
            ],
            'grup' => [
                'required',
                'string',
                'max:50',
            ],
            'parameter' => [
                'required',
                'string',
                'max:100',
            ],
            'kategori' => [
                'required',
                'string',
                'max:150',
            ],
            'target' => [
                'required',
                'string',
                'max:100',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'jenis_bahan_id.required' => 'Jenis bahan wajib dipilih.',
            'grup.required'           => 'Grup wajib diisi (misal: A, B, atau C).',
            'parameter.required'      => 'Parameter wajib diisi.',
            'kategori.required'       => 'Kategori / Kriteria wajib diisi.',
            'target.required'         => 'Target wajib diisi (misal: Max. 30%, Min. 70%).',
            'status.required'         => 'Status wajib dipilih.',
        ]);

        $item = MasterGroupRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $message = $id
            ? 'Master Group RM berhasil diperbarui.'
            : 'Master Group RM berhasil ditambahkan.';

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $item,
        ]);
    }

    public function edit($id): JsonResponse
    {
        $item = MasterGroupRm::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $item,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        if (! $this->canDeleteMaster()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki wewenang untuk menghapus master data ini.',
            ], 403);
        }

        $item = MasterGroupRm::findOrFail($id);
        $item->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Master Group RM berhasil dihapus.',
        ]);
    }

    public function getByJenisBahan($jenisBahanId): JsonResponse
    {
        $items = MasterGroupRm::where('jenis_bahan_id', $jenisBahanId)
            ->where('status', true)
            ->orderBy('grup')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $items,
        ]);
    }
}
