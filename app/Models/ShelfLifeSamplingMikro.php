<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShelfLifeSamplingMikro extends Model
{
    protected $table = 'shelf_life_sampling_mikro';
    protected $fillable = [
        'shelf_life_sampling_detail_id',
        'shift_analis',
        'nama_analis',
        'waktu_analisa',
        'eb',
        'sa',
        'tpc',
        'ym',
        'scanned_at',
    ];

    public function shelfLifeSamplingDetail()
    {
        return $this->belongsTo(ShelfLifeSamplingDetail::class, 'shelf_life_sampling_detail_id', 'id');
    }
}
