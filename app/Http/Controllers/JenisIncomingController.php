<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JenisIncomingController extends Controller
{
    /**
     * Menampilkan halaman dan data Jenis Incoming.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisIncoming::query()
                ->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('status', function (JenisIncoming $item) {
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

                ->addColumn('action', function (JenisIncoming $item) {
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

        return view('jenis-incoming.index');
    }

    /**
     * Menyimpan data baru atau perubahan data.
     */
    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'kategori' => [
                'required',
                'string',
                'max:50',
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('jenis_incomings', 'nama')->ignore($id),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kategori.required' => 'Kategori wajib diisi.',
            'kategori.max' => 'Kategori maksimal 50 karakter.',

            'nama.required' => 'Nama jenis incoming wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nama.unique' => 'Nama jenis incoming sudah tersedia.',

            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status tidak valid.',
        ]);

        $jenisIncoming = JenisIncoming::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'kategori' => $validated['kategori'],
                'nama' => $validated['nama'],
                'status' => $validated['status'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $id
                ? 'Jenis Incoming berhasil diperbarui.'
                : 'Jenis Incoming berhasil ditambahkan.',
            'data' => $jenisIncoming,
        ]);
    }

    /**
     * Mengambil data untuk modal edit.
     */
    public function edit(JenisIncoming $jenisIncoming): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $jenisIncoming,
        ]);
    }

    /**
     * Menghapus data.
     */
    public function destroy(JenisIncoming $jenisIncoming): JsonResponse
    {
        if ($jenisIncoming->suppliers()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena sudah digunakan oleh Supplier.',
            ], 422);
        }

        $jenisIncoming->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis Incoming berhasil dihapus.',
        ]);
    }
}