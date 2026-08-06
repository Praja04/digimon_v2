<?php

namespace App\Http\Controllers;

use App\Models\SamplingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SamplingStatusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SamplingStatus::query()
                ->orderBy('id');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('status', function (SamplingStatus $item) {
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

                ->addColumn('action', function (SamplingStatus $item) {
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

        return view('sampling-status.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sampling_statuses', 'kode')->ignore($id),
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sampling_statuses', 'nama')->ignore($id),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.max' => 'Kode maksimal 50 karakter.',
            'kode.unique' => 'Kode sudah digunakan.',

            'nama.required' => 'Nama status sampling wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nama.unique' => 'Nama status sampling sudah digunakan.',

            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status tidak valid.',
        ]);

        $samplingStatus = SamplingStatus::updateOrCreate(
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
                ? 'Status Sampling berhasil diperbarui.'
                : 'Status Sampling berhasil ditambahkan.',
            'data' => $samplingStatus,
        ]);
    }

    public function edit(
        SamplingStatus $samplingStatus
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $samplingStatus,
        ]);
    }

    public function destroy(
        SamplingStatus $samplingStatus
    ): JsonResponse {
        $samplingStatus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Status Sampling berhasil dihapus.',
        ]);
    }
}