<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\MonitoringPasteurisasiRelation;
use App\Models\Color;
use App\Models\User;

class MonitoringPasteurisasi extends Model
{
    protected $table = 'monitoring_pasteurisasi';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function additionalBatches()
    {
        return $this->hasMany(MonitoringPasteurisasiRelation::class, 'monitoring_pasteurisasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
