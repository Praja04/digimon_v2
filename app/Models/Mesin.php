<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    protected $table = 'mesin';

    protected $fillable = [
        'mesin',
        'variant',
        'waktu',
        'status',
        'berat',
        'unit'
    ];
}
