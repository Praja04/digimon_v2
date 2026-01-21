<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\MonitoringTurunBlendingRelation;
use App\Models\Color;
use App\Models\User;

class MonitoringTurunBlending extends Model
{
    protected $table = 'monitoring_turun_blending';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function additionalBatches()
    {
        return $this->hasMany(MonitoringTurunBlendingRelation::class, 'monitoring_turun_blending_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
