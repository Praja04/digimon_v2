<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
