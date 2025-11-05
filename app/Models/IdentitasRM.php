<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentitasRM extends Model
{
    protected $table = 'identitas_rm';

    protected $guarded = [];

    public function analisaGaramGula()
    {
        return $this->hasMany(AnalisaGaramGula::class, 'id_identitas');
    }

    public function analisaLongTerm()
    {
        return $this->hasMany(AnalisaLongTerm::class, 'id_identitas');
    }

    public function analisaShortTerm()
    {
        return $this->hasMany(AnalisaShortTerm::class, 'id_identitas');
    }

    public function samplingDokumen()
    {
        return $this->hasOne(SamplingDokumen::class, 'id_identitas');
    }

    public function samplingFisikKemasan()
    {
        return $this->hasOne(SamplingFisikKemasan::class, 'id_identitas');
    }

    public function samplingFisikRaw()
    {
        return $this->hasOne(SamplingFisikRaw::class, 'id_identitas');
    }

    public function samplingKondisiMobil()
    {
        return $this->hasOne(SamplingKondisiMobil::class, 'id_identitas');
    }

    public function KonfirmasiKedatangan()
    {
        return $this->hasOne(KonfirmasiKedatangan::class, 'id_identitas');
    }

    public function isSamplingComplete()
    {
        if ($this->jenis === 'Garam') {
            return $this->samplingKondisiMobil && $this->samplingDokumen && $this->samplingFisikKemasan;
        } else {
            return $this->samplingKondisiMobil && $this->samplingDokumen && $this->samplingFisikKemasan && $this->samplingFisikRaw;
        }
    }
}
