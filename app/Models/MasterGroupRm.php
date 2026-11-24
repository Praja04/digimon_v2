<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterGroupRm extends Model
{
    use HasFactory;

    protected $table = 'master_group_rms';

    protected $fillable = [
        'jenis_bahan_id',
        'grup',
        'parameter',
        'kategori',
        'target',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function jenisBahan(): BelongsTo
    {
        return $this->belongsTo(MasterJenisBahan::class, 'jenis_bahan_id');
    }
}
