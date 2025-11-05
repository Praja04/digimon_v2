<?php

namespace App\Http\Controllers;

use App\Models\IdentitasRM;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\RMPMStoreRequest;
use App\Models\AnalisaGaramGula;
use App\Models\AnalisaLongTerm;
use App\Models\AnalisaShortTerm;
use App\Models\Color;
use App\Models\KonfirmasiKedatangan;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\Facades\DNS2DFacade;

class RMPMController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = IdentitasRM::query()->orderBy('created_at', 'desc');

            // Filter berdasarkan tanggal mulai
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal_kedatangan', '>=', $request->start_date);
            }

            // Filter berdasarkan tanggal akhir
            if ($request->filled('end_date')) {
                $query->whereDate('tanggal_kedatangan', '<=', $request->end_date);
            }

            // Filter berdasarkan jenis
            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            $identitas_rm = $query->get();

            return DataTables::of($identitas_rm)
                ->addIndexColumn()
                ->editColumn('tanggal_kedatangan', function ($data) {
                    return \Carbon\Carbon::parse($data->tanggal_kedatangan)->locale('id')->isoFormat('D MMMM Y');
                })
                ->addColumn('qr_code', function ($data) {
                    return '
                        <button class="btn btn-sm btn-primary me-1" id="btnQRCode" data-id="' . $data->id . '">
                           <span class="mdi mdi-qrcode"></span> QR Code
                        </button>
                    ';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <a class="btn btn-sm btn-info me-1" href="' . route('rmpm.show', $data->id) . '">
                           <span class="mdi mdi-eye"></span> Lihat
                        </a>
                    ';
                })
                ->addColumn('status', function ($item) {
                    $status = '⌛ Proses';
                    if (in_array($item->jenis, ['Garam', 'Gula'])) {
                        foreach ($item->analisaGaramGula as $analisa) {
                            if ($analisa->disposisi) {
                                $status = '✅ Selesai';
                                break;
                            }
                        }
                    } elseif (in_array($item->jenis, ['Gula Tebu', 'Gula Kelapa'])) {
                        foreach ($item->analisaLongTerm as $analisa) {
                            if (!empty($analisa->disposisi)) {
                                $status = '✅ Selesai';
                                break;
                            }
                        }
                    }
                    return $status;
                })
                ->rawColumns(['action', 'qr_code'])
                ->make(true);
        }
        return view('app.rmpm.index');
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

        $colors = Color::orderBy('name', 'asc')->get();

        return view('app.rmpm.show', compact('identitas', 'data_dokumen', 'data_mobil', 'data_kemasan', 'data_raw', 'analisa_garam_gula', 'analisa_short_term', 'analisa_long_term', 'colors'));
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

            $qrText = route('rmpm.show', $id);
            $qrCode = DNS2DFacade::getBarcodePNG($qrText, 'QRCODE');

            return response()->json([
                'status' => 'success',
                'qrCode' => $qrCode,
                'label' => 'RM - ' . $identitas->id,
                'tanggal' => \Carbon\Carbon::parse($identitas->tanggal_kedatangan)->locale('id')->isoFormat('D MMMM Y')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
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
        // Validasi awal yang selalu dicek
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'uji_kristal'  => 'required|in:positif,negatif',
        ]);

        $ujiKristal = $request->uji_kristal;
        $attachmentName = null;
        $disposisi = null;

        if ($ujiKristal === 'negatif') {
            $attachmentName = '-';
            $disposisi = 'Release';
        } else {
            // Jika positif: attachment wajib
            $request->validate([
                'attachment' => 'required|image|mimes:jpg,jpeg,png,gif|max:5000',
            ]);

            if ($request->hasFile('attachment')) {
                $filename = 'attachment_' . time() . '_' . uniqid() . '.' . $request->attachment->extension();
                $request->file('attachment')->storeAs('uploads/attachment_analisa', $filename, 'public');
                $attachmentName = basename($filename);
            }
        }

        AnalisaLongTerm::create([
            'id_identitas'    => $request->id_identitas,
            'uji_kristal'     => $ujiKristal,
            'disposisi'       => $disposisi,
            'attachment'      => $attachmentName,
            'created_by' => auth()->user()->id,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    public function storeShortTerm(Request $request)
    {
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'brix' => 'nullable|array',
            'ph' => 'nullable|array',
            'kotoran' => 'nullable|array',
            'ka' => 'nullable|array',
            'organo' => 'nullable|array',
            'warna' => 'nullable|array',
            'aroma' => 'nullable|array',
            'disposisi' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $brix = array_map(fn($val) => str_replace(',', '.', $val), $request->brix);
        $ph = array_map(fn($val) => str_replace(',', '.', $val), $request->ph);

        $ka = array_map(fn($val) => str_replace(',', '.', $val), $request->ka);

        DB::beginTransaction();

        try {
            $jumlah = count($request->brix);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas'    => $request->id_identitas,
                    'brix'            => $brix[$i] ?? null,
                    'ph'              => $ph[$i] ?? null,
                    'kotoran'         => $request->kotoran[$i] ?? null,
                    'ka'              => $ka[$i] ?? null,
                    'organo'          => $request->organo[$i] ?? null,
                    'warna'           => $request->warna[$i] ?? null,
                    'aroma'           => $request->aroma[$i] ?? null,
                    'disposisi'       => $request->disposisi ?? null,
                    'created_by'        => auth()->user()->id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            // 4. Insert semua data analisa
            AnalisaShortTerm::insert($dataAnalisa);

            DB::commit();

            return response()->json(['message' => 'Berhasil menyimpan data analisa dan disposisi'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function storeGaramGula(Request $request)
    {
        $request->validate([
            'id_identitas' => 'required|exists:identitas_rm,id',
            'fisik' => 'nullable|array',
            '%ka' => 'nullable|array',
            'kotoran' => 'nullable|array',
            'organo' => 'nullable|array',
            'warna' => 'nullable|array',
            'aroma' => 'nullable|array',
            '%nacl' => 'nullable|array',
            'gross_weight' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $jumlah = count($request->fisik);
            $dataAnalisa = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataAnalisa[] = [
                    'id_identitas'  => $request->id_identitas,
                    'fisik'         => $request->fisik[$i] ?? null,
                    '%ka'           => $request['%ka'][$i] ?? null,
                    'kotoran'       => $request->kotoran[$i] ?? null,
                    'organo'        => $request->organo[$i] ?? null,
                    'warna'         => $request->warna[$i] ?? null,
                    'aroma'         => $request->aroma[$i] ?? null,
                    '%nacl'         => $request['%nacl'][$i] ?? null,
                    'gross_weight'  => $request->gross_weight[$i] ?? null,
                    'disposisi'    => $request->disposisi ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                    'created_by' => auth()->user()->id,
                ];
            }

            AnalisaGaramGula::insert($dataAnalisa);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data analisa garam gula berhasil disimpan.'
            ], 201);
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
}
