<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressTestData extends Model
{
    protected $table = 'press_test_data';
    protected $fillable = ['nama_analis_field', 'shift', 'variant', 'waktu', 'batas', 'mesin_press_test', 'mesin_retail'];
}
