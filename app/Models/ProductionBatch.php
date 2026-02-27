<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;
use App\Models\Pelarutan1;
use App\Models\Pelarutan2;
use App\Models\BlendingAwal;
use App\Models\BlendingAfterAdjustMikro;
use App\Models\MonitoringTurunBlending;
use App\Models\MonitoringPasteurisasi;
use App\Models\MonitoringStorageKimia;
use App\Models\MonitoringStorageMikro;

class ProductionBatch extends Model
{
    protected $guarded = [];

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function pelarutan_1()
    {
        return $this->hasMany(Pelarutan1::class);
    }

    public function pelarutan_2()
    {
        return $this->hasMany(Pelarutan2::class);
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

    public function monitoringOnGoingKimia()
    {
        return $this->hasMany(MonitoringOnGoingKimia::class);
    }

    public function monitoringOnGoingMikro()
    {
        return $this->hasMany(MonitoringOnGoingMikro::class);
    }

    public function monitoringDailyTank()
    {
        return $this->hasMany(MonitoringDailyTank::class);
    }

    public function shelfLifeSamples()
    {
        return $this->hasMany(ShelfLifeSamples::class);
    }

    public function getBatchRangeArrayAttribute()
    {
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $this->batch_range, $matches)) {
            return range((int)$matches[1], (int)$matches[2]);
        }
        return [$this->batch_range];
    }

    public function isPelarutan1Complete(): bool
    {
        $pelarutan1Items = $this->pelarutan_1;
        $jumlahPelarutan1 = $pelarutan1Items->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($jumlahPelarutan1 === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $pelarutan1Items->every(function ($item) {
            return !is_null($item->disposition);
        });

        return $isAllFilled;
    }

    public function isPelarutan2Complete(): bool
    {
        $pelarutan2Items = $this->pelarutan_2;
        $jumlahPelarutan2 = $pelarutan2Items->count();

        // Jika tidak ada data GGA, belum lengkap
        if ($jumlahPelarutan2 === 0) {
            return false;
        }

        // Cek apakah semua data brix dan nacl sudah terisi
        $isAllFilled = $pelarutan2Items->every(function ($item) {
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
