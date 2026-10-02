<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocAdjustment extends Model
{
    use HasFactory;

    protected $table = 'doc_adjustments';

    protected $fillable = [
        'production_batch_id',
        'tanggal_record_doc',
        'halaman',
        'proses',
        'jenis_kecap',
        'no_batch',
        'tanggal_produksi',
        'volume_batch',
        'shift',
        'bahan_rows',
        'disposisi',
        'keterangan',
        'adj1_jam',
        'adj1_status',
        'adj1_petugas',
        'adj2_jam',
        'adj2_status',
        'adj2_petugas',
        'adj3_jam',
        'adj3_status',
        'adj3_petugas',
        'qc_analis',
        'doc_code',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_record_doc' => 'date',
        'tanggal_produksi' => 'date',
        'bahan_rows' => 'array',
    ];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }
}
