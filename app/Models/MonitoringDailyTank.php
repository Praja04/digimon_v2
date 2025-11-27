<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Color;

class MonitoringDailyTank extends Model
{
    protected $table = 'monitoring_daily_tank';

    protected $guarded = [];

    public function qcField()
    {
        return $this->belongsTo(User::class, 'qc_field');
    }

    public function qcAnalisa()
    {
        return $this->belongsTo(User::class, 'qc_analisa');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }
}
