<?php

namespace App\Http\Controllers;

use App\Http\Requests\RMPMStoreRequest;
use App\Models\AnalisaGaramGula;
use App\Models\AnalisaLongTerm;
use App\Models\AnalisaShortTerm;
use App\Models\IdentitasRM;
use App\Models\KonfirmasiKedatangan;
use App\Models\PackagingIncoming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\Facades\DNS2DFacade;
use Yajra\DataTables\DataTables;

class RMPMController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MENU UTAMA RMPM
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('app.rmpm.menu');
    }

    /*
    |--------------------------------------------------------------------------
    | RAW MATERIAL
    |--------------------------------------------------------------------------
    */

    public function rm(Request $request)
    {
        if ($request->ajax()) {
            $query = IdentitasRM::query()
                ->orderBy('created_at', 'desc');

            if ($request->filled('start_date')) {
                $query->whereDate(
                    'tanggal_kedatangan',
                    '>=',
                    $request->start_date
                );
            }

            if ($request->filled('end_date')) {
                $query->whereDate(
                    'tanggal_kedatangan',
                    '<=',
                    $request->end_date
                );
            }

            if ($request->filled('jenis')) {
                $query->where(
                    'jenis',
                    $request->jenis
                );
            }

            $identitasRm = $query->get();

            return DataTables::of($identitasRm)
                ->addIndexColumn()

                ->editColumn(
                    'tanggal_kedatangan',
                    function ($data) {
                        return \Carbon\Carbon::parse(
                            $data->tanggal_kedatangan
                        )
                            ->locale('id')
                            ->isoFormat('D MMMM Y');
                    }
                )

                ->addColumn('qr_code', function ($data) {
                    return '
                        <button
                            type="button"
                            class="btn btn-sm btn-primary me-1"
                            id="btnQRCode"
                            data-id="' . $data->id . '"
                        >
                            <span class="mdi mdi-qrcode"></span>
                            QR Code
                        </button>
                    ';
                })

                ->addColumn('action', function ($data) {
                    return '
                        <a
                            class="btn btn-sm btn-info me-1"
                            href="' . route('rmpm.show', $data->id) . '"
                        >
                            <span class="mdi mdi-eye"></span>
                            Lihat
                        </a>
                    ';
                })

                ->addColumn('status', function ($item) {
                    $status = '⌛ Proses';

                    if (
                        in_array(
                            $item->jenis,
                            ['Garam', 'Gula'],
                            true
                        )
                    ) {
                        foreach (
                            $item->analisaGaramGula as $analisa
                        ) {
                            if ($analisa->disposisi) {
                                $status = '✅ Selesai';
                                break;
                            }
                        }
                    } elseif (
                        in_array(
                            $item->jenis,
                            ['Gula Tebu', 'Gula Kelapa'],
                            true
                        )
                    ) {
                        foreach (
                            $item->analisaLongTerm as $analisa
                        ) {
                            if (!empty($analisa->disposisi)) {
                                $status = '✅ Selesai';
                                break;
                            }
                        }
                    }

                    return $status;
                })

                ->rawColumns([
                    'action',
                    'qr_code',
                ])

                ->make(true);
        }

        return view('app.rmpm.rm');
    }

    /*
    |--------------------------------------------------------------------------
    | PACKAGING MATERIAL
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan empat pilihan proses Packaging Material.
     */
  public function pm()
{
    return view('app.rmpm.pm');
}


public function pmInnerOuter()
{
    return view('app.rmpm.pm-placeholder', [
        'title' => 'Pengecekan Inner / Outer',
        'subtitle' =>
            'Kelola antrean sampling dan pemeriksaan material Inner / Outer.',
        'icon' => 'mdi-chart-donut',
        'step' => 'Tahap 2',
    ]);
}

/*
|--------------------------------------------------------------------------
| PM - KARTON
|--------------------------------------------------------------------------
*/

public function pmKarton()
{
    return view('app.rmpm.karton');
}

public function pmKartonMenu()
{
    return view('app.rmpm.pm-placeholder', [
        'title' => 'Menu Karton',
        'subtitle' =>
            'Daftar SPB Karton yang menunggu proses pemeriksaan dan sampling.',
        'icon' => 'mdi-format-list-bulleted-square',
        'step' => 'Karton',
    ]);
}

public function pmKartonDisplay(
    PackagingIncoming $packagingIncoming
)
{
    $packagingIncoming->load([
        'jenisIncoming',
        'jenisMaterial',
        'supplier',
        'samplingStatus',
    ]);

    abort_unless(
        in_array(
            $packagingIncoming
                ->jenisIncoming
                ?->nama,
            [
                'Karton',
                'Kardus',
            ],
            true
        ),
        404,
        'Data incoming bukan kategori Karton.'
    );

    return view(
        'app.rmpm.karton-display',
        compact('packagingIncoming')
    );
}

public function pmPouch()
{
    return view('app.rmpm.pm-placeholder', [
        'title' => 'Pengecekan Pouch',
        'subtitle' =>
            'Kelola antrean sampling dan pemeriksaan ukuran, seal, ketebalan, serta visual Pouch.',
        'icon' => 'mdi-package-variant',
        'step' => 'Tahap 4',
    ]);
}

public function pmKartonBct(
    PackagingIncoming $packagingIncoming
)
{
    $packagingIncoming->load([
        'jenisIncoming',
        'jenisMaterial',
        'supplier',
        'samplingStatus',
    ]);

    abort_unless(
        in_array(
            $packagingIncoming
                ->jenisIncoming
                ?->nama,
            [
                'Karton',
                'Kardus',
            ],
            true
        ),
        404,
        'Data incoming bukan kategori Karton.'
    );

    return view(
        'app.rmpm.karton-bct',
        compact('packagingIncoming')
    );
}

    /*
    |--------------------------------------------------------------------------
    | DETAIL RAW MATERIAL
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $identitas = IdentitasRM::findOrFail($id);

        $data_dokumen = $identitas->samplingDokumen;
        $data_mobil = $identitas->samplingKondisiMobil;
        $data_kemasan = $identitas->samplingFisikKemasan;
        $data_raw = $identitas->samplingFisikRaw;
        $analisa_garam_gula = $identitas->analisaGaramGula;
        $analisa_short_term = $identitas->analisaShortTerm;
        $analisa_long_term = $identitas->analisaLongTerm;

        $konfirmasi = KonfirmasiKedatangan::where(
            'id_identitas',
            $id
        )->first();

        return view(
            'app.rmpm.show',
            compact(
                'identitas',
                'data_dokumen',
                'data_mobil',
                'data_kemasan',
                'data_raw',
                'analisa_garam_gula',
                'analisa_short_term',
                'analisa_long_term',
                'konfirmasi'
            )
        );
    }

    public function showAnalisa($id)
    {
        $identitas = IdentitasRM::findOrFail($id);

        return view(
            'app.rmpm.analisa',
            compact('identitas')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN IDENTITAS RAW MATERIAL
    |--------------------------------------------------------------------------
    */

    public function store(RMPMStoreRequest $request)
    {
        try {
            IdentitasRM::create(
                $request->validated()
            );

            return response()->json([
                'status' => 'success',
                'message' =>
                    'Data identitas RM berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | QR CODE
    |--------------------------------------------------------------------------
    */

    public function getQRCode($id)
    {
        try {
            $identitas = IdentitasRM::findOrFail($id);

            $qrText = url(
                '/rmpm/' . $id . '/analisa'
            );

            $qrCode = DNS2DFacade::getBarcodePNG(
                $qrText,
                'QRCODE'
            );

            $tanggal = \Carbon\Carbon::parse(
                $identitas->created_at
            )->format('Y-m-d');

            $label =
                'RMPM/' .
                $identitas->no_spb .
                '/' .
                $tanggal .
                '/' .
                $identitas->id;

            return response()->json([
                'status' => 'success',
                'qrCode' => $qrCode,
                'label' => $label,

                'tanggal' => \Carbon\Carbon::parse(
                    $identitas->tanggal_kedatangan
                )
                    ->locale('id')
                    ->isoFormat('D MMMM Y'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' =>
                    'Gagal generate QR Code: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | KONFIRMASI
    |--------------------------------------------------------------------------
    */

    public function getKonfirmasi($id)
    {
        try {
            $identitas = IdentitasRM::with([
                'samplingKondisiMobil',
                'samplingDokumen',
                'samplingFisikKemasan',
                'samplingFisikRaw',
            ])->findOrFail($id);

            $jamAnalisaExist =
                KonfirmasiKedatangan::where(
                    'id_identitas',
                    $id
                )->exists();

            return response()->json([
                'jam_analisa_exists' =>
                    $jamAnalisaExist,

                'sampling_complete' =>
                    $identitas->isSamplingComplete(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',

                'message' =>
                    'Gagal mengambil data konfirmasi: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    public function updateKonfirmasi(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'exists:identitas_rm,id',
            ],

            'jam' => [
                'required',
            ],
        ]);

        $konfirmasi = KonfirmasiKedatangan::where(
            'id_identitas',
            $request->id
        )->first();

        if ($konfirmasi) {
            $konfirmasi->update([
                'waktu_analisa' => $request->jam,
                'dianalisa_by' => auth()->id(),
            ]);
        } else {
            KonfirmasiKedatangan::create([
                'id_identitas' => $request->id,
                'waktu_kedatangan' => $request->jam,
                'diterima_by' => auth()->id(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' =>
                'Data konfirmasi berhasil disimpan.',
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA LONG TERM
    |--------------------------------------------------------------------------
    */

    public function storeLongTerm(Request $request)
    {
        $request->validate([
            'id_identitas' => [
                'required',
                'exists:identitas_rm,id',
            ],

            'uji_kristal' => [
                'required',
                'in:positif,negatif',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $ujiKristal = $request->uji_kristal;
        $attachmentName = null;
        $disposisi = null;

        if ($ujiKristal === 'negatif') {
            $attachmentName = '-';
            $disposisi = 'Release';
        } else {
            $request->validate([
                'attachment' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,gif',
                    'max:5000',
                ],

                'disposisi' => [
                    'required',
                    'in:Release,Reject',
                ],
            ]);

            if ($request->hasFile('attachment')) {
                $filename =
                    'attachment_' .
                    time() .
                    '_' .
                    uniqid() .
                    '.' .
                    $request->attachment->extension();

                $request
                    ->file('attachment')
                    ->storeAs(
                        'uploads/attachment_analisa',
                        $filename,
                        'public'
                    );

                $attachmentName = basename($filename);
            }

            $disposisi = $request->disposisi;
        }

        AnalisaLongTerm::create([
            'id_identitas' =>
                $request->id_identitas,

            'uji_kristal' =>
                $ujiKristal,

            'disposisi' =>
                $disposisi,

            'attachment' =>
                $attachmentName,

            'keterangan' =>
                $request->keterangan,

            'created_by' =>
                auth()->id(),
        ]);

        return response()->json([
            'message' =>
                'Data analisa long term berhasil disimpan.',
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA SHORT TERM
    |--------------------------------------------------------------------------
    */

    public function storeShortTerm(Request $request)
    {
        $request->validate([
            'id_identitas' =>
                'required|exists:identitas_rm,id',

            'brix' =>
                'required|array|min:1',

            'brix.*' =>
                'required|string',

            'ph' =>
                'required|array|min:1',

            'ph.*' =>
                'required|string',

            'kotoran' =>
                'nullable|array',

            'ka' =>
                'required|array|min:1',

            'ka.*' =>
                'required|string',

            'organo' =>
                'nullable|array',

            'warna' =>
                'nullable|array',

            'aroma' =>
                'nullable|array',

            'disposisi' =>
                'required|in:Release,Reject',

            'keterangan' =>
                'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $jumlah = count($request->brix);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas' =>
                        $request->id_identitas,

                    'brix' =>
                        $this->nullableFloat(
                            $request->brix[$i] ?? null
                        ),

                    'ph' =>
                        $this->nullableFloat(
                            $request->ph[$i] ?? null
                        ),

                    'kotoran' =>
                        $this->nullableFloat(
                            $request->kotoran[$i] ?? null
                        ),

                    'ka' =>
                        $this->nullableFloat(
                            $request->ka[$i] ?? null
                        ),

                    'organo' =>
                        $this->nullableString(
                            $request->organo[$i] ?? null
                        ),

                    'warna' =>
                        $this->nullableString(
                            $request->warna[$i] ?? null
                        ),

                    'aroma' =>
                        $this->nullableString(
                            $request->aroma[$i] ?? null
                        ),

                    'disposisi' =>
                        $request->disposisi,

                    'keterangan' =>
                        $request->keterangan,

                    'created_by' =>
                        auth()->id(),

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ];
            }

            AnalisaShortTerm::insert($dataAnalisa);

            DB::commit();

            return response()->json([
                'message' =>
                    'Berhasil menyimpan data analisa short term.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' =>
                    'Gagal menyimpan data: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA GARAM DAN GULA
    |--------------------------------------------------------------------------
    */

    public function storeGaramGula(Request $request)
    {
        $request->validate([
            'id_identitas' =>
                'required|exists:identitas_rm,id',

            'fisik' =>
                'required|array|min:1',

            'fisik.*' =>
                'required|string',

            '%ka' =>
                'nullable|array',

            'kotoran' =>
                'nullable|array',

            'organo' =>
                'nullable|array',

            'warna' =>
                'nullable|array',

            'aroma' =>
                'nullable|array',

            '%nacl' =>
                'nullable|array',

            'gross_weight' =>
                'nullable|array',

            'disposisi' =>
                'required|in:Release,Reject',

            'keterangan' =>
                'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $jumlah = count($request->fisik);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas' =>
                        $request->id_identitas,

                    'fisik' =>
                        $this->nullableString(
                            $request->fisik[$i] ?? null
                        ),

                    '%ka' =>
                        $this->nullableFloat(
                            $request['%ka'][$i] ?? null
                        ),

                    'kotoran' =>
                        $this->nullableFloat(
                            $request->kotoran[$i] ?? null
                        ),

                    'organo' =>
                        $this->nullableString(
                            $request->organo[$i] ?? null
                        ),

                    'warna' =>
                        $this->nullableString(
                            $request->warna[$i] ?? null
                        ),

                    'aroma' =>
                        $this->nullableString(
                            $request->aroma[$i] ?? null
                        ),

                    '%nacl' =>
                        $this->nullableFloat(
                            $request['%nacl'][$i] ?? null
                        ),

                    'gross_weight' =>
                        $this->nullableFloat(
                            $request->gross_weight[$i] ?? null
                        ),

                    'disposisi' =>
                        $request->disposisi,

                    'keterangan' =>
                        $request->keterangan,

                    'created_by' =>
                        auth()->id(),

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ];
            }

            AnalisaGaramGula::insert($dataAnalisa);

            DB::commit();

            return response()->json([
                'status' => 'success',

                'message' =>
                    'Data analisa berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' =>
                    'Gagal menyimpan data: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DISPOSISI LONG TERM
    |--------------------------------------------------------------------------
    */

    public function updateDisposisiLongTerm(
        Request $request
    ) {
        $request->validate([
            'disposisi' => [
                'required',
                'in:Release,Reject',
            ],
        ]);

        $data = AnalisaLongTerm::findOrFail(
            $request->id
        );

        $data->disposisi = $request->disposisi;
        $data->save();

        return response()->json([
            'message' =>
                'Disposisi berhasil diperbarui.',

            'data' =>
                $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function nullableFloat(
        $value
    ): ?float {
        if (
            $value === null ||
            trim((string) $value) === ''
        ) {
            return null;
        }

        return (float) str_replace(
            ',',
            '.',
            $value
        );
    }

    private function nullableString(
        $value
    ): ?string {
        if (
            $value === null ||
            trim((string) $value) === ''
        ) {
            return null;
        }

        return strtoupper(
            trim((string) $value)
        );
    }
}