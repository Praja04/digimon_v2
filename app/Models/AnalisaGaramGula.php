<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisaGaramGula extends Model
{
    protected $table = 'analisa_garam_gula';

    protected $guarded = [];

    public function identitas()
    {
        return $this->belongsTo(IdentitasRm::class, 'id_identitas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
