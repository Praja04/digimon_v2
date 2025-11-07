<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MonitoringPasteurisasi;

class MonitoringPasteurisasiRelation extends Model
{
    protected $table = 'monitoring_pasteurisasi_relations';

    protected $guarded = [];

    public function monitoringPasteurisasi()
    {
        return $this->belongsTo(MonitoringPasteurisasi::class);
    }
}
