<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelarutan2Draft extends Model
{
    protected $table = 'pelarutan_2_drafts';

    protected $guarded = [];

    public function pelarutan2()
    {
        return $this->belongsTo(
            Pelarutan2::class,
            'pelarutan_2_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}