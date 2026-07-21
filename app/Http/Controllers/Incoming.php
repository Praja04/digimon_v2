<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;

class IncomingController extends Controller
{
    public function index()
    {
        $incomings = JenisIncoming::all();

        return view('app.incoming.index', compact('incomings'));
    }
}