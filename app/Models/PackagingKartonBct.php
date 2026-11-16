<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingKartonBct extends Model
{
    protected $table = 'packaging_karton_bcts';

    protected $fillable = [
        'packaging_incoming_id',
        'bct4gt1',
        'bct4gt2',
        'resampling_count',
        'values',
        'average',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bct4gt1' => 'float',
        'bct4gt2' => 'float',
        'resampling_count' => 'integer',
        'values' => 'array',
        'average' => 'float',
    ];

    public function packagingIncoming(): BelongsTo
    {
        return $this->belongsTo(PackagingIncoming::class, 'packaging_incoming_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

