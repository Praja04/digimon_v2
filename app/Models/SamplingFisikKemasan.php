<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SamplingFisikKemasan extends Model
{
    protected $table = 'sampling_fisik_kemasan';

    protected $guarded = [];

    public function identitas()
    {
        return $this->hasOne(IdentitasRm::class, 'id', 'id_identitas');
    }
}
