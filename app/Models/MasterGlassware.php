<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGlassware extends Model
{
    use HasFactory;

    protected $table = 'master_glasswares';

    protected $fillable = [
        'jenis_glassware',
        'nomor_glassware',
        'berat_glassware',
        'uom_berat',
        'status',
    ];

    protected $casts = [
        'berat_glassware' => 'decimal:4',
        'status' => 'boolean',
    ];
}
