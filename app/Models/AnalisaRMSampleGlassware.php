<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisaRMSampleGlassware extends Model
{
    use HasFactory;

    protected $table = 'analisa_rm_sample_glasswares';

    protected $fillable = [
        'transaksi_id',
        'sampel_ke',
        'no_beaker',
        'berat_beaker_500',
        'berat_beaker_250',
        'timbang_a',
        'timbang_b',
        'no_cawan',
        'berat_cawan',
        'timbang_aa',
    ];

    protected $casts = [
        'sampel_ke' => 'integer',
        'berat_beaker_500' => 'float',
        'berat_beaker_250' => 'float',
        'timbang_a' => 'float',
        'timbang_b' => 'float',
        'berat_cawan' => 'float',
        'timbang_aa' => 'float',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiAnalisaRM::class, 'transaksi_id');
    }
}

