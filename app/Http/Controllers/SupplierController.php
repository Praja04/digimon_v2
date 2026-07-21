<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::query()
                ->with('jenisIncoming')
                ->orderBy('id');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('jenis_incoming', function (Supplier $supplier) {
                    return $supplier->jenisIncoming?->nama ?? '-';
                })

                ->editColumn('status', function (Supplier $supplier) {
                    if ($supplier->status) {
                        return '
                            <span class="badge bg-success">
                                <i class="mdi mdi-check-circle-outline me-1"></i>
                                Aktif
                            </span>
                        ';
                    }

                    return '
                        <span class="badge bg-secondary">
                            <i class="mdi mdi-close-circle-outline me-1"></i>
                            Tidak Aktif
                        </span>
                    ';
                })

                ->addColumn('action', function (Supplier $supplier) {
                    return '
                        <div class="d-flex gap-1">
                            <button
                                type="button"
                                class="btn btn-warning btn-sm btnEdit"
                                data-id="' . $supplier->id . '"
                                title="Edit"
                            >
                                <i class="mdi mdi-pencil"></i>
                            </button>

                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnDelete"
                                data-id="' . $supplier->id . '"
                                data-nama="' . e($supplier->nama) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns([
                    'status',
                    'action',
                ])

                ->make(true);
        }

        $jenisIncomings = JenisIncoming::query()
            ->where('status', true)
            ->orderBy('nama')
            ->get();

        return view('supplier.index', compact('jenisIncomings'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'jenis_incoming_id' => [
                'required',
                'integer',
                'exists:jenis_incomings,id',
            ],

            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'kode')->ignore($id),
            ],

            'nama' => [
                'required',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'jenis_incoming_id.required' =>
                'Jenis Incoming wajib dipilih.',

            'jenis_incoming_id.exists' =>
                'Jenis Incoming tidak ditemukan.',

            'kode.required' =>
                'Kode supplier wajib diisi.',

            'kode.unique' =>
                'Kode supplier sudah digunakan.',

            'nama.required' =>
                'Nama supplier wajib diisi.',

            'status.required' =>
                'Status wajib dipilih.',
        ]);

        $supplier = Supplier::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'jenis_incoming_id' =>
                    $validated['jenis_incoming_id'],

                'kode' => strtoupper(
                    str_replace(' ', '_', $validated['kode'])
                ),

                'nama' => $validated['nama'],
                'status' => $validated['status'],
            ]
        );

        return response()->json([
            'success' => true,

            'message' => $id
                ? 'Supplier berhasil diperbarui.'
                : 'Supplier berhasil ditambahkan.',

            'data' => $supplier,
        ]);
    }

    public function edit(Supplier $supplier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $supplier,
        ]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier berhasil dihapus.',
        ]);
    }
}