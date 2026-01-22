<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PressTestMesinController extends Controller
{
    public function index()
    {
        return view('dashboard.press-test-mesin.index');
    }
}
