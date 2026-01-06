<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShelfLifeSamplingKimia extends Model
{
    protected $table = 'shelf_life_sampling_kimia';
    protected $fillable = [
        'shelf_life_sampling_detail_id',
        'shift_analis',
        'nama_analis',
        'waktu_analisa',
        'nacl',
        'brix',
        'aw',
        'ph',
        'bj',
        'buih',
        'aroma',
        'color_id',
        'organo',
        'visco',
        'total_nitrogen',
        'scanned_at',
    ];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function shelfLifeSamplingDetail()
    {
        return $this->belongsTo(ShelfLifeSamplingDetail::class, 'shelf_life_sampling_detail_id', 'id');
    }
}
