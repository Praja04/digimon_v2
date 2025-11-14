<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlendingAfterAdjustMikro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlendingAfterAdjustController extends Controller
{
    public function index()
    {
        return view('dashboard.blending-after-adjust.index');
    }
}
