<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\User;

class DocBlendingAwal extends Model
{
    protected $table = 'doc_blending_awals';

    protected $guarded = [];

    protected $casts = [
        'tanggal_record_doc' => 'date',
        'batch_blocks' => 'array',
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
