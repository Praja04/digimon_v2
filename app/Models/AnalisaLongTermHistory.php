<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnalisaLongTerm;
use App\Models\IdentitasRM;
use App\Models\User;

class AnalisaLongTermHistory extends Model
{
    protected $table = 'analisa_long_term_histories';

    protected $guarded = [];

    protected $casts = [
        'attachment' => 'array',
    ];

    public function analisaLongTerm()
    {
        return $this->belongsTo(AnalisaLongTerm::class, 'analisa_long_term_id');
    }

    public function identitas()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getPhotosAttribute(): array
    {
        $val = $this->attachment;
        if (empty($val) || $val === '-') {
            return [];
        }
        if (is_array($val)) {
            return $val;
        }
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return [$val];
        }
        return [];
    }
}
