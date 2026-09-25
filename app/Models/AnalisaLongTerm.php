<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IdentitasRM;
use App\Models\User;
use App\Models\AnalisaLongTermHistory;

class AnalisaLongTerm extends Model
{
    protected $table = 'analisa_long_term';

    protected $guarded = [];

    protected $casts = [
        'attachment' => 'array',
    ];

    public function identitas()
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(AnalisaLongTermHistory::class, 'analisa_long_term_id')->orderBy('created_at', 'desc');
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

