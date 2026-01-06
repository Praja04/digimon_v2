<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShelfLifeSamples extends Model
{
    protected $table = 'shelf_life_samples';
    protected $fillable = [
        'storage',
        'production_batch_id',
    ];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function shelfLifeSamplingDetails()
    {
        return $this->hasMany(ShelfLifeSamplingDetail::class, 'shelf_life_sample_id', 'id');
    }
}
