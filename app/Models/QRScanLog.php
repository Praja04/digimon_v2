<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRScanLog extends Model
{
    protected $table = 'qr_scan_logs';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
