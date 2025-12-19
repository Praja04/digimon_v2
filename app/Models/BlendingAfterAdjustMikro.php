<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\KonfirmasiBlendingAfterAdjustMikro;

class BlendingAfterAdjustMikro extends Model
{
    protected $table = 'blending_after_adjust_mikro';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }
}


