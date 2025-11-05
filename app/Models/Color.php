<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $guarded = [];

    public function gga()
    {
        return $this->hasMany(GGA::class);
    }

    public function ggas()
    {
        return $this->hasMany(GGAS::class);
    }

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
}
