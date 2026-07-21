<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRM;

class SamplingFisikKemasan extends Model
{
    protected $table = 'sampling_fisik_kemasan';

    protected $guarded = [];

    public function identitas()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas', 'id');
    }
}