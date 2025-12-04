<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BlendingAwal;
use App\Models\MonitoringTurunBlending;
use App\Models\MonitoringStorageKimia;
use App\Models\MonitoringDailyTank;

class Color extends Model
{
    protected $guarded = [];

    public function blendingAwal()
    {
        return $this->hasMany(BlendingAwal::class);
    }

    public function monitoringTurunBlending()
    {
        return $this->hasMany(MonitoringTurunBlending::class);
    }

    public function monitoringStorageKimia()
    {
        return $this->hasMany(MonitoringStorageKimia::class);
    }

    public function monitoringDailyTank()
    {
        return $this->hasMany(MonitoringDailyTank::class);
    }

    public function monitoringOnGoingKimia()
    {
        return $this->hasMany(MonitoringOnGoingKimia::class);
    }

    public function monitoringOnGoingMikro()
    {
        return $this->hasMany(MonitoringOnGoingMikro::class);
    }
}
