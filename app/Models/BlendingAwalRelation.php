<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BlendingAwal;

class BlendingAwalRelation extends Model
{
    protected $guarded = [];

    public function blendingAwal()
    {
        return $this->belongsTo(BlendingAwal::class);
    }
}
