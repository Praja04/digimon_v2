<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringTurunBlendingDraft extends Model
{
    protected $table = 'monitoring_turun_blending_drafts';

    protected $guarded = [];

    public function monitoringTurunBlending(): BelongsTo
    {
        return $this->belongsTo(
            MonitoringTurunBlending::class,
            'monitoring_turun_blending_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}