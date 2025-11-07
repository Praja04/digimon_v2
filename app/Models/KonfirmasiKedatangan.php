<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRM;
use App\Models\User;

class KonfirmasiKedatangan extends Model
{
    protected $table = 'konfirmasi_kedatangan';
   
    protected $guarded = [];

    public function identitasRM()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function diterimaBy()
    {
        return $this->belongsTo(User::class, 'diterima_by');
    }

    public function dianalisaBy()
    {
        return $this->belongsTo(User::class, 'dianalisa_by');
    }
}
