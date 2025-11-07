<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRM;
use App\Models\User;

class AnalisaShortTerm extends Model
{
    protected $table = 'analisa_short_term';
  
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
