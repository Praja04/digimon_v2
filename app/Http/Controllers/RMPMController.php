<?php

namespace App\Http\Controllers;

use App\Models\IdentitasRM;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\RMPMStoreRequest;
use App\Models\AnalisaGaramGula;
use App\Models\AnalisaLongTerm;
use App\Models\AnalisaShortTerm;
use App\Models\KonfirmasiKedatangan;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\Facades\DNS2DFacade;

class RMPMController extends Controller
{
public function index()
{
    return view('app.rmpm.menu');
}

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

                if (in_array(
                    $item->jenis,
                    ['Garam', 'Gula'],
                    true
                )) {
                    foreach ($item->analisaGaramGula as $analisa) {
                        if ($analisa->disposisi) {
                            $status = '✅ Selesai';
                            break;
                        }
                    }
                } elseif (in_array(
                    $item->jenis,
                    ['Gula Tebu', 'Gula Kelapa'],
                    true
                )) {
                    foreach ($item->analisaLongTerm as $analisa) {
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

public function pm()
{
    return view('app.rmpm.pm');
}

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
        $konfirmasi = KonfirmasiKedatangan::where('id_identitas', $id)->first();

        return view('app.rmpm.show', compact('identitas', 'data_dokumen', 'data_mobil', 'data_kemasan', 'data_raw', 'analisa_garam_gula', 'analisa_short_term', 'analisa_long_term', 'konfirmasi'));
    }

    public function showAnalisa($id)
    {
        $identitas = IdentitasRM::findOrFail($id);
        return view('app.rmpm.analisa', compact('identitas'));
    }

    public function store(RMPMStoreRequest $request)
    {
        try {
            IdentitasRM::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Data identitas RM berhasil disimpan.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQRCode($id)
    {
        try {
            $identitas = IdentitasRM::findOrFail($id);

            $qrText = url('/rmpm/' . $id . '/analisa');
            $qrCode = DNS2DFacade::getBarcodePNG($qrText, 'QRCODE');

            $tanggal = \Carbon\Carbon::parse($identitas->created_at)->format('Y-m-d');
            $label = 'RMPM/' . $identitas->no_spb . '/' . $tanggal . '/' . $identitas->id;

            return response()->json([
                'status'  => 'success',
                'qrCode'  => $qrCode,
                'label'   => $label,
                'tanggal' => \Carbon\Carbon::parse($identitas->tanggal_kedatangan)->locale('id')->isoFormat('D MMMM Y'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal generate QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKonfirmasi($id)
    {
        try {
            $identitas = IdentitasRM::findOrFail($id);

            $identitas = IdentitasRM::with([
                'samplingKondisiMobil',
                'samplingDokumen',
                'samplingFisikKemasan',
                'samplingFisikRaw',
            ])->findOrFail($id);

            $jamAnalisaExist = KonfirmasiKedatangan::where('id_identitas', $id)->exists();

            return response()->json([
                'jam_analisa_exists' => $jamAnalisaExist,
                'sampling_complete' => $identitas->isSamplingComplete()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateKonfirmasi(Request $request)
    {
        $request->validate([
            'jam' => 'required'
        ]);

        // Cek apakah sudah ada data konfirmasi
        $konfirmasi = KonfirmasiKedatangan::where('id_identitas', $request->id)->first();

        if ($konfirmasi) {
            // Update data yang sudah ada
            $konfirmasi->update([
                //'jam_kedatangan' => $request->jam_kedatangan,
                'waktu_analisa' => $request->jam,
                'dianalisa_by' => auth()->user()->id,
            ]);
        } else {
            // Buat data baru
            KonfirmasiKedatangan::create([
                'id_identitas' => $request->id,
                'waktu_kedatangan' => $request->jam,
                'diterima_by' => auth()->user()->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data konfirmasi berhasil disimpan.'
        ], 201);
    }

    public function storeLongTerm(Request $request)
    {
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'uji_kristal'  => 'required|in:positif,negatif',
            'keterangan'   => 'nullable|string',
        ]);

        $ujiKristal = $request->uji_kristal;
        $attachmentName = null;
        $disposisi = null;

        if ($ujiKristal === 'negatif') {
            $attachmentName = '-';
            $disposisi = 'Release';
        } else {
            $request->validate([
                'attachment' => 'required|image|mimes:jpg,jpeg,png,gif|max:5000',
            ]);

            if ($request->hasFile('attachment')) {
                $filename = 'attachment_' . time() . '_' . uniqid() . '.' . $request->attachment->extension();
                $request->file('attachment')->storeAs('uploads/attachment_analisa', $filename, 'public');
                $attachmentName = basename($filename);
            }

            $disposisi = $request->disposisi;
        }

        AnalisaLongTerm::create([
            'id_identitas'    => $request->id_identitas,
            'uji_kristal'     => $ujiKristal,
            'disposisi'       => $disposisi,
            'attachment'      => $attachmentName,
            'keterangan'      => $request->keterangan,
            'created_by'      => auth()->user()->id,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json(['message' => 'Data analisa long term berhasil disimpan.'], 201);
    }

    public function storeShortTerm(Request $request)
    {
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'brix'         => 'required|array|min:1',
            'brix.*'       => 'required|string',
            'ph'           => 'required|array|min:1',
            'ph.*'         => 'required|string',
            'kotoran'      => 'nullable|array',
            'ka'           => 'required|array|min:1',
            'ka.*'         => 'required|string',
            'organo'       => 'nullable|array',
            'warna'        => 'nullable|array',
            'aroma'        => 'nullable|array',
            'disposisi'    => 'required|in:Release,Reject',
            'keterangan'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $jumlah     = count($request->brix);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas' => $request->id_identitas,
                    'brix'         => $this->nullableFloat($request->brix[$i]    ?? null),
                    'ph'           => $this->nullableFloat($request->ph[$i]      ?? null),
                    'kotoran'      => $this->nullableFloat($request->kotoran[$i] ?? null),
                    'ka'           => $this->nullableFloat($request->ka[$i]      ?? null),
                    'organo'       => $this->nullableString($request->organo[$i] ?? null),
                    'warna'        => $this->nullableString($request->warna[$i]  ?? null),
                    'aroma'        => $this->nullableString($request->aroma[$i]  ?? null),
                    'disposisi'    => $request->disposisi,
                    'keterangan'    => $request->keterangan,
                    'created_by'   => auth()->id(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            AnalisaShortTerm::insert($dataAnalisa);
            DB::commit();

            return response()->json(['message' => 'Berhasil menyimpan data analisa short term.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function storeGaramGula(Request $request)
    {
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'fisik'        => 'required|array|min:1',
            'fisik.*'      => 'required|string',
            '%ka'          => 'nullable|array',
            'kotoran'      => 'nullable|array',
            'organo'       => 'nullable|array',
            'warna'        => 'nullable|array',
            'aroma'        => 'nullable|array',
            '%nacl'        => 'nullable|array',
            'gross_weight' => 'nullable|array',
            'disposisi'    => 'required|in:Release,Reject',
            'keterangan'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $jumlah      = count($request->fisik);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas' => $request->id_identitas,
                    'fisik'        => $this->nullableString($request->fisik[$i]           ?? null),
                    '%ka'          => $this->nullableFloat($request['%ka'][$i]            ?? null),
                    'kotoran'      => $this->nullableFloat($request->kotoran[$i]          ?? null),
                    'organo'       => $this->nullableString($request->organo[$i]          ?? null),
                    'warna'        => $this->nullableString($request->warna[$i]           ?? null),
                    'aroma'        => $this->nullableString($request->aroma[$i]           ?? null),
                    '%nacl'        => $this->nullableFloat($request['%nacl'][$i]          ?? null),
                    'gross_weight' => $this->nullableFloat($request->gross_weight[$i]     ?? null),
                    'disposisi'    => $request->disposisi,
                    'keterangan'    => $request->keterangan,
                    'created_by'   => auth()->id(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            AnalisaGaramGula::insert($dataAnalisa);
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data analisa berhasil disimpan.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function updateDisposisiLongTerm(Request $request)
    {
        $request->validate([
            'disposisi' => 'required|in:Release,Reject',
        ]);

        $data = AnalisaLongTerm::findOrFail($request->id);
        $data->disposisi = $request->disposisi;
        $data->save();

        return response()->json([
            'message' => 'Disposisi berhasil diperbarui.',
            'data' => $data
        ]);
    }

    private function nullableFloat($value): ?float
    {
        if ($value === null || trim((string) $value) === '') return null;
        return (float) str_replace(',', '.', $value);
    }

    private function nullableString($value): ?string
    {
        if ($value === null || trim((string) $value) === '') return null;
        return strtoupper(trim($value));
    }
}
