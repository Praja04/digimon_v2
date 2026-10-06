<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterParameterRm extends Model
{
    use HasFactory;

    protected $table = 'master_parameter_rms';

    protected $fillable = [
        'jenis_bahan_id',
        'kategori',
        'nama_pilihan',
        'is_custom',
        'urutan',
        'status',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'status'    => 'boolean',
        'urutan'    => 'integer',
    ];

    public function jenisBahan(): BelongsTo
    {
        return $this->belongsTo(MasterJenisBahan::class, 'jenis_bahan_id');
    }
}
