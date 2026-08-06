<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingPouchSampling extends Model
{
    use HasFactory;

    protected $table = 'packaging_pouch_samplings';

    protected $fillable = [
        'packaging_incoming_id',
        'status_proses',
        'qty',
        'uom',
        'jumlah_sampel',
        'hasil_sampel',
        'hasil_thickness',
        'coa',
        'rekomendasi',
        'konfirmasi_ketidaksesuaian',
        'jenis_ketidaksesuaian',
        'foto_pengecekan',
        'foto_ketidaksesuaian',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'jumlah_sampel' => 'integer',
        'hasil_sampel' => 'array',
        'hasil_thickness' => 'array',
        'jenis_ketidaksesuaian' => 'array',
        'foto_ketidaksesuaian' => 'array',
    ];
}