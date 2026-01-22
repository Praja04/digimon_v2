<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\PressTestMesin1Export;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PressTestMesinController extends Controller
{
    public function index()
    {
        return view('dashboard.press-test-mesin.index');
    }

    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $variant = $request->input('variant');
        $status = $request->input('status');
        $limit = $request->input('limit');

        $fileName = 'Press_Test_Mesin_1_' .
            ($tanggal ? $tanggal : 'All') . '_' .
            ($variant ? str_replace(' ', '_', $variant) : 'All_Variant') . '_' .
            now()->timestamp . '.xlsx';

        return Excel::download(
            new PressTestMesin1Export(
                $tanggal,
                $variant,
                $status,
                $limit
            ),
            $fileName
        );
    }
}
