<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisaLongTerm extends Model
{
    protected $table = 'analisa_long_term';

    protected $guarded = [];

    public function identitas()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
