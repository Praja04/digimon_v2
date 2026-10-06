<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBahan;
use App\Models\MasterParameterRm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterParameterRmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterParameterRm::query()
                ->with('jenisBahan');

            if ($request->filled('filter_kategori')) {
                $data->where('kategori', $request->filter_kategori);
            }

            if ($request->filled('filter_jenis_bahan')) {
                $filterJb = $request->filter_jenis_bahan;
                if ($filterJb === 'global') {
                    $data->whereNull('jenis_bahan_id');
                } else {
                    $data->where('jenis_bahan_id', $filterJb);
                }
            }

            $data->orderBy('kategori')
                ->orderBy('urutan')
                ->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_bahan', function (MasterParameterRm $item) {
                    return $item->jenisBahan?->nama ?? '<span class="badge bg-secondary-subtle text-secondary">Semua / Global</span>';
                })
                ->editColumn('kategori', function (MasterParameterRm $item) {
                    $kat = strtolower($item->kategori);
                    $badge = match ($kat) {
                        'warna'  => '<span class="badge bg-primary px-2 py-1"><i class="mdi mdi-palette me-1"></i>Warna</span>',
                        'aroma'  => '<span class="badge bg-info px-2 py-1"><i class="mdi mdi-scent me-1"></i>Aroma</span>',
                        'organo' => '<span class="badge bg-warning text-dark px-2 py-1"><i class="mdi mdi-food-apple-outline me-1"></i>Organo (Rasa)</span>',
                        default  => '<span class="badge bg-secondary">' . e(ucfirst($item->kategori)) . '</span>',
                    };
                    return $badge;
                })
                ->editColumn('is_custom', function (MasterParameterRm $item) {
                    if ($item->is_custom) {
                        return '<span class="badge bg-info-subtle text-info"><i class="mdi mdi-form-textbox me-1"></i>Input Teks Bebas</span>';
                    }
                    return '<span class="badge bg-light text-muted">Dropdown Standar</span>';
                })
                ->editColumn('status', function (MasterParameterRm $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterParameterRm $item) {
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
                                data-nama="' . e($item->nama_pilihan . ' (' . ucfirst($item->kategori) . ')') . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['jenis_bahan', 'kategori', 'is_custom', 'status', 'action'])
                ->make(true);
        }

        $jenisBahans = MasterJenisBahan::where('status', true)->orderBy('nama')->get();

        return view('master-data-rm.parameter.index', compact('jenisBahans'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'jenis_bahan_id' => [
                'nullable',
                'exists:master_jenis_bahans,id',
            ],
            'kategori' => [
                'required',
                'string',
                'in:warna,aroma,organo',
            ],
            'nama_pilihan' => [
                'required',
                'string',
                'max:150',
            ],
            'is_custom' => [
                'nullable',
                'boolean',
            ],
            'urutan' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'kategori.required'     => 'Kategori parameter wajib dipilih.',
            'kategori.in'           => 'Kategori parameter harus berupa Warna, Aroma, atau Organo.',
            'nama_pilihan.required' => 'Nama / nilai pilihan wajib diisi.',
            'status.required'       => 'Status wajib dipilih.',
        ]);

        $validated['is_custom'] = $request->boolean('is_custom');
        $validated['urutan'] = $request->filled('urutan') ? (int) $request->urutan : 0;

        MasterParameterRm::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data pilihan parameter berhasil diperbarui.' : 'Data pilihan parameter berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterParameterRm::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterParameterRm::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data pilihan parameter berhasil dihapus.',
        ]);
    }

    public function getActiveOptions(Request $request): JsonResponse
    {
        $jenisStr = $request->input('jenis'); // e.g. "Gula Kelapa", "Gula Tebu", "Garam"
        
        $jbId = null;
        if ($jenisStr) {
            $jb = MasterJenisBahan::where('nama', 'LIKE', '%' . trim($jenisStr) . '%')->first();
            $jbId = $jb?->id;
        }

        $query = MasterParameterRm::where('status', true);

        if ($jbId) {
            $query->where(function ($q) use ($jbId) {
                $q->where('jenis_bahan_id', $jbId)
                  ->orWhereNull('jenis_bahan_id');
            });
        }

        $all = $query->orderBy('urutan')->orderBy('id')->get();

        $warna = [];
        $aroma = [];
        $organo = [];

        foreach ($all as $item) {
            $kat = strtolower($item->kategori);
            $val = trim($item->nama_pilihan);
            if ($kat === 'warna') {
                if (!in_array($val, $warna)) $warna[] = $val;
            } elseif ($kat === 'aroma') {
                if (!in_array($val, $aroma)) $aroma[] = $val;
            } elseif ($kat === 'organo') {
                $organo[] = [
                    'label'    => $val,
                    'value'    => $val,
                    'isCustom' => (bool) $item->is_custom,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'warna'  => $warna,
                'aroma'  => $aroma,
                'organo' => $organo,
            ],
        ]);
    }
}
