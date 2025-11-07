<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRm;

class SamplingKondisiMobil extends Model
{
    protected $table = 'sampling_kondisi_mobil';

    protected $guarded = [];

    public function identitas()
    {
        return $this->hasOne(IdentitasRm::class, 'id', 'id_identitas');
    }
}
