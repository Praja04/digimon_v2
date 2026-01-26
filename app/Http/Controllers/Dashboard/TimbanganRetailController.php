<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TimbanganRetailMesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimbanganRetailController extends Controller
{
    public function index()
    {
        $beratHariIni = TimbanganRetailMesin::whereDate('waktu', today())
            ->sum('berat');

        $transaksiHariIni = TimbanganRetailMesin::whereDate('waktu', today())
            ->count();

        $rataRataBerat = TimbanganRetailMesin::whereDate('waktu', today())
            ->avg('berat');

        $mesinAktif = TimbanganRetailMesin::whereDate('waktu', today())
            ->select('mesin', DB::raw('count(*) as total'))
            ->groupBy('mesin')
            ->orderBy('total', 'desc')
            ->first();

        $dataMesin = TimbanganRetailMesin::select('mesin', DB::raw('count(*) as total_transaksi'), DB::raw('sum(berat) as total_berat'))
            ->whereDate('waktu', today())
            ->groupBy('mesin')
            ->get();

        $transaksiTerbaru = TimbanganRetailMesin::orderBy('waktu', 'desc')
            ->limit(10)
            ->get();

        $transaksiPerJam = TimbanganRetailMesin::whereDate('waktu', today())
            ->select(DB::raw('HOUR(waktu) as jam'), DB::raw('count(*) as total'))
            ->groupBy('jam')
            ->orderBy('jam')
            ->get();

        $beratPerVariant = TimbanganRetailMesin::whereDate('waktu', today())
            ->select('variant', DB::raw('sum(berat) as total_berat'))
            ->groupBy('variant')
            ->orderBy('total_berat', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.timbangan-retail.index', compact(
            'beratHariIni',
            'transaksiHariIni',
            'rataRataBerat',
            'mesinAktif',
            'dataMesin',
            'transaksiTerbaru',
            'transaksiPerJam',
            'beratPerVariant'
        ));
    }
}
