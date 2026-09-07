<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressTestMesin1 extends Model
{
    protected $table = 'press_test_mesin_1';

    protected $fillable = [
        'date',
        'time',
        'grup',
        'shift',
        'mesin',
        'press',
        'varian',
        'variant',
        'sample',
        'jarak',
        'batas',
        'status',
        'status_sensor',
        'verif_manual',
        'jenis_bocor',
    ];

    protected $appends = [
        'statusSensor',
        'verifManual',
        'jenisBocor',
    ];

    public function getVarianAttribute()
    {
        return $this->attributes['varian'] ?? $this->attributes['variant'] ?? null;
    }

    public function getVariantAttribute()
    {
        return $this->attributes['variant'] ?? $this->attributes['varian'] ?? null;
    }

    public function getStatusSensorAttribute()
    {
        return $this->attributes['status_sensor'] ?? $this->attributes['status'] ?? null;
    }

    public function getVerifManualAttribute()
    {
        return $this->attributes['verif_manual'] ?? null;
    }

    public function getJenisBocorAttribute()
    {
        return $this->attributes['jenis_bocor'] ?? null;
    }
}
