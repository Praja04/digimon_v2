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
                    $canDelete = $this->canDeleteMaster();
                    $deleteBtn = '';
                    if ($canDelete) {
                        $deleteBtn = '
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnDelete"
                                data-id="' . $item->id . '"
                                data-nama="' . e($item->nama) . '"
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
        if (! $this->canDeleteMaster()) {
            return response()->json([
                'status'  => false,
                'message' => 'Hanya role Supervisor ke atas yang berhak menghapus data master.',
            ], 403);
        }

        try {
            $data = MasterJenisBahan::findOrFail($id);

            $supplierIds = \App\Models\MasterSupplierRm::where('jenis_bahan_id', $id)->pluck('id');
            $asalBahanIds = \App\Models\MasterAsalBahan::where('jenis_bahan_id', $id)->pluck('id');

            $isUsedInTransaksi = \App\Models\IdentitasRM::where('jenis_bahan_id', $id)
                ->orWhereIn('supplier_id', $supplierIds)
                ->orWhereIn('asal_bahan_id', $asalBahanIds)
                ->exists();

            if ($isUsedInTransaksi) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak dapat dihapus karena sudah digunakan dalam transaksi RM. Silakan ubah status menjadi "Tidak Aktif" jika tidak ingin digunakan.',
                ], 422);
            }

            // Hapus relasi yang belum pernah terpakai di transaksi (misal typo)
            \App\Models\MasterStandarRm::where('id_jenis_bahan', $id)->delete();
            \App\Models\MasterAsalBahan::where('jenis_bahan_id', $id)->delete();
            \App\Models\MasterSupplierRm::where('jenis_bahan_id', $id)->delete();
            \App\Models\MasterParameterRm::where('jenis_bahan_id', $id)->delete();
            $data->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data jenis bahan berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
