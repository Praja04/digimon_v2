<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJenisBahan extends Model
{
    use HasFactory;

    protected $table = 'master_jenis_bahans';

    protected $fillable = [
        'nama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function suppliers(): HasMany
    {
        return $this->hasMany(MasterSupplierRm::class, 'jenis_bahan_id');
    }

    public function asalBahans(): HasMany
    {
        return $this->hasMany(MasterAsalBahan::class, 'jenis_bahan_id');
    }

    public function standarRms(): HasMany
    {
        return $this->hasMany(MasterStandarRm::class, 'id_jenis_bahan');
    }
}
