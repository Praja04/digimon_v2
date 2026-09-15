<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPasteurisasiDraft extends Model
{
    use HasFactory;

    protected $table = 'monitoring_pasteurisasi_drafts';

    protected $fillable = [
        'monitoring_pasteurisasi_id',
        'brix',
        'visco',
        'nacl',
        'bj',
        'ph',
        'aw',
        'organo',
        'buih',
        'aroma',
        'endapan',
        'status_disposition',
        'disposition',
        'disposition_remark',
        'adjustment_qty_air',
        'adjustment_qty_gula',
        'adjustment_qty_garam',
        'created_by',
    ];

    public function monitoringPasteurisasi()
    {
        return $this->belongsTo(
            MonitoringPasteurisasi::class,
            'monitoring_pasteurisasi_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}