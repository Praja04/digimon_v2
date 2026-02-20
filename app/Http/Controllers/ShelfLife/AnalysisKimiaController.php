<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelfLife\AnalysisKimiaStoreRequest;
use App\Models\Color;
use App\Models\ShelfLifeSamplingDetail;
use App\Models\ShelfLifeSamplingKimia;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use function Symfony\Component\Clock\now;

class AnalysisKimiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (empty($request->kelompok_sample) || empty($request->tanggal_analisa)) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->make(true);
            }

            $query = ShelfLifeSamplingDetail::with('shelfLifeSamplingKimia')
                ->where('kelompok_sample', $request->kelompok_sample)
                ->where('tanggal_analisa', $request->tanggal_analisa);

            if (!empty($request->filter_status)) {
                $status = $request->filter_status;

                if ($status == 'belum') {
                    $query->doesntHave('shelfLifeSamplingKimia');
                } elseif ($status == 'proses') {
                    $query->whereHas('shelfLifeSamplingKimia', function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->whereNotNull('shift_analis')
                                ->orWhereNotNull('nama_analis');
                        })
                            ->where(function ($subQ) {
                                $subQ->whereNull('nacl')
                                    ->orWhereNull('brix')
                                    ->orWhereNull('ph')
                                    ->orWhereNull('bj')
                                    ->orWhereNull('buih')
                                    ->orWhereNull('aroma')
                                    ->orWhereNull('color_id')
                                    ->orWhereNull('organo')
                                    ->orWhereNull('visco')
                                    ->orWhereNull('total_nitrogen');
                            });
                    });
                } elseif ($status == 'lengkap') {
                    $query->whereHas('shelfLifeSamplingKimia', function ($q) {
                        $q->whereNotNull('shift_analis')
                            ->whereNotNull('nama_analis')
                            ->whereNotNull('nacl')
                            ->whereNotNull('brix')
                            ->whereNotNull('ph')
                            ->whereNotNull('bj')
                            ->whereNotNull('buih')
                            ->whereNotNull('aroma')
                            ->whereNotNull('color_id')
                            ->whereNotNull('organo')
                            ->whereNotNull('visco')
                            ->whereNotNull('total_nitrogen');
                    });
                }
            }

            $query->orderBy('bulan_ke', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $kimia = $data->shelfLifeSamplingKimia;
                    $bulanKe = $data->bulan_ke;

                    if (!$kimia) {
                        return '<span class="badge bg-danger">Belum Dianalisa</span>';
                    }

                    $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
                    $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);

                    $isComplete = $kimia->shift_analis && $kimia->nama_analis &&
                        $kimia->nacl && $kimia->brix &&
                        $kimia->ph && $kimia->bj && $kimia->buih &&
                        $kimia->aroma && $kimia->color_id && $kimia->organo;

                    if (!$hideVisco) {
                        $isComplete = $isComplete && $kimia->visco;
                    }

                    if ($showTotalNitrogen) {
                        $isComplete = $isComplete && $kimia->total_nitrogen;
                    }

                    if ($isComplete) {
                        return '<span class="badge bg-success">Lengkap</span>';
                    }

                    if ($kimia->shift_analis || $kimia->nama_analis) {
                        return '<span class="badge bg-warning text-dark">Proses</span>';
                    }

                    return '<span class="badge bg-danger">Belum Dianalisa</span>';
                })
                ->addColumn('action', function ($data) {
                    $kimia = $data->shelfLifeSamplingKimia;
                    $bulanKe = $data->bulan_ke;

                    $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
                    $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);

                    $isComplete = $kimia && $kimia->shift_analis && $kimia->nama_analis &&
                        $kimia->nacl && $kimia->brix &&
                        $kimia->ph && $kimia->bj && $kimia->buih &&
                        $kimia->aroma && $kimia->color_id && $kimia->organo;

                    if (!$hideVisco) {
                        $isComplete = $isComplete && $kimia->visco;
                    }

                    if ($showTotalNitrogen) {
                        $isComplete = $isComplete && $kimia->total_nitrogen;
                    }

                    $isAnalisKimia = auth()->user()->role == 'Analis Kimia';

                    if ($isComplete) {
                        return '
                    <a class="btn btn-sm btn-success me-1" href="' . route("shelf-life.analysis-kimia.show", $data->id) . '">
                        <span class="mdi mdi-eye"></span> Lihat
                    </a>
                    ';
                    }

                    if ($isAnalisKimia) {
                        return '
                            <a class="btn btn-sm btn-primary me-1" href="' . route("shelf-life.analysis-kimia.show", $data->id) . '">
                                <span class="mdi mdi-flask"></span> Analisa
                            </a>
                    ';
                    } else {
                        return '
                        <span class="mdi mdi-lock text-muted"></span>
                    ';
                    }

                    return '';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('app.shelf_life.analysis_kimia.index');
    }

    public function show($id)
    {
        $data = ShelfLifeSamplingDetail::with(['shelfLifeSample.productionBatch', 'shelfLifeSamplingKimia'])->findOrFail($id);

        if (!$data->is_checked) {
            return redirect()->route('shelf-life.analysis-kimia.index')
                ->with('error', 'Sample belum di-checklist. Silakan checklist terlebih dahulu di <a href="' . route('shelf-life.checksheet.index') . '" class="alert-link">halaman checksheet</a>.');
        }

        $colors = Color::orderBy('name', 'asc')->get();
        $bulanKe = $data->bulan_ke;

        return view('app.shelf_life.analysis_kimia.show', compact('data', 'colors', 'bulanKe'));
    }

    public function edit($id)
    {
        try {
            $data = ShelfLifeSamplingKimia::where('shelf_life_sampling_detail_id', $id)->first();

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(AnalysisKimiaStoreRequest $request)
    {
        try {
            $detail = ShelfLifeSamplingDetail::find($request->shelf_life_sampling_detail_id);

            if (!$detail->is_checked) {
                return response()->json([
                    'message' => 'Sample belum di-checklist. Silakan checklist terlebih dahulu.',
                    'redirect_url' => route('shelf-life.checksheet.index')
                ], 403);
            }


            $bulanKe = $detail->bulan_ke;

            $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
            $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);

            $existingData = ShelfLifeSamplingKimia::where('shelf_life_sampling_detail_id', $request->shelf_life_sampling_detail_id)->first();

            $isComplete = $existingData &&
                $existingData->nacl && $existingData->brix &&
                $existingData->ph && $existingData->bj && $existingData->buih &&
                $existingData->aroma && $existingData->color_id && $existingData->organo;

            if (!$hideVisco) {
                $isComplete = $isComplete && $existingData->visco;
            }

            if ($showTotalNitrogen) {
                $isComplete = $isComplete && $existingData->total_nitrogen;
            }

            if ($isComplete) {
                return response()->json([
                    'message' => 'Data analisis sudah lengkap dan tidak bisa diubah.'
                ], 409);
            }

            $dataToSave = [
                'shelf_life_sampling_detail_id' => $request->shelf_life_sampling_detail_id,
            ];

            if ($request->has('shift_analis') && $request->shift_analis) {
                $dataToSave['shift_analis'] = $request->shift_analis;
            }

            if ($request->has('nama_analis') && $request->nama_analis) {
                $dataToSave['nama_analis'] = strtoupper($request->nama_analis);
            }

            if ($request->has('nacl') && $request->nacl) {
                $dataToSave['waktu_analisa'] = now();
                $dataToSave['nacl'] = str_replace(',', '.', $request->nacl);
            }

            if ($request->has('brix') && $request->brix) {
                $dataToSave['brix'] = str_replace(',', '.', $request->brix);
            }

            if ($request->has('aw') && $request->aw) {
                $dataToSave['aw'] = str_replace(',', '.', $request->aw);
            }

            if ($request->has('ph') && $request->ph) {
                $dataToSave['ph'] = str_replace(',', '.', $request->ph);
            }

            if ($request->has('bj') && $request->bj) {
                $dataToSave['bj'] = str_replace(',', '.', $request->bj);
            }

            if ($request->has('buih') && $request->buih) {
                $dataToSave['buih'] = str_replace(',', '.', $request->buih);
            }

            if ($request->has('aroma') && $request->aroma) {
                $dataToSave['aroma'] = strtoupper($request->aroma);
            }

            if ($request->has('color') && $request->color) {
                $dataToSave['color_id'] = $request->color;
            }

            if ($request->has('organo') && $request->organo) {
                $dataToSave['organo'] = strtoupper($request->organo);
            }

            if (!$hideVisco && $request->has('visco') && $request->visco) {
                $dataToSave['visco'] = str_replace(',', '.', $request->visco);
            }

            if ($showTotalNitrogen && $request->has('total_nitrogen') && $request->total_nitrogen) {
                $dataToSave['total_nitrogen'] = str_replace(',', '.', $request->total_nitrogen);
            }

            $data = ShelfLifeSamplingKimia::updateOrCreate(
                ['shelf_life_sampling_detail_id' => $request->shelf_life_sampling_detail_id],
                $dataToSave
            );

            $message = $data->wasRecentlyCreated
                ? 'Data berhasil disimpan'
                : 'Data berhasil diperbarui';

            return response()->json([
                'message' => $message,
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
