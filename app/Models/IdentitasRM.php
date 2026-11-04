<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnalisaLongTerm;
use App\Models\SamplingDokumen;
use App\Models\SamplingFisikKemasan;
use App\Models\SamplingFisikRaw;
use App\Models\SamplingKondisiMobil;
use App\Models\KonfirmasiKedatangan;

use App\Models\AnalisaLongTermHistory;

class IdentitasRM extends Model
{
    protected $table = 'identitas_rm';

    protected $guarded = [];

    public function transaksiAnalisa()
    {
        return $this->hasMany(TransaksiAnalisaRM::class, 'id_identitas')
            ->orderByRaw("CASE WHEN kategori = 'incoming' OR kategori IS NULL THEN 1 WHEN kategori = 'sta' THEN 2 WHEN kategori = 'monitoring' THEN 3 ELSE 4 END")
            ->orderBy('id', 'asc');
    }

    public function analisaSampel()
    {
        return $this->transaksiAnalisa();
    }

    public function analisaGaramGula()
    {
        return $this->transaksiAnalisa();
    }

    public function analisaBahanKering()
    {
        return $this->transaksiAnalisa();
    }

    public function analisaBahanCair()
    {
        return $this->transaksiAnalisa();
    }

    public function analisaLongTerm()
    {
        return $this->hasMany(AnalisaLongTerm::class, 'id_identitas');
    }

    public function analisaLongTermHistories()
    {
        return $this->hasMany(AnalisaLongTermHistory::class, 'id_identitas')->orderBy('created_at', 'desc');
    }

    public function analisaShortTerm()
    {
        return $this->transaksiAnalisa();
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
        return $this->samplingKondisiMobil && $this->samplingDokumen && $this->samplingFisikKemasan && $this->samplingFisikRaw;
    }

    public function getMaterialCategoryAttribute(): string
    {
        $upper = strtoupper(trim((string)$this->jenis));
        if ((str_contains($upper, 'KELAPA') || str_contains($upper, 'TEBU') || $upper === 'GK' || $upper === 'GT') && !str_contains($upper, 'RAFINASI')) {
            return 'kelapa-tebu';
        }
        if (str_contains($upper, 'GARAM')) {
            return 'garam';
        }
        if (
            str_contains($upper, 'GLUKOSA') || 
            str_contains($upper, 'FRUCTOSE') || 
            str_contains($upper, 'HFCS') || 
            str_contains($upper, 'KECAP') || 
            str_contains($upper, 'HAITIAN') || 
            str_contains($upper, 'CARAMEL') || 
            str_contains($upper, 'KARAMEL') ||
            str_contains($upper, 'LIQUID')
        ) {
            return 'cair';
        }
        return 'kering';
    }
}
