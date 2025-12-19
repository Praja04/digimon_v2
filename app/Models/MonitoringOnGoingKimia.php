<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringOnGoingKimia extends Model
{
    protected $table = 'monitoring_on_going_kimia';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->hasOne(ProductionBatch::class, 'id', 'production_batch_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
