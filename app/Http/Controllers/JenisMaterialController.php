<?php

namespace App\Http\Controllers;

use App\Models\JenisMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JenisMaterialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisMaterial::query()
                ->orderBy('id');

            $canDelete = $this->canDeleteMaster();

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('status', function (JenisMaterial $item) {
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

                ->addColumn('action', function (JenisMaterial $item) use ($canDelete) {
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

                ->rawColumns([
                    'status',
                    'action',
                ])

                ->make(true);
        }

        return view('jenis-material.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('jenis_materials', 'kode')->ignore($id),
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('jenis_materials', 'nama')->ignore($id),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.max' => 'Kode maksimal 50 karakter.',
            'kode.unique' => 'Kode sudah digunakan.',

            'nama.required' => 'Nama jenis material wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nama.unique' => 'Nama jenis material sudah digunakan.',

            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status tidak valid.',
        ]);

        $jenisMaterial = JenisMaterial::updateOrCreate(
            [
                'id' => $id,
            ],
            [
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
                ? 'Jenis Material berhasil diperbarui.'
                : 'Jenis Material berhasil ditambahkan.',
            'data' => $jenisMaterial,
        ]);
    }

    public function edit(
        JenisMaterial $jenisMaterial
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $jenisMaterial,
        ]);
    }

    public function destroy(
        JenisMaterial $jenisMaterial
    ): JsonResponse {
        if (! $this->canDeleteMaster()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya role Supervisor ke atas yang berhak menghapus data master.',
            ], 403);
        }

        try {
            if (\App\Models\PackagingIncoming::query()->where('jenis_material_id', $jenisMaterial->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat dihapus karena sudah digunakan pada data Incoming PM. Silakan ubah status menjadi "Tidak Aktif" jika sudah tidak digunakan.',
                ], 422);
            }

            $jenisMaterial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jenis Material berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Jenis Material: ' . $e->getMessage(),
            ], 500);
        }
    }
}