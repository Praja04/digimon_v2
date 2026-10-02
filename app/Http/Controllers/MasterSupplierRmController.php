<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBahan;
use App\Models\MasterSupplierRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterSupplierRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterSupplierRm::query()
                ->with('jenisBahan')
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterSupplierRm $item) {
                    return $item->jenisBahan?->nama ?? '-';
                })
                ->editColumn('status', function (MasterSupplierRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterSupplierRm $item) {
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
                                data-nama="' . e($item->nama_supplier) . '"
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

        $jenisBahans = MasterJenisBahan::where('status', true)->orderBy('nama')->get();

        return view('master-data-rm.supplier.index', compact('jenisBahans'));
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
            'nama_supplier' => [
                'required',
                'string',
                'max:150',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'jenis_bahan_id.required' => 'Jenis bahan wajib dipilih.',
            'jenis_bahan_id.exists'   => 'Jenis bahan tidak valid.',
            'nama_supplier.required'  => 'Nama supplier / manufactur wajib diisi.',
            'status.required'         => 'Status wajib dipilih.',
        ]);

        MasterSupplierRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data supplier RM berhasil diperbarui.' : 'Data supplier RM berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterSupplierRm::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterSupplierRm::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data supplier RM berhasil dihapus.',
        ]);
    }

    public function getByJenisBahan($jenisBahanId): JsonResponse
    {
        $jenisBahan = MasterJenisBahan::find($jenisBahanId);

        // 1. Coba exact match jenis_bahan_id
        $suppliers = MasterSupplierRm::where('jenis_bahan_id', $jenisBahanId)
            ->where('status', true)
            ->orderBy('nama_supplier')
            ->get(['id', 'nama_supplier']);

        // 2. Jika tidak ada yang exact, cari berdasarkan kemiripan nama bahan (misal 'GULA' -> 'GULA TEBU', 'GULA KELAPA')
        if ($suppliers->isEmpty() && $jenisBahan) {
            $cleanName = trim(strtoupper($jenisBahan->nama));
            $relatedJenisIds = MasterJenisBahan::where('nama', 'LIKE', "%{$cleanName}%")
                ->orWhereRaw('? LIKE CONCAT("%", nama, "%")', [$cleanName])
                ->pluck('id');

            if ($relatedJenisIds->isNotEmpty()) {
                $suppliers = MasterSupplierRm::whereIn('jenis_bahan_id', $relatedJenisIds)
                    ->where('status', true)
                    ->orderBy('nama_supplier')
                    ->get(['id', 'nama_supplier']);
            }
        }

        // 3. Jika masih kosong (misal jenis bahan baru), tampilkan seluruh supplier aktif agar pilihan tetap muncul
        if ($suppliers->isEmpty()) {
            $suppliers = MasterSupplierRm::where('status', true)
                ->orderBy('nama_supplier')
                ->get(['id', 'nama_supplier']);
        }

        return response()->json([
            'status' => true,
            'data'   => $suppliers,
        ]);
    }

    public function getAllActive(): JsonResponse
    {
        $suppliers = MasterSupplierRm::where('status', true)
            ->orderBy('nama_supplier')
            ->get(['id', 'nama_supplier']);

        return response()->json([
            'status' => true,
            'data'   => $suppliers,
        ]);
    }
}
