<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSupplierRm extends Model
{
    use HasFactory;

    protected $table = 'master_supplier_rms';

    protected $fillable = [
        'jenis_bahan_id',
        'nama_supplier',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function jenisBahan(): BelongsTo
    {
        return $this->belongsTo(MasterJenisBahan::class, 'jenis_bahan_id');
    }

    public function asalBahans(): HasMany
    {
        return $this->hasMany(MasterAsalBahan::class, 'supplier_rm_id');
    }
}
