<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringStorageKimia extends Model
{
    protected $table = 'monitoring_storage_kimia';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function additionalBatches()
    {
        return $this->hasMany(MonitoringStorageKimiaRelation::class, 'monitoring_storage_kimia_id');
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
