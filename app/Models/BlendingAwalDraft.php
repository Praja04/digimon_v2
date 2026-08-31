<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlendingAwalDraft extends Model
{
    protected $table = 'blending_awal_drafts';

    protected $guarded = [];

    public function blendingAwal()
    {
        return $this->belongsTo(
            BlendingAwal::class,
            'blending_awal_id'
        );
    }

    public function color()
    {
        return $this->belongsTo(
            Color::class,
            'color_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}