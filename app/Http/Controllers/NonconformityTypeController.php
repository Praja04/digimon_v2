<?php

namespace App\Http\Controllers;

use App\Models\NonconformityType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class NonconformityTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = NonconformityType::query()
                ->orderBy('id');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('status', function (NonconformityType $item) {
                    if ($item->status) {
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

                ->addColumn('action', function (NonconformityType $item) {
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

                ->rawColumns([
                    'status',
                    'action',
                ])

                ->make(true);
        }

        return view('nonconformity-type.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'nonconformity_types',
                    'kode'
                )->ignore($id),
            ],

            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'nonconformity_types',
                    'nama'
                )->ignore($id),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kode.required' =>
                'Kode jenis ketidaksesuaian wajib diisi.',

            'kode.max' =>
                'Kode maksimal 50 karakter.',

            'kode.unique' =>
                'Kode jenis ketidaksesuaian sudah digunakan.',

            'nama.required' =>
                'Nama jenis ketidaksesuaian wajib diisi.',

            'nama.max' =>
                'Nama maksimal 150 karakter.',

            'nama.unique' =>
                'Nama jenis ketidaksesuaian sudah digunakan.',

            'status.required' =>
                'Status wajib dipilih.',

            'status.boolean' =>
                'Status tidak valid.',
        ]);

        $nonconformityType =
            NonconformityType::updateOrCreate(
                [
                    'id' => $id,
                ],
                [
                    'kode' => strtoupper(
                        str_replace(
                            ' ',
                            '_',
                            $validated['kode']
                        )
                    ),

                    'nama' => $validated['nama'],
                    'status' => $validated['status'],
                ]
            );

        return response()->json([
            'success' => true,

            'message' => $id
                ? 'Jenis Ketidaksesuaian berhasil diperbarui.'
                : 'Jenis Ketidaksesuaian berhasil ditambahkan.',

            'data' => $nonconformityType,
        ]);
    }

    public function edit(
        NonconformityType $nonconformityType
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $nonconformityType,
        ]);
    }

    public function destroy(
        NonconformityType $nonconformityType
    ): JsonResponse {
        $nonconformityType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis Ketidaksesuaian berhasil dihapus.',
        ]);
    }
}