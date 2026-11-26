<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'no_batch',
        'exp_date',
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
        'exp_date' => 'date',
        'qty' => 'decimal:2',
        'jumlah_sampel' => 'integer',
        'hasil_sampel' => 'array',
        'hasil_thickness' => 'array',
        'jenis_ketidaksesuaian' => 'array',
        'foto_ketidaksesuaian' => 'array',
    ];

    public function packagingIncoming(): BelongsTo
    {
        return $this->belongsTo(PackagingIncoming::class, 'packaging_incoming_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}