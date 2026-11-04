<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKalkulasiRmExport;
use App\Http\Requests\RMPMStoreRequest;
use App\Models\AnalisaLongTerm;
use App\Models\AnalisaLongTermHistory;
use App\Models\IdentitasRM;
use App\Models\KonfirmasiKedatangan;
use App\Models\MasterGlassware;
use App\Models\MasterJenisBahan;
use App\Models\MasterParameterRm;
use App\Models\MasterStandarRm;
use App\Models\PackagingIncoming;
use App\Models\TransaksiAnalisaRM;
use App\Models\AnalisaRMSampleDetail;
use App\Models\AnalisaRMSampleGlassware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
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
            $query = IdentitasRM::with(['analisaSampel', 'analisaLongTerm'])
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
                    $userRole = strtolower(trim(auth()->user()->role ?? ''));
                    $isSupervisor = in_array($userRole, ['supervisor', 'head of dapartement', 'head of department', 'dept head'], true);

                    $exportBtn = '';

                    // Tombol Export selalu muncul jika data sudah memiliki sampel analisa (baik draft/simpan sementara maupun final)
                    $hasAnalisa = $data->analisaSampel && $data->analisaSampel->isNotEmpty();

                    if ($hasAnalisa) {
                        $docRmUrl = route('doc-rm.index', ['id' => $data->id]);
                        $exportBtn = '
                            <a
                                class="btn btn-sm btn-success me-1"
                                href="' . $docRmUrl . '"
                                title="Buka Dokumen Laporan Kalkulasi RM"
                            >
                                <i class="ri-file-excel-2-line"></i>
                                Export
                            </a>
                        ';
                    }

                    $btn = '
                        <a
                            class="btn btn-sm btn-info me-1"
                            href="' . route('rmpm.show', $data->id) . '"
                        >
                            <span class="mdi mdi-eye"></span>
                            Lihat
                        </a>
                        ' . $exportBtn . '
                    ';

                    if ($isSupervisor) {
                        $btn .= '
                            <button
                                type="button"
                                class="btn btn-sm btn-danger btnDelete"
                                data-id="' . $data->id . '"
                                data-spb="' . htmlspecialchars($data->no_spb ?? '-', ENT_QUOTES) . '"
                                title="Hapus Data"
                            >
                                <span class="mdi mdi-delete"></span>
                                Hapus
                            </button>
                        ';
                    }

                    return $btn;
                })

                ->addColumn('status', function ($item) {
                    $status = '⌛ Proses';

                    // 1. Cek sampel analisa dari transaksi_analisa_rm
                    foreach ($item->transaksiAnalisa as $transaksi) {
                        if (!empty($transaksi->disposisi) && $transaksi->status !== 'draft') {
                            $status = '✅ Selesai';
                            break;
                        } elseif ($transaksi->status === 'draft') {
                            $status = '⌛ Draft';
                        }
                    }

                    // 2. Jika belum selesai, cek juga analisa Long Term
                    if ($status !== '✅ Selesai') {
                        foreach ($item->analisaLongTerm as $analisa) {
                            if (!empty($analisa->disposisi) && $analisa->status !== 'draft') {
                                $status = '✅ Selesai';
                                break;
                            } elseif ($analisa->status === 'draft' && $status !== '⌛ Draft') {
                                $status = '⌛ Draft';
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

    /**
     * Hapus data Raw Material (Identitas RM beserta seluruh relasi terkait).
     * Hanya dapat diakses oleh role Supervisor dan Head of Department.
     */
    public function destroy($id)
    {
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if (!in_array($userRole, ['supervisor', 'head of dapartement', 'head of department', 'dept head'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Fitur hapus data hanya dapat diakses oleh Supervisor.',
            ], 403);
        }

        try {
            $identitas = IdentitasRM::findOrFail($id);
            $noSpb = $identitas->no_spb ?? ('#' . $id);

            \DB::transaction(function () use ($identitas) {
                // Hapus data Identitas RM (foreign keys cascade menghapus analisa & sampling terkait)
                $identitas->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => "Data Raw Material dengan No SPB: {$noSpb} berhasil dihapus.",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Raw Material tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus data Raw Material: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export Dokumen Hasil Kalkulasi Analisa RM per SPB ke Excel
     */
    public function exportKalkulasi(Request $request, $id)
    {
        $identitas = IdentitasRM::with([
            'analisaShortTerm',
            'analisaGaramGula',
        ])->findOrFail($id);

        $standartBps = $request->input('standart_bps', $identitas->standart_bps ?? '');
        if ($request->has('standart_bps')) {
            $identitas->standart_bps = $standartBps;
            // Persist jika diubah
            \DB::table('identitas_rm')->where('id', $id)->update(['standart_bps' => $standartBps ?: null]);
        }

        $cleanSpb = preg_replace('/[^A-Za-z0-9_\-]/', '_', $identitas->no_spb ?? ('ID_' . $id));
        $cleanJenis = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper(trim($identitas->jenis ?? 'RM')));
        $timestamp = now()->format('Ymd_His');

        $fileName = "LAPORAN_KALKULASI_{$cleanJenis}_{$cleanSpb}_{$timestamp}.xlsx";

        return Excel::download(
            new LaporanKalkulasiRmExport($identitas),
            $fileName
        );
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
    ) {
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
    ) {
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
            'analisaBahanKering',
            'analisaBahanCair',
            'analisaLongTerm.user',
            'analisaLongTerm.histories.user',
            'analisaLongTermHistories.user',
        ])->findOrFail($id);

        $data_dokumen = $identitas->samplingDokumen;
        $data_mobil = $identitas->samplingKondisiMobil;
        $data_kemasan = $identitas->samplingFisikKemasan;
        $data_raw = $identitas->samplingFisikRaw;
        $analisa_sampel = $identitas->analisaSampel;
        $analisa_garam_gula = $analisa_sampel;
        $analisa_short_term = $analisa_sampel;
        $analisa_bahan_kering = $analisa_sampel;
        $analisa_bahan_cair = $analisa_sampel;
        $analisa_long_term = $identitas->analisaLongTerm;
        $analisa_long_term_histories = $identitas->analisaLongTermHistories;

        $konfirmasi = KonfirmasiKedatangan::where(
            'id_identitas',
            $id
        )->first();

        $dynamicFields = $this->getDynamicFieldsForMaterial($identitas->jenis);

        return view(
            'app.rmpm.show',
            compact(
                'identitas',
                'data_dokumen',
                'data_mobil',
                'data_kemasan',
                'data_raw',
                'analisa_sampel',
                'analisa_garam_gula',
                'analisa_short_term',
                'analisa_bahan_kering',
                'analisa_bahan_cair',
                'analisa_long_term',
                'analisa_long_term_histories',
                'konfirmasi',
                'dynamicFields'
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
            'analisaBahanKering',
            'analisaBahanCair',
        ])->findOrFail($id);

        $existingLongTerm = $identitas->analisaLongTerm->last();
        $existingSampel = $identitas->analisaSampel ?? collect();
        $existingShortTerm = $existingSampel;
        $existingGaramGula = $existingSampel;
        $existingBahanKering = $existingSampel;
        $existingBahanCair = $existingSampel;
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

        $beakerQuery = DB::table('analisa_rm_sample_glasswares')
            ->join('analisa_rm_transaksi', 'analisa_rm_sample_glasswares.transaksi_id', '=', 'analisa_rm_transaksi.id')
            ->whereDate('analisa_rm_sample_glasswares.created_at', today())
            ->whereNotNull('no_beaker')
            ->where('no_beaker', '!=', '')
            ->where('analisa_rm_transaksi.id_identitas', '!=', $id);

        $cawanQuery = DB::table('analisa_rm_sample_glasswares')
            ->join('analisa_rm_transaksi', 'analisa_rm_sample_glasswares.transaksi_id', '=', 'analisa_rm_transaksi.id')
            ->whereDate('analisa_rm_sample_glasswares.created_at', today())
            ->whereNotNull('no_cawan')
            ->where('no_cawan', '!=', '')
            ->where('analisa_rm_transaksi.id_identitas', '!=', $id);

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
            'total_today' => [
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

        // Preload Master Standards for this material using SQL DISTINCT
        $cleanJenis = strtoupper(trim((string)$identitas->jenis));
        $standards = MasterStandarRm::where('status', true)
            ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
            })
            ->select('parameter', 'min_standar', 'max_standar', 'target_text', 'uom', 'jenis_standar')
            ->distinct()
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

        $dynamicFields = $this->getDynamicFieldsForMaterial($identitas->jenis);

        return view(
            'app.rmpm.analisa',
            compact(
                'identitas',
                'existingLongTerm',
                'existingSampel',
                'existingShortTerm',
                'existingGaramGula',
                'existingBahanKering',
                'existingBahanCair',
                'histories',
                'initialGlassware',
                'initialStandards',
                'initialParameters',
                'dynamicFields'
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
            $cleanSpb = trim((string)$request->no_spb);
            $cleanJenis = strtolower(trim((string)$request->jenis));

            $exists = IdentitasRM::where('no_spb', $cleanSpb)
                ->whereRaw('LOWER(TRIM(jenis)) = ?', [$cleanJenis])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No SPB "' . $cleanSpb . '" dengan jenis material "' . $request->jenis . '" sudah pernah diinput sebelumnya (tidak boleh duplikat).',
                    'errors' => [
                        'no_spb' => ['Kombinasi No SPB dan jenis bahan ini sudah terdaftar. Tidak dapat diinput 2x.']
                    ]
                ], 422);
            }

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
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if ($userRole === 'analis field') {
            $saveAction = 'draft';
        }
        $isDraft = ($saveAction === 'draft');

        // Check if existing record is already final (locked)
        $existingLongTerm = AnalisaLongTerm::where('id_identitas', $request->id_identitas)->latest()->first();
        if ($existingLongTerm && $existingLongTerm->status && $existingLongTerm->status !== 'draft') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data analisa Long Term sudah berstatus FINAL dan tidak dapat diubah lagi.',
            ], 422);
        }

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
            $rules['attachments'] = ['nullable', 'array', 'max:5'];
            $rules['attachments.*'] = ['file', 'mimes:jpeg,jpg,png,webp', 'max:2048'];
        } elseif ($request->hasFile('attachment')) {
            $rules['attachment'] = ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'];
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
    | ANALISA SAMPEL TERPADU (SEMUA BAHAN BAKU)
    |--------------------------------------------------------------------------
    */
    public function storeSampel(Request $request)
    {
        $kategori = $request->input('kategori', 'incoming');
        if (!in_array($kategori, ['incoming', 'sta', 'monitoring'])) {
            $kategori = 'incoming';
        }

        $saveAction = $request->input('save_action', 'final');
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if ($userRole === 'analis field') {
            $saveAction = 'draft';
        }
        $isDraft = ($saveAction === 'draft');

        // Cek apakah data untuk kategori ini sudah FINAL
        $existing = TransaksiAnalisaRM::where('id_identitas', $request->id_identitas)
            ->where('kategori', $kategori)
            ->first();

        if ($existing && $existing->status && $existing->status !== 'draft') {
            $kategoriLabels = [
                'incoming'   => 'Incoming',
                'sta'        => 'STA (Short Term Analisa)',
                'monitoring' => 'Monitoring',
            ];
            $label = $kategoriLabels[$kategori] ?? 'Analisa';
            return response()->json([
                'status'  => 'error',
                'message' => "Data analisa {$label} sudah berstatus FINAL dan tidak dapat diubah lagi.",
            ], 422);
        }

        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'disposisi'    => $isDraft ? 'nullable|in:Release,Reject,' : 'required|in:Release,Reject',
            'keterangan'   => 'nullable|string',
            'kategori'     => 'nullable|string|in:incoming,sta,monitoring',
        ]);

        DB::beginTransaction();

        try {
            $fieldArrays = [
                $request->brix ?? [],
                $request->ph ?? [],
                $request->ka ?? [],
                $request['%ka'] ?? [],
                $request->kotoran ?? [],
                $request->nacl ?? [],
                $request['%nacl'] ?? [],
                $request->berat_jenis ?? [],
                $request->suhu ?? [],
                $request->aw ?? [],
                $request->viskositas ?? [],
                $request->tn ?? [],
                $request['%tn'] ?? [],
                $request->gross_weight ?? [],
                $request->organo ?? [],
                $request->warna ?? [],
                $request->aroma ?? [],
                $request->fisik ?? [],
                $request->rasa ?? [],
            ];
            $counts = array_map('count', $fieldArrays);
            $jumlah = max(1, ...$counts);

            // Simpan ke skema normalisasi dinamis (Table 2 Transaksi & Table 4 Sample Detail)
            $transaksi = TransaksiAnalisaRM::updateOrCreate(
                [
                    'id_identitas' => $request->id_identitas,
                    'kategori'     => $kategori,
                ],
                [
                    'disposisi'    => $userRole === 'analis field' ? ($existing->disposisi ?? null) : $request->disposisi,
                    'status'       => $isDraft ? 'draft' : 'final',
                    'keterangan'   => $userRole === 'analis field' ? ($existing->keterangan ?? null) : $request->keterangan,
                    'created_by'   => auth()->id(),
                ]
            );

            // Hapus sampel yang melebihi jumlah baru jika jumlah sampel dikurangi
            $transaksi->details()->where('sampel_ke', '>', $jumlah)->delete();
            $transaksi->glasswares()->where('sampel_ke', '>', $jumlah)->delete();

            $identitasObj = IdentitasRM::find($request->id_identitas);
            $cleanJenis = strtoupper(trim((string)($identitasObj?->jenis ?? '')));
            $masterStandards = MasterStandarRm::where('status', true)
                ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                    $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
                })
                ->get();

            $detailsBatch = [];
            $glasswaresBatch = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $sampelKe = $i + 1;

                // 1. Simpan setiap parameter yang didefinisikan di master standar
                foreach ($masterStandards as $std) {
                    $paramName = $std->parameter;
                    $upper = strtoupper(trim($paramName));
                    $val = null;

                    if (str_contains($upper, 'BRIX')) {
                        $val = $request->brix[$i] ?? null;
                    } elseif ($upper === 'PH' || preg_match('/\bPH\b/i', $upper)) {
                        $val = $request->ph[$i] ?? null;
                    } elseif (str_contains($upper, 'KOTORAN')) {
                        $val = $request->kotoran[$i] ?? null;
                    } elseif (str_contains($upper, 'KADAR AIR') || $upper === 'KA' || $upper === '%KA') {
                        $val = $request['%ka'][$i] ?? ($request->ka[$i] ?? null);
                    } elseif (str_contains($upper, 'NACL')) {
                        $val = $request['%nacl'][$i] ?? ($request->nacl[$i] ?? null);
                    } elseif (str_contains($upper, 'ORGANO') || str_contains($upper, 'RASA')) {
                        $val = $request->organo[$i] ?? ($request->rasa[$i] ?? null);
                    } elseif (str_contains($upper, 'WARNA')) {
                        $val = $request->warna[$i] ?? null;
                    } elseif (str_contains($upper, 'AROMA')) {
                        $val = $request->aroma[$i] ?? null;
                    } elseif (str_contains($upper, 'BERAT JENIS')) {
                        $val = $request->berat_jenis[$i] ?? null;
                    } elseif (str_contains($upper, 'SUHU')) {
                        $val = $request->suhu[$i] ?? null;
                    } elseif (str_contains($upper, 'AW')) {
                        $val = $request->aw[$i] ?? null;
                    } elseif (str_contains($upper, 'VISKOSITAS')) {
                        $val = $request->viskositas[$i] ?? null;
                    } elseif (str_contains($upper, 'TN') || str_contains($upper, 'NITROGEN')) {
                        $val = $request['%tn'][$i] ?? ($request->tn[$i] ?? null);
                    } elseif (str_contains($upper, 'WEIGHT') || str_contains($upper, 'GROSS')) {
                        $val = $request->gross_weight[$i] ?? null;
                    } elseif (str_contains($upper, 'FISIK')) {
                        $val = $request->fisik[$i] ?? null;
                    } else {
                        $slug = Str::slug($paramName, '_');
                        $val = $request->{$slug}[$i] ?? null;
                    }

                    if ($val !== null && $val !== '') {
                        $isSesuai = true;
                        if ($std->min_standar !== null && (float)$val < (float)$std->min_standar) {
                            $isSesuai = false;
                        }
                        if ($std->max_standar !== null && (float)$val > (float)$std->max_standar) {
                            $isSesuai = false;
                        }

                        $detailsBatch[] = [
                            'transaksi_id'      => $transaksi->id,
                            'sampel_ke'         => $sampelKe,
                            'master_standar_id' => $std->id,
                            'parameter'         => $paramName,
                            'nilai'             => (string)$val,
                            'is_sesuai'         => $isSesuai,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ];
                    }
                }

                // 2. Simpan glassware jika ada
                if (!empty($request->no_beaker[$i]) || !empty($request->no_cawan[$i])) {
                    $glasswaresBatch[] = [
                        'transaksi_id'     => $transaksi->id,
                        'sampel_ke'        => $sampelKe,
                        'no_beaker'        => $this->nullableString($request->no_beaker[$i] ?? null),
                        'berat_beaker_500' => $this->nullableFloat($request->berat_beaker_500[$i] ?? null),
                        'berat_beaker_250' => $this->nullableFloat($request->berat_beaker_250[$i] ?? null),
                        'timbang_a'        => $this->nullableFloat($request->timbang_a[$i] ?? null),
                        'timbang_b'        => $this->nullableFloat($request->timbang_b[$i] ?? null),
                        'no_cawan'         => $this->nullableString($request->no_cawan[$i] ?? null),
                        'berat_cawan'      => $this->nullableFloat($request->berat_cawan[$i] ?? null),
                        'timbang_aa'       => $this->nullableFloat($request->timbang_aa[$i] ?? null),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            if (!empty($detailsBatch)) {
                foreach ($detailsBatch as $d) {
                    AnalisaRMSampleDetail::updateOrCreate(
                        [
                            'transaksi_id'      => $d['transaksi_id'],
                            'sampel_ke'         => $d['sampel_ke'],
                            'master_standar_id' => $d['master_standar_id'],
                        ],
                        [
                            'parameter'  => $d['parameter'],
                            'nilai'      => $d['nilai'],
                            'is_sesuai'  => $d['is_sesuai'],
                        ]
                    );
                }
            }
            if (!empty($glasswaresBatch)) {
                foreach ($glasswaresBatch as $g) {
                    AnalisaRMSampleGlassware::updateOrCreate(
                        [
                            'transaksi_id' => $g['transaksi_id'],
                            'sampel_ke'    => $g['sampel_ke'],
                        ],
                        $g
                    );
                }
            }

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

    public function storeShortTerm(Request $request)
    {
        return $this->storeSampel($request);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA GARAM DAN GULA
    |--------------------------------------------------------------------------
    */

        public function storeGaramGula(Request $request)
    {
        return $this->storeSampel($request);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA BAHAN KERING (MSG, ND, GULA RAFINASI)
    |--------------------------------------------------------------------------
    */
    public function storeBahanKering(Request $request)
    {
        return $this->storeSampel($request);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISA BAHAN CAIR (GLUKOSA, HFCS, SARI KECAP, KARAMEL)
    |--------------------------------------------------------------------------
    */
    public function storeBahanCair(Request $request)
    {
        return $this->storeSampel($request);
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
            TransaksiAnalisaRM::where('id_identitas', $id)
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

    public function getDynamicFieldsForMaterial($jenis): array
    {
        $cleanJenis = strtoupper(trim((string)$jenis));
        $jb = MasterJenisBahan::whereRaw('UPPER(nama) = ?', [$cleanJenis])
            ->orWhere('nama', 'LIKE', '%' . trim((string)$jenis) . '%')
            ->first();

        $standards = collect();
        if ($jb) {
            // DISTINCT SQL: Ambil parameter unik dari database master standar
            $standards = MasterStandarRm::where('status', true)
                ->where('id_jenis_bahan', $jb->id)
                ->select('parameter', 'jenis_standar', 'min_standar', 'max_standar', 'target_text', 'uom')
                ->distinct()
                ->get();
        }

        $isGaram = str_contains($cleanJenis, 'GARAM');

        if ($standards->isEmpty()) {
            return [
                ['name' => 'brix', 'key' => 'brix[]', 'label' => 'Brix', 'type' => 'number', 'unit' => '°Bx'],
                ['name' => 'ph', 'key' => 'ph[]', 'label' => 'pH', 'type' => 'number', 'unit' => ''],
                ['name' => 'kotoran', 'key' => 'kotoran[]', 'label' => '% Kotoran', 'type' => 'number', 'unit' => '%'],
                ['name' => 'ka', 'key' => 'ka[]', 'label' => '% KA', 'type' => 'number', 'unit' => '%'],
                ['name' => 'organo', 'key' => 'organo[]', 'label' => 'Organo', 'type' => 'organo-select', 'unit' => ''],
                ['name' => 'warna', 'key' => 'warna[]', 'label' => 'Warna', 'type' => 'dropdown', 'masterKey' => 'warna', 'unit' => ''],
                ['name' => 'aroma', 'key' => 'aroma[]', 'label' => 'Aroma', 'type' => 'dropdown', 'masterKey' => 'aroma', 'unit' => ''],
            ];
        }

        $fields = [];
        foreach ($standards as $std) {
            $paramRaw = trim($std->parameter);
            $upper = strtoupper($paramRaw);
            $uom = trim((string)$std->uom);

            if (str_contains($upper, 'BRIX')) {
                $fields[] = [
                    'name' => 'brix',
                    'key' => 'brix[]',
                    'label' => 'Brix',
                    'type' => 'number',
                    'unit' => $uom ?: '°Bx',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif ($upper === 'PH' || preg_match('/\bPH\b/i', $upper)) {
                $fields[] = [
                    'name' => 'ph',
                    'key' => 'ph[]',
                    'label' => 'pH',
                    'type' => 'number',
                    'unit' => $uom ?: '',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif (str_contains($upper, 'KOTORAN')) {
                $isKuali = (strtolower((string)$std->jenis_standar) === 'kuali')
                    || ($std->min_standar === null && $std->max_standar === null && !str_contains($upper, '%') && ($uom === '' || $uom === null));

                if ($isKuali) {
                    $fields[] = [
                        'name' => 'kotoran',
                        'key' => 'kotoran[]',
                        'label' => 'Kotoran',
                        'type' => 'text',
                        'unit' => '',
                        'target' => $std->target_text,
                    ];
                } else {
                    $fields[] = [
                        'name' => 'kotoran',
                        'key' => 'kotoran[]',
                        'label' => '% Kotoran',
                        'type' => 'number',
                        'unit' => $uom ?: '%',
                        'min' => $std->min_standar,
                        'max' => $std->max_standar,
                    ];
                }
            } elseif (str_contains($upper, 'KADAR AIR') || $upper === 'KA' || $upper === '%KA') {
                $fieldKey = $isGaram ? '%ka[]' : 'ka[]';
                $fieldName = $isGaram ? '%ka' : 'ka';
                $fields[] = [
                    'name' => $fieldName,
                    'key' => $fieldKey,
                    'label' => $isGaram ? '%KA' : '% KA',
                    'type' => 'number',
                    'unit' => '%',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif (str_contains($upper, 'ORGANO') || str_contains($upper, 'RASA')) {
                $fields[] = [
                    'name' => 'organo',
                    'key' => 'organo[]',
                    'label' => 'Organo',
                    'type' => 'organo-select',
                    'unit' => '',
                ];
            } elseif (str_contains($upper, 'WARNA')) {
                $fields[] = [
                    'name' => 'warna',
                    'key' => 'warna[]',
                    'label' => 'Warna',
                    'type' => 'dropdown',
                    'masterKey' => 'warna',
                    'unit' => '',
                ];
            } elseif (str_contains($upper, 'AROMA')) {
                $fields[] = [
                    'name' => 'aroma',
                    'key' => 'aroma[]',
                    'label' => 'Aroma',
                    'type' => 'dropdown',
                    'masterKey' => 'aroma',
                    'unit' => '',
                ];
            } elseif (str_contains($upper, 'NACL')) {
                $fields[] = [
                    'name' => '%nacl',
                    'key' => '%nacl[]',
                    'label' => '%NaCl',
                    'type' => 'number',
                    'unit' => '%',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif (str_contains($upper, 'BERAT JENIS')) {
                $fields[] = [
                    'name' => 'berat_jenis',
                    'key' => 'berat_jenis[]',
                    'label' => 'Berat Jenis',
                    'type' => 'number',
                    'unit' => $uom ?: '',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif (str_contains($upper, 'WEIGHT') || str_contains($upper, 'BERAT') || str_contains($upper, 'GROSS')) {
                $fields[] = [
                    'name' => 'gross_weight',
                    'key' => 'gross_weight[]',
                    'label' => 'Gross Weight',
                    'type' => 'number',
                    'unit' => 'kg',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            } elseif (str_contains($upper, 'FISIK')) {
                $fields[] = [
                    'name' => 'fisik',
                    'key' => 'fisik[]',
                    'label' => 'Fisik',
                    'type' => 'text',
                    'unit' => '',
                    'target' => $std->target_text,
                ];
            } else {
                $slug = Str::slug($paramRaw, '_');
                $isNumeric = ($std->min_standar !== null || $std->max_standar !== null || is_numeric($std->target_text));
                $fields[] = [
                    'name' => $slug,
                    'key' => $slug . '[]',
                    'label' => $paramRaw,
                    'type' => $isNumeric ? 'number' : 'text',
                    'unit' => $uom ?: '',
                    'min' => $std->min_standar,
                    'max' => $std->max_standar,
                ];
            }
        }

        // Keep sensory parameters (Organo, Warna, Aroma) at the end of the table
        $measurementFields = [];
        $sensoryFields = [];
        $sensoryNames = ['organo', 'warna', 'aroma'];

        foreach ($fields as $f) {
            if (in_array($f['name'], $sensoryNames)) {
                $sensoryFields[] = $f;
            } else {
                $measurementFields[] = $f;
            }
        }

        return collect(array_merge($measurementFields, $sensoryFields))
            ->unique('name')
            ->values()
            ->all();
    }
}
