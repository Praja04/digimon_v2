<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlendingAwalForemanDraft extends Model
{
    protected $guarded = [];

    public function blendingAwal(): BelongsTo
    {
        return $this->belongsTo(BlendingAwal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}