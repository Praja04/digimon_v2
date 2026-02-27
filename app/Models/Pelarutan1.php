<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\Color;
use App\Models\User;

class Pelarutan1 extends Model
{
    protected $table = 'pelarutan_1';

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
