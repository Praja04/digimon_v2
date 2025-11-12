<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringOnGoingMikro extends Model
{
    protected $table = 'monitoring_on_going_mikro';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->hasOne(ProductionBatch::class, 'id', 'production_batch_id');
    }

    public function analis()
    {
        return $this->belongsTo(User::class, 'analis_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function analisEb()
    {
        return $this->belongsTo(User::class, 'analis_eb');
    }

    public function analisTpc()
    {
        return $this->belongsTo(User::class, 'analis_tpc');
    }

    public function analisYm()
    {
        return $this->belongsTo(User::class, 'analis_ym');
    }

    public function analisBendaAsing()
    {
        return $this->belongsTo(User::class, 'analis_benda_asing');
    }
}
