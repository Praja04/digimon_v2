<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonconformityType extends Model
{
    use HasFactory;

    protected $table = 'nonconformity_types';

    protected $fillable = [
        'kode',
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}