<?php

namespace App\Http\Controllers;

use App\Models\PackagingIncoming;
use App\Models\PackagingPouchSampling;
use App\Models\SamplingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Milon\Barcode\Facades\DNS2DFacade;

class PackagingPouchController extends Controller
{
    public function index(Request $request): View
    {
        $query = PackagingIncoming::query()
            ->with([
                'jenisIncoming',
                'jenisMaterial',
                'samplingStatus',
                'supplier',
            ])
            ->whereHas(
                'jenisIncoming',
                function ($jenisQuery) {
                    $jenisQuery->where(
                        'nama',
                        'Pouch'
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
                            function ($materialQuery) use ($search) {
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

        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'Belum Sampling') {
                $query->where(
                    function ($statusQuery) {
                        $statusQuery
                            ->whereNull('sampling_status_id')
                            ->orWhereHas(
                                'samplingStatus',
                                function ($samplingQuery) {
                                    $samplingQuery->where(
                                        'nama',
                                        'like',
                                        '%Belum%'
                                    );
                                }
                            );
                    }
                );
            }

            if ($status === 'Sudah Sampling') {
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

        $samplingByIncoming = PackagingPouchSampling::query()
            ->whereIn(
                'packaging_incoming_id',
                $incomings->pluck('id')
            )
            ->get()
            ->keyBy('packaging_incoming_id');

        return view(
            'app.rmpm.pouch-menu',
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

        $this->ensurePouch($packagingIncoming);

        $sampling = PackagingPouchSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        return view(
            'app.rmpm.pouch-sampling',
            compact(
                'packagingIncoming',
                'sampling'
            )
        );
    }

    public function resume(
        PackagingIncoming $packagingIncoming
    ): View {
        $packagingIncoming->load([
            'jenisIncoming',
            'jenisMaterial',
            'supplier',
            'samplingStatus',
        ]);

        $this->ensurePouch($packagingIncoming);

        $sampling = PackagingPouchSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->firstOrFail();

        abort_unless(
            $sampling->status_proses === 'final',
            404,
            'Laporan hanya tersedia untuk sampling yang sudah final.'
        );

        return view(
            'app.rmpm.pouch-resume',
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
            'samplingStatus',
        ]);

        $this->ensurePouch($packagingIncoming);

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

        $existingSampling = PackagingPouchSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $isFinal = $saveMode === 'final';

        $rules = [
            'save_mode' => [
                'required',
                'in:draft,final',
            ],

            'qty' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'uom' => [
                'nullable',
                'string',
                'max:200',
            ],

            'jumlah_sampel' => [
                $isFinal ? 'required' : 'nullable',
                'integer',
                'min:1',
                'max:200',
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
                'nullable',
                'array',
                'max:6',
            ],

            'jenis_ketidaksesuaian.*' => [
                'string',
                'distinct',
                'in:Miss Print,Ukuran Tidak Standar,Seal Tidak Standar,Thickness Tidak Standar,Barcode Tidak Terbaca',
            ],

            'jenis_ketidaksesuaian_lainnya' => [
                'nullable',
                'string',
                'max:255',
            ],

            'foto_pengecekan' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'foto' => [
                'nullable',
                'array',
                'max:10',
            ],

            'foto.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'samples' => [
                'nullable',
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

            'samples.*.tebal' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.berat' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.side_seal_1' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.side_seal_2' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.bottom_seal' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'samples.*.bottom_high' => [
                'nullable',
                'numeric',
                'min:0',
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

            'barcode' => [
                'nullable',
                'string',
                'max:500',
            ],

            'qr_code' => [
                'nullable',
                'string',
                'max:500',
            ],

            'samples.*.drop_test' => [
                'nullable',
                'in:OK,NOK',
            ],

            'samples.*.pretest' => [
                'nullable',
                'in:OK,NOK',
            ],

            'thickness' => [
                'nullable',
                'array',
                'size:3',
            ],

            'thickness.*.nilai_1' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'thickness.*.nilai_2' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];

        if (
            $isFinal
            && (
                ! $existingSampling
                || ! $existingSampling->foto_pengecekan
            )
        ) {
            $rules['foto_pengecekan'][0] =
                'required';
        }

        $adaKetidaksesuaian =
            $request->input(
                'konfirmasi_ketidaksesuaian'
            ) === 'Ada';

        if ($isFinal && $adaKetidaksesuaian) {
            $rules['jenis_ketidaksesuaian'][0] =
                'required';

            $existingPhotos = array_values(
                array_filter(
                    $existingSampling?->foto_ketidaksesuaian
                    ?? []
                )
            );

            if (count($existingPhotos) === 0) {
                $rules['foto'][0] = 'required';
            }
        }

        if (
            $isFinal
            && ! $adaKetidaksesuaian
        ) {
            $request->merge([
                'konfirmasi_ketidaksesuaian' =>
                    'Tidak Ada',

                'jenis_ketidaksesuaian' => [],
            ]);
        }

        $validated = $request->validate(
            $rules,
            [
                'jumlah_sampel.required' =>
                    'Jumlah sampel wajib diisi.',

                'rekomendasi.required' =>
                    'Rekomendasi wajib dipilih untuk Simpan Final.',

                'konfirmasi_ketidaksesuaian.required' =>
                    'Konfirmasi ketidaksesuaian wajib dipilih untuk Simpan Final.',

                'foto_pengecekan.required' =>
                    'Foto pengecekan wajib diunggah untuk Simpan Final.',

                'foto.required' =>
                    'Minimal satu foto ketidaksesuaian wajib diunggah.',

                'foto.max' =>
                    'Maksimal 10 foto ketidaksesuaian.',

                'foto.*.image' =>
                    'Semua file ketidaksesuaian harus berupa gambar.',

                'foto.*.max' =>
                    'Ukuran setiap foto maksimal 5 MB.',

                'jenis_ketidaksesuaian.required' =>
                    'Pilih minimal satu jenis ketidaksesuaian.',
            ]
        );

        $jenisKetidaksesuaianFinal = array_values(
            array_filter(
                $validated['jenis_ketidaksesuaian'] ?? []
            )
        );

        $jenisLainnya = trim(
            (string) (
                $validated[
                    'jenis_ketidaksesuaian_lainnya'
                ] ?? ''
            )
        );

        if ($jenisLainnya !== '') {
            $jenisKetidaksesuaianFinal[] =
                $jenisLainnya;
        }

        $jenisKetidaksesuaianFinal = array_values(
            array_unique(
                $jenisKetidaksesuaianFinal
            )
        );

        if (
            $isFinal
            && $adaKetidaksesuaian
            && count($jenisKetidaksesuaianFinal) === 0
        ) {
            throw ValidationException::withMessages([
                'jenis_ketidaksesuaian' => [
                    'Pilih jenis ketidaksesuaian atau isi field Lainnya.',
                ],
            ]);
        }

        $existingPhotoCount = count(
            array_filter(
                $existingSampling?->foto_ketidaksesuaian
                ?? []
            )
        );

        $newPhotoCount = count(
            $request->file('foto', [])
        );

        if ($existingPhotoCount + $newPhotoCount > 10) {
            throw ValidationException::withMessages([
                'foto' => [
                    'Total foto ketidaksesuaian maksimal 10 termasuk foto yang sudah tersimpan.',
                ],
            ]);
        }

        DB::transaction(
            function () use (
                $request,
                $validated,
                $packagingIncoming,
                $existingSampling,
                $isFinal,
                $jenisKetidaksesuaianFinal
            ): void {
                $sampling = $existingSampling
                    ?? new PackagingPouchSampling();

                $fotoPengecekanPath =
                    $sampling->foto_pengecekan;

                $fotoKetidaksesuaianPaths =
                    array_values(
                        array_filter(
                            $sampling->foto_ketidaksesuaian
                            ?? []
                        )
                    );

                if (
                    $request->hasFile(
                        'foto_pengecekan'
                    )
                ) {
                    if (
                        $fotoPengecekanPath
                        && Storage::disk('public')->exists(
                            $fotoPengecekanPath
                        )
                    ) {
                        Storage::disk('public')->delete(
                            $fotoPengecekanPath
                        );
                    }

                    $fotoPengecekanPath = $request
                        ->file('foto_pengecekan')
                        ->store(
                            'packaging-pouch/pengecekan',
                            'public'
                        );
                }

                if ($request->hasFile('foto')) {
                    foreach (
                        $request->file('foto', [])
                        as $photo
                    ) {
                        $fotoKetidaksesuaianPaths[] =
                            $photo->store(
                                'packaging-pouch/ketidaksesuaian',
                                'public'
                            );
                    }
                }

                if (
                    $isFinal
                    && (
                        $validated[
                            'konfirmasi_ketidaksesuaian'
                        ] ?? null
                    ) !== 'Ada'
                ) {
                    foreach (
                        $fotoKetidaksesuaianPaths
                        as $photoPath
                    ) {
                        if (
                            Storage::disk('public')->exists(
                                $photoPath
                            )
                        ) {
                            Storage::disk('public')->delete(
                                $photoPath
                            );
                        }
                    }

                    $fotoKetidaksesuaianPaths = [];
                }

                $sampling->fill([
                    'packaging_incoming_id' =>
                        $packagingIncoming->id,

                    'status_proses' =>
                        $isFinal
                            ? 'final'
                            : 'draft',

                    'qty' =>
                        $validated['qty'] ?? null,

                    'uom' =>
                        $validated['uom'] ?? null,

                    'jumlah_sampel' =>
                        $validated['jumlah_sampel']
                        ?? 1,

                    'hasil_sampel' =>
                        $this->mergeSingleBarcodeQrSample(
                            $validated['samples'] ?? [],
                            $sampling->hasil_sampel ?? [],
                            $validated['barcode'] ?? null,
                            $validated['qr_code'] ?? null
                        ),

                    'hasil_thickness' =>
                        array_values(
                            $validated['thickness']
                            ?? [
                                ['nilai_1' => null, 'nilai_2' => null],
                                ['nilai_1' => null, 'nilai_2' => null],
                                ['nilai_1' => null, 'nilai_2' => null],
                            ]
                        ),

                    'coa' =>
                        $validated['coa'] ?? null,

                    'rekomendasi' =>
                        $validated['rekomendasi']
                        ?? null,

                    'konfirmasi_ketidaksesuaian' =>
                        $validated[
                            'konfirmasi_ketidaksesuaian'
                        ] ?? null,

                    'jenis_ketidaksesuaian' =>
                        $jenisKetidaksesuaianFinal,

                    'foto_pengecekan' =>
                        $fotoPengecekanPath,

                    'foto_ketidaksesuaian' =>
                        array_values(
                            $fotoKetidaksesuaianPaths
                        ),

                    'keterangan' =>
                        $validated['keterangan']
                        ?? null,

                    'updated_by' =>
                        auth()->id(),
                ]);

                if (! $sampling->exists) {
                    $sampling->created_by =
                        auth()->id();
                }

                $sampling->save();

                if ($isFinal) {
                    $sudahSamplingId =
                        SamplingStatus::query()
                            ->where(
                                function ($statusQuery) {
                                    $statusQuery
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

                    if (! $sudahSamplingId) {
                        abort(
                            422,
                            'Master status Sudah Sampling belum tersedia.'
                        );
                    }

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
                $isFinal
                    ? 'Data sampling Pouch berhasil disimpan final.'
                    : 'Data sampling Pouch berhasil disimpan sementara.',

            'redirect_url' =>
                $isFinal
                    ? route(
                        'rmpm.pm.pouch.resume',
                        $packagingIncoming
                    )
                    : route(
                        'rmpm.pm.pouch.sampling',
                        $packagingIncoming
                    ),
        ]);
    }

    private function mergeSingleBarcodeQrSample(
        array $submittedSamples,
        array $existingSamples,
        ?string $barcode,
        ?string $qrCode
    ): array {
        $samples = array_values(
            $submittedSamples
        );

        if (count($samples) === 0) {
            $samples[] = [];
        }

        foreach ($samples as $index => &$sample) {
            unset(
                $sample['barcode_qr'],
                $sample['barcode'],
                $sample['qr_code']
            );

            if ($index === 0) {
                $sample['barcode'] =
                    filled($barcode)
                        ? trim($barcode)
                        : null;

                $sample['qr_code'] =
                    filled($qrCode)
                        ? trim($qrCode)
                        : null;
            }
        }

        unset($sample);

        return $samples;
    }

    public function getQRCode(
        int $id
    ): JsonResponse {
        $packagingIncoming = PackagingIncoming::query()
            ->with([
                'jenisIncoming',
                'jenisMaterial',
                'samplingStatus',
            ])
            ->findOrFail($id);

        $this->ensurePouch($packagingIncoming);

        $sampling = PackagingPouchSampling::query()
            ->where(
                'packaging_incoming_id',
                $packagingIncoming->id
            )
            ->first();

        $qrText =
            $sampling?->status_proses === 'final'
                ? route(
                    'rmpm.pm.pouch.resume',
                    $packagingIncoming
                )
                : route(
                    'rmpm.pm.pouch.sampling',
                    $packagingIncoming
                );

        $qrCode = DNS2DFacade::getBarcodePNG(
            $qrText,
            'QRCODE'
        );

        $tanggal = optional(
            $sampling?->created_at
                ?? $packagingIncoming->created_at
        )->format('Y-m-d')
            ?? now()->format('Y-m-d');

        $label =
            'POUCH/' .
            ($packagingIncoming->no_spb ?? '-') .
            '/' .
            $tanggal .
            '/' .
            $packagingIncoming->id;

        return response()->json([
            'status' => 'success',
            'qrCode' => $qrCode,
            'label' => $label,
            'url' => $qrText,
        ]);
    }

    private function ensurePouch(
        PackagingIncoming $packagingIncoming
    ): void {
        abort_unless(
            strtolower(
                trim(
                    $packagingIncoming
                        ->jenisIncoming
                        ?->nama
                    ?? ''
                )
            ) === 'pouch',
            404,
            'Data incoming bukan kategori Pouch.'
        );
    }
}