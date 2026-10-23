<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBahan;
use App\Models\MasterStandarRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterStandarRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterStandarRm::query()
                ->with('jenisBahan');

            if ($request->filled('filter_jenis_bahan')) {
                $data->where('id_jenis_bahan', $request->filter_jenis_bahan);
            }

            if ($request->filled('filter_jenis_standar')) {
                $data->where('jenis_standar', $request->filter_jenis_standar);
            }

            $data->orderBy('id_jenis_bahan')->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterStandarRm $item) {
                    return $item->jenisBahan?->nama ?? '-';
                })
                ->addColumn('parameter_display', function (MasterStandarRm $item) {
                    $html = '<span class="fw-semibold text-dark fs-14">' . e($item->parameter) . '</span>';
                    if ($item->jenis_standar === 'kuali' && $item->target_text && stripos($item->parameter, 'organo') === false) {
                        $html .= '<br><span class="text-muted small fs-12"><i class="mdi mdi-information-outline me-1"></i>' . e($item->target_text) . '</span>';
                    }
                    return $html;
                })
                ->addColumn('jenis_standar_badge', function (MasterStandarRm $item) {
                    if ($item->jenis_standar === 'kuanti') {
                        return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="ri-ruler-line me-1"></i>Kuanti</span>';
                    }
                    return '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="ri-file-text-line me-1"></i>Kuali</span>';
                })
                ->addColumn('min_display', function (MasterStandarRm $item) {
                    if ($item->min_standar !== null && $item->min_standar !== '') {
                        if (stripos($item->parameter, 'organo') !== false) {
                            return '<span class="fw-semibold text-dark">OK = ' . floatval($item->min_standar) . '%</span>';
                        }
                        return '<span class="fw-semibold text-dark">' . floatval($item->min_standar) . '</span>';
                    }
                    if ($item->jenis_standar === 'kuali' && $item->target_text && stripos($item->parameter, 'organo') !== false) {
                        return '<span class="fw-semibold text-dark">' . e($item->target_text) . '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('max_display', function (MasterStandarRm $item) {
                    if ($item->max_standar !== null && $item->max_standar !== '') {
                        return '<span class="fw-semibold text-dark">' . floatval($item->max_standar) . '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('standar_display', function (MasterStandarRm $item) {
                    if ($item->jenis_standar === 'kuali') {
                        return $item->target_text
                            ? '<span class="text-dark">' . e($item->target_text) . '</span>'
                            : '<span class="text-muted">—</span>';
                    }

                    $hasMin = $item->min_standar !== null && $item->min_standar !== '';
                    $hasMax = $item->max_standar !== null && $item->max_standar !== '';

                    if ($hasMin && $hasMax) {
                        return '<span class="text-dark">' . floatval($item->min_standar) . ' - ' . floatval($item->max_standar) . '</span>';
                    } elseif ($hasMin) {
                        return '<span class="text-dark">Min. ' . floatval($item->min_standar) . '</span>';
                    } elseif ($hasMax) {
                        return '<span class="text-dark">Maks. ' . floatval($item->max_standar) . '</span>';
                    } elseif ($item->target_text) {
                        return '<span class="text-dark">' . e($item->target_text) . '</span>';
                    }

                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('uom_display', function (MasterStandarRm $item) {
                    return $item->uom ? e($item->uom) : '—';
                })
                ->editColumn('status', function (MasterStandarRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterStandarRm $item) {
                    return '
                        <div class="d-flex gap-1 justify-content-center">
                            <button
                                type="button"
                                class="btn btn-warning btn-sm btnEdit px-2 py-1"
                                data-id="' . $item->id . '"
                                title="Edit"
                            >
                                <i class="mdi mdi-pencil fs-14"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnDelete px-2 py-1"
                                data-id="' . $item->id . '"
                                data-nama="' . e(($item->jenisBahan?->nama ?? '') . ' - ' . $item->parameter) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete fs-14"></i>
                            </button>
                        </div>
                    ';
                })
                ->filterColumn('standar_display', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('target_text', 'like', "%{$keyword}%")
                          ->orWhere('min_standar', 'like', "%{$keyword}%")
                          ->orWhere('max_standar', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['parameter_display', 'jenis_standar_badge', 'standar_display', 'uom_display', 'min_display', 'max_display', 'target_text_display', 'status', 'action'])
                ->make(true);
        }

        $jenisBahans = MasterJenisBahan::where('status', true)->orderBy('nama')->get();
        $countKuanti = MasterStandarRm::where('jenis_standar', 'kuanti')->count();
        $countKuali  = MasterStandarRm::where('jenis_standar', 'kuali')->count();

        return view('master-data-rm.standar.index', compact('jenisBahans', 'countKuanti', 'countKuali'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'id_jenis_bahan' => [
                'required',
                'integer',
                'exists:master_jenis_bahans,id',
            ],
            'jenis_standar' => [
                'required',
                'in:kuanti,kuali',
            ],
            'parameter' => [
                'required',
                'string',
                'max:100',
            ],
            'min_standar' => [
                'nullable',
                'numeric',
            ],
            'max_standar' => [
                'nullable',
                'numeric',
            ],
            'target_text' => [
                'nullable',
                'string',
                'max:150',
            ],
            'uom' => [
                'nullable',
                'string',
                'max:20',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'id_jenis_bahan.required' => 'Jenis bahan wajib dipilih.',
            'id_jenis_bahan.exists'   => 'Jenis bahan tidak valid.',
            'jenis_standar.required'  => 'Jenis standar (Kuanti/Kuali) wajib dipilih.',
            'parameter.required'      => 'Nama parameter wajib diisi.',
            'min_standar.numeric'     => 'Min standar harus berupa angka.',
            'max_standar.numeric'     => 'Max standar harus berupa angka.',
            'status.required'         => 'Status wajib dipilih.',
        ]);

        if ($validated['jenis_standar'] === 'kuali') {
            $validated['min_standar'] = null;
            $validated['max_standar'] = null;
        } else {
            $min = $validated['min_standar'] ?? null;
            $max = $validated['max_standar'] ?? null;
            $uom = $validated['uom'] ?? '';

            if ($min !== null && $max !== null) {
                $validated['target_text'] = floatval($min) . ' - ' . floatval($max) . ($uom ? " $uom" : '');
            } elseif ($min !== null) {
                $validated['target_text'] = 'Min ' . floatval($min) . ($uom ? " $uom" : '');
            } elseif ($max !== null) {
                $validated['target_text'] = 'Maks ' . floatval($max) . ($uom ? " $uom" : '');
            } else {
                $validated['target_text'] = null;
            }
        }

        MasterStandarRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data standar mutu RM berhasil diperbarui.' : 'Data standar mutu RM berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterStandarRm::with('jenisBahan')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterStandarRm::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data standar mutu RM berhasil dihapus.',
        ]);
    }

    public function getActiveByJenis(Request $request): JsonResponse
    {
        $jenisName = $request->input('jenis');
        $jenisId = $request->input('id_jenis_bahan');

        $query = MasterStandarRm::where('status', true);

        if ($jenisId) {
            $query->where('id_jenis_bahan', $jenisId);
        } elseif ($jenisName) {
            $cleanName = strtoupper(trim($jenisName));
            $query->whereHas('jenisBahan', function ($q) use ($cleanName) {
                $q->whereRaw('UPPER(nama) = ?', [$cleanName]);
            });
        }

        $standards = $query->get();

        $map = [];
        foreach ($standards as $s) {
            $key = strtoupper(trim($s->parameter));
            $map[$key] = [
                'parameter'   => $s->parameter,
                'min'         => $s->min_standar,
                'max'         => $s->max_standar,
                'target_text' => $s->target_text,
                'uom'         => $s->uom,
            ];
        }

        return response()->json([
            'status'    => true,
            'standards' => $map,
        ]);
    }
}
