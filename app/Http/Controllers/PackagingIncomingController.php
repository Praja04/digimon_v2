<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;
use App\Models\JenisMaterial;
use App\Models\PackagingIncoming;
use App\Models\PackagingInnerOuterSampling;
use App\Models\PackagingPouchSampling;
use App\Models\SamplingStatus;
use App\Models\Supplier;
use App\Services\WpmApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PackagingIncomingController extends Controller
{
    public function index(
        Request $request,
        WpmApiService $wpmApiService
    ): View
    {
        $jenisIncomings = JenisIncoming::query()
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        $jenisMaterials = JenisMaterial::query()
            ->orderBy('id')
            ->get();

        $suppliers = Supplier::query()
            ->orderBy('id')
            ->get();

        $samplingStatuses = SamplingStatus::query()
            ->orderBy('id')
            ->get();

        $query = PackagingIncoming::query()
            ->with([
                'jenisIncoming',
                'supplier',
                'jenisMaterial',
                'samplingStatus',
            ])
            ->latest();

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
                        ->orWhere(
                            'no_mobil',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'supplier',
                            function ($supplierQuery) use ($search) {
                                $supplierQuery
                                    ->where(
                                        'nama',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'nama_supplier',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
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

        if ($request->filled('jenis_incoming_filter')) {
            $query->where(
                'jenis_incoming_id',
                $request->input('jenis_incoming_filter')
            );
        }

        if ($request->filled('status_filter')) {
            $statusFilter =
                (string) $request->input(
                    'status_filter'
                );

            if ($statusFilter === 'draft') {
                $draftIncomingIds = collect()
                    ->merge(
                        PackagingPouchSampling::query()
                            ->where(
                                'status_proses',
                                'draft'
                            )
                            ->pluck(
                                'packaging_incoming_id'
                            )
                    )
                    ->merge(
                        PackagingInnerOuterSampling::query()
                            ->where(
                                'status_proses',
                                'draft'
                            )
                            ->pluck(
                                'packaging_incoming_id'
                            )
                    )
                    ->unique()
                    ->values();

                $query->whereIn(
                    'id',
                    $draftIncomingIds
                );
            } else {
                $query->where(
                    'sampling_status_id',
                    $statusFilter
                );
            }
        }

        $incomings = $query
            ->paginate(10)
            ->withQueryString();

        $this->attachSamplingProcessStatus(
            $incomings->getCollection()
        );

        if ($request->boolean('partial')) {
            return view(
                'app.rmpm.partials.incoming-list',
                compact(
                    'jenisIncomings',
                    'samplingStatuses',
                    'incomings'
                )
            );
        }

        try {
            $masterBarangWpm =
                $wpmApiService->getMasterBarang();

            $wpmError = null;
        } catch (Throwable $exception) {
            report($exception);

            $masterBarangWpm = [];

            $wpmError =
                'Data MID dari WPM gagal dimuat. Periksa jaringan atau API WPM.';
        }

        return view(
            'app.rmpm.incoming-index',
            compact(
                'jenisIncomings',
                'jenisMaterials',
                'suppliers',
                'samplingStatuses',
                'incomings',
                'masterBarangWpm',
                'wpmError'
            )
        );
    }

    public function store(
        Request $request
    ): JsonResponse {
        $validated = $this->validateIncoming(
            $request
        );

        $defaultStatusId = SamplingStatus::query()
            ->where(
                'nama',
                'like',
                '%Belum Sampling%'
            )
            ->value('id');

        if (! $defaultStatusId) {
            $defaultStatusId = SamplingStatus::query()
                ->orderBy('id')
                ->value('id');
        }

        $incoming = PackagingIncoming::query()
            ->create([
                'no_spb' =>
                    trim($validated['no_spb']),

                'tanggal_kedatangan' =>
                    $validated['tanggal_kedatangan'],

                'jam_kedatangan' =>
                    $validated['jam_kedatangan'] ?? null,

                'jumlah' =>
                    $validated['quantity_incoming'],

                'jumlah_sampel' =>
                    $validated['jumlah_sampel'],

                'jenis_incoming_id' =>
                    $validated['jenis_incoming_id'],

                'jenis_incoming_lainnya' =>
                    $validated['jenis_incoming_lainnya'] ?? null,

                'supplier_id' =>
                    $validated['supplier_id'],

                'supplier_lainnya' =>
                    $validated['supplier_lainnya'] ?? null,

                'jenis_material_id' =>
                    $validated['jenis_material_id'],

                'sampling_status_id' =>
                    $defaultStatusId,

                'mid' =>
                    $this->nullableUpper(
                        $validated['mid'] ?? null
                    ),

                'no_mobil' =>
                    $this->nullableUpper(
                        $validated['no_mobil'] ?? null
                    ),

                'keterangan' =>
                    $validated['keterangan'] ?? null,

                'created_by' =>
                    auth()->id(),
            ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Data incoming berhasil disimpan.',
            'data' => [
                'id' => $incoming->id,
                'no_spb' => $incoming->no_spb,
            ],
        ], 201);
    }

    public function edit(
        PackagingIncoming $packagingIncoming
    ): JsonResponse {
        $this->ensureForeman();
        return response()->json([
            'success' => true,
            'data' => [
                'id' =>
                    $packagingIncoming->id,

                'no_spb' =>
                    $packagingIncoming->no_spb,

                'jenis_incoming_id' =>
                    $packagingIncoming
                        ->jenis_incoming_id,

                'jenis_incoming_lainnya' =>
                    $packagingIncoming
                        ->jenis_incoming_lainnya,

                'supplier_id' =>
                    $packagingIncoming
                        ->supplier_id,

                'supplier_lainnya' =>
                    $packagingIncoming
                        ->supplier_lainnya,

                'jenis_material_id' =>
                    $packagingIncoming
                        ->jenis_material_id,

                'mid' =>
                    $packagingIncoming->mid,

                'no_mobil' =>
                    $packagingIncoming->no_mobil,

                'tanggal_kedatangan' =>
                    optional(
                        $packagingIncoming
                            ->tanggal_kedatangan
                    )->format('Y-m-d'),

                'jam_kedatangan' =>
                    $packagingIncoming
                        ->jam_kedatangan,

                'quantity_incoming' =>
                    $packagingIncoming
                        ->jumlah,

                'jumlah_sampel' =>
                    $packagingIncoming
                        ->jumlah_sampel,

                'status_name' =>
                    $this->resolveEffectiveStatusName(
                        $packagingIncoming
                    ),

                'keterangan' =>
                    $packagingIncoming
                        ->keterangan,
            ],
        ]);
    }

    public function update(
        Request $request,
        PackagingIncoming $packagingIncoming
    ): JsonResponse {
        $this->ensureForeman();
        $validated = $this->validateIncoming(
            $request,
            $packagingIncoming->id
        );

        $packagingIncoming->update([
            'no_spb' =>
                trim($validated['no_spb']),

            'tanggal_kedatangan' =>
                $validated['tanggal_kedatangan'],

            'jam_kedatangan' =>
                $validated['jam_kedatangan'] ?? null,

            'jumlah' =>
                $validated['quantity_incoming'],

            'jumlah_sampel' =>
                $validated['jumlah_sampel'],

            'jenis_incoming_id' =>
                $validated['jenis_incoming_id'],

            'jenis_incoming_lainnya' =>
                $validated['jenis_incoming_lainnya'] ?? null,

            'supplier_id' =>
                $validated['supplier_id'],

            'supplier_lainnya' =>
                $validated['supplier_lainnya'] ?? null,

            'jenis_material_id' =>
                $validated['jenis_material_id'],

            'mid' =>
                $this->nullableUpper(
                    $validated['mid'] ?? null
                ),

            'no_mobil' =>
                $this->nullableUpper(
                    $validated['no_mobil'] ?? null
                ),

            'keterangan' =>
                $validated['keterangan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Data incoming berhasil diperbarui.',
            'data' => [
                'id' => $packagingIncoming->id,
                'no_spb' => $packagingIncoming->no_spb,
            ],
        ]);
    }

    public function destroy(
        Request $request,
        PackagingIncoming $packagingIncoming
    ): JsonResponse|RedirectResponse {
        $this->ensureForeman();
        $statusName = strtolower(
            $packagingIncoming
                ->samplingStatus
                ?->nama
            ?? ''
        );

        $hasDraftSampling =
            PackagingPouchSampling::query()
                ->where(
                    'packaging_incoming_id',
                    $packagingIncoming->id
                )
                ->where(
                    'status_proses',
                    'draft'
                )
                ->exists()
            || PackagingInnerOuterSampling::query()
                ->where(
                    'packaging_incoming_id',
                    $packagingIncoming->id
                )
                ->where(
                    'status_proses',
                    'draft'
                )
                ->exists();

        if (
            str_contains($statusName, 'sudah')
            || str_contains($statusName, 'selesai')
            || $hasDraftSampling
        ) {
            $message = $hasDraftSampling
                ? 'Data memiliki draft sampling. Lanjutkan proses sampling terlebih dahulu.'
                : 'Data yang sudah sampling tidak dapat dihapus.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('rmpm.pm.incoming.create')
                ->with('error', $message);
        }

        $packagingIncoming->delete();

        $message =
            'Data incoming berhasil dihapus.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('rmpm.pm.incoming.create')
            ->with('success', $message);
    }

    private function attachSamplingProcessStatus(
        $incomings
    ): void {
        $incomingIds =
            $incomings
                ->pluck('id')
                ->filter()
                ->values();

        if ($incomingIds->isEmpty()) {
            return;
        }

        $pouchStatuses =
            PackagingPouchSampling::query()
                ->whereIn(
                    'packaging_incoming_id',
                    $incomingIds
                )
                ->pluck(
                    'status_proses',
                    'packaging_incoming_id'
                );

        $innerOuterStatuses =
            PackagingInnerOuterSampling::query()
                ->whereIn(
                    'packaging_incoming_id',
                    $incomingIds
                )
                ->pluck(
                    'status_proses',
                    'packaging_incoming_id'
                );

        $incomings->each(
            function (
                PackagingIncoming $incoming
            ) use (
                $pouchStatuses,
                $innerOuterStatuses
            ): void {
                $jenisName = strtolower(
                    trim(
                        $incoming
                            ->jenisIncoming
                            ?->nama
                        ?? ''
                    )
                );

                $processStatus = null;

                if (
                    str_contains(
                        $jenisName,
                        'pouch'
                    )
                ) {
                    $processStatus =
                        $pouchStatuses[
                            $incoming->id
                        ] ?? null;
                } elseif (
                    str_contains(
                        $jenisName,
                        'inner'
                    )
                    || str_contains(
                        $jenisName,
                        'outer'
                    )
                ) {
                    $processStatus =
                        $innerOuterStatuses[
                            $incoming->id
                        ] ?? null;
                }

                $incoming->setAttribute(
                    'process_status',
                    $processStatus
                );
            }
        );
    }

    private function resolveEffectiveStatusName(
        PackagingIncoming $packagingIncoming
    ): string {
        $jenisName = strtolower(
            trim(
                $packagingIncoming
                    ->jenisIncoming
                    ?->nama
                ?? ''
            )
        );

        if (
            str_contains($jenisName, 'pouch')
        ) {
            $processStatus =
                PackagingPouchSampling::query()
                    ->where(
                        'packaging_incoming_id',
                        $packagingIncoming->id
                    )
                    ->value('status_proses');

            if ($processStatus === 'draft') {
                return 'Draft';
            }
        }

        if (
            str_contains($jenisName, 'inner')
            || str_contains($jenisName, 'outer')
        ) {
            $processStatus =
                PackagingInnerOuterSampling::query()
                    ->where(
                        'packaging_incoming_id',
                        $packagingIncoming->id
                    )
                    ->value('status_proses');

            if ($processStatus === 'draft') {
                return 'Draft';
            }
        }

        return $packagingIncoming
                ->samplingStatus
                ?->nama
            ?? 'Belum Sampling';
    }

    private function validateIncoming(
        Request $request,
        ?int $ignoreId = null
    ): array {
        $validated = $request->validate(
            [
                'no_spb' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(
                        'packaging_incomings',
                        'no_spb'
                    )->ignore($ignoreId),
                ],

                'tanggal_kedatangan' => [
                    'required',
                    'date',
                ],

                'jam_kedatangan' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'quantity_incoming' => [
                    'required',
                    'numeric',
                    'min:0.01',
                    'max:9999999999999.99',
                ],

                'jumlah_sampel' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:50',
                ],

                'jenis_incoming_id' => [
                    'required',
                    'exists:jenis_incomings,id',
                ],

                'jenis_incoming_lainnya' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'supplier_id' => [
                    'required',
                    'exists:suppliers,id',
                ],

                'supplier_lainnya' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'jenis_material_id' => [
                    'required',
                    'exists:jenis_materials,id',
                ],

                'mid' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'no_mobil' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'keterangan' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'no_spb.required' =>
                    'Nomor SPB wajib diisi.',

                'no_spb.unique' =>
                    'Nomor SPB sudah terdaftar.',

                'tanggal_kedatangan.required' =>
                    'Tanggal kedatangan wajib diisi.',

                'jam_kedatangan.date_format' =>
                    'Format jam kedatangan tidak valid.',

                'quantity_incoming.required' =>
                    'Quantity incoming wajib diisi.',

                'quantity_incoming.numeric' =>
                    'Quantity incoming harus berupa angka.',

                'quantity_incoming.min' =>
                    'Quantity incoming minimal 0,01.',

                'jumlah_sampel.required' =>
                    'Jumlah sampel wajib diisi.',

                'jumlah_sampel.integer' =>
                    'Jumlah sampel harus berupa angka bulat.',

                'jumlah_sampel.min' =>
                    'Jumlah sampel minimal 1.',

                'jumlah_sampel.max' =>
                    'Jumlah sampel maksimal 50.',

                'jenis_incoming_id.required' =>
                    'Jenis incoming wajib dipilih.',

                'supplier_id.required' =>
                    'Supplier wajib dipilih.',

                'jenis_material_id.required' =>
                    'Jenis material wajib dipilih.',

                'mid.required' =>
                    'MID dari WPM wajib dipilih.',
            ]
        );

        $jenisIncoming = JenisIncoming::query()
            ->find($validated['jenis_incoming_id']);

        $isJenisIncomingOthers =
            strtolower(
                trim(
                    (string) ($jenisIncoming?->nama ?? '')
                )
            ) === 'others';

        $supplier = Supplier::query()
            ->find($validated['supplier_id']);

        $supplierName = strtolower(
            trim(
                (string) (
                    $supplier?->nama
                    ?? $supplier?->nama_supplier
                    ?? ''
                )
            )
        );

        $isSupplierOthers =
            $supplierName === 'others';

        if (
            $isJenisIncomingOthers
            && trim(
                (string) (
                    $validated['jenis_incoming_lainnya']
                    ?? ''
                )
            ) === ''
        ) {
            throw ValidationException::withMessages([
                'jenis_incoming_lainnya' => [
                    'Jenis incoming lainnya wajib diisi saat memilih Others.',
                ],
            ]);
        }

        if (
            $isSupplierOthers
            && trim(
                (string) (
                    $validated['supplier_lainnya']
                    ?? ''
                )
            ) === ''
        ) {
            throw ValidationException::withMessages([
                'supplier_lainnya' => [
                    'Supplier lainnya wajib diisi saat memilih Others.',
                ],
            ]);
        }

        $validated['jenis_incoming_lainnya'] =
            $isJenisIncomingOthers
                ? trim(
                    (string) (
                        $validated['jenis_incoming_lainnya']
                        ?? ''
                    )
                )
                : null;

        $validated['supplier_lainnya'] =
            $isSupplierOthers
                ? trim(
                    (string) (
                        $validated['supplier_lainnya']
                        ?? ''
                    )
                )
                : null;

        return $validated;
    }

    private function ensureForeman(): void
    {
        abort_unless(
            auth()->check()
            && auth()->user()?->role === 'Foreman',
            403,
            'Kelola data incoming hanya dapat dilakukan oleh Foreman.'
        );
    }

    private function nullableUpper(
        mixed $value
    ): ?string {
        if (
            $value === null
            || trim((string) $value) === ''
        ) {
            return null;
        }

        return strtoupper(
            trim((string) $value)
        );
    }
}
