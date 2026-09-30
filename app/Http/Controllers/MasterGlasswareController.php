<?php

namespace App\Http\Controllers;

use App\Models\MasterGlassware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterGlasswareController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterGlassware::query();

            if ($request->filled('filter_jenis')) {
                $data->where('jenis_glassware', $request->filter_jenis);
            }

            $data->orderBy('jenis_glassware')->orderByRaw('CAST(nomor_glassware AS UNSIGNED) ASC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('berat_glassware', function (MasterGlassware $item) {
                    if ($item->berat_glassware !== null) {
                        return number_format($item->berat_glassware, 4, '.', '') . ' ' . e($item->uom_berat);
                    }
                    return '<span class="text-muted fst-italic">-</span>';
                })
                ->editColumn('status', function (MasterGlassware $item) {
                    if ($item->status) {
                        return '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>';
                    }
                    return '<span class="badge bg-secondary"><i class="mdi mdi-close-circle-outline me-1"></i>Tidak Aktif</span>';
                })
                ->addColumn('action', function (MasterGlassware $item) {
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
                                data-nama="' . e($item->jenis_glassware . ' No. ' . $item->nomor_glassware) . '"
                                title="Hapus"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['berat_glassware', 'status', 'action'])
                ->make(true);
        }

        $jenisGlasswareOptions = MasterGlassware::select('jenis_glassware')
            ->distinct()
            ->orderBy('jenis_glassware')
            ->pluck('jenis_glassware');

        return view('master-data-rm.glassware.index', compact('jenisGlasswareOptions'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $validated = $request->validate([
            'jenis_glassware' => [
                'required',
                'string',
                'max:100',
            ],
            'nomor_glassware' => [
                'required',
                'string',
                'max:50',
            ],
            'berat_glassware' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'uom_berat' => [
                'required',
                'string',
                'max:20',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'jenis_glassware.required' => 'Jenis glassware wajib diisi.',
            'nomor_glassware.required' => 'Nomor glassware wajib diisi.',
            'berat_glassware.numeric'  => 'Berat glassware harus berupa angka.',
            'uom_berat.required'        => 'UoM berat wajib diisi.',
            'status.required'          => 'Status wajib dipilih.',
        ]);

        MasterGlassware::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return response()->json([
            'status'  => true,
            'message' => $id ? 'Data glassware berhasil diperbarui.' : 'Data glassware berhasil ditambahkan.',
        ]);
    }

    public function edit($id): JsonResponse
    {
        $data = MasterGlassware::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $data = MasterGlassware::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data glassware berhasil dihapus.',
        ]);
    }

    public function getActiveData(Request $request): JsonResponse
    {
        $excludeIdentitasId = $request->input('exclude_identitas_id');

        $glasswares = MasterGlassware::where('status', true)->get();

        $beakerQuery = \Illuminate\Support\Facades\DB::table('analisa_short_term')
            ->whereDate('created_at', today())
            ->whereNotNull('no_beaker')
            ->where('no_beaker', '!=', '');

        $cawanQuery = \Illuminate\Support\Facades\DB::table('analisa_short_term')
            ->whereDate('created_at', today())
            ->whereNotNull('no_cawan')
            ->where('no_cawan', '!=', '');

        if ($excludeIdentitasId) {
            $beakerQuery->where('id_identitas', '!=', $excludeIdentitasId);
            $cawanQuery->where('id_identitas', '!=', $excludeIdentitasId);
        }

        $totalBeakerToday = (clone $beakerQuery)->count();
        $totalCawanToday = (clone $cawanQuery)->count();

        $beakerUsage = [];
        $rawBeakerUsage = (clone $beakerQuery)
            ->select('no_beaker', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('no_beaker')
            ->pluck('count', 'no_beaker')
            ->toArray();

        foreach ($rawBeakerUsage as $code => $cnt) {
            $phys = trim(explode('.', (string)$code)[0]);
            if ($phys !== '') {
                $beakerUsage[$phys] = ($beakerUsage[$phys] ?? 0) + $cnt;
            }
        }

        $cawanUsage = [];
        $rawCawanUsage = (clone $cawanQuery)
            ->select('no_cawan', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('no_cawan')
            ->pluck('count', 'no_cawan')
            ->toArray();

        foreach ($rawCawanUsage as $code => $cnt) {
            $phys = trim(explode('.', (string)$code)[0]);
            if ($phys !== '') {
                $cawanUsage[$phys] = ($cawanUsage[$phys] ?? 0) + $cnt;
            }
        }

        $beaker500 = [];
        $beaker250 = [];
        $cawan = [];

        foreach ($glasswares as $gw) {
            $jenis = strtoupper(trim($gw->jenis_glassware));
            $nomor = (string) $gw->nomor_glassware;
            $berat = $gw->berat_glassware !== null ? (float) $gw->berat_glassware : 0;

            if (str_contains($jenis, '500')) {
                $beaker500[$nomor] = $berat;
            } elseif (str_contains($jenis, '250')) {
                $beaker250[$nomor] = $berat;
            } elseif (str_contains($jenis, 'CAWAN')) {
                $cawan[$nomor] = $berat;
            }
        }

        $beakerTransactions = \Illuminate\Support\Facades\DB::table('analisa_short_term')
            ->join('identitas_rm', 'analisa_short_term.id_identitas', '=', 'identitas_rm.id')
            ->leftJoin('users', 'analisa_short_term.created_by', '=', 'users.id')
            ->whereDate('analisa_short_term.created_at', today())
            ->whereNotNull('analisa_short_term.no_beaker')
            ->where('analisa_short_term.no_beaker', '!=', '')
            ->select(
                'analisa_short_term.id',
                'analisa_short_term.id_identitas',
                'analisa_short_term.no_beaker',
                'analisa_short_term.kategori',
                'analisa_short_term.timbang_a',
                'analisa_short_term.timbang_b',
                'analisa_short_term.kotoran',
                'analisa_short_term.created_at',
                'identitas_rm.no_spb',
                'identitas_rm.jenis',
                'identitas_rm.supplier',
                'users.name as analyst_name'
            )
            ->orderByDesc('analisa_short_term.created_at')
            ->get();

        $cawanTransactions = \Illuminate\Support\Facades\DB::table('analisa_short_term')
            ->join('identitas_rm', 'analisa_short_term.id_identitas', '=', 'identitas_rm.id')
            ->leftJoin('users', 'analisa_short_term.created_by', '=', 'users.id')
            ->whereDate('analisa_short_term.created_at', today())
            ->whereNotNull('analisa_short_term.no_cawan')
            ->where('analisa_short_term.no_cawan', '!=', '')
            ->select(
                'analisa_short_term.id',
                'analisa_short_term.id_identitas',
                'analisa_short_term.no_cawan',
                'analisa_short_term.kategori',
                'analisa_short_term.timbang_aa',
                'analisa_short_term.ka',
                'analisa_short_term.created_at',
                'identitas_rm.no_spb',
                'identitas_rm.jenis',
                'identitas_rm.supplier',
                'users.name as analyst_name'
            )
            ->orderByDesc('analisa_short_term.created_at')
            ->get();

        return response()->json([
            'status'              => true,
            'beaker_500'          => $beaker500,
            'beaker_250'          => $beaker250,
            'cawan'               => $cawan,
            'total_today'         => [
                'beaker' => $totalBeakerToday,
                'cawan'  => $totalCawanToday,
            ],
            'usage'               => [
                'beaker' => $beakerUsage,
                'cawan'  => $cawanUsage,
            ],
            'max_limits'          => [
                'beaker' => 8,
                'cawan'  => 2,
            ],
            'beaker_transactions' => $beakerTransactions,
            'cawan_transactions'  => $cawanTransactions,
        ]);
    }
}
