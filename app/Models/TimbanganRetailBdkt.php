<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimbanganRetailBdkt extends Model
{
    protected $table = 'timbangan_retail_bdkt';

    protected $fillable = [
        'mesin',
        'variant',
        'waktu',
        'status',
        'berat',
        'unit',
        'nik',
        'filler',
    ];
}

