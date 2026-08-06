<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamplingStatus extends Model
{
    use HasFactory;

    protected $table = 'sampling_statuses';

    protected $fillable = [
        'kode',
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}