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
                'required',
                'array',
                'min:1',
            ],

            'samples.*.berat' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.hasil_berat' => [
                'nullable',
                'in:OK,NOK',
            ],

            'samples.*.gramasi' => [
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
                'in:Berat Under,Berat Over,Gramasi Tidak Standar',
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

        DB::transaction(
            function () use (
                $request,
                $validated,
                $packagingIncoming,
                $existingSampling,
                $existingFoto,
                $existingFotoKetidaksesuaian,
                $adaKetidaksesuaian
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

                    'lot_sebelum' =>
                        $validated['lot_sebelum'] ?? null,

                    'lot_setelah' =>
                        $validated['lot_setelah'] ?? null,

                    'hasil_sampel' =>
                        $this->mergeWeightSamples(
                            $sampling->hasil_sampel ?? [],
                            $validated['samples']
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
                            ? array_values(
                                $validated[
                                    'jenis_ketidaksesuaian'
                                ] ?? []
                            )
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

    private function mergeWeightSamples(
        array $existingSamples,
        array $submittedSamples
    ): array {
        $mergedSamples = [];

        foreach (
            array_values($submittedSamples)
            as $index => $submittedSample
        ) {
            $mergedSamples[] = array_merge(
                $existingSamples[$index] ?? [],
                [
                    'berat' =>
                        $submittedSample['berat'] ?? null,

                    'hasil_berat' =>
                        $submittedSample[
                            'hasil_berat'
                        ] ?? null,

                    'gramasi' =>
                        $submittedSample['gramasi'] ?? null,
                ]
            );
        }

        return $mergedSamples;
    }
}
