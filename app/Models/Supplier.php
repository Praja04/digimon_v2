<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'jenis_incoming_id',
        'kode',
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function jenisIncoming(): BelongsTo
    {
        return $this->belongsTo(
            JenisIncoming::class,
            'jenis_incoming_id'
        );
    }
}