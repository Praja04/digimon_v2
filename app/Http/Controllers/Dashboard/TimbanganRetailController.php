<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TimbanganRetailMesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TimbanganRetailController extends Controller
{
    public function index()
    {
        return view('dashboard.timbangan-retail.index');
    }

    public function analisa()
    {
        return view('dashboard.timbangan-retail.analisa');
    }
}
