<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;
use App\Models\Color;
use App\Models\User;

class GGA extends Model
{
    protected $table = 'gga';

    protected $guarded = [];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
