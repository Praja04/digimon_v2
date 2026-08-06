<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingIncoming extends Model
{
    protected $table = 'packaging_incomings';

    protected $fillable = [
        'no_spb',
        'jenis_incoming_id',
        'supplier_id',
        'jenis_material_id',
        'uom_id',
        'sampling_status_id',
        'mid',
        'no_mobil',
        'tanggal_kedatangan',
        'jam_kedatangan',
        'jumlah',
        'jumlah_sampel',
        'no_batch',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kedatangan' => 'date',
        'jumlah' => 'decimal:2',
        'jumlah_sampel' => 'integer',
    ];

    public function jenisIncoming()
    {
        return $this->belongsTo(
            JenisIncoming::class,
            'jenis_incoming_id'
        );
    }

    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function jenisMaterial()
    {
        return $this->belongsTo(
            JenisMaterial::class,
            'jenis_material_id'
        );
    }

    public function samplingStatus()
    {
        return $this->belongsTo(
            SamplingStatus::class,
            'sampling_status_id'
        );
    }
}