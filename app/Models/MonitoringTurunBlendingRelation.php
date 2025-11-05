<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringTurunBlendingRelation extends Model
{
    protected $guarded = [];

    public function monitoringTurunBlending()
    {
        return $this->belongsTo(MonitoringTurunBlending::class);
    }

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }
}
