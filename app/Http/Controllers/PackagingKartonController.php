<?php

namespace App\Http\Controllers;

use App\Models\PackagingIncoming;
use App\Models\PackagingKartonSampling;
use App\Models\SamplingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            if ($request->input('status') === 'Belum Sampling') {
                $query->where(function ($query) {
                    $query
                        ->whereNull('sampling_status_id')
                        ->orWhereHas(
                            'samplingStatus',
                            fn ($statusQuery) =>
                                $statusQuery->where('nama', 'like', '%Belum%')
                        );
                });
            }

            if ($request->input('status') === 'Sudah Sampling') {
                $query->whereHas('samplingStatus', function ($query) {
                    $query->where(function ($nameQuery) {
                        $nameQuery
                            ->where('nama', 'like', '%Sudah%')
                            ->orWhere('nama', 'like', '%Selesai%');
                    });
                });
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

        abort_unless(
            in_array(
                $packagingIncoming->jenisIncoming?->nama,
                ['Karton', 'Kardus'],
                true
            ),
            404,
            'Data incoming bukan kategori Karton.'
        );

        $sampling = PackagingKartonSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        return view(
            'app.rmpm.karton-sampling',
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
        $existingSampling = PackagingKartonSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $existingFoto = array_values(
            array_filter(
                is_array($existingSampling?->foto)
                    ? $existingSampling->foto
                    : []
            )
        );

        $existingFotoKetidaksesuaian = array_values(
            array_filter(
                is_array($existingSampling?->foto_ketidaksesuaian)
                    ? $existingSampling->foto_ketidaksesuaian
                    : []
            )
        );

        $adaKetidaksesuaian =
            $request->input('konfirmasi_ketidaksesuaian') === 'Ada';

        if (! $adaKetidaksesuaian) {
            $request->merge([
                'konfirmasi_ketidaksesuaian' => 'Tidak Ada',
                'jenis_ketidaksesuaian' => [],
                'jenis_ketidaksesuaian_lainnya' => null,
            ]);
        }

        $rules = [
            'jumlah_sampel' => [
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


            'samples' => [
                'required',
                'array',
                'min:1',
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
                'max:5120',
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
                'required',
                'in:Diterima,Diterima Bersyarat,Ditolak,WIP',
            ],

            'konfirmasi_ketidaksesuaian' => [
                'required',
                'in:Ada,Tidak Ada',
            ],

            'jenis_ketidaksesuaian' => [
                $adaKetidaksesuaian ? 'required' : 'nullable',
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
                count($existingFoto) === 0 ? 'required' : 'nullable',
                'array',
                'max:' . max(0, 10 - count($existingFoto)),
            ],

            'foto.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'foto_ketidaksesuaian' => [
                (
                    $adaKetidaksesuaian
                    && count($existingFotoKetidaksesuaian) === 0
                )
                    ? 'required'
                    : 'nullable',
                'array',
                'max:' . max(
                    0,
                    10 - count($existingFotoKetidaksesuaian)
                ),
            ],

            'foto_ketidaksesuaian.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];

        $validated = $request->validate(
            $rules,
            [
                'foto.required' =>
                    'Foto pengecekan wajib diunggah.',

                'foto.max' =>
                    'Jumlah total foto pengecekan maksimal 10.',

                'foto_ketidaksesuaian.required' =>
                    'Foto ketidaksesuaian wajib diunggah.',

                'foto_ketidaksesuaian.max' =>
                    'Jumlah total foto ketidaksesuaian maksimal 10.',

                'jenis_ketidaksesuaian.required' =>
                    'Pilih minimal satu jenis ketidaksesuaian.',
            ]
        );

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
            $adaKetidaksesuaian
            && in_array(
                'Lainnya',
                $validated[
                    'jenis_ketidaksesuaian'
                ] ?? [],
                true
            )
            && $jenisKetidaksesuaianLainnya === ''
        ) {
            return response()->json([
                'message' =>
                    'Jenis ketidaksesuaian lainnya wajib diisi.',

                'errors' => [
                    'jenis_ketidaksesuaian_lainnya' => [
                        'Jenis ketidaksesuaian lainnya wajib diisi.',
                    ],
                ],
            ], 422);
        }

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

        DB::transaction(
            function () use (
                $request,
                $validated,
                $packagingIncoming,
                $existingSampling,
                $existingFoto,
                $existingFotoKetidaksesuaian,
                $adaKetidaksesuaian,
                $jenisKetidaksesuaian
            ): void {
                $sampling =
                    $existingSampling
                    ?? new PackagingKartonSampling([
                        'packaging_incoming_id' =>
                            $packagingIncoming->id,
                    ]);

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

                $sampling->fill([
                    'jumlah_sampel' =>
                        $validated['jumlah_sampel'],

                    'no_batch' =>
                        $validated['no_batch'] ?? null,


                    'hasil_sampel' =>
                        $this->mergeKartonSamples(
                            $request,
                            $sampling->hasil_sampel ?? [],
                            $validated['samples'],
                            $validated['gramasi'] ?? null
                        ),

                    'coa' =>
                        $validated['coa'] ?? null,

                    'rekomendasi' =>
                        $validated['rekomendasi'],

                    'konfirmasi_ketidaksesuaian' =>
                        $validated[
                            'konfirmasi_ketidaksesuaian'
                        ],

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
                        $validated['keterangan'] ?? null,

                    'updated_by' =>
                        auth()->id(),
                ]);

                if (! $sampling->exists) {
                    $sampling->created_by =
                        auth()->id();
                }

                $sampling->save();

                $sudahSamplingId =
                    SamplingStatus::query()
                        ->where(
                            'nama',
                            'like',
                            '%Sudah Sampling%'
                        )
                        ->value('id');

                if ($sudahSamplingId) {
                    $packagingIncoming->update([
                        'sampling_status_id' =>
                            $sudahSamplingId,
                    ]);
                }
            }
        );

        return response()->json([
            'success' => true,

            'message' =>
                'Data pemeriksaan Berat Karton berhasil disimpan.',

            'redirect_url' =>
                route(
                    'rmpm.pm.karton.display',
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
                        $submittedSample[
                            'panjang'
                        ] ?? null,

                    'lebar' =>
                        $submittedSample[
                            'lebar'
                        ] ?? null,

                    'tinggi' =>
                        $submittedSample[
                            'tinggi'
                        ] ?? null,

                    'bct' =>
                        $submittedSample[
                            'bct'
                        ] ?? null,

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
                        $submittedSample[
                            'design'
                        ] ?? null,

                    'warna' =>
                        $submittedSample[
                            'warna'
                        ] ?? null,

                    'tulisan' =>
                        $submittedSample[
                            'tulisan'
                        ] ?? null,

                    'foto' =>
                        $sampleFoto,

                    'berat' =>
                        $submittedSample[
                            'berat'
                        ] ?? null,

                    'hasil_berat' =>
                        $submittedSample[
                            'hasil_berat'
                        ] ?? null,
                ]
            );

            /*
             * Requirement:
             * 1 SPB hanya memiliki 1 data gramasi.
             * Gramasi disimpan pada sampel pertama
             * agar tidak perlu menambah kolom database.
             */
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
}