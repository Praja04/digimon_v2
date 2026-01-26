<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShelfLifeSamplingDetail extends Model
{
    protected $table = 'shelf_life_sampling_detail';
    protected $fillable = [
        'shelf_life_sample_id',
        'variant_fg',
        'kelompok_sample',
        'kelompok_tanggal',
        'koding',
        'jam_koding',
        'bulan_ke',
        'tanggal_filling',
        'ruang_sl',
        'bin_location',
        'is_checked',
        'tanggal_analisa',
    ];

    public function shelfLifeSample()
    {
        return $this->belongsTo(ShelfLifeSamples::class);
    }

    public function shelfLifeSamplingKimia()
    {
        return $this->hasOne(ShelfLifeSamplingKimia::class, 'shelf_life_sampling_detail_id', 'id');
    }

    public function shelfLifeSamplingMikro()
    {
        return $this->hasOne(ShelfLifeSamplingMikro::class, 'shelf_life_sampling_detail_id', 'id');
    }
}
