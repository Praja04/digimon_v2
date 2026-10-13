<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\KonfirmasiMonitoringStorageMikro;

class MonitoringStorageMikro extends Model
{
    protected $table = 'monitoring_storage_mikro';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => $this->nama_analis ?: '-',
        ]);
    }
}
