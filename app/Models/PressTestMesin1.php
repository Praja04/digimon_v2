<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressTestMesin1 extends Model
{
    protected $table = 'press_test_mesin_1';
    protected $fillable = ['variant', 'waktu', 'jarak', 'batas'];
}
