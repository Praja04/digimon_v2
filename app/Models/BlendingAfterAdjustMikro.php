<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlendingAfterAdjustMikro extends Model
{
    protected $table = 'blending_after_adjust_mikro';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    // Relasi ke konfirmasi_blending_after_adjust_mikro
    public function konfirmasi()
    {
        return $this->hasOne(KonfirmasiBlendingAfterAdjustMikro::class, 'blending_after_adjust_mikro_id');
    }
}


