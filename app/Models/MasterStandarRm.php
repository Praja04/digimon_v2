<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterStandarRm extends Model
{
    use HasFactory;

    protected $table = 'master_standar_rms';

    protected $fillable = [
        'id_jenis_bahan',
        'parameter',
        'min_standar',
        'max_standar',
        'target_text',
        'uom',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'min_standar' => 'float',
        'max_standar' => 'float',
        'status'      => 'boolean',
    ];

    public function jenisBahan(): BelongsTo
    {
        return $this->belongsTo(MasterJenisBahan::class, 'id_jenis_bahan');
    }
}
