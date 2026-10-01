<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\User;

class DocPasteurisasiStorage extends Model
{
    protected $table = 'doc_pasteurisasi_storages';

    protected $guarded = [];

    protected $casts = [
        'tanggal_record_doc' => 'date',
        'pasteurisasi_rows' => 'array',
        'storage_rows' => 'array',
    ];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
