<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $guarded = [];

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function gga()
    {
        return $this->hasMany(GGA::class);
    }

    public function ggas()
    {
        return $this->hasMany(GGAS::class);
    }

    public function BlendingAwal()
    {
        return $this->hasMany(BlendingAwal::class);
    }

    public function blendingAfterAdjustMikro()
    {
        return $this->hasMany(BlendingAfterAdjustMikro::class);
    }

    public function monitoringTurunBlending()
    {
        return $this->hasMany(MonitoringTurunBlending::class);
    }

    public function monitoringPasteurisasi()
    {
        return $this->hasMany(MonitoringPasteurisasi::class);
    }

    public function monitoringStorageKimia()
    {
        return $this->hasMany(MonitoringStorageKimia::class);
    }

    public function monitoringStorageMikro()
    {
        return $this->hasMany(MonitoringStorageMikro::class);
    }

    public function getBatchRangeArrayAttribute()
    {
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $this->batch_range, $matches)) {
            return range((int)$matches[1], (int)$matches[2]);
        }
        return [$this->batch_range];
    }

    public function isGGAComplete(): bool
    {
        $ggaItems = $this->gga;
        $jumlahGGA = $ggaItems->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($jumlahGGA === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $ggaItems->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isGGasComplete(): bool
    {
        $ggasItems = $this->ggas;
        $jumlahGGA = $ggasItems->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($jumlahGGA === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $ggasItems->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isBlendingAwalComplete()
    {
        $blending = $this->BlendingAwal;
        $data = $blending->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $blending->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isBlendingAwalMikroComplete()
    {
        $blending = $this->blendingAfterAdjustMikro;
        $data = $blending->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $blending->every(function ($item) {
            return !is_null($item->hasil);
        });

        return $isAllFilled;
    }

    public function isMonitoringTurunBlendingComplete()
    {
        $monitoring = $this->monitoringTurunBlending;
        $data = $monitoring->count();

        // Jika tidak ada data, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $monitoring->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isMonitoringPasteurisasiComplete()
    {
        $monitoring = $this->monitoringPasteurisasi;
        $data = $monitoring->count();

        // Jika tidak ada data, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $monitoring->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isMonitoringStorageKimiaComplete()
    {
        $monitoring = $this->monitoringStorageKimia;
        $data = $monitoring->count();

        // Jika tidak ada data, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $monitoring->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isMonitoringStorageMikroComplete()
    {
        $monitoring = $this->monitoringStorageMikro;
        $data = $monitoring->count();

        // Jika tidak ada data, belum lengkap
        if ($data === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $monitoring->every(function ($item) {
            return !is_null($item->hasil);
        });

        return $isAllFilled;
    }
}
