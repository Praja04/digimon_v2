<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RMPMController extends Controller
{
    public function index()
    {
        return view('dashboard.rmpm.index');
    }
}
