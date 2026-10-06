<?php

namespace App\Http\Controllers;

use App\Models\MasterParameterRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterAromaRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterParameterRm::query()
                ->where('kategori', 'aroma')
                ->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_pilihan', function (MasterParameterRm $item) {
                    return '<span class="fw-medium text-dark">' . e($item->nama_pilihan) . '</span>';
                })
                ->editColumn('status', function (MasterParameterRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterParameterRm $item) {
                    return '
                        <div class="d-flex justify-content-center gap-1">
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
                                data-nama="' . e($item->nama_pilihan) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['nama_pilihan', 'status', 'action'])
                ->make(true);
        }

        return view('master-data-rm.aroma.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'nama_pilihan' => [
                'required',
                'string',
                'max:150',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'nama_pilihan.required' => 'Nama pilihan aroma wajib diisi.',
            'status.required'       => 'Status wajib dipilih.',
        ]);

        $validated['kategori'] = 'aroma';
        $validated['jenis_bahan_id'] = null;
        $validated['is_custom'] = false;
        $validated['urutan'] = 0;

        MasterParameterRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data aroma berhasil diperbarui.' : 'Data aroma berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterParameterRm::where('kategori', 'aroma')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterParameterRm::where('kategori', 'aroma')->findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data aroma berhasil dihapus.',
        ]);
    }
}
