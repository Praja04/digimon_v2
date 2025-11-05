<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SamplingDokumen extends Model
{
    protected $table = 'sampling_dokumen';

    protected $guarded = [];

    public function identitas()
    {
        return $this->hasOne(IdentitasRm::class, 'id', 'id_identitas');
    }
}
