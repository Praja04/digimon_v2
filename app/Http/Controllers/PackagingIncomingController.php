<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;
use App\Models\JenisMaterial;
use App\Models\PackagingIncoming;
use App\Models\SamplingStatus;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackagingIncomingController extends Controller
{
    public function index(Request $request): View
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
            $query->where(
                'sampling_status_id',
                $request->input('status_filter')
            );
        }

        $incomings = $query
            ->paginate(10)
            ->withQueryString();

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

        return view(
            'app.rmpm.incoming-index',
            compact(
                'jenisIncomings',
                'jenisMaterials',
                'suppliers',
                'samplingStatuses',
                'incomings'
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

                'supplier_id' =>
                    $validated['supplier_id'],

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

                'supplier_id' =>
                    $packagingIncoming
                        ->supplier_id,

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
                    $packagingIncoming
                        ->samplingStatus
                        ?->nama
                    ?? 'Belum Sampling',

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

            'supplier_id' =>
                $validated['supplier_id'],

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
        $statusName = strtolower(
            $packagingIncoming
                ->samplingStatus
                ?->nama
            ?? ''
        );

        if (
            str_contains($statusName, 'sudah')
            || str_contains($statusName, 'selesai')
        ) {
            $message =
                'Data yang sudah sampling tidak dapat dihapus.';

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

    private function validateIncoming(
        Request $request,
        ?int $ignoreId = null
    ): array {
        return $request->validate(
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

                'supplier_id' => [
                    'required',
                    'exists:suppliers,id',
                ],

                'jenis_material_id' => [
                    'required',
                    'exists:jenis_materials,id',
                ],

                'mid' => [
                    'nullable',
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
            ]
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