<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\IdentitasRM;
use App\Models\PackagingIncoming;
use Illuminate\Http\Request;

class RMPMController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard.rm.index');
    }

    public function rm()
    {
        $today = now()->format('Y-m-d');
        $hasTodayData = IdentitasRM::whereDate('tanggal_kedatangan', $today)->exists();

        if ($hasTodayData) {
            $defaultDate = $today;
        } else {
            $latestRmDate = IdentitasRM::max('tanggal_kedatangan');
            $defaultDate = $latestRmDate ? date('Y-m-d', strtotime($latestRmDate)) : $today;
        }

        return view('dashboard.rm.index', compact('defaultDate'));
    }

    public function pm()
    {
        $today = now()->format('Y-m-d');
        $hasTodayData = PackagingIncoming::whereDate('tanggal_kedatangan', $today)->exists();

        if ($hasTodayData) {
            $defaultDate = $today;
        } else {
            $latestPmDate = PackagingIncoming::max('tanggal_kedatangan');
            $defaultDate = $latestPmDate ? date('Y-m-d', strtotime($latestPmDate)) : $today;
        }

        return view('dashboard.pm.index', compact('defaultDate'));
    }
}
