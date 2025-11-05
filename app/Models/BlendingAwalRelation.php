<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlendingAwalRelation extends Model
{
    protected $guarded = [];

    public function blendingAwal()
    {
        return $this->belongsTo(BlendingAwal::class);
    }
}
