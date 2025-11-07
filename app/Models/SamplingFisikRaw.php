<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRm;

class SamplingFisikRaw extends Model
{
    protected $table = 'sampling_fisik_raw';

    protected $guarded = [];

    public function identitas()
    {
        return $this->hasOne(IdentitasRm::class, 'id', 'id_identitas');
    }
}
