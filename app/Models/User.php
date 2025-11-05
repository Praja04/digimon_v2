<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function role(): Attribute
    {
        return new Attribute(
            get: fn($value) =>  ["Head Of Dapartement", "Supervisor", "Foreman", "Analis Kimia", "Analis Mikro", "Analis RM", "Analis Field", "Operator"][$value],
        );
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function gga()
    {
        return $this->hasMany(GGA::class, 'created_by');
    }

    public function ggas()
    {
        return $this->hasMany(GGAS::class, 'created_by');
    }

    public function blendingAwal()
    {
        return $this->hasMany(BlendingAwal::class, 'created_by');
    }

    public function monitoringTurunBlending()
    {
        return $this->hasMany(MonitoringTurunBlending::class, 'created_by');
    }

    public function monitoringPasteurisasi()
    {
        return $this->hasMany(MonitoringPasteurisasi::class, 'created_by');
    }

    public function monitoringStorageKimia()
    {
        return $this->hasMany(MonitoringStorageKimia::class, 'created_by');
    }

    public function monitoringStorageMikro()
    {
        return $this->hasMany(MonitoringStorageMikro::class, 'created_by');
    }

    public function konfirmasiMonitoringStorageMikro()
    {
        return $this->hasMany(KonfirmasiMonitoringStorageMikro::class, 'created_by');
    }

    public function blendingAfterAdjustMikro()
    {
        return $this->hasMany(BlendingAfterAdjustMikro::class, 'created_by');
    }

    public function konfirmasiBlendingAfterAdjustMikro()
    {
        return $this->hasMany(KonfirmasiBlendingAfterAdjustMikro::class, 'created_by');
    }

    public function monitoringDailyTankMikro()
    {
        return $this->hasMany(MonitoringDailyTank::class, 'qc_analisa');
    }

    public function monitoringDailyTankField()
    {
        return $this->hasMany(MonitoringDailyTank::class, 'qc_field');
    }

    public function analisaGaramGula()
    {
        return $this->hasMany(AnalisaGaramGula::class, 'created_by');
    }

    public function analisaLongTerm()
    {
        return $this->hasMany(AnalisaLongTerm::class, 'created_by');
    }

    public function analisaShortTerm()
    {
        return $this->hasMany(AnalisaShortTerm::class, 'created_by');
    }

    public function konfirmasiKedatangan()
    {
        return $this->hasMany(KonfirmasiKedatangan::class, 'diterima_by');
    }

    public function dianalisaBy()
    {
        return $this->hasMany(KonfirmasiKedatangan::class, 'dianalisa_by');
    }
}
