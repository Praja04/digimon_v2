<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InnerOuter extends Model
{
    protected $fillable = [
        'kode',
        'jenis',
        'material_id',
        'supplier_id',
        'status_id',
        'uom_id',
        'rekomendasi_id',
        'ketidaksesuaian_id',
        'keterangan'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function rekomendasi()
    {
        return $this->belongsTo(Rekomendasi::class);
    }

    public function ketidaksesuaian()
    {
        return $this->belongsTo(Ketidaksesuaian::class);
    }
}