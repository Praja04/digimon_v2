

namespace App\Http\Controllers;

use App\Models\PackagingIncoming;
use App\Models\PackagingInnerOuterSampling;
use App\Models\SamplingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PackagingInnerOuterController extends Controller
{
    public function index(Request $request): View
    {
        $query = PackagingIncoming::query()
            ->with([
                'jenisIncoming',
                'jenisMaterial',
                'supplier',
                'samplingStatus',
            ])
            ->whereHas(
                'jenisIncoming',
                function ($jenisQuery) {
                    $jenisQuery->whereIn(
                        'nama',
                        [
                            'Inner',
                            'Outer',
                            'Inner / Outer',
                            'Outers',
                        ]
                    );
                }
            )
            ->latest('tanggal_kedatangan')
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim(
                $request->input('search')
            );

            $query->where(
                function ($incomingQuery) use ($search) {
                    $incomingQuery
                        ->where(
                            'no_spb',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'mid',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'jenisMaterial',
                            function ($materialQuery) use (
                                $search
                            ) {
                                $materialQuery->where(
                                    'nama',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );
                }
            );
        }

        if ($request->filled('jenis_incoming')) {
            $jenisIncoming =
                $request->input('jenis_incoming');

            $query->whereHas(
                'jenisIncoming',
                function ($jenisQuery) use (
                    $jenisIncoming
                ) {
                    if (
                        $jenisIncoming === 'Inner'
                    ) {
                        $jenisQuery->whereIn(
                            'nama',
                            [
                                'Inner',
                                'Inner / Outer',
                            ]
                        );

                        return;
                    }

                    if (
                        $jenisIncoming === 'Outer'
                    ) {
                        $jenisQuery->whereIn(
                            'nama',
                            [
                                'Outer',
                                'Outers',
                            ]
                        );
                    }
                }
            );
        }

        if ($request->filled('status')) {
            $status = $request->input('status');

            if (
                $status === 'Belum Sampling'
            ) {
                $query->where(
                    function ($statusQuery) {
                        $statusQuery
                            ->whereNull(
                                'sampling_status_id'
                            )
                            ->orWhereHas(
                                'samplingStatus',
                                function (
                                    $samplingQuery
                                ) {
                                    $samplingQuery
                                        ->where(
                                            'nama',
                                            'like',
                                            '%Belum%'
                                        );
                                }
                            );
                    }
                );
            }

            if (
                $status === 'Sudah Sampling'
            ) {
                $query->whereHas(
                    'samplingStatus',
                    function ($samplingQuery) {
                        $samplingQuery->where(
                            function ($nameQuery) {
                                $nameQuery
                                    ->where(
                                        'nama',
                                        'like',
                                        '%Sudah%'
                                    )
                                    ->orWhere(
                                        'nama',
                                        'like',
                                        '%Selesai%'
                                    );
                            }
                        );
                    }
                );
            }
        }

        $incomings = $query
            ->paginate(10)
            ->withQueryString();

        $samplingByIncoming =
            PackagingInnerOuterSampling::query()
                ->whereIn(
                    'packaging_incoming_id',
                    $incomings->pluck('id')
                )
                ->get()
                ->keyBy(
                    'packaging_incoming_id'
                );

        return view(
            'app.rmpm.inner-outer',
            compact(
                'incomings',
                'samplingByIncoming'
            )
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

        $allowedJenis = [
            'Inner',
            'Outer',
            'Inner / Outer',
            'Outers',
        ];

        abort_unless(
            in_array(
                $packagingIncoming
                    ->jenisIncoming
                    ?->nama,
                $allowedJenis,
                true
            ),
            404,
            'Data incoming bukan kategori Inner atau Outer.'
        );

        $sampling =
            PackagingInnerOuterSampling::query()
                ->where(
                    'packaging_incoming_id',
                    $packagingIncoming->id
                )
                ->first();

        return view(
            'app.rmpm.inner-outer-sampling',
            compact(
                'packagingIncoming',
                'sampling'
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

        $allowedJenis = [
            'Inner',
            'Outer',
            'Inner / Outer',
            'Outers',
        ];

        abort_unless(
            in_array(
                $packagingIncoming
                    ->jenisIncoming
                    ?->nama,
                $allowedJenis,
                true
            ),
            404,
            'Data incoming bukan kategori Inner atau Outer.'
        );

        $jenisIncomingName = strtolower(
            $packagingIncoming
                ->jenisIncoming
                ?->nama
            ?? ''
        );

        $jenisMaterialName = strtolower(
            $packagingIncoming
                ->jenisMaterial
                ?->nama
            ?? ''
        );

        $isOuter =
            (
                str_contains(
                    $jenisIncomingName,
                    'outer'
                )
                &&
                ! str_contains(
                    $jenisIncomingName,
                    'inner'
                )
            )
            ||
            str_contains(
                $jenisMaterialName,
                'outer'
            );

        $saveMode = $request->input(
            'save_mode',
            'final'
        );

        if (! in_array(
            $saveMode,
            ['draft', 'final'],
            true
        )) {
            $saveMode = 'final';
        }

        $isFinal =
            $saveMode === 'final';

        $rules = [
            'save_mode' => [
                'required',
                'in:draft,final',
            ],

            'jumlah_sampel' => [
                $isFinal ? 'required' : 'nullable',
                'required',
                'integer',
                'min:1',
                'max:50',
            ],

            'no_batch' => [
                'nullable',
                'string',
                'max:100',
            ],

            'lot_sebelum' => [
                'nullable',
                'string',
                'max:100',
            ],

            'lot_setelah' => [
                'nullable',
                'string',
                'max:100',
            ],

            'samples' => [
                'nullable',
                'array',
            ],

            'samples.*.berat_gross' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.inside_core' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.lebar' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.pitch' =>
                $isOuter
                    ? [
                        'nullable',
                        'numeric',
                        'min:0',
                    ]
                    : [
                        'nullable',
                    ],

            'samples.*.thickness' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.arah_vertikal' => [
                'nullable',
                'in:V,-',
            ],

            'samples.*.arah_terbalik' => [
                'nullable',
                'in:V,-',
            ],

            'samples.*.laminasi' => [
                'nullable',
                'in:OK,NG',
            ],

            'samples.*.barcode' => [
                'nullable',
                'string',
                'max:100',
            ],

            'samples.*.design' => [
                'nullable',
                'in:OK,NG',
            ],

            'samples.*.warna' => [
                'nullable',
                'in:OK,NG',
            ],

            'samples.*.tulisan' => [
                'nullable',
                'string',
                'max:255',
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
                'nullable',
                'in:Ada,Tidak Ada',
            ],

            'jenis_ketidaksesuaian' => [
                'nullable',
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

            'foto_pengecekan' => [
                'nullable',
                'array',
                'max:10',
            ],

            'foto_pengecekan.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'foto_ketidaksesuaian' => [
                'nullable',
                'array',
                'max:10',
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

        $existingSampling = PackagingInnerOuterSampling::query()
            ->where('packaging_incoming_id', $packagingIncoming->id)
            ->first();

        /*
         * Data FINAL hanya boleh dikoreksi oleh Foreman.
         * Role lain tetap boleh membuat Draft/Final selama datanya belum Final.
         */
        $this->ensureFinalCanBeChanged(
            $existingSampling
        );

        $existingFotoPengecekan = collect(
            $existingSampling?->foto_pengecekan ?? []
        )->filter()->values()->all();

        $existingFotoKetidaksesuaian = collect(
            $existingSampling?->foto_ketidaksesuaian ?? []
        )->filter()->values()->all();

        $adaKetidaksesuaian =
            $request->input('konfirmasi_ketidaksesuaian') === 'Ada';

        if (! $adaKetidaksesuaian) {
            $request->merge([
                'konfirmasi_ketidaksesuaian' => 'Tidak Ada',
                'jenis_ketidaksesuaian' => [],
                'jenis_ketidaksesuaian_lainnya' => null,
            ]);
        }

        $validator = Validator::make(
            $request->all(),
            $rules,
            [
                'jumlah_sampel.required' =>
                    'Jumlah sampel wajib diisi.',

                'rekomendasi.required' =>
                    'Rekomendasi wajib dipilih.',

                'jenis_ketidaksesuaian.array' =>
                    'Jenis ketidaksesuaian harus berupa pilihan.',

                'foto_pengecekan.max' =>
                    'Foto pengecekan maksimal 10 foto.',

                'foto_ketidaksesuaian.max' =>
                    'Foto ketidaksesuaian maksimal 10 foto.',
            ]
        );

        $validator->after(
            function ($validator) use (
                $request,
                $isFinal,
                $adaKetidaksesuaian,
                $existingFotoPengecekan,
                $existingFotoKetidaksesuaian
            ) {
                $newFotoPengecekan = count(
                    $request->file('foto_pengecekan', [])
                );

                $newFotoKetidaksesuaian = count(
                    $request->file('foto_ketidaksesuaian', [])
                );

                if (
                    count($existingFotoPengecekan)
                    + $newFotoPengecekan
                    > 10
                ) {
                    $validator->errors()->add(
                        'foto_pengecekan',
                        'Total foto pengecekan maksimal 10 foto.'
                    );
                }

                if (
                    count($existingFotoKetidaksesuaian)
                    + $newFotoKetidaksesuaian
                    > 10
                ) {
                    $validator->errors()->add(
                        'foto_ketidaksesuaian',
                        'Total foto ketidaksesuaian maksimal 10 foto.'
                    );
                }

                if (! $isFinal) {
                    return;
                }

                if (
                    count($existingFotoPengecekan)
                    + $newFotoPengecekan
                    < 1
                ) {
                    $validator->errors()->add(
                        'foto_pengecekan',
                        'Foto pengecekan wajib minimal 1 foto.'
                    );
                }

                if (! $adaKetidaksesuaian) {
                    return;
                }

                $selectedJenis =
                    $request->input(
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
                        'Pilih minimal satu jenis ketidaksesuaian atau isi jenis lainnya.'
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
                    + $newFotoKetidaksesuaian
                    < 1
                ) {
                    $validator->errors()->add(
                        'foto_ketidaksesuaian',
                        'Foto ketidaksesuaian wajib minimal 1 foto.'
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

        $sampling = DB::transaction(
            function () use (
                $request,
                $validated,
                $packagingIncoming,
                $isOuter,
                $isFinal,
                $jenisKetidaksesuaian
            ) {
                $sampling =
                    PackagingInnerOuterSampling::query()
                        ->firstOrNew([
                            'packaging_incoming_id' =>
                                $packagingIncoming->id,
                        ]);

                $fotoPengecekanPaths = collect(
                    $sampling->foto_pengecekan ?? []
                )->filter()->values()->all();

                $fotoKetidaksesuaianPaths = collect(
                    $sampling->foto_ketidaksesuaian ?? []
                )->filter()->values()->all();

                foreach (
                    $request->file('foto_pengecekan', [])
                    as $photo
                ) {
                    $fotoPengecekanPaths[] = $photo->store(
                        'packaging-inner-outer/pengecekan',
                        'public'
                    );
                }

                foreach (
                    $request->file('foto_ketidaksesuaian', [])
                    as $photo
                ) {
                    $fotoKetidaksesuaianPaths[] = $photo->store(
                        'packaging-inner-outer/ketidaksesuaian',
                        'public'
                    );
                }

                if (
                    ($validated['konfirmasi_ketidaksesuaian'] ?? 'Tidak Ada')
                    !== 'Ada'
                ) {
                    foreach ($fotoKetidaksesuaianPaths as $path) {
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }

                    $fotoKetidaksesuaianPaths = [];
                }

                $hasilSampel = collect(
                    $validated['samples'] ?? []
                )
                    ->values()
                    ->map(
                        function ($sample) use (
                            $isOuter
                        ) {
                            if (! $isOuter) {
                                $sample['pitch'] = '-';
                            }

                            return $sample;
                        }
                    )
                    ->all();

                $sampling->fill([
                    'status_proses' =>
                        $isFinal
                            ? 'final'
                            : 'draft',

                    'jumlah_sampel' =>
                        $validated[
                            'jumlah_sampel'
                        ],

                    'no_batch' =>
                        $validated[
                            'no_batch'
                        ]
                        ?? null,

                    'lot_sebelum' =>
                        $validated[
                            'lot_sebelum'
                        ]
                        ?? null,

                    'lot_setelah' =>
                        $validated[
                            'lot_setelah'
                        ]
                        ?? null,

                    'hasil_sampel' =>
                        $hasilSampel,

                    'coa' =>
                        $validated['coa']
                        ?? null,

                    'rekomendasi' =>
                        $validated[
                            'rekomendasi'
                        ] ?? null,

                    'konfirmasi_ketidaksesuaian' =>
                        $validated[
                            'konfirmasi_ketidaksesuaian'
                        ]
                        ?? null,

                    'jenis_ketidaksesuaian' =>
                        $jenisKetidaksesuaian,

                    'foto_pengecekan' =>
                        $fotoPengecekanPaths,

                    'foto_ketidaksesuaian' =>
                        $fotoKetidaksesuaianPaths,

                    'keterangan' =>
                        $validated[
                            'keterangan'
                        ]
                        ?? null,

                    'updated_by' =>
                        auth()->id(),
                ]);

                if (! $sampling->exists) {
                    $sampling->created_by =
                        auth()->id();
                }

                $sampling->save();

                if (! $isFinal) {
                    return $sampling;
                }

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

                return $sampling;
            }
        );

        return response()->json([
            'success' => true,

            'message' =>
                $isFinal
                    ? 'Data pemeriksaan Inner / Outer berhasil disimpan final.'
                    : 'Data pemeriksaan Inner / Outer berhasil disimpan sementara.',

            'data' => [
                'id' =>
                    $sampling->id,

                'packaging_incoming_id' =>
                    $sampling
                        ->packaging_incoming_id,
            ],

            'redirect_url' =>
                route(
                    'rmpm.pm.inner-outer'
                ),
        ]);
    }

    private function isForeman(): bool
    {
        return auth()->check()
            && auth()->user()?->role === 'Foreman';
    }

    private function ensureFinalCanBeChanged(
        ?PackagingInnerOuterSampling $sampling
    ): void {
        if (
            $sampling?->status_proses === 'final'
            && ! $this->isForeman()
        ) {
            abort(
                403,
                'Data sampling yang sudah final hanya dapat dikoreksi oleh Foreman.'
            );
        }
    }

}