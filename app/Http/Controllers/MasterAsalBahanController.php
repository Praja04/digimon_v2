<?php

namespace App\Http\Controllers;

use App\Models\MasterAsalBahan;
use App\Models\MasterJenisBahan;
use App\Models\MasterSupplierRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterAsalBahanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterAsalBahan::query()
                ->with(['jenisBahan', 'supplierRm'])
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterAsalBahan $item) {
                    return $item->jenisBahan?->nama ?? '-';
                })
                ->addColumn('supplier', function (MasterAsalBahan $item) {
                    return $item->supplierRm?->nama_supplier ?? '-';
                })
                ->editColumn('status', function (MasterAsalBahan $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterAsalBahan $item) {
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
                                data-nama="' . e($item->asal_bahan) . '"
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

        return view('master-data-rm.asal-bahan.index', compact('jenisBahans'));
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
            'supplier_rm_id' => [
                'required',
                'integer',
                'exists:master_supplier_rms,id',
            ],
            'asal_bahan' => [
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
            'supplier_rm_id.required' => 'Supplier / manufactur wajib dipilih.',
            'supplier_rm_id.exists'   => 'Supplier tidak valid.',
            'asal_bahan.required'     => 'Asal bahan wajib diisi.',
            'status.required'         => 'Status wajib dipilih.',
        ]);

        MasterAsalBahan::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data asal bahan berhasil diperbarui.' : 'Data asal bahan berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterAsalBahan::with('supplierRm')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterAsalBahan::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data asal bahan berhasil dihapus.',
        ]);
    }

    public function getAllActive(): JsonResponse
    {
        $asalBahans = MasterAsalBahan::where('status', true)
            ->select('asal_bahan')
            ->distinct()
            ->orderBy('asal_bahan')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $asalBahans,
        ]);
    }

    public function getByJenisBahan($jenisBahanId): JsonResponse
    {
        $jenisBahan = MasterJenisBahan::find($jenisBahanId);

        // 1. Coba exact match jenis_bahan_id
        $asalBahans = MasterAsalBahan::where('jenis_bahan_id', $jenisBahanId)
            ->where('status', true)
            ->select('asal_bahan')
            ->distinct()
            ->orderBy('asal_bahan')
            ->get();

        // 2. Jika kosong, cari berdasarkan nama mirip (misal 'GULA' -> 'GULA TEBU', 'GULA KELAPA')
        if ($asalBahans->isEmpty() && $jenisBahan) {
            $cleanName = trim(strtoupper($jenisBahan->nama));
            $relatedJenisIds = MasterJenisBahan::where('nama', 'LIKE', "%{$cleanName}%")
                ->orWhereRaw('? LIKE CONCAT("%", nama, "%")', [$cleanName])
                ->pluck('id');

            if ($relatedJenisIds->isNotEmpty()) {
                $asalBahans = MasterAsalBahan::whereIn('jenis_bahan_id', $relatedJenisIds)
                    ->where('status', true)
                    ->select('asal_bahan')
                    ->distinct()
                    ->orderBy('asal_bahan')
                    ->get();
            }
        }

        // 3. Fallback jika masih kosong: tampilkan seluruh asal bahan aktif agar pilihan tetap muncul
        if ($asalBahans->isEmpty()) {
            $asalBahans = MasterAsalBahan::where('status', true)
                ->select('asal_bahan')
                ->distinct()
                ->orderBy('asal_bahan')
                ->get();
        }

        return response()->json([
            'status' => true,
            'data'   => $asalBahans,
        ]);
    }

    public function getBySupplier($supplierId): JsonResponse
    {
        $asalBahans = MasterAsalBahan::where('supplier_rm_id', $supplierId)
            ->where('status', true)
            ->select('asal_bahan')
            ->distinct()
            ->orderBy('asal_bahan')
            ->get();

        if ($asalBahans->isEmpty()) {
            $asalBahans = MasterAsalBahan::where('status', true)
                ->select('asal_bahan')
                ->distinct()
                ->orderBy('asal_bahan')
                ->get();
        }

        return response()->json([
            'status' => true,
            'data'   => $asalBahans,
        ]);
    }
}
