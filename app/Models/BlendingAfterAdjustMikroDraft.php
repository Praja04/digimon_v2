<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlendingAfterAdjustMikroDraft extends Model
{
    use HasFactory;

    protected $table = 'blending_after_adjust_mikro_drafts';

    protected $fillable = [
        'blending_after_adjust_mikro_id',
        'eb',
        'tpc',
        'ym',
        'hasil',
        'created_by',
    ];

    public function blendingAfterAdjustMikro()
    {
        return $this->belongsTo(
            BlendingAfterAdjustMikro::class,
            'blending_after_adjust_mikro_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}