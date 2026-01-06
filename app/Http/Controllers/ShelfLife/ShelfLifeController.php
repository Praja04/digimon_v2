<?php

namespace App\Http\Controllers\ShelfLife;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShelfLifeController extends Controller
{
    public function index()
    {
        return view('app.shelf_life.index');
    }
}
