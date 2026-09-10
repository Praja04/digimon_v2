<?php

namespace App\Http\Controllers\Analisa;

use App\Http\Controllers\Controller;
use App\Models\BlendingAfterAdjustMikro;
use App\Models\BlendingAfterAdjustMikroDraft;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BlendingAwalMikroController extends Controller
{
    private function isAnalisMikro(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'Analis Mikro';
    }

    /**
     * Menentukan field WAJIB yang sedang aktif berdasarkan DATA FINAL.
     *
     * Draft tidak pernah membuat step maju.
     * Step hanya maju setelah user memilih Simpan Final.
     */
    private function currentStep(BlendingAfterAdjustMikro $blending): string
    {
        if (empty($blending->shift) || empty($blending->nama_analis)) {
            return 'analis';
        }

        if (is_null($blending->eb)) {
            return 'eb';
        }

        if (is_null($blending->tpc)) {
            return 'tpc';
        }

        if (is_null($blending->ym)) {
            return 'ym';
        }

        return 'complete';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionBatch::with('blendingAfterAdjustMikro')
                ->has('blendingAfterAdjustMikro')
                ->orderBy('date', 'desc');

            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $blendingAwalMikro = $query->get();

            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'complete') {
                    $blendingAwalMikro = $blendingAwalMikro->filter(function ($batch) {
                        return $batch->isBlendingAwalMikroComplete();
                    });
                } elseif ($request->status == 'progress') {
                    $blendingAwalMikro = $blendingAwalMikro->filter(function ($batch) {
                        return !$batch->isBlendingAwalMikroComplete();
                    });
                }
            }

            $blendingAwalMikro = $blendingAwalMikro->sortBy(function ($batch) {
                return $batch->isBlendingAwalMikroComplete() ? 1 : 0;
            })->values();

            return DataTables::of($blendingAwalMikro)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    return $data->description ?? '-';
                })
                ->addColumn('blending_count', function ($data) {
                    return $data->blendingAfterAdjustMikro->count() ?? '-';
                })
                ->addColumn('status_blending_awal', function ($data) {
                    $isComplete = $data->isBlendingAwalMikroComplete();
                    $icon = $isComplete ? '✅' : '⌛';
                    $text = $isComplete ? 'Complete' : 'Progress';

                    return '<span>' . $icon . ' ' . $text . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $showUrl = route('analisa.blending-awal-mikro.show', ['id' => $data->id]);

                    return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Lihat Detail">
                        <i class="mdi mdi-eye"></i> Lihat
                    </a>
                ';
                })
                ->rawColumns(['status_blending_awal', 'action'])
                ->make(true);
        }

        return view('app.analisa.blending_awal_mikro.index');
    }

    public function show($id)
    {
        $productionBatch = ProductionBatch::with([
            'blendingAfterAdjustMikro'
        ])->findOrFail($id);

        return view('app.analisa.blending_awal_mikro.show', compact('productionBatch'));
    }

    public function show_batch($id)
    {
        $blending = BlendingAfterAdjustMikro::with('productionBatch')->findOrFail($id);

        return view('app.analisa.blending_awal_mikro.show_batch', compact('blending'));
    }

    /**
     * Mengambil data FINAL dan DRAFT secara terpisah.
     *
     * FINAL menentukan step.
     * DRAFT hanya mengisi kembali form pada step yang sama.
     */
    public function getBlendingData(Request $request)
    {
        try {
            $blending = BlendingAfterAdjustMikro::find($request->id);

            if (!$blending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $draft = BlendingAfterAdjustMikroDraft::where(
                'blending_after_adjust_mikro_id',
                $blending->id
            )->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $blending->id,
                    'current_step' => $this->currentStep($blending),

                    // Data final
                    'shift' => $blending->shift,
                    'nama_analis' => $blending->nama_analis,
                    'eb' => $blending->eb,
                    'tpc' => $blending->tpc,
                    'ym' => $blending->ym,
                    'hasil' => $blending->hasil,
                    'updated_at' => $blending->updated_at,

                    // Data sementara
                    'has_draft' => (bool) $draft,
                    'draft' => $draft ? [
                        'shift' => $draft->shift,
                        'nama_analis' => $draft->nama_analis,
                        'eb' => $draft->eb,
                        'tpc' => $draft->tpc,
                        'ym' => $draft->ym,
                        'hasil' => $draft->hasil,
                        'created_by' => $draft->created_by,
                        'updated_at' => $draft->updated_at,
                    ] : null,
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * SIMPAN SEMENTARA
     *
     * Fitur opsional.
     * User boleh langsung Simpan Final tanpa pernah membuat draft.
     *
     * Field pada step aktif tetap WAJIB diisi.
     * Bedanya:
     * - Simpan Sementara -> tabel draft, step TIDAK maju, Production TIDAK dipanggil.
     * - Simpan Final     -> tabel utama, step maju, Production dipanggil.
     */
    public function saveDraft(Request $request)
    {
        try {
            if (!$this->isAnalisMikro()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya Analis Mikro yang dapat menyimpan draft.'
                ], 403);
            }

            $blending = BlendingAfterAdjustMikro::find($request->id);

            if (!$blending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $step = $this->currentStep($blending);

            if ($step === 'complete') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data analisa sudah lengkap.'
                ], 409);
            }

            $input = $request->all();

            foreach (['eb', 'tpc', 'ym'] as $field) {
                if (
                    array_key_exists($field, $input)
                    && $input[$field] !== null
                    && $input[$field] !== ''
                ) {
                    $input[$field] = str_replace(',', '.', $input[$field]);
                }
            }

            $rules = [];
            $messages = [];
            $draftData = [];

            if ($step === 'analis') {
                $rules = [
                    'shift_analis' => 'required|integer|min:1|max:3',
                    'nama_analis' => 'required|string|max:255',
                ];

                $messages = [
                    'shift_analis.required' => 'Shift wajib diisi.',
                    'shift_analis.integer' => 'Shift harus berupa angka.',
                    'shift_analis.min' => 'Shift minimal 1.',
                    'shift_analis.max' => 'Shift maksimal 3.',
                    'nama_analis.required' => 'Nama Analis wajib diisi.',
                    'nama_analis.string' => 'Nama Analis harus berupa teks.',
                    'nama_analis.max' => 'Nama Analis maksimal 255 karakter.',
                ];
            } elseif ($step === 'eb') {
                $rules = [
                    'eb' => 'required|numeric|min:0',
                ];

                $messages = [
                    'eb.required' => 'EB wajib diisi.',
                    'eb.numeric' => 'EB harus berupa angka.',
                    'eb.min' => 'EB tidak boleh negatif.',
                ];
            } elseif ($step === 'tpc') {
                $rules = [
                    'tpc' => 'required|numeric|min:0',
                ];

                $messages = [
                    'tpc.required' => 'TPC wajib diisi.',
                    'tpc.numeric' => 'TPC harus berupa angka.',
                    'tpc.min' => 'TPC tidak boleh negatif.',
                ];
            } elseif ($step === 'ym') {
                $rules = [
                    'ym' => 'required|numeric|min:0',
                ];

                $messages = [
                    'ym.required' => 'YM wajib diisi.',
                    'ym.numeric' => 'YM harus berupa angka.',
                    'ym.min' => 'YM tidak boleh negatif.',
                ];
            }

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lengkapi field wajib sebelum menyimpan sementara.',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($step === 'analis') {
                $draftData['shift'] = $input['shift_analis'];
                $draftData['nama_analis'] = strtoupper(trim($input['nama_analis']));
            } elseif ($step === 'eb') {
                $draftData['eb'] = $input['eb'];
            } elseif ($step === 'tpc') {
                $draftData['tpc'] = $input['tpc'];
            } elseif ($step === 'ym') {
                $draftData['ym'] = $input['ym'];
            }

            $draftData['created_by'] = auth()->id();

            $draft = BlendingAfterAdjustMikroDraft::updateOrCreate(
                [
                    'blending_after_adjust_mikro_id' => $blending->id
                ],
                $draftData
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan sementara. Anda tetap berada di langkah yang sama sampai memilih Simpan Final.',
                'current_step' => $step,
                'data' => $draft
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data sementara.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * SIMPAN FINAL
     *
     * Memfinalkan hanya step yang sedang aktif.
     * User TIDAK wajib mempunyai draft.
     */
    public function update(Request $request)
    {
        try {
            if (!$this->isAnalisMikro()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya Analis Mikro yang dapat melakukan Simpan Final.'
                ], 403);
            }

            $blending = BlendingAfterAdjustMikro::find($request->id);

            if (!$blending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $step = $this->currentStep($blending);

            if ($step === 'complete') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data analisa sudah lengkap.'
                ], 409);
            }

            $input = $request->all();

            foreach (['eb', 'tpc', 'ym'] as $field) {
                if (
                    array_key_exists($field, $input)
                    && $input[$field] !== null
                    && $input[$field] !== ''
                ) {
                    $input[$field] = str_replace(',', '.', $input[$field]);
                }
            }

            $rules = [];
            $messages = [];
            $updateData = [];
            $fieldName = '';

            if ($step === 'analis') {
                $rules = [
                    'shift_analis' => 'required|integer|min:1|max:3',
                    'nama_analis' => 'required|string|max:255',
                ];

                $messages = [
                    'shift_analis.required' => 'Shift wajib diisi.',
                    'shift_analis.integer' => 'Shift harus berupa angka.',
                    'shift_analis.min' => 'Shift minimal 1.',
                    'shift_analis.max' => 'Shift maksimal 3.',
                    'nama_analis.required' => 'Nama Analis wajib diisi.',
                    'nama_analis.string' => 'Nama Analis harus berupa teks.',
                    'nama_analis.max' => 'Nama Analis maksimal 255 karakter.',
                ];

                $fieldName = 'Shift dan Nama Analis';
            } elseif ($step === 'eb') {
                $rules = [
                    'eb' => 'required|numeric|min:0',
                ];

                $messages = [
                    'eb.required' => 'EB wajib diisi.',
                    'eb.numeric' => 'EB harus berupa angka.',
                    'eb.min' => 'EB tidak boleh negatif.',
                ];

                $fieldName = 'EB';
            } elseif ($step === 'tpc') {
                $rules = [
                    'tpc' => 'required|numeric|min:0',
                ];

                $messages = [
                    'tpc.required' => 'TPC wajib diisi.',
                    'tpc.numeric' => 'TPC harus berupa angka.',
                    'tpc.min' => 'TPC tidak boleh negatif.',
                ];

                $fieldName = 'TPC';
            } elseif ($step === 'ym') {
                $rules = [
                    'ym' => 'required|numeric|min:0',
                ];

                $messages = [
                    'ym.required' => 'YM wajib diisi.',
                    'ym.numeric' => 'YM harus berupa angka.',
                    'ym.min' => 'YM tidak boleh negatif.',
                ];

                $fieldName = 'YM';
            }

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lengkapi field wajib sebelum menyimpan final.',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($step === 'analis') {
                $updateData['shift'] = $input['shift_analis'];
                $updateData['nama_analis'] = strtoupper(trim($input['nama_analis']));
            } elseif ($step === 'eb') {
                $updateData['eb'] = $input['eb'];
            } elseif ($step === 'tpc') {
                $updateData['tpc'] = $input['tpc'];
            } elseif ($step === 'ym') {
                $updateData['ym'] = $input['ym'];
            }

            DB::beginTransaction();

            try {
                $blending->update($updateData);
                $blending->refresh();

                $hasil = 'PENDING';

                if (
                    !is_null($blending->eb)
                    && !is_null($blending->tpc)
                    && !is_null($blending->ym)
                ) {
                    if (
                        $blending->eb == 0
                        && $blending->tpc == 30
                        && $blending->ym == 0
                    ) {
                        $hasil = 'OK';
                    } else {
                        $hasil = 'NOT OK';
                    }
                }

                $blending->update([
                    'hasil' => $hasil
                ]);

                /**
                 * Existing Digimon -> Production sync.
                 * HANYA dipanggil saat Simpan Final.
                 * Simpan Sementara tidak pernah masuk ke sini.
                 */
                $client = new \GuzzleHttp\Client();

                $apiResponse = $client->request(
                    'POST',
                    env('PRODUCTION_URL') . "api/blending-awal/mikro/{$blending->id}",
                    [
                        'json' => [
                            'hasil' => $hasil,
                        ],
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                        'timeout' => 15,
                    ]
                );

                if ($apiResponse->getStatusCode() !== 200) {
                    throw new \RuntimeException(
                        'Gagal update data Blending Awal Mikro ke Production.'
                    );
                }

                /**
                 * Draft baru dibuang setelah final lokal + API berhasil.
                 */
                BlendingAfterAdjustMikroDraft::where(
                    'blending_after_adjust_mikro_id',
                    $blending->id
                )->delete();

                DB::commit();

                $blending->refresh();

                return response()->json([
                    'status' => 'success',
                    'message' => "Data {$fieldName} berhasil disimpan final.",
                    'hasil' => $hasil,
                    'current_step' => $this->currentStep($blending),
                    'data' => [
                        'shift' => $blending->shift,
                        'nama_analis' => $blending->nama_analis,
                        'eb' => $blending->eb,
                        'tpc' => $blending->tpc,
                        'ym' => $blending->ym,
                    ]
                ], 200);
            } catch (\Throwable $e) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Simpan Final gagal. Draft tetap aman.',
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
