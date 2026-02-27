<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class PelarutanController extends Controller
{
    public function index()
    {
        return view('dashboard.pelarutan.index');
    }
}
