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
     * Menampilkan halaman dan data AJAX DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JenisIncoming::query()
                ->select([
                    'id',
                    'kategori',
                    'nama',
                    'status',
                    'created_at',
                    'updated_at',
                ])
                ->orderBy('id', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('status', function (JenisIncoming $item) {
                    if ((bool) $item->status) {
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
                    $nama = htmlspecialchars(
                        $item->nama,
                        ENT_QUOTES,
                        'UTF-8'
                    );

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
                                data-nama="' . $nama . '"
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
     * Menambahkan atau memperbarui data.
     */
    public function store(Request $request): JsonResponse
    {
        $id = $request->filled('id')
            ? (int) $request->input('id')
            : null;

        $request->merge([
            'kategori' => 'PM',
            'nama' => trim((string) $request->input('nama')),
        ]);

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
                Rule::unique('jenis_incomings', 'nama')
                    ->ignore($id),
            ],

            'status' => [
                'required',
                Rule::in(['0', '1', 0, 1]),
            ],
        ], [
            'kategori.required' =>
                'Kategori wajib diisi.',

            'nama.required' =>
                'Nama jenis incoming wajib diisi.',

            'nama.max' =>
                'Nama jenis incoming maksimal 100 karakter.',

            'nama.unique' =>
                'Nama jenis incoming sudah tersedia.',

            'status.required' =>
                'Status wajib dipilih.',

            'status.in' =>
                'Status yang dipilih tidak valid.',
        ]);

        $payload = [
            'kategori' => 'PM',
            'nama' => $validated['nama'],
            'status' => (int) $validated['status'],
        ];

        if ($id !== null) {
            $jenisIncoming = JenisIncoming::findOrFail($id);
            $jenisIncoming->update($payload);

            $message = 'Jenis Incoming berhasil diperbarui.';
        } else {
            $jenisIncoming = JenisIncoming::create($payload);

            $message = 'Jenis Incoming berhasil ditambahkan.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $jenisIncoming,
        ]);
    }

    /**
     * Mengambil satu data untuk form edit.
     */
    public function edit(
        JenisIncoming $jenisIncoming
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $jenisIncoming,
        ]);
    }

    /**
     * Menghapus data yang belum dipakai Supplier.
     */
    public function destroy(
        JenisIncoming $jenisIncoming
    ): JsonResponse {
        if ($jenisIncoming->suppliers()->exists()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Data tidak dapat dihapus karena sudah digunakan oleh Supplier.',
            ], 422);
        }

        $jenisIncoming->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Jenis Incoming berhasil dihapus.',
        ]);
    }
}