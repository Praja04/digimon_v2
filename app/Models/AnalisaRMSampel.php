<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisaRMSampel extends Model
{
    use HasFactory;

    protected $table = 'analisa_rm_sampels';

    protected $guarded = ['id'];

    public function identitas()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

