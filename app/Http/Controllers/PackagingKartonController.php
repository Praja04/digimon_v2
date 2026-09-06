<?php

namespace App\Http\Controllers;

use App\Models\PackagingIncoming;
use App\Models\PackagingKartonSampling;
use App\Models\PackagingKartonSamplingDraft;
use App\Models\SamplingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PackagingKartonController extends Controller
{
    public function index(Request $request): View
    {
        $query = PackagingIncoming::query()
            ->with([
                'jenisIncoming',
                'jenisMaterial',
                'samplingStatus',
            ])
            ->whereHas(
                'jenisIncoming',
                fn ($query) => $query->whereIn('nama', ['Karton', 'Kardus'])
            )
            ->latest('tanggal_kedatangan')
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($query) use ($search) {
                $query
                    ->where('no_spb', 'like', "%{$search}%")
                    ->orWhere('mid', 'like', "%{$search}%")
                    ->orWhereHas(
                        'jenisMaterial',
                        fn ($materialQuery) =>
                            $materialQuery->where('nama', 'like', "%{$search}%")
                    );
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'Belum Sampling') {
                $query
                    ->whereNotIn(
                        'id',
                        PackagingKartonSampling::query()
                            ->select('packaging_incoming_id')
                    )
                    ->whereNotIn(
                        'id',
                        PackagingKartonSamplingDraft::query()
                            ->select('packaging_incoming_id')
                    );
            }

            if ($status === 'Draft') {
                $query
                    ->whereIn(
                        'id',
                        PackagingKartonSamplingDraft::query()
                            ->select('packaging_incoming_id')
                    )
                    ->whereNotIn(
                        'id',
                        PackagingKartonSampling::query()
                            ->select('packaging_incoming_id')
                    );
            }

            if ($status === 'Sudah Sampling') {
                $query->whereIn(
                    'id',
                    PackagingKartonSampling::query()
                        ->select('packaging_incoming_id')
                );
            }
        }

        $incomings = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'app.rmpm.karton-menu',
            compact('incomings')
        );
    }

    public function sampling(
        PackagingIncoming $packagingIncoming
    ): View {
        $packagingIncoming->load([
            'jenisIncoming',
            'jenisMaterial',
            'supplier',
            'samplingStatus',
        ]);

        $this->ensureKarton($packagingIncoming);

        $finalSampling = PackagingKartonSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $draft = PackagingKartonSamplingDraft::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $sampling = $draft ?? $finalSampling;

        if ($draft) {
            $draft->setAttribute('status_proses', 'draft');
        }

        return view(
            'app.rmpm.karton-sampling',
            compact(
                'packagingIncoming',
                'sampling',
                'draft',
                'finalSampling'
            )
        );
    }

    public function storeSampling(
        Request $request,
        PackagingIncoming $packagingIncoming
    ): JsonResponse {
        $packagingIncoming->loadMissing([
            'jenisIncoming',
            'jenisMaterial',
        ]);

        $this->ensureKarton($packagingIncoming);

        $saveMode = $request->input(
            'save_mode',
            'final'
        );

        if (! in_array($saveMode, ['draft', 'final'], true)) {
            $saveMode = 'final';
        }

        $isFinal = $saveMode === 'final';

        $existingFinal = PackagingKartonSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        /*
         * Setelah sampling sudah FINAL:
         * - Foreman boleh melakukan koreksi.
         * - Role selain Foreman tidak boleh mengubah data lagi.
         */
        $this->ensureFinalCanBeChanged(
            $existingFinal
        );

        /*
         * Koreksi data FINAL oleh Foreman harus langsung disimpan sebagai FINAL,
         * bukan dibuat Draft baru di atas data Final yang sudah ada.
         */
        if (
            $existingFinal
            && $this->isForeman()
            && $saveMode !== 'final'
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Data sudah final. Koreksi oleh Foreman harus disimpan melalui Simpan Koreksi.',
            ], 422);
        }

        $existingDraft = PackagingKartonSamplingDraft::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $existingSource = $existingDraft ?? $existingFinal;

        $existingFoto = array_values(
            array_filter(
                is_array($existingSource?->foto)
                    ? $existingSource->foto
                    : []
            )
        );

        $existingFotoKetidaksesuaian = array_values(
            array_filter(
                is_array($existingSource?->foto_ketidaksesuaian)
                    ? $existingSource->foto_ketidaksesuaian
                    : []
            )
        );

        $adaKetidaksesuaian =
            $request->input('konfirmasi_ketidaksesuaian') === 'Ada';

        if (! $adaKetidaksesuaian) {
            $request->merge([
                'konfirmasi_ketidaksesuaian' =>
                    $request->filled('konfirmasi_ketidaksesuaian')
                        ? 'Tidak Ada'
                        : null,
                'jenis_ketidaksesuaian' => [],
                'jenis_ketidaksesuaian_lainnya' => null,
            ]);
        }

        $rules = [
            'save_mode' => [
                'required',
                'in:draft,final',
            ],

            'jumlah_sampel' => [
                $isFinal ? 'required' : 'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'no_batch' => [
                'nullable',
                'string',
                'max:100',
            ],

            'samples' => [
                $isFinal ? 'required' : 'nullable',
                'array',
            ],

            'samples.*.panjang' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.lebar' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.tinggi' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.bct' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.scan_barcode' => [
                'nullable',
                'in:Terbaca,Tidak Terbaca',
            ],

            'samples.*.no_batch_lot' => [
                'nullable',
                'string',
                'max:150',
            ],

            'samples.*.no_barcode' => [
                'nullable',
                'string',
                'max:255',
            ],

            'samples.*.design' => [
                'nullable',
                'in:OK,NOK',
            ],

            'samples.*.warna' => [
                'nullable',
                'in:OK,NOK',
            ],

            'samples.*.tulisan' => [
                'nullable',
                'in:OK,NOK',
            ],

            'samples.*.foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'samples.*.berat' => [
                'nullable',
                'integer',
                'min:0',
                'max:20',
            ],

            'samples.*.hasil_berat' => [
                'nullable',
                'in:OK,NOK',
            ],

            'gramasi' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'coa' => [
                'nullable',
                'in:Ada,Tidak Ada',
            ],

            'rekomendasi' => [
                $isFinal ? 'required' : 'nullable',
                'in:Diterima,Diterima Bersyarat,Ditolak,WIP',
            ],

            'konfirmasi_ketidaksesuaian' => [
                $isFinal ? 'required' : 'nullable',
                'in:Ada,Tidak Ada',
            ],

            'jenis_ketidaksesuaian' => [
                (
                    $isFinal
                    && $adaKetidaksesuaian
                )
                    ? 'required'
                    : 'nullable',
                'array',
                'max:10',
            ],

            'jenis_ketidaksesuaian.*' => [
                'string',
                'distinct',
                'max:255',
            ],

            'jenis_ketidaksesuaian_lainnya' => [
                'nullable',
                'string',
                'max:255',
            ],

            'foto' => [
                'nullable',
                'array',
                'max:' . max(
                    0,
                    10 - count($existingFoto)
                ),
            ],

            'foto.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'foto_ketidaksesuaian' => [
                'nullable',
                'array',
                'max:' . max(
                    0,
                    10 - count(
                        $existingFotoKetidaksesuaian
                    )
                ),
            ],

            'foto_ketidaksesuaian.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];

        $validator = Validator::make(
            $request->all(),
            $rules,
            [
                'jumlah_sampel.required' =>
                    'Jumlah sampel wajib diisi.',

                'rekomendasi.required' =>
                    'Rekomendasi wajib dipilih.',

                'konfirmasi_ketidaksesuaian.required' =>
                    'Konfirmasi ketidaksesuaian wajib dipilih.',

                'jenis_ketidaksesuaian.required' =>
                    'Pilih minimal satu jenis ketidaksesuaian.',

                'foto.max' =>
                    'Jumlah total foto pengecekan maksimal 10.',

                'foto_ketidaksesuaian.max' =>
                    'Jumlah total foto ketidaksesuaian maksimal 10.',
            ]
        );

        $validator->after(
            function ($validator) use (
                $request,
                $isFinal,
                $adaKetidaksesuaian,
                $existingFoto,
                $existingFotoKetidaksesuaian
            ) {
                $newFotoCount = count(
                    $request->file('foto', [])
                );

                $newFotoKetidaksesuaianCount = count(
                    $request->file(
                        'foto_ketidaksesuaian',
                        []
                    )
                );

                if (
                    count($existingFoto)
                    + $newFotoCount
                    > 10
                ) {
                    $validator->errors()->add(
                        'foto',
                        'Jumlah total foto pengecekan maksimal 10.'
                    );
                }

                if (
                    count($existingFotoKetidaksesuaian)
                    + $newFotoKetidaksesuaianCount
                    > 10
                ) {
                    $validator->errors()->add(
                        'foto_ketidaksesuaian',
                        'Jumlah total foto ketidaksesuaian maksimal 10.'
                    );
                }

                if (! $isFinal) {
                    return;
                }

                if (
                    count($existingFoto)
                    + $newFotoCount
                    < 1
                ) {
                    $validator->errors()->add(
                        'foto',
                        'Foto pengecekan wajib diunggah.'
                    );
                }

                if (! $adaKetidaksesuaian) {
                    return;
                }

                $selectedJenis = $request->input(
                    'jenis_ketidaksesuaian',
                    []
                );

                $customJenis = trim(
                    (string) $request->input(
                        'jenis_ketidaksesuaian_lainnya',
                        ''
                    )
                );

                if (
                    count($selectedJenis) < 1
                    && $customJenis === ''
                ) {
                    $validator->errors()->add(
                        'jenis_ketidaksesuaian',
                        'Pilih minimal satu jenis ketidaksesuaian.'
                    );
                }

                if (
                    in_array(
                        'Lainnya',
                        $selectedJenis,
                        true
                    )
                    && $customJenis === ''
                ) {
                    $validator->errors()->add(
                        'jenis_ketidaksesuaian_lainnya',
                        'Jenis ketidaksesuaian lainnya wajib diisi.'
                    );
                }

                if (
                    count($existingFotoKetidaksesuaian)
                    + $newFotoKetidaksesuaianCount
                    < 1
                ) {
                    $validator->errors()->add(
                        'foto_ketidaksesuaian',
                        'Foto ketidaksesuaian wajib diunggah.'
                    );
                }
            }
        );

        $validated = $validator->validate();

        $jenisKetidaksesuaian =
            array_values(
                array_filter(
                    $validated[
                        'jenis_ketidaksesuaian'
                    ] ?? [],
                    fn ($value) =>
                        $value !== 'Lainnya'
                )
            );

        $jenisKetidaksesuaianLainnya =
            trim(
                (string) (
                    $validated[
                        'jenis_ketidaksesuaian_lainnya'
                    ] ?? ''
                )
            );

        if (
            $jenisKetidaksesuaianLainnya !== ''
            && ! in_array(
                $jenisKetidaksesuaianLainnya,
                $jenisKetidaksesuaian,
                true
            )
        ) {
            $jenisKetidaksesuaian[] =
                $jenisKetidaksesuaianLainnya;
        }

        $result = DB::transaction(
            function () use (
                $request,
                $validated,
                $packagingIncoming,
                $existingDraft,
                $existingFinal,
                $existingFoto,
                $existingFotoKetidaksesuaian,
                $adaKetidaksesuaian,
                $jenisKetidaksesuaian,
                $isFinal
            ) {
                $fotoPaths = $existingFoto;

                foreach (
                    $request->file('foto', [])
                    as $file
                ) {
                    if (count($fotoPaths) >= 10) {
                        break;
                    }

                    $fotoPaths[] = $file->store(
                        'packaging-karton/pengecekan',
                        'public'
                    );
                }

                $fotoKetidaksesuaianPaths =
                    $existingFotoKetidaksesuaian;

                foreach (
                    $request->file(
                        'foto_ketidaksesuaian',
                        []
                    )
                    as $file
                ) {
                    if (
                        count($fotoKetidaksesuaianPaths)
                        >= 10
                    ) {
                        break;
                    }

                    $fotoKetidaksesuaianPaths[] =
                        $file->store(
                            'packaging-karton/ketidaksesuaian',
                            'public'
                        );
                }

                if (! $adaKetidaksesuaian) {
                    foreach (
                        $fotoKetidaksesuaianPaths
                        as $path
                    ) {
                        if (
                            $path
                            && Storage::disk('public')
                                ->exists($path)
                        ) {
                            Storage::disk('public')
                                ->delete($path);
                        }
                    }

                    $fotoKetidaksesuaianPaths = [];
                }

                $currentSampling =
                    $existingDraft
                    ?? $existingFinal;

                $existingSamples =
                    is_array($currentSampling?->hasil_sampel)
                        ? $currentSampling->hasil_sampel
                        : [];

                $submittedSamples =
                    $validated['samples'] ?? [];

                $hasilSampel =
                    $this->mergeKartonSamples(
                        $request,
                        $existingSamples,
                        $submittedSamples,
                        $validated['gramasi'] ?? null
                    );

                $data = [
                    'jumlah_sampel' =>
                        $validated['jumlah_sampel']
                        ?? null,

                    'no_batch' =>
                        $validated['no_batch']
                        ?? null,

                    'hasil_sampel' =>
                        $hasilSampel,

                    'coa' =>
                        $validated['coa']
                        ?? null,

                    'rekomendasi' =>
                        $validated['rekomendasi']
                        ?? null,

                    'konfirmasi_ketidaksesuaian' =>
                        $validated[
                            'konfirmasi_ketidaksesuaian'
                        ] ?? null,

                    'jenis_ketidaksesuaian' =>
                        $adaKetidaksesuaian
                            ? $jenisKetidaksesuaian
                            : [],

                    'foto' =>
                        array_values($fotoPaths),

                    'foto_ketidaksesuaian' =>
                        array_values(
                            $fotoKetidaksesuaianPaths
                        ),

                    'keterangan' =>
                        $validated['keterangan']
                        ?? null,

                    'updated_by' =>
                        auth()->id(),
                ];

                if (! $isFinal) {
                    $draft =
                        $existingDraft
                        ?? new PackagingKartonSamplingDraft([
                            'packaging_incoming_id' =>
                                $packagingIncoming->id,
                        ]);

                    $draft->fill($data);

                    if (! $draft->exists) {
                        $draft->created_by =
                            auth()->id();
                    }

                    $draft->save();

                    return [
                        'record' => $draft,
                        'mode' => 'draft',
                    ];
                }

                $sampling =
                    $existingFinal
                    ?? new PackagingKartonSampling([
                        'packaging_incoming_id' =>
                            $packagingIncoming->id,
                    ]);

                $sampling->fill($data);

                if (! $sampling->exists) {
                    $sampling->created_by =
                        auth()->id();
                }

                $sampling->save();

                $sudahSamplingId =
                    SamplingStatus::query()
                        ->where(
                            function ($query) {
                                $query
                                    ->where(
                                        'nama',
                                        'like',
                                        '%Sudah Sampling%'
                                    )
                                    ->orWhere(
                                        'nama',
                                        'like',
                                        '%Selesai%'
                                    );
                            }
                        )
                        ->value('id');

                if ($sudahSamplingId) {
                    $packagingIncoming->update([
                        'sampling_status_id' =>
                            $sudahSamplingId,
                    ]);
                }

                PackagingKartonSamplingDraft::query()
                    ->where(
                        'packaging_incoming_id',
                        $packagingIncoming->id
                    )
                    ->delete();

                return [
                    'record' => $sampling,
                    'mode' => 'final',
                ];
            }
        );

        return response()->json([
            'success' => true,

            'message' =>
                $isFinal
                    ? 'Data pemeriksaan Berat Karton berhasil disimpan final.'
                    : 'Data pemeriksaan Berat Karton berhasil disimpan sementara.',

            'data' => [
                'id' => $result['record']->id,
                'packaging_incoming_id' =>
                    $result['record']->packaging_incoming_id,
            ],

            'redirect_url' =>
                $isFinal
                    ? route(
                        'rmpm.pm.karton.display',
                        $packagingIncoming
                    )
                    : route(
                        'rmpm.pm.karton.sampling',
                        $packagingIncoming
                    ),
        ]);
    }

    private function mergeKartonSamples(
        Request $request,
        array $existingSamples,
        array $submittedSamples,
        mixed $gramasi
    ): array {
        $mergedSamples = [];

        foreach (
            array_values($submittedSamples)
            as $index => $submittedSample
        ) {
            $existingSample =
                $existingSamples[$index] ?? [];

            $sampleFoto =
                $existingSample['foto'] ?? null;

            $uploadedFoto =
                $request->file(
                    "samples.{$index}.foto"
                );

            if ($uploadedFoto) {
                if (
                    $sampleFoto
                    && Storage::disk('public')
                        ->exists($sampleFoto)
                ) {
                    Storage::disk('public')
                        ->delete($sampleFoto);
                }

                $sampleFoto =
                    $uploadedFoto->store(
                        'packaging-karton/samples',
                        'public'
                    );
            }

            $sample = array_merge(
                $existingSample,
                [
                    'panjang' =>
                        $submittedSample['panjang']
                        ?? null,

                    'lebar' =>
                        $submittedSample['lebar']
                        ?? null,

                    'tinggi' =>
                        $submittedSample['tinggi']
                        ?? null,

                    'bct' =>
                        $submittedSample['bct']
                        ?? null,

                    'scan_barcode' =>
                        $submittedSample[
                            'scan_barcode'
                        ] ?? null,

                    'no_batch_lot' =>
                        $submittedSample[
                            'no_batch_lot'
                        ] ?? null,

                    'no_barcode' =>
                        $submittedSample[
                            'no_barcode'
                        ] ?? null,

                    'design' =>
                        $submittedSample['design']
                        ?? null,

                    'warna' =>
                        $submittedSample['warna']
                        ?? null,

                    'tulisan' =>
                        $submittedSample['tulisan']
                        ?? null,

                    'foto' =>
                        $sampleFoto,

                    'berat' =>
                        $submittedSample['berat']
                        ?? null,

                    'hasil_berat' =>
                        $submittedSample[
                            'hasil_berat'
                        ] ?? null,
                ]
            );

            if ($index === 0) {
                $sample['gramasi'] =
                    $gramasi;
            } else {
                unset($sample['gramasi']);
            }

            $mergedSamples[] = $sample;
        }

        return $mergedSamples;
    }

    private function isForeman(): bool
    {
        return auth()->check()
            && auth()->user()?->role === 'Foreman';
    }

    private function ensureFinalCanBeChanged(
        ?PackagingKartonSampling $sampling
    ): void {
        if (
            $sampling
            && ! $this->isForeman()
        ) {
            abort(
                403,
                'Data sampling yang sudah final hanya dapat dikoreksi oleh Foreman.'
            );
        }
    }

    private function ensureKarton(
        PackagingIncoming $packagingIncoming
    ): void {
        abort_unless(
            in_array(
                $packagingIncoming->jenisIncoming?->nama,
                ['Karton', 'Kardus'],
                true
            ),
            404,
            'Data incoming bukan kategori Karton.'
        );
    }
}
