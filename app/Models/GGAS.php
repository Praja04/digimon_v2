<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GGAS extends Model
{
    protected $table = 'ggas';

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
