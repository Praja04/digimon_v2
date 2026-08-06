<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisIncoming extends Model
{
    use HasFactory;

    protected $table = 'jenis_incomings';

    protected $fillable = [
        'kategori',
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function suppliers(): HasMany
    {
        return $this->hasMany(
            Supplier::class,
            'jenis_incoming_id'
        );
    }
}