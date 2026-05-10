<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimbanganRetailMesin extends Model
{
    protected $table = 'timbangan_retail_mesin';

    protected $fillable = [
        'mesin',
        'variant',
        'waktu',
        'status',
        'berat',
        'unit',
        'nik',
        'filler'
    ];
}
