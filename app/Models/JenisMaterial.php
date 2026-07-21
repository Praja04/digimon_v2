<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisMaterial extends Model
{
    use HasFactory;

    protected $table = 'jenis_materials';

    protected $fillable = [
        'kode',
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}