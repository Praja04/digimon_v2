<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringStorageMikro extends Model
{
    protected $table = 'monitoring_storage_mikro';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function konfirmasi()
    {
        return $this->hasOne(KonfirmasiMonitoringStorageMikro::class, 'monitoring_storage_mikro_id');
    }
}
