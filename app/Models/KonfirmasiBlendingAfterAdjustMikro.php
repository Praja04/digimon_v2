<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfirmasiBlendingAfterAdjustMikro extends Model
{
    protected $table = 'konfirmasi_blending_after_adjust_mikro';

    protected $guarded = [];

    // Relasi ke blending_after_adjust_mikro
    public function blendingAfterAdjustMikro()
    {
        return $this->belongsTo(BlendingAfterAdjustMikro::class, 'blending_after_adjust_mikro_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
