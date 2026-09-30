<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MasterJenisBahanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterJenisBahan::query()
                ->withCount(['suppliers', 'asalBahans'])
                ->orderBy('nama');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function (MasterJenisBahan $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterJenisBahan $item) {
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
                                data-nama="' . e($item->nama) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('master-data-rm.jenis-bahan.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('master_jenis_bahans', 'nama')->ignore($id),
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'nama.required' => 'Nama jenis bahan wajib diisi.',
            'nama.unique'   => 'Nama jenis bahan sudah digunakan.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        MasterJenisBahan::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data jenis bahan berhasil diperbarui.' : 'Data jenis bahan berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterJenisBahan::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterJenisBahan::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data jenis bahan berhasil dihapus.',
        ]);
    }
}
