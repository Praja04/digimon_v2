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
        $today = now()->format('Y-m-d');

        // Periksa apakah hari ini ada data kedatangan RM atau PM
        $hasTodayData = IdentitasRM::whereDate('tanggal_kedatangan', $today)->exists()
            || PackagingIncoming::whereDate('tanggal_kedatangan', $today)->exists();

        if ($hasTodayData) {
            $defaultDate = $today;
        } else {
            // Jika hari ini belum ada transaksi, gunakan tanggal transaksi kedatangan terbaru
            $latestRmDate = IdentitasRM::max('tanggal_kedatangan');
            $latestPmDate = PackagingIncoming::max('tanggal_kedatangan');
            $latestDate = max($latestRmDate, $latestPmDate);

            $defaultDate = $latestDate ? date('Y-m-d', strtotime($latestDate)) : $today;
        }

        return view('dashboard.rmpm.index', compact('defaultDate'));
    }
}
