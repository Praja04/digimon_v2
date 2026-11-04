<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisaRMSampleDetail extends Model
{
    use HasFactory;

    protected $table = 'analisa_rm_sample_details';

    protected $fillable = [
        'transaksi_id',
        'sampel_ke',
        'master_standar_id',
        'parameter',
        'nilai',
        'is_sesuai',
    ];

    protected $casts = [
        'sampel_ke' => 'integer',
        'is_sesuai' => 'boolean',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiAnalisaRM::class, 'transaksi_id');
    }

    public function masterStandar(): BelongsTo
    {
        return $this->belongsTo(MasterStandarRm::class, 'master_standar_id');
    }
}

