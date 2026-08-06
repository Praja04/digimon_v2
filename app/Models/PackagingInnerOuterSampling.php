<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingInnerOuterSampling extends Model
{
    protected $fillable = [
        'packaging_incoming_id',
        'jumlah_sampel',
        'no_batch',
        'lot_sebelum',
        'lot_setelah',
        'hasil_sampel',
        'coa',
        'rekomendasi',
        'konfirmasi_ketidaksesuaian',
        'jenis_ketidaksesuaian',
        'foto_pengecekan',
        'foto_ketidaksesuaian',
        'keterangan',
        'created_by',
        'updated_by',
        'status_proses',
    ];

    protected $casts = [
        'hasil_sampel' => 'array',
        'jenis_ketidaksesuaian' => 'array',
        'foto_pengecekan' => 'array',
        'foto_ketidaksesuaian' => 'array',
    ];

    public function packagingIncoming(): BelongsTo
    {
        return $this->belongsTo(
            PackagingIncoming::class
        );
    }
}