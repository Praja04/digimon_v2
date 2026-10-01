<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterAsalBahan extends Model
{
    use HasFactory;

    protected $table = 'master_asal_bahans';

    protected $fillable = [
        'jenis_bahan_id',
        'supplier_rm_id',
        'asal_bahan',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function jenisBahan(): BelongsTo
    {
        return $this->belongsTo(MasterJenisBahan::class, 'jenis_bahan_id');
    }

    public function supplierRm(): BelongsTo
    {
        return $this->belongsTo(MasterSupplierRm::class, 'supplier_rm_id');
    }
}
