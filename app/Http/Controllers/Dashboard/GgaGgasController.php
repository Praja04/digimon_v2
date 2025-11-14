<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GGA;
use App\Models\GGAS;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GgaGgasController extends Controller
{
    public function index()
    {
        return view('dashboard.gga-ggas.index');
    }
}
