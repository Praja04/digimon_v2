<?php

namespace App\Http\Controllers;

use App\Http\Requests\RMPMStoreRequest;
use App\Models\AnalisaGaramGula;
use App\Models\AnalisaLongTerm;
use App\Models\AnalisaLongTermHistory;
use App\Models\AnalisaShortTerm;
use App\Models\IdentitasRM;
use App\Models\KonfirmasiKedatangan;
use App\Models\MasterGlassware;
use App\Models\MasterJenisBahan;
use App\Models\MasterParameterRm;
use App\Models\MasterStandarRm;
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
                    $jenisUpper = strtoupper(trim($item->jenis ?? ''));

                    if ($jenisUpper === 'GARAM') {
                        foreach ($item->analisaGaramGula as $analisa) {
                            if (!empty($analisa->disposisi) && $analisa->status !== 'draft') {
                                $status = '✅ Selesai';
                                break;
                            } elseif ($analisa->status === 'draft') {
                                $status = '⌛ Draft';
                            }
                        }
                    } else {
                        // Gula, Gula Kelapa, Gula Tebu, etc. Check long term or short term incoming
                        foreach ($item->analisaLongTerm as $analisa) {
                            if (!empty($analisa->disposisi) && $analisa->status !== 'draft') {
                                $status = '✅ Selesai';
                                break;
                            } elseif ($analisa->status === 'draft') {
                                $status = '⌛ Draft';
                            }
                        }
                        if ($status === '⌛ Proses') {
                            foreach ($item->analisaShortTerm as $analisa) {
                                if (!empty($analisa->disposisi) && $analisa->status !== 'draft') {
                                    $status = '✅ Selesai';
                                    break;
                                } elseif ($analisa->status === 'draft') {
                                    $status = '⌛ Draft';
                                }
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

        $masterJenisBahans = \App\Models\MasterJenisBahan::where('status', true)->orderBy('nama')->get();
        $masterSuppliers = \App\Models\MasterSupplierRm::where('status', true)->orderBy('nama_supplier')->get();
        $masterAsalBahans = \App\Models\MasterAsalBahan::where('status', true)->select('asal_bahan')->distinct()->orderBy('asal_bahan')->get();

        return view('app.rmpm.rm', compact('masterJenisBahans', 'masterSuppliers', 'masterAsalBahans'));
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
        $identitas = IdentitasRM::with([
            'samplingDokumen',
            'samplingKondisiMobil',
            'samplingFisikKemasan',
            'samplingFisikRaw',
            'analisaGaramGula',
            'analisaShortTerm',
            'analisaLongTerm.user',
            'analisaLongTerm.histories.user',
            'analisaLongTermHistories.user',
        ])->findOrFail($id);

        $data_dokumen = $identitas->samplingDokumen;
        $data_mobil = $identitas->samplingKondisiMobil;
        $data_kemasan = $identitas->samplingFisikKemasan;
        $data_raw = $identitas->samplingFisikRaw;
        $analisa_garam_gula = $identitas->analisaGaramGula;
        $analisa_short_term = $identitas->analisaShortTerm;
        $analisa_long_term = $identitas->analisaLongTerm;
        $analisa_long_term_histories = $identitas->analisaLongTermHistories;

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
                'analisa_long_term_histories',
                'konfirmasi'
            )
        );
    }

    public function showAnalisa($id)
    {
        $identitas = IdentitasRM::with([
            'analisaLongTerm.user',
            'analisaLongTerm.histories.user',
            'analisaLongTermHistories.user',
            'analisaShortTerm',
            'analisaGaramGula',
        ])->findOrFail($id);

        $existingLongTerm = $identitas->analisaLongTerm->last();
        $existingShortTerm = $identitas->analisaShortTerm ?? collect();
        $existingGaramGula = $identitas->analisaGaramGula ?? collect();
        $histories = $identitas->analisaLongTermHistories;

        // Preload Master Glassware Tare Weights & Quota
        $glasswares = MasterGlassware::where('status', true)->get();
        $beaker500 = [];
        $beaker250 = [];
        $cawan = [];

        foreach ($glasswares as $gw) {
            $jenisGw = strtoupper(trim($gw->jenis_glassware));
            $nomor = (string) $gw->nomor_glassware;
            $phys = trim(explode('.', (string)$gw->nomor_glassware)[0]);
            $berat = $gw->berat_glassware !== null ? (float) $gw->berat_glassware : 0;

            if (str_contains($jenisGw, '500')) {
                $beaker500[$nomor] = $berat;
                if ($phys !== '') {
                    $beaker500[$phys] = $berat;
                }
            } elseif (str_contains($jenisGw, '250')) {
                $beaker250[$nomor] = $berat;
                if ($phys !== '') {
                    $beaker250[$phys] = $berat;
                }
            } elseif (str_contains($jenisGw, 'CAWAN')) {
                $cawan[$nomor] = $berat;
                if ($phys !== '') {
                    $cawan[$phys] = $berat;
                }
            }
        }

        $beakerQuery = DB::table('analisa_short_term')
            ->whereDate('created_at', today())
            ->whereNotNull('no_beaker')
            ->where('no_beaker', '!=', '')
            ->where('id_identitas', '!=', $id);

        $cawanQuery = DB::table('analisa_short_term')
            ->whereDate('created_at', today())
            ->whereNotNull('no_cawan')
            ->where('no_cawan', '!=', '')
            ->where('id_identitas', '!=', $id);

        $totalBeakerToday = (clone $beakerQuery)->count();
        $totalCawanToday = (clone $cawanQuery)->count();

        $beakerUsage = [];
        $rawBeakerUsage = (clone $beakerQuery)
            ->select('no_beaker', DB::raw('count(*) as count'))
            ->groupBy('no_beaker')
            ->pluck('count', 'no_beaker')
            ->toArray();
        foreach ($rawBeakerUsage as $code => $cnt) {
            $phys = trim(explode('.', (string)$code)[0]);
            if ($phys !== '') {
                $beakerUsage[$phys] = ($beakerUsage[$phys] ?? 0) + $cnt;
            }
        }

        $cawanUsage = [];
        $rawCawanUsage = (clone $cawanQuery)
            ->select('no_cawan', DB::raw('count(*) as count'))
            ->groupBy('no_cawan')
            ->pluck('count', 'no_cawan')
            ->toArray();
        foreach ($rawCawanUsage as $code => $cnt) {
            $phys = trim(explode('.', (string)$code)[0]);
            if ($phys !== '') {
                $cawanUsage[$phys] = ($cawanUsage[$phys] ?? 0) + $cnt;
            }
        }

        $initialGlassware = [
            'status'     => true,
            'beaker_500' => $beaker500,
            'beaker_250' => $beaker250,
            'cawan'      => $cawan,
            'total_today'=> [
                'beaker' => $totalBeakerToday,
                'cawan'  => $totalCawanToday,
            ],
            'usage'      => [
                'beaker' => $beakerUsage,
                'cawan'  => $cawanUsage,
            ],
            'max_limits' => [
                'beaker' => 8,
                'cawan'  => 2,
            ],
        ];

        // Preload Master Standards for this material
        $cleanJenis = strtoupper(trim((string)$identitas->jenis));
        $standards = MasterStandarRm::where('status', true)
            ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
            })
            ->get();

        $standardsMap = [];
        foreach ($standards as $s) {
            $key = strtoupper(trim($s->parameter));
            $standardsMap[$key] = [
                'parameter'   => $s->parameter,
                'min'         => $s->min_standar,
                'max'         => $s->max_standar,
                'target_text' => $s->target_text,
                'uom'         => $s->uom,
            ];
        }

        $initialStandards = [
            'status'    => true,
            'standards' => $standardsMap,
        ];

        // Preload Master Parameters (Warna, Aroma, Organo)
        $jb = MasterJenisBahan::where('nama', 'LIKE', '%' . trim((string)$identitas->jenis) . '%')->first();
        $paramQuery = MasterParameterRm::where('status', true);
        if ($jb) {
            $paramQuery->where(function ($q) use ($jb) {
                $q->where('jenis_bahan_id', $jb->id)
                  ->orWhereNull('jenis_bahan_id');
            });
        }
        $paramList = $paramQuery->orderBy('urutan')->orderBy('id')->get();
        $pWarna = [];
        $pAroma = [];
        $pOrgano = [];
        foreach ($paramList as $p) {
            $kat = strtolower($p->kategori);
            $val = trim($p->nama_pilihan);
            if ($kat === 'warna') {
                if (!in_array($val, $pWarna)) {
                    $pWarna[] = $val;
                }
            } elseif ($kat === 'aroma') {
                if (!in_array($val, $pAroma)) {
                    $pAroma[] = $val;
                }
            } elseif ($kat === 'organo') {
                $pOrgano[] = [
                    'label'    => $val,
                    'value'    => $val,
                    'isCustom' => (bool) $p->is_custom,
                ];
            }
        }
        $initialParameters = [
            'status' => true,
            'data'   => [
                'warna'  => $pWarna,
                'aroma'  => $pAroma,
                'organo' => $pOrgano,
            ],
        ];

        return view(
            'app.rmpm.analisa',
            compact(
                'identitas',
                'existingLongTerm',
                'existingShortTerm',
                'existingGaramGula',
                'histories',
                'initialGlassware',
                'initialStandards',
                'initialParameters'
            )
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

            $konfirmasi = KonfirmasiKedatangan::where('id_identitas', $id)->first();

            $jamAnalisaExist = !empty($konfirmasi?->waktu_analisa);
            $jamKedatanganExist = !empty($konfirmasi?->waktu_kedatangan);

            return response()->json([
                'jam_analisa_exists' => $jamAnalisaExist,
                'jam_kedatangan_exists' => $jamKedatanganExist,
                'jam_analisa' => $konfirmasi?->waktu_analisa ? \Carbon\Carbon::parse($konfirmasi->waktu_analisa)->format('Y-m-d\TH:i') : null,
                'jam_kedatangan' => $konfirmasi?->waktu_kedatangan ? \Carbon\Carbon::parse($konfirmasi->waktu_kedatangan)->format('Y-m-d\TH:i') : null,
                'sampling_complete' => (bool) $identitas->isSamplingComplete(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data konfirmasi: ' . $e->getMessage(),
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
            'tipe' => [
                'nullable',
                'in:kedatangan,analisa',
            ],
        ]);

        $tipe = $request->input('tipe', 'kedatangan');

        $konfirmasi = KonfirmasiKedatangan::where(
            'id_identitas',
            $request->id
        )->first();

        if ($konfirmasi) {
            if ($tipe === 'analisa') {
                $konfirmasi->update([
                    'waktu_analisa' => $request->jam,
                    'dianalisa_by' => auth()->id(),
                ]);
            } else {
                $konfirmasi->update([
                    'waktu_kedatangan' => $request->jam,
                    'diterima_by' => auth()->id(),
                ]);
            }
        } else {
            if ($tipe === 'analisa') {
                KonfirmasiKedatangan::create([
                    'id_identitas' => $request->id,
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
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data konfirmasi berhasil disimpan.',
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA LONG TERM
    |--------------------------------------------------------------------------
    */

    public function storeLongTerm(Request $request)
    {
        $saveAction = $request->input('save_action', 'final');
        $isDraft = ($saveAction === 'draft');

        $rules = [
            'id_identitas' => [
                'required',
                'exists:identitas_rm,id',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ];

        if ($isDraft) {
            $rules['uji_kristal'] = ['nullable', 'in:positif,negatif,'];
            $rules['disposisi'] = ['nullable', 'in:Release,Release Bersyarat,Reject,'];
            $rules['group'] = ['nullable', 'in:Group A,Group B,Group C,'];
        } else {
            $rules['uji_kristal'] = ['required', 'in:positif,negatif'];
            $rules['disposisi'] = ['required', 'in:Release,Release Bersyarat,Reject'];
            if (in_array($request->disposisi, ['Release', 'Release Bersyarat'])) {
                $rules['group'] = ['required', 'in:Group A,Group B,Group C'];
            }
        }

        if ($request->hasFile('attachments')) {
            $rules['attachments'] = ['nullable'];
        } elseif ($request->hasFile('attachment')) {
            $rules['attachment'] = ['nullable'];
        }

        $request->validate($rules);

        $ujiKristal = $request->filled('uji_kristal') ? $request->uji_kristal : null;
        $disposisi = $request->filled('disposisi') ? $request->disposisi : null;

        if (!$isDraft && $ujiKristal === 'negatif' && empty($disposisi)) {
            $disposisi = 'Release';
        }

        $group = in_array($disposisi, ['Release', 'Release Bersyarat']) ? ($request->filled('group') ? $request->group : null) : null;

        // Collect existing saved photos
        $photoList = [];
        if ($request->filled('existing_attachments')) {
            $existing = $request->existing_attachments;
            if (is_string($existing)) {
                $decoded = json_decode($existing, true);
                $existing = is_array($decoded) ? $decoded : [$existing];
            }
            if (is_array($existing)) {
                foreach ($existing as $item) {
                    if (is_string($item)) {
                        $clean = basename(urldecode(trim($item)));
                        if ($clean !== '' && $clean !== '-' && !in_array($clean, $photoList)) {
                            $photoList[] = $clean;
                        }
                    }
                }
            }
        }

        // Process uploaded files (max 5 photos total)
        $filesToUpload = [];
        if ($request->hasFile('attachments')) {
            $raw = $request->file('attachments');
            $filesToUpload = is_array($raw) ? $raw : [$raw];
        } elseif ($request->hasFile('attachment')) {
            $raw = $request->file('attachment');
            $filesToUpload = is_array($raw) ? $raw : [$raw];
        } else {
            $all = $request->allFiles();
            foreach ($all as $f) {
                if (is_array($f)) {
                    foreach ($f as $subF) {
                        if ($subF instanceof \Illuminate\Http\UploadedFile && $subF->isValid()) {
                            $filesToUpload[] = $subF;
                        }
                    }
                } elseif ($f instanceof \Illuminate\Http\UploadedFile && $f->isValid()) {
                    $filesToUpload[] = $f;
                }
            }
        }

        foreach ($filesToUpload as $file) {
            if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) {
                continue;
            }
            if (count($photoList) >= 5) {
                break;
            }
            $ext = $file->getClientOriginalExtension() ?: ($file->extension() ?: 'jpg');
            $filename =
                'attachment_' .
                time() .
                '_' .
                uniqid() .
                '.' .
                strtolower($ext);

            $file->storeAs(
                'uploads/attachment_analisa',
                $filename,
                'public'
            );

            $photoList[] = basename($filename);
        }

        // Safety fallback: If still empty and no new files were uploaded, check if existing record in DB already has photos
        if (empty($photoList) && empty($filesToUpload)) {
            $existingRecord = AnalisaLongTerm::where('id_identitas', $request->id_identitas)->latest()->first();
            if ($existingRecord && !empty($existingRecord->photos)) {
                $photoList = $existingRecord->photos;
            }
        }

        if (!$isDraft && $ujiKristal === 'positif' && empty($photoList)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lampiran gambar kristal wajib diunggah (minimal 1 foto, maksimal 5 foto) untuk uji kristal positif.',
            ], 422);
        }

        // Find existing record or create new
        $analisa = AnalisaLongTerm::where('id_identitas', $request->id_identitas)->latest()->first();

        if ($analisa && $analisa->status === 'draft') {
            $analisa->update([
                'uji_kristal' => $ujiKristal,
                'disposisi' => $disposisi,
                'group' => $group,
                'attachment' => $photoList,
                'keterangan' => $request->keterangan,
                'status' => $isDraft ? 'draft' : 'final',
                'created_by' => auth()->id(),
            ]);
        } else {
            $analisa = AnalisaLongTerm::create([
                'id_identitas' => $request->id_identitas,
                'uji_kristal' => $ujiKristal,
                'disposisi' => $disposisi,
                'group' => $group,
                'attachment' => $photoList,
                'keterangan' => $request->keterangan,
                'status' => $isDraft ? 'draft' : 'final',
                'created_by' => auth()->id(),
            ]);
        }

        // Record history log (only on manual save / final save, not on silent auto-saves)
        $isAutoSave = $request->boolean('is_auto_save') || $request->input('is_auto_save') == '1';
        if (!$isAutoSave) {
            AnalisaLongTermHistory::create([
                'analisa_long_term_id' => $analisa->id,
                'id_identitas' => $request->id_identitas,
                'user_id' => auth()->id(),
                'action' => $isDraft ? 'Simpan Sementara' : 'Simpan Final',
                'uji_kristal' => $ujiKristal,
                'disposisi' => $disposisi,
                'group' => $group,
                'attachment' => $photoList,
                'keterangan' => $request->keterangan,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $isDraft
                ? 'Data analisa berhasil disimpan sementara (Draft).'
                : 'Data analisa long term berhasil disimpan.',
            'data' => $analisa,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA SHORT TERM
    |--------------------------------------------------------------------------
    */

    public function storeShortTerm(Request $request)
    {
        $kategori = $request->input('kategori', 'incoming');
        if (!in_array($kategori, ['incoming', 'sta', 'monitoring'])) {
            $kategori = 'incoming';
        }

        $saveAction = $request->input('save_action', 'final');
        $isDraft = ($saveAction === 'draft');

        $rules = [
            'id_identitas' => 'required|exists:identitas_rm,id',
            'disposisi'    => $isDraft ? 'nullable|in:Release,Reject,' : 'required|in:Release,Reject',
            'keterangan'   => 'nullable|string',
            'kategori'     => 'nullable|string|in:incoming,sta,monitoring',
        ];

        // For incoming, require main parameters if final. For STA / Monitoring or Draft, allow flexible partial inputs.
        if (!$isDraft && $kategori === 'incoming') {
            $rules['brix']   = 'required|array|min:1';
            $rules['ph']     = 'required|array|min:1';
            $rules['ka']     = 'required|array|min:1';
        } else {
            $rules['brix']   = 'nullable|array';
            $rules['ph']     = 'nullable|array';
            $rules['ka']     = 'nullable|array';
        }

        $rules['kotoran'] = 'nullable|array';
        $rules['organo']  = 'nullable|array';
        $rules['warna']   = 'nullable|array';
        $rules['aroma']   = 'nullable|array';

        $request->validate($rules);

        DB::beginTransaction();

        try {
            // Determine number of sample rows from whichever array is provided
            $sampleCounts = [
                count($request->brix ?? []),
                count($request->ph ?? []),
                count($request->kotoran ?? []),
                count($request->ka ?? []),
                count($request->organo ?? []),
                count($request->warna ?? []),
                count($request->aroma ?? []),
                count($request->timbang_a ?? []),
                count($request->timbang_b ?? []),
                count($request->no_beaker ?? []),
            ];
            $jumlah = max(1, ...$sampleCounts);

            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas'     => $request->id_identitas,
                    'kategori'         => $kategori,
                    'brix'             => $this->nullableFloat($request->brix[$i] ?? null),
                    'ph'               => $this->nullableFloat($request->ph[$i] ?? null),
                    'no_beaker'        => $this->nullableString($request->no_beaker[$i] ?? null),
                    'berat_beaker_500' => $this->nullableFloat($request->berat_beaker_500[$i] ?? null),
                    'berat_beaker_250' => $this->nullableFloat($request->berat_beaker_250[$i] ?? null),
                    'timbang_a'        => $this->nullableFloat($request->timbang_a[$i] ?? null),
                    'timbang_b'        => $this->nullableFloat($request->timbang_b[$i] ?? null),
                    'kotoran'          => $this->nullableFloat($request->kotoran[$i] ?? null),
                    'rasa'             => $this->nullableString($request->rasa[$i] ?? null),
                    'aroma_pengotor'   => $this->nullableString($request->aroma_pengotor[$i] ?? null),
                    'no_cawan'         => $this->nullableString($request->no_cawan[$i] ?? null),
                    'berat_cawan'      => $this->nullableFloat($request->berat_cawan[$i] ?? null),
                    'timbang_aa'       => $this->nullableFloat($request->timbang_aa[$i] ?? null),
                    'ka'               => $this->nullableFloat($request->ka[$i] ?? null),
                    'organo'           => $this->nullableString($request->organo[$i] ?? null),
                    'warna'            => $this->nullableString($request->warna[$i] ?? null),
                    'aroma'            => $this->nullableString($request->aroma[$i] ?? null),
                    'disposisi'        => $request->disposisi,
                    'status'           => $isDraft ? 'draft' : 'final',
                    'keterangan'       => $request->keterangan,
                    'created_by'       => auth()->id(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            // Remove existing records for this specific kategori so it cleanly updates
            AnalisaShortTerm::where('id_identitas', $request->id_identitas)
                ->where(function ($q) use ($kategori) {
                    $q->where('kategori', $kategori)
                      ->orWhere(function ($sub) use ($kategori) {
                          if ($kategori === 'incoming') {
                              $sub->whereNull('kategori');
                          }
                      });
                })
                ->delete();

            AnalisaShortTerm::insert($dataAnalisa);

            DB::commit();

            $kategoriLabels = [
                'incoming'   => 'Incoming',
                'sta'        => 'STA (Short Term Analisa)',
                'monitoring' => 'Monitoring',
            ];
            $label = $kategoriLabels[$kategori] ?? 'Analisa';

            $message = $isDraft
                ? "Data {$label} berhasil disimpan sementara (Draft)."
                : "Berhasil menyimpan data {$label}.";

            return response()->json([
                'status'  => 'success',
                'message' => $message,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
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
        $saveAction = $request->input('save_action', 'final');
        $isDraft = ($saveAction === 'draft');

        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'fisik'        => $isDraft ? 'nullable|array' : 'required|array|min:1',
            'fisik.*'      => 'nullable|string',
            '%ka'          => 'nullable|array',
            'kotoran'      => 'nullable|array',
            'organo'       => 'nullable|array',
            'warna'        => 'nullable|array',
            'aroma'        => 'nullable|array',
            '%nacl'        => 'nullable|array',
            'gross_weight' => 'nullable|array',
            'disposisi'    => $isDraft ? 'nullable|in:Release,Reject,' : 'required|in:Release,Reject',
            'keterangan'   => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $sampleCounts = [
                count($request->fisik ?? []),
                count($request['%ka'] ?? []),
                count($request->kotoran ?? []),
                count($request->organo ?? []),
                count($request->warna ?? []),
                count($request->aroma ?? []),
                count($request['%nacl'] ?? []),
                count($request->gross_weight ?? []),
            ];
            $jumlah = max(1, ...$sampleCounts);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas' => $request->id_identitas,
                    'fisik'        => $this->nullableString($request->fisik[$i] ?? null),
                    '%ka'          => $this->nullableFloat($request['%ka'][$i] ?? null),
                    'kotoran'      => $this->nullableFloat($request->kotoran[$i] ?? null),
                    'organo'       => $this->nullableString($request->organo[$i] ?? null),
                    'warna'        => $this->nullableString($request->warna[$i] ?? null),
                    'aroma'        => $this->nullableString($request->aroma[$i] ?? null),
                    '%nacl'        => $this->nullableFloat($request['%nacl'][$i] ?? null),
                    'gross_weight' => $this->nullableFloat($request->gross_weight[$i] ?? null),
                    'disposisi'    => $request->disposisi,
                    'status'       => $isDraft ? 'draft' : 'final',
                    'keterangan'   => $request->keterangan,
                    'created_by'   => auth()->id(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            AnalisaGaramGula::where('id_identitas', $request->id_identitas)->delete();
            AnalisaGaramGula::insert($dataAnalisa);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => $isDraft
                    ? 'Data analisa berhasil disimpan sementara (Draft).'
                    : 'Data analisa berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
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
            'id' => [
                'required',
                'exists:analisa_long_term,id',
            ],
            'disposisi' => [
                'required',
                'in:Release,Release Bersyarat,Reject',
            ],
            'group' => [
                'nullable',
                'in:Group A,Group B,Group C',
            ],
            'keterangan_update' => [
                'nullable',
                'string',
            ],
        ]);

        $data = AnalisaLongTerm::findOrFail(
            $request->id
        );

        $oldDisposisi = $data->disposisi;
        $oldGroup = $data->group;

        $newDisposisi = $request->disposisi;
        $newGroup = in_array($newDisposisi, ['Release', 'Release Bersyarat']) ? $request->group : null;

        if (in_array($newDisposisi, ['Release', 'Release Bersyarat']) && empty($newGroup)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Group ABC wajib dipilih untuk disposisi Release atau Release Bersyarat.',
            ], 422);
        }

        $data->disposisi = $newDisposisi;
        $data->group = $newGroup;
        if ($request->filled('keterangan_update')) {
            $data->keterangan = ($data->keterangan ? $data->keterangan . "\n" : '') . '[Update Disposisi]: ' . $request->keterangan_update;
        }
        $data->save();

        // Record history log
        AnalisaLongTermHistory::create([
            'analisa_long_term_id' => $data->id,
            'id_identitas' => $data->id_identitas,
            'user_id' => auth()->id(),
            'action' => 'Update Disposisi',
            'uji_kristal' => $data->uji_kristal,
            'disposisi' => $newDisposisi,
            'group' => $newGroup,
            'attachment' => $data->attachment,
            'keterangan' => $request->keterangan_update ?? "Update disposisi dari '{$oldDisposisi}' ke '{$newDisposisi}'" . ($newGroup ? " ({$newGroup})" : ''),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Disposisi berhasil diperbarui.',
            'data' => $data,
        ]);
    }

    public function resetDraft(Request $request, $id)
    {
        try {
            // Delete all unfinalized/draft records for this identitas ID across short-term, long-term, and garam-gula
            AnalisaShortTerm::where('id_identitas', $id)
                ->where(function ($q) {
                    $q->where('status', 'draft')
                      ->orWhereNull('disposisi')
                      ->orWhere('disposisi', '');
                })
                ->delete();

            $draftLongTerms = AnalisaLongTerm::where('id_identitas', $id)
                ->where(function ($q) {
                    $q->where('status', 'draft')
                      ->orWhereNull('disposisi')
                      ->orWhere('disposisi', '');
                })
                ->get();

            foreach ($draftLongTerms as $dlt) {
                AnalisaLongTermHistory::where('analisa_long_term_id', $dlt->id)->delete();
                $dlt->delete();
            }

            AnalisaLongTermHistory::where('id_identitas', $id)
                ->where('action', 'Simpan Sementara')
                ->delete();

            AnalisaGaramGula::where('id_identitas', $id)
                ->where(function ($q) {
                    $q->where('status', 'draft')
                      ->orWhereNull('disposisi')
                      ->orWhere('disposisi', '');
                })
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Semua data simpan sementara (draft) berhasil di-reset.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal reset draft: ' . $e->getMessage(),
            ], 500);
        }
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