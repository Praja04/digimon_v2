<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use App\Models\ShelfLifeSamplingDetail;
use App\Models\ShelfLifeSamplingMikro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AnalysisMikroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('get_kelompok_tanggal')) {
                $kelompokTanggal = ShelfLifeSamplingDetail::select('kelompok_tanggal')
                    ->distinct()
                    ->whereNotNull('kelompok_tanggal')
                    ->where('kelompok_sample', $request->kelompok_sample)
                    ->orderBy('kelompok_tanggal', 'asc')
                    ->pluck('kelompok_tanggal');

                return response()->json($kelompokTanggal);
            }

            if (empty($request->kelompok_sample) || empty($request->kelompok_tanggal)) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->make(true);
            }

            $query = ShelfLifeSamplingDetail::with('shelfLifeSamplingMikro')
                ->where('kelompok_sample', $request->kelompok_sample)
                ->where('kelompok_tanggal', $request->kelompok_tanggal);

            if (!empty($request->filter_status)) {
                $status = $request->filter_status;

                if ($status == 'belum') {
                    $query->doesntHave('shelfLifeSamplingMikro');
                } elseif ($status == 'proses') {
                    $query->whereHas('shelfLifeSamplingMikro', function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->whereNotNull('shift_analis')
                                ->orWhereNotNull('nama_analis');
                        })
                            ->where(function ($subQ) {
                                $subQ->whereNull('shift_analis')
                                    ->orWhereNull('nama_analis')
                                    ->orWhereNull('eb')
                                    ->orWhereNull('tpc')
                                    ->orWhereNull('ym');
                            });
                    });
                } elseif ($status == 'lengkap') {
                    $query->whereHas('shelfLifeSamplingMikro', function ($q) {
                        $q->whereNotNull('shift_analis')
                            ->whereNotNull('nama_analis')
                            ->whereNotNull('eb')
                            ->whereNotNull('tpc')
                            ->whereNotNull('ym');
                    });
                }
            }

            $query->orderBy('bulan_ke', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $mikro = $data->shelfLifeSamplingMikro;
                    $bulanKe = $data->bulan_ke;

                    if (!$mikro) {
                        return '<span class="badge bg-danger">Belum Dianalisa</span>';
                    }

                    $showSa = in_array($bulanKe, [1, 24]);

                    // Cek kelengkapan parameter wajib
                    $isComplete = !is_null($mikro->shift_analis) &&
                        !is_null($mikro->nama_analis) &&
                        !is_null($mikro->eb) &&
                        !is_null($mikro->tpc) &&
                        !is_null($mikro->ym);

                    // Tambahkan SA untuk bulan ke-1 dan ke-24
                    if ($showSa) {
                        $isComplete = $isComplete && !is_null($mikro->sa);
                    }

                    if ($isComplete) {
                        return '<span class="badge bg-success">Lengkap</span>';
                    }

                    // Status proses jika minimal shift atau nama analis sudah diisi
                    if (!is_null($mikro->shift_analis) || !is_null($mikro->nama_analis)) {
                        return '<span class="badge bg-warning text-dark">Proses</span>';
                    }

                    return '<span class="badge bg-danger">Belum Dianalisa</span>';
                })
                ->addColumn('action', function ($data) {
                    $mikro = $data->shelfLifeSamplingMikro;
                    $bulanKe = $data->bulan_ke;

                    if (!$mikro) {
                        return '
                        <a class="btn btn-sm btn-primary me-1" href="' . route("shelf-life.analysis-mikro.show", $data->id) . '">
                            <span class="mdi mdi-flask"></span> Analisa
                        </a>
                    ';
                    }

                    $showSa = in_array($bulanKe, [1, 24]);

                    // Cek kelengkapan parameter wajib
                    $isComplete = !is_null($mikro->shift_analis) &&
                        !is_null($mikro->nama_analis) &&
                        !is_null($mikro->eb) &&
                        !is_null($mikro->tpc) &&
                        !is_null($mikro->ym);

                    // Tambahkan SA untuk bulan ke-1 dan ke-24
                    if ($showSa) {
                        $isComplete = $isComplete && !is_null($mikro->sa);
                    }

                    $isAnalisMikro = auth()->user()->role == 'Analis Mikro';

                    if ($isComplete) {
                        return '
                        <a class="btn btn-sm btn-success me-1" href="' . route("shelf-life.analysis-mikro.show", $data->id) . '">
                            <span class="mdi mdi-eye"></span> Lihat
                        </a>
                    ';
                    }

                    if ($isAnalisMikro) {
                        return '
                        <a class="btn btn-sm btn-primary me-1" href="' . route("shelf-life.analysis-mikro.show", $data->id) . '">
                            <span class="mdi mdi-flask"></span> Analisa
                        </a>
                    ';
                    } else {
                        return '
                        <span class="mdi mdi-lock text-muted"></span>
                    ';
                    }
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('app.shelf_life.analysis_mikro.index');
    }

    public function show($id)
    {
        $data = ShelfLifeSamplingDetail::with(['shelfLifeSample.productionBatch', 'shelfLifeSamplingMikro'])->findOrFail($id);

        if (!$data->is_checked) {
            return redirect()->route('shelf-life.analysis-kimia.index')
                ->with('error', 'Sample belum di-checklist. Silakan checklist terlebih dahulu di <a href="' . route('shelf-life.checksheet.index') . '" class="alert-link">halaman checksheet</a>.');
        }

        $bulanKe = $data->bulan_ke;

        return view('app.shelf_life.analysis_mikro.show', compact('data', 'bulanKe'));
    }

    public function getMikroData(Request $request)
    {
        try {
            $detail = ShelfLifeSamplingDetail::findOrFail($request->id);
            $mikro = ShelfLifeSamplingMikro::where('shelf_life_sampling_detail_id', $request->id)->first();

            // Jika belum ada data, buat record baru
            if (!$mikro) {
                $mikro = ShelfLifeSamplingMikro::create([
                    'shelf_life_sampling_detail_id' => $request->id
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $mikro->id,
                    'shelf_life_sampling_detail_id' => $mikro->shelf_life_sampling_detail_id,
                    'shift_analis' => $mikro->shift_analis,
                    'nama_analis' => $mikro->nama_analis,
                    'eb' => $mikro->eb,
                    'sa' => $mikro->sa,
                    'tpc' => $mikro->tpc,
                    'ym' => $mikro->ym,
                    'bulan_ke' => $detail->bulan_ke
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $mergeData = [];

            if ($request->filled('eb')) {
                $mergeData['eb'] = str_replace(',', '.', $request->eb);
            }

            if ($request->filled('sa')) {
                $mergeData['sa'] = str_replace(',', '.', $request->sa);
            }

            if ($request->filled('tpc')) {
                $mergeData['tpc'] = str_replace(',', '.', $request->tpc);
            }

            if ($request->filled('ym')) {
                $mergeData['ym'] = str_replace(',', '.', $request->ym);
            }

            $request->merge($mergeData);

            $mikro = ShelfLifeSamplingMikro::where('shelf_life_sampling_detail_id', $request->shelf_life_sampling_detail_id)->first();

            if (!$mikro) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $detail = ShelfLifeSamplingDetail::findOrFail($request->shelf_life_sampling_detail_id);
            if (!$detail->is_checked) {
                return response()->json([
                    'message' => 'Sample belum di-checklist. Silakan checklist terlebih dahulu.',
                    'redirect_url' => route('shelf-life.checksheet.index')
                ], 403);
            }
            $bulanKe = $detail->bulan_ke;
            $showSa = in_array($bulanKe, [1, 24]);

            $rules = [];
            $updateData = [];

            // Step 1: Shift & Nama Analis
            if ($request->filled('shift_analis') || $request->filled('nama_analis')) {
                $rules['shift_analis'] = 'required|integer|min:1|max:3';
                $rules['nama_analis'] = 'required|string|max:255';

                $updateData['shift_analis'] = $request->shift_analis;
                $updateData['nama_analis'] = $request->nama_analis;
            }

            // Step 2: EB
            if ($request->filled('eb')) {
                if (is_null($mikro->shift_analis) || is_null($mikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input EB.'
                    ], 409);
                }
                $rules['eb'] = 'required|numeric|min:0';
                $updateData['eb'] = $request->eb;
            }

            // Step 3: SA (hanya untuk bulan ke-1 dan ke-24)
            if ($request->filled('sa')) {
                if (is_null($mikro->shift_analis) || is_null($mikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input SA.'
                    ], 409);
                }

                if (is_null($mikro->eb)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'EB harus diisi terlebih dahulu sebelum input SA.'
                    ], 409);
                }

                if (!$showSa) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'SA hanya bisa diinput pada bulan ke-1 dan ke-24.'
                    ], 409);
                }

                $rules['sa'] = 'required|numeric|min:0';
                $updateData['sa'] = $request->sa;
            }

            // Step 4: TPC
            if ($request->filled('tpc')) {
                if (is_null($mikro->shift_analis) || is_null($mikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input TPC.'
                    ], 409);
                }

                if (is_null($mikro->eb)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'EB harus diisi terlebih dahulu sebelum input TPC.'
                    ], 409);
                }

                // Jika bulan ke-1 atau ke-24, SA harus diisi dulu
                if ($showSa && is_null($mikro->sa)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'SA harus diisi terlebih dahulu sebelum input TPC (Bulan ke-' . $bulanKe . ').'
                    ], 409);
                }

                $rules['tpc'] = 'required|numeric|min:0';
                $updateData['tpc'] = $request->tpc;
            }

            // Step 5: YM
            if ($request->filled('ym')) {
                if (is_null($mikro->shift_analis) || is_null($mikro->nama_analis)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift dan Nama Analis harus diisi terlebih dahulu sebelum input YM.'
                    ], 409);
                }

                if (is_null($mikro->eb)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'EB harus diisi terlebih dahulu sebelum input YM.'
                    ], 409);
                }

                if ($showSa && is_null($mikro->sa)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'SA harus diisi terlebih dahulu sebelum input YM (Bulan ke-' . $bulanKe . ').'
                    ], 409);
                }

                if (is_null($mikro->tpc)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'TPC harus diisi terlebih dahulu sebelum input YM.'
                    ], 409);
                }

                $rules['ym'] = 'required|numeric|min:0';
                $updateData['ym'] = $request->ym;
            }

            // Validasi
            if (!empty($rules)) {
                $validator = Validator::make($request->only(array_keys($rules)), $rules, [
                    'shift_analis.required' => 'Shift wajib diisi.',
                    'shift_analis.integer' => 'Shift harus berupa angka.',
                    'shift_analis.min' => 'Shift minimal 1.',
                    'shift_analis.max' => 'Shift maksimal 3.',
                    'nama_analis.required' => 'Nama Analis wajib diisi.',
                    'nama_analis.string' => 'Nama Analis harus berupa teks.',
                    'nama_analis.max' => 'Nama Analis maksimal 255 karakter.',
                    'eb.required' => 'EB wajib diisi.',
                    'eb.numeric' => 'EB harus berupa angka.',
                    'eb.min' => 'EB tidak boleh negatif.',
                    'sa.required' => 'SA wajib diisi.',
                    'sa.numeric' => 'SA harus berupa angka.',
                    'sa.min' => 'SA tidak boleh negatif.',
                    'tpc.required' => 'TPC wajib diisi.',
                    'tpc.numeric' => 'TPC harus berupa angka.',
                    'tpc.min' => 'TPC tidak boleh negatif.',
                    'ym.required' => 'YM wajib diisi.',
                    'ym.numeric' => 'YM harus berupa angka.',
                    'ym.min' => 'YM tidak boleh negatif.',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }
            }

            if (empty($updateData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang diinput.'
                ], 409);
            }

            // Update waktu analisa jika belum ada
            if (is_null($mikro->waktu_analisa)) {
                $updateData['waktu_analisa'] = now();
            }

            $mikro->update($updateData);
            $mikro->refresh();

            // Tentukan nama field untuk pesan
            $fieldName = '';
            if (isset($updateData['shift_analis'])) $fieldName = 'Shift dan Nama Analis';
            if (isset($updateData['eb'])) $fieldName = 'EB';
            if (isset($updateData['sa'])) $fieldName = 'SA';
            if (isset($updateData['tpc'])) $fieldName = 'TPC';
            if (isset($updateData['ym'])) $fieldName = 'YM';

            $message = "Data {$fieldName} berhasil disimpan.";

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'shift_analis' => $mikro->shift_analis,
                    'nama_analis' => $mikro->nama_analis,
                    'eb' => $mikro->eb,
                    'sa' => $mikro->sa,
                    'tpc' => $mikro->tpc,
                    'ym' => $mikro->ym,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
