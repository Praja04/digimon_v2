<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlendingAwal extends Model
{
    protected $table = 'blending_awal';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function additionalBatches()
    {
        return $this->hasMany(BlendingAwalRelation::class, 'blending_awal_id');
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
