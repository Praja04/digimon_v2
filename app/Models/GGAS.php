<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\Color;
use App\Models\User;

class GGAS extends Model
{
    protected $table = 'ggas';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
