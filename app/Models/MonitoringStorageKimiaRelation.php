<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MonitoringStorageKimia;

class MonitoringStorageKimiaRelation extends Model
{
    protected $guarded = [];

    public function monitoringStorageKimia()
    {
        return $this->belongsTo(MonitoringStorageKimia::class);
    }
}
