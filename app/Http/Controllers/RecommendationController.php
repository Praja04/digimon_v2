<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Recommendation::query()
                ->orderBy('id');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('status', function (Recommendation $item) {
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

                ->addColumn('action', function (Recommendation $item) {
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

        return view('recommendation.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('recommendations', 'kode')->ignore($id),
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('recommendations', 'nama')->ignore($id),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kode.required' => 'Kode rekomendasi wajib diisi.',
            'kode.max' => 'Kode maksimal 50 karakter.',
            'kode.unique' => 'Kode rekomendasi sudah digunakan.',

            'nama.required' => 'Nama rekomendasi wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nama.unique' => 'Nama rekomendasi sudah digunakan.',

            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status tidak valid.',
        ]);

        $recommendation = Recommendation::updateOrCreate(
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
                ? 'Rekomendasi berhasil diperbarui.'
                : 'Rekomendasi berhasil ditambahkan.',

            'data' => $recommendation,
        ]);
    }

    public function edit(
        Recommendation $recommendation
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $recommendation,
        ]);
    }

    public function destroy(
        Recommendation $recommendation
    ): JsonResponse {
        $recommendation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi berhasil dihapus.',
        ]);
    }
}