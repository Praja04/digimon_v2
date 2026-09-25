<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelarutan1Draft extends Model
{
    protected $table = 'pelarutan_1_drafts';

    protected $guarded = [];

    public function pelarutan1()
    {
        return $this->belongsTo(Pelarutan1::class, 'pelarutan_1_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}