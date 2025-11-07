<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MonitoringStorageMikro;
use App\Models\User;

class KonfirmasiMonitoringStorageMikro extends Model
{
    protected $table = 'konfirmasi_monitoring_storage_mikro';

    protected $guarded = [];

    // Relasi ke monitoring_storage_mikro
    public function monitoringStorageMikro()
    {
        return $this->belongsTo(MonitoringStorageMikro::class, 'monitoring_storage_mikro_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
