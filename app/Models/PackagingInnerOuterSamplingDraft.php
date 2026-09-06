<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingInnerOuterSamplingDraft extends Model
{
    protected $guarded = [];

    protected $casts = [
        'hasil_sampel' => 'array',
        'jenis_ketidaksesuaian' => 'array',
        'foto_pengecekan' => 'array',
        'foto_ketidaksesuaian' => 'array',
    ];

    public function packagingIncoming(): BelongsTo
    {
        return $this->belongsTo(
            PackagingIncoming::class,
            'packaging_incoming_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}