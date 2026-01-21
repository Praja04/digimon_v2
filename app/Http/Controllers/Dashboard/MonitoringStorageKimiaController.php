<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringStorageKimiaController extends Controller
{
    public function index()
    {
        return view('dashboard.monitoring-storage-kimia.index');
    }
}
