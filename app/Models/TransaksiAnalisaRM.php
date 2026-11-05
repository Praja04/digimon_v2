<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiAnalisaRM extends Model
{
    use HasFactory;

    protected $table = 'analisa_rm_transaksi';

    protected $with = ['details'];

    protected $fillable = [
        'id_identitas',
        'kategori',
        'disposisi',
        'status',
        'keterangan',
        'created_by',
    ];

    public function identitas(): BelongsTo
    {
        return $this->belongsTo(IdentitasRM::class, 'id_identitas');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AnalisaRMSampleDetail::class, 'transaksi_id');
    }

    public function glasswares(): HasMany
    {
        return $this->hasMany(AnalisaRMSampleGlassware::class, 'transaksi_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Dynamic accessor untuk membaca parameter analisa dari table analisa_rm_sample_details
     */
    public function getAttribute($key)
    {
        $val = parent::getAttribute($key);
        if ($val !== null) {
            return $val;
        }

        if (!in_array($key, ['id', 'id_identitas', 'kategori', 'disposisi', 'status', 'keterangan', 'created_by', 'created_at', 'updated_at', 'details', 'glasswares', 'identitas', 'creator'])) {
            return $this->getDetailValue($key);
        }

        return null;
    }

    public function getDetailValue(?string $key)
    {
        if (empty($key)) {
            return null;
        }

        $details = $this->details;
        if (!$details || $details->isEmpty()) {
            return null;
        }

        $cleanKey = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$key));
        if ($cleanKey === 'ka' || $cleanKey === 'kadarair') $target = 'kadarair';
        elseif ($cleanKey === 'nacl') $target = 'nacl';
        elseif ($cleanKey === 'gw' || $cleanKey === 'grossweight') $target = 'grossweight';
        elseif ($cleanKey === 'bj' || $cleanKey === 'beratjenis') $target = 'beratjenis';
        else $target = $cleanKey;

        $matching = $details->filter(function ($d) use ($target) {
            $paramClean = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$d->parameter));
            if ($paramClean === 'ka' || $paramClean === 'kadarair') $paramNorm = 'kadarair';
            elseif ($paramClean === 'nacl') $paramNorm = 'nacl';
            elseif ($paramClean === 'gw' || $paramClean === 'grossweight') $paramNorm = 'grossweight';
            elseif ($paramClean === 'bj' || $paramClean === 'beratjenis') $paramNorm = 'beratjenis';
            else $paramNorm = $paramClean;

            return $paramNorm === $target 
                || str_contains($paramNorm, $target) 
                || str_contains($target, $paramNorm);
        });

        if ($matching->isEmpty()) {
            return null;
        }

        return $matching->first()->nilai;
    }
}

